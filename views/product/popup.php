<div style="width: 720px; max-height: 400px; overflow: auto;">
    <div class="wide form" id="search-product">
    <?php $form=$this->beginWidget('CActiveForm', array(
    	'action'=>Yii::app()->createUrl($this->route),
    	'method'=>'get',
    )); ?>
        <div class="clearfix">
            <div class="cell span9">
                <?php echo $form->label($model, 'name');?>
                <div class="item">
                    <div class="main">
                        <?php echo $form->textField($model,'name', array('class' => 'span6')); ?>
                    </div>
                </div>
            </div>
            <div class="cell span2">
    		<?php echo CHtml::htmlButton('搜索', array('type'=>'submit')); ?>
    		</div>
        </div>
    
    <?php $this->endWidget(); ?>
    </div>
    <?php $gridView = $this->widget('zii.widgets.grid.CGridView', array(
        'id'=>'product-grid',
        'dataProvider'=>$model->search(),
        'ajaxUpdate'=>null,
        'emptyText'=>'无产品信息',
        'columns'=>array(
            array(
                'class'=>'CCheckBoxColumn',
                'selectableRows'=>2,
                'checkBoxHtmlOptions'=>array('name'=>'product_id[]'),
            ),
            'id',
            'name',
            'no',
            array('header'=>'品牌', 'value'=>'$data->brand ? $data->brand->name : ""'),
            array('header'=>'类别', 'value'=>'$data->cate->name'),
            array('header'=>'单位', 'value'=>'$data->unit->name.CHtml::hiddenField("totalStock", $data->totalStock).CHtml::hiddenField("sales_price", $data->sales_price).CHtml::hiddenField("buy_price", $data->buy_price).CHtml::hiddenField("safe_quantity", $data->safe_quantity)', 'type' => 'raw'),
        ),
    ));
    ?>
</div>
<div style="float:right">
    <?=Chtml::htmlButton('确认', array('class' => 'highlight', 'id' => 'popup-confirm'));?>
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
        $(gridId + ' tbody tr:has(:checked)').each(function(){
            var $row = $(this),
                d = {
                    'id': $row.find(':checkbox').val(),
                    'name': $(this.cells[2]).text(),
                    'no': $(this.cells[3]).text(),
                    'brand': $(this.cells[4]).text(),
                    'cate': $(this.cells[5]).text(),
                    'unit': $(this.cells[6]).text()
                };
            $row.find(':hidden').each(function() {
                d[this.name] = this.value;
            });
            data.push(d);
        });
        populateData(data);
        $('#select-product').dialog('close');
    });
</script>
