<?php
namespace CarloNicora\Minimalism\TestSuite\Interfaces;

use CarloNicora\Minimalism\Interfaces\Sql\Interfaces\SqlDataObjectInterface;
use CarloNicora\Minimalism\Interfaces\Sql\Interfaces\SqlQueryFactoryInterface;

interface TableDataInterface
{
    /**
     * @return SqlDataObjectInterface|SqlQueryFactoryInterface
     */
    public function row(
    ): SqlDataObjectInterface|SqlQueryFactoryInterface;

    /**
     * @return string
     */
    public static function getTableClass(
    ): string;
}