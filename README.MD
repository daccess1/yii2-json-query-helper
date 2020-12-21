<p align="center">
    <h1 align="center">JSON Query Helper for Yii 2</h1>
    <br>
</p>

This package provides a `JsonQueryHelper` class, that allows you to generate `yii\db\Expression` expressions for searching and filtering by content JSON columns.


Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/):

```
php composer.phar require --prefer-dist daccess1/yii2-json-query-helper
```

Usage
-----

To use this helper, simply pass any methods results to query builder, like this
```php
use daccess1\JsonQueryHelper\JsonQueryHelper;

Model::find()->andWhere(JsonQueryHelper::createOrJsonContainsExpression('json_field_name', ['value_1', 'value_2', 'value_3']));

$query->andWhere(QueryHelper::JSONContains(['or', 'destinations', $this->destinations]));
SELECT * FROM `trip` WHERE (JSON_CONTAINS(destinations, "16", "$") OR JSON_CONTAINS(destinations, "17", "$") OR JSON_CONTAINS(destinations, "18", "$")) LIMIT 21

	SELECT * FROM `trip` WHERE JSON_CONTAINS(destinations, "16", "$") LIMIT 21
```

Requirements
------------

* PHP 7.2 - 7.4