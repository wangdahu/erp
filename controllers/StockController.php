<?php

class StockController extends ErpController{
    
    public $defaultAction = 'index';
    
    public function init(){
        if (!ErpPrivilege::stockCheck(ErpPrivilege::STOCK_ADD) && !ErpPrivilege::stockCheck(ErpPrivilege::STOCK_VIEW)){
            throw new CHttpException(403, '无权限查看 <a href="javascript:" onclick="history.go(-1); ">返回上一步</a>');
        }
        parent::init();
        $this->breadcrumbs['货品库存'] = array('/erp/stock');
    }
    
    public function actionIndex(){
        $this->breadcrumbs[] = '货品仓库';
        $stock = new Stock('search');
        $stock->unsetAttributes();
        if (isset($_GET['Stock'])){
            $stock->attributes = $_GET['Stock'];
        }
        $this->render('index', array('model' => $stock));
    }
    
    public function actionInItem(){
        
    }
    
    public function actionAllocate(){
        $this->breadcrumbs[] = '产品调拨';
        $this->render('allocate');
    }
    
    //货品入库  入库单  下拉详情
    public function actionItems($id){
        $criteria = new CDbCriteria();
        $model = $this->loadModel($id, 'StockIn');
        $model->setDbCriteria($criteria);
        
        $this->widget('zii.widgets.grid.CGridView', array(
            'id'=>"order-{$_GET['order_id']}-product-item",
            'dataProvider'=> new CArrayDataProvider($model->items),
            'enablePagination'=>false,
            'emptyText'=>'无产品信息',
            'template'=>'{items}',
            'columns'=>array(
                array('name' => '入库产品', 'value' => '$data->product_name'),
                array('name' => '型号', 'value' => '$data->product_no'),
                array('name' => '品牌', 'value' => '$data->product_brand'),
                array('name' => '单位', 'value' => '$data->product_unit'),
                array('name' => '所在仓库', 'value' => '$data->storehouse->name'),
                array('name' => '单价', 'value' => '$data->price'),
                array('name' => '入库数量', 'value' => '$data->quantity'),
                array('name' => '金额', 'value' => '$data->totalPrice'),
                array('header' => '当前库存', 'value' => '$data->stock->quantity'),
            ),
        ));
    }
    
    public function actionQuantity(){
        $stock = Stock::model()->find("storehouse_id={$_GET['storehouse_id']} AND product_id={$_GET['product_id']}");
        Yii::app()->end($stock !== null ? (string) $stock->quantity : "0");
    }
}
