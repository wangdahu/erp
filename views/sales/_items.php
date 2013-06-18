<div class="main-panel">
    <?php if($model->isNewRecord || $this->action->id == 'update'){?>
        <?php 
        echo CHtml::link('添加已有产品', array('/pss/product/popup'), array('class' => 'button js-dialog-link', 'data-id' => 'select-product'));
        if (PssPrivilege::stockCheck(PssPrivilege::STOCK_ADD)){
            echo CHtml::link('新添产品', array('/pss/product/create&popup=1'), array('class' => 'button js-dialog-link'));
        }
        ?>
    <?php }?>
</div>
<?php 
echo CHtml::errorSummary($model->items, '', null, array('id' => 'item_error', 'style' => 'display:none'));
$gridView = $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'order-product-grid',
    'dataProvider'=>new CArrayDataProvider($model->items),
    'selectableRows'=>0,
    'ajaxUpdate'=>null,
    'htmlOptions'=>array('style'=>'width:800px'),
    'enablePagination'=>false,
    'emptyText'=>'无产品信息',
    'template'=>'{items}',
    'columns'=>array(
        array('name' => '产品名称', 'type'=>'raw', 'value' => '$data->product_name.CHtml::activeHiddenField($data, "[$data->product_id]product_id",array("value"=>"$data->product_id"))'),
        array('name' => '型号', 'value' => '$data->product_no'),
        array('name' => '品牌', 'value' => '$data->product_brand'),
        array('name' => '类别', 'value' => '$data->product_cate'),
        array('name' => '单位', 'value' => '$data->product_unit'),
        array('name' => '数量', 'type'=>'raw', 'value' => 'Yii::app()->controller->action->id != "view" ? CHtml::activeTextField($data, "[$data->product_id]quantity",array("class"=>"span2")) : $data->quantity', 'htmlOptions'=>array('class'=>'quantity')),
        array('name' => '单价（元）', 'type'=>'raw', 'value' => 'Yii::app()->controller->action->id != "view" ? CHtml::activeTextField($data, "[$data->product_id]price",array("class"=>"span2")) : Yii::app()->format->number($data->price)', 'htmlOptions'=>array('class'=>'price')),
        array('name' => '金额（元）', 'type'=>'number', 'value' => '$data->totalPrice', 'htmlOptions'=>array('class'=>'sub-total')),
        array('name' => '操作', 'value' => 'CHtml::link("", "javascript:", array("class"=>"delete"))', 'type' => 'raw', 'visible' => Yii::app()->controller->action->id != "view", 'headerHtmlOptions'=>array('class'=>'span1')),
    ),
));

?>
<script>
var tab, ids = [];
$(function(){
	if ($('#item_error li').length > 0){
        var str = $('#item_error li').map(function(){
            return $(this).text();
        }).get();
        $.alert(str.join("<br>"));
    }
    
    tab = $('#order-product-grid');
    tab.delegate(".delete", "click", function(){
        var row = $(this).closest('tr')[0],
            i = $.inArray(row.cells[0].textContent, ids);
        ids.splice(i, 1);
        $(row).remove();
        tab.find('.empty').parents('tr').toggle(ids.length == 0);
        calculateTotalPrice();
    }).delegate('.quantity input,.price input', 'input', function(){
        var row = $(this).closest('tr'),
            quantity = $('.quantity input', row).val(),
            price = $('.price input', row).val();
        $('.sub-total', row).text((quantity * price).toFixed(2));
        calculateTotalPrice();
    });
});

function calculateTotalPrice(){
    var total = 0;
    tab.find(".sub-total").each(function() {
        total += parseFloat(this.textContent, 10);
    });
    $("#SalesOrder_total_price").val(total.toFixed(2));
}

function populateData(data){
    tab.find('.empty').parents('tr').hide();
    $.each(data, function(i, v){
        if ($.inArray(v.id, ids) !== -1) return;
        ids.push(v.id);
        tab.find("tbody").append(
        '<tr>'+
            '<td>'+v.name+'<input name="SalesOrderItem['+v.id+'][product_id]" type="hidden" value="'+v.id+'"></td>'+
            '<td>'+v.no+'</td>'+
            '<td>'+v.brand+'</td>'+
            '<td>'+v.cate+'</td>'+
            '<td>'+v.unit+'</td>'+
            '<td class="quantity"><input name="SalesOrderItem['+v.id+'][quantity]" class="span2" min="0" type="text" value=""></td>'+
            '<td class="price"><input name="SalesOrderItem['+v.id+'][price]" class="span2" min="0.00" type="text" value="'+v.sales_price+'"></td>'+
            '<td class="sub-total">0.00</td>'+
            '<td><a href="javascript:" class="delete" >&nbsp;</a></td>'+
        '</tr>');
    });
}
</script>
