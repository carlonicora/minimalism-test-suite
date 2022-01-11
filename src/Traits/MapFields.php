<?php
namespace CarloNicora\Minimalism\TestSuite\Traits;

use BackedEnum;
use RuntimeException;

trait MapFields
{
    /**
     * @param array $fieldsDefinitions
     * @param array $fieldValues
     * @return array
     */
    protected static function mapFields(
        array $fieldsDefinitions,
        array $fieldValues
    ): array
    {
        if (empty($fieldValues)) {
            return [];
        }

        $fieldNames = array_keys($fieldsDefinitions);
        if (count($fieldNames) !== count($fieldValues)) {
            throw new RuntimeException('Fieldnames count not equal to fieldvalues count');
        }

        foreach ($fieldValues as &$fieldValue) {
            if ($fieldValue instanceof BackedEnum) {
                $fieldValue = $fieldValue->value;
            }
        }

        return array_combine($fieldNames, $fieldValues);
    }
}