<?php

/**
 * This is the model class for table "pss_customer".
 *
 * The followings are the available columns in table 'pss_customer':
 * @property integer $id
 * @property string $name
 * @property integer $type
 * @property string $no
 * @property string $business
 * @property integer $country
 * @property integer $province
 * @property integer $city
 * @property string $address
 * @property string $phone
 * @property string $fax
 * @property integer $user_id
 * @property integer $followman_id
 * @property string $followman
 * @property string $created
 * @property string $updated
 * @property integer $deleted
 * @property CustomerLinkman $linkman
 */
class Customer extends ActiveRecord
{
    public $operator_id;
    public $date_type;
    
    public function beforeFind(){
        parent::beforeFind();
    
        $viewRights = PssPrivilege::customerCheck(PssPrivilege::CUSTOMER_VIEW);
        if (!$viewRights){
            $criteria = new CDbCriteria();
            $criteria->compare('followman_id', Yii::app()->user->id);
            $this->getDbCriteria()->mergeWith($criteria);
        }
    }
    
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Customer the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function defaultScope(){
	    $viewRights = PssPrivilege::customerCheck(PssPrivilege::CUSTOMER_VIEW);
        if (!$viewRights){
            return array('condition' => 'deleted=0 and followman_id=:followman_id',
                         'params' => array(':followman_id' => Yii::app()->user->id));
        }
        return array();
	}
	
    public function init(){
        if ($this->isNewRecord){
            $this->linkman = new CustomerLinkman();
        }
    }
	
	//获取国家集合
	public function getCountryList(){
	    return array( 1=>'中国', 2=>'国外');
	}
	
	//获取省份集合
	public function getProvinceList(){
	    return District::getChildrens();
	}
	
	//获取城市集合
	public function getCityList(){
	    $cityList = array();
        if($this->province) {
            $cityList = District::getChildrens($this->province);
        }
        return $cityList;
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
            ),
            'searchAttribute' => array(
                'class' => 'pss.models.behaviors.SearchAttribute',
            ),
	    );
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'pss_customer';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, type, followman_id, address', 'required'),
			array('type, country, province, city, followman_id', 'numerical', 'integerOnly'=>true),
			array('name, no, phone, fax, followman', 'length', 'max'=>50),
			array('business, address', 'length', 'max'=>100),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, type, no, business, country, province, city, address, phone, fax, user_id, operator_id,
			       dept_id, date_type, date_pattern, start_date, end_date, keyword', 'safe', 'on'=>'search'),
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
            'linkman' => array(self::HAS_ONE, 'CustomerLinkman', 'customer_id'),
            'salesOrders' => array(self::HAS_MANY, 'SalesOrder', 'customer_id'),
            'salesOrderCount' => array(self::STAT, 'SalesOrder', 'customer_id'),
            'salesTotalPrice' => array(self::STAT, 'SalesOrder', 'customer_id', 'select'=>'SUM(total_price)'),
            'receivedPrice' => array(self::STAT, 'ReceiveItem', 'customer_id', 'select' => 'SUM(price)', 'defaultValue'=>'0.00'),
            'receiveItems' => array(self::HAS_MANY, 'ReceiveItem', 'customer_id'),
            'lastReceiveItem' => array(self::HAS_ONE, 'ReceiveItem', 'customer_id', 'order' => 'lastReceiveItem.id DESC'),
            'lastReceiveTime' => array(self::STAT, 'ReceiveItem', 'customer_id', 'select' => 'MAX(created)', 'defaultValue' => '0'),
        );
    }
    
    public function getNotReceivedPrice(){
        return sprintf("%.2f", $this->salesTotalPrice-$this->receivedPrice);
    }
    
    public function scopes(){
        return array(
            'hasSalesOrder' => array(
                'with' => array('salesOrderCount', 'salesTotalPrice'),
                'condition' => 'exists(select * from pss_sales_order where customer_id=t.id)',
            ),
        );
    }
    
    /**
     * 应收客户范围
     * @return Customer
     */
    public function receivable(){
        $ids = array(0);
        foreach (SalesOrder::model()->incomplete()->findAll(array('select' => 'id, customer_id')) as $model){
            $ids[] = $model->customer_id;
        }
        $criteria = new CDbCriteria();
        $criteria->compare('t.id', $ids);
        $this->getDbCriteria()->mergeWith($criteria);
        return $this;
    }
    
    public function getCreater(){
        return Account::user($this->user_id);
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '序号',
			'name' => '客户名称',
			'type' => '客户属性',
			'followman_id' => '跟进人',
			'no' => '客户编号',
			'business' => '主营业务/产品',
			'country' => '国家',
			'province' => '省份',
			'city' => '城市',
			'address' => '公司地址',
			'phone' => '电话',
			'fax' => '传真',
			'user_id' => '录入人',
			'created' => '录入时间',
			'updated' => '更行时间',
			'deleted' => '删除',
			'fullAddress' => '通讯地址',
			'salesOrderCount' => '销售单数',
			'salesTotalPrice' => '应收金额',
			'receivedPrice' => '已收金额',
		    'operator_id' => '记账人',
		);
	}
	
    public function beforeSave(){
        if ($this->isNewRecord){
            $this->user_id = Yii::app()->user->id;
        }
        return parent::beforeSave();
    }
	
	public function getUser(){
	    return Account::user($this->user_id);
	}
	
	public function getFollowUser(){
	    return Account::user($this->followman_id);
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
        $criteria->compare('name', $this->keyword, true);
        
        if (!empty($this->dept_id)){
            $usres = Account::departmentUsers($this->dept_id);
            $ids = array_keys($usres);
            $ids[] = 0;
            $criteria->compare('user_id', $ids);
        }
        $criteria->compare('user_id',$this->user_id);
        
        
        if ($this->date_type == 'receiveItems.created'){
            $criteria->with = array('receiveItems' => array('order' => 'receiveItems.id DESC'));
            $criteria->together = true;
            $criteria->mergeWith($this->compareDate('receiveItems.created'));
        }else{
            $criteria->mergeWith($this->compareDate('t.created'));
        }
        
        
        $criteria->compare('province',$this->province);
        $criteria->compare('city', $this->city);
        
        $criteria->compare('name', $this->name, true);//弹出层搜索
        
        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
            'pagination'=>array(
                'pageSize'=>30,
            ),
        ));
    }
    
    public function getFullAddress(){
        if ($this->country == 2){//国外
            return '国外：'.$this->address;
        }else{//中国
            $province = $this->provinceList[empty($this->province) ? '0' : $this->province];
            $city = $this->cityList[empty($this->city) ? '0' : $this->city];
            return '中国：'.(empty($province) ? '' : $province.' - ') . (empty($city) ? '' : $city.' - ') . $this->address;
        }
    }
}
