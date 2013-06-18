<div>
    <div class="wide form" id="search-customer">
    <?php $form=$this->beginWidget('CActiveForm', array(
    	'action'=>Yii::app()->createUrl($this->route),
    	'method'=>'get',
    )); ?>
        <div class="clearfix">
            <div class="cell span9">
                <?php echo $form->label($model, 'name');?>
                <div class="item">
                    <div class="main">
                        <?php echo $form->textField($model,'name', array('class' => 'span6', 'placeholder' => '客户名称')); ?>
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
        'id'=>'customer-grid',
        'dataProvider'=>$model->search(),
        'selectableRows'=>1,
        'ajaxUpdate'=>null,
        'emptyText'=>'暂无客户信息',
        'htmlOptions' => array('style' => 'max-height: 400px; overflow-y: auto; overflow-x: hidden'),
        'columns'=>array(
        	array('name'=>'name', 'headerHtmlOptions' => array('class' => 'span2')),
        	array('name'=>'linkman.name', 'headerHtmlOptions' => array('class' => 'span2')),
        	array('name'=>'fullAddress', 'headerHtmlOptions' => array('class' => 'span7')),
        	array('name'=>'电话', 'value'=>'$data->linkman->showPhones', 'type'=>'ntext', 'headerHtmlOptions' => array('class' => 'span5')),
        ),
    ));  ?>
</div>
<style>
    table tr{cursor: pointer;}
</style>
<?php
$n = '\n';
Yii::app()->clientScript->registerScript('search', <<<SCRIPT
 	$('#search-customer form').submit(function(){
		$('#customer-grid').yiiGridView('update', {data: $(this).serialize()});
		return false;
	});
    $('#customer-grid').parent().delegate('tbody tr', 'click', function(){
        var emptyText = $('#customer-grid .empty');
        if(emptyText.html() == null){
            var data = {}, items = $(this).children();
            data.id = $.fn.yiiGridView.getKey('customer-grid', this.rowIndex-1);
            data.name = items.eq(0).text();
            data.linkman = items.eq(1).text();
            data.address = items.eq(2).text();
            data.phone = items.eq(3).text().split('<br>').join("{$n}");
            populateCustomerData(data);
            $('#select-customer').dialog('close');
         }
    });
SCRIPT
);