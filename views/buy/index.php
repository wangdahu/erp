<?php 
$this->renderPartial('../_buyTop');
$isHistory = $this->action->id == 'history';
$isAdmin = ErpPrivilege::buyCheck(ErpPrivilege::BUY_ADMIN);

$form = $this->beginWidget('ActiveForm', array(
    'id'=>'backSalesSearch',
    'method'=>'get',
    'action' => Yii::app()->createUrl($this->route),
    'htmlOptions' => array('class' => 'main-search-form'),
));
?>

    <div class="clearfix">
        <div class="cell span8">
            <div class="main">
                <?=$form->label($model, 'buyer_id');?>
                <?php $this->widget('ext.Selector', array('model' => $model,
                                                          'names' => array('buyer_dept_id', 'buyer_id'),
                                                          'htmlOptions' => array(array('class' => 'medium'), array('class' => 'small'))
                                                      )); ?>
            </div>
        </div>
        <div class="cell span4">
            <div class="main">
            <?=$form->label($model, 'approval_status');?>
            <?=$form->dropDownList($model, 'approval_id', $model->approveSelectValue, array('class' => 'small'));?>
            </div>
        </div>
        <div class="cell span4">
            <div class="main">
            <?=$form->label($model, '订单状态');?>
            <?=$form->dropDownList($model, 'status', array(''=>'请选择') + $model->statusOptions, array('class' => 'small'));?>
            </div>
        </div>
    </div>
    <div class="clearfix">
        <div class="cell span12">
            <div class="main">
                <?php echo $form->label($model, '录入时间');?>
                <?php 
                $dates = $model->datePatternOptions;
                $dates[3] .= ' '.$form->textField($model,'start_date', array('class' => 'js-datepicker', 'data-group' => 'created', 'data-type' => 'start')). ' ';
                $dates[3] .= $form->textField($model,'end_date', array('class' => 'js-datepicker', 'data-group' => 'created'));
                echo $form->radioButtonList($model,'date_pattern', $dates, array('separator'=>'&nbsp;')); ?>
            </div>
        </div>
        
        <div class="cell span11">
            <div class="main">
            <?=$form->label($model, '关键词');?>
            <?=$form->textField($model, 'keyword', array('class' => 'xlarge', 'placeholder' => '请输入单号/供应商名称/产品名称'));?>
            <?php echo CHtml::htmlButton('查找', array('type'=>'submit')); ?>
            </div>
        </div>
    </div>
<?php $this->endWidget();?>

<?if (!$isHistory){
?>

<div class="main-panel">
<?php
if (ErpPrivilege::buyCheck(ErpPrivilege::BUY_ORDER_CREATE)){
    echo CHtml::link('新添采购单', array('create'), array('class' => 'button'));
}
if (ErpPrivilege::stockCheck(ErpPrivilege::STOCK_ADD)){
    echo CHtml::htmlButton('采购入库', array('id' => 'btn_stockin', 'disabled' => 'disabled', 'style' => 'margin-right: 10px'));
}
if (ErpPrivilege::buyCheck(ErpPrivilege::BUY_BACK)){
    echo CHtml::htmlButton('采购退货',  array('id' => 'btn_backbuy', 'disabled' => 'disabled'));
}
?>
</div>
<?php
}
$gridView = $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'buy-order',
    'dataProvider'=>$model->search(),
    'emptyText'=>'暂无采购信息',
    'selectableRows' => 1,
    'selectionChanged' => 'function(id){
        $("#btn_stockin,#btn_backbuy").prop("disabled", !$("#" + id + " :checked").length);
    }',
    'columns'=>array(
        array('class'=>'CCheckBoxColumn', 'checkBoxHtmlOptions'=>array('name'=>'id')),
        array('name'=>'no', 'type'=>'raw', 'htmlOptions'=>array('style'=>'position:relative;'),
              'value'=>'CHtml::link($data->no, array("view", "id" => $data->id))
                    .Chtml::hiddenField("approval_status", $data->approveStatus)
                    .CHtml::tag("i", array(
                    "class"=>"arrow-open",
                    "style"=>"position: absolute; top:7px; right:7px;"))'),
        array('name'=>'supplier_name', 'value'=>'"<div class=\"ellipsis\">{$data->supplier_name}</div>"', 'type'=>'raw'),
        array('name'=>'total_price', 'type'=>'number'),
        'buyer',
        array('name' => 'created', 'type' =>'datetime', 'headerHtmlOptions' => array('class' => 'span3')),
        array('name'=>'approval_status', 'type'=>'raw', 'value'=>'$data->approveStatusText', 'headerHtmlOptions' => array('class' => 'span2')),
        array('name' => 'status', 'value'=>'array_key_exists($data->status, $data->statusOptions) ? $data->statusOptions[$data->status] : ""', 
                'headerHtmlOptions' => array('class' => 'span2')),
        array('visible' => !$isHistory, 'name' => '操作', 'type'=>'raw', 'value' => '
              ErpPrivilege::buyCheck(ErpPrivilege::BUY_ADMIN) || $data->buyer_id == Yii::app()->user->id ?
                  CHtml::linkButton("", 
                      array(
                          "class"=>"statement",
                          "title"=>"结单",
                          "submit"=>"",
                          "params"=>array("command"=>"statement", "id"=>"$data->id"),
                          "confirm"=>"确定要结单“ $data->no ”？"
                      )
                  ) : "无"', 
              'headerHtmlOptions' => array('class' => 'span1')),
        array('name' => '操作', 'type' => 'raw', 
              'visible' => $isHistory && $isAdmin,
              'value' => 'CHtml::link("&nbsp;", Yii::app()->createUrl("/erp/buy/deletehistory", array("id"=>$data->id)),
                          array("class"=>"js-confirm-link delete", "title"=>"删除", "data-title" => "删除后将无法恢复，您确定要删除吗？"))', 
              'headerHtmlOptions' => array('class' => 'span1')),
    ), 
));
?>
<script>
$(function(){
    $("#buy-order .arrow-open").toggle(
        function() {
            $(this).openView("<?=$this->createUrl('items')?>&id="+$(this).parents("tr").first().find(":checkbox").val());
        },
        function () {
            $(this).closeView();
        }
    );
    
    //采购入库
    $("#btn_stockin").click(function(){
    	var approval_status = $("#buy-order :checked").parents("tr").find(":hidden[name='approval_status']").val();
    	if(approval_status == '2'){//审批通过
    		$(this).prop('onclick', function(){
    			location.href="<?=$this->createUrl('/erp/stockin/create')?>&order_id="+$("#buy-order :checked").val();
    		});
      }else{//审批不通过
    		$.alert("该采购单未通过审批，不能做入库操作！");
      }
    });
    
    //采购退货
    $("#btn_backbuy").click(function(){
    	var approval_status = $("#buy-order :checked").parents("tr").find(":hidden[name='approval_status']").val();
    	if(approval_status == '2'){//审批通过
    		$(this).prop('onclick', function(){
    			location.href="<?=$this->createUrl('/erp/backbuy/create')?>&order_id="+$("#buy-order :checked").val();
    		});
      }else{//审批不通过
    		$.alert("该采购单未通过审批，不能做退货操作！");
      }
    });
});
</script>
