<?php

/**
 * This is the model class for table "invited_message".
 *
 * The followings are the available columns in table 'invited_message':
 * @property integer $idInvitedMessage
 * @property integer $idMessage
 * @property integer $idUserInvited
 */
class InvitedMessage extends CActiveRecord {
	/**
	 * @return string the associated database table name
	 */
	public function tableName()	{
		return 'invited_message';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('idMessage, idUserInvited', 'required'),
			array('idMessage, idUserInvited', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('idInvitedMessage, idMessage, idUserInvited', 'safe', 'on'=>'search'),
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
	public function attributeLabels() {
		return array(
			'idInvitedMessage' => 'Id Invited Message',
			'idMessage' => 'Id Message',
			'idUserInvited' => 'Id User Invited',
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
	public function search(){
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('idInvitedMessage',$this->idInvitedMessage);
		$criteria->compare('idMessage',$this->idMessage);
		$criteria->compare('idUserInvited',$this->idUserInvited);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return InvitedMessage the static model class
	 */
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
        
        public function getAllMessagesOfUser(){
            $idUser = Yii::app()->user->idUser;
            $messages = Yii::app()->db->createCommand('
                select meg.*,u.firstName,u.lastName,u.avatarPath, temp.countInvited
                from messages meg
                inner join users u on u.idUser = meg.idUserCreate
                inner join (
                        select meg_t.idMessage,count(invmeg_t.idUserInvited) as countInvited 
                        from messages as meg_t
                        left join invited_message invmeg_t on invmeg_t.idMessage = meg_t.idMessage
                        group by meg_t.idMessage
                ) temp on temp.idMessage = meg.idMessage
                left join invited_message invmeg on invmeg.idMessage = meg.idMessage
                where invmeg.idUserInvited = :idUser or meg.idUserCreate = :idUser
                group by meg.idMessage 
                order by meg.dateCreate desc')
                    ->bindValues(array(':idUser' => $idUser, ':idUser' => $idUser))
                    ->queryAll();
            return $messages;
        }
        
        public function getUserReplyMessage($idMessage){            
            $messages = Yii::app()->db->createCommand('
                select meg.idUserCreate as userReply,u.firstName,u.lastName
                from messages meg
                inner join users u on u.idUser = meg.idUserCreate                
                where meg.idMessage = :idMessage')
                    ->bindValues(array(':idMessage' => $idMessage))
                    ->query()->read();            
            return $messages;
        }
        
        public function getUserReplyAllMessage($idMessage){
            $messages = Yii::app()->db->createCommand('
                select meg_t.idUserCreate as userReply,u_t.firstName,u_t.lastName 
                from messages meg_t
                inner join users u_t on u_t.idUser = meg_t.idUserCreate 
                where meg_t.idMessage = :idMessage
                union
                select invmeg_t.idUserInvited as userReply,u_t2.firstName,u_t2.lastName
                from invited_message invmeg_t
                inner join users u_t2 on u_t2.idUser = invmeg_t.idUserInvited 
                inner join messages as meg_t2 on invmeg_t.idMessage = meg_t2.idMessage
                where meg_t2.idMessage = :idMessage')
                    ->bindValues(array(':idMessage' => $idMessage))
                    ->queryAll();
            return $messages;            
        }
}
