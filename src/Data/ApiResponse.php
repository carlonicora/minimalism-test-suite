<?php
namespace CarloNicora\Minimalism\TestSuite\Data;

use CarloNicora\JsonApi\Document;
use CarloNicora\JsonApi\Objects\ResourceObject;
use CarloNicora\Minimalism\Enums\HttpCode;
use CurlHandle;
use Exception;
use Throwable;

class ApiResponse
{
    protected ?HttpCode $responseHttpCode = null;
    protected string|Document $response;
    protected string $error = '';

    /**
     * ApiResponse constructor.
     * @param false|CurlHandle|resource $curl
     * @param string|bool $curlResponse
     * @param array $resoponseHttpHeaders
     * @throws Exception
     */
    public function __construct(
        false|CurlHandle $curl,
        string|bool $curlResponse,
        protected array $resoponseHttpHeaders
    )
    {
        if (curl_error($curl)) {
            $this->error = 'Curl Error: ' . curl_error($curl);
        }

        if ($httpCode = curl_getinfo($curl, option: CURLINFO_RESPONSE_CODE)) {
            $this->responseHttpCode = HttpCode::from($httpCode);
        }

        $returnedJson = substr($curlResponse, curl_getinfo($curl, option: CURLINFO_HEADER_SIZE));

        if ($httpCode >= 400) {
            $this->error = 'API returned error: ' . $returnedJson;
        }

        if (!empty($returnedJson)) {
            try {
                $apiResponse = json_decode($returnedJson, true, 512, JSON_THROW_ON_ERROR);
                $this->response = new Document($apiResponse);
            } catch (Exception| Throwable) {
                $this->response = $returnedJson;
            }
        }
    }

    /**
     * @return HttpCode|null
     */
    public function getHttpCode(): ?HttpCode
    {
        return $this->responseHttpCode;
    }

    /**
     * @return array
     */
    public function getHttpHeaders(): array
    {
        return $this->resoponseHttpHeaders;
    }

    /**
     * @return Document|string
     */
    public function getResponse(): Document|string
    {
        return $this->response;
    }

    /**
     * @return ResourceObject
     */
    public function getFirstResource(): ResourceObject
    {
        return $this->response->resources[0];
    }

    /**
     * @return int
     */
    public function getResourceCount(): int
    {
        return count($this->response->resources);
    }

    /**
     * @return Document
     */
    public function getDocument(): Document
    {
        return $this->response;
    }

    /**
     * @return string
     */
    public function getError(): string
    {
        return $this->error;
    }
}