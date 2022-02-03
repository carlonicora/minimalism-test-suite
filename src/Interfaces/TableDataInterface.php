<?php
namespace CarloNicora\Minimalism\TestSuite\Interfaces;

use CarloNicora\Minimalism\Interfaces\Sql\Interfaces\SqlDataObjectInterface;

interface TableDataInterface
{
    /**
     * @return SqlDataObjectInterface
     */
    public function row(
    ): SqlDataObjectInterface;

    /**
     * @return string
     */
    public function tableClass(
    ): string;
}