<?php

namespace Junges\Pix;

use Junges\Pix\Contracts\CanResolveEndpoints;
use Junges\Pix\Exceptions\Psp\InvalidPspException;

class Psp
{
    public static string $defaultPsp = 'default';
    public ?string $currentPsp = null;

    public static function getConfig(): Psp
    {
        return new static();
    }

    public static function defaultPsp($psp)
    {
        self::$defaultPsp = $psp;
    }

    public function currentPsp(string $psp = null): Psp
    {
        $psp = $psp ?? self::$defaultPsp;

        $this->currentPsp = $psp;

        return $this;
    }

    public function getCurrentPsp(): ?string
    {
        return $this->currentPsp ?? self::$defaultPsp;
    }

    public static function getDefaultPsp(): string
    {
        return self::$defaultPsp;
    }

    public static function availablePsps(): array
    {
        return array_keys(config('laravel-pix.psp'));
    }

    public function getOauthTokenUrl(): string
    {
        return $this->getPspConfig($this->getCurrentPsp())['oauth_token_url'] ?? '';
    }

    public function getBankingOauthTokenUrl(): string
    {
        return $this->getPspConfig($this->getCurrentPsp())['banking_oauth_token_url'] ?? '';
    }

    public function getPspSSLCertificate(): string
    {
        return $this->getPspConfig($this->getCurrentPsp())['ssl_certificate'] ?? '';
    }

    public function getBankingPspSSLCertificate(): string
    {
        return $this->getPspConfig($this->getCurrentPsp())['banking_ssl_certificate'] ?? '';
    }

    public function getPspBaseUrl(): string
    {
        return $this->getPspConfig($this->getCurrentPsp())['base_url'] ?? '';
    }

    public function getPspClientId(): string
    {
        return $this->getPspConfig($this->getCurrentPsp())['client_id'] ?? '';
    }

    public function getPspClientSecret(): string
    {
        return $this->getPspConfig($this->getCurrentPsp())['client_secret'] ?? '';
    }

    public function getPspPixKey(): string
    {
        return $this->getPspConfig($this->getCurrentPsp())['pix_key'] ?? '';
    }

    public function getPspApiKey(): string
    {
        return $this->getPspConfig($this->getCurrentPsp())['api_key'] ?? '';
    }

    public function getPspBankingBaseUrl(): string
    {
        return $this->getPspConfig($this->getCurrentPsp())['banking_base_url'] ?? '';
    }

    public function getPspBankingClientId(): string
    {
        return $this->getPspConfig($this->getCurrentPsp())['banking_client_id'] ?? '';
    }

    public function getPspBankingClientSecret(): string
    {
        return $this->getPspConfig($this->getCurrentPsp())['banking_client_secret'] ?? '';
    }

    public function getPspOauthBearerToken(): string
    {
        return $this->getPspConfig($this->getCurrentPsp())['oauth_bearer_token'] ?? '';
    }

    public function getAuthenticationClass(): string
    {
        return $this->getPspConfig($this->getCurrentPsp())['authentication_class'] ?? '';
    }

    public function getBankingAuthenticationClass(): string
    {
        return $this->getPspConfig($this->getCurrentPsp())['banking_authentication_class'] ?? '';
    }

    public function getEndpointsResolver(): CanResolveEndpoints
    {
        return app($this->getPspConfig($this->getCurrentPsp())['resolve_endpoints_using']);
    }

    public function getBankingEndpointsResolver(): CanResolveEndpoints
    {
        return app($this->getPspConfig($this->getCurrentPsp())['resolve_banking_endpoints_using']);
    }

    private function getPspConfig(string $psp)
    {
        throw_if(!$this->validatePsp($this->getCurrentPsp()), InvalidPspException::pspNotFound($this->getCurrentPsp()));

        return config("laravel-pix.psp.{$psp}");
    }

    private function validatePsp(string $psp): bool
    {
        return in_array($psp, $this->availablePsps());
    }
}
