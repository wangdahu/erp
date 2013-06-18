<?php

/**
 * This is the model class for table "erp_product_detail".
 *
 * The followings are the available columns in table 'erp_product_detail':
 * @property integer $id
 * @property integer $product_id
 * @property string $en_intro
 * @property string $en_remark
 * @property string $size
 * @property string $volume
 * @property string $gross_weight
 * @property string $weight
 * @property string $packaging
 * @property string $material
 */
class ProductDetail extends ActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ProductDetail the static model class
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
		return 'erp_product_detail';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('product_id', 'required'),
			array('size', 'length', 'max'=>100),
			array('volume, gross_weight, weight, packaging, material', 'length', 'max'=>50),
			array('en_intro, en_remark', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, product_id, en_intro, en_remark, size, volume, gross_weight, weight, packaging, material', 'safe', 'on'=>'search'),
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
	        'product' => array(self::BELONGS_TO, 'Product', 'product_id'),
        );
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'product_id' => 'Product',
            'en_intro' => '英文介绍',
            'en_remark' => '英文备注',
            'size' => '产品尺寸',
            'volume' => '体积',
            'gross_weight' => '毛重',
            'weight' => '净重',
            'packaging' => '包装',
            'material' => '材质',
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
		$criteria->compare('product_id',$this->product_id);
		$criteria->compare('en_intro',$this->en_intro,true);
		$criteria->compare('en_remark',$this->en_remark,true);
		$criteria->compare('size',$this->size,true);
		$criteria->compare('volume',$this->volume,true);
		$criteria->compare('gross_weight',$this->gross_weight,true);
		$criteria->compare('weight',$this->weight,true);
		$criteria->compare('packaging',$this->packaging,true);
		$criteria->compare('material',$this->material,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}
