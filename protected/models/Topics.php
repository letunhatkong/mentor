<?php
/**
 * This is the model class for table "topics".
 * @author      UTC.KongLtn
 *
 * The followings are the available columns in table 'topics':
 * @property integer $idTopic
 * @property string $name
 * @property integer $active
 */
class Topics extends CActiveRecord {
    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'topics';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('active', 'numerical', 'integerOnly' => true),
            array('name', 'length', 'max' => 50),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('idTopic, name, active', 'safe', 'on' => 'search'),
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
            'idTopic' => 'Id Topic',
            'name' => 'Name',
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
    public function search() {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('idTopic', $this->idTopic);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('active', $this->active);

        return new CactiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $classname active record class name.
     * @return Topics the static model class
     */
    public static function model($classname = __CLASS__) {
        return parent::model($classname);
    }
}
