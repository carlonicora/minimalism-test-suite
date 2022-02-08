<?php
namespace CarloNicora\Minimalism\TestSuite\Interfaces;

use CarloNicora\Minimalism\Interfaces\Sql\Interfaces\SqlDataObjectInterface;
use CarloNicora\Minimalism\Interfaces\Sql\Interfaces\SqlFactoryInterface;

interface TableDataInterface
{
    /**
     * @return SqlDataObjectInterface|SqlFactoryInterface
     */
    public function row(
    ): SqlDataObjectInterface|SqlFactoryInterface;

    /**
     * @return string
     */
    public static function getTableClass(
    ): string;
}