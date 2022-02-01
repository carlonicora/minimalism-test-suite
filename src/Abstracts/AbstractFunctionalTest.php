<?php

namespace CarloNicora\Minimalism\TestSuite\Abstracts;

use CarloNicora\Minimalism\TestSuite\Data\ApiRequest;
use CarloNicora\Minimalism\TestSuite\Data\ApiResponse;
use Exception;
use PHPUnit\Framework\TestCase;

abstract class AbstractFunctionalTest extends TestCase
{
    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        usleep(100000);
    }

    /**
     * @throws Exception
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::deleteAllFilesInFolder(__DIR__ . '/../../../../../cache');
        self::deleteAllFilesInFolder(__DIR__ . '/../../../../../logs', false);
    }

    /**
     * @param string $dir
     * @param bool $recursive
     */
    private static function deleteAllFilesInFolder(
        string $dir,
        bool $recursive=true,
    ): void
    {
        foreach(glob($dir . '/*', GLOB_NOSORT) as $file) {
            if(is_file($file)) {
                unlink($file);
            } elseif ($recursive){
                self::deleteAllFilesInFolder($file);
                rmdir($file);
            }
        }
    }

    /**
     * @param ApiRequest $request
     * @param string|null $serverUrl
     * @param string|null $hostName
     * @return ApiResponse
     * @throws Exception
     */
    protected static function call(
        ApiRequest $request,
        ?string $serverUrl=null,
        ?string $hostName=null,
    ): ApiResponse
    {
        $curl = curl_init();

        if ($serverUrl === null) {
            /** @noinspection CallableParameterUseCaseInTypeContextInspection */
            $serverUrl = $_ENV['MINIMALISM_SERVICE_TESTER_SERVER'] ?? 'http://docker.for.mac.localhost/v1.0';
        }

        $options = $request->getCurlOpts(serverUrl: $serverUrl, hostName: $hostName, isTestEnvironment: true, requestHeaders: $request->requestHeader);

        curl_setopt_array($curl, $options);

        $curlResponse = curl_exec($curl);

        $result = new ApiResponse($curl, $curlResponse, $request::$responseHeaders);

        if (isset($curl)) {
            curl_close($curl);
        }

        unset($curl);

        return $result;
    }
}