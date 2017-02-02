<?php
/**
 * This is the model class for table "comments".
 * @author      UTC.KongLtn
 * @author      UTC.HuyTD
 * @author      UTC.BaoDTQ
 *
 * The followings are the available columns in table 'comments':
 * @property integer $idComment
 * @property integer $idCommentParent
 * @property integer $idUserComment
 * @property integer $idSession
 * @property string $content
 * @property string $contentMediaType
 * @property string $contentMediaPath
 * @property string $dateCreate
 * @property string $lastUpdate
 * @property string contentMediaPathTemp
 * @property integer isTemp
 */
class Comments extends CActiveRecord {
	/**
	 * @return string the associated database table name
	 */
	public function tableName()	{
		return 'comments';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('idUserComment, idSession, contentMediaType', 'required'),
			array('idCommentParent, idUserComment, idSession', 'numerical', 'integerOnly'=>true),
			array('contentMediaType', 'length', 'max'=>10),
			array('contentMediaPath', 'length', 'max'=>255),
			array('content, dateCreate', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('idComment, idCommentParent, idUserComment, idSession, content, contentMediaType, contentMediaPath, dateCreate, lastUpdate', 'safe', 'on'=>'search'),
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
			'idComment' => 'Id Comment',
			'idCommentParent' => 'Id Comment Parent',
			'idUserComment' => 'Id User Comment',
			'idSession' => 'Id Session',
			'content' => 'Content',
			'contentMediaType' => 'Content Media Type',
			'contentMediaPath' => 'Content Media Path',
			'dateCreate' => 'Date Create',
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
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('idComment',$this->idComment);
		$criteria->compare('idCommentParent',$this->idCommentParent);
		$criteria->compare('idUserComment',$this->idUserComment);
		$criteria->compare('idSession',$this->idSession);
		$criteria->compare('content',$this->content,true);
		$criteria->compare('contentMediaType',$this->contentMediaType,true);
		$criteria->compare('contentMediaPath',$this->contentMediaPath,true);
		$criteria->compare('dateCreate',$this->dateCreate,true);
        $criteria->compare('lastUpdate',$this->lastUpdate,true);


        return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Comments the static model class
	 */
	public static function model($className=__CLASS__)	{
		return parent::model($className);
	}


    /**
     * Get data comment, like
     * @param integer $idSession
     * @param string $orderType
     * @return array $dataComment
     */
    public function getDataComment($idSession,$orderType="DESC"){
        $dataComment = Yii::app()->db->createCommand(
            'SELECT u.firstName, u.lastName, u.avatarPath, c.content, c.idUserComment,
                c.dateCreate, c.contentMediaType, c.contentMediaPath, c.idComment,c.idCommentParent, temp.countLikes
            FROM users AS u
            INNER JOIN comments AS c ON u.idUser = c.idUserComment
            INNER JOIN (
            SELECT c_temp.idComment, COUNT(l_temp.idLike) AS countLikes
            FROM comments AS c_temp
            LEFT JOIN likes AS l_temp ON c_temp.idComment = l_temp.idComment
            GROUP BY c_temp.idComment

            )AS temp ON temp.idComment = c.idComment

            WHERE c.idSession = :idSession AND (c.isTemp = 0 or c.isTemp is null)
            ORDER BY c.dateCreate '.$orderType)
            ->bindValues(array(':idSession'=>$idSession))
            ->queryAll();
        return $dataComment;
    }
    

    /**
     * Get quantity of comment for each session
     * @param $idSession
     * @return integer $dataNumComment['numComment'] total comment
     */
    public function getNumComment($idSession){
        $dataNumComment = Yii::app()->db->createCommand(
            'SELECT COUNT(c.idComment) AS numComment
            FROM comments AS c
            WHERE c.idSession = :idSession AND (c.isTemp = 0 or c.isTemp is null) AND (c.idCommentParent = 0 or c.idCommentParent is null)')
            ->bindValues(array(':idSession'=>$idSession))
            ->query()->read();
        return $dataNumComment['numComment'];
    }


    /**
     * Get comments of Detail session
     * @param $idComment
     * @return array $dataDetailComment
     */
    public function getDetailComment($idComment){
        $dataDetailComment = Yii::app()->db->createCommand(
            'select u.firstName, u.lastName, u.avatarPath, c.*, temp.countLikes
            from comments as c
            inner join users as u on c.idUserComment = u.idUser
            INNER JOIN (
                SELECT c_temp.idComment, COUNT(l_temp.idLike) AS countLikes
                FROM comments AS c_temp
                LEFT JOIN likes AS l_temp ON c_temp.idComment = l_temp.idComment
                GROUP BY c_temp.idComment

            )AS temp ON temp.idComment = c.idComment
            where c.idComment = :idComment')
            ->bindValues(array(':idComment'=>$idComment))
            ->query()->read();
        return $dataDetailComment;
    }

    /**
     * Get reply comments by parent comment id
     * @param integer $idCommentParent
     * @return array $dataCommentReply
     */
    public function getCommentReplyWithIdCommentParent($idCommentParent){
        $dataCommentReply = Yii::app()->db->createCommand(
            'select u.firstName, u.lastName, u.avatarPath, c.*
            from comments as c
            inner join users as u on c.idUserComment = u.idUser
            where c.idCommentParent = :idCommentParent')
            ->bindValues(array(':idCommentParent'=>$idCommentParent))
            ->queryAll();
        return $dataCommentReply;
    }


    /**
     * Get Text Comments of Session
     * @param integer $sessionId
     * @return array all text comment of session by session id
     */
    public function getTextCommentsOfSession ($sessionId){
        return Yii::app()->db->createCommand(
            'select u.firstName, u.lastName, u.avatarPath, c.*
            from comments as c
            inner join users as u on c.idUserComment = u.idUser
            where c.idSession = :sessionId and c.contentMediaType = "TEXT"
            and (c.idCommentParent = 0 or c.idCommentParent is null)
            order by c.idComment ASC')
            ->bindValues(array(":sessionId" => $sessionId))
            ->queryAll();
    }


    /**
     * Get reply text comment by commentParentId
     * @param integer $commentParentId
     * @return array all text child comment of parent comment
     */
    public function getTextReplyByCommentParentId($commentParentId){
        return Yii::app()->db->createCommand(
            'select u.firstName, u.lastName, u.avatarPath, c.*
            from comments as c
            inner join users as u on c.idUserComment = u.idUser
            where c.idCommentParent = :idCommentParent and c.contentMediaType = "TEXT"')
            ->bindValues(array(':idCommentParent'=>$commentParentId))
            ->queryAll();
    }

    /**
     * Get session id from comment id
     * @param $id
     * @return int
     */
    public function getSessionIdFromCommentId($id){
        $model = Comments::model()->findByPk((int) $id);
        return (isset($model) && !is_null($model) && $model->idSession > 0 ) ? $model->idSession : -1;
    }
}
