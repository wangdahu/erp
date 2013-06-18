<?php if ($approve_list): ?>
    <table class="home">
        <thead>
            <tr>
                <th class="span3">单据类型</th>
                <th>单号</th>
                <th class="span2">发布人</th>
                <th class="span3">发布时间</th>
            </tr>
        </thead>
        
        <tbody>
            <?php foreach ($approve_list as $list): 
                $types= FormFlow::getLeftMenuList();
            ?>
                    <td><?php echo $types[$list['form_name']];?></td>
                    <td><?php echo CHtml::link($list['no'], Yii::app()->createUrl(FormFlow::noticeLink($list['form_name'], $list['task_id']))); ?></td>
                    <td><?php echo Account::user($list['user_id'])->name; ?></td>
                    <td class="gray"><?php echo Yii::app()->format->datetime($list['created']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <div class="no-content">暂无进销存事务审批</div>
<?php endif; ?>