<?php

class StockoutController extends PssController{
    
    public function init(){
        parent::init();
        $this->breadcrumbs['货品库存'] = array('/pss/stock/index');
        $this->breadcrumbs['货品出库'] = array('/pss/stock/index');
    }
    
    //货品出库
    public function actionIndex(){
        $this->breadcrumbs[] = '出库单';
        $model = new StockOut('search');
        $model->unsetAttributes();
        if (isset($_GET['StockOut'])){
            $model->attributes = $_GET['StockOut'];
        }
        $this->render('index', array('model' => $model));
    }
    
    //产品库存  新增出库单
    public function actionCreate($order_id=''){
        $this->breadcrumbs['出库单'] = array('/pss/stockout/index');
        $this->breadcrumbs[] = '新增出库单';
        $model=new StockOut();
        $model->out_name = Yii::app()->user->name;
        $model->out_id = Yii::app()->user->id;
        $model->out_dept_id = Yii::app()->user->department_id;
        $model->out_dept = Account::department($model->out_dept_id)->name;
        $model->corp_name = Account::corp()->name;
        $model->address = Account::corp()->address;
        
        if (isset($_GET['StockOut'])){
            $model->attributes=$_GET['StockOut'];
        }
        $salesOrder = null;
        if (!empty($order_id)){
            $model->bindSalesOrder($order_id);
            $salesOrder = SalesOrder::model()->findByPk($order_id);
        }
        $items = $this->getOutItems($order_id);
        
        if(isset($_POST['StockOut'])){
            $model->attributes=$_POST['StockOut'];
            $model->items = $items;
            
            if($model->save()){
                $approval = $model->createApproval($model->approval_id);
                $res = PssFlow::verifyNodeAuthority($approval['node_id'], $approval['task_id']);
                if (isset($res['prime']) && $res['prime'] === true) {
                    //自动审批
                    PssFlow::approved($approval['task_id'], $approval['node_id'], $res['node_relate_id'], '通过', 2);
                }
                
                Yii::app()->user->setFlash('page_flash', json_encode(array('msg'=>'添加成功')));
                $this->redirect(array('view', 'id' => $model->id));
            }
        }
        $this->render('form',array('model'=>$model, 'items'=>$items, 'relatedOrder' => $salesOrder));
    }
    
    public function actionUpdate($id){
        $this->breadcrumbs['出库单'] = array('/pss/stockout/index');
        $this->breadcrumbs[] = "出库单详情编辑";
        $model = StockOut::model()->findByPk($id);
        
        if(isset($_POST['StockOut'])){
            $model->attributes=$_POST['StockOut'];
            foreach ($model->items as $item){
                $item->delete();
            }
            $items = $this->getOutItems("");
            $model->items = $items;
            
            if($model->save()){
                //提醒第一审批人“XXX修改了XX单”
                $node_id = FlowProcess::getCurrentNode($model->approval_id);
                PssFlow::noticeUser($node_id, $model->approval_id, 6);
                
                Yii::app()->user->setFlash('page_flash', json_encode(array('msg'=>'添加成功')));
                $this->redirect(array('view', 'id' => $model->id));
            }
        }
        $salesOrder = SalesOrder::model()->findByPk($model->sales_order_id);
        $this->render('form',array('model'=>$model, 'items'=>$model->items, 'relatedOrder' => $salesOrder));
    }
    
    //出库单详情
    public function actionView($id){
        $this->breadcrumbs['出库单'] = array('/pss/stockout/index');
        $this->breadcrumbs[] = "出库单详情";
        $model = $this->loadModel($id, 'StockOut');
        $this->render('view', array('model' => $model, 'items' => $model->items));
    }
    
    /**
     * @return StockOut
     */
    protected function loadModel($id=null){
        $model = StockOut::model()->findByPk($id ? $id : $_GET['id']);
        if ($model == null){
            // 由页面来源判定是否有上一步
            $url = isset($_SERVER['HTTP_REFERER']) ? '<a href="javascript:;" onclick="history.go(-1)">返回上一步</a>' : '<a href="index.php">返回OA主页</a>';
            throw new CHttpException(500, '很抱歉，您无权查看当前页面内容，请联系管理员进行授权。'.$url);
        }
        return $model;
    }
    
    //货品出库 出库单  下拉详情
    public function actionItems($id){
        $criteria = new CDbCriteria();
        $model = $this->loadModel($id, 'StockOut');
        $model->setDbCriteria($criteria);
        
        $this->widget('zii.widgets.grid.CGridView', array(
            'id'=>"order-{$id}-product-item",
            'dataProvider'=> new CArrayDataProvider($model->items),
            'enablePagination'=>false,
            'emptyText'=>'无产品信息',
            'template'=>'{items}',
            'columns'=>array(
                array('name' => '出库产品', 'value' => '$data->product_name'),
                array('name' => '型号', 'value' => '$data->product_no'),
                array('name' => '品牌', 'value' => '$data->product_brand'),
                array('name' => '单价', 'value' => '$data->price'),
                array('name' => '应付金额', 'value' => '$data->totalPrice'),
                array('name' => '出库数量', 'value' => '$data->quantity'),
                array('header' => '当前库存', 'value' => '$data->stock->quantity'),
            ),
        ));
    }
    
    //出库产品
    public function actionItem(){
        $this->breadcrumbs[] = '出库产品';
        $model = new StockOutItem('search');
        $model->unsetAttributes();
        if(isset($_GET['StockOutItem'])){
            $model->attributes = $_GET['StockOutItem'];
        }
        $this->render('item', array('model' => $model));
    }
    
    //销售退货产品
    public function actionBack(){
        $this->breadcrumbs[] = '销售退货';
        $model = new BackSales('search');
        $model->unsetAttributes();
        if (isset($_GET['BackSales'])){
            $model->attributes = $_GET['BackSales'];
        }
        $this->render("back", array('model' => $model));
    }
    
    /**
     * @return StockInItem[] array
     */
    protected function getOutItems($order_id=''){
        $items = array();
        
        //var_dump($_POST['StockInItem']);exit;
        if (isset($_POST['StockOutItem'])){
            $itemData = $_POST['StockOutItem'];
            
            foreach ($itemData as $val){
                
                $item = new StockOutItem;
                $item->attributes = $val;
                $items[] = $item;
            }
        }elseif (!empty($order_id)){
            $order = SalesOrder::model()->findByPk($order_id);
            foreach ($order->products as $k => $product){
                $item = new StockOutItem();
                $item->product_id = $product->id;
                $item->product_name = $product->name;
                $item->product_no = $product->no;
                $item->product_brand = $product->brand->name;
                $item->product_unit = $product->unit->name;
                $item->product_cate = $product->cate->name;
                $item->quantity = $order->items[$k]->quantity - $order->items[$k]->outQuantity;
                $item->price = $order->items[$k]->price;
                $items[] = $item;
            }
        }
        return $items;
    }
}