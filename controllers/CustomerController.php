<?php

class CustomerController extends ErpController
{
    //客户管理列表查询
    public function actionIndex() {
        $this->breadcrumbs[] = '客户管理';
        $customer = new Customer('search');
        $customer->unsetAttributes();
        if(isset($_GET['Customer'])){
            $customer->attributes = $_GET['Customer'];
        }
        $this->render('index', array('customer' => $customer));
    }
    
    //销售单新增客户弹出层新增
    public function actionPopup(){
        $customer = new Customer('search');
        $customer->unsetAttributes();
        if(isset($_GET['Customer'])){
            $customer->attributes = $_GET['Customer'];
        }
        $this->renderPartial('popup', array('model' => $customer), false, true);
    }
    
    //客户管理  信息保存
    public function saveCustomer($customer){
        $customer->attributes = $_POST['Customer'];
        $customerLinkmanParams = $_POST['CustomerLinkman'];
        
        if(isset($_POST['ajax']) && $_POST['ajax']==='Customer'){
            echo CActiveForm::validate($customer);
            Yii::app()->end();
        }
        
        //手机号码处理
        if(isset($customerLinkmanParams['mobile']) && $customerLinkmanParams['mobile']){
            $mobile_params = $customer->linkman->formatMobile($customerLinkmanParams['mobile']);
            $customerLinkmanParams['mobile'] = $mobile_params['mobile'];
        }
        //电话号码处理
        if(isset($customerLinkmanParams['phone'], $customerLinkmanParams['phone_type'])){
            $phone_params = $customer->linkman->formatPhone($customerLinkmanParams['phone_type'], $customerLinkmanParams['phone']);
            $customerLinkmanParams['phone'] = $phone_params['phone'];
            $customerLinkmanParams['phone_type'] = $phone_params['phone_type'];
        }
        //跟进人姓名
        $customer->followman = Account::user($customer->followman_id)->name;
        
        //保存
        $customer->linkman->attributes = $customerLinkmanParams;
        if ($customer->validate() && $customer->linkman->validate()){
            $customer->save(false);
            $customer->linkman->customer_id = $customer->id;
            $customer->linkman->save(false);
            return true;
        }
        return false;
    }
    
    //客户管理新增
    public function actionCreate(){
        $this->breadcrumbs['客户管理'] = array('/erp/customer');
        $this->breadcrumbs[] = '新添客户';
        $customer = new Customer();
        
        $customer->linkman->gender = 1;
        $customer->user_id = Yii::app()->user->id;
        $customer->followman_id = Yii::app()->user->id;
        $customer->followman = Yii::app()->user->name;
        $customer->type = 0;
        $render_data['customer'] = $customer;
        $request = Yii::app()->request;
        
        if(isset($_POST['Customer'])){
             $bool = $this->saveCustomer($customer);
             
             if($bool){
                 if ($request->isAjaxRequest){//新增销售单   添加客户操作
                     echo $customer->id ? CJSON::encode(array('status' => 1, 'cus_data' => $customer->attributes+array('fullAddress'=>$customer->fullAddress), 'link_data' => $customer->linkman->attributes)) : CJSON::encode(array('status' => 0));
                 }else{//新增客户信息操作
                     Yii::app()->user->setFlash('page_flash', json_encode(array('msg'=>'保存成功')));
                     $_POST['submit_value']== 1 ? $this->redirect(array("/erp/customer")) : $this->refresh();
                 }
             }else{
                if($request->isAjaxRequest){
                    Yii::app()->end(CJSON::encode(array('status' => 0)));
                }else{
                    Yii::app()->user->setFlash('page_flash', json_encode(array('msg'=>'保存失败', 'type'=>'error')));
                    $this->refresh();
                }
            }
        }else{
            if ($request->isAjaxRequest){
                $this->renderPartial('popup-create', $render_data, false, true);
            }else{
                $render_data['title'] = "新添客户";
                $this->render('form', $render_data);
            }
        }
    }
    
    //客户信息修改
    public function actionUpdate($id){
        $this->breadcrumbs['客户管理'] = array('/erp/customer');
        $this->breadcrumbs[] = '客户详情';
        
        $customer = $this->loadModel($id, 'Customer');
        $render_data['customer'] = $customer;
        $request = Yii::app()->request;
        
        if(isset($_POST['Customer'])){
            $this->saveCustomer($customer);
            Yii::app()->user->setFlash('page_flash', json_encode(array('msg'=>'保存成功')));
            $this->refresh();
        }else{
            $render_data['title'] = "客户详情";
            $this->render('form', $render_data);
        }
    }
    
    //删除进销存客户信息
    public function actionDelete(){
        $ids = $_POST['id'];
        $trans = Customer::model()->dbConnection->beginTransaction();
        foreach ($ids as $id){
            Customer::model()->updateByPk($id, array('deleted' => 1));
        }
        $trans->commit();
        echo CJSON::encode(array('status' => 1));
    }
    
    // Uncomment the following methods and override them if needed
    /*
    public function filters()
    {
    	// return the filter configuration for this controller, e.g.:
    	return array(
    		'inlineFilterName',
    		array(
    			'class'=>'path.to.FilterClass',
    			'propertyName'=>'propertyValue',
    		),
    	);
    }
    
    public function actions()
    {
    	// return external action classes, e.g.:
    	return array(
    		'action1'=>'path.to.ActionClass',
    		'action2'=>array(
    			'class'=>'path.to.AnotherActionClass',
    			'propertyName'=>'propertyValue',
    		),
    	);
    }
    */
}
