<?php

/**
 * This is the model class for table "notify".
 *
 * The followings are the available columns in table 'notify':
 * @property integer $notifyId
 * @property integer $createUserId
 * @property string $dateCreate
 * @property string $typeNotify
 * @property string $content
 * @property string $link
 * @property string seconds
 */
class Notify extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'notify';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('createUserId', 'numerical', 'integerOnly' => true),
            array('typeNotify, link', 'length', 'max' => 512),
            array('content', 'length', 'max' => 1024),
            array('dateCreate', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('notifyId, createUserId, dateCreate, typeNotify, content, link', 'safe', 'on' => 'search'),
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
            'notifyId' => 'Notify',
            'createUserId' => 'Create User',
            'dateCreate' => 'Date Create',
            'typeNotify' => 'Type Notify',
            'content' => 'Content',
            'link' => 'Link',
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

        $criteria->compare('notifyId', $this->notifyId);
        $criteria->compare('createUserId', $this->createUserId);
        $criteria->compare('dateCreate', $this->dateCreate, true);
        $criteria->compare('typeNotify', $this->typeNotify, true);
        $criteria->compare('content', $this->content, true);
        $criteria->compare('link', $this->link, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Notify the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * Get all notification by user Id
     * @param $id
     * @return array
     */
    public function getAllNotifyById($id)
    {
        $notifyData = Yii::app()->db->createCommand(
            'select nu.notifyUserId, nu.isRead, nu.dateRead, nu.idLink, n_u.*, i_s.isJoined
            from notify_user nu
            inner join (
                select n.*, u.gender, u.firstName, u.lastName, u.avatarPath
                from notify n
                inner join users u
                on n.createUserId = u.idUser
            ) as n_u
            on nu.notifyId = n_u.notifyId
            left join invited_session i_s
            on n_u.typeNotify = "SESS" and i_s.idSession = n_u.link and i_s.idUserInvited = :userId
            where nu.userId = :userId
            order by n_u.seconds DESC')
            ->bindValues(array(':userId' => $id))
            ->queryAll();

        // Add redirect link
        foreach ($notifyData as $key => $val) {
            $notifyData[$key]["redirect"] = " ";
            $type = $notifyData[$key]["typeNotify"];
            if ($type === 'MESS') {
                $notifyData[$key]["redirect"] = 'messages#message_index_' . $val["link"];
            } else if ($type === 'SESS') {
                $notifyData[$key]["redirect"] = 'observation/detail/id/' . $notifyData[$key]['link'];
            } else if ($type === 'COMM' || $type === 'LIKE') {
                $id = Comments::model()->getSessionIdFromCommentId($notifyData[$key]["link"]);
                $notifyData[$key]["redirect"] = 'observation/detail/id/' . $id . "#commentRow" . $val["link"];
            }
        }
        return $notifyData;
    }

    /**
     * Get invations data by id
     * @param $id
     * @return mixed
     */
    public function getInvitationsById($id)
    {
        $notifyData = Yii::app()->db->createCommand(
            'select nu.notifyUserId, nu.isRead, nu.dateRead, nu.idLink, n_u.*, i_s.isJoined
            from notify_user nu
            inner join (
                select n.*, u.gender, u.firstName, u.lastName, u.avatarPath
                from notify n
                inner join users u
                on n.createUserId = u.idUser && n.typeNotify = "SESS"
            ) as n_u
            on nu.notifyId = n_u.notifyId
            left join invited_session i_s
            on n_u.typeNotify = "SESS" and i_s.idSession = n_u.link and i_s.idUserInvited = :userId
            where nu.userId = :userId
            order by n_u.seconds DESC')
            ->bindValues(array(':userId' => $id))
            ->queryAll();

        // Add redirect link
        foreach ($notifyData as $key => $val) {
            $notifyData[$key]["redirect"] = "";
            $type = $notifyData[$key]["typeNotify"];
            if ($type === 'SESS') {
                $notifyData[$key]["redirect"] = 'observation/detail/id/' . $notifyData[$key]['link'];
            }
        }
        return $notifyData;
    }
}
