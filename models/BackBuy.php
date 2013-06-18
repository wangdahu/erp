<?php

/**
 * This is the model class for table "pss_back_buy".
 *
 * The followings are the available columns in table 'pss_back_buy':
 * @property integer $id
 * @property string $no
 * @property integer $order_id
 * @property integer $user_id
 * @property integer $back_id
 * @property string $back_name
 * @property integer $back_dept_id
 * @property string $back_dept
 * @property integer $buyer_id
 * @property string $buyer
 * @property integer $buyer_dept_id
 * @property string $buyer_dept
 * @property string $corp_name
 * @property string $address
 * @property string $linkman
 * @property string $phone
 * @property integer $supplier_id
 * @property string $supplier_name
 * @property string $supplier_address
 * @property string $supplier_linkman
 * @property string $supplier_phone
 * @property string $remark
 * @property string $total_price
 * @property integer $approval_id
 * @property integer $created
 * @property integer $updated
 */
class BackBuy extends BillForm
{
    
//    public function beforeFind(){
//        parent::beforeFind();
//    
//        $viewRights = PssPrivilege::buyCheck(PssPrivilege::BUY_ORDER_VIEW);
//        if (!$viewRights){
//            $criteria = new CDbCriteria();
//            $criteria->compare('buyer_id', Yii::app()->user->id);
//            $criteria->compare('back_id', Yii::app()->user->id, false, 'OR');
//            $this->getDbCriteria()->mergeWith($criteria);
//        }
//    }
    
    public function defaultScope(){
        if (!PssPrivilege::buyCheck(PssPrivilege::BUY_ORDER_VIEW)){
            return array(
                'condition' => 'buyer_id=:buyer_id OR back_id=:back_id',
                'params' => array(':buyer_id' => Yii::app()->user->id, ':back_id' => Yii::app()->user->id),
            );
        }
        return array();
    }
    
    public function billNumber(){
        return $this->no;
    }
    
    public function bindBuyOrder($orderId){
        $buyOrder = BuyOrder::model()->findByPk($orderId);
        $this->order_id = $orderId;
        $this->_bindAttributes($buyOrder);
    }
    
    private function _bindAttributes(BuyOrder $buyOrder){
        $attributes = array('corp_name', 'address', 'linkman', 'phone',
                'supplier_id', 'supplier_name', 'supplier_address', 'supplier_linkman', 'supplier_phone', 'buyer', 'buyer_id');
        foreach ($attributes as $attr){
            $this->setAttribute($attr, $buyOrder->getAttribute($attr));
        }
    }
    
    protected function beforeSave(){
        if (parent::beforeSave()){
            $flag = true;
            foreach ($this->items as $item){
                $flag = $item->vaildQuantity() && $flag;
                
                if (!$this->getIsBindOrder()) continue;
                
//                foreach ($this->buyOrder->items as $buyItem){
//                    if ($buyItem->product_id == $item->product_id){
//                        if($item->quantity > $buyItem->inQuantity){
//                            //$item->addError('quantity', '退货数量不可超过入库数目');
//                            $flag = false;
//                        }
//                    }
//                }
            }
            return $flag;
        }
        return false;
    }
    
    public function scopes(){
        return array(
            'hasBind' => array('condition' => 'order_id IS NOT NULL'),
        );
    }
    
    public function getIsBindOrder(){
        return !empty($this->order_id);
    }
    
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return BackBuy the static model class
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
		return 'pss_back_buy';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('no, back_name, back_id, back_dept, back_dept_id, buyer_id, buyer, buyer_dept, buyer_dept_id, corp_name, address, linkman, phone, supplier_id, supplier_name, supplier_address, supplier_linkman, supplier_phone, total_price', 'required'),
			array('approval_id', 'required', 'message' => '请选择审批流程'),
			array('no', 'validBillNumber', 'on'=>'insert'),
			array('order_id, back_dept_id, buyer_dept_id, back_id, user_id, buyer_id, supplier_id, approval_id', 'numerical', 'integerOnly'=>true),
	        array('no', 'unique', 'className' => 'Number', 'attributeName' => 'no', 'on'=>'insert'),
	        array('order_id, back_dept_id, buyer_dept_id, back_id, user_id, buyer_id, supplier_id, approval_status', 'numerical', 'integerOnly'=>true),
			array('no, buyer, back_name, buyer_dept, back_dept, corp_name, linkman, phone, supplier_name, supplier_linkman', 'length', 'max'=>50),
			array('address, supplier_address, supplier_phone', 'length', 'max'=>100),
			array('total_price', 'length', 'max'=>10),
			array('no', 'unique', 'className' => 'Number', 'attributeName' => 'no', 'on'=>'insert'),
			array('remark', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, no, order_id, user_id, buyer_id, buyer, corp_name, address, linkman, phone, supplier_id, supplier_name, supplier_address, 
			       supplier_linkman, supplier_phone, delivery_date, balance_date, remark, total_price, approval_id, created, 
			       date_pattern, start_date, end_date, keyword', 'safe', 'on'=>'search'),
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
            'items' => array(self::HAS_MANY, 'BackBuyItem', 'back_buy_id'),
            'buyOrder' => array(self::BELONGS_TO, 'BuyOrder', 'order_id'),
            'task' => array(self::BELONGS_TO, 'CoreFlowTask', 'approval_id'),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '序号',
			'no' => '退货单号',
			'order_id' => '关联的采购单',
		    'back_dept_id' => '退货部门ID',
		    'back_dept' => '退货部门',
		    'buyer_dept_id' => '采购人部门id',
		    'buyer_dept' => '采购人部门',
			'user_id' => '录入人',
			'back_name' => '退货人',
			'back_id' => '退货人',
			'buyer_id' => '采购员',
			'buyer' => '采购人',
			'corp_name' => '我方公司名称',
			'address' => '通讯地址',
			'linkman' => '我方联系人',
			'phone' => '电话',
			'supplier_id' => '供应商',
			'supplier_name' => '供应商名称',
			'supplier_address' => '通讯地址',
			'supplier_linkman' => '供应商联系人',
			'supplier_phone' => '电话',
			'remark' => '备注',
			'total_price' => '退货总额',
			'approval_id' => '审批状态',
			'created' => '填单日期',
			'updated' => '修改时间',
			'items' => '产品信息',
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
        if (!empty($this->keyword)){
            $cr=new CDbCriteria;
            $cr->with = array('items');
            $cr->together = true;
            $cr->compare('no', $this->keyword, true, "OR")
               ->compare('supplier_name', $this->keyword, true, "OR")
               ->compare('items.product_name', $this->keyword, true, "OR");
            
            $criteria->mergeWith($cr);
        }
        $criteria->compare('back_dept_id', $this->back_dept_id);
        $criteria->compare('back_id', $this->back_id);
        $criteria->compare('buyer_dept_id', $this->buyer_dept_id);
        $criteria->compare('buyer_id',$this->buyer_id);
        $criteria->mergeWith($this->compareDate('created'));
        
        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
            'pagination'=>array('pageSize'=>30),
        ));
    }
}