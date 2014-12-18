yii2-children-count-behavior
============================
Behavior for counting records in the dependent objects and updating the relevant column after insert, edit and remove any child record.

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```sh
php composer.phar require idealia/yii2-children-count-behavior "*"
```

or add

```json
"idealia/yii2-children-count-behavior": "*"
```

to the require section of your `composer.json` file.
	
Usage
-----

```php
public function behaviors()
{
     return [
         [
             'class' => ChildrenCountBehavior::className(),
             'countRelation' => 'getDocuments',
             'parentRelation' => 'getTask',
             'columnToUpdate' => 'document_count'
         ],
     ];
}
  ```
