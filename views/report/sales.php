<?php
$params = isset($_GET['type']) ? array('type' => $_GET['type']) : array();

$selectMenu = array(
    array('label' => '销售总表', 'url' => array('/pss/report/sales') + $params, 'active' => true),
);
?>
<div id="page_body">

    <div id="page_title">
        <div id="page_breadcrumbs">
            当前位置：
            <?php
            $this->widget('zii.widgets.CBreadcrumbs', array(
                'links' => $this->breadcrumbs
            ));
            ?>
        </div>
    </div>

    <div class="main-box">
        <div class="main-body">
            <aside class="span5">
                <h1 class="sidebar-caption">报表统计</h1>
                <?php
                $this->widget('zii.widgets.CMenu', array('items' => $selectMenu, 'activeCssClass' => 'selected', 'htmlOptions' => array(
                        'class' => 'left-menu',
                        )));
                ?>
            </aside>

            <div class="prepend5 main-container">
                <div class="main-content">
                    <div class="main-title"><strong>销售总表</strong></div>
                    <div class="main-panel">统计条件</div>
                    
                    <div class="wide form">
                    <?php $form=$this->beginWidget('CActiveForm', array(
                    	'action'=>Yii::app()->createUrl($this->route),
                    	'method'=>'get',
                    )); ?>
                        <div class="clearfix">
                            <div class="cell span9">
                                <?php echo $form->label($search, 'type');?>
                                <div class="item">
                                    <div class="main">
                                        <?php echo $form->dropDownList($search, 'type', array('user' => '按人员', 'customer' => '按客户', 'product' => '按产品')); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix">
                            <div class="cell">
                                <?php echo $form->label($search, 'date_pattern');?>
                                <div class="item">
                                    <div class="main">
                                        <?php $dates = array('时间范围');
                                        $dates[] = $form->textField($search,'start_date', array('class' => 'js-datepicker', 'data-group' => 'report_date', 'data-type' => 'start'));
                                        $dates[] = $form->textField($search,'end_date', array('class' => 'js-datepicker', 'data-group' => 'report_date'));
                                        ?>
                                        <?php echo $form->radioButtonList($search,'date_pattern', array('今天', '本周', '本月', implode($dates, ' ')), array('separator'=>'&nbsp;')); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix">
                            <div class="cell span7">
                                <?php echo $form->label($search, 'view');?>
                                <div class="item">
                                    <div class="main">
                                        <?php echo $form->radioButtonList($search,'view', array('list'=>'列表','bargraph'=>'柱状图', 'piegraph'=>'饼状图'), array('separator'=>'&nbsp;')); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="cell span2">
                    		<?php echo CHtml::htmlButton('统计', array('type'=>'submit')); ?>
                    		</div>
                        </div>
                    <?php $this->endWidget(); ?>
                    </div>
                    
                    <?php 
                    $dataProvider = $model->search();
                    $dataProvider->setPagination(false);
                    $this->renderPartial("_{$search->view}", array('search' => $search, 'dataProvider' => $dataProvider));
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

