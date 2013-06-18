<?php

class FinanceController extends ErpController
{
    public $defaultAction = 'receive';
    
    public function init(){
        if (!ErpPrivilege::otherCheck(ErpPrivilege::FINANCE_ADMIN)){
            throw new CHttpException(403, '无权限查看 <a href="javascript:" onclick="history.go(-1); ">返回上一步</a>');
        }
        parent::init();
        $this->breadcrumbs['财务管理'] = array('/erp/finance/receive');
    }
    
    public function actionReceive(){
        $this->breadcrumbs[] = '应收';
        $model = new Customer('search');
        $model->unsetAttributes();
        $model->receivable();
        $model->date_type = 'receiveItems.created';
        if (isset($_GET['Customer'])){
            $model->attributes = $_GET['Customer'];
        }
        $this->render('receive', array('model' => $model));
    }
    
    public function actionPay(){
        $this->breadcrumbs[] = '应付';
        $model = new Supplier('search');
        $model->unsetAttributes();
        $model->payable();
        $model->date_type = 'payItems.created';
        if (isset($_GET['Supplier'])){
            $model->attributes = $_GET['Supplier'];
        }
        $this->render('pay', array('model' => $model));
    }
    
    public function actionBilling($type=0){
        $this->breadcrumbs['收支报表'] = array('/erp/finance/billing');
        $this->breadcrumbs[] = $type == '0' ? '收入报表' : '支出报表';
        $model = $this->loadSearchBilling();
        $model->type = $type;
        $this->render('billing', array('model' => $model));
    }
    
    protected function loadSearchBilling(){
        $model = new Billing('search');
        $model->unsetAttributes();
        if (isset($_GET['Billing'])){
            $model->attributes = $_GET['Billing'];
        }
        return $model;
    }
    
    public function actionAddbilling($type=0){
        $model = new Billing();
        $model->type = $type;
        $model->operator = Yii::app()->user->name;
        $model->operator_id = Yii::app()->user->id;
        $model->created = time();
        
        if(isset($_POST['Billing']) && isset($_POST['BillingItem'])){
            $model->attributes = $_POST['Billing'];
            if($model->save()){

                foreach ($_POST['BillingItem'] as $attrs){
                    $item = new BillingItem;
                    $item->attributes = $attrs;
                    $model->addItem($item, false);
                }
                Yii::app()->user->setFlash('page_flash', json_encode(array('msg'=>'保存成功')));
                $this->redirect(Yii::app()->request->urlReferrer);
            }else{
                Yii::app()->user->setFlash('page_flash', json_encode(array('msg'=>'保存失败'.CHtml::errorSummary($model,'','',array()), 'type'=>'warn')));
                $this->redirect(Yii::app()->request->urlReferrer);
            }
        }else{
            $this->renderPartial('addbilling', array('model' => $model, 'items' => array(new BillingItem())), false, true);
        }
    }
    
    public function actionItems($id){
        $billing = Billing::model()->findByPk($id);
        $this->widget('zii.widgets.grid.CGridView', array(
            'id'=>'billing-items',
            'dataProvider'=>new CArrayDataProvider($billing->items, array('pagination' => false)),
            'emptyText' => '暂无收支记录',
            'template'=>'{items}',
            'columns'=>array(
                array('header' => '序号', 'value' => '$row+1', 'headerHtmlOptions'=>array('class'=>'span3'), ),
                array('header' => '收支科目', 'value' => '$data->typeOptions[$data->type]', 'headerHtmlOptions'=>array('class'=>'span5'), ),
                array('header' => $billing->type == '0' ? '实收金额' : '实付金额', 'value' => '$data->price', 'headerHtmlOptions'=>array('class'=>'span5')),
                array('header' => '备注', 'value' => '$data->remark',),
            ),
        ));
    }
    
    
    public function actionChargein($id){
        $customer = $this->loadModel($id, 'Customer');
        $models = $this->createReceiveItems($customer);
        if (isset($_POST['ReceiveItem'])){
            foreach ($_POST['ReceiveItem'] as $i => $data){
                $model = $models[$i];
                $model->attributes = $data;
                if ($model->validate()){
                    $model->save(false);
                }
            }
            $this->redirect(array('/erp/finance/receive'));
        }
        $this->renderPartial('chargein', 
            array('customer' => $customer, 'models' => $models), false, true);
    }
    
    
    public function actionChargeout($id){
        $supplier = $this->loadModel($id, 'Supplier');
        $models = $this->createPayItems($supplier);
        if (isset($_POST['PayItem'])){
            foreach ($_POST['PayItem'] as $i => $data){
                $model = $models[$i];
                $model->attributes = $data;
                if ($model->validate()){
                    $model->save(false);
                }
            }
            $this->redirect(array('/erp/finance/pay'));
        }
//        echo "<pre>";
//        print_r($models->attributes);
//        exit;
        $this->renderPartial('chargeout',
            array('supplier' => $supplier, 'models' => $models), false, true);
    }
    
    
    public function createReceiveItems(Customer $customer){
        $models = array();
        foreach ($customer->salesOrders as $order){
            if ($order->isPassApprove && $order->is_history == 0){
                $model = new ReceiveItem;
                $model->operator = Yii::app()->user->name;
                $model->operator_id = Yii::app()->user->id;
                $model->order_id = $order->id;
                $model->customer_id = $customer->id;
                $models[] = $model;
            }
        }
        return $models;
    }
    
    public function createPayItems(Supplier $supplier){
        $models = array();
        foreach ($supplier->buyOrders as $order){
            if ($order->isPassApprove && $order->is_history == 0){
                $model = new PayItem;
                $model->operator = Yii::app()->user->name;
                $model->operator_id = Yii::app()->user->id;
                $model->order_id = $order->id;
                $model->supplier_id = $supplier->id;
                $models[] = $model;
            }
        }
        return $models;
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
