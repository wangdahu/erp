<?php
$this->widget('zii.widgets.CMenu', array('htmlOptions' => array('class' => 'bigTab radius-top'), 'items' => array(
    array('label' => '应收', 'url' => array('/pss/finance/receive')),
    array('label' => '应付', 'url' => array('/pss/finance/pay')),
    array('label' => '收支报表', 'url' => array('/pss/finance/billing')),
)));

