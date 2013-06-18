<?php 
$this->renderPartial('../_salesTop');

$isHistory = $this->action->id == 'history';
$isAdmin = ErpPrivilege::salesCheck(ErpPrivilege::SALES_ADMIN);

$form = $this->beginWidget('ActiveForm', array(
    'id'=>'sales-search',
    'method'=>'get',
    'action' => Yii::app()->createUrl($this->route),
    'htmlOptions' => array('class' => 'main-search-form'),
));
?>
    <div class="clearfix">
        <div class="cell span8">
            <div class="main">
                <?=$form->label($model, 'salesman_id');?>
                <?php $this->widget('ext.Selector', array('model' => $model,
                                                          'names' => array('salesman_dept_id', 'salesman_id'),
                                                          'htmlOptions' => array(array('class' => 'medium'), array('class' => 'small'))
                                                      )); ?>
            </div>
        </div>
        <div class="cell span4">
            <div class="main">
            <?=$form->label($model, 'approval_id');?>
            <?=$form->dropDownList($model, 'approval_id', $model->approveSelectValue, array('class' => 'small'));?>
            </div>
        </div>
        <div class="cell span4">
            <div class="main">
            <?=$form->label($model, 'status');?>
            <?=$form->dropDownList($model, 'status', array(''=>'请选择') + $model->statusOptions, array('class' => 'small'));?>
            </div>
        </div>
    </div>
    <div class="clearfix">
        <div class="cell span12">
            <div class="main">
                <?php echo $form->label($model, 'created');?>
                <?php $dates = $model->datePatternOptions;
                $dates[3] .= ' '.$form->textField($model, 'start_date', array('class' => 'js-datepicker', 'data-group' => 'sales_date', 'data-type' => 'start')). ' ';
                $dates[3] .= $form->textField($model, 'end_date', array('class' => 'js-datepicker', 'data-group' => 'sales_date', ));
                ?>
                <?php echo $form->radioButtonList($model,'date_pattern', $dates, array('separator'=>'&nbsp;')); ?>
            </div>
        </div>
        
        <div class="cell span11">
            <div class="main">
            <?=$form->label($model, 'keyword');?>
            <?=$form->textField($model, 'keyword', array('class' => 'xlarge', 'placeholder' => '请输入单号/客户名称/产品名称'));?>
            <?php echo CHtml::htmlButton('查找', array('type'=>'submit')); ?>
            </div>
        </div>
    </div>
<?php
$this->endWidget();

