<?php

/**
 * This is the model class for table "erp_buy_urged_relate".
 *
 * The followings are the available columns in table 'erp_buy_urged_relate':
 * @property integer $id
 * @property integer $urged_id
 * @property integer $from_uid
 * @property string $from_name
 * @property integer $to_uid
 * @property string $to_name
 */
class BuyUrgedRelate extends ActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return BuyUrgedRelate the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'erp_buy_urged_relate';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('urged_id, from_uid, from_name, to_uid, to_name', 'required'),
			array('urged_id, from_uid, to_uid', 'numerical', 'integerOnly'=>true),
			array('from_name, to_name', 'length', 'max'=>50),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, urged_id, from_uid, from_name, to_uid, to_name', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
	        'buyUrged' => array(self::BELONGS_TO, 'BuyUrged', 'urged_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '编号',
			'urged_id' => 'Urged',
			'from_uid' => '催办人',
			'from_name' => '催办人',
			'to_uid' => '被催办人',
			'to_name' => '被催办人',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('urged_id',$this->urged_id);
		$criteria->compare('from_uid',$this->from_uid);
		$criteria->compare('from_name',$this->from_name,true);
		$criteria->compare('to_uid',$this->to_uid);
		$criteria->compare('to_name',$this->to_name,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}
