<?php

class SupplierController extends ErpController
{
    public function beforeAction($action){
        $request = Yii::app()->request;
        if ($request->isAjaxRequest){
            Yii::app()->clientScript->scriptMap['jquery.js'] = false;
        }
        return parent::beforeAction($action);
    }
    
    //供应商首页
    public function actionIndex(){
        $this->breadcrumbs[] = '供应商管理';
        $supplier = $this->createSearchModel();
        $this->render('index', array('supplier' => $supplier));
    }
    
    public function actionPopup(){
        $supplier = $this->createSearchModel();
        $this->renderPartial('popup', array('model' => $supplier), false, true);
    }
    
    protected function createSearchModel(){
        $supplier = new Supplier('search');
        $supplier->unsetAttributes();
        if(isset($_GET['Supplier'])){
            $supplier->attributes = $_GET['Supplier'];
        }
        return $supplier;
    }
    
    //供应商信息保存
    public function saveSupplier($supplier){
        $supplier->attributes = $_POST['Supplier'];
        $linkman_params = $_POST['SupplierLinkman'];
        
        //手机号码处理
        if(isset($linkman_params['mobile'])){
            $mobile_params = $supplier->linkman->formatMobile($linkman_params['mobile']);
            $linkman_params['mobile'] = $mobile_params['mobile'];
        }
        //电话号码处理
        if(isset($linkman_params['phone'], $linkman_params['phone_type'])){
            $phone_params = $supplier->linkman->formatPhone($linkman_params['phone_type'], $linkman_params['phone']);
            $linkman_params['phone'] = $phone_params['phone'];
            $linkman_params['phone_type'] = $phone_params['phone_type'];
        }
        //跟进人姓名
        $supplier->followman = Account::user($supplier->followman_id)->name;
        
        //保存
        $supplier->linkman->attributes = $linkman_params;
        if($supplier->validate() && $supplier->linkman->validate()){
            $supplier->save(false);
            $supplier->linkman->supplier_id = $supplier->id;
            $supplier->linkman->save(false);
            return true;
        }
        return false;
    }
    
    //供应商新增
    public function actionCreate(){
        $this->breadcrumbs['供应商管理'] = array('/erp/supplier');
        $this->breadcrumbs[] = '添加供应商';
        
        $supplier = new Supplier();
        $supplier->user_id = Yii::app()->user->id;
        $supplier->followman_id = Yii::app()->user->id;
        $supplier->followman = Yii::app()->user->name;
        $supplier->linkman->gender = 1;
        $request = Yii::app()->request;
        
        if(isset($_POST['Supplier'])){
            $bool = $this->saveSupplier($supplier);
            if($bool){
                if($request->isAjaxRequest){
                    Yii::app()->end($supplier->id ? CJSON::encode(array('status' => 1, 'sup_data' => $supplier->attributes + array('fullAddress' => $supplier->fullAddress), 'link_data' => $supplier->linkman->attributes)) : CJSON::encode(array('status' => 0)));
                }else{
                    Yii::app()->user->setFlash('page_flash', json_encode(array('msg'=>'保存成功')));
                    $_POST['submit_value']== 1 ? $this->redirect(array("/erp/supplier")) : $this->refresh();
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
            $renderData['supplier'] = $supplier;
            $renderData['title'] = "新添供应商";
            
            if($request->isAjaxRequest){
                $this->renderPartial('popup-form', $renderData, false, true);
            }else{
                $this->render('form', $renderData);
            }
        }
    }
    
    //供应商修改
    public function actionUpdate($id){
        $this->breadcrumbs['供应商管理'] = array('/erp/supplier');
        $this->breadcrumbs[] = '供应商详情';
        
        $supplier = $this->loadModel($id, "Supplier");
        if(isset($_POST['Supplier'])){
            $this->saveSupplier($supplier);
            Yii::app()->user->setFlash('page_flash', json_encode(array('msg'=>'保存成功')));
            $this->refresh();
        }else{
            $renderData['supplier'] = $supplier;
            $renderData['title'] = "供应商详情";
            $this->render('form', $renderData);
        }
    }
    
    public function actionDelete(){
        $ids = $_POST['id'];
        $trans = Supplier::model()->dbConnection->beginTransaction();
        foreach ($ids as $id){
            Supplier::model()->updateByPk($id, array('deleted' => 1));
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
