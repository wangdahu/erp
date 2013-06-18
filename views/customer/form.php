<?php
Yii::app()->clientScript->registerScriptfile($this->module->assetsUrl . '/js/city.js');
Yii::app()->clientScript->registerScriptfile($this->module->assetsUrl . '/js/row.js');

?>
    <div class="main-title-big  radius-top">
    	<h3><?php echo $title?></h3>
    </div>
    
    <!-- 进销存新增客户页面start -->
    <?php $form = $this->beginWidget('ActiveForm', array('id' => 'Customer'));?>
    <div class="main-search-forms">
        <!-- 隐藏域 -->
        <?php echo $form->hiddenField($customer->linkman, 'id');?>
        <br>
        <!-- 客户信息start -->
        <div style='border-bottom: 2px solid #C0C0C0; padding-left: 10px; padding-bottom: 5px; margin: 0 10px 15px 10px;'>
        	<label style='font-weight: bold; font-size: 16px;'>客户信息</label>
        </div>
        <div class="more-info" style="display: block;">
            <div class="clearfix">
                <div class="cell span10">
                    <label for="CrmContact_type"><?php echo $form->labelEx($customer, 'type');?></label>
                    <div class="item">
                        <div class="main">
                            <?php echo $form->radioButtonList($customer, 'type', array('0'=>'企业客户', '1'=>'个人客户'), array('separator' => ' ')); ?>
                        </div>
                        <?php echo $form->error($customer,'type');?>
                    </div>
                </div>
                <div class="cell span10">
                    <label><?php echo $form->labelEx($customer, 'followman_id');?></label>
                    <div class="item">
                        <div class="main">
                            <?=$form->searchField($customer,'followman', array('placeholder' => "请输入人员姓名或拼音", 'class' => 'span5 js-complete'));?>
                            <?=$form->hiddenField($customer,'followman_id');?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="clearfix">
                <div class="cell span10">
                    <label><?php echo $form->labelEx($customer, 'name');?></label>
                    <div class="item">
                        <div class="main">
                            <?php echo $form->textField($customer, 'name'); ?>
                        </div>
                        <?php echo $form->error($customer,'name');?>
                    </div>
                </div>
                <div class="cell span10">
                    <label><?php echo $form->labelEx($customer, 'business');?></label>
                    <div class="item">
                        <div class="main">
                            <?php echo $form->textField($customer, 'business', array('style' => 'width:230px;')); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="clearfix">
            <div class="cell">
                <label><?php echo $form->labelEx($customer, 'address');?></label>
                <div class="item">
                    <div class="main">
                            <?php echo $form->dropDownList($customer, 'country', $customer->countryList, array('class' => 'country_blur'));?>
                            <span class="for-china">
                            <?php echo $form->dropDownList($customer, 'province', array(''=>'请选择省份') + $customer->provinceList, array('class' => 'province_blur'));?>
                            <?php echo $form->dropDownList($customer, 'city', array(''=>'请选择城市') + $customer->cityList, array('class' => 'city_value span3'));?>
                            </span>
                            <?php echo $form->textField($customer, 'address', array('style' => 'width:280px;', 'placeholder' => '请输入详细街道地址...')); ?>
                            <?php echo $form->error($customer,'province');?>
                            <?php echo $form->error($customer,'city');?>
                            <?php echo $form->error($customer,'address');?>
                     </div>
                </div>
            </div>
        </div>
        <!-- 客户信息end -->
        
        <!-- 联系人信息start -->
        <div style='border-bottom: 2px solid #C0C0C0; padding-left: 10px; padding-bottom: 5px; margin: 10px 10px 15px 10px;'>
        	<label style='font-weight: bold; font-size: 16px;'>联系人信息</label>
        </div>
        <div class="clearfix">
            <div class="cell span10">
                <?php echo $form->labelEx($customer->linkman, 'name')?>
                <div class="item">
                    <div class="main">
                        <?php echo $form->textField($customer->linkman, 'name'); ?>
                        <?php echo $form->radioButtonList($customer->linkman, 'gender', array('1' => '先生', '2' => '女士'), array('separator' => ' '));?>
                    </div>
                    <?=$form->error($customer->linkman, 'name');?>
                    <?=$form->error($customer->linkman, 'gender');?>
                </div>
            </div>
            <div class="cell span10">
                <label><?php echo $form->labelEx($customer->linkman, 'department');?></label>
                <div class="item">
                    <div class="main">
                       <?php echo $form->textField($customer->linkman, 'department'); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="clearfix">
            <div class="cell span10">
                <label><?php echo $form->labelEx($customer->linkman, 'post');?></label>
                <div class="item">
                    <div class="main">
                        <?php echo $form->textField($customer->linkman, 'post');?>
                    </div>
                </div>
            </div>
            <div class="cell span10">
                <label><?php echo $form->labelEx($customer->linkman, 'in_no');?></label>
                <div class="item">
                    <div class="main">
                       <?php echo $form->textField($customer->linkman, 'in_no');?>
                    </div>
                </div>
            </div>
        </div>
        <div class="clearfix">
            <div class="cell span10">
                <label><?php echo $form->labelEx($customer->linkman, 'email');?></label>
                <div class="item">
                    <div class="main">
                        <?php echo $form->textField($customer->linkman, 'email');?>
                    </div>
                </div>
            </div>
            <div class="cell span10">
                <label><?php echo $form->labelEx($customer->linkman, 'fax');?></label>
                <div class="item">
                    <div class="main">
                       <?php echo $form->textField($customer->linkman, 'fax');?>
                    </div>
                </div>
            </div>
        </div>
        <div class="clearfix">
            <div class="cell span10">
                <label><?php echo $form->labelEx($customer->linkman, 'im_no');?></label>
                <div class="item">
                    <div class="main">
                        <?php echo $form->textField($customer->linkman, 'im_no');?>
                    </div>
                </div>
            </div>
        </div>
        <div class="js-contact-box">
            <?php foreach ($customer->linkman->mobiles as $i => $mobile):?>
            <div class="clearfix js-mobile">
                <div class="cell span10">
                    <label><?= $i == 0 ? $form->labelEx($customer->linkman, 'mobile') : "&nbsp;";?></label>
                    <div class="item">
                        <div class="main">
                            <?=$form->textField($customer->linkman, "mobile[]", array('class' => 'js-contact_repeat', 'value' => $mobile));?>
                            <?php if ($i == 0):?>
                            <a href="javascript:;" class="js-add-mobile">添加</a>
                            <?php else:?>
                            <a href="javascript:;" class="js-remove-mobile">删除</a>
                            <?php endif;?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach;?>
            <?php echo $form->error($customer->linkman,'mobile');?>
            <div class="clearfix js-mobile" style="display:none;">
                <div class="cell">
                    <label>&nbsp;</label>
                    <div class="item span10">
                        <div class="main">
                            <?php echo $form->textField($customer->linkman, 'mobile[]', array('class' => 'js-contact_repeat', 'value' => ''));?> 
                            <a href="javascript:;" class="js-remove-mobile">删除</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="js-contact-box">
            <?php foreach ($customer->linkman->phones as $i => $phone):?>
            <div class="clearfix js-phone">
                <div class="cell span12">
                    <label><?= $i == 0 ? $form->labelEx($customer->linkman, 'phone') : "&nbsp;";?></label>
                    <div class="item">
                        <div class="main">
                            <?php echo CHtml::dropDownList("CustomerLinkman[phone_type][]", $customer->linkman->phoneTypes[$i], $customer->linkman->phoneTypeList); ?>
                            <?php echo $form->textField($customer->linkman, "phone[]", array('value' => $phone));?>
                            <?php if ($i == 0):?>
                            <a href="javascript:;" class="js-add-phone">添加</a>
                            <?php else:?>
                            <a href="javascript:;" class="js-remove-phone">删除</a>
                            <?php endif;?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach;?>
            <div class="clearfix js-phone" style="display:none;">
                <div class="cell">
                    <label >&nbsp;</label>
                    <div class="item span10">
                        <div class="main">
                            <?php echo $form->dropDownList($customer->linkman, "phone_type[]", $customer->linkman->phoneTypeList); ?> 
                            <?php echo $form->textField($customer->linkman, 'phone[]', array('value' => ''));?>
                            <a href="javascript:;" class="js-remove-phone">删除</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="clearfix">
            <div class="cell">
                <label><?php echo $form->labelEx($customer->linkman, 'remark');?></label>
                <div class="item">
                    <div class="main">
                    	<?php echo $form->textArea($customer->linkman, 'remark', array('rows' => 4, 'cols' => '90'))?>
                    </div>
                </div>
            </div>
        </div>
        <!-- 联系人信息end -->
        
        <div class="actions">
            <input name="submit_value" id="submitValue" type="hidden" />
            <?php if($title == '新添客户'){?>
            <button type="submit" class="highlight" onclick="$('#submitValue').val('0')" >保存并继续添加</button>
            <?php }?>
            <button type="submit" class="highlight" onclick="$('#submitValue').val('1')" >保存</button>
            <button type="reset" onclick='javascript:history.back();'>取消</button>
        </div>
    </div>
    <?php $this->endWidget();?>
    <!-- 进销存新增客户页面end -->
