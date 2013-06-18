<aside class="portlet-light span5">
    <h2>审批表单</h2>
    <div style='height: 500px; overflow-y: auto;'>
    <?php
        $form_name = Yii::app()->request->getQuery('form_name');
         $this->widget('zii.widgets.CMenu', array(
                'items'=>array(
                    array('label'=>'销售单', 'url'=>array('/erp/approve/index', 'form_name' => 'SalesOrder'), 'active' => $form_name == 'SalesOrder'),
                    array('label'=>'采购单', 'url'=>array('/erp/approve/index', 'form_name' => 'BuyOrder' ), 'active' => $form_name == 'BuyOrder'),
                    array('label'=>'入库单', 'url'=>array('/erp/approve/index', 'form_name' => 'StockIn'), 'active' => $form_name == 'StockIn'),
                    array('label'=>'出库单', 'url'=>array('/erp/approve/index', 'form_name' => 'StockOut'), 'active' => $form_name == 'StockOut'),
                    array('label'=>'调拨单', 'url'=>array('/erp/approve/index', 'form_name' => 'StockAllocate'), 'active' => $form_name == 'StockAllocate'),
                    array('label'=>'销售退货单', 'url'=>array('/erp/approve/index', 'form_name' => 'BackSales'), 'active' => $form_name == 'BackSales'),
                    array('label'=>'采购退货单', 'url'=>array('/erp/approve/index', 'form_name' => 'BackBuy'), 'active' => $form_name == 'BackBuy'),
                ),
                'htmlOptions' => array( 'class' => 'portlet-list'),
        ));
    ?>
    </div>
</aside>
