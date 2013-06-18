<?php

/**
 * This is the model class for table "pss_back_sales".
 *
 * The followings are the available columns in table 'pss_back_sales':
 * @property integer $id
 * @property integer $order_id
 * @property string $no
 * @property integer $user_id
 * @property integer $back_id
 * @property string $back_name
 * @property integer $back_dept_id
 * @property string $back_dept
 * @property integer $salesman_id
 * @property string $salesman
 * @property integer $salesman_dept_id
 * @property string $salesman_dept
 * @property string $corp_name
 * @property string $address
 * @property string $linkman
 * @property string $phone
 * @property integer $customer_id
 * @property string $customer_name
 * @property string $customer_address
 * @property string $customer_linkman
 * @property string $customer_phone
 * @property string $remark
 * @property string $total_price
 * @property integer $approval_id
 * @property integer $created
 * @property integer $updated
 */
class BackSales extends BillForm
{
    public function billNumber(){
        return $this->no;
    }
    
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return BackSales the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

//    public function beforeFind(){
//        parent::beforeFind();
//    
//        $viewRights = PssPrivilege::salesCheck(PssPrivilege::SALES_ORDER_VIEW);
//        if (!$viewRights){
//            $criteria = new CDbCriteria();
//            $criteria->compare('salesman_id', Yii::app()->user->id);
//            $criteria->compare('back_id', Yii::app()->user->id, false, 'OR');
//            $this->getDbCriteria()->mergeWith($criteria);
//        }
//    }
    
    public function defaultScope(){
        if (!PssPrivilege::salesCheck(PssPrivilege::SALES_ORDER_VIEW)){
            return array(
                'condition' => 'salesman_id=:salesman_id OR back_id=:back_id',
                'params' => array(':salesman_id' => Yii::app()->user->id, ':back_id' => Yii::app()->user->id),
            );
        }
        return array();
    }
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'pss_back_sales';
	}
    
    public function beforeValidate(){
        if(parent::beforeValidate()){
            $flag = true;
            foreach ($this->items as $item){
                $flag = $item->vaildQuantity($this) && $flag;
            }
            return $flag;
        }
        return false;
    }

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('no, user_id, salesman_id, salesman, salesman_dept_id, salesman_dept, back_id, back_name, back_dept_id, back_dept, corp_name, address, linkman, phone, customer_id, customer_name, customer_address, customer_linkman, customer_phone, total_price', 'required'),
			array('approval_id', 'required', 'message' => '请选择审批流程'),
			array('no', 'validBillNumber', 'on'=>'insert'),
			array('order_id, user_id, salesman_id, salesman_dept_id, back_dept_id, customer_id, approval_id', 'numerical', 'integerOnly'=>true),
	        array('no', 'unique', 'className' => 'Number', 'attributeName' => 'no', 'on'=>'insert'),
	        array('order_id, user_id, salesman_id, salesman_dept_id, back_dept_id, customer_id, approval_status', 'numerical', 'integerOnly'=>true),
			array('no, salesman, salesman_dept, back_name, back_dept, corp_name, linkman, phone, customer_name, customer_linkman, back_name', 'length', 'max'=>50),
			array('address, customer_address, customer_phone', 'length', 'max'=>100),
			array('total_price', 'length', 'max'=>10),
			array('remark', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, order_id, no, user_id, salesman_id, salesman, corp_name, address, linkman, phone, customer_id, customer_name, customer_address, 
			       customer_linkman, customer_phone, delivery_date, balance_date, remark, total_price, approval_id, created,date_pattern, start_date, 
			       end_date, keyword', 'safe', 'on'=>'search'),
		);
	}
	
    public function scopes(){
        return array(
            'hasBind' => array('condition' => 'order_id IS NOT NULL'),
        );
    }
	
//    protected function beforeSave(){
//        if (parent::beforeSave()){
//            $flag = true;
//            foreach ($this->items as $item){
//                $flag = $item->vaildQuantity() && $flag;
//                
//                if (!$this->getIsBindOrder()) continue;
//                
//                foreach ($this->salesOrder->items as $salesItem){
//                    if ($salesItem->product_id == $item->product_id){
////                        if($item->quantity > $salesItem->inQuantity){
////                            $item->addError('quantity', '退货数量单号不可超过入库数目');
////                            $flag = false;
////                        }
//                    }
//                }
//            }
//            
//            return $flag;
//        }
//        
//        return false;
//    }
    
    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'items' => array(self::HAS_MANY, 'BackSalesItem', array('back_sales_id')),
            'salesOrder' => array(self::BELONGS_TO, 'SalesOrder', array('order_id')),
            'task' => array(self::BELONGS_TO, 'CoreFlowTask', 'approval_id'),
        );
    }
	
    public function getIsBindOrder(){
        return !empty($this->order_id);
    }

    
    public function bindSalesOrder($orderId){
        $salesOrder = SalesOrder::model()->findByPk($orderId);
        $this->order_id = $orderId;
        $this->_bindAttributes($salesOrder);
    }
    
    private function _bindAttributes(SalesOrder $salesOrder){
        $attributes = array('salesman', 'salesman_id', 'salesman_dept_id', 'salesman_dept', 'corp_name', 'address', 'linkman', 'phone', 
            'customer_id', 'customer_name', 'customer_address', 'customer_linkman', 'customer_phone', 'total_price');
        foreach ($attributes as $attr){
            $this->setAttribute($attr, $salesOrder->getAttribute($attr));
        }
    }
    
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '序号',
			'order_id' => '关联的销售单',
			'salesman_dept_id' => '业务员部门ID',
			'salesman_dept' => '业务员部门',
			'no' => '退货单号',
			'user_id' => '填单人id',
			'salesman_id' => '业务员',
			'salesman' => '业务员',
			'corp_name' => '我方公司名称',
			'address' => '通讯地址',
			'linkman' => '我方联系人',
			'phone' => '电话',
			'customer_id' => '客户编号',
			'customer_name' => '客户名称',
		    'back_id' => '退货人',
		    'back_name' => '退货人',
			'customer_address' => '通讯地址',
			'customer_linkman' => '客户联系人',
			'customer_phone' => '电话',
			'delivery_date' => '交付日期',
			'balance_date' => '结算日期',
			'remark' => '备注',
			'total_price' => '退货总额',
			'approval_id' => '审批状态',
			'created' => '填单时间',
			'updated' => '更新时间',
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
               ->compare('customer_name', $this->keyword, true, "OR")
               ->compare('items.product_name', $this->keyword, true, "OR");
            
            $criteria->mergeWith($cr);
        }
        $criteria->compare('salesman_dept_id', $this->salesman_dept_id);
		$criteria->compare('salesman_id',$this->salesman_id);
		$criteria->compare('back_dept_id', $this->back_dept_id);
		$criteria->compare('back_id',$this->back_id);
		$criteria->mergeWith($this->compareDate('created'));
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		    'pagination'=>array('pageSize'=>30),
		));
	}
}