<?php

/**
 * This is the model class for table "pss_buy_urged_reply".
 *
 * The followings are the available columns in table 'pss_buy_urged_reply':
 * @property integer $id
 * @property integer $urged_id
 * @property integer $user_id
 * @property string $content
 * @property string $created
 */
class BuyUrgedReply extends ActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return BuyUrgedReply the static model class
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
		return 'pss_buy_urged_reply';
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
			array('urged_id, user_id', 'numerical', 'integerOnly'=>true),
			array('content', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, urged_id, user_id, content, created', 'safe', 'on'=>'search'),
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
			'id' => 'ID',
			'urged_id' => '催办ID',
			'user_id' => '回复人',
			'content' => '回复',
			'created' => '录入时间',
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
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('content',$this->content,true);
		$criteria->compare('created',$this->created,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}