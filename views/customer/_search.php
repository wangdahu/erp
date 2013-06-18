<?php 
Yii::app()->clientScript->registerScriptfile($this->module->assetsUrl . '/js/city.js');
Yii::app()->clientScript->registerScriptfile($this->module->assetsUrl . '/js/row.js');
?>

<!-- 进销存客户管理搜索页面start -->
<?php $form = $this->beginWidget('ActiveForm', array(
      'action' => Yii::app()->createUrl($this->route),
      'method' => 'get',
      'id' => 'Customer_search'
  ));?>
    
    <div class="clearfix">
        <div class="cell span8">
            <div class="main">
                <?=$form->label($customer, 'user_id');?>
                <?php $this->widget('ext.Selector', array('model' => $customer,
                                                          'names' => array('dept_id', 'user_id'),
                                                          'htmlOptions' => array(array('class' => 'medium'), array('class' => 'small'))
                                                      )); ?>
            </div>
        </div>
        <div class="cell span10">
            <div class="item">
                <div class="main">
                    <label>所在地区</label>
                    <?php echo $form->dropDownList($customer, 'province', array(''=>'请选择省份') + $customer->provinceList, array('class' => 'province_blur'));?>
                    <?php echo $form->dropDownList($customer, 'city', array(''=>'请选择城市') + $customer->cityList, array('class' => 'city_value'));?>
                </div>
            </div>
        </div>
    </div>
    <div class="clearfix">
        <div class="cell span12">
            <div class="main">
                <?php echo $form->label($customer, '录入时间');?>
                    <?php 
                    $dates = $customer->datePatternOptions;
                    $dates[3] .= ' '.$form->textField($customer,'start_date', array('class' => 'js-datepicker', 'data-group' => 'created', 'data-type' => 'start')). ' ';
                    $dates[3] .= $form->textField($customer,'end_date', array('class' => 'js-datepicker', 'data-group' => 'created'));
                    echo $form->radioButtonList($customer,'date_pattern', $dates, array('separator'=>'&nbsp;')); ?>
            </div>
        </div>
        
        <div class="cell span11">
            <div class="main">
            <?=$form->label($customer, '关键词');?>
            <?=$form->textField($customer, 'keyword', array('class' => 'xlarge', 'placeholder' => '请输入客户名称'));?>
            <?php echo CHtml::htmlButton('查找', array('type'=>'submit')); ?>
            </div>
        </div>
    </div>
<!-- 进销存客户管理搜索页面end -->
<?php $this->endWidget(); ?>