if (!$isHistory){
?>
<div class="main-panel">
<?php
if (ErpPrivilege::salesCheck(ErpPrivilege::SALES_ORDER_CREATE)){
    echo CHtml::link('新添销售单', array('/erp/sales/create'), array('class' => 'button highlight'));
}
if (ErpPrivilege::stockCheck(ErpPrivilege::STOCK_ADD)){
    //'onclick'=>'location.href="'.$this->createUrl('/erp/stockout/create').'&order_id="+$("#sales-order :checked").val();'
    echo CHtml::htmlButton('销售出库', array('id' => 'btn_stockout', 'disabled' => 'disabled', 'style' => 'margin-right: 10px'));
}
if (ErpPrivilege::salesCheck(ErpPrivilege::SALES_BACK)){
    //'onclick'=>'location.href="'.$this->createUrl('/erp/backsales/create').'&order_id="+$("#sales-order :checked").val();
    echo CHtml::htmlButton('销售退货', array('id' => 'btn_backsale', 'disabled' => 'disabled'));
}
?>
</div>
<?php
}
$gridView = $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'sales-order',
    'dataProvider'=>$model->search(),
    'emptyText'=>'暂无销售单信息',
    'selectableRows' => 1,
    'selectionChanged' => 'function(id){
            $("#btn_stockout, #btn_backsale").prop("disabled", !$("#" + id + " :checked").length);
    }',
    'columns'=>array(
        array('class'=>'CCheckBoxColumn', 'checkBoxHtmlOptions'=>array('name'=>'id[]')),
        array('name'=>'no', 'type'=>'raw', 'htmlOptions'=>array('style'=>'position:relative;'),
                'value'=>'CHtml::link($data->no, array("view", "id" => $data->id))
                .CHtml::tag("i", array(
                "class"=>"arrow-open",
                "style"=>"position: absolute; top:7px; right:7px;"))'),
        array('name' => 'customer_name', 'type' => 'raw', 'value' => '$data->customer_name.Chtml::hiddenField("approval_status", $data->approveStatus)'),
        array('name' => 'total_price', 'type'=>'number'),
        array('name' => 'receivedPrice', 'type'=>'number'),
        array('name' => '未收金额', 'value' => '$data->notReceivedPrice', 'type'=>'number'),
        array('name' => 'salesman', 'headerHtmlOptions' => array('class' => 'span3')),
        array('name' => 'created', 'type'=>'datetime', 'headerHtmlOptions' => array('class' => 'span4')),
        array('name' => 'approval_id', 'type'=>'raw', 'value'=>'$data->approveStatusText', 'headerHtmlOptions' => array('class' => 'span2')),
        array('name' => 'status', 'value'=>'array_key_exists($data->status, $data->statusOptions) ? $data->statusOptions[$data->status] : ""', 
                'headerHtmlOptions' => array('class' => 'span2')),
        array('visible' => !$isHistory, 'name' => '操作', 'type'=>'raw', 'value' => 
                'ErpPrivilege::salesCheck(ErpPrivilege::SALES_ADMIN) || $data->salesman_id == Yii::app()->user->id ?
                ($data->isPassApprove ? 
                    CHtml::link("&nbsp;", Yii::app()->createUrl("/erp/sales/statement", array("id"=>$data->id)),
                    array("class"=>"js-confirm-link statement", "title"=>"结单", "data-title" => "确定要结单“ $data->no ”？"))
                    :
                    CHtml::link("&nbsp;", Yii::app()->createUrl("/erp/sales/warn"),
                    array("class"=>"js-confirm-link statement", "title"=>"结单", "data-title" => "单据未审批通过，不能结单！"))
                )
                : 
                "无"', 'headerHtmlOptions' => array('class' => 'span1')),
        array('name' => '操作', 'type' => 'raw', 
              'visible' => $isHistory && $isAdmin,
              'value' => 'CHtml::link("&nbsp;", Yii::app()->createUrl("/erp/sales/deletehistory", array("id"=>$data->id)),
                          array("class"=>"js-confirm-link delete", "title"=>"删除", "data-title" => "删除后将无法恢复，您确定要删除吗？"))', 
              'headerHtmlOptions' => array('class' => 'span1')),
    ),
));
?>

<script>
$(function(){
    $("#sales-order .arrow-open").toggle(
        function() {
            $(this).openView("<?=$this->createUrl('items')?>&id="+$(this).parents("tr").first().find(":checkbox").val());
        },
        function () {
            $(this).closeView();
        }
    );
    
    //销售出库
    $("#btn_stockout").click(function(){
		var approval_status = $("#sales-order :checked").parents("tr").find(":hidden[name='approval_status']").val();
		if(approval_status == '2'){//审批通过
			$(this).prop('onclick', function(){
				location.href="<?=$this->createUrl('/erp/stockout/create')?>&order_id="+$("#sales-order :checked").val();
			});
	    }else{//审批不通过
			$.alert("该销售单未通过审批，不能做出库操作！");
	    }
    });
    
    //销售退货
    $("#btn_backsale").click(function(){
		var approval_status = $("#sales-order :checked").parents("tr").find(":hidden[name='approval_status']").val();
		if(approval_status == '2'){//审批通过
			$(this).prop('onclick', function(){
				location.href="<?=$this->createUrl('/erp/backsales/create')?>&order_id="+$("#sales-order :checked").val();
			});
	    }else{//审批不通过
			$.alert("该销售单未通过审批，不能做退货操作！");
	    }
    });
});
</script>
