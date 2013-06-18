<?php $is_view = Yii::app()->controller->action->id == "view";?>
<div class="main-panel">
    <?php if(!$is_view){?>
        <?php echo CHtml::htmlButton('添加已有产品', array('data-href'=>$this->createUrl('/pss/product/popup'), 'class' => 'js-dialog-link', 'data-id' => 'select-product', 'disabled' => $model->isBindOrder || $is_view));?>
        <?php echo CHtml::htmlButton('新添产品', array('data-href'=>$this->createUrl('/pss/product/create'), 'class' => 'js-dialog-link', 'disabled' => $model->isBindOrder || $is_view));?>
    <?php }
    ?>
</div>
<?php
echo CHtml::errorSummary($model->items, '', null, array('id' => 'item_error', 'style' => 'display:none'));
$isBindText = $model->isBindOrder ? "true" : "false";
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
        array('name' => '产品名称', 'type'=>'raw', 'value' => '$data->product_name.CHtml::activeHiddenField($data, "[$data->product_id]product_id")', 'htmlOptions'=>array('class'=>'product')),
        array('name' => '型号', 'value' => '$data->product_no', 'headerHtmlOptions'=>array('class'=>'span2')),
        array('name' => '品牌', 'value' => '$data->product_brand'),
        array('name' => '产品类别', 'value' => '$data->product_cate'),
        array('name' => '单位', 'value' => '$data->product_unit'),
        array('name' => '所在仓库', 'type'=>'raw', 'value' => 'Yii::app()->controller->action->id != "view" ? CHtml::activeDropDownList($data, "[$data->product_id]storehouse_id",
                array("" => "请选择")+CHtml::listData(Storehouse::model()->findAll(), "id", "name")) : 
                Storehouse::model()->findByPk($data->storehouse_id)->name', 'htmlOptions'=>array('class' => 'storehouse')),
        array('name' => '库存', 'value'=>'$data->isNewRecord ? "" : 
               Stock::model()->find("product_id=".$data->product_id." and storehouse_id=".$data->storehouse_id)->quantity', 'htmlOptions' => array('class' => 'stock')),
        array('name' => '数量', 'type'=>'raw', 'value' => 'Yii::app()->controller->action->id != "view" ? CHtml::activeTextField($data, "[$data->product_id]quantity", 
                array("class"=>"span2")) : $data->quantity',
                'htmlOptions'=>array('class' => 'quantity')),
        array('name' => '单价（元）', 'type'=>'raw', 'value' => 'Yii::app()->controller->action->id != "view" ? CHtml::activeTextField($data, "[$data->product_id]price", 
                array("class"=>"span2",)) : Yii::app()->format->number($data->price)',
                'htmlOptions'=>array('class' => 'price')),
        array('name' => '金额（元）', 'type'=>'number', 'value' => '$data->totalPrice', 'htmlOptions'=>array('class'=>'sub-total')),
        array('name' => '操作', 'type'=>'raw', 'value' => 'CHtml::link("&nbsp;", "javascript:", array("class"=>"delete"))',
                'headerHtmlOptions'=>array("class"=> "span1"), 'visible'=>(!$is_view)), 
    ),
)); 

echo CHtml::dropDownList('storehouse_select',null,
        array("" => "请选择")+CHtml::listData(Storehouse::model()->findAll(), "id", "name"), array('style'=>'display:none'));
?>
<script>
var tab, ids = [], totalPriceId = "<?=CHtml::activeId($model, 'total_price');?>";
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
        $("#"+totalPriceId).val(calculateTotalPrice());
    }).end()
    .find('.quantity input').live('blur', function(){
        var quantity = $(this).val(), 
        price = $(this).parent().next().find('input').val();
        console.log(totalPriceId);
        $(this).parent().next().next().text((quantity * price).toFixed(2));
        $("#"+totalPriceId).val(calculateTotalPrice());
    }).end()
    .find('.price input').live('blur', function(){
        var quantity = $(this).parent().prev().find('input').val(), 
        price = $(this).val();
        $(this).parent().next().text((quantity * price).toFixed(2));
        $("#"+totalPriceId).val(calculateTotalPrice());
    });
    
    tab.find(".storehouse select").live('change', function(){
        $(this).parents("tr").find(".stock").load("<?=$this->createUrl('/pss/stock/quantity')?>&storehouse_id="+(this.value == "" ? 0 : this.value)+"&product_id="+$(this).parents("tr").find(".product input").val());
    }).change();
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
        row = $('<tr>'+
            '<td class="product">'+v.name+'<input name="BackBuyItem['+v.id+'][product_id]" type="hidden" value="'+v.id+'"></td>'+
            '<td>'+v.no+'</td>'+
            '<td>'+v.brand+'</td>'+
            '<td>'+v.cate+'</td>'+
            '<td>'+v.unit+'</td>'+
            '<td class="storehouse"></td>'+
            '<td class="stock">0</td>'+
            '<td class="quantity"><input name="BackBuyItem['+v.id+'][quantity]" class="span2" min="0" type="text" value="'+v.safe_quantity+'"></td>'+
            '<td class="price"><input name="BackBuyItem['+v.id+'][price]" class="span2" min="0.00" type="text" value="'+v.buy_price+'"></td>'+
            '<td class="sub-total">0.00</td>'+
            '<td><a href="javascript:" class="delete">&nbsp;</a></td>'+
        '</tr>');
        row.find('.storehouse').append(
                $("#storehouse_select").clone().show().removeAttr("id").attr("name", "BackBuyItem["+v.id+"][storehouse_id]"));
        tab.find("tbody").append(row);
    });
}
</script>
