<?php

/**
 * This is the model class for table "erp_buy_urged".
 *
 * The followings are the available columns in table 'erp_buy_urged':
 * @property integer $id
 * @property integer $user_id
 * @property integer $item_id
 * @property string $content
 * @property string $created
 */
class BuyUrged extends ActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return BuyUrged the static model class
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
            )
	    );
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'erp_buy_urged';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('content', 'required'),
			array('user_id, item_id', 'numerical', 'integerOnly'=>true),
			array('content', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, user_id, item_id, content, created', 'safe', 'on'=>'search'),
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
            'members' => array(self::HAS_MANY, 'BuyUrgedRelate', 'urged_id'),
            'salesItem' => array(self::BELONGS_TO, 'SalesOrderItem', 'item_id'),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'user_id' => '催办人',
			'item_id' => '销售清单ID',
			'content' => '催办事由',
			'created' => '创建时间',
		);
	}
	
    protected function afterSave(){
        parent::afterSave();
/*         In::notice(array(
            array('app'=>'erp', 'to_uid'=>$this->to_uid, 'msg'=>'您好，'.$this->from_uid.'向您发布了一个采购催办提醒', 'url'=>Yii::app()->createUrl('/erp/buy/plan')),
        )); */
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
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('item_id',$this->item_id);
		$criteria->compare('content',$this->content,true);
		$criteria->compare('created',$this->created,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		    'pagination' => array(
		        'pageSize' => 1,
		    ),
		));
	}
}
