<div>
    <?php
    //编辑和复制流程
    if ($is_edit) {
        $order = 1;
        $node_str = '';
        foreach ($nodes as $node) {
            $list = WorkFlow::getAllIdsListByNodeId($node['id']);
            $role = implode(',', $list['role']);
            $user = implode(',', $list['user']);
            $department = implode(',', $list['department']);
            $department_manage = implode(',', $list['department_manage']);
            switch ($node['type']) {
                case 0://审批节点
                    $str = mb_strlen($node['name'], 'utf-8') > 20 ? mb_substr($node['name'], 0, 20, 'utf-8') . '...' : $node['name'];
                    $str .= '<br /> 点击修改';
                    $href = '<a href="javascript:void(0)" data-act="add">添加下一审批人</a><br/><a data-act="del" href="javascript:void(0)">删除此审批人</a>';
                    $html =
                            <<<EOF
    <div id="node_{$order}">
        <div class="rectangle bg_node" data-node='node' style="float:left;">
            <a href="javascript:void(0);" title='{$node['name']}'>{$str}</a>
        </div>
        <div class="notice">{$href}</div>
        <div class="arrows"></div>
        <input type="hidden" value="{$node['need_all_allowed']}" class="is_all" />
        <input type="hidden" value="{$role}" class="role" />
        <input type="hidden" value="{$node['name']}" class="node_name" />
        <input type="hidden" value="{$user}" class="user" />
        <input type="hidden" value="{$department}" class="department" />
        <input type="hidden" value="{$department_manage}" class="department_manage" />
    </div>
EOF;
                    $order++;
                    $node_str .= $html;
                    break;
                case 1://开始节点
                    if (!empty($node['name'])) {
                        $_tmp_str = mb_strlen($node['name'], 'utf-8') > 20 ? mb_substr($node['name'], 0, 20, 'utf-8') . '...' : $node['name'];
                        $str = '申请人（' . $_tmp_str . '）<br/> 点击修改';
                    } else {
                        $str = '点此，设置流程适用人';
                    }
                    $start_node_str =
                            <<<EOF
    <div id="start_node">
        <div class="rectangle bg" data-node='start' style="float:left;">
            <a href="javascript:void(0);" title='{$node['name']}'>{$str}</a>
        </div>
        <div class="notice">在此设置哪些人员发布申请时使用此流程,不操作则默认全体人员可使用此流程</div>
        <div class="arrows"></div>
        <input type="hidden" value="{$role}" class="role" />
        <input type="hidden" value="{$node['name']}" class="node_name" />
        <input type="hidden" value="{$user}" class="user" />
        <input type="hidden" value="{$department}" class="department" />
    </div>
EOF;
                    break;
                case 2://结束节点
                    if (!empty($node['name'])) {
                        $_tmp_str = mb_strlen($node['name'], 'utf-8') > 20 ? mb_substr($node['name'], 0, 20, 'utf-8') . '...' : $node['name'];
                        $str = '审批完成后通知：（' . $_tmp_str . '）<br/> 点击修改';
                    } else {
                        $str = '点此，设置审批完成后需通知的人员';
                    }
                    $end_node_str =
                            <<<EOF
    <div id="end_node" style="height:54px;">
        <div class="rectangle bg" data-node='end' style="float:left;">
            <a href="javascript:void(0);" title='{$node['name']}'>{$str}</a>
        </div>
        <div class="notice">在此设置审批完成后需通知哪些人员，如请假申请完成后通知前台,以便进行考勤统计（默认会通知申请发布人）</div>
        <input type="hidden" value="{$role}" class="role" />
        <input type="hidden" value="{$node['name']}" class="node_name" />
        <input type="hidden" value="{$user}" class="user" />
        <input type="hidden" value="{$department}" class="department" />
        <!-- 屏蔽完成节点短信提醒 
        <input type="hidden" value="" class="sms_notice" />
        -->
    </div>
EOF;
                    break;
            }
        }
        //如果节点为空 默认添加第一个节点 复制流程时使用
        if (empty($node_str)) {
            $node_str =
                    <<<EOF
    <div id="node_1">
        <div class="rectangle bg_node" data-node='node' style="float:left;">
        <a href="javascript:void(0);">点此，添加第1审批人</a>
        </div>
        <div class="notice">在此设置此流程的第1个审批人</div>
        <div class="arrows"></div>
        <input type="hidden" value="" class="role" />
        <input type="hidden" value="" class="user" />
        <input type="hidden" value="" class="department" />
        <input type="hidden" value="" class="is_all" />
        <input type="hidden" value="" class="node_name" />
        <input type="hidden" value="" class="department_manage" />
    </div>
EOF;
        }
        //如果节点为空 默认添加第一个节点 复制流程时使用
        if (empty($end_node_str)) {
            $end_node_str =
                    <<<EOF
            <div id="end_node" style="height:54px;">
            <div class="rectangle bg" data-node='end' style="float:left;">
                <a href="javascript:void(0);">点此，设置审批完成后需通知的人员</a>
            </div>
            <div class="notice">在此设置审批完成后需通知哪些人员，如请假申请完成后通知前台,以便进行考勤统计（默认会通知申请发布人）</div>
                <input type="hidden" value="" class="role" />
                <input type="hidden" value="" class="user" />
                <input type="hidden" value="" class="department" />
                <input type="hidden" value="" class="node_name" />
                <!-- 屏蔽完成节点短信提醒 
                <input type="hidden" value="" class="sms_notice" />
                -->
            </div>
EOF;
        }
        echo $start_node_str . $node_str . $end_node_str;
        ?>

        <?php
    } else {//新增流程
        ?>
        <div id="start_node">
            <div class="rectangle bg" data-node='start' style="float:left;">
                <a href="javascript:void(0);">申请人（员工...）<br> 点击修改</a>
            </div>
            <div class="notice">在此设置哪些人员发布申请时使用此流程,不操作则默认全体人员可使用此流程</div>
            <div class="arrows"></div>
            <input type="hidden" value="2" class="role" />
            <input type="hidden" value="员工" class="node_name" />
            <input type="hidden" value="" class="user" />
            <input type="hidden" value="" class="department" />
        </div>

        <div id="node_1">
            <div class="rectangle bg_node" data-node='node' style="float:left;">
                <a href="javascript:void(0);">点此，添加第1审批人</a>
            </div>
            <div class="notice">在此设置此流程的第1个审批人</div>
            <div class="arrows"></div>
            <input type="hidden" value="" class="role" />
            <input type="hidden" value="" class="user" />
            <input type="hidden" value="" class="department_manage" />
            <input type="hidden" value="" class="department" />
            <input type="hidden" value="" class="is_all" />
            <input type="hidden" value="" class="node_name" />
        </div>

        <div id="end_node" style="height:54px;">
            <div class="rectangle bg" data-node='end' style="float:left;">
                <a href="javascript:void(0);">点此，设置审批完成后需通知的人员</a>
            </div>
            <div class="notice">在此设置审批完成后需通知哪些人员，如请假申请完成后通知前台,以便进行考勤统计（默认会通知申请发布人）</div>
            <input type="hidden" value="" class="role" />
            <input type="hidden" value="" class="user" />
            <input type="hidden" value="" class="department" />
            <input type="hidden" value="" class="node_name" />
            <!-- 屏蔽完成节点短信提醒 
            <input type="hidden" value="" class="sms_notice" />
            -->
        </div>
        <?php
    }
    ?>
</div>
