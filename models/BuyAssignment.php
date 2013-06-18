<?php

/**
 * This is the model class for table "pss_buy_assignment".
 *
 * The followings are the available columns in table 'pss_buy_assignment':
 * @property integer $assign_id
 * @property integer $product_id
 * @property integer $type
 */
class BuyAssignment extends ActiveRecord
{
    const TYPE_USER = 0;
    const TYPE_ROLE = 1;
    
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return BuyAssignment the static model class
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
		return 'pss_buy_assignment';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('assign_id, product_id, type', 'required'),
			array('assign_id, product_id, type', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('assign_id, product_id, type', 'safe', 'on'=>'search'),
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
            'product' => array(self::BELONGS_TO, 'Product', 'product_id', 'select'=>'id, name'),
        );
    }
	
    public static function getBuyerType(){
        return array(self::TYPE_ROLE => '指定角色', self::TYPE_USER => '指定人员');
    }
	
	
    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'assign_id' => '用户id或角色id',
            'product_id' => '产品',
            'type' => '0=用户,1=角色',
            'choose_buyer' => '选择产品采购人',
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

		$criteria->compare('assign_id',$this->assign_id);
		$criteria->compare('product_id',$this->product_id);
		$criteria->compare('type',$this->type);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}
