<?php $this->renderPartial('../_stockTop'); ?>

<aside class="portlet-light span5">
    <h2>产品类别</h2>
    <div style='width: 180px; height: 550px; overflow: auto;'>
        <?php
        $items = array(array('label'=>'全部', 'url'=>array('/erp/product/list'), 'active'=> !isset($_GET['cate_id'])));
        foreach ($cate as $cat){
            $items[] = array('label'=>$cat->name, 'url'=>array('/erp/product/list', 'cate_id' => $cat->id));
        }
         $this->widget('zii.widgets.CMenu', array(
                'items'=>$items,
                'htmlOptions' => array( 'class' => 'portlet-list'),
        ));
        ?>
    </div>
</aside>
    
<div style="overflow:auto;">

<!-- 搜索条件start -->
<?php $this->renderPartial('_search', array('product' => $model));?>
<!-- 搜索条件end -->
<div class="main-panel">
<?php
if (ErpPrivilege::stockCheck(ErpPrivilege::STOCK_ADD)){
    echo CHtml::link('添加产品', array('/erp/product/create'.(!empty($cate_id) ? "&cate_id=".$cate_id : '')), array('class' => 'button js-dialog-link', 'style' => 'margin: 5px 0 5px 3px'));
}
?>
</div>
<?php
if($cate_id !=0){
    $model->cate_id = $cate_id;
}

$this->widget('zii.widgets.grid.CGridView', array(
        'id' => 'product-grid',
        'dataProvider' => $model->search(),
        'emptyText' => '暂无录入产品数据！',
        'selectableRows' => 2,
        'columns'=>array(
            //array('name'=>'id', 'headerHtmlOptions' => array('class' => 'span2')),
            'name','no',
            array('name'=>'unit_id', 'value'=>'$data->unit->name'),
            array('name'=>'brand_id', 'value'=>'$data->brand?$data->brand->name:""'),
            array('name'=>'buy_price', 'type' => 'number', 'headerHtmlOptions' => array('class' => 'span3')),
            array('name'=>'sales_price', 'type' => 'number', 'headerHtmlOptions' => array('class' => 'span3')),
            array('name'=>'可用库存', 'value'=>'$data->totalStock', 'headerHtmlOptions' => array('class' => 'span3')),
            array('name'=>'库存报警', 'value'=>'$data->safe_quantity', 'headerHtmlOptions' => array('class' => 'span2')),
            array(
                'header'=>'操作',
                'type' => 'raw',
                'value' => 'CHtml::link("&nbsp;", Yii::app()->createUrl("/erp/product/update", array("id"=>$data->id)), array("class"=>"js-dialog-link update", "data-title"=>"修改产品"))."&nbsp;&nbsp;&nbsp;".
                 (!ErpPrivilege::stockCheck(ErpPrivilege::STOCK_ADMIN) ? "" : 
                    CHtml::link("&nbsp;", Yii::app()->createUrl("/erp/product/delete", array("id"=>$data->id)), array("class"=>"js-confirm-link delete", "data-title"=>"您确定要删除此产品?")))',
                'headerHtmlOptions' => array('class' => 'span2'),
                'visible' => ErpPrivilege::stockCheck(ErpPrivilege::STOCK_ADMIN),
            ),
        ),
));?>
</div>
<div class="clear"></div>
