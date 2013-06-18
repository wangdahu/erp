<?php

/**
 * This is the model class for table "pss_billing".
 *
 * The followings are the available columns in table 'pss_billing':
 * @property integer $id
 * @property string $no
 * @property string $operator
 * @property integer $operator_id
 * @property integer $type
 * @property integer $balance_type
 * @property string $cheque
 * @property integer $partner_type
 * @property integer $partner_id
 * @property integer $created
 */
class Billing extends ActiveRecord
{
    
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Billing the static model class
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
            ),
            'searchAttribute' => array(
                'class' => 'pss.models.behaviors.SearchAttribute',
            ),
        );
    }
    

//    public function beforeValidate(){
//        if(parent::beforeValidate()){
//            if($this->find("no='$this->no'")){
//                $this->addError('no', '"'.$this->no.'" 的单据编号已存在');
//                return false;
//            }
//        }
//    }

    
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'pss_billing';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('no, operator, operator_id, type, balance_type, partner_type, partner_id, partner_name', 'required'),
			array('id, operator_id, type, balance_type, partner_type, partner_id, created', 'numerical', 'integerOnly'=>true),
			array('no, operator, cheque', 'length', 'max'=>50),
			array('no', 'unique', 'on'=>'insert'),
			array('partner_name', 'length', 'max'=>100),
			array('created', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, no, operator, operator_id, type, balance_type, cheque, partner_type, partner_id, created,
			       dept_id, date_pattern, start_date, end_date, keyword', 'safe', 'on'=>'search'),
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
	        'items' => array(self::HAS_MANY, 'BillingItem', 'billing_id'),
	        'totalPrice' => array(self::STAT, 'BillingItem', 'billing_id', 'select'=>'SUM(price)'),
		);
	}
	
	public static function getTypeOptions(){
	    return array('收入', '支出');
	}
	
	public static function getBalanceTypeOptions(){
	    return array('现金', '转账');
	}
	
	public static function getPartnerTypeOptions(){
	    return array('客户', '供应商');
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '编号',
			'no' => '单据编号',
			'operator' => '记账人',
			'operator_id' => '记账人',
			'type' => '类型',
			'balance_type' => '结算方式',
			'cheque' => '支票号',
			'partner_type' => '对象类型',
			'partner_id' => '收支对象',
			'partner_name' => '收支对象',
			'created' => '填单时间',
			'items' => '收支明细',
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
        if(!empty($this->keyword)){
            $cr = new CDbCriteria();
            $cr->together = true;
            $cr->compare('partner_name', $this->keyword, true, 'or');
            $cr->compare('no', $this->keyword, true, 'or');
            $criteria->mergeWith($cr);
        }
        if(!empty($this->dept_id)){
            $users = Account::departmentUsers($this->dept_id);
            $ids = array_keys($users);
            $ids[] = 0;
            $criteria->compare('operator_id', $ids);
        }
        $criteria->compare('operator_id',$this->operator_id);
        $criteria->compare('type',$this->type);
        $criteria->mergeWith($this->compareDate('created'));
        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }
	
	/**
	 * 添加收支明细
	 * @param BillingItem $item
	 * @return bool 是否添加成功
	 */
	public function addItem(BillingItem $item, $runValidation=true){
	    $item->billing_id = $this->id;
	    return $item->save($runValidation);
	}
}