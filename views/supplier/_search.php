<?php 
Yii::app()->clientScript->registerScriptfile($this->module->assetsUrl . '/js/city.js');
Yii::app()->clientScript->registerScriptfile($this->module->assetsUrl . '/js/row.js');
?>

<!-- 进销存供应商管理搜索页面start -->
<?php $form = $this->beginWidget('ActiveForm', array(
    'action' => Yii::app()->createUrl($this->route),
    'id' => 'Supplier',
    'method' => 'get'
));?>
    
    <div class="clearfix">
        <div class="cell span8">
            <div class="main">
                <?=$form->label($supplier, 'user_id');?>
                <?php $this->widget('ext.Selector', array('model' => $supplier,
                                                          'names' => array('dept_id', 'user_id'),
                                                          'htmlOptions' => array(array('class' => 'medium'), array('class' => 'small'))
                                                      )); ?>
            </div>
        </div>
        <div class="cell span10">
            <div class="item">
                <div class="main">
                    <label>所在地区</label>
                    <?php echo $form->dropDownList($supplier, 'province', $supplier->provinceList, array('class' => 'province_blur'));?>
                    <?php echo $form->dropDownList($supplier, 'city', $supplier->cityList, array('class' => 'city_value'));?>
                </div>
            </div>
        </div>
    </div>
    <div class="clearfix">
        <div class="cell span12">
            <div class="main">
                <?php echo $form->label($supplier, '录入时间');?>
                    <?php 
                    $dates = $supplier->datePatternOptions;
                    $dates[3] .= ' '.$form->textField($supplier,'start_date', array('class' => 'js-datepicker', 'data-group' => 'created', 'data-type' => 'start')). ' ';
                    $dates[3] .= $form->textField($supplier,'end_date', array('class' => 'js-datepicker', 'data-group' => 'created'));
                    echo $form->radioButtonList($supplier,'date_pattern', $dates, array('separator'=>'&nbsp;')); ?>
            </div>
        </div>
        
        <div class="cell span11">
            <div class="main">
            <?=$form->label($supplier, '关键词');?>
            <?=$form->textField($supplier, 'keyword', array('class' => 'xlarge', 'placeholder' => '请输入供应商名称'));?>
            <?php echo CHtml::htmlButton('查找', array('type'=>'submit')); ?>
            </div>
        </div>
    </div>
<?php $this->endWidget();?>
<!-- 进销存供应商管理搜索页面end -->
