<?php
Yii::app()->clientScript->registerScriptfile($this->module->assetsUrl . '/js/city.js');
Yii::app()->clientScript->registerScriptfile($this->module->assetsUrl . '/js/row.js');
Yii::app()->clientScript->registerScriptfile('/source/oa/js/autocomplete.js');
?>
<!-- 进销存新增客户页面start -->
<?php $form = $this->beginWidget('ActiveForm', array('id' => 'customer_popup'));

$form->clientOptions['afterValidate'] = "js:function(form, data, hasError) {
    $.post(form.attr('action'), form.serialize(), function(json){
            if(json.status){
                var cus_data = json['cus_data'],
                    link_data = json['link_data'],
                    data = {};
                
                $('#SalesOrder_customer_id').val(cus_data['id']);
                $('#SalesOrder_customer_name').val(cus_data['name']);
                $('#SalesOrder_customer_address').val(cus_data['fullAddress']);
                $('#SalesOrder_customer_linkman').val(link_data['name']);
                
                data.id = cus_data['id'];
                data.name = cus_data['name'];
                data.linkman = link_data['name'];
                data.address = cus_data['fullAddress'];
                data.phone = link_data['phone'];
                populateCustomerData(data);
                $('#select-customer').dialog('close');
                //关闭弹出层
                $('#customer_popup .js-dialog-close').click();
            }
        }, 'json');
    return false;
}";
?>
    <div class="main-search-forms">
        <!-- 客户信息start -->
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
            </div>
            <div class="clearfix">
                <div class="cell span10">
                    <label><?php echo $form->labelEx($customer, 'name');?></label>
                    <div class="item">
                        <div class="main">
                            <?php echo $form->textField($customer, 'name', array('size' => '30')); ?>
                        </div>
                        <?php echo $form->error($customer,'name');?>
                    </div>
                </div>
                <div class="cell span10">
                    <label><?php echo $form->labelEx($customer, 'followman_id');?></label>
                    <div class="item">
                        <div class="main">
                            <?=$form->searchField($customer,'followman', array('placeholder' => "请输入人员姓名或拼音", 'class' => 'span5'));?>
                            <?=$form->hiddenField($customer,'followman_id');?>
                            <?=$form->error($customer, 'followman');?>
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
                            <?php echo $form->dropDownList($customer, 'city', array(''=>'请选择城市') + $customer->cityList, array('class' => 'city_value'));?>
                            </span>
                            <?php echo $form->textField($customer, 'address', array('style' => 'width:270px;', 'placeholder' => '请输入详细街道地址...')); ?>
                            <?php echo $form->error($customer,'province');?>
                            <?php echo $form->error($customer,'city');?>
                            <?php echo $form->error($customer,'address');?>
                     </div>
                </div>
            </div>
        </div>
        <!-- 客户信息end -->
        
        <!-- 联系人信息start -->
        <div class="clearfix">
            <div class="cell span10">
                <label><?php echo $form->labelEx($customer->linkman, '联系人姓名')?><span style='color:red; margin-left: 5px;'>*</span></label>
                <div class="item">
                    <div class="main">
                        <?php echo $form->textField($customer->linkman, 'name'); ?>
                        <?php echo $form->radioButtonList($customer->linkman, 'gender', array('1' => '先生', '2' => '女士'), array('separator' => ' '));?>
                    </div>
                    <?php echo $form->error($customer->linkman, 'name');?>
                    <?php echo $form->error($customer->linkman,'gender');?>
                </div>
            </div>
            <div class="cell span10">
                <label><?php echo $form->labelEx($customer->linkman, 'email');?></label>
                <div class="item">
                    <div class="main">
                        <?php echo $form->textField($customer->linkman, 'email');?>
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
            <div class="cell span10">
                <label><?php echo $form->labelEx($customer->linkman, 'fax');?></label>
                <div class="item">
                    <div class="main">
                       <?php echo $form->textField($customer->linkman, 'fax');?>
                    </div>
                </div>
            </div>
        </div>
        <div class="js-contact-box">
            <div class="clearfix js-mobile">
                <div class="cell span10">
                    <label><?php echo $form->labelEx($customer->linkman, 'mobile');?></label>
                    <div class="item">
                        <div class="main">
                            <?Php echo $form->textField($customer->linkman, 'mobile[]', array('class' => 'js-contact_repeat'));?>
                            <a href="javascript:;" class="js-add-mobile">添加</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="clearfix js-mobile" style="display:none;">
                <div class="cell">
                    <label >&nbsp;</label>
                    <div class="item span10">
                        <div class="main">
                            <?Php echo $form->textField($customer->linkman, 'mobile[]', array('class' => 'js-contact_repeat'));?> 
                            <a href="javascript:;" class="js-remove-mobile">删除</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="js-contact-box">
            <div class="clearfix js-phone">
                <div class="cell span10">
                    <label><?php echo $form->labelEx($customer->linkman, 'phone');?></label>
                    <div class="item">
                        <div class="main">
                            <?php echo $form->dropDownList($customer->linkman, "phone_type[]", $customer->linkman->phoneTypeList); ?> 
                            <?php echo $form->textField($customer->linkman, 'phone[]');?>
                            <a href="javascript:;" class="js-add-phone">添加</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="clearfix js-phone" style="display:none;">
                <div class="cell">
                    <label >&nbsp;</label>
                    <div class="item span10">
                        <div class="main">
                            <?php echo $form->dropDownList($customer->linkman, "phone_type[]", $customer->linkman->phoneTypeList); ?> 
                            <?php echo $form->textField($customer->linkman, 'phone[]');?>
                            <a href="javascript:;" class="js-remove-phone">删除</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- 联系人信息end -->
        
        <div class="actions" style='text-align: right; padding-right: 5px;'>
            <input name="submit_value" id="submitValue" type="hidden" />
            <button type="submit" class="highlight">确定</button>
            <button type="reset" class="js-dialog-close">取消</button>
        </div>
    </div>
<?php $this->endWidget();?>
<!-- 进销存新增客户页面end -->
    
<script>
$(function(){
    $("#Customer_followman_id").complete();
    
    $('#Customer').submit(function(){
        $.post(this.action, $(this).serialize(), function(json){
            if(json.status){
                var cus_data = json['cus_data'],
                    link_data = json['link_data'],
                    data = {};
                
                $('#SalesOrder_customer_id').val(cus_data['id']);
                $('#SalesOrder_customer_name').val(cus_data['name']);
                $('#SalesOrder_customer_address').val(cus_data['province'] + '-' + cus_data['city'] + '-' + cus_data['address']);
                $('#SalesOrder_customer_linkman').val(link_data['name']);
                
                data.id = cus_data['id'];
                data.name = cus_data['name'];
                data.linkman = link_data['name'];
                data.address = cus_data['province'] + '-' + cus_data['city'] + '-' + cus_data['address'];
                data.phone = link_data['phone'];
                populateCustomerData(data);
                $('#select-customer').dialog('close');
                //关闭弹出层
                $('.ui-widget-content').remove();
            }
        }, 'json');
        return false;
    });
});
</script>
