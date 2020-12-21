<?php

use yii\db\Expression;

class JsonQueryHelper {
    /**
     * Создание [[Expression]] для проверки наличия элемента в JSON-поле
     *
     * @param string $field
     * @param mixed $item
     *
     * @return Expression
     */
    public static function createJsonContainsExpression(string $field, $item) : Expression
    {
        $expression = 'JSON_CONTAINS(' . $field . ', "' . $item . '", "$")';

        return new Expression($expression);
    }

    /**
     * Создание [[Expression]] для проверки наличия любого из элементов в JSON-поле
     *
     * @param string $field
     * @param array $items
     *
     * @return Expression
     */
    public static function createOrJsonContainsExpression(string $field, array $items) : Expression
    {
        $i = 0;
        $expression = '(';

        foreach ($items as $item) {
            if ($i) {
                $expression .= ' OR ';
            }

            $expression .= 'JSON_CONTAINS(' . $field . ', "' . $item . '", "$")';
            $i++;
        }

        $expression .= ')';

        return new Expression($expression);
    }

    /**
     * Создание [[Expression]] для проверки наличия всех элементов в JSON-поле
     *
     * @param string $field
     * @param array $items
     *
     * @return Expression
     */
    public static function createAndJsonContainsExpression(string $field, array $items) : Expression
    {
        $i = 0;
        $expression = '(';

        foreach ($items as $item) {
            if ($i) {
                $expression .= ' AND ';
            }

            $expression .= 'JSON_CONTAINS(' . $field . ', "' . $item . '", "$")';
            $i++;
        }

        $expression .= ')';

        return new Expression($expression);
    }
}