<?php
namespace CarloNicora\Minimalism\TestSuite\Commands;

use CarloNicora\Minimalism\TestSuite\Data\ApiRequest;
use CarloNicora\Minimalism\TestSuite\Data\ApiResponse;
use Exception;

class ApiCallerCommand
{
    /**
     * @param ApiRequest $request
     * @param string $server
     *
     * @param string|null $hostName
     * @param bool|null $isTestEnvironment
     * @return ApiResponse
     * @throws Exception
     */
    public static function call(
        ApiRequest $request,
        string $server,
        ?string $hostName=null,
        ?bool $isTestEnvironment=false,
    ): ApiResponse
    {
        $curl = curl_init();

        $options = $request->getCurlOpts(
            serverUrl: $server,
            hostName: $hostName,
            isTestEnvironment: $isTestEnvironment,
        );

        curl_setopt_array($curl, $options);

        $curlResponse = curl_exec($curl);

        $result = new ApiResponse($curl, $curlResponse, $request::$responseHeaders);

        curl_close($curl);

        unset($curl);

        return $result;
    }
}