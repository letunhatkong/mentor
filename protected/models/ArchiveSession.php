<?php

/**
 * This is the model class for table "archive_session".
 *
 * The followings are the available columns in table 'archive_session':
 * @property integer $idArchiveSession
 * @property integer $idArchive
 * @property integer $idSession
 */
class ArchiveSession extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'archive_session';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('idArchive, idSession', 'required'),
            array('idArchive, idSession', 'numerical', 'integerOnly' => true),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('idArchiveSession, idArchive, idSession', 'safe', 'on' => 'search'),
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
            'idArchiveSession' => 'Archive Session Id',
            'idArchive' => 'Archive Id',
            'idSession' => 'Session Id',
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

        $criteria->compare('idArchiveSession', $this->idArchiveSession);
        $criteria->compare('idArchive', $this->idArchive);
        $criteria->compare('idSession', $this->idSession);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return ArchiveSession the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * Get Document name (archive name)
     * @param $id
     * @return mixed
     */
    public function getDocumentName($id)
    {
        $documentName = Yii::app()->db->createCommand('
                    SELECT a.name, a.path,a.typeArchive, a.link
                    FROM archives AS a 
                    INNER JOIN archive_session AS s ON a.idArchive = s.idArchive
                    WHERE s.idSession = :idSession')
            ->bindValues(array(':idSession' => $id))
            ->queryAll();
        return $documentName;
    }

    /**
     * Get ArchiveSessions
     * @return mixed
     */
    public function getArchiveSessions()
    {
        return Yii::app()->db->createCommand(
            'select *
            from archive_session
            order by archive_session.idArchive DESC')
            ->queryAll();
    }

    /**
     * Get archives by session id
     * @param $sessionId
     * @return mixed
     */
    public function getArchivesBySessionId($sessionId)
    {
        return Yii::app()->db->createCommand('
           SELECT ar.*
            FROM archive_session AS ars
            inner join archives as ar
            on ar.idArchive = ars.idArchive
            where ars.idSession = :sessionId')
            ->bindValues(array(':sessionId' => $sessionId))
            ->queryAll();
    }

}
