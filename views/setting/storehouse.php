<?php
$this->renderPartial('../_settingTop');
Yii::app()->clientScript->registerScriptfile('/source/oa/js/autocomplete.js');
Yii::app()->clientScript->registerCssfile($this->module->assetsUrl . '/css/storehouse.css');
?>
<div class="main-panel">
<?php 
if (PssPrivilege::otherCheck(PssPrivilege::SETTING)){
    echo CHtml::link('添加仓库', array('/pss/storehouse/create'), array('class' => 'button js-dialog-link'));
}
?>
</div>
<?php

$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'notice-grid',
    'dataProvider' => $model->search(),
    'emptyText' => '暂无仓库设置信息！',
    'columns' => array(
        //array('name' => 'id', 'value' => '$data->id', 'headerHtmlOptions' => array('class' => 'span2')),
        array('name' => 'name', 'value' => '$data->name'),
        array('name' => 'description', 'value' => '$data->description', 'type' => 'ntext'),
        array('name' => 'storeman_id', 'value' => '$data->storeman->name'),
        array('name' => '所属部门', 'value' => '$data->getDeptName($data->storeman->department_id)', 'headerHtmlOptions' => array('class' => 'span3')),
        array(
            'header'=>'操作',
            'type' => 'raw',
            'visible' => PssPrivilege::otherCheck(PssPrivilege::SETTING),
            'value' => 'CHtml::link("&nbsp;", Yii::app()->createUrl("/pss/storehouse/update", array("id" => $data->id)), array("class"=>"js-dialog-link update", "data-title"=>"修改仓库设置")).
                CHtml::link("&nbsp;", Yii::app()->createUrl("/pss/storehouse/delete",array("id" => $data->id)), array("class"=>"js-confirm-link delete", "data-title"=>"您确定要删除当前仓库?"))',
            'headerHtmlOptions' => array('class' => 'span2')
             )
        ),
));
