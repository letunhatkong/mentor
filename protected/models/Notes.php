<?php

/**
 * This is the model class for table "notes".
 * @author      UTC.KongLtn
 *
 * The followings are the available columns in table 'notes':
 * @property integer $idNote
 * @property integer $idUserCreate
 * @property string $content
 * @property string $dateCreate
 * @property string $lastUpdate
 */
class Notes extends CActiveRecord {

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'notes';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return array(
            array('idUserCreate', 'required'),
            array('idUserCreate', 'numerical', 'integerOnly' => true),
            array('content, dateCreate, lastUpdate', 'safe'),
            array('idNote, idUserCreate, content, dateCreate, lastUpdate', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        return array();
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'idNote' => 'Note Id',
            'idUserCreate' => 'Created User Id',
            'content' => 'Content',
            'dateCreate' => 'Created Date',
            'lastUpdate' => 'Last Update',
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
    public function search() {
        $criteria = new CDbCriteria;
        $criteria->compare('idNote', $this->idNote);
        $criteria->compare('idUserCreate', $this->idUserCreate);
        $criteria->compare('content', $this->content, true);
        $criteria->compare('dateCreate', $this->dateCreate, true);
        $criteria->compare('lastUpdate', $this->lastUpdate, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Comments the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }


    /**
     * Load model by id
     * @param integer $id
     * @return object $model
     * @throws CHttpException
     */
    public static function loadModel($id) {
        $model = Notes::model()->findByPk((int)$id);
        if ($model === null) {
            throw new CHttpException(404, 'The requested page does not exist.');
        }
        return $model;
    }


    /**
     * Check current note is owned by user id
     * @param integer $userId
     * @param integer $noteId
     * @return bool true or false
     */
    public static function checkOwner($userId, $noteId) {
        if ($userId == null || $noteId == null) return false;
        $model = Notes::model()->findByPk((int)$noteId);
        if ($model === null) {
            return false;
        } else {
            return ($model->idUserCreate === $userId) ? true : false;
        }
    }

}
