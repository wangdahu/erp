<?php

/**
 * This is the model class for table "pss_billing_item".
 *
 * The followings are the available columns in table 'pss_billing_item':
 * @property integer $id
 * @property integer $billing_id
 * @property integer $type
 * @property string $price
 * @property string $remark
 */
class BillingItem extends ActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return BillingItem the static model class
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
		return 'pss_billing_item';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('type, price', 'required'),
			array('billing_id, type', 'numerical', 'integerOnly'=>true),
			array('price', 'length', 'max'=>10),
			array('remark', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, billing_id, type, price, remark', 'safe', 'on'=>'search'),
		);
	}
	
    public static function getInTypeOptions(){
        return array_slice(self::getTypeOptions(), 0, 3);
    }
    
    public static function getOutTypeOptions(){
        return array_slice(self::getTypeOptions(), 3, 3, true);
    }
    
    public static function getTypeOptions(){
        return array('业务收入', '其他收入', '采购退款', '业务支出', '其他支出', '销售退款');
    }

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'billing_id' => 'Billing',
			'type' => 'Type',
			'price' => 'Price',
			'remark' => 'Remark',
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
		$criteria->compare('billing_id',$this->billing_id);
		$criteria->compare('type',$this->type);
		$criteria->compare('price',$this->price,true);
		$criteria->compare('remark',$this->remark,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}