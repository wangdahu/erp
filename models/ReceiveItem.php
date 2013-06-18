<?php

/**
 * This is the model class for table "erp_receive_item".
 *
 * The followings are the available columns in table 'erp_receive_item':
 * @property integer $id
 * @property integer $customer_id
 * @property integer $order_id
 * @property string $operator
 * @property integer $operator_id
 * @property string $price
 * @property string $remark
 */
class ReceiveItem extends ActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ReceiveItem the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
    /**
     * (non-PHPdoc)
     * @see CModel::behaviors()
     */
    public function behaviors(){
        return array(
            'timestampBehavior' => array(
                'class' => 'zii.behaviors.CTimestampBehavior',
                'createAttribute' => 'created',
                'updateAttribute' => null,
            )
        );
    }
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'erp_receive_item';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('customer_id, order_id, operator, operator_id, price', 'required'),
			array('customer_id, order_id, operator_id', 'numerical', 'integerOnly'=>true),
			array('operator', 'length', 'max'=>50),
			array('price', 'length', 'max'=>10),
			array('remark', 'length', 'max'=>255),
			array('created', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, customer_id, order_id, operator, operator_id, price, remark', 'safe', 'on'=>'search'),
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
	        'salesOrder' => array(self::BELONGS_TO, 'SalesOrder', 'order_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'customer_id' => '所属客户',
			'order_id' => '收款销售单',
			'operator' => '记账员',
			'operator_id' => '记账员',
			'price' => '实收金额',
			'remark' => '备注',
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
		$criteria->compare('customer_id',$this->customer_id);
		$criteria->compare('order_id',$this->order_id);
		$criteria->compare('operator',$this->operator,true);
		$criteria->compare('operator_id',$this->operator_id);
		$criteria->compare('price',$this->price,true);
		$criteria->compare('remark',$this->remark,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}
