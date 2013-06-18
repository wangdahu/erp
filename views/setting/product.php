<?php $this->renderPartial('../_settingTop'); ?>
<aside class="portlet-light span5">
    <h2>产品信息</h2>
<?php
     $this->widget('zii.widgets.CMenu', array(
            'items'=>array(
                array('label'=>'产品类别', 'url'=>array('/pss/setting/product', 'type' => 1)),
                array('label'=>'产品品牌', 'url'=>array('/pss/setting/product', 'type' => 2)),
                array('label'=>'产品单位', 'url'=>array('/pss/setting/product', 'type' => 3)),
            ),
            'htmlOptions' => array( 'class' => 'portlet-list'),
    ));
?>
</aside>

<?php
    if($type == '1'){//产品类别
        $title = '产品类别';
        $add_button = CHtml::link('添加类别', array('/pss/product/cate'), array('class' => 'button js-dialog-link', 'style' => 'margin: 0 0 5px 10px;'));
        $oprate_button = 'CHtml::link("&nbsp;", Yii::app()->createUrl("/pss/product/cate&id=$data->id"), array("class"=>"js-dialog-link update", "data-title"=>"修改产品类别")).
                          CHtml::link("&nbsp;", Yii::app()->createUrl("/pss/setting/delete&id=$data->id&type='.$type.'"), array("class"=>"js-confirm-link delete", "data-title"=>"您确定要删除当前类别?"))';
    }else if($type == '2'){//产品品牌
        $title = '产品品牌';
        $add_button = CHtml::link('添加品牌', array('/pss/product/brand'), array('class' => 'button js-dialog-link', 'style' => 'margin: 0 0 5px 10px;'));
        $oprate_button = 'CHtml::link("&nbsp;", Yii::app()->createUrl("/pss/product/brand&id=$data->id"), array("class"=>"js-dialog-link update", "data-title"=>"修改产品品牌")).
                          CHtml::link("&nbsp;", Yii::app()->createUrl("/pss/setting/delete&id=$data->id&type='.$type.'"), array("class"=>"js-confirm-link delete", "data-title"=>"您确定要删除当前品牌?"))';
    }else{//产品单位
        $title = '产品单位';
        $add_button = CHtml::link('添加单位', array('/pss/product/unit'), array('class' => 'button js-dialog-link', 'style' => 'margin: 0 0 5px 10px;'));
        $oprate_button = 'CHtml::link("&nbsp;", Yii::app()->createUrl("/pss/product/unit&id=$data->id"), array("class"=>"js-dialog-link update", "data-title"=>"修改产品单位")).
                          CHtml::link("&nbsp;", Yii::app()->createUrl("/pss/setting/delete&id=$data->id&type='.$type.'"), array("class"=>"js-confirm-link delete", "data-title"=>"您确定要删除当前单位?"))';
    }
?>

<div style="overflow:auto;">
<div class="main-title-big  radius-top" style='font-weight: bold; margin-bottom: 5px; font-size: 20px;'><label><?php echo $title;?></label></div>

<?php
if (PssPrivilege::otherCheck(PssPrivilege::SETTING)){
    echo $add_button;
}

$column_array = array(
    //array('name' => 'id', 'value' => '$data->id', 'headerHtmlOptions' => array('class' => 'span2')),
    array('name' => 'name', 'value' => '$data->name'),
    array('name' => 'remark', 'value' => '$data->remark', 'type' => 'ntext'),
    array(
        'header'=>'操作',
        'visible' => PssPrivilege::otherCheck(PssPrivilege::SETTING),
        'headerHtmlOptions' => array('class' => 'span2'),
        'type' => 'raw',
        'value' => $oprate_button,
    )
);

$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'notice-grid',
    'dataProvider' => $model->search(),
    'columns' => $column_array,
    'emptyText' => '暂无任何数据！',
    'selectableRows' => 2,
));
?>
</div>
<div class="clear"></div>
