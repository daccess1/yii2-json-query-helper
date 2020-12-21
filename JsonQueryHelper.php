<?php

namespace daccess1\JSONQueryHelper;

use Exception;
use yii\base\InvalidArgumentException;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * Helper class for creating JSON_CONTAINS SQL query strings
 *
 * @package common\helpers
 */
class JSONQueryHelper
{
    private const MODE_AND = 'AND';
    private const MODE_OR = 'OR';
    private const MODES = [self::MODE_AND, self::MODE_OR];

    /**
     * Create JSON_CONTAINS [[yii\db\Expression]] expression fo usage with ActiveRecord query
     *
     * @param array $params array of parameters for generating SQL query expression
     *
     * @return Expression
     *
     * @throws Exception
     */
    public static function JSONContains(array $params) : Expression
    {
        $count_params = count($params);

        if ($count_params === 2) {
            $field = ArrayHelper::getValue($params, 0);

            $field_params = static::parseFieldParam($field);
            $field_name = ArrayHelper::getValue($field_params, 'field_name');
            $path = ArrayHelper::getValue($field_params, 'path');

            $value = ArrayHelper::getValue($params, 1);

            if (is_array($value)) {
                return static::createOrJsonContainsExpression($field_name, $value, $path);
            } else {
                return static::createJsonContainsExpression($field_name, $value, $path);
            }
        }

        if ($count_params === 3) {
            $mode = strtoupper(ArrayHelper::getValue($params, 0));

            if (!in_array($mode, static::MODES)) {
                throw new InvalidArgumentException('JSONContains first param must be either "AND" or "OR"');
            }

            $values = ArrayHelper::getValue($params, 2);

            if (!is_array($values)) {
                throw new InvalidArgumentException('JSONContains third param must be an array');
            }

            $field = ArrayHelper::getValue($params, 1);
            $field_params = static::parseFieldParam($field);
            $field_name = ArrayHelper::getValue($field_params, 'field_name');
            $path = ArrayHelper::getValue($field_params, 'path');

            if ($mode === static::MODE_AND) {
                return static::createAndJsonContainsExpression($field_name, $values, $path);
            }

            if ($mode === static::MODE_OR) {
                return static::createOrJsonContainsExpression($field_name, $values, $path);
            }
        }

        throw new InvalidArgumentException('JSONContains params array must contain only 2 or 3 elements');
    }

    /**
     * Create [[Expression]] to check if JSON field contains single item
     *
     * @param string $field field name
     * @param mixed $item value to search for
     * @param string $path JSON path
     *
     * @return Expression
     */
    private static function createJsonContainsExpression(string $field, $item, string $path) : Expression
    {
        $expression = static::generateExpression($field, $item, $path);

        return new Expression($expression);
    }

    /**
     * Create [[Expression]] to check if JSON field contains any of items
     *
     * @param string $field field name
     * @param array $items list of values to search for
     * @param string $path JSON path
     *
     * @return Expression
     */
    private static function createOrJsonContainsExpression(string $field, array $items, string $path) : Expression
    {
        $i = 0;
        $expression = '(';

        foreach ($items as $item) {
            if ($i) {
                $expression .= ' OR ';
            }

            $expression .= static::generateExpression($field, $item, $path);
            $i++;
        }

        $expression .= ')';

        return new Expression($expression);
    }

    /**
     * Create [[Expression]] to check if JSON field contains all items
     *
     * @param string $field field name
     * @param array $items list of values to search for
     * @param string $path JSON path
     *
     * @return Expression
     */
    private static function createAndJsonContainsExpression(string $field, array $items, string $path) : Expression
    {
        $i = 0;
        $expression = '(';

        foreach ($items as $item) {
            if ($i) {
                $expression .= ' AND ';
            }

            $expression .= static::generateExpression($field, $item, $path);;
            $i++;
        }

        $expression .= ')';

        return new Expression($expression);
    }

    /**
     * Generate SQL expression
     *
     * @param string $field
     * @param $item
     * @param string $path
     *
     * @return string
     */
    private static function generateExpression(string $field, $item, string $path = '$') : string
    {
        return 'JSON_CONTAINS(`' . $field . '`, "' . $item . '", "' . $path . '")';
    }

    /**
     * Parse field param and get JSON path
     *
     * @param $field
     *
     * @return string[]
     * @throws Exception
     */
    private static function parseFieldParam($field) : array
    {
        if (is_string($field)) {
            if (!strlen($field)) {
                throw new InvalidArgumentException('JSONContains field name cannot be empty');
            }

            return [
                'field_name' => $field,
                'path' => '$',
            ];
        }

        if (is_array($field)) {
            $field_name = ArrayHelper::getValue($field, 0);

            if (!is_string($field_name) || !strlen($field_name)) {
                throw new InvalidArgumentException('JSONContains field name cannot be empty');
            }

            $path = ArrayHelper::getValue($field, 1);

            if (!is_string($path) || !strlen($path)) {
                throw new InvalidArgumentException('JSONContains path must be a string and cannot be empty');
            }

            return [
                'field_name' => $field_name,
                'path' => $path,
            ];
        }

        throw new InvalidArgumentException('JSONContains field could not be parsed');
    }
}