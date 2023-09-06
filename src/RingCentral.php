<?php

namespace Coxlr\RingCentral;

use Coxlr\RingCentral\Exceptions\CouldNotAuthenticate;
use Coxlr\RingCentral\Exceptions\CouldNotSendMessage;
use RingCentral\SDK\Http\ApiException;
use RingCentral\SDK\Http\ApiResponse;
use RingCentral\SDK\Platform\Platform;
use RingCentral\SDK\SDK;

class RingCentral
{
    protected ?Platform $ringCentral = null;

    protected string $serverUrl;

    protected string $clientId;

    protected string $clientSecret;

    protected string $username;

    protected ?string $operatorExtension = null;

    protected ?string $adminExtension = null;

    protected string $loggedInExtension;

    protected string $loggedInExtensionId;

    protected string $operatorToken;

    protected ?string $adminToken = null;

    public function setClientId(string $clientId): static
    {
        $this->clientId = $clientId;

        return $this;
    }

    public function setClientSecret(string $clientSecret): static
    {
        $this->clientSecret = $clientSecret;

        return $this;
    }

    public function setServerUrl(string $serverUrl): static
    {
        $this->serverUrl = $serverUrl;

        return $this;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function setOperatorToken(string $operatorToken): static
    {
        $this->operatorToken = $operatorToken;

        return $this;
    }

    public function setAdminToken(string $adminToken): static
    {
        $this->adminToken = $adminToken;

        return $this;
    }

    public function clientId(): string
    {
        return $this->clientId;
    }

    public function clientSecret(): string
    {
        return $this->clientSecret;
    }

    public function serverUrl(): string
    {
        return $this->serverUrl;
    }

    public function username(): string
    {
        return $this->username;
    }

    public function operatorExtension(): null|string
    {
        return $this->operatorExtension;
    }

    public function operatorToken(): string
    {
        return $this->operatorToken;
    }

    public function adminExtension(): null|string
    {
        return $this->adminExtension ? : $this->operatorExtension;
    }

    public function adminToken(): string
    {
        return $this->adminToken ?: $this->operatorToken;
    }

    public function connect(): void
    {
        $this->ringCentral = (new SDK($this->clientId(), $this->clientSecret(), $this->serverUrl()))->platform();
    }

    public function loginOperator(): void
    {
        $this->login($this->operatorToken());
    }

    public function loginAdmin(): void
    {
        $this->login($this->adminToken());
    }

    public function login(string $token): void
    {
        $this->ringCentral->login([ 'jwt' => $token, ]);

        $this->setLoggedInExtension();
    }

    public function setLoggedInExtension(): void
    {
        $extension = $this->ringCentral->get('/account/~/extension/~/')->json();
        $this->loggedInExtensionId = $extension->id;
        $this->loggedInExtension = $extension->extensionNumber;
    }

    public function loggedInExtensionId(): string
    {
        return $this->loggedInExtensionId;
    }

    public function loggedInExtension(): string
    {
        return $this->loggedInExtension;
    }

    /**
     * @throws CouldNotAuthenticate
     */
    public function authenticateOperator(): bool
    {
        if (! $this->ringCentral) {
            $this->connect();
        }

        if (! $this->operatorLoggedIn()) {
            $this->loginOperator();
        }

        if (! $this->ringCentral->loggedIn()) {
            throw CouldNotAuthenticate::operatorLoginFailed();
        }

        return true;
    }

    public function operatorLoggedIn(): bool
    {
        if ($this->ringCentral->loggedIn()) {
            return $this->loggedInExtension() === $this->operatorExtension();
        }

        return false;
    }

    /**
     * @throws CouldNotAuthenticate|ApiException
     */
    public function authenticateAdmin(): bool
    {
        if (! $this->ringCentral) {
            $this->connect();
        }

        if (! $this->adminLoggedIn()) {
            $this->loginAdmin();
        }

        if (! $this->ringCentral->loggedIn()) {
            throw CouldNotAuthenticate::adminLoginFailed();
        }

        return true;
    }

    /**
     * @throws ApiException
     */
    public function adminLoggedIn(): bool
    {
        if ($this->ringCentral->loggedIn()) {
            return $this->ringCentral->get('/account/~/extension/~/')->json()->extensionNumber === $this->adminExtension();
        }

        return false;
    }

    /**
     * @throws CouldNotSendMessage
     * @throws CouldNotAuthenticate
     * @throws ApiException
     */
    public function sendMessage(array $message): ApiResponse
    {
        if (empty($message['to'])) {
            throw CouldNotSendMessage::toNumberNotProvided();
        }

        if (empty($message['text'])) {
            throw CouldNotSendMessage::textNotProvided();
        }

        $this->authenticateOperator();

        return $this->ringCentral->post('/account/~/extension/~/sms', [
            'from' => ['phoneNumber' => $this->username()],
            'to' => [
                ['phoneNumber' => $message['to']],
            ],
            'text' => $message['text'],
        ]);
    }

    /**
     * @throws CouldNotAuthenticate
     * @throws ApiException
     */
    public function getExtensions(): array
    {
        $this->authenticateAdmin();

        $r = $this->ringCentral->get('/account/~/extension');

        return $r->json()->records;
    }

    /**
     * @throws ApiException
     */
    public function getMessages(string $extensionId, ?object $fromDate = null, ?object $toDate = null, ?int $perPage = 100): array
    {
        $dates = [];

        if ($fromDate) {
            $dates['dateFrom'] = $fromDate->format('c');
        }

        if ($toDate) {
            $dates['dateTo'] = $toDate->format('c');
        }

        $r = $this->ringCentral->get('/account/~/extension/'.$extensionId.'/message-store', array_merge(
            [
                'messageType' => 'SMS',
                'perPage' => $perPage,
            ],
            $dates
        ));

        return $r->json()->records;
    }

    /**
     * @throws ApiException
     * @throws CouldNotAuthenticate
     */
    public function getOperatorMessages(?object $fromDate = null, ?object $toDate = null, ?int $perPage = 100): array
    {
        $this->authenticateOperator();

        return $this->getMessages('~', $fromDate, $toDate, $perPage);
    }

    /**
     * @throws CouldNotAuthenticate
     * @throws ApiException
     */
    public function getMessagesForExtensionId(string $extensionId, ?object $fromDate = null, ?object $toDate = null, ?int $perPage = 100): array
    {
        $this->authenticateAdmin();

        return $this->getMessages($extensionId, $fromDate, $toDate, $perPage);
    }

    /**
     * @throws CouldNotAuthenticate
     * @throws ApiException
     */
    public function getMessageAttachmentById(string $extensionId, string $messageId, string $attachementId): ApiResponse
    {
        $this->authenticateAdmin();

        return $this->ringCentral->get('/account/~/extension/'.$extensionId.'/message-store/' . $messageId . '/content/' . $attachementId);
    }
}
