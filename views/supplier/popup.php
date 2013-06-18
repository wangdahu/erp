<div style="width: 720px; max-height: 400px;">
    <div class="wide form" id="search-supplier">
    <?php $form=$this->beginWidget('CActiveForm', array(
    	'action'=>Yii::app()->createUrl($this->route),
    	'method'=>'get',
    )); ?>
        <div class="clearfix">
            <div class="cell span9">
                <?php echo $form->label($model, 'name');?>
                <div class="item">
                    <div class="main">
                        <?php echo $form->textField($model,'name', array('class' => 'span6', 'placeholder' => '供应商名称')); ?>
                    </div>
                </div>
            </div>
            <div class="cell span2">
    		<?php echo CHtml::htmlButton('搜索', array('type'=>'submit')); ?>
    		</div>
        </div>
    
    <?php $this->endWidget(); ?>
    </div>
    
    <?php $this->widget('zii.widgets.grid.CGridView', array(
        'id'=>'supplier-grid',
        'dataProvider'=>$model->search(),
        'selectableRows'=>1,
        'ajaxUpdate'=>null,
        'emptyText'=>'暂无供应商信息',
        'columns'=>array(
        	array('name'=>'name', 'headerHtmlOptions'=>array('class'=>'span3')),
        	array('name'=>'linkman.name', 'headerHtmlOptions'=>array('class'=>'span2')),
        	'fullAddress',
        	array('name'=>'linkman.phone', 'type'=>'ntext', 'value'=>'$data->linkman->showPhones', 'headerHtmlOptions'=>array('class'=>'span5')),
             //array('visible' => true, 'name' => 'data', 'value' => 'json_encode($data->attributes + array("linkman" => $data->linkman->attributes))'),
        ),
    ));  ?>
</div>
<style>
    table tr{cursor: pointer;}
</style>
<?php
$n = '\n';
Yii::app()->clientScript->registerScript('search', <<<SCRIPT
 	$('#search-supplier form').submit(function(){
		$('#supplier-grid').yiiGridView('update', {data: $(this).serialize()});
		return false;
	});
    
    $('#supplier-grid').parent().delegate('tbody tr', 'click', function(){
        var emptyText = $('#supplier-grid .empty');
        if(emptyText.html() == null){
            var data = {}, items = $(this).children();
            data.id = $.fn.yiiGridView.getKey('supplier-grid', this.rowIndex-1);
            data.name = items.eq(0).text();
            data.linkman = items.eq(1).text();
            data.address = items.eq(2).text();
            data.phone = items.eq(3).text().split('<br>').join("{$n}");
            populateSupplierData(data);
            $('#select-supplier').dialog('close');
        }
    });
SCRIPT
);
