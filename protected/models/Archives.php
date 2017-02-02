<?php

/**
 * This is the model class for table "archives".
 * @author      UTC.KongLtn
 *
 * The followings are the available columns in table 'archives':
 * @property integer $idArchive
 * @property integer $idUserCreate
 * @property string $name
 * @property string $fileName
 * @property string $link
 * @property string $path
 * @property string $dateCreate
 * @property string $typeArchive
 */
class Archives extends CActiveRecord {

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'archives';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return array(
            array('idUserCreate', 'required'),
            array('idUserCreate', 'numerical', 'integerOnly' => true),
            array('name', 'length', 'max' => 50),
            array('fileName', 'length', 'max' => 50),
            array('link', 'length', 'max' => 1024),
            array('path', 'length', 'max' => 255),
            array('dateCreate', 'safe'),
            array('typeArchive', 'length', 'max' => 50),
            array('idArchive, idUserCreate, name, path, link, dateCreate, typeArchive', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()    {
        return array();
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()    {
        return array(
            'idArchive' => 'Archive Id',
            'idUserCreate' => 'Created User Id',
            'name' => 'Name',
            'link' => 'Link',
            'path' => 'Path',
            'dateCreate' => 'Created Date',
            'typeArchive' => 'Archive Type',
            'fileName' => 'File Name'
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
    public function search()    {
        $criteria = new CDbCriteria;

        $criteria->compare('idArchive', $this->idArchive);
        $criteria->compare('idUserCreate', $this->idUserCreate);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('link', $this->link, true);
        $criteria->compare('path', $this->path, true);
        $criteria->compare('dateCreate', $this->dateCreate, true);
        $criteria->compare('typeArchive', $this->typeArchive, true);
        $criteria->compare('fileName', $this->fileName, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $classname
     * @return Comments the static model class
     * @internal param string $className active record class name.
     */
    public static function model($classname = __CLASS__)    {
        return parent::model($classname);
    }


    /**
     * Get all record from archives table by idUserCreate
     * @param $id user create id
     * @return array result of SQL query
     */
    public function getAllByUserId($id)   {
        $conn = Yii::app()->db;
        $conn->active = true; // Start connect
        $command = $conn->createCommand();
        $rows = $command->select('*')
            ->from('archives')
            ->where('idUserCreate=:id', array(':id' => $id))
            ->order('dateCreate desc')
            ->query();
        $result = $rows->readAll();
        $conn->active = false; // Close connect
        return $result;
    }


    /**
     * Check current archive is owned by user id
     * @param $userId id of user
     * @param $arId id of Archive
     * @return bool
     */
    public static function checkOwner($userId, $arId)  {
        if ($userId == null || $arId == null) return false;
        $model = Archives::model()->findByPk((int)$arId);
        if ($model === null) {
            return false;
        } else {
            return ($model->idUserCreate === $userId) ? true : false;
        }
    }


    /**
     * Get all archives from database
     * @return array archive list
     */
    public function getArchives(){
        return Yii::app()->db->createCommand(
            'select *
            from archives
            order by archives.idArchive DESC')
            ->queryAll();
    }


}
