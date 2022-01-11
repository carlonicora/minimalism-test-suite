<?php
namespace CarloNicora\Minimalism\TestSuite\Interfaces;

use CarloNicora\Minimalism\Interfaces\Data\Interfaces\TableInterface;

interface TableDataInterface
{
    /**
     * @return string|TableInterface
     */
    public static function tableClass(): string|TableInterface;

    /**
     * @return array
     */
    public function row(): array;
}