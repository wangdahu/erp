<?php

/**
 * This is the model class for table "erp_supplier".
 *
 * The followings are the available columns in table 'erp_supplier':
 * @property integer $id
 * @property string $name
 * @property string $no
 * @property string $business
 * @property integer $country
 * @property integer $province
 * @property integer $city
 * @property string $address
 * @property integer $user_id
 * @property string $created
 * @property string $updated
 * @property integer $deleted
 */
class Supplier extends ActiveRecord
{
    public $operator_id;
    public $date_type;
    
    public function beforeFind(){
        parent::beforeFind();
    
        $viewRights = ErpPrivilege::supplierCheck(ErpPrivilege::SUPPLIER_VIEW);
        if (!$viewRights){
            $criteria = new CDbCriteria();
            $criteria->compare('followman_id', Yii::app()->user->id);
            $this->getDbCriteria()->mergeWith($criteria);
        }
    }
    
    public function defaultScope(){
        $viewRights = ErpPrivilege::supplierCheck(ErpPrivilege::SUPPLIER_VIEW);
        if (!$viewRights){
            return array('condition' => 't.deleted=0 and followman_id=:followman_id',
                         'params' => array(':followman_id' => Yii::app()->user->id));
        }
        return array();
    }
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Supplier the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	public function init(){
	    if($this->isNewRecord){
	        $this->linkman = new SupplierLinkman();
	    }
	}
	/**
	 * 应付客户范围
	 * @return Supplier
	 */
	public function payable(){
	    $ids = array(0);
	    foreach (BuyOrder::model()->incomplete()->findAll(array('select' => 'id, supplier_id')) as $model){
	        $ids[] = $model->supplier_id;
	    }
	    $criteria = new CDbCriteria();
	    $criteria->compare('t.id', $ids);
	    $this->getDbCriteria()->mergeWith($criteria);
	    return $this;
	}
	
	/**
	 * 获取国家集合
	 */
	public function getCountryList(){
	    return array( 1=>'中国', 2=>'国外');
	}
	
	/**
	 * 获取省份集合
	 */
	public function getProvinceList(){
	    return array(''=>'请选择省份') + District::getChildrens();
	}
	
	/**
	 * 获取城市集合
	 */
	public function getCityList(){
        $cityList = array(''=>'请选择城市');
        if($this->province) {
            $cityList += District::getChildrens($this->province);
        }
        return $cityList;
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'erp_supplier';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, followman_id, address', 'required'),
			array('country, province, city, user_id, deleted', 'numerical', 'integerOnly'=>true),
			array('name, no', 'length', 'max'=>50),
			array('business, address', 'length', 'max'=>100),
			array('user_id', 'default', 'value' => Yii::app()->user->id),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, no, business, country, province, city, address, user_id, created, updated, deleted, operator_id,
			       dept_id, date_type, date_pattern, start_date, end_date, keyword', 'safe', 'on'=>'search'),
		);
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
                'class' => 'erp.models.behaviors.SearchAttribute',
            ),
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
            'linkman' => array(self::HAS_ONE, 'SupplierLinkman', 'supplier_id'),
            'buyOrders' => array(self::HAS_MANY, 'BuyOrder', 'supplier_id'),
            'buyOrderCount' => array(self::STAT, 'BuyOrder', 'supplier_id'),
            'buyTotalPrice' => array(self::STAT, 'BuyOrder', 'supplier_id', 'select'=>'SUM(total_price)'),
            'paidPrice' => array(self::STAT, 'PayItem', 'supplier_id', 'select' => 'SUM(price)', 'defaultValue'=>'0.00'),
            'payItems' => array(self::HAS_MANY, 'PayItem', 'supplier_id'),
            'lastPayTime' => array(self::STAT, 'PayItem', 'supplier_id', 'select' => 'MAX(created)'),
        );
    }

    public function getNotPaidPrice(){
        return sprintf("%.2f", $this->buyTotalPrice-$this->paidPrice);
    }
    
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '序号',
			'name' => '供应商名称',
			'no' => '编号',
			'followman_id' => '跟进人',
			'business' => '业务/产品',
			'country' => '国家',
			'province' => '省份',
			'city' => '城市',
			'address' => '详细地址',
			'user_id' => '录入人',
			'created' => '录入时间',
			'updated' => '更新时间',
			'deleted' => '删除',
			'fullAddress' => '通讯地址',
	        'buyOrderCount' => '销售单数',
	        'buyTotalPrice' => '应收金额',
	        'paidPrice' => '已付金额',
		    'operator_id' => '记账人',
		);
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
        
        if(!empty($this->keyword)){
            $cr = new CDbCriteria();
            $cr->compare('name', $this->keyword, true, 'or');
            $criteria->mergeWith($cr);
        }
        $criteria->compare('province',$this->province);
        if(!empty($this->dept_id)){
            $users = Account::departmentUsers($this->dept_id);
            $ids = array_keys($users);
            $ids[] = 0;
            $criteria->compare('user_id', $ids);
        }
        
        if ($this->date_type == 'payItems.created'){
            $criteria->with = array('payItems' => array('order' => 'payItems.id DESC'));
            $criteria->together = true;
            $criteria->mergeWith($this->compareDate('payItems.created'));
        }else{
            $criteria->mergeWith($this->compareDate('t.created'));
        }
        
        $criteria->compare('user_id',$this->user_id);
        $criteria->compare('name', $this->name, true);
        
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
