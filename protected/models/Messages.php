<?php

/**
 * This is the model class for table "messages".
 * @author      UTC.KongLtn
 *
 * The followings are the available columns in table 'messages':
 * @property integer $idMessage
 * @property integer $idUserCreate
 * @property string $content
 * @property string $dateCreate
 * @property string $lastUpdate
 * @property string $idMessageReply
 */
class Messages extends CActiveRecord {
	/**
	 * @return string the associated database table name
	 */
	public function tableName()	{
		return 'messages';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('idUserCreate', 'required'),
			array('idUserCreate', 'numerical', 'integerOnly'=>true),
			array('content, dateCreate, lastUpdate', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('idMessage, idUserCreate, content, dateCreate, lastUpdate', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()	{
		return array(
			'idMessage' => 'Id Message',
			'idUserCreate' => 'Id User Create',
			'content' => 'Content',
			'dateCreate' => 'Date Create',
			'lastUpdate' => 'Last Update',
            'idMessageReply' => 'Reply Message Id'
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
	public function search()	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('idMessage',$this->idMessage);
		$criteria->compare('idUserCreate',$this->idUserCreate);
		$criteria->compare('content',$this->content,true);
		$criteria->compare('dateCreate',$this->dateCreate,true);
		$criteria->compare('lastUpdate',$this->lastUpdate,true);
        $criteria->compare('idMessageReply',$this->idMessageReply,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Messages the static model class
	 */
	public static function model($className=__CLASS__)	{
		return parent::model($className);
	}
}
