<?php

/**
 * This is the model class for table "users".
 * @author      UTC.KongLtn
 * @author      UTC.HuyTD
 * @author      UTC.BaoDTQ
 *
 * The followings are the available columns in table 'users':
 * @property integer $idUser
 * @property string $firstName
 * @property string $lastName
 * @property string $username
 * @property string $password
 * @property string $phone
 * @property string $email
 * @property string $avatarPath
 * @property string $dateCreate
 * @property string $gender
 * @property string $role
 * @property string $lastSeen
 */
class Users extends CActiveRecord {
    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'users';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('firstName, lastName, username, password, phone, email', 'length', 'max' => 50),
            array('avatarPath', 'length', 'max' => 255),
            array('dateCreate, lastSeen', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('idUser, firstName, lastName, username, password, phone, email, avatarPath, dateCreate, role, gender, lastSeen', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array();
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'idUser' => 'Id User',
            'firstName' => 'First Name',
            'lastName' => 'Last Name',
            'username' => 'Username',
            'password' => 'Password',
            'phone' => 'Phone',
            'email' => 'Email',
            'avatarPath' => 'Avartar Path',
            'dateCreate' => 'Date Create',
            'role' => 'Role', // default is NULL
            'gender' => 'Gender', // 0 = Female, 1 = Male
            'lastSeen' => 'Last seen'
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
    public function search()  {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('idUser', $this->idUser);
        $criteria->compare('firstName', $this->firstName, true);
        $criteria->compare('lastName', $this->lastName, true);
        $criteria->compare('username', $this->username, true);
        $criteria->compare('password', $this->password, true);
        $criteria->compare('phone', $this->phone, true);
        $criteria->compare('email', $this->email, true);
        $criteria->compare('avatarPath', $this->avatarPath, true);
        $criteria->compare('dateCreate', $this->dateCreate, true);
        $criteria->compare('role', $this->role, true);
        $criteria->compare('gender', $this->gender, true);
        $criteria->compare('lastSeen', $this->lastSeen, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Users the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * Check validate password
     * @param string $password
     * @return bool true or false
     */
    public function validatePassword($password) {
        return ($password == $this->password);
    }


    /**
     * Check user is admin
     * @param integer $userId
     * @return bool true or false
     */
    public static function isAdmin($userId) {
        if ($userId == null) return false;
        $model = Users::model()->findByPk((int)$userId);
        if (isset($model) && $model !== null && $model->role === 'ADMIN') {
            return true;
        } else {
            return false;
        }
    }

    public function getFullName($userId){
        if ($userId == null || $userId < 1) return false;
        $model = Users::model()->findByPk((int)$userId);
        if (isset($model) && $model !== null) {
            return $model->firstName." ".$model->lastName;
        } else {
            return false;
        }
    }


    /**
     * Get invited Users
     * @return array $dataUsersInvited
     */
    public function getUserInvited() {
        $dataUsersInvited = Yii::app()->db->createCommand(
            'SELECT idUser, firstName,lastName
              FROM users
              WHERE idUser != :idUser')
            ->bindValues(array(':idUser' => Yii::app()->user->idUser))
            ->queryAll();
        return $dataUsersInvited;
    }


    /**
     * Get all user
     * @return array
     */
    public function getUsers() {
        return Yii::app()->db->createCommand(
            'SELECT *
              FROM users')
            ->queryAll();
    }

}
