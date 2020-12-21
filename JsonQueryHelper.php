<?php

namespace daccess1\JSONQueryHelper;

use Exception;
use yii\base\InvalidArgumentException;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * Helper class for creating JSON_CONTINS SQL query strings
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
     * @param array $params
     *
     * @return Expression
     *
     * @throws Exception
     */
    public static function JSONContains(array $params) : Expression
    {
        $count_params = count($params);

        if ($count_params !== 2 && $count_params !== 3) {
            throw new InvalidArgumentException('JSONContains params array must contain only 2 or 3 elements');
        }

        if ($count_params === 2) {
            $field = ArrayHelper::getValue($params, 0);
            $value = ArrayHelper::getValue($params, 1);

            if (!is_string($field) || !strlen($field)) {
                throw new InvalidArgumentException('JSONContains second param must be a string and cannot be empty');
            }

            if (is_array($value)) {
                return static::createOrJsonContainsExpression($field, $value);
            } else {
                return static::createJsonContainsExpression($field, $value);
            }
        }

        if ($count_params === 3) {
            $mode = strtoupper(ArrayHelper::getValue($params, 0));
            $field = ArrayHelper::getValue($params, 1);
            $values = ArrayHelper::getValue($params, 2);

            if (!in_array($mode, static::MODES)) {
                throw new InvalidArgumentException('JSONContains first param must be either "AND" or "OR"');
            }

            if (!is_string($field) || !strlen($field)) {
                throw new InvalidArgumentException('JSONContains second param must be a string and cannot be empty');
            }

            if (!is_array($values)) {
                throw new InvalidArgumentException('JSONContains third param must be an array');
            }

            if ($mode === static::MODE_AND) {
                return static::createAndJsonContainsExpression($field, $values);
            }

            if ($mode === static::MODE_OR) {
                return static::createOrJsonContainsExpression($field, $values);
            }
        }
    }

    /**
     * Create [[Expression]] to check if a JSON field contains single item
     *
     * @param string $field
     * @param mixed $item
     *
     * @return Expression
     */
    private static function createJsonContainsExpression(string $field, $item) : Expression
    {
        $expression = 'JSON_CONTAINS(`' . $field . '`, "' . $item . '", "$")';

        return new Expression($expression);
    }

    /**
     * Create [[Expression]] to check if a JSON field contains any of items
     *
     * @param string $field
     * @param array $items
     *
     * @return Expression
     */
    private static function createOrJsonContainsExpression(string $field, array $items) : Expression
    {
        $i = 0;
        $expression = '(';

        foreach ($items as $item) {
            if ($i) {
                $expression .= ' OR ';
            }

            $expression .= 'JSON_CONTAINS(`' . $field . '`, "' . $item . '", "$")';
            $i++;
        }

        $expression .= ')';

        return new Expression($expression);
    }

    /**
     * Create [[Expression]] to check if a JSON field contains all of items
     *
     * @param string $field
     * @param array $items
     *
     * @return Expression
     */
    private static function createAndJsonContainsExpression(string $field, array $items) : Expression
    {
        $i = 0;
        $expression = '(';

        foreach ($items as $item) {
            if ($i) {
                $expression .= ' AND ';
            }

            $expression .= 'JSON_CONTAINS(`' . $field . '`, "' . $item . '", "$")';
            $i++;
        }

        $expression .= ')';

        return new Expression($expression);
    }
}