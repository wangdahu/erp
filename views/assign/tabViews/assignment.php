<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'assignment-view',
    'htmlOptions' => array('style' => 'width:510px'),
    'dataProvider'=> new CArrayDataProvider($buyers),
    'emptyText'=>'无分配信息',
    'columns'=>array(
        array('name'=>'采购对象', 'value'=> '$data->name', 'headerHtmlOptions' => array('class' => 'span3')),
        array('name'=>'负责产品', 'value'=> '$data->products', 'htmlOptions' => array('id' => 'product_info', 'style' => 'text-align:left')),
        array(
            'header'=>'操作',
            'type' => 'raw',
            'value' => 'CHtml::link("&nbsp;", Yii::app()->createUrl("/pss/assign/update", array("id"=>$data->id, "type"=>$data->type)), array("class"=>"js-dialog-link update", "data-title"=>"修改采购分配")).
                CHtml::link("&nbsp;", Yii::app()->createUrl("/pss/assign/delete", array("id"=>$data->id, "type"=>$data->type)), 
                array("class"=>"js-confirm-link delete", "data-title"=>"您确定要删除所选分配?",))',
            'headerHtmlOptions' => array('class' => 'span3')
        ),
    ),
));
