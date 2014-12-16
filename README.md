yii2-children-count-behavior
============================
Behavior for counting records in the dependent objects and updating the relevant column after insert, edit and remove any child record
 
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
