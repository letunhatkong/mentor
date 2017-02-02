<?php

/**
 * This is the model class for table "invited_session".
 *
 * The followings are the available columns in table 'invited_session':
 * @property integer $idInvitedSession
 * @property integer $idSession
 * @property integer $idUserInvited
 * @property integer $isJoined
 */
class InvitedSession extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'invited_session';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('idSession, idUserInvited', 'required'),
            array('idSession, idUserInvited', 'numerical', 'integerOnly' => true),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('idInvitedSession, idSession, idUserInvited', 'safe', 'on' => 'search'),
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
            'idInvitedSession' => 'Id Invited Session',
            'idSession' => 'Id Session',
            'idUserInvited' => 'Id User Invited',
            'isJoined' => 'Is Joined',
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

        $criteria->compare('idInvitedSession', $this->idInvitedSession);
        $criteria->compare('idSession', $this->idSession);
        $criteria->compare('idUserInvited', $this->idUserInvited);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return InvitedSession the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * Find planned sessions belong to both invited user and create user in Observation
     * @returns array of data planned session
     */
    public function getSessionInPlannedTabObservation()
    {
        $id = Yii::app()->user->idUser;
        $sessionPlanned = Yii::app()->db->createCommand(
            'SELECT u.firstName, u.lastName, u.avatarPath, name, title, description, s.datePost,
                    s.dateCreate, s.idSession, COUNT(DISTINCT(a.idArchive)) AS numArchive,s.active, s.idUserCreate
                FROM sessions AS s
                LEFT JOIN invited_session AS i ON s.idSession = i.idSession
                INNER JOIN users AS u ON s.idUserCreate = u.idUser
                INNER JOIN topics AS t ON s.idTopic = t.idTopic
                LEFT JOIN archive_session AS a ON s.idSession = a.idSession
                WHERE (i.idUserInvited = :idUserInvited OR s.idUserCreate = :idUserCreate) AND ((s.datePost+ INTERVAL 1 DAY) >= Now())
                        AND (t.active = 1) AND s.active = 0
                GROUP BY s.idSession
                ORDER BY s.active desc, s.dateCreate desc')
            ->bindValues(array(':idUserInvited' => $id, ':idUserCreate' => $id))
            ->queryAll();
        return $sessionPlanned;
    }

    /**
     * Find activated sessions belong to both invited user and create user in Observation
     * @returns array of data activated session
     */
    public function getSessionInActiveTabObservation()
    {
        $id = Yii::app()->user->idUser;
        $result = Yii::app()->db->createCommand(
            'SELECT u.firstName, u.lastName, u.avatarPath, name, title, description, s.datePost,
                    s.dateCreate, s.idSession, COUNT(DISTINCT(a.idArchive)) AS numArchive,s.active, s.idUserCreate
                FROM sessions AS s
                LEFT JOIN invited_session AS i ON s.idSession = i.idSession
                INNER JOIN users AS u ON s.idUserCreate = u.idUser
                INNER JOIN topics AS t ON s.idTopic = t.idTopic
                LEFT JOIN archive_session AS a ON s.idSession = a.idSession
                WHERE (i.idUserInvited = :idUserInvited OR s.idUserCreate = :idUserCreate) AND ((s.datePost+ INTERVAL 1 DAY) >= Now())
                        AND (t.active = 1) AND s.active = 1 AND (i.isJoined = 1 OR s.idUserCreate = :idUserCreate)
                GROUP BY s.idSession
                ORDER BY s.active desc, s.datePost desc')
            ->bindValues(array(':idUserInvited' => $id, ':idUserCreate' => $id))
            ->queryAll();
        return $result;
    }

    /**
     * Find past sessions belong to both invited user and create user in Observation
     * @returns array of data past session
     */
    public function getSessionInPastTabObservation()
    {
        $id = Yii::app()->user->idUser;
        $sessionPast = Yii::app()->db->createCommand(
            'SELECT u.firstName, u.lastName, u.avatarPath, name, title, description, s.datePost,
                    s.dateCreate,s.idSession, COUNT(DISTINCT(a.idArchive)) AS numArchive,s.active, s.idUserCreate
                FROM sessions AS s
                LEFT JOIN invited_session AS i ON s.idSession = i.idSession
                INNER JOIN users AS u ON s.idUserCreate = u.idUser
                INNER JOIN topics AS t ON s.idTopic = t.idTopic
                LEFT JOIN archive_session AS a ON s.idSession = a.idSession
                WHERE (i.idUserInvited = :idUserInvited OR s.idUserCreate = :idUserCreate) AND ((s.datePost+ INTERVAL 1 DAY) < Now())
                        AND (t.active = 1)
                GROUP BY s.idSession
                ORDER BY s.dateCreate DESC')
            ->bindValues(array(':idUserInvited' => $id, ':idUserCreate' => $id))
            ->queryAll();
        return $sessionPast;
    }

    /**
     * Get data for each session
     * @param $id
     * @returns array of each session
     */
    public function getDetail($id)
    {
        $detailSession = Yii::app()->db->createCommand(
            'SELECT u.firstName, u.lastName, u.avatarPath, t.name, title, description, datePost, s.dateCreate,
                    s.idUserCreate, COUNT(a.idArchive) AS numArchive, s.idSession
                    FROM sessions AS s
                    INNER JOIN users AS u ON s.idUserCreate = u.idUser
                    INNER JOIN topics AS t ON s.idTopic = t.idTopic 
                    LEFT JOIN archive_session AS a ON s.idSession = a.idSession
                    WHERE (s.idSession = :idSession) AND (t.active = 1)')
            ->bindValues(array(':idSession' => $id))
            ->query();
        return $detailSession;
    }

    /**
     * Get data for edit session
     * @param $id
     * @returns array data of edit session
     */
    public function getDetailEdit($id)
    {
        $detailSessionEdit = Yii::app()->db->createCommand(
            'SELECT u.firstName, u.lastName, u.avatarPath, t.name, title, description, datePost, s.dateCreate,
                     s.idUserCreate, i.idUserInvited, s.idTopic
                    FROM sessions AS s
                    INNER JOIN users AS u ON s.idUserCreate = u.idUser
                    INNER JOIN topics AS t ON s.idTopic = t.idTopic
                    LEFT JOIN invited_session AS i ON s.idSession = i.idSession
                    LEFT JOIN archive_session AS a ON s.idSession = a.idSession
                    WHERE (s.idSession = :idSession) AND (t.active = 1)')
            ->bindValues(array(':idSession' => $id))
            ->query();
        return $detailSessionEdit;

    }

    /**
     * Get data for active session
     * @param $id
     * @returns array of data acive session
     */
    public function getActiveDetail($id)
    {
        $detailActiveSession = Yii::app()->db->createCommand(
            'SELECT u.firstName, u.lastName, u.avatarPath, t.name, title, description, datePost, s.dateCreate,
                    s.idUserCreate, COUNT(a.idArchive) AS numArchive, s.idSession, s.activatedPoint
                    FROM sessions AS s
                    INNER JOIN users AS u ON s.idUserCreate = u.idUser
                    INNER JOIN topics AS t ON s.idTopic = t.idTopic 
                    LEFT JOIN archive_session AS a ON s.idSession = a.idSession
                    WHERE (s.idSession = :idSession) AND (t.active = 1) AND (s.active = 1)')
            ->bindValues(array(':idSession' => $id))
            ->query();
        return $detailActiveSession;
    }

    /**
     * Get id user create and id user invited
     * @param $idSession
     * @returns array of data user create and id user invited
     */
    public function getIdUserCreateAndInvitedUser($idSession)
    {
        $idUserSession = Yii::app()->db->createCommand(
            'SELECT idUserCreate as idUser
                FROM sessions
                WHERE idSession = :idSession
                UNION
                SELECT i.idUserInvited as idUser
                FROM sessions as s
                INNER JOIN invited_session AS i
                on s.idSession = i.idSession and i.isJoined = 1
                WHERE s.idSession = :idSession')
            ->bindValues(array(':idSession' => $idSession))
            ->queryAll();
        return $idUserSession;
    }

    /**
     * Get id user invited
     * @param $idSession
     * @returns array of data user invited
     */
    public function getIdUserInvited($idSession)
    {
        $idUserInvited = Yii::app()->db->createCommand(
            'SELECT  i.idUserInvited
                FROM sessions AS s
                LEFT JOIN invited_session AS i ON s.idSession = i.idSession
                WHERE s.idSession = :idSession')
            ->bindValues(array(':idSession' => $idSession))
            ->queryAll();
        return $idUserInvited;
    }

    public function getInfoOfInvitedUsers($sessionId) {
        $idUserInvited = Yii::app()->db->createCommand(
            'SELECT  i.idUserInvited, u.firstName, u.lastName, u.avatarPath
                FROM invited_session AS i
                INNER JOIN sessions AS s
                ON s.idSession = i.idSession AND i.isJoined = 1
                INNER JOIN users AS u
                ON u.idUser = i.idUserInvited
                WHERE s.idSession = :idSession')
            ->bindValues(array(':idSession' => $sessionId))
            ->queryAll();
        return $idUserInvited;
    }

    /**
     * Get id archive edited
     * @param $idSession
     * @returns array of data archive edited
     */
    public function getIdArchiveEdit($idSession)
    {
        $idArchiveEdit = Yii::app()->db->createCommand(
            'SELECT  a.idArchive
                    FROM archive_session AS a
                    LEFT JOIN sessions AS s ON s.idSession = a.idSession
                    WHERE s.idSession = :idSession')
            ->bindValues(array(':idSession' => $idSession))
            ->queryAll();
        return $idArchiveEdit;
    }

    /**
     * Get data of planned session in planning screen
     * @returns array of data planned session
     */
    public function getSessionInPlannedTabPlanning()
    {
        $id = Yii::app()->user->idUser;
        $sessionPlanedPlanning = Yii::app()->db->createCommand(
            'SELECT u.firstName, u.lastName, u.avatarPath, name, title, description, s.datePost, COUNT(DISTINCT(i.idUserInvited)) AS numInvitedUser,
                            s.dateCreate, s.idSession, COUNT(DISTINCT(a.idArchive)) AS numArchive,s.active, s.idUserCreate
                        FROM sessions AS s
                        LEFT JOIN invited_session AS i ON s.idSession = i.idSession
                        INNER JOIN users AS u ON s.idUserCreate = u.idUser
                        INNER JOIN topics AS t ON s.idTopic = t.idTopic 
                        LEFT JOIN archive_session AS a ON s.idSession = a.idSession
                        WHERE (s.idUserCreate = :idUserCreate) AND ((s.datePost+ INTERVAL 1 DAY) >= Now())
                                AND (t.active = 1)
                        GROUP BY s.idSession
                        ORDER BY s.dateCreate DESC')
            ->bindValues(array(':idUserCreate' => $id))
            ->queryAll();
        return $sessionPlanedPlanning;
    }

    /**
     * Get data of past session in planning screen
     * @returns array of data past session
     */
    public function getSessionInPastTabPlanning()
    {
        $id = Yii::app()->user->idUser;
        $sessionPastPlanning = Yii::app()->db->createCommand(
            'SELECT u.firstName, u.lastName, u.avatarPath, name, title, description, s.datePost, COUNT(DISTINCT(i.idUserInvited)) AS numInvitedUser,
                            s.dateCreate, s.idSession, COUNT(DISTINCT(a.idArchive)) AS numArchive,s.active, s.idUserCreate
                        FROM sessions AS s
                        LEFT JOIN invited_session AS i ON s.idSession = i.idSession
                        INNER JOIN users AS u ON s.idUserCreate = u.idUser
                        INNER JOIN topics AS t ON s.idTopic = t.idTopic 
                        LEFT JOIN archive_session AS a ON s.idSession = a.idSession
                        WHERE (s.idUserCreate = :idUserCreate) AND ((s.datePost+ INTERVAL 1 DAY) < Now())
                                AND (t.active = 1)
                        GROUP BY s.idSession
                        ORDER BY s.dateCreate DESC')
            ->bindValues(array(':idUserCreate' => $id))
            ->queryAll();
        return $sessionPastPlanning;
    }

    /**
     * Check a user is invited in session
     * @param $sessionId
     * @param $userId
     * @return bool
     */
    public function checkUserIsInvited($sessionId, $userId){
        $data = InvitedSession::model()->findAllByAttributes(array(
            "idSession" =>$sessionId,
            "idUserInvited" => $userId
        ));
        return (count($data) > 0);
    }
}       
