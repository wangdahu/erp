<?php

class BuyController extends PssController
{
    
    public $defaultAction='plan';
    
    public function init(){
        parent::init();
        $this->breadcrumbs['采购单'] = array('/pss/buy');
    }
    
	public function actionPlan(){
	    $this->breadcrumbs[] = '采购计划';
	    $product = new Product('search');
	    $product->unsetAttributes();
	    
        if(isset($_GET['Product'])){
            $product->attributes = $_GET['Product'];
        }
        $product->stockout()->with(array('salesQuantity', 'buyAssignments'));
        if (!PssPrivilege::buyCheck(PssPrivilege::BUY_ADMIN)){
            $product->assignTo(Yii::app()->user->id);
        }
        $this->render('plan', array('model' => $product));
    }
    
    //采购催办查看及回复
    public function actionBuyUrged($product_id){
        //根据产品$product_id查询所有销售产品(SalesOrderItem)ID
        $orderitems = SalesOrderItem::model()->findAll('product_id='.$product_id);
        $item_ids = array(0);
        foreach ($orderitems as $item){
            $item_ids[] = $item->id;
        }
        
        if(isset($_POST['BuyUrgedReply'])){
            
            $model = new BuyUrgedReply();
            $model->attributes = $_POST['BuyUrgedReply'];
            $model->user_id = Yii::app()->user->id;
            
            if($model->save()){
                $model->refresh();
                In::notice(array(array('app'=>'pss','to_uid'=>$model->buyUrged->user_id,
                        'msg'=>"您好，".Yii::app()->user->name."回复了您的采购催办",'url'=>Yii::app()->createUrl('/pss/sales'))));
                Yii::app()->user->setFlash('page_flash', CJSON::encode(array('msg' => '回复成功')));
                $this->redirect("index.php?r=pss/buy/plan");
            }else{
                Yii::app()->user->setFlash('page_flash', CJSON::encode(array('msg' => '回复失败', 'type' => 'error')));
                $this->redirect("index.php?r=pss/buy/plan");
            }
        }
        $buyUrged = new BuyUrged('search');
        $buyUrged->unsetAttributes();
        $buyUrged->item_id = $item_ids;
        $dataProvider = $buyUrged->search();
        
        echo $this->processOutput($this->widget('zii.widgets.CListView', array(
                'id' => 'urged-view',
                'dataProvider' => $dataProvider,
                'itemView'=>'urgedView',   // refers to the partial view named '_post'
                'template' => "{items}\n{pager}",
                //'cssFile' => false,
        //'ajaxUrl' => $this->createUrl($this->route),
        ), true));
        
       /*  $this->renderPartial("urgedView", array('model' => new BuyUrgedReply(), 'product_id' => $product_id,
            'urgeds' => BuyUrged::model()->findAll("item_id in({$item_ids}) and to_uid=".Yii::app()->user->id)), false, true); */
    }
    
    public function actionIndex(){
        $this->breadcrumbs[] = '进行中采购单';
        $this->processOrderCommand();
        $order = $this->createSearchModel()->incomplete();
        $this->render('index', array('model' => $order));
    }
    
    public function actionPopup(){
        $order = $this->createSearchModel()->hasPass();
        $this->renderPartial('popup', array('model' => $order), false, true);
    }
    
    public function actionHistory(){
        $this->breadcrumbs[] = '历史采购单';
        $this->processOrderCommand();
        $order = $this->createSearchModel()->history();
        $this->render('index', array('model' => $order));
    }



    public function actionDelete() {
        $out = array('status' => 1);
        echo json_encode($out);
    }
    
    public function actionCreate(){
        $this->breadcrumbs['进行中采购单'] = array('/pss/buy/index');
        $this->breadcrumbs[] = '新增采购单';
        $buyOrder=new BuyOrder();
        $buyOrder->buyer = Yii::app()->user->name;
        $buyOrder->buyer_id = Yii::app()->user->id;
        $buyOrder->buyer_dept_id = Yii::app()->user->department_id;
        $buyOrder->buyer_dept = Account::department($buyOrder->buyer_dept_id)->name;
        $buyOrder->corp_name = Account::corp()->name;
        $buyOrder->address = Account::corp()->address;
        $buyOrder->items = $this->getBuyItems();
        if(isset($_POST['BuyOrder'])){
            $buyOrder->attributes=$_POST['BuyOrder'];
            if($buyOrder->save()){
                $approval = $buyOrder->createApproval($buyOrder->approval_id);
                $res = PssFlow::verifyNodeAuthority($approval['node_id'], $approval['task_id']);
                if (isset($res['prime']) && $res['prime'] === true) {
                    //自动审批
                    PssFlow::approved($approval['task_id'], $approval['node_id'], $res['node_relate_id'], '通过', 2);
                }
                
                Yii::app()->user->setFlash('page_flash', json_encode(array('msg'=>'添加成功')));
                $this->redirect(array('view', 'id' => $buyOrder->id));
            }
        }
        $this->render('form',array('model'=> $buyOrder));
    }
    
	public function actionView($id){
	    $order = $this->loadOrder($id);
	    $this->breadcrumbs['进行中采购单'] = array('/pss/buy/index');
	    $this->breadcrumbs[] = "采购单详情";
	    $this->render('view', array('model' => $order));
	}
	
