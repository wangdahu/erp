<?php

Yii::import('zii.widgets.CPortlet');

/**
 * 等待我审批的进销存单据
 */
class ErpPortlet extends HomePortlet {
    
    public $title = '等待我审批的进销存单据';
    
    public function __construct() {
        $this->app = 'erp';
        //$this->moreLink = Yii::app()->createUrl('erp');//更多
        $this->iconUrl = 'icon/portlet_icon.png';
        $this->backgroundImgUrl = 'images/home-portlet.png';
        //$this->unfoldImgUrl = 'images/unfold.png';
        
        parent::__construct();
    }
    
    protected function renderContent() {
        $this->render('erpPortlet', array('approve_list'=>self::getList()));
    }
    
    public function getList() {
        /**
         * 标题（链接）、发布人、时间
         */
        $task = $list = array();
        $res = ErpFlow::getNodeByProcessStatus(1);
        if (is_array($res) && count($res)) {
            foreach ($res as $v) {
                //判断节点权限 如果有权 则记录任务ID
                $result = ErpFlow::verifyNodeAuthority($v['node_id'], $v['task_id']);
                if (isset($result['prime']) && $result['prime'] === true) {
                    array_push($task, $v['task_id']);
                }
            }
        }

        $columns = 'main.id, main.no, main.user_id, main.created, main.approval_id AS task_id';
        
        
        $buy_list = Yii::app()->db->createCommand()->select($columns, "'BuyOrder' as form_name,")
        ->from('erp_buy_order main')->where(array('in', 'main.approval_id', $task));
        //入库单
        $stockin_list = Yii::app()->db->createCommand()->select($columns, "'StockIn' as form_name,")
        ->from('erp_stock_in main')->where(array('in', 'main.approval_id', $task));
        //出库单
        $stockout_list = Yii::app()->db->createCommand()->select($columns, "'StockOut' as form_name,")
        ->from('erp_stock_out main')->where(array('in', 'main.approval_id', $task));
        //销售退货
        $backsales_list = Yii::app()->db->createCommand()->select($columns, "'BackSales' as form_name,")
        ->from('erp_back_sales main')->where(array('in', 'main.approval_id', $task));
        //采购退货
        $backbuy_list = Yii::app()->db->createCommand()->select($columns, "'BackBuy' as form_name,")
        ->from('erp_back_buy main')->where(array('in', 'main.approval_id', $task));
        //调拨单
        $stockallocate_list = Yii::app()->db->createCommand()->select($columns, "'StockAllocate' as form_name,")
        ->from('erp_stock_allocate main')->where(array('in', 'main.approval_id', $task));
        
        $list = Yii::app()->db->createCommand()->select($columns, "'SalesOrder' as form_name,")->where(array('in', 'main.approval_id', $task))
                ->from('erp_sales_order main')
                ->union($buy_list->text)
                ->union($stockin_list->text)
                ->union($stockout_list->text)
                ->union($backsales_list->text)
                ->union($backbuy_list->text)
                ->union($stockallocate_list->text)
                
                ->queryAll();
        //echo "<pre>";
        //print_r($list);
        return $list;
    }
}

