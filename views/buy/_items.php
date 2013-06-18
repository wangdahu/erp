<div class="main-panel">
    <?php if($model->isNewRecord || $this->action->id == 'update'){?>
        <?php echo CHtml::link('添加已有产品', array('/erp/product/popup', 'buy' => '1'), array('class' => 'button js-dialog-link', 'data-id' => 'select-product')); ?>
        <?php 
        if (ErpPrivilege::stockCheck(ErpPrivilege::STOCK_ADD)){
            echo CHtml::link('新添产品', array('/erp/product/create', 'popup' => 1), array('class' => 'button js-dialog-link')); 
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
    'htmlOptions'=>array('style'=>'width:900px'),
    'enablePagination'=>false,
    'emptyText'=>'无产品信息',
    'template'=>'{items}',
    'columns'=>array(
//        array('name' => '编号', 'type' => 'raw', 'value' => '$data->product_id.Html5::activeHiddenField($data, "[".$data->product_id."]product_id", 
//                    array("value"=>"$data->product_id"))'),
        array('name' => '产品名称', 'type' => 'raw', 'value' => '$data->product_name.Html5::activeHiddenField($data, "[".$data->product_id."]product_id", 
                    array("value"=>"$data->product_id"))'),
        array('name' => '型号', 'value' => '$data->product_no'),
        array('name' => '品牌', 'value' => '$data->product_brand'),
        array('name' => '类别', 'value' => '$data->product_cate'),
        array('name' => '单位', 'value' => '$data->product_unit'),//$data->price
        array('name' => '数量', 'type' => 'raw', 'value' => 'Yii::app()->controller->action->id != "view" ? Html5::activeTextField($data, "[".$data->product_id."]quantity", 
                    array("class"=>"span2", "min"=>"0", "value"=>"$data->quantity")) : $data->quantity',
                    'htmlOptions'=>array('class' => 'quantity')),
        array('name' => '单价（元）', 'type' => 'raw', 'value' => 'Yii::app()->controller->action->id != "view" ? Html5::activeTextField($data, "[".$data->product_id."]price", 
                    array("class"=>"span2", "value"=>"$data->price")) : Yii::app()->format->number($data->price)',
                    'htmlOptions'=>array('class' => 'price')),
        array('name' => '金额（元）', 'type'=>'number', 'value' => '$data->totalPrice', 'htmlOptions' => array('class' => 'sub-total')),
        array('name' => '操作', 'visible' => Yii::app()->controller->action->id != "view", 'type' => 'raw', 'value' => 'CHtml::link("", "#", array("class"=>"delete"))', 'headerHtmlOptions'=>array('class'=>'span1')),
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
    tab.find(".delete").live("click", function(){
        var i = $.inArray($(this).parent().siblings().first().text());
        ids.splice(i, 1);
        $(this).parents('tr').remove();
        tab.find('.empty').parents('tr').toggle(ids.length == 0);
        $("#BuyOrder_total_price").val(calculateTotalPrice());
    }).end()
    .find('.quantity input').live('blur', function(){
        var quantity = $(this).val(), 
        price = $(this).parent().next().find('input').val();
        console.log(quantity, price);
        $(this).parent().next().next().text((quantity * price).toFixed(2));
        $("#BuyOrder_total_price").val(calculateTotalPrice());
    }).end()
    .find('.price input').live('blur', function(){
        var quantity = $(this).parent().prev().find('input').val(), 
        price = $(this).val();
        $(this).parent().next().text((quantity * price).toFixed(2));
        $("#BuyOrder_total_price").val(calculateTotalPrice());
    });
});

function calculateTotalPrice(){
    var result = 0;
    tab.find(".sub-total").each(function(){
        result += parseFloat($(this).text());
    });
    return result.toFixed(2);
}

function populateData(data){
    tab.find('.empty').parents('tr').hide();
    $.each(data, function(i, v){
        if ($.inArray(v.id, ids) !== -1) return;
        ids.push(v.id);
        tab.find("tbody").append(
        '<tr>'+
            //'<td>'+v.id+'<input name="BuyOrderItem['+v.id+'][product_id]" type="hidden" value="'+v.id+'"></td>'+
            '<td>'+v.name+'<input name="BuyOrderItem['+v.id+'][product_id]" type="hidden" value="'+v.id+'"></td>'+
            '<td>'+v.no+'</td>'+
            '<td>'+v.brand+'</td>'+
            '<td>'+v.cate+'</td>'+
            '<td>'+v.unit+'</td>'+
            '<td class="quantity"><input name="BuyOrderItem['+v.id+'][quantity]" class="span2" min="0" type="text" value="'+(v.safe_quantity == "" ? v.safe_quantity : 0)+'"></td>'+
            '<td class="price"><input name="BuyOrderItem['+v.id+'][price]" class="span2" type="text" value="'+v.buy_price+'"></td>'+
            '<td class="sub-total">0.00</td>'+
            '<td><a href="javascript:" class="delete">&nbsp;</a></td>'+
        '</tr>');
    });
}
</script>
