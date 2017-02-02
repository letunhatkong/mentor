<?php

/**
 * This is the model class for table "Likes".
 * @author      UTC.KongLtn
 *
 * The followings are the available columns in table 'Likes':
 * @property integer $idLike
 * @property integer $idUserLike
 * @property string $idComment
 */
class Likes extends CActiveRecord {

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'likes';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return array(
            array('idUserLike', 'numerical', 'integerOnly' => true),
            array('idComment', 'numerical', 'integerOnly' => true),
            array('idLike, idUserLike, idComment', 'safe', 'on' => 'search'),
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
            'idLike' => 'Like Id',
            'idUserLike' => 'User Id Like',
            'idComment' => 'Comment Id'

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
        $criteria->compare('idLike', $this->idLike);
        $criteria->compare('idUserLike', $this->idUserLike);
        $criteria->compare('idComment', $this->idComment);

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
     * Check like status of user when user click like button
     * @param integer $userId
     * @param integer $cmtId
     * @return bool true or false
     */
    public static function checkLiked($userId, $cmtId) {
        if ($userId == null || $cmtId == null) return false;
        $model = Likes::model()->findByAttributes(array("idUserLike" => $userId, "idComment" => $cmtId));
        return ($model === null) ? false : true;
    }


    /**
     * Get Liked User List by comment Id
     * @param integer $cmtId
     * @return string $text
     */
    public function getLikedUserList($cmtId) {
        if (is_null($cmtId) || $cmtId < 1) return "";
        $text = "";
        try {
            $list = Yii::app()->db->createCommand(
                'select l.*, u.firstName, u.lastName
                from likes as l
                left join users as u
                on u.idUser = l.idUserLike
                where l.idComment = :cmtId
                order by l.idLike DESC')
                ->bindValues(array(':cmtId' => $cmtId))
                ->queryAll();
            $i = 1;
            foreach($list as $txt) {
                $text .= $txt["firstName"]." ".$txt["lastName"];
                $text .= ($i < count($list)) ? ",":"";
                $i++;
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        return $text;
    }

}
