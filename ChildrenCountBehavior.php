<?php
/**
 *  yii2-children-count-behavior
 *
 * @copyright Copyright (c) 2014  Idealia Sp. z o. o.
 * @link http://idealia.pl
 */

namespace idealia\behavior;

use yii\base\Event;
use yii\base\Behavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\AfterSaveEvent;

/**
 * Behavior for counting records in the dependent objects and updating the relevant column after insert, edit and remove
 * any child record
 *
 * ```php
 *
 * public function behaviors()
 * {
 *      return [
 *          [
 *              'class' => ChildrenCountBehavior::className(),
 *              'countRelation' => 'getDocuments',
 *              'parentRelation' => 'getTask',
 *              'columnToUpdate' => 'document_count'
 *          ],
 *      ];
 * }
 *
 * ```
 *
 * @author Piotr Grzelka <piotr.grzelka@idealia.pl>
 * @version 0.9
 */
class ChildrenCountBehavior extends Behavior
{
    /**
     * @var String We will update this column
     */
    public $columnToUpdate;

    /**
     * @var String Relation to a model in which update the quantity
     */
    public $parentRelation;

    /**
     * @var String The ratio of the number of records you retrieve
     */
    public $countRelation;

    /**
     * @return array
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'afterInsert',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterUpdate',
            ActiveRecord::EVENT_AFTER_DELETE => 'afterDelete',
        ];
    }

    /**
     * @param ActiveRecord $model
     */
    private function processModel(ActiveRecord $model)
    {
        $model->detachBehaviors();
        $count = $model->{$this->countRelation}();
        if ($count instanceof ActiveQuery) {
            $count = $count->count();
        } else if (is_array($count)) {
            $count = json_encode($count);
        }
        $model->{$this->columnToUpdate} = $count;
        $model->save(false, [$this->columnToUpdate]);
    }

    /**
     * @param ActiveRecord $owner
     */
    private function process(ActiveRecord $owner)
    {
        /**
         * @var $model ActiveRecord
         */
        $model = $owner->{$this->parentRelation}()->one();

        if ($model) {
            $this->processModel($model);
        }


    }

    /**
     * Counting records after creating the object
     *
     * @param Event $event
     */
    public function afterInsert(Event $event)
    {
        $this->process($this->owner);
    }

    /**
     * Counting records after the update in the new and the previous record
     *
     * @param AfterSaveEvent $event
     */
    public function afterUpdate(AfterSaveEvent $event)
    {
        $this->process($this->owner);

        $relation = $this->owner->{$this->parentRelation}();
        $column = array_shift($relation->link);

        if (isset($event->changedAttributes[$column])) {
            $model = (new $relation->modelClass)->findOne($event->changedAttributes[$column]);
            $this->processModel($model);
        }

    }

    /**
     * Counting results after remove
     *
     * @param Event $event
     */
    public function afterDelete(Event $event)
    {
        $this->process($this->owner);
    }

}
