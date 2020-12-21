<p align="center">
    <h1 align="center">JSON Query Helper for Yii 2</h1>
    <br>
</p>

This package provides a `JsonQueryHelper` class, that allows you to generate `yii\db\Expression` expressions for searching by content of JSON columns.


Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/):

```
php composer.phar require --prefer-dist daccess1/yii2-json-query-helper
```

Usage
-----

To use this helper, simply it's result to ActiveQuery builder, like this
```php
use daccess1\JsonQueryHelper\JsonQueryHelper;

Model::find()->where(JsonQueryHelper::JSONContains(['json_field', [123, 'value_2']]));
```
The code above will generate an SQL query like this:
```sql
SELECT * FROM `model` WHERE (JSON_CONTAINS(`json_field`, "123", "$") OR JSON_CONTAINS(`json_field`, "value_2", "$"))
```

Values array can contain any number of elements with mixed types. Parameters array can contain either two or three values, it's structure is similar to default Yii2 query builder params. See examples of all possible ways of usage below for more details.

```php
//Check if single value is present in JSON
JsonQueryHelper::JSONContains(['json_field', 'value_1'])

//Check if any of values is present in JSON
QueryHelper::JSONContains(['or', 'json_field', ['value_1', 'value_2' ... 'value_n']])
//Equivalent shorthand
JsonQueryHelper::JSONContains(['json_field', ['value_1', 'value_2' ... 'value_n']])

//Check if all values is present in JSON
QueryHelper::JSONContains(['and', 'json_field', ['value_1', 'value_2' ... 'value_n']])
```

Requirements
------------

* PHP 7.2 - 7.4