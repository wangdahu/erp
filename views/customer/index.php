<?php 
Yii::app()->clientScript->registerCssfile($this->module->assetsUrl . '/pss.css');
?>
    <div class="main-title-big  radius-top">
    	<h3>客户管理</h3>
    </div>
    
    <!-- 搜索条件start -->
    <div class="main-search-form">
        <?$this->renderPartial('_search', array('customer' => $customer, 'params' => $customer->attributes));?>
    </div>
    <!-- 搜索条件end -->
    
    <!-- 列表信息start -->
    <div class="main-panel">
    <?php
    if (PssPrivilege::customerCheck(PssPrivilege::CUSTOMER_CREATE)){
        echo Html5::link('新添客户', array('/pss/customer/create'), array('class' => 'button highlight'));
    }
    if (PssPrivilege::customerCheck(PssPrivilege::CUSTOMER_ADMIN)){
        echo Html5::link('删除', array('/pss/customer/delete'), array('id' => 'customer_delete', 'class' => 'button'));
    }
    ?>
    </div>
    
    <?php 
        $this->widget('zii.widgets.grid.CGridView', array(
            'id' => 'customer-grid',
            'dataProvider' => $customer->search(),
            'emptyText' => '暂无客户信息！',
            'columns'=>array(
                array('class' => 'CCheckBoxColumn', 'selectableRows' => 2, 'checkBoxHtmlOptions' => array('name' => 'id[]'), 'headerHtmlOptions' => array('class' => 'span1')),
                array('name' => 'name', 'type' => 'raw', 'value' => 'CHtml::link("$data->name", array("update", "id" => $data->id))', 'headerHtmlOptions' => array('class' => 'span4')),
                array('name' => 'linkman.name', 'value' =>'$data->linkman ? $data->linkman->name : ""', 'headerHtmlOptions' => array('class' => 'span2')),
                array('name' => 'fullAddress', 'value' => '$data->fullAddress'),
                array('name' => 'linkman.mobile', 'type' => 'ntext', 'value' => '$data->linkman ? $data->linkman->showMobiles : ""', 'headerHtmlOptions' => array('class' => 'span3')),
                array('name' => 'linkman.phone', 'type' => 'ntext', 'value' => '$data->linkman ? $data->linkman->showPhones : ""', 'headerHtmlOptions' => array('class' => 'span4')),
                array('name' => 'followman_id', 'value' => '$data->followUser->name', 'headerHtmlOptions' => array('class' => 'span2')),
                array('name' => 'created', 'value' => '$data->created', 'type' => 'datetime', 'headerHtmlOptions' => array('class' => 'span3')),
            ),
        ));
        ?>
    <!-- 列表信息end -->
    
    <script>
        $(function() {
            //展开/收起详细条件搜索
            $('.search-button').click(function(){
                $('.more-info').toggle();
                $('#isShow').val($('.more-info:visible').length);
            });
            
            //搜索联系人
            $(".search-form form").submit(function(){
                $.fn.yiiGridView.update('contact-grid', { data: $(this).serialize()} );
                return false;
            });
            
            //获取选中的复选框
            function getChecked() {
                return $("#customer-grid :checked").serialize();
            }
            
            //删除客户
            $("#customer_delete").click(function(){
                var ids = getChecked();
                if (!ids.length){
                    $.alert("请选择要删除的客户");
                    return false;
                }
                $.confirm("您确认要删除所选客户? ", function(){
                    $.post("<?=$this->createUrl("/pss/customer/delete")?>", ids, function(json){
                        if(json.status == 1){
                            location.reload();
                        }else{
                            $.alert("操作失败");
                            return false;
                        }
                    }, 'json');
                });
                return false;
            });
        });
    </script>
