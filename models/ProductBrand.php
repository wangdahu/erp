<?php

/**
 * This is the model class for table "pss_product_brand".
 *
 * The followings are the available columns in table 'pss_product_brand':
 * @property integer $id
 * @property string $name
 * @property string $remark
 * @property string $created
 */
class ProductBrand extends ActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ProductBrand the static model class
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
                    'updateAttribute' => 'updated',
            )
        );
    }
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'pss_product_brand';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name', 'required'),
			array('name', 'length', 'max'=>50),
			array('remark', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, remark, created', 'safe', 'on'=>'search'),
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
            'products' => array(self::HAS_MANY, 'Product', 'brand_id'),
        );
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '编号',
			'name' => '品牌名称',
			'remark' => '描述',
			'created' => '录入时间',
	        'updated' => '修改时间',
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('remark',$this->remark,true);
		$criteria->compare('created',$this->created,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	protected function beforeDelete(){
	    if (parent::beforeDelete()){
	        if (count($this->products) > 0){
	            $this->addError('products', '该品牌下有产品信息，不能删除');
	            return false;
	        }
	    }
	    return true;
	}
}