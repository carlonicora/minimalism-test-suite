<?php
namespace CarloNicora\Minimalism\TestSuite\Factories;

use CarloNicora\Minimalism\Interfaces\Data\Interfaces\DataInterface;
use Exception;

class DataFactory
{
    /**
     * @throws Exception
     */
    public static function cleanDatabases(
        DataInterface $data,
        array $tables,
    ): void
    {
        foreach ($tables as $tableClass => $tableDataCleass) {
            /** @noinspection PhpUndefinedMethodInspection */
            $data->runSQL(
                tableInterfaceClassName: $tableClass,
                sql: 'TRUNCATE TABLE ' . $tableClass::getTableName()
            );
        }
    }

    /**
     * @param DataInterface $data
     * @param array $tables
     */
    public static function generateTestData(
        DataInterface $data,
        array $tables,
    ): void
    {
        foreach ($tables as $tableClass => $tableDataClass) {
            $records = [];

            foreach ($tableDataClass::cases() as $record) {
                $row = $record->row();
                if (! empty($row)) {
                    $records [] = $record->row();
                }
            }

            if (! empty($records)) {
                /** @noinspection UnusedFunctionResultInspection */
                $data->insert(
                    tableInterfaceClassName: $tableClass,
                    records: $records
                );
            }
        }
    }
}