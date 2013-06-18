<?
$this->renderPartial('../_settingTop');
$title = FormFlow::getLeftMenuList();
?>

<?php $this->renderpartial('../_approveLeft');?>

<div style="overflow:auto;">
<div class="main-title-big  radius-top" style='font-weight: bold; margin-bottom: 5px; font-size: 20px;'><label><?php echo $title[$form_name];?>&nbsp;审批流程</label></div>
<?
echo Html5::link('新建审批流程', array('/pss/approve/createflow', 'form_name' => $form_name), array('class' => 'button', 'style' => 'margin: 0 0 5px 10px;'));
$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'flow-grid',
    'dataProvider' => new CArrayDataProvider($model->flows),
    'emptyText' => '暂无审批流程！',
    'selectableRows' => 2,
    'columns' => array(
        array('name'=>'流程名称', 'type'=>'raw', 'value'=>'CHtml::link($data->name, array("updateflow","form_name"=>'.$form_name.',"flow_id"=>$data->flow_id),array("title"=>"查看编辑"))', 'headerHtmlOptions'=>array('class'=>'span5')),
        array('name'=>'流程适用人范围及审批流程', 'type'=>'raw',
              'value'=>'"流程适用人：".implode(" → ", $data->applyNodes)."<br>".
                        "审批流程：".implode(" → ",$data->approveNodes)."<br>".
                        "审批完成通知人员：".implode(" → ", $data->noticeNodes)'),
        array('name'=>'操作', 'type'=>'raw', 'value'=>'
              CHtml::link("&nbsp;", array("updateflow","form_name"=>'.$form_name.',"flow_id"=>$data->flow_id), array("class"=>"update", "title"=>"编辑"))."&nbsp&nbsp".
              CHtml::link("&nbsp;",array("copyflow","form_name"=>'.$form_name.',"flow_id"=>$data->flow_id, "group_id" => 0), array("class"=>"js-dialog-link copy-flow", "title"=>"流程复制"))."&nbsp&nbsp".
              CHtml::link("&nbsp;",array("deleteflow", "group_id" => 0, "flow_id" => $data->flow_id, "form_name" => '.$form_name.'), array("class"=>"delete js-confirm-link", "title"=>"删除", "data-title" => "您确定要删除当前流程吗？"))', 
              'headerHtmlOptions'=>array('class'=>'span3')),
    ),
));?>
</div>
