<?php

/**
 * This is the model class for table "user_device".
 *
 * The followings are the available columns in table 'user_device':
 * @property integer $userDeviceId
 * @property string $deviceType
 * @property string $deviceToken
 * @property integer $userId
 * @property integer $active
 */
class UserDevice extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'user_device';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('userId, active', 'numerical', 'integerOnly' => true),
            array('deviceType', 'length', 'max' => 50),
            array('deviceToken', 'length', 'max' => 512),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('userDeviceId, deviceType, deviceToken, userId, active', 'safe', 'on' => 'search'),
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
            'userDeviceId' => 'User Device',
            'deviceType' => 'Device Type',
            'deviceToken' => 'Device Token',
            'userId' => 'User',
            'active' => 'Active',
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

        $criteria->compare('userDeviceId', $this->userDeviceId);
        $criteria->compare('deviceType', $this->deviceType, true);
        $criteria->compare('deviceToken', $this->deviceToken, true);
        $criteria->compare('userId', $this->userId);
        $criteria->compare('active', $this->active);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return UserDevice the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * Update userId, device token, connection status
     * @param $id
     * @param $token
     * @param $status
     * @param $type
     * @return bool
     */
    public static function updateDeviceToken($id, $token, $status, $type)
    {
        if (isset($id) && isset($token) && isset($status) && isset($type)) {
            try {
                $id = intval($id);
                $obj = UserDevice::model()->findByAttributes(array(
                    'userId' => $id,
                    'deviceToken' => $token
                ));

                // New a userDevice
                if (is_null($obj) && ($status == 1 || $status == 0) && $id > 0 && is_integer($id)) {
                    $newRow = new UserDevice();
                    $newRow->userId = $id;
                    $newRow->deviceToken = $token;
                    $newRow->active = $status;
                    $newRow->deviceType = $type;
                    $newRow->save();
                    return $newRow;
                } // Edit a exists userDevice
                else if (!is_null($obj) && ($status == 1 || $status == 0) && $id > 0 && is_integer($id)) {
                    $obj->deviceType = $type;
                    $obj->active = $status;
                    $obj->save();
                }
                return $obj;

            } catch (Exception $e) {
                echo $e->getMessage();
                exit;
            }
        } else {
            return false;
        }
    }

    /**
     * Get deviceToken array by userId
     * @param $id
     * @return array
     */
//    public static function getDeviceTokenByUserId($id)
//    {
//        $objAr = UserDevice::model()->findAllByAttributes(array(
//            'userId' => $id,
//            'active' => 1
//        ));
//        $count = NotifyUser::model()->countNotifyById($id);
//        $tokenArray = array();
//        if ($objAr && !is_null($objAr)) {
//            foreach ($objAr as $obj) {
//                $objData = $obj->attributes;
//                $objData['countNotify'] = $count;
//                array_push($tokenArray, $objData);
//            }
//        }
//        return $tokenArray;
//    }

    public function getDeviceTokenByUserId($id)
    {
        $objArIos = UserDevice::model()->findAllByAttributes(array(
            'userId' => $id,
            'active' => 1,
            'deviceType' => 'ios'
        ));
        $objArAndroid = UserDevice::model()->findAllByAttributes(array(
            'userId' => $id,
            'active' => 1,
            'deviceType' => 'android'
        ));
        $count = NotifyUser::model()->countNotifyById($id);

        $tokenArray = array();
        $tokenArray['countNotify'] = $count;
        $tokenArray['ios'] = $objArIos;
        $tokenArray['android'] = array();
        foreach ($objArAndroid as $android) {
            array_push($tokenArray['android'], $android->deviceToken);
        }

        return $tokenArray;
    }

}
