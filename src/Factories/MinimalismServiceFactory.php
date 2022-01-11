<?php
namespace CarloNicora\Minimalism\TestSuite\Factories;

use CarloNicora\Minimalism\Interfaces\ServiceInterface;
use CarloNicora\Minimalism\Minimalism;
use Exception;

class MinimalismServiceFactory
{
    /** @var Minimalism  */
    protected Minimalism $minimalism;

    /**
     *
     */
    public function __construct(
    )
    {
        $_SERVER['HTTP_TEST_ENVIRONMENT'] = 1;
        $this->minimalism = new Minimalism();
    }

    /**
     * @template InstanceOfType
     * @param class-string<InstanceOfType> $serviceName
     * @return InstanceOfType
     * @throws Exception
     */
    protected function create(
        string $serviceName,
    ): ServiceInterface
    {
        return $this->minimalism->getService($serviceName);
    }
}