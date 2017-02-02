<?php

/**
 * This is the model class for table "sessions".
 * @author UTC.BaoDTQ
 * @author UTC.HuyTD
 * @author UTC.KongLtn
 *
 * The followings are the available columns in table 'sessions':
 * @property integer $idSession
 * @property integer $idUserCreate
 * @property integer $idTopic
 * @property string $title
 * @property string $description
 * @property integer $active
 * @property string $datePost
 * @property string $dateCreate
 * @property string $lastUpdate
 * @property string $activatedPoint
 */
class Sessions extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'sessions';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('idUserCreate, idTopic, title', 'required'),
            array('idUserCreate, idTopic, active', 'numerical', 'integerOnly' => true),
            array('title', 'length', 'max' => 255),
            array('description, datePost, dateCreate, lastUpdate', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('idSession, idUserCreate, idTopic, title, description, active, datePost, dateCreate, lastUpdate', 'safe', 'on' => 'search'),
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
            'idSession' => 'Id Session',
            'idUserCreate' => 'Id User Create',
            'idTopic' => 'Id Topic',
            'title' => 'Title',
            'description' => 'Description',
            'active' => 'Active',
            'datePost' => 'Date Post',
            'dateCreate' => 'Date Create',
            'lastUpdate' => 'Last Update',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CactiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CactiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('idSession', $this->idSession);
        $criteria->compare('idUserCreate', $this->idUserCreate);
        $criteria->compare('idTopic', $this->idTopic);
        $criteria->compare('title', $this->title, true);
        $criteria->compare('description', $this->description, true);
        $criteria->compare('active', $this->active);
        $criteria->compare('datePost', $this->datePost, true);
        $criteria->compare('dateCreate', $this->dateCreate, true);
        $criteria->compare('lastUpdate', $this->lastUpdate, true);

        return new CactiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CactiveRecord descendants!
     * @param string $className active record class name.
     * @return Sessions the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * Get user created id of session
     * @param integer $idSession
     * @returns id of user create session
     */
    public function getIdUserCreate($idSession)
    {
        $idUserSession = Yii::app()->db->createCommand(
            'SELECT idUserCreate
                    FROM sessions 
                    WHERE idSession = :idSession')
            ->bindValues(array(':idSession' => $idSession))
            ->query();
        return $idUserSession;
    }


    /**
     * Advanced search in top bar
     * @param integer $currentUserId
     * @param array $text
     * @param integer $userId
     * @param integer $topicId
     * @param string $fromDate
     * @param string $toDate
     * @return array|bool $dataSession result for search page, return false if error
     */
    public function searchAdvanced($currentUserId, $text, $userId, $topicId, $fromDate, $toDate)
    {
        // Error Case
        if (!isset($currentUserId) || is_null($currentUserId)) return false;

        // Search by:
        $search = '';
        // Text
        if (isset($text) && !is_null($text) && count($text) > 0) {
            $i = 1;
            foreach ($text as $part) {
                if ($i == 1) {
                    $search .= ' and ( ';
                } else {
                    $search .= ' or ';
                }
                $search .= ' s.description like "%' . $part . '%"';
                if ($i == count($text)) {
                    $search .= ' ) ';
                }
                $i++;
            }
        }
        // User
        if (isset($userId) && $userId != "-1") {
            $search .= ' and s.idUserCreate = ' . $userId;
        }
        // Topic
        if (isset($topicId) && $topicId != "-1") {
            $search .= ' and s.idTopic = ' . $topicId;
        }
        // from Date to Date
        if ($fromDate) $search .= ' and s.dateCreate >= ' . '"' . $fromDate . '" ';
        if ($toDate) $search .= ' and s.dateCreate <= ' . '"' . $toDate . ' 23:59:59" ';

        $conn = Yii::app()->db;
        $conn->active = true; // Start connect
        $dataPlanned = $conn->createCommand(
            'select s.*,u.firstName,u.lastName,u.avatarPath, temp.countInvited, t.name, count(DISTINCT(a.idArchive)) AS numArchive
             from sessions s
             inner join users u on u.idUser = s.idUserCreate
             inner join topics AS t ON s.idTopic = t.idTopic
             LEFT JOIN archive_session AS a ON s.idSession = a.idSession
             inner join (
                         select s_t.idSession,count(invs_t.idUserInvited) as countInvited
                 from sessions as s_t
                 left join invited_session invs_t on invs_t.idSession = s_t.idSession
                 group by s_t.idSession
             ) temp on temp.idSession = s.idSession
             left join invited_session invs on invs.idSession = s.idSession
             where (1=1)
             ' . $search . '
             and (invs.idUserInvited = :idUser or s.idUserCreate = :idUser)
             and ((s.datePost+ INTERVAL 1 DAY) >= Now()) and (t.active = 1)
             group by s.idSession
             order by s.active desc, s.datePost ASC')
            ->bindValues(array(':idUser' => $currentUserId))
            ->queryAll();

        $dataPast = $conn->createCommand(
            'select s.*,u.firstName,u.lastName,u.avatarPath, temp.countInvited, t.name, count(DISTINCT(a.idArchive)) AS numArchive
             from sessions s
             inner join users u on u.idUser = s.idUserCreate
             inner join topics AS t ON s.idTopic = t.idTopic
             LEFT JOIN archive_session AS a ON s.idSession = a.idSession
             inner join (
                         select s_t.idSession,count(invs_t.idUserInvited) as countInvited
                 from sessions as s_t
                 left join invited_session invs_t on invs_t.idSession = s_t.idSession
                 group by s_t.idSession
             ) temp on temp.idSession = s.idSession
             left join invited_session invs on invs.idSession = s.idSession
             where (1=1)
             ' . $search . '
             and (invs.idUserInvited = :idUser or s.idUserCreate = :idUser)
             and ((s.datePost+ INTERVAL 1 DAY) < Now()) and (t.active = 1)
             group by s.idSession
             order by s.dateCreate desc')
            ->bindValues(array(':idUser' => $currentUserId))
            ->queryAll();

        $conn->active = false;
        $dataSession = array();
        $dataSession["dataPlanned"] = ($dataPlanned) ? $dataPlanned : null;
        $dataSession["dataPast"] = ($dataPast) ? $dataPast : null;

        return $dataSession;
    }

    /**
     * Get active session detail
     * @returns array of active session detail
     */
    public function getActiveSession()
    {
        $conn = Yii::app()->db;
        return $conn->createCommand(
            'select s.*, u.username, count(i.idSession) as countUser
            from sessions s
            left join users u on s.idUserCreate = u.idUser
            left join invited_session i on s.idSession = i.idSession
            where (s.datePost+ INTERVAL 1 DAY) >= Now() and s.active = 1
            group by s.idSession')
            ->queryAll();
    }

    /**
     * Get planned session detail
     * @returns array of planned session detail
     */
    public function getPlannedSession()
    {
        $conn = Yii::app()->db;
        return $conn->createCommand(
            'select s.*, u.username, count(i.idSession) as countUser
            from sessions s
            left join users u on s.idUserCreate = u.idUser
            left join invited_session i on s.idSession = i.idSession
            where (s.datePost+ INTERVAL 1 DAY) >= Now() and s.active != 1
            group by s.idSession')
            ->queryAll();
    }

    /**
     * Get past session detail
     * @returns array of past session detail
     */
    public function getPastSession()
    {
        $conn = Yii::app()->db;
        return $conn->createCommand(
            'select s.*, u.username, count(i.idSession) as countUser
            from sessions s
            left join users u on s.idUserCreate = u.idUser
            left join invited_session i on s.idSession = i.idSession
            where (s.datePost+ INTERVAL 1 DAY) < Now()
            group by s.idSession')
            ->queryAll();
    }

    /**
     * Get Sessions
     * @returns array of session detail
     */
    public function getSessions()
    {
        $conn = Yii::app()->db;
        return $conn->createCommand(
            'select s.*, u.username, count(i.idInvitedSession) as countUser
            from sessions s
            left join users u on s.idUserCreate = u.idUser
            left join invited_session i on s.idSession = i.idSession
            group by s.idSession
            order by s.idSession DESC')
            ->queryAll();
    }

    /**
     * Remove a member from a session
     * @param $userId
     * @param $sessionId
     * @return bool
     */
    public function removeMemberFromSession($sessionId, $userId)
    {
        if (isset($userId) && isset($sessionId) && !is_null($userId) && !is_null($sessionId)) {
            try {
                InvitedSession::model()->deleteAllByAttributes(
                    array(), 'idSession = :sesId and idUserInvited = :uId', array(
                    "sesId" => $sessionId,
                    "uId" => $userId
                ));
                return true;
            } catch (Exception $e) {
                echo $e->getMessage();
                return false;
            }
        }
        return false;
    }

    /**
     * Check access permission of a user with a session
     * @param $userId
     * @param $sessionId
     * @return bool
     */
    public function checkPermission($userId, $sessionId)
    {
        if (!isset($userId) || !isset($sessionId) || is_null($userId) || is_null($sessionId)) {
            return false;
        }
        // Check is owner
        $owner = Sessions::model()->countByAttributes(array(
            'idSession' => $sessionId,
            'idUserCreate' => $userId
        ));
        if ($owner > 0) return true;
        // Check is invited user
        $count = InvitedSession::model()->countByAttributes(array(
            'idSession' => $sessionId,
            'idUserInvited' => $userId,
            'isJoined' => 1
        ));
        return ($count > 0);
    }

    /**
     * Check current user is owner of session
     * @param $sessionId
     * @return bool
     */
    public function checkOwner($sessionId)
    {
        $userId = Yii::app()->user->idUser;
        // Check is owner
        $owner = Sessions::model()->countByAttributes(array(
            'idSession' => $sessionId,
            'idUserCreate' => $userId
        ));
        return ($owner > 0) ? true : false;
    }
}
