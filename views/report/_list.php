
<?php
$columns = array(
    'customer' => array(
        array('name'=>'name', 'value'=>'$data->name'),
        array('name'=>'salesOrderCount', 'value'=>'$data->salesOrderCount'),
        array('name'=>'salesTotalPrice', 'value'=>'$data->salesTotalPrice'),
        array('header'=>'已收金额', 'value'=>'$data->receivedPrice'),
        array('header'=>'未收金额', 'value'=>'$data->notReceivedPrice'),
    ),
    'user' => array(
        array('header'=>'业务员', 'value'=>'$data->name'),
        //array('name'=>'department.name', 'value'=>'$data->department->name'),
        array('header'=>'销售单数', 'value'=>'$data->salesOrderCount'),
        array('header'=>'应收金额', 'value'=>'$data->salesTotalPrice'),
        array('header'=>'已收金额', 'value'=>'$data->receivedPrice'),
        array('header'=>'未收金额', 'value'=>'$data->notReceivedPrice'),
    ),
    'product' => array(
        array('name'=>'name', 'value'=>'$data->name'),
        array('header'=>'销售单数', 'value'=>'$data->salesOrderCount'),
        array('header'=>'销售数量', 'value'=>'$data->salesQuantity'),
        array('header'=>'销售金额', 'value'=>'$data->salesTotalPrice'),
        array('header'=>'采购单数', 'value'=>'$data->buyOrderCount'),
        array('header'=>'采购数量', 'value'=>'$data->buyQuantity'),
        array('header'=>'采购金额', 'value'=>'$data->buyTotalPrice'),
    ),
    'department' => null,
);

$this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'report',
    'dataProvider'=> $dataProvider,
    'emptyText'=>'无销售记录',
    'columns'=>$columns[$search->type],
));
?>

