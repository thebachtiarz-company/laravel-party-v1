<?php

declare(strict_types=1);

namespace TheBachtiarz\Party\Bmkg\Libraries;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http as CURL;
use TheBachtiarz\Base\App\Libraries\Curl\AbstractCurl;
use TheBachtiarz\Base\App\Libraries\Curl\Data\CurlResponseInterface;
use TheBachtiarz\Party\Bmkg\Interfaces\Configs\BmkgConfigInterface;

use function array_merge;
use function assert;
use function count;
use function sprintf;

abstract class AbstractBmkgLibrary extends AbstractCurl
{
    // ? Public Methods

    // ? Protected Methods

    protected function sendRequest(string $method): CurlResponseInterface
    {
        $pendingRequest = $this->curl();

        if ($this->token) {
            $pendingRequest->withToken($this->token);
        }

        if ($this->userAgent) {
            $pendingRequest->withUserAgent($this->userAgent);
        }

        $response = $pendingRequest->{$method}($this->urlDomainResolver(), $this->bodyDataResolver());
        assert($response instanceof Response);

        return $this->customResponse($response);
    }

    protected function urlDomainResolver(): string
    {
        return sprintf(
            '%s/%s',
            BmkgConfigInterface::BASE_URL_DATA,
            $this->getSubUrl(),
        );
    }

    protected function bodyDataResolver(): array
    {
        return $this->body;
    }

    /**
     * Custom request curl response
     */
    abstract protected function customResponse(Response $response): CurlResponseInterface;

    // ? Private Methods

    /**
     * Request curl init
     */
    private function curl(): PendingRequest
    {
        $headers = ['Accept' => 'application/json'];

        if (count($this->header)) {
            $headers = array_merge($headers, $this->header);
        }

        return CURL::withHeaders($headers);
    }

    // ? Getter Modules

    // ? Setter Modules
}
