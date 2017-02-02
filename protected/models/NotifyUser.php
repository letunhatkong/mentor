<?php

/**
 * This is the model class for table "notify_user".
 *
 * The followings are the available columns in table 'notify_user':
 * @property integer $notifyUserId
 * @property integer $userId
 * @property integer $notifyId
 * @property integer $isRead
 * @property string $dateRead
 * @property string $idLink
 */
class NotifyUser extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'notify_user';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('userId, notifyId, isRead', 'numerical', 'integerOnly' => true),
            array('dateRead', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('notifyUserId, userId, notifyId, isRead, dateRead', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array();
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'notifyUserId' => 'Notify User',
            'userId' => 'User',
            'notifyId' => 'Notify',
            'isRead' => 'Is Read',
            'dateRead' => 'Date Read',
            'idLink' => 'Link Id'
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('notifyUserId', $this->notifyUserId);
        $criteria->compare('userId', $this->userId);
        $criteria->compare('notifyId', $this->notifyId);
        $criteria->compare('isRead', $this->isRead);
        $criteria->compare('dateRead', $this->dateRead, true);
        $criteria->compare('idLink', $this->idLink);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return NotifyUser the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * Check current user is owner of notification
     * @param $userId
     * @param $notifyId
     * @return bool
     */
    public function checkOwner($userId, $notifyId)
    {
        if ($userId == null || $notifyId == null) return false;
        $model = NotifyUser::model()->findByAttributes(array(
            'userId' => $userId,
            'notifyId' => $notifyId
        ));
        if ($model === null) {
            return false;
        } else {
            return ($model->userId == $userId) ? true : false;
        }
    }

    /**
     * Delete a notification by user Id & notify Id
     * @param $userId
     * @param $notifyId
     * @return bool
     */
    public function delNotifyOfUser($userId, $notifyId)
    {
        try {
            NotifyUser::model()->deleteAllByAttributes(array(
                'userId' => $userId,
                'notifyId' => $notifyId
            ));
        } catch (Exception $e) {
            return false;
        }
        return true;
    }

    /**
     * Count notification by user Id
     * @param $id
     * @return string
     */
    public function countNotifyById($id)
    {
        return NotifyUser::model()->countByAttributes(array(
            'userId' => $id,
            'isRead' => 0
        ));
    }
}
