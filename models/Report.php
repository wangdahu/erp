<?php

class Report extends CFormModel{
    
    public $type;
    
    public $date_pattern, $start_date, $end_date;
    
    public $view;
    
    public function rules(){
        return array(
            array('type, date_pattern, start_date, end_date, view', 'safe'),
        );
    }
    
    
    public function attributeLabels(){
        return array(
            'type' => '统计方式',
            'date_pattern' => '统计时间',
            'view' => '显示方式',
            'user' => '选择人员',
        );
    }
}

