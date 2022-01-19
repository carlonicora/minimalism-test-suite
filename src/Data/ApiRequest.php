<?php
namespace CarloNicora\Minimalism\TestSuite\Data;

use CarloNicora\Minimalism\TestSuite\Enums\Verbs;
use CURLFile;
use Exception;

class ApiRequest
{
    public static array $responseHeaders = [];

    /**
     * Data constructor.
     * @param Verbs $verb
     * @param string $endpoint
     * @param array|null $body
     * @param array|null $payload
     * @param string|null $bearer
     * @param array $files
     * @param array $requestHeader
     */
    public function __construct(
        public Verbs $verb,
        public string $endpoint,
        public ?array $body = null,
        public ?array $payload = null,
        public ?string $bearer = null,
        public array $files = [],
        public array $requestHeader = []
    )
    {
    }

    /**
     *
     */
    protected function getCurlHttpHeaders(
        ?string $hostName=null,
    ): array
    {
        if ($hostName === null){
            /** @noinspection CallableParameterUseCaseInTypeContextInspection */
            $hostName = $_ENV['MINIMALISM_SERVICE_TESTER_HOSTNAME'];
        }
        $httpHeaders = [
            'Host:'.$hostName,
            'Test-Environment:1'
        ];

        if (!empty($this->files)) {
            $httpHeaders[] = 'Content-Type:multipart/form-data';
        } else {
            $httpHeaders[] ='Content-Type:application/vnd.api+json';
        }

        if ($this->bearer !== null) {
            $httpHeaders[] = 'Authorization:Bearer ' . $this->bearer;
        }

        return array_merge($httpHeaders, $this->requestHeader);
    }

    /**
     * @param string $serverUrl
     * @param string|null $hostName
     * @return array
     * @throws Exception
     */
    public function getCurlOpts(
        string $serverUrl,
        ?string $hostName=null,
    ): array
    {
        /** @noinspection CurlSslServerSpoofingInspection */
        $opts = [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HTTPHEADER => $this->getCurlHttpHeaders(hostName: $hostName),
            CURLOPT_HEADER => 1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        ];

        $endpointWithUriParams = null;

        switch ($this->verb){
            case Verbs::Post:
                $opts [CURLOPT_POST] = 1;
                $opts[CURLOPT_POSTFIELDS] = [];

                if (!empty($this->files)) {
                    $buildFiles = static function(
                        array $files,
                        bool $subLevel = false
                    ) use (&$buildFiles): array
                    {
                        $fileArray = [];
                        foreach ($files as $fileKey => $file) {
                            $multidimensialKey = $subLevel ? '[' . $fileKey . ']' : $fileKey;
                            if (!empty($file['path'])) {
                                $cFile = new CURLFile(
                                    $file['path'],
                                    $file['mimeType'],
                                    $file['name']
                                );

                                $fileArray [$multidimensialKey] = $cFile;
                            } elseif (!empty($file['tmp_name'])){
                                $cFile = new CURLFile(
                                    $file['tmp_name'],
                                    $file['type'],
                                    $file['name']
                                );

                                $fileArray [$multidimensialKey] = $cFile;
                            } else {
                                foreach ($buildFiles($file, true) as $subFileKey => $subFile) {
                                    $fileArray [$multidimensialKey . $subFileKey ] = $subFile;
                                }
                            }
                        }

                        return $fileArray;
                    };

                    $opts[CURLOPT_POSTFIELDS] = $buildFiles($this->files);
                }

                if ($this->body !== null){
                    if ($opts[CURLOPT_POSTFIELDS] === []){
                        $opts[CURLOPT_POSTFIELDS] = http_build_query($this->body) ;
                    } else {
                        $opts[CURLOPT_POSTFIELDS] = array_merge($opts[CURLOPT_POSTFIELDS], $this->body);
                    }
                }

                if ($this->payload !== null){
                    if ($opts[CURLOPT_POSTFIELDS] === []){
                        $opts[CURLOPT_POSTFIELDS] = json_encode($this->payload, JSON_THROW_ON_ERROR);
                    } else {
                        $opts[CURLOPT_POSTFIELDS]['payload'] = json_encode($this->payload, JSON_THROW_ON_ERROR);
                    }
                }

                break;
            case Verbs::Delete:
            case Verbs::Patch:
            case Verbs::Put:
                $opts [CURLOPT_CUSTOMREQUEST] = $this->verb->value;

                if ($this->body !== null){
                    $opts[CURLOPT_POSTFIELDS] = http_build_query($this->body) ;
                } elseif ($this->payload !== null){
                    $opts[CURLOPT_POSTFIELDS] = json_encode($this->payload, JSON_THROW_ON_ERROR);
                }

                break;
            default:
                if (isset($this->body)) {
                    $query = http_build_query($this->body);
                    if (!empty($query)) {
                        $endpointWithUriParams .= ((str_contains($this->endpoint, '?')) ? '&' : '?') . $query;
                    }
                }
                break;
        }

        $opts[CURLOPT_URL] = $serverUrl . ($endpointWithUriParams ?? $this->endpoint);

        $opts[CURLOPT_HEADERFUNCTION] = static function($stub, $header)
        {
            $len = strlen($header);
            $header = explode(':', $header, 2);
            if (count($header) < 2) // ignore invalid headers
            {
                return $len;
            }

            static::$responseHeaders[strtolower(trim($header[0]))][] = trim($header[1]);

            return $len;
        };

        return $opts;
    }
}