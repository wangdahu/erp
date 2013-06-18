<?php $this->beginContent('//layouts/column1'); ?>
<div id="page_body">
    <div id="page_title">
        <div id="page_breadcrumbs">当前位置：<?php $this->widget('zii.widgets.CBreadcrumbs', array('links' => $this->breadcrumbs));?></div>
        &nbsp;&nbsp;<?=CHtml::htmlButton('返回', array('onclick' => 'history.back()'));?>
    </div>
    <div class="simple-box radius" style="min-height: 600px;">
    <?php echo $content; ?>
    </div>
</div>
<?php Yii::app()->clientScript->registerCssfile($this->module->assetsUrl . '/pss.css');?>
<?php Yii::app()->clientScript->registerScriptFile($this->module->assetsUrl . '/pss.js');?>
<?php $this->endContent(); ?>
<script>
(function() {
    var els = $('.js-complete');
    if(els.length) {
        $.getScript('/source/oa/js/autocomplete.js', function() {
            els.complete();
        });
    }
})();
</script>
<style>
.yiiTab ul.tabs { padding: 48px 0 3px 0; margin: 0; border-bottom: 1px solid #4F81BD; font: bold 14px Verdana, sans-serif; }
.yiiTab ul.tabs li { list-style: none; margin: -32px 0 0 0; padding:10px; display: inline; }
.yiiTab ul.tabs a { padding: 8px 1.5em; border: 1px solid #4F81BD; border-bottom: none; background: #d3dfee; text-decoration: none; }
.yiiTab ul.tabs a:link { color: #667; }
.yiiTab ul.tabs a:visited { color: #667; }
.yiiTab ul.tabs a:hover { color: #000; background: #E6F2FF; border-color: #227; }
.yiiTab ul.tabs a.active { background: white; border-bottom: 1px solid white; }
.yiiTab div.view { border-left: none; border-right: none; border-bottom: none; padding: 8px; margin: 0; }
</style>