	public function actionUpdate($id){
	    $model = BuyOrder::model()->findByPk($id);
	    $this->breadcrumbs['进行中采购单'] = array('/pss/buy/index');
	    $this->breadcrumbs[] = "采购单详情编辑";
	    if(isset($_POST['BuyOrder'])){
            $model->attributes=$_POST['BuyOrder'];
            foreach ($model->items as $item){//删除产品信息
                $item->delete();
            }
            $model->items = $this->getBuyItems();
            if($model->save()){
                //提醒第一审批人“XXX修改了XX单”
                $node_id = FlowProcess::getCurrentNode($model->approval_id);
                PssFlow::noticeUser($node_id, $model->approval_id, 6);
                
                Yii::app()->user->setFlash('page_flash', json_encode(array('msg'=>'修改成功')));
                $this->redirect(array('view', 'id' => $model->id));
            }
        }
	    $this->render('form',array('model'=> $model));
	}
    
    /**
     * @return BuyOrder
     */
    protected function loadOrder($id=null){
        $model = BuyOrder::model()->findByPk($id ? $id : $_GET['id']);
        if ($model === null){
            // 由页面来源判定是否有上一步
            $url = isset($_SERVER['HTTP_REFERER']) ? '<a href="javascript:;" onclick="history.go(-1)">返回上一步</a>' : '<a href="index.php">返回OA主页</a>';
            throw new CHttpException(500, '很抱歉，您无权查看当前页面内容，请联系管理员进行授权。'.$url);
        }
        return $model;
    }
	
    public function actionSalesOrders(){
        $criteria = new CDbCriteria();
        $criteria->with = array('form' => array('scopes' => array('incomplete', 'hasPass')));
        $item = new SalesOrderItem('search');
        $item->product_id = $_GET['product_id'];
        $item->setDbCriteria($criteria);
        $this->widget('zii.widgets.grid.CGridView', array(
            'id'=>'order-product-grid',
            'dataProvider'=> $item->search(),
            'enablePagination'=>false,
            'emptyText'=>'暂无销售单信息',
            'template'=>'{items}',
            'columns'=>array(
                //array('name' => 'id', 'value' => '$data->id'),
                array('name' => 'form.no', 'value' => '$data->form->no'),
                array('name' => 'form.customer_name', 'value' => '$data->form->customer_name'),
                array('name' => 'quantity', 'value' => '$data->quantity'),
                array('name' => 'price', 'value' => '$data->price'),
                array('name' => 'totalPrice', 'value' => '$data->totalPrice'),
                array('name' => 'form.salesman', 'value' => '$data->form->salesman'),
                array('name' => 'form.created', 'value' => 'date("Y-m-d H:i", $data->form->created)'),
            ),
        ));
    }
    
    /**
     * 历史采购单删除
     */
    public function actionDeletehistory($id){
        BuyOrder::model()->deleteByPk($id);
        Yii::app()->user->setFlash('page_flash', json_encode(array('msg'=>'删除成功！')));
        Yii::app()->end(CJSON::encode(array('status' => 1)));
    }
    
	public function actionItems(){
        $criteria = new CDbCriteria();
        $criteria->with = 'items';
        $buyOrder = $this->loadOrder($_GET['id']);
        $buyOrder->setDbCriteria($criteria);
        $this->widget('zii.widgets.grid.CGridView', array(
            'id'=>"order-{$_GET['id']}-product-item",
            'dataProvider'=> new CArrayDataProvider($buyOrder->items),
            'enablePagination'=>false,
            'emptyText'=>'无产品信息',
            'template'=>'{items}',
            'columns'=>array(
                array('name' => '采购产品', 'value' => '$data->product_name'),
                array('name' => '型号', 'value' => '$data->product_no'),
                array('name' => '品牌', 'value' => '$data->product_brand'),
                array('name' => '单价', 'value' => '$data->price'),
                array('name' => '采购数量', 'value' => '$data->quantity'),
                array('name' => '应付金额', 'value' => '$data->totalPrice'),
                array('header' => '已入库数量', 'value' => '$data->inQuantity'),
                array('header' => '当前库存', 'value' => '$data->product->totalStock'),
            ),
        ));
	}
	
    protected function getBuyItems(){
        $items = array();
        if (isset($_POST['BuyOrderItem'])){
            $itemData = $_POST['BuyOrderItem'];
            foreach ($itemData as $val){
                $item = new BuyOrderItem;
                $item->attributes = $val;
                $items[] = $item;
            }
        }elseif (isset($_GET['id']) && is_array($_GET['id'])){
            $criteria = new CDbCriteria();
            $criteria->compare('t.id', $_GET['id']);
            $products = Product::model()->with(array('cate', 'unit', 'brand'))->findAll($criteria);
            foreach ($products as $product){
                $item = new BuyOrderItem;
                $item->product_id = $product->id;
                $item->product_name = $product->name;
                $item->product_no = $product->no;
                $item->product_cate = $product->cate->name;
                $item->product_brand = $product->brand->name;
                $item->product_unit = $product->unit->name;
                $item->price = $product->buy_price;
                $item->quantity = 0;
                $items[] = $item;
            }
        }
        
        return $items;
    }
    
    
    
    protected function processOrderCommand(){
        if (isset($_POST['command'], $_POST['id']) && $_POST['command'] == "statement"){
            $this->loadOrder($_POST['id'])->intoHistory();
            $this->refresh();
        }
    }
    
    /**
     * @return BuyOrder
     */
    protected function createSearchModel(){
        $order = new BuyOrder('search');
        $order->unsetAttributes();
        if(isset($_GET['BuyOrder'])){
            $order->attributes = $_GET['BuyOrder'];
        }
        return $order;
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
