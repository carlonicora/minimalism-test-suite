<?php
namespace CarloNicora\Minimalism\TestSuite\Factories;

use CarloNicora\Minimalism\Interfaces\ServiceInterface;
use CarloNicora\Minimalism\Minimalism;
use Exception;

class MinimalismServiceFactory
{
    /** @var Minimalism|null  */
    private static ?Minimalism $minimalism=null;

    /**
     * @return void
     */
    private static function setupMinimalism(
    ): void
    {
        $_SERVER['HTTP_TEST_ENVIRONMENT'] = 1;
        self::$minimalism = new Minimalism();
    }

    /**
     * @template InstanceOfType
     * @param class-string<InstanceOfType> $serviceName
     * @return InstanceOfType
     * @throws Exception
     */
    public static function create(
        string $serviceName,
    ): ServiceInterface
    {
        if (self::$minimalism === null){
            self::setupMinimalism();
        }

        return self::$minimalism->getService($serviceName);
    }
}