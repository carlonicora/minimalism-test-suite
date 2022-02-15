<?php
namespace CarloNicora\Minimalism\TestSuite\Factories;

use CarloNicora\Minimalism\Exceptions\MinimalismException;
use CarloNicora\Minimalism\Factories\MinimalismFactories;
use CarloNicora\Minimalism\Interfaces\Sql\Interfaces\SqlInterface;
use CarloNicora\Minimalism\Services\MySQL\Factories\SqlQueryFactory;
use CarloNicora\Minimalism\TestSuite\Interfaces\TableDataInterface;

class DataFactory
{
    /**
     * @param SqlInterface $data
     * @param string $dataFolder
     * @return void
     * @throws MinimalismException
     */
    public static function cleanDatabases(
        SqlInterface $data,
        string $dataFolder,
    ): void
    {
        foreach (glob($dataFolder . DIRECTORY_SEPARATOR . '*/*.php', GLOB_NOSORT) as $dataFile) {

            /** @noinspection PhpUndefinedMethodInspection */
            $tableClass = MinimalismFactories::getNamespace($dataFile)::getTableClass();
            $factory = SqlQueryFactory::create($tableClass);
            /** @noinspection UnusedFunctionResultInspection */
            $data->read(
                queryFactory: $factory->setSql('TRUNCATE TABLE ' . $factory->getTable()->getFullName()),
            );
        }
    }

    /**
     * @param SqlInterface $data
     * @param string $dataFolder
     */
    public static function generateTestData(
        SqlInterface $data,
        string $dataFolder,
    ): void
    {
        foreach (glob($dataFolder . DIRECTORY_SEPARATOR . '*/*.php', GLOB_NOSORT) as $dataFile) {
            /** @var TableDataInterface $tableDataClass */
            $tableDataClass = MinimalismFactories::getNamespace($dataFile);

            $records = [];

            /** @noinspection PhpUndefinedMethodInspection */
            foreach ($tableDataClass::cases() as $record) {
                $records[] = $record->row();
            }

            /** @noinspection UnusedFunctionResultInspection */
            $data->create($records);
        }
    }
}