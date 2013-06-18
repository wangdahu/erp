<div style="width: 720px; max-height: 400px; overflow: auto;">
    <div class="wide form" id="search-product">
    <?php $form=$this->beginWidget('CActiveForm', array(
        'action'=>Yii::app()->createUrl($this->route),
        'method'=>'get',
    )); ?>
        <div class="clearfix">
            <div class="cell span9">
                <?php echo $form->label($model, '关键字');?>
                <div class="item">
                    <div class="main">
                        <?php echo $form->textField($model,'keyword', array('class' => 'span6', 'placeholder' => '请输入产品名称/型号/品牌')); ?>
                    </div>
                </div>
            </div>
            <div class="cell span2">
            <?php echo CHtml::htmlButton('搜索', array('type'=>'submit')); ?>
            </div>
        </div>
    
    <?php $this->endWidget(); ?>
    </div>
    
    <?php 
    $dataProvider = $model->search();
    $gridView = $this->widget('zii.widgets.grid.CGridView', array(
        'dataProvider' => $dataProvider,
        'id' => 'allocateProduct',
        'ajaxUpdate'=>null,
        'emptyText' => '暂无产品数据！',
        'columns'=>array(
            array('class'=>'CCheckBoxColumn', 'selectableRows'=>2, 'checkBoxHtmlOptions'=>array('name'=>'id[]'), ),
            array('name'=>'产品名称', 'type'=>'raw', 'value'=>'$data->product->name.Chtml::hiddenField("product_id", $data->product->id);'),
            array('name'=>'型号', 'value'=>'$data->product->no'),
            array('name'=>'单位', 'value'=>'$data->product->unit->name'),
            array('name'=>'品牌', 'type' => 'raw', 'value'=>'$data->product->brand ? $data->product->brand->name : ""'),
            array('name'=>'产品类别', 'value'=>'$data->product->cate ? $data->product->cate->name : ""'),
            array('name'=>'所在仓库', 'type' => 'raw', 'value'=>'$data->storehouse->name'),
            array('name'=>'当前库存', 'type' => 'raw', 'value'=>'$data->quantity.CHtml::hiddenField("storehouse_id", $data->storehouse_id)'),
        ),
    ));
    ?>
</div>
    
<div style="float:right">
    <?=Chtml::htmlButton('确认', array('class' => 'highlight js-dialog-close', 'id' => 'popup-confirm'));?>
    <?=Chtml::htmlButton('取消', array('class' => 'js-dialog-close'));?>
</div>


<script>
    var gridId = '#<?php echo $gridView->id ?>';
 	$('#search-product form').submit(function(){
		$(gridId).yiiGridView('update', {data: $(this).serialize()});
		return false;
	});
    $('#popup-confirm').click(function(){
        var data = [];
        $(gridId + ' tbody input:checked').each(function(){
            var items = $(this).parents('tr').children();
            data.push({
                'id':$(this).val(),
            'product_id': $(this).parents().find(':hidden[name=product_id]').val(),
            'name':items.eq(1).text(),
            'no':items.eq(2).text(),
            'unit':items.eq(3).text(),
            'brand':items.eq(4).text(),
            'cate':items.eq(5).text(),
            'storehouse':items.eq(6).text(),
            'stock':items.eq(7).text(),
            'storehouse_id':$(this).parents().find(':hidden[name=storehouse_id]').val(),
            });
        });
        populateData(data);
    });
</script>
