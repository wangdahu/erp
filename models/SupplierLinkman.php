<?php

/**
 * This is the model class for table "pss_supplier_linkman".
 *
 * The followings are the available columns in table 'pss_supplier_linkman':
 * @property integer $id
 * @property integer $supplier_id
 * @property string $name
 * @property integer $gender
 * @property string $department
 * @property string $post
 * @property string $in_no
 * @property string $email
 * @property string $fax
 * @property string $im_no
 * @property string $mobile
 * @property string $phone
 * @property string $remark
 */
class SupplierLinkman extends ActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return SupplierLinkman the static model class
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
		return 'pss_supplier_linkman';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, gender', 'required'),
			array('supplier_id, gender', 'numerical', 'integerOnly'=>true),
			array('name, department, post, in_no, email, fax', 'length', 'max'=>50),
			array('im_no', 'length', 'max'=>100),
			array('mobile, phone, remark, phone_type', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, supplier_id, name, gender, department, post, in_no, email, fax, im_no, mobile, phone, remark', 'safe', 'on'=>'search'),
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
            'supplier' => array(self::BELONGS_TO, 'Supplier', 'supplier_id')
        );
    }
    
    public function getMobiles(){
        return explode(',', $this->mobile);
    }
    
    public function getShowMobiles(){
        if(empty($this->mobile)){
            return $this->mobile;
        }else{
            $mobiles = "";
            foreach ($this->mobiles as $mobile){
                $mobiles[] = $mobile;
            }
            return join("\n", $mobiles);
        }
    }
    
    public function getShowPhones(){
        if(empty($this->phone)){
            return $this->phone;
        }else{
            $phones = "";
            foreach ($this->phones as $k=>$phone){
                $phones[] = !empty($this->phoneTypeList[$this->phoneTypes[$k]]) ? $this->phoneTypeList[$this->phoneTypes[$k]]."：".$phone : "";
            }
            return join("\n", $phones);
        }
    }
    
    public function getPhones(){
        return explode(',', $this->phone);
    }
    
    public function getPhoneTypes(){
        return explode(',', $this->phone_type);
    }
    
    public function getPhoneTypeList(){
        return array('工作电话', '家庭电话', '宿舍电话');
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '主键',
			'supplier_id' => '供应商ID',
			'name' => '姓名',
			'gender' => '性别',
			'department' => '所属部门',
			'post' => '职务',
			'in_no' => 'IDKIN号',
			'email' => '邮箱',
			'fax' => '传真',
			'im_no' => 'QQ/MSN',
			'mobile' => '手机',
			'phone' => '电话',
			'remark' => '备注',
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
		$criteria->compare('supplier_id',$this->supplier_id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('gender',$this->gender);
		$criteria->compare('department',$this->department,true);
		$criteria->compare('post',$this->post,true);
		$criteria->compare('in_no',$this->in_no,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('fax',$this->fax,true);
		$criteria->compare('im_no',$this->im_no,true);
		$criteria->compare('mobile',$this->mobile,true);
		$criteria->compare('phone',$this->phone,true);
		$criteria->compare('remark',$this->remark,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    /**
     * 电话号码处理，用于渲染视图
     * @param array $phone_type 固话类型
     * @param array $phone 电话号码
     * @return array  元素数组，格式：87888888,0755-88575666,...., 88575666
     */
    public static function formatPhone($phone_type, $phone){
        $phone = array_filter($phone);
        $tmp = array();
        foreach ($phone as $k => $v){
            $tmp[] = $phone_type[$k];
        }
        return array('phone' => implode(',', $phone), 'phone_type' => implode(',', $tmp));
    }
    
    /**
     * 手机号码处理，用于渲染视图
     * @param array $mobile 手机号码
     * @return array 元素数组，格式：xxx,xxx,xxx,xxx...,xxx
     */
    public static function formatMobile($mobile){
        $mobile = array_filter($mobile);
        return array('mobile' => implode(',', $mobile));
    }
}
