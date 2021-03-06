<?php
Yii::app()->clientScript->registerScriptfile($this->module->assetsUrl . '/js/city.js');
Yii::app()->clientScript->registerScriptfile($this->module->assetsUrl . '/js/row.js');
Yii::app()->clientScript->registerScriptfile('/source/oa/js/autocomplete.js');
?>
<!-- 进销存新增供应商页面start -->
<?php $form = $this->beginWidget('ActiveForm', array('id' => 'supplier_popup'));

$form->clientOptions['afterValidate'] = "js:function(form, data, hasError) {
    $.post(form.attr('action'), form.serialize(), function(json){
        if(json.status){
            var sup_data = json['sup_data'],
                link_data = json['link_data'];
            $('#BuyOrder_supplier_id').val(sup_data['id']);
            $('#BuyOrder_supplier_name').val(sup_data['name']);
            $('#BuyOrder_supplier_address').val(sup_data['fullAddress']);
            $('#BuyOrder_supplier_linkman').val(link_data['phone']);
            
            var data = {}, items = $(this).children();
            data.id = sup_data['id'];
            data.name = sup_data['name'];
            data.linkman = link_data['name'];
            data.address = sup_data['fullAddress'];
            data.phone = link_data['phone'];
            populateSupplierData(data);
            //关闭弹出层
            $('#supplier_popup .js-dialog-close').click();
        }
    }, 'json');
    return false;
}";
?>
    <div class="main-search-forms">
        <!-- 供应商信息start -->
        <div style='border-bottom: 2px solid #C0C0C0; padding-left: 10px; padding-bottom: 5px; margin: 15px 10px 15px 10px;'>
        	<label style='font-weight: bold; font-size: 16px;'>供应商信息</label>
        </div>
        <div class="more-info" style="display: block;">
            <div class="clearfix">
                <div class="cell span10">
                    <label for="CrmContact_type"><?php echo $form->labelEx($supplier, 'name');?></label>
                    <div class="item">
                        <div class="main">
                            <?php echo $form->textField($supplier, 'name', array("style" => "width: 250px;")); ?>
                        </div>
                        <?php echo $form->error($supplier,'name');?>
                    </div>
                </div>
                <div class="cell span10">
                    <label><?php echo $form->labelEx($supplier, 'followman_id');?></label>
                    <div class="item">
                        <div class="main">
                            <?=$form->searchField($supplier,'followman', array('placeholder' => "请输入人员姓名或拼音", 'class' => 'span5'));?>
                            <?=$form->hiddenField($supplier,'followman_id');?>
                        </div>
                        <?php echo $form->error($supplier, 'followman');?>
                    </div>
                </div>
            </div>
            <div class="clearfix">
                <div class="cell span10">
                    <label><?php echo $form->labelEx($supplier, 'business');?></label>
                    <div class="item">
                        <div class="main">
                            <?php echo $form->textField($supplier, 'business', array("style" => "width: 250px;")); ?>
                        </div>
                        <?php echo $form->error($supplier,'business');?>
                    </div>
                </div>
            </div>
        </div>
        <div class="clearfix">
            <div class="cell">
                <label><?php echo $form->labelEx($supplier, 'address');?></label>
                <div class="item">
                    <div class="main">
                            <?php echo $form->dropDownList($supplier, 'country', $supplier->countryList, array('class' => 'country_blur'));?>
                            <span class="for-china">
                            <?php echo $form->dropDownList($supplier, 'province', $supplier->provinceList, array('class' => 'province_blur'));?>
                            <?php echo $form->dropDownList($supplier, 'city', $supplier->cityList, array('class' => 'city_value'));?>
                            </span>
                            <?php echo $form->textField($supplier, 'address', array('style' => 'width:280px;', 'placeholder' => '请输入详细街道地址...')); ?>
                            <?php echo $form->error($supplier, 'address');?>
                     </div>
                </div>
            </div>
        </div>
        <!-- 供应商信息end -->
        
        <!-- 联系人信息start -->
        <div style='border-bottom: 2px solid #C0C0C0; padding-left: 10px; padding-bottom: 5px; margin: 10px 10px 15px 10px;'>
        	<label style='font-weight: bold; font-size: 16px;'>联系人信息</label>
        </div>
        <div class="clearfix">
            <div class="cell span10">
                <?php echo $form->labelEx($supplier->linkman, 'name')?>
                <div class="item">
                    <div class="main">
                        <?php echo $form->textField($supplier->linkman, 'name'); ?>
                        <?php echo $form->radioButtonList($supplier->linkman, 'gender', array('1' => '先生', '2' => '女士'), array('separator' => ' '));?>
                    </div>
                    <?php echo $form->error($supplier->linkman,'name');?>
                    <?php echo $form->error($supplier->linkman,'gender');?>
                </div>
            </div>
            <div class="cell span10">
                <label><?php echo $form->labelEx($supplier->linkman, 'department');?></label>
                <div class="item">
                    <div class="main">
                       <?php echo $form->textField($supplier->linkman, 'department'); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="clearfix">
            <div class="cell span10">
                <label><?php echo $form->labelEx($supplier->linkman, 'post');?></label>
                <div class="item">
                    <div class="main">
                        <?php echo $form->textField($supplier->linkman, 'post');?>
                    </div>
                </div>
            </div>
            <div class="cell span10">
                <label><?php echo $form->labelEx($supplier->linkman, 'in_no');?></label>
                <div class="item">
                    <div class="main">
                       <?php echo $form->textField($supplier->linkman, 'in_no');?>
                    </div>
                </div>
            </div>
        </div>
        <div class="clearfix">
            <div class="cell span10">
                <label><?php echo $form->labelEx($supplier->linkman, 'email');?></label>
                <div class="item">
                    <div class="main">
                        <?php echo $form->textField($supplier->linkman, 'email');?>
                    </div>
                </div>
            </div>
            <div class="cell span10">
                <label><?php echo $form->labelEx($supplier->linkman, 'fax');?></label>
                <div class="item">
                    <div class="main">
                       <?php echo $form->textField($supplier->linkman, 'fax');?>
                    </div>
                </div>
            </div>
        </div>
        <div class="clearfix">
            <div class="cell span10">
                <label><?php echo $form->labelEx($supplier->linkman, 'im_no');?></label>
                <div class="item">
                    <div class="main">
                        <?php echo $form->textField($supplier->linkman, 'im_no');?>
                    </div>
                </div>
            </div>
        </div>
        <div class="js-contact-box">
            <div class="clearfix js-mobile">
                <div class="cell span10">
                    <label><?php echo $form->labelEx($supplier->linkman, 'mobile');?></label>
                    <div class="item">
                        <div class="main">
                            <?Php echo $form->textField($supplier->linkman, 'mobile[]', array('class' => 'js-contact_repeat'));?>
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
                            <?Php echo $form->textField($supplier->linkman, 'mobile[]', array('class' => 'js-contact_repeat'));?> 
                            <a href="javascript:;" class="js-remove-mobile">删除</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="js-contact-box">
            <div class="clearfix js-phone">
                <div class="cell span10">
                    <label><?php echo $form->labelEx($supplier->linkman, 'phone');?></label>
                    <div class="item">
                        <div class="main">
                            <?php echo $form->dropDownList($supplier->linkman, "phone_type[]", $supplier->linkman->phoneTypeList); ?> 
                            <?php echo $form->textField($supplier->linkman, 'phone[]');?>
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
                            <?php echo $form->dropDownList($supplier->linkman, "phone_type[]", $supplier->linkman->phoneTypeList); ?> 
                            <?php echo $form->textField($supplier->linkman, 'phone[]');?>
                            <a href="javascript:;" class="js-remove-phone">删除</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="clearfix">
            <div class="cell">
                <label><?php echo $form->labelEx($supplier->linkman, 'remark');?></label>
                <div class="item">
                    <div class="main">
                    	<?php echo $form->textArea($supplier->linkman, 'remark', array('rows' => 4, 'cols' => '90'))?>
                    </div>
                </div>
            </div>
        </div>
        <!-- 联系人信息end -->
        
        <div class="actions">
            <input name="submit_value" id="submitValue" type="hidden" />
            <button type="submit" class="highlight" onclick="$('#submitValue').val('1')" >保存</button>
            <button type="reset" class="js-dialog-close">取消</button>
        </div>
    </div>
<?php $this->endWidget();?>
<!-- 进销存新增供应商页面end -->
