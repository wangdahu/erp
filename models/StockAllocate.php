<?php

/**
 * This is the model class for table "pss_stock_allocate".
 *
 * The followings are the available columns in table 'pss_stock_allocate':
 * @property integer $id
 * @property string $no
 * @property integer $user_id
 * @property integer $allocate_man_id
 * @property string $allocate_name
 * @property integer $allocate_dept_id
 * @property string $allocate_dept
 * @property integer $storehouse_id
 * @property string $remark
 * @property integer $approval_id
 * @property integer $created
 * @property integer $updated
 */
class StockAllocate extends BillForm
{
//    public function beforeFind(){
//        parent::beforeFind();
//        if (!PssPrivilege::stockCheck(PssPrivilege::STOCK_VIEW) && PssPrivilege::stockCheck(PssPrivilege::STOCK_ADD)){
//            $this->getDbCriteria()->compare('allocate_man_id', Yii::app()->user->id);
//        }
//    }
    
    public function defaultScope(){
        if (!PssPrivilege::stockCheck(PssPrivilege::STOCK_VIEW) && PssPrivilege::stockCheck(PssPrivilege::STOCK_ADD)){
            return array(
                'condition' => 'allocate_man_id=:allocate_man_id',
                'params' => array(':allocate_man_id' => Yii::app()->user->id),
            );
        }
        return array();
    }
    
    public function billNumber(){
        return $this->no;
    }
    
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return StockAllocate the static model class
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
		return 'pss_stock_allocate';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('no, user_id, allocate_man_id, allocate_name, allocate_dept_id, allocate_dept, storehouse_id', 'required'),
			array('approval_id', 'required', 'message' => '请选择审批流程'),
			array('no', 'validBillNumber', 'on'=>'insert'),
			array('user_id, allocate_man_id, allocate_dept_id, storehouse_id, approval_id', 'numerical', 'integerOnly'=>true),
	        array('no', 'unique', 'className' => 'Number', 'attributeName' => 'no', 'on'=>'insert'),
			array('user_id, allocate_man_id, allocate_dept_id, storehouse_id, approval_status', 'numerical', 'integerOnly'=>true),
			array('no, allocate_name, allocate_dept', 'length', 'max'=>50),
			array('remark', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, no, user_id, allocate_man_id, allocate_name, storehouse_id, remark, approval_id, created, 
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
		    'items' => array(self::HAS_MANY, 'StockAllocateItem', 'allocate_id'),
		    'storehouse' => array(self::BELONGS_TO, 'Storehouse', 'storehouse_id'),
            'task' => array(self::BELONGS_TO, 'CoreFlowTask', 'approval_id'),
		);
	}
    
    /**
     * 获取所有仓库
     */
    public function getStorehouseList(){
        return CHtml::listData(Storehouse::model()->findAll(), 'id', 'name');
    }
    
    public function beforeSave(){
        if(parent::beforeSave()){
            $flag = true;
            foreach ($this->items as $item){
                $flag = $item->vaildQuantity() && $flag;
            }
            return $flag;
        }
        return false;
    }
    
    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => '序号',
            'no' => '调拨单号',
            'user_id' => '录入人',
            'allocate_man_id' => '调拨人',
            'allocate_name' => '调拨人',
            'storehouse_id' => '仓库',
            'approval_id' => '审批状态',
            'allocate_dept_id' => '调拨部门ID',
            'allocate_dept' => '调拨部门',
            'remark' => '备注',
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
            $cr->compare('no', $this->keyword, true, "OR")
               ->compare('remark', $this->keyword, true, "OR");
            $criteria->mergeWith($cr);
        }
        $criteria->mergeWith($this->compareDate('created'));
        $criteria->compare('storehouse_id',$this->storehouse_id);
        $criteria->compare('allocate_dept_id', $this->allocate_dept_id);
        $criteria->compare('allocate_man_id', $this->allocate_man_id);
        
        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
            'pagination'=>array('pageSize'=>30),
        ));
    }
}