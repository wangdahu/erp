<?php

class ReportController extends ErpController{
    
    public $layout = '//layouts/column1';
    
    public $defaultAction = 'sales';
    
    public function init(){
        if (!ErpPrivilege::otherCheck(ErpPrivilege::REPORT_VIEW)){
            throw new CHttpException(403, '无权限查看 <a href="javascript:" onclick="history.go(-1); ">返回上一步</a>');
        }
        parent::init();
    }
    
    public function actionSales(){
        $this->breadcrumbs[] = '财务报表';
        $search = $this->loadSearch();
        $model = $this->createModel($search->type);
        $this->render('sales', array('model' => $model, 'search' => $search));
    }
    
    public function actionBargraph(){
        $search = $this->loadSearch();
        $model = $this->createModel($search->type);
        $this->render('bargraph', array('model' => $model, 'search' => $search));
    }
    
    public function actionPiegraph(){
        
    }
    
    public function loadSearch(){
        $search = new Report('search');
        $search->unsetAttributes();
        $search->type = 'user';
        $search->view = 'list';
        if (isset($_GET['Report'])){
            $search->setAttributes($_GET['Report']);
        }
        return $search;
    }
    
    public function createModel($type){
        $scenario = 'sales';
        $criteria = new CDbCriteria();
        switch ($type){
            case 'user':
                $criteria->group = 'salesman_id';
                $model = new ErpUser($scenario);
                $model->hasSalesOrder();
                break;
            case 'department':
                $criteria->group = 'salesman_dept_id';
                break;
            case 'customer':
                $model = new Customer($scenario);
                $model->hasSalesOrder();
                break;
            case 'product':
                $model = new Product($scenario);
                $model->hasSalesOrder();
                break;
        }
        
        return $model;
    }
}
