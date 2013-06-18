<?php $is_view = Yii::app()->controller->action->id != "view";?>
<div class="">
    <div class="main main-panel" style='width: 98%'>
    &nbsp;
    <?php if($is_view){?>
        <?=CHtml::htmlButton('添加已有产品', 
                array('disabled' => false, 'data-href' => $this->createUrl('/erp/stockallocate/popup'),
                     'class' => 'js-dialog-link', 'data-id' => 'select-product', 'data-title' => '选择已有产品')); ?>
    <?php }?>
    </div>
    <?php 
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
            array('name' => '产品名称', 'type'=>'raw', 'value' => '$data->product_name.
                   CHtml::activeHiddenField($data, "[$data->from_stock_id]from_stock_id").
                   CHtml::hiddenField("StockAllocateItem[$data->from_stock_id][pro_id]", $data->product_id)'),
            array('name' => '型号', 'value' => '$data->product_no'),
            array('name' => '品牌', 'value' => '$data->product_brand'),
            array('name' => '产品类别', 'value' => '$data->product_cate'),
            array('name' => '单位', 'value' => '$data->product_unit'),
            array('name' => '所在仓库', 'type'=>'raw', 'value' => 'Storehouse::model()->find("id={$data->from_storehouse_id}")->name.CHtml::activeHiddenField($data, "[".Stock::model()->find("product_id={$data["product_id"]} and storehouse_id={$data->from_storehouse_id}")->id."]from_storehouse_id", 
                   array("value"=>"$data->from_storehouse_id"))', 'htmlOptions'=>array('class' => 'storehouse',)),
            array('name' => '当前库存', 'type'=>'raw', 'value'=>'Stock::model()->find("product_id=".$data["product_id"]." and storehouse_id=".$data->from_storehouse_id)->quantity', 'htmlOptions' => array('class' => 'stock')),
            array('name' => '转出数量', 'type'=>'raw', 'value'=>'Yii::app()->controller->action->id != "view" ? Html5::activeTextField($data, "[".Stock::model()->find("product_id={$data["product_id"]} and storehouse_id={$data->from_storehouse_id}")->id."]quantity", 
                    array("class"=>"span2", "min"=>"0", "max"=>Stock::model()->find("product_id=".$data["product_id"]." and storehouse_id=".$data->from_storehouse_id)->quantity, "value"=>"$data->quantity")) : $data->quantity',
                    'htmlOptions'=>array('class' => 'quantity')),
            array('name' => '操作', 'type'=>'raw', 'value' => 'CHtml::link("&nbsp;", "javascript:", array("class"=>"delete"))',
                    'htmlOptions'=>array("class"=> $is_view ? "delete" : "", 'style'=>$is_view ? 'display:block;' : 'display:none;'),
                    'headerHtmlOptions'=>array('class'=>'span1'), 'visible'=>$is_view),
        ),
    ));
    ?>
</div>
<script>
if ($('#item_error li').length > 0){
    var str = $('#item_error li').map(function(){
        return $(this).text();
    }).get();
    $.alert(str.join("<br>"));
}

var tab,ids = {};
tab = $('#stockallocate_form');
function populateData(data){
    tab.find('.empty').parents('tr').hide();
    
    if(data == ""){
        row = $(
            '<tr>'+
                '<td colspan="10"><span class="empty">无产品信息</span></td>'+
            '</tr>');
        if(tab.find('.empty').html()){
            tab.find("tbody").html(row);
        }
    }else{
        $.each(data, function(i, v){
            if (v.id in ids) return;
            ids[v.id] = 1;
            row = $(
                '<tr>'+
                    '<td>'+v.name+'<input name="StockAllocateItem['+v.id+'][from_stock_id]" type="hidden" value="'+v.id+'"><input name="StockAllocateItem['+v.id+'][pro_id]" type="hidden" value="'+v.product_id+'"></td>'+
                    '<td>'+v.no+'</td>'+
                    '<td>'+v.brand+'</td>'+
                    '<td>'+v.cate+'</td>'+
                    '<td>'+v.unit+'</td>'+
                    '<td class="storehouse">'+v.storehouse+'<input name="StockAllocateItem['+v.id+'][from_storehouse_id]" type="hidden" value="'+v.storehouse_id+'"></td>'+
                    '<td class="stock">'+v.stock+'</td>'+
                    '<td class="quantity"><input name="StockAllocateItem['+v.id+'][quantity]" class="span2" min="0" type="text" value=""></td>'+
                    '<td><a href="javascript:" class="delete">&nbsp;</a></td>'+
                '</tr>');
            tab.find("tbody").append(row);
        });
    }
}

tab.delegate('.delete', 'click', function(){
    var tr = $(this).closest('tr');
    delete ids[tr.find('td:first input').val()];
    tr.remove();
});

tab.find(".storehouse select").live('change', function(){
    $(this).parents("tr").find(".stock").load("<?=$this->createUrl('/erp/stock/quantity')?>&storehouse_id="+this.value+"&product_id="+$(this).parents("tr").find(".product input").val());
});
</script>


