<?php

namespace OCA\TimeTracker\Tests\Unit\Mock;

use OCP\IRequest;

class RequestMock implements IRequest
{
    public $params = [];

    public function __get($name) {
        return $this->params[$name] ?? null;
    }

    public function __isset($name) {
        return isset($this->params[$name]);
    }

    public function getHeader(string $name): string { return ""; }

    public function getParam(string $key, $default = null) { return $this->params[$key] ?? $default; }

    public function getParams(): array { return $this->params; }

    public function getMethod(): string { return "GET"; }

    public function getUploadedFile(string $key) { return null; }

    public function getEnv(string $key) { return null; }

    public function getCookie(string $key) { return null; }

    public function passesCSRFCheck(): bool { return true; }

    public function passesStrictCookieCheck(): bool { return true; }

    public function passesLaxCookieCheck(): bool { return true; }

    public function getId(): string { return ""; }

    public function getRemoteAddress(): string { return ""; }

    public function getServerProtocol(): string { return ""; }

    public function getHttpProtocol(): string { return ""; }

    public function getRequestUri(): string { return ""; }

    public function getRawPathInfo(): string { return ""; }

    public function getPathInfo(): string { return ""; }

    public function getScriptName(): string { return ""; }

    public function isUserAgent(array $agent): bool { return false; }

    public function getInsecureServerHost(): string { return ""; }

    public function getServerHost(): string { return ""; }
}