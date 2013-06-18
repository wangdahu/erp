<?php

/**
 * This is the model class for table "pss_pay_item".
 *
 * The followings are the available columns in table 'pss_pay_item':
 * @property integer $id
 * @property integer $supplier_id
 * @property integer $order_id
 * @property string $operator
 * @property integer $operator_id
 * @property string $price
 * @property string $remark
 * @property integer $created
 */
class PayItem extends ActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return PayItem the static model class
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
		return 'pss_pay_item';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('supplier_id, order_id, operator, operator_id, price', 'required'),
			array('supplier_id, order_id, operator_id', 'numerical', 'integerOnly'=>true),
			array('operator', 'length', 'max'=>50),
			array('price', 'length', 'max'=>10),
			array('remark', 'length', 'max'=>255),
            array('created', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, supplier_id, order_id, operator, operator_id, price, remark, created', 'safe', 'on'=>'search'),
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
            'buyOrder' => array(self::BELONGS_TO, 'BuyOrder', 'order_id'),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'supplier_id' => 'Supplier',
			'order_id' => 'Order',
			'operator' => 'Operator',
			'operator_id' => 'Operator',
			'price' => 'Price',
			'remark' => 'Remark',
			'created' => 'Created',
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
		$criteria->compare('supplier_id',$this->supplier_id);
		$criteria->compare('order_id',$this->order_id);
		$criteria->compare('operator',$this->operator,true);
		$criteria->compare('operator_id',$this->operator_id);
		$criteria->compare('price',$this->price,true);
		$criteria->compare('remark',$this->remark,true);
		$criteria->compare('created',$this->created);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}