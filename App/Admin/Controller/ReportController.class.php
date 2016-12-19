<?php

namespace Admin\Controller;

use Admin\Controller\CommonController;

class ReportController extends CommonController {
    /*     * *
     *
     * 报告管理
     */

    public function index() {

        $member_table = M('member');
        $bonus_table = M('bonus');
        if (!empty($_REQUEST['search_starttime']) && !empty($_REQUEST['search_endtime'])) {
            $startime = strtotime($_REQUEST['search_starttime']);
            $endtime = strtotime($_REQUEST['search_endtime']);

            if ($startime <= $endtime) {
                $times = (strtotime($_REQUEST['search_starttime'] . '00:00:00') . ',' . strtotime($_REQUEST['search_endtime'] . '23:59:59'));
                $search['search_starttime'] = $_REQUEST['search_starttime'];
                $search['search_endtime'] = $_REQUEST['search_endtime'];
            } else {
                $times = (strtotime($_REQUEST['search_endtime'] . '00:00:00') . ',' . strtotime($_REQUEST['search_starttime'] . '23:59:59'));
                $search['search_starttime'] = $_REQUEST['search_endtime'];
                $search['search_endtime'] = $_REQUEST['search_starttime'];
            }
            $map['create_date'] = array('between', $times);
            //$timespan = strtotime(urlderode($_REQUEST['start_time'])) . "," . strtotime(urlderode($_REQUEST['end_time']));
        } elseif (!empty($_REQUEST['search_starttime'])) {
            $xtime = strtotime($_REQUEST['search_starttime'] . '00:00:00');
            $map['create_date'] = array("egt", $xtime);
            $search['search_starttime'] = $_REQUEST['search_starttime'];
        } elseif (!empty($_REQUEST['search_endtime'])) {
            $xtime = strtotime($_REQUEST['search_endtime'] . '23:59:59');
            $map['create_date'] = array("elt", $xtime);
            $search['search_endtime'] = $_REQUEST['search_endtime'];
        }
        if (!empty($_REQUEST['search_status'])) {
            $map['status'] = $_REQUEST['search_status'];
            $search['search_status'] = $_REQUEST['search_status'];
        }
        if (!empty($_REQUEST['search_type'])) {

            $map['type'] = $_REQUEST['search_type'];
            $search['search_type'] = $_REQUEST['search_type'];
        }

        if (!empty($_REQUEST['search_username'])) {
            $info = $member_table->field('id')->where(array('username' => trim($_REQUEST['search_username'], " ")))->find();
            $map['uid'] = $info['id'];
            $search['search_username'] = $_REQUEST['search_username'];
        }

        $type = getbonustype();
        $status = array('1' => '收入', '2' => '支出');

        $count = $bonus_table->where($map)->count(); // 查询满足要求的总记录数
        $Page = new \Think\Page($count, 50); // 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show();
        $list = $bonus_table->order('id desc')->where($map)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $bcount = count($list);
        for ($i = 0; $i < $bcount; $i++) {

            if (!empty($list[$i]['fid'])) {
                $frominfo = $member_table->field('username')->find($list[$i]['fid']);
                $list[$i]['fromname'] = $frominfo['username'];
            } else {
                $list[$i]['fromname'] = '无';
            }
            $userinfo = $member_table->field('username')->find($list[$i]['uid']);
            $list[$i]['username'] = $userinfo['username'];
        }

        $this->assign('type', $type);
        $this->assign('status', $status);
        $this->assign('list', $list);
        $this->assign('count', $count);
        $this->assign('page', $show);
        $this->assign('arr', $search);
        $this->display();
    }

    public function recharge() {

        $member_table = M('member');
        $recharge_table = M('recharge');
        if (!empty($_REQUEST['search_starttime']) && !empty($_REQUEST['search_endtime'])) {
            $startime = strtotime($_REQUEST['search_starttime']);
            $endtime = strtotime($_REQUEST['search_endtime']);

            if ($startime <= $endtime) {
                $times = (strtotime($_REQUEST['search_starttime'] . '00:00:00') . ',' . strtotime($_REQUEST['search_endtime'] . '23:59:59'));
                $search['search_starttime'] = $_REQUEST['search_starttime'];
                $search['search_endtime'] = $_REQUEST['search_endtime'];
            } else {
                $times = (strtotime($_REQUEST['search_endtime'] . '00:00:00') . ',' . strtotime($_REQUEST['search_starttime'] . '23:59:59'));
                $search['search_starttime'] = $_REQUEST['search_endtime'];
                $search['search_endtime'] = $_REQUEST['search_starttime'];
            }
            $map['create_date'] = array('between', $times);
            //$timespan = strtotime(urlderode($_REQUEST['start_time'])) . "," . strtotime(urlderode($_REQUEST['end_time']));
        } elseif (!empty($_REQUEST['search_starttime'])) {
            $xtime = strtotime($_REQUEST['search_starttime'] . '00:00:00');
            $map['create_date'] = array("egt", $xtime);
            $search['search_starttime'] = $_REQUEST['search_starttime'];
        } elseif (!empty($_REQUEST['search_endtime'])) {
            $xtime = strtotime($_REQUEST['search_endtime'] . '23:59:59');
            $map['create_date'] = array("elt", $xtime);
            $search['search_endtime'] = $_REQUEST['search_endtime'];
        }
        if (!empty($_REQUEST['search_status'])) {
            $map['status'] = $_REQUEST['search_status'];
            $search['search_status'] = $_REQUEST['search_status'];
        }
        if (!empty($_REQUEST['search_type'])) {

            $map['type'] = $_REQUEST['search_type'];
            $search['search_type'] = $_REQUEST['search_type'];
        }
        if (!empty($_REQUEST['search_username'])) {

            $info = $member_table->field('id')->where(array('username' => trim($_REQUEST['search_username'], " ")))->find();
            $map['user_id'] = $info['id'];
            $search['search_username'] = $_REQUEST['search_username'];
        }

        $type = getbonustype();
        $status = array('1' => '充值', '2' => '扣除');
        $count = $recharge_table->where($map)->count(); // 查询满足要求的总记录数
        $Page = new \Think\Page($count, 50); // 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show();
        $list = $recharge_table->order('id desc')->where($map)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $rcount = count($list);
        for ($i = 0; $i < $rcount; $i++) {
            $userinfo = $member_table->field('username,name,mobile')->find($list[$i]['uid']);
            $list[$i]['username'] = $userinfo['username'];
            $list[$i]['name'] = $userinfo['name'];
            $list[$i]['mobile'] = $userinfo['mobile'];
            $admininfo = M('admin')->field('username')->find($list[$i]['hid']);
            $list[$i]['adminname'] = $admininfo['username'];
        }
        $this->assign('status', $status);
        $this->assign('type', $type);
        $this->assign('list', $list);
        $this->assign('count', $count);
        $this->assign('page', $show);
        $this->assign('arr', $search);
        $this->display();
    }

    public function tuanduiyejitongji() {
        $member_table = M('member');
        $total_table = M('total');
        $memberlevel_table = M('memberlevel');
        if (!empty($_REQUEST['search_starttime']) && !empty($_REQUEST['search_endtime'])) {
            $startime = strtotime($_REQUEST['search_starttime']);
            $endtime = strtotime($_REQUEST['search_endtime']);

            if ($startime <= $endtime) {
                $times = (strtotime($_REQUEST['search_starttime'] . '00:00:00') . ',' . strtotime($_REQUEST['search_endtime'] . '23:59:59'));
                $search['search_starttime'] = $_REQUEST['search_starttime'];
                $search['search_endtime'] = $_REQUEST['search_endtime'];
            } else {
                $times = (strtotime($_REQUEST['search_endtime'] . '00:00:00') . ',' . strtotime($_REQUEST['search_starttime'] . '23:59:59'));
                $search['search_starttime'] = $_REQUEST['search_endtime'];
                $search['search_endtime'] = $_REQUEST['search_starttime'];
            }
            $map['create_date'] = array('between', $times);
            //$timespan = strtotime(urlderode($_REQUEST['start_time'])) . "," . strtotime(urlderode($_REQUEST['end_time']));
        } elseif (!empty($_REQUEST['search_starttime'])) {
            $xtime = strtotime($_REQUEST['search_starttime'] . '00:00:00');
            $map['create_date'] = array("egt", $xtime);
            $search['search_starttime'] = $_REQUEST['search_starttime'];
        } elseif (!empty($_REQUEST['search_endtime'])) {
            $xtime = strtotime($_REQUEST['search_endtime'] . '23:59:59');
            $map['create_date'] = array("elt", $xtime);
            $search['search_endtime'] = $_REQUEST['search_endtime'];
        }

        if (!empty($_REQUEST['search_username'])) {

            $info = $member_table->field('id')->where(array('username' => trim($_REQUEST['search_username'], " ")))->find();
            $map['uid'] = $info['id'];
            $search['search_username'] = $_REQUEST['search_username'];
        }


        $count = $total_table->where($map)->count(); // 查询满足要求的总记录数
        $Page = new \Think\Page($count, 50); // 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show();
        $list = $total_table->order('id desc')->where($map)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $rcount = count($list);
        for ($i = 0; $i < $rcount; $i++) {
            $userinfo = $member_table->field('username,name,mobile')->find($list[$i]['uid']);
            $levelinfo = $memberlevel_table->field('title')->find($list[$i]['level']);
            $list[$i]['username'] = $userinfo['username'];
            $list[$i]['name'] = $userinfo['name'];
            $list[$i]['level'] = $levelinfo['title'];
        }

        $this->assign('list', $list);
        $this->assign('count', $count);
        $this->assign('page', $show);
        $this->assign('arr', $search);
        $this->display();
    }

    //审核汇款充值
    public function listshenhe() {
        $member_table = M('member');
        $remittance_table = M('remittance');
        $map['status'] = 1;
        if (!empty($_REQUEST['search_starttime']) && !empty($_REQUEST['search_endtime'])) {
            $startime = strtotime($_REQUEST['search_starttime']);
            $endtime = strtotime($_REQUEST['search_endtime']);

            if ($startime <= $endtime) {
                $times = (strtotime($_REQUEST['search_starttime'] . '00:00:00') . ',' . strtotime($_REQUEST['search_endtime'] . '23:59:59'));
                $search['search_starttime'] = $_REQUEST['search_starttime'];
                $search['search_endtime'] = $_REQUEST['search_endtime'];
            } else {
                $times = (strtotime($_REQUEST['search_endtime'] . '00:00:00') . ',' . strtotime($_REQUEST['search_starttime'] . '23:59:59'));
                $search['search_starttime'] = $_REQUEST['search_endtime'];
                $search['search_endtime'] = $_REQUEST['search_starttime'];
            }
            $map['create_date'] = array('between', $times);
            //$timespan = strtotime(urldecode($_REQUEST['start_time'])) . "," . strtotime(urldecode($_REQUEST['end_time']));
        } elseif (!empty($_REQUEST['search_starttime'])) {
            $xtime = strtotime($_REQUEST['search_starttime'] . '00:00:00');
            $map['create_date'] = array("egt", $xtime);
            $search['search_starttime'] = $_REQUEST['search_starttime'];
        } elseif (!empty($_REQUEST['search_endtime'])) {
            $xtime = strtotime($_REQUEST['search_endtime'] . '23:59:59');
            $map['create_date'] = array("elt", $xtime);
            $search['search_endtime'] = $_REQUEST['search_endtime'];
        }
        if (!empty($_REQUEST['search_username'])) {
            $idInfo = $member_table->field('id')->where(array('username' => $_REQUEST['search_username']))->find();
            $map['uid'] = $idInfo['id'];
            $search['search_username'] = $_REQUEST['search_username'];
        }
        $count = $remittance_table->where($map)->count(); // 查询满足要求的总记录数
        $Page = new \Think\Page($count, 50); // 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show();
        $list = $remittance_table->order('id desc')->where($map)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        for ($i = 0; $i < $count; $i++) {
            $userInfo = $member_table->field('username')->find($list[$i]['uid']);
            $list[$i]['username'] = $userInfo['username'];
        }
        $this->assign('count', $count);
        $this->assign('page', $show);
        $this->assign('list', $list);
        $this->assign('arr', $search);
        $this->display();
    }

    //确认汇款充值
    public function listqueren() {
        $member_table = M('member');
        $remittance_table = M('remittance');
        $map['status'] = array('in', '2,3');
        if (!empty($_REQUEST['search_starttime']) && !empty($_REQUEST['search_endtime'])) {
            $startime = strtotime($_REQUEST['search_starttime']);
            $endtime = strtotime($_REQUEST['search_endtime']);

            if ($startime <= $endtime) {
                $times = (strtotime($_REQUEST['search_starttime'] . '00:00:00') . ',' . strtotime($_REQUEST['search_endtime'] . '23:59:59'));
                $search['search_starttime'] = $_REQUEST['search_starttime'];
                $search['search_endtime'] = $_REQUEST['search_endtime'];
            } else {
                $times = (strtotime($_REQUEST['search_endtime'] . '00:00:00') . ',' . strtotime($_REQUEST['search_starttime'] . '23:59:59'));
                $search['search_starttime'] = $_REQUEST['search_endtime'];
                $search['search_endtime'] = $_REQUEST['search_starttime'];
            }
            $map['create_date'] = array('between', $times);
            //$timespan = strtotime(urldecode($_REQUEST['start_time'])) . "," . strtotime(urldecode($_REQUEST['end_time']));
        } elseif (!empty($_REQUEST['search_starttime'])) {
            $xtime = strtotime($_REQUEST['search_starttime'] . '00:00:00');
            $map['create_date'] = array("egt", $xtime);
            $search['search_starttime'] = $_REQUEST['search_starttime'];
        } elseif (!empty($_REQUEST['search_endtime'])) {
            $xtime = strtotime($_REQUEST['search_endtime'] . '23:59:59');
            $map['create_date'] = array("elt", $xtime);
            $search['search_endtime'] = $_REQUEST['search_endtime'];
        }
        if (!empty($_REQUEST['search_username'])) {
            $idInfo = $member_table->field('id')->where(array('username' => $_REQUEST['search_username']))->find();
            $map['uid'] = $idInfo['id'];
            $search['search_username'] = $_REQUEST['search_username'];
        }
        $count = $remittance_table->where($map)->count(); // 查询满足要求的总记录数
        $Page = new \Think\Page($count, 50); // 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show();
        $list = $remittance_table->order('id desc')->where($map)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        for ($i = 0; $i < $count; $i++) {
            $userInfo = $member_table->field('username')->find($list[$i]['uid']);
            $list[$i]['username'] = $userInfo['username'];
        }
        $this->assign('count', $count);
        $this->assign('page', $show);
        $this->assign('list', $list);
        $this->assign('arr', $search);
        $this->display();
    }

    //审核汇款
    public function shenPiRemittance() {
        $remittance_table = M('remittance');
        if (IS_POST) {
            $id = I('post.id', '', 'htmlspecialchars');
            $relust = $remittance_table->save(array('id' => $id, 'status' => 2, 'shenhedate' => time()));
            if ($relust) {
                $json['status'] = 1;
                $json['msg'] = '操作成功';
                echo json_encode($json);
                exit;
            } else {
                $json['status'] = 2;
                $json['msg'] = '操作失败';
                echo json_encode($json);
                exit;
            }
        }
    }

    //拒绝汇款
    public function jujieRemittance() {
        $remittance_table = M('remittance');
        if (IS_POST) {
            $id = I('post.id', '', 'htmlspecialchars');
            $relust = $remittance_table->save(array('id' => $id, 'status' => 4, 'shenhedate' => time()));
            if ($relust) {
                $json['status'] = 1;
                $json['msg'] = '操作成功';
                echo json_encode($json);
                exit;
            } else {
                $json['status'] = 2;
                $json['msg'] = '操作失败';
                echo json_encode($json);
                exit;
            }
        }
    }

    //发放汇款
    public function fafangRemittance() {
        $todayTime = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
        $beginThismonth = mktime(0, 0, 0, date('m'), 1, date('Y'));  //当月第一天时间戳
        $week = mktime(0, 0, 0, date('m'), date('d') - date('w'), date('Y')); //当周第一天
        $remittance_table = M('remittance');
        $member_table = M('member');
        $bonus_table = M('bonus');
        $recharge_table = M('recharge');
        $member_table->startTrans();
        $todayTime = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
        if (IS_POST) {
            $id = I('post.id', '', 'htmlspecialchars');
            $info = $remittance_table->find($id);
            if ($info['status'] != 2) {
                $json['status'] = 2;
                $json['msg'] = '非法操作';
                echo json_encode($json);
                exit;
            }
            $relust = $remittance_table->save(array('id' => $id, 'status' => 3, 'fafangdate' => time()));
            $userInfo = $member_table->field('cash')->find($info['uid']);
            $allmoney = $userInfo['cash'] + $info['money'];
            $relust1 = $member_table->save(array('id' => $info['uid'], 'cash' => $allmoney));
            $relust2 = $bonus_table->add(array('uid' => $info['uid'], 'type' => '22', 'income' => $info['money'], 'status' => '1', 'balance' => $allmoney, 'message' => '线下充值发放', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '1'));
            $relust3 = $recharge_table->add(array('uid' => $info['uid'], 'hid' => session('userid'), 'money' => $info['money'], 'type' => '6', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'status' => '1'));


            if ($relust && $relust1 && $relust2 && $relust3) {
                $member_table->commit();
                $json['status'] = 1;
                $json['msg'] = '操作成功';
                echo json_encode($json);
                exit;
            } else {
                $member_table->rollback();
                $json['status'] = 2;
                $json['msg'] = '操作失败';
                echo json_encode($json);
                exit;
            }
        }
    }

    //未处理提现
    public function listapproved() {
        $withdrawals_table = M('withdrawals');
        $member_table = M('member');
        $map['status'] = 1;
        if (!empty($_REQUEST['search_starttime']) && !empty($_REQUEST['search_endtime'])) {
            $startime = strtotime($_REQUEST['search_starttime']);
            $endtime = strtotime($_REQUEST['search_endtime']);

            if ($startime <= $endtime) {
                $times = (strtotime($_REQUEST['search_starttime'] . '00:00:00') . ',' . strtotime($_REQUEST['search_endtime'] . '23:59:59'));
                $search['search_starttime'] = $_REQUEST['search_starttime'];
                $search['search_endtime'] = $_REQUEST['search_endtime'];
            } else {
                $times = (strtotime($_REQUEST['search_endtime'] . '00:00:00') . ',' . strtotime($_REQUEST['search_starttime'] . '23:59:59'));
                $search['search_starttime'] = $_REQUEST['search_endtime'];
                $search['search_endtime'] = $_REQUEST['search_starttime'];
            }
            $map['create_date'] = array('between', $times);
            //$timespan = strtotime(urldecode($_REQUEST['start_time'])) . "," . strtotime(urldecode($_REQUEST['end_time']));
        } elseif (!empty($_REQUEST['search_starttime'])) {
            $xtime = strtotime($_REQUEST['search_starttime'] . '00:00:00');
            $map['create_date'] = array("egt", $xtime);
            $search['search_starttime'] = $_REQUEST['search_starttime'];
        } elseif (!empty($_REQUEST['search_endtime'])) {
            $xtime = strtotime($_REQUEST['search_endtime'] . '23:59:59');
            $map['create_date'] = array("elt", $xtime);
            $search['search_endtime'] = $_REQUEST['search_endtime'];
        }
        if (!empty($_REQUEST['search_username'])) {
            $idInfo = $member_table->field('id')->where(array('username' => $_REQUEST['search_username']))->find();
            $map['uid'] = $idInfo['id'];
            $search['search_username'] = $_REQUEST['search_username'];
        }
        $type = allmomeytype();
        $status = array('1' => '等待审核', '2' => '审核通过', '3' => '拒绝通过', '4' => '已经发放');
        $count = $withdrawals_table->where($map)->count(); // 查询满足要求的总记录数
        $Page = new \Think\Page($count, 50); // 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show();
        $list = $withdrawals_table->order('id desc')->where($map)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        for ($i = 0; $i < $count; $i++) {
            $userInfo = $member_table->field('username')->find($list[$i]['uid']);
            $list[$i]['username'] = $userInfo['username'];
        }
        $this->assign('status', $status);
        $this->assign('count', $count);
        $this->assign('page', $show);
        $this->assign('list', $list);
        $this->assign('arr', $search);
        $this->assign('type', $type);

        $this->display();
    }

    //提现审核通过
    public function tixiangongguo($id) {
        $withdrawals_table = M('withdrawals');
        if (IS_POST) {
            $id = I('post.id', '', 'htmlspecialchars');
            $relust = $withdrawals_table->save(array('id' => $id, 'status' => 2, 'release_date' => time()));
            if ($relust) {
                $json['status'] = 1;
                $json['msg'] = '操作成功';
                echo json_encode($json);
                exit;
            } else {
                $json['status'] = 2;
                $json['msg'] = '操作失败';
                echo json_encode($json);
                exit;
            }
        }
    }

    //提现审核拒绝
    public function tixianjujue($id) {
        $todayTime = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
        $beginThismonth = mktime(0, 0, 0, date('m'), 1, date('Y'));  //当月第一天时间戳
        $week = mktime(0, 0, 0, date('m'), date('d') - date('w'), date('Y')); //当周第一天
        $withdrawals_table = M('withdrawals');
        $bonus_table = M('bonus');
        $member_table = M('member');
        $bonus_table->startTrans();
        if (IS_POST) {
            $id = I('post.id', '', 'htmlspecialchars');
            $info = $withdrawals_table->find($id);

            switch ($info['type']) {
                case 19:
                    $userInfo = $member_table->field('allbonus')->find($info['uid']);
                    $allusername = $userInfo['allbonus'] + $info['truemoney'];
                    $relust1 = $withdrawals_table->save(array('id' => $id, 'status' => 3, 'release_date' => time()));
                    $relust3 = $member_table->save(array('id' => $info['uid'], 'allbonus' => $allusername));
                    $relust2 = $bonus_table->add(array('uid' => $info['uid'], 'type' => $info['type'], 'income' => $info['truemoney'], 'status' => '1', 'balance' => $allusername, 'message' => '提现审核不通过返还', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '11'));

                    break;
                case 20:
                    $userInfo = $member_table->field('alljingtaibonus')->find($info['uid']);
                    $allusername = $userInfo['alljingtaibonus'] + $info['truemoney'];
                    $relust1 = $withdrawals_table->save(array('id' => $id, 'status' => 3, 'release_date' => time()));
                    $relust3 = $member_table->save(array('id' => $info['uid'], 'alljingtaibonus' => $allusername));
                    $relust2 = $bonus_table->add(array('uid' => $info['uid'], 'type' => $info['type'], 'income' => $info['truemoney'], 'status' => '1', 'balance' => $allusername, 'message' => '提现审核不通过返还', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '11'));


                    break;
            }





            if ($relust1 && $relust2 && $relust3) {
                $bonus_table->commit();
                $json['status'] = 1;
                $json['msg'] = '操作成功';
                echo json_encode($json);
                exit;
            } else {
                $bonus_table->rollback();
                $json['status'] = 2;
                $json['msg'] = '操作失败';
                echo json_encode($json);
                exit;
            }
        }
    }

    //积分提现审核通过
    public function jifentixiangongguo($id) {
        $todayTime = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
        $beginThismonth = mktime(0, 0, 0, date('m'), 1, date('Y'));  //当月第一天时间戳
        $week = mktime(0, 0, 0, date('m'), date('d') - date('w'), date('Y')); //当周第一天
        $integral_table = M('integral');
        $member_table = M('member');
        $bonus_table = M('bonus');
        $member_table->startTrans();
        if (IS_POST) {
            $Finance = A('Home/Finance');
            $id = I('post.id', '', 'htmlspecialchars');

            $info = $integral_table->find($id);
            $userInfo = $member_table->find($info['uid']);
            $rels = $Finance->splitbonus($info['uid'], $info['totalnum']);
            $allusermoney = $userInfo['fenhongbonus'] + $rels;
            $relust1 = $member_table->save(array('fenhongbonus' => $allusermoney, 'id' => $info['uid']));
            $relust2 = $bonus_table->add(array('uid' => $info['uid'], 'income' => $rels, 'balance' => $allusermoney, 'type' => '13', 'status' => '1', 'message' => '溢价积分', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '3'));

            $relust = $integral_table->save(array('id' => $id, 'status' => 2, 'release_date' => time()));

            if ($relust && $relust1 && $relust2) {
                $member_table->commit();
                $json['status'] = 1;
                $json['msg'] = '操作成功';
                echo json_encode($json);
                exit;
            } else {
                $member_table->rollback();
                $json['status'] = 2;
                $json['msg'] = '操作失败';
                echo json_encode($json);
                exit;
            }
        }
    }

    //积分提现审核拒绝
    public function jifentixianjujue() {
        $todayTime = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
        $beginThismonth = mktime(0, 0, 0, date('m'), 1, date('Y'));  //当月第一天时间戳
        $week = mktime(0, 0, 0, date('m'), date('d') - date('w'), date('Y')); //当周第一天
        $integral_table = M('integral');
        $bonus_table = M('bonus');
        $member_table = M('member');
        $bonus_table->startTrans();
        if (IS_POST) {
            $id = I('post.id', '', 'htmlspecialchars');
            $info = $integral_table->find($id);
            $userInfo = $member_table->field('integral')->find($info['uid']);
            $allusername = $userInfo['integral'] + ceil($info['totalnum']);
            $relust1 = $integral_table->save(array('id' => $id, 'status' => 3, 'release_date' => time()));
            $relust3 = $member_table->save(array('id' => $info['uid'], 'integral' => $allusername));
            $relust2 = $bonus_table->add(array('uid' => $info['uid'], 'type' => 10, 'income' => $info['totalnum'], 'status' => '1', 'balance' => $allusername, 'message' => '卖出审核不通过返还', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '11'));

            if ($relust1 && $relust2 && $relust3) {
                $bonus_table->commit();
                $json['status'] = 1;
                $json['msg'] = '操作成功';
                echo json_encode($json);
                exit;
            } else {
                $bonus_table->rollback();
                $json['status'] = 2;
                $json['msg'] = '操作失败';
                echo json_encode($json);
                exit;
            }
        }
    }

    //已处理提现
    public function listapprovedfafang() {
        $withdrawals_table = M('withdrawals');
        $member_table = M('member');
        $map['status'] = array('in', '2,3,4');
        if (!empty($_REQUEST['search_starttime']) && !empty($_REQUEST['search_endtime'])) {
            $startime = strtotime($_REQUEST['search_starttime']);
            $endtime = strtotime($_REQUEST['search_endtime']);

            if ($startime <= $endtime) {
                $times = (strtotime($_REQUEST['search_starttime'] . '00:00:00') . ',' . strtotime($_REQUEST['search_endtime'] . '23:59:59'));
                $search['search_starttime'] = $_REQUEST['search_starttime'];
                $search['search_endtime'] = $_REQUEST['search_endtime'];
            } else {
                $times = (strtotime($_REQUEST['search_endtime'] . '00:00:00') . ',' . strtotime($_REQUEST['search_starttime'] . '23:59:59'));
                $search['search_starttime'] = $_REQUEST['search_endtime'];
                $search['search_endtime'] = $_REQUEST['search_starttime'];
            }
            $map['create_date'] = array('between', $times);
            //$timespan = strtotime(urldecode($_REQUEST['start_time'])) . "," . strtotime(urldecode($_REQUEST['end_time']));
        } elseif (!empty($_REQUEST['search_starttime'])) {
            $xtime = strtotime($_REQUEST['search_starttime'] . '00:00:00');
            $map['create_date'] = array("egt", $xtime);
            $search['search_starttime'] = $_REQUEST['search_starttime'];
        } elseif (!empty($_REQUEST['search_endtime'])) {
            $xtime = strtotime($_REQUEST['search_endtime'] . '23:59:59');
            $map['create_date'] = array("elt", $xtime);
            $search['search_endtime'] = $_REQUEST['search_endtime'];
        }
        if (!empty($_REQUEST['search_username'])) {
            $idInfo = $member_table->field('id')->where(array('username' => $_REQUEST['search_username']))->find();
            $map['uid'] = $idInfo['id'];
            $search['search_username'] = $_REQUEST['search_username'];
        }
        $type = allmomeytype();
        $status = array('1' => '等待审核', '2' => '审核通过', '3' => '拒绝通过', '4' => '已经发放');
        $count = $withdrawals_table->where($map)->count(); // 查询满足要求的总记录数
        $Page = new \Think\Page($count, 50); // 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show();
        $list = $withdrawals_table->order('id desc')->where($map)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        for ($i = 0; $i < $count; $i++) {
            $userInfo = $member_table->field('username,name')->find($list[$i]['uid']);
            $list[$i]['username'] = $userInfo['username'];
            $list[$i]['name'] = $userInfo['name'];
        }
        $this->assign('status', $status);
        $this->assign('count', $count);
        $this->assign('page', $show);
        $this->assign('list', $list);
        $this->assign('arr', $search);
        $this->assign('type', $type);

        $this->display();
    }

    //提现发放
    public function fafangtixian() {
        $withdrawals_table = M('withdrawals');
        if (IS_POST) {
            $id = I('post.id', '', 'htmlspecialchars');
            $relust = $withdrawals_table->save(array('id' => $id, 'status' => 4, 'release_date' => time()));
            if ($relust) {

                $json['status'] = 1;
                $json['msg'] = '操作成功';
                echo json_encode($json);
                exit;
            } else {

                $json['status'] = 2;
                $json['msg'] = '操作失败';
                echo json_encode($json);
                exit;
            }
        }
    }

    //查看会员挂单顺序（业绩表）
    public function order() {
        $member_table = M('member');
        $achievement_table = M('achievement');
        if (!empty($_REQUEST['search_starttime']) && !empty($_REQUEST['search_endtime'])) {
            $startime = strtotime($_REQUEST['search_starttime']);
            $endtime = strtotime($_REQUEST['search_endtime']);

            if ($startime <= $endtime) {
                $times = (strtotime($_REQUEST['search_starttime'] . '00:00:00') . ',' . strtotime($_REQUEST['search_endtime'] . '23:59:59'));
                $search['search_starttime'] = $_REQUEST['search_starttime'];
                $search['search_endtime'] = $_REQUEST['search_endtime'];
            } else {
                $times = (strtotime($_REQUEST['search_endtime'] . '00:00:00') . ',' . strtotime($_REQUEST['search_starttime'] . '23:59:59'));
                $search['search_starttime'] = $_REQUEST['search_endtime'];
                $search['search_endtime'] = $_REQUEST['search_starttime'];
            }
            $map['create_date'] = array('between', $times);
            //$timespan = strtotime(urldecode($_REQUEST['start_time'])) . "," . strtotime(urldecode($_REQUEST['end_time']));
        } elseif (!empty($_REQUEST['search_starttime'])) {
            $xtime = strtotime($_REQUEST['search_starttime'] . '00:00:00');
            $map['create_date'] = array("egt", $xtime);
            $search['search_starttime'] = $_REQUEST['search_starttime'];
        } elseif (!empty($_REQUEST['search_endtime'])) {
            $xtime = strtotime($_REQUEST['search_endtime'] . '23:59:59');
            $map['create_date'] = array("elt", $xtime);
            $search['search_endtime'] = $_REQUEST['search_endtime'];
        }
        if (!empty($_REQUEST['search_username'])) {
            $idInfo = $member_table->field('id')->where(array('username' => $_REQUEST['search_username']))->find();
            $map['uid'] = $idInfo['id'];
            $search['search_username'] = $_REQUEST['search_username'];
        }
        $count = $achievement_table->where($map)->count(); // 查询满足要求的总记录数
        $Page = new \Think\Page($count, 50); // 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show();
        $list = $achievement_table->order('id desc')->where($map)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        for ($i = 0; $i < $count; $i++) {
            $userInfo = $member_table->field('username')->find($list[$i]['uid']);
            $list[$i]['username'] = $userInfo['username'];
        }
        $this->assign('count', $count);
        $this->assign('page', $show);
        $this->assign('list', $list);
        $this->assign('arr', $search);
        $this->display();
    }

    public function listweichulisell() {
        $integral_table = M('integral');
        $map['status'] = 1;
        if (!empty($_REQUEST['search_starttime']) && !empty($_REQUEST['search_endtime'])) {
            $startime = strtotime($_REQUEST['search_starttime']);
            $endtime = strtotime($_REQUEST['search_endtime']);

            if ($startime <= $endtime) {
                $times = (strtotime($_REQUEST['search_starttime'] . '00:00:00') . ',' . strtotime($_REQUEST['search_endtime'] . '23:59:59'));
                $search['search_starttime'] = $_REQUEST['search_starttime'];
                $search['search_endtime'] = $_REQUEST['search_endtime'];
            } else {
                $times = (strtotime($_REQUEST['search_endtime'] . '00:00:00') . ',' . strtotime($_REQUEST['search_starttime'] . '23:59:59'));
                $search['search_starttime'] = $_REQUEST['search_endtime'];
                $search['search_endtime'] = $_REQUEST['search_starttime'];
            }
            $map['create_date'] = array('between', $times);
            //$timespan = strtotime(urldecode($_REQUEST['start_time'])) . "," . strtotime(urldecode($_REQUEST['end_time']));
        } elseif (!empty($_REQUEST['search_starttime'])) {
            $xtime = strtotime($_REQUEST['search_starttime'] . '00:00:00');
            $map['create_date'] = array("egt", $xtime);
            $search['search_starttime'] = $_REQUEST['search_starttime'];
        } elseif (!empty($_REQUEST['search_endtime'])) {
            $xtime = strtotime($_REQUEST['search_endtime'] . '23:59:59');
            $map['create_date'] = array("elt", $xtime);
            $search['search_endtime'] = $_REQUEST['search_endtime'];
        }
        $member_table = M('member');
        $count = $integral_table->where($map)->count(); // 查询满足要求的总记录数
        $Page = new \Think\Page($count, 50); // 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show();
        $list = $integral_table->order('id desc')->where($map)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $count = count($list);
        for ($i = 0; $i < $count; $i++) {
            $info = $member_table->field('username,name')->find($list[$i]['uid']);
            $list[$i]['username'] = $info['username'];
            $list[$i]['name'] = $info['name'];
        }
        $status = array('1' => '等待审核', '2' => '处理完成', '3' => '拒绝通过');
        $this->assign('count', $count);
        $this->assign('page', $show);
        $this->assign('list', $list);
        $this->assign('arr', $search);
        $this->assign('status', $status);
        $this->display();
    }

    public function listyichulisell() {
        $integral_table = M('integral');
        $map['status'] = array('in', '2,3,4');
        if (!empty($_REQUEST['search_starttime']) && !empty($_REQUEST['search_endtime'])) {
            $startime = strtotime($_REQUEST['search_starttime']);
            $endtime = strtotime($_REQUEST['search_endtime']);

            if ($startime <= $endtime) {
                $times = (strtotime($_REQUEST['search_starttime'] . '00:00:00') . ',' . strtotime($_REQUEST['search_endtime'] . '23:59:59'));
                $search['search_starttime'] = $_REQUEST['search_starttime'];
                $search['search_endtime'] = $_REQUEST['search_endtime'];
            } else {
                $times = (strtotime($_REQUEST['search_endtime'] . '00:00:00') . ',' . strtotime($_REQUEST['search_starttime'] . '23:59:59'));
                $search['search_starttime'] = $_REQUEST['search_endtime'];
                $search['search_endtime'] = $_REQUEST['search_starttime'];
            }
            $map['create_date'] = array('between', $times);
            //$timespan = strtotime(urldecode($_REQUEST['start_time'])) . "," . strtotime(urldecode($_REQUEST['end_time']));
        } elseif (!empty($_REQUEST['search_starttime'])) {
            $xtime = strtotime($_REQUEST['search_starttime'] . '00:00:00');
            $map['create_date'] = array("egt", $xtime);
            $search['search_starttime'] = $_REQUEST['search_starttime'];
        } elseif (!empty($_REQUEST['search_endtime'])) {
            $xtime = strtotime($_REQUEST['search_endtime'] . '23:59:59');
            $map['create_date'] = array("elt", $xtime);
            $search['search_endtime'] = $_REQUEST['search_endtime'];
        }
        $member_table = M('member');
        $count = $integral_table->where($map)->count(); // 查询满足要求的总记录数
        $Page = new \Think\Page($count, 50); // 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show();
        $list = $integral_table->order('id desc')->where($map)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $count = count($list);
        for ($i = 0; $i < $count; $i++) {
            $info = $member_table->field('username,name')->find($list[$i]['uid']);
            $list[$i]['username'] = $info['username'];
            $list[$i]['name'] = $info['name'];
        }
        $status = array('1' => '等待审核', '2' => '处理完成', '3' => '拒绝通过');
        $this->assign('count', $count);
        $this->assign('page', $show);
        $this->assign('list', $list);
        $this->assign('arr', $search);
        $this->assign('status', $status);
        $this->display();
    }

    public function addzengzhi() {
        if (IS_POST) {
            $todayTime = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
            $nowmonth = mktime(0, 0, 0, date('m'), 1, date('Y')); //当月第一天的时间戳
            $week = mktime(0, 0, 0, date('m'), date('d') - date('w'), date('Y')); //当周第一天  

            $priceweek_table = M('priceweek');
            $price = I('post.price', '', 'htmlspecialchars'); //单价
            $time = I('post.wdate', '', 'htmlspecialchars'); //时间
            $weeks = strtotime($time);
            $date = date('d', $weeks);
            $week = date('w', $weeks);
            $month = date('m', $weeks);
            $year = date('Y', $weeks);

            $setweek = mktime(0, 0, 0, $month, ($date - $week), $year); //设置天数的当周第一天  
            $setmonth = mktime(0, 0, 0, $month, 1, $year); //设置当月第一天的时间戳

            $rel = $priceweek_table->where(array('week' => $setmonth))->find();//之前是按照周计算的。后来改为按月计算
            if ($rel) {
                $json['status'] = 2;
                $json['msg'] = '当月记录已经添加，操作失败';
                echo json_encode($json);
                exit;
            }
            $relust = $priceweek_table->add(array(
                'price' => $price,
                'create_date' => time(), //这条记录的添加时间
                'date' => date('Y-m-d H:i:s', $setweek), //这条记录的添加时间
                'addtime' => $todayTime, //这条记录的添加时间
             //   'month' => $setmonth,
               // 'week' => $setweek,
                'week'=>$setmonth,
            ));

            if ($relust) {
                $json['status'] = 1;
                $json['msg'] = '操作成功';
                echo json_encode($json);
                exit;
            } else {
                $json['status'] = 2;
                $json['msg'] = '操作失败';
                echo json_encode($json);
                exit;
            }
        }
        $this->display();
    }

    //编辑每周的单价
    public function savezengzhi() {

        $ids = I('get.id', '', 'htmlspecialchars');
        $priceweek_table = M('priceweek');
        $data = $priceweek_table->find($ids);
        if (IS_POST) {
            $todayTime = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
            $nowmonth = mktime(0, 0, 0, date('m'), 1, date('Y')); //当月第一天的时间戳
            $week = mktime(0, 0, 0, date('m'), date('d') - date('w'), date('Y')); //当周第一天  


            $id = I('post.id', '', 'htmlspecialchars'); //
            $price = I('post.price', '', 'htmlspecialchars'); //单价
            $time = I('post.wdate', '', 'htmlspecialchars'); //时间
            $weeks = strtotime($time);
            $date = date('d', $weeks);
            $week = date('w', $weeks);
            $month = date('m', $weeks);
            $year = date('Y', $weeks);

            $setweek = mktime(0, 0, 0, $month, ($date - $week), $year); //设置天数的当周第一天  
            $setmonth = mktime(0, 0, 0, $month, 1, $year); //设置当月第一天的时间戳

            $rel = $priceweek_table->where(array('week' => $setmonth))->find();
            if ($rel) {
                if ($rel['id'] != $id) {
                    $json['status'] = 2;
                    $json['msg'] = '当月记录已经添加，操作失败';
                    echo json_encode($json);
                    exit;
                }
            }
            $relust = $priceweek_table->save(array(
                'id' => $id,
                'price' => $price,
                'create_date' => time(), //这条记录的添加时间
                'date' => date('Y-m-d H:i:s', $setweek), //这条记录的添加时间
                'addtime' => $todayTime, //这条记录的添加时间
               // 'month' => $setmonth,
               // 'week' => $setweek,
                'week'=>$setmonth,
            ));

            if ($relust) {
                $json['status'] = 1;
                $json['msg'] = '操作成功';
                echo json_encode($json);
                exit;
            } else {
                $json['status'] = 2;
                $json['msg'] = '操作失败';
                echo json_encode($json);
                exit;
            }
        }
        $this->assign('data', $data);
        $this->display();
    }

    public function listzengzhi() {


        $week = mktime(0, 0, 0, date('m'), date('d') - date('w'), date('Y'));
        $lasweek = $week - 60 * 60 * 24 * 7;
        // $data = getbonusparam(); //获取的奖金比例参数
        $achievement_table = M('achievement');
        $bonus_table = M('bonus');
        $integral_table = M('integral'); //积分提现记录

        $allachievement = $achievement_table->where(array('week' => $lasweek))->sum('money'); //上-周的总业绩
        $allbonus = $bonus_table->where(array('week' => $lasweek, 'status' => '1', 'action' => '3'))->sum('income'); //上-周的拨出的总利息
        $allprofit = ($allachievement - $allbonus) * 0.10; //当周总利润百分之十
        $number = $integral_table->where(array('status' => '1'))->sum('totalnum'); //挂单的未完成的总数量


        $monovalent = $allprofit / $number; //一积分等于的钱
        $monovalent = (empty($monovalent)) ? 无人卖出 : $monovalent;
        $this->assign('monovalent', $monovalent);



        $priceweek_table = M('priceweek');
        $count = $priceweek_table->where($map)->count(); // 查询满足要求的总记录数
        $Page = new \Think\Page($count, 50); // 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show();
        $list = $priceweek_table->order('week desc')->where($map)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $count = count($list);
        //  $status = array('1' => '使用', '2' => '未用');
        $this->assign('count', $count);
        $this->assign('page', $show);
        $this->assign('list', $list);
        $this->assign('status', $status);
        $this->display();
    }

    public function addfenhong() {
        if (IS_POST) {
            $todayTime = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
            $nowmonth = mktime(0, 0, 0, date('m'), 1, date('Y')); //当月第一天的时间戳
            $week = mktime(0, 0, 0, date('m'), date('d') - date('w'), date('Y')); //当周第一天  

            $pricemonth_table = M('pricemonth');
            $price = I('post.price', '', 'htmlspecialchars'); //单价
            $time = I('post.wdate', '', 'htmlspecialchars'); //时间
            $weeks = strtotime($time);
            $date = date('d', $weeks);
            $week = date('w', $weeks);
            $month = date('m', $weeks);
            $year = date('Y', $weeks);

            $setmonth = mktime(0, 0, 0, $month, 1, $year); //设置当月第一天的时间戳

            $rel = $pricemonth_table->where(array('month' => $setmonth))->find();
            if ($rel) {
                $json['status'] = 2;
                $json['msg'] = '当月记录已经添加，操作失败';
                echo json_encode($json);
                exit;
            }
            $relust = $pricemonth_table->add(array(
                'price' => $price,
                'create_date' => time(), //这条记录的添加时间
                'date' => date('Y-m-d H:i:s', $setmonth), //这条记录的添加时间
                'addtime' => $todayTime, //这条记录的添加时间
                'month' => $setmonth,
            ));

            if ($relust) {
                $json['status'] = 1;
                $json['msg'] = '操作成功';
                echo json_encode($json);
                exit;
            } else {
                $json['status'] = 2;
                $json['msg'] = '操作失败';
                echo json_encode($json);
                exit;
            }
        }
        $this->display();
    }

    //编辑每月的单价
    public function savefenhong() {

        $ids = I('get.id', '', 'htmlspecialchars');
        $pricemonth_table = M('pricemonth');
        $data = $pricemonth_table->find($ids);
        if (IS_POST) {
            $todayTime = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
            $nowmonth = mktime(0, 0, 0, date('m'), 1, date('Y')); //当月第一天的时间戳
            $week = mktime(0, 0, 0, date('m'), date('d') - date('w'), date('Y')); //当周第一天  


            $id = I('post.id', '', 'htmlspecialchars'); //
            $price = I('post.price', '', 'htmlspecialchars'); //单价
            $time = I('post.wdate', '', 'htmlspecialchars'); //时间
            $weeks = strtotime($time);
            $date = date('d', $weeks);
            $week = date('w', $weeks);
            $month = date('m', $weeks);
            $year = date('Y', $weeks);


            $setmonth = mktime(0, 0, 0, $month, 1, $year); //设置当月第一天的时间戳

            $rel = $pricemonth_table->where(array('month' => $setmonth))->find();
            if ($rel) {
                if ($rel['id'] != $id) {
                    $json['status'] = 2;
                    $json['msg'] = '当月记录已经添加，操作失败';
                    echo json_encode($json);
                    exit;
                }
            }
            $relust = $pricemonth_table->save(array(
                'id' => $id,
                'price' => $price,
                'create_date' => time(), //这条记录的添加时间
                'date' => date('Y-m-d H:i:s', $setweek), //这条记录的添加时间
                'addtime' => $todayTime, //这条记录的添加时间
                'month' => $setmonth,
            ));

            if ($relust) {
                $json['status'] = 1;
                $json['msg'] = '操作成功';
                echo json_encode($json);
                exit;
            } else {
                $json['status'] = 2;
                $json['msg'] = '操作失败';
                echo json_encode($json);
                exit;
            }
        }
        $this->assign('data', $data);
        $this->display();
    }

    public function listfenhong() {
        $data = getbonusparam(); //获取的奖金比例参数
        $bonus_table = M('bonus');
        $achievement_table = M('achievement');
        $member_table = M('member');
        $lastmonth = mktime(0, 0, 0, date('m') - 1, 1, date('Y')); //上个月第一天的时间戳
        $allachievement = $achievement_table->where(array('month' => $lastmonth))->sum('money'); //上个月的总市场业绩
        $allbonus = $bonus_table->where(array('month' => $lastmonth, 'status' => '1', 'action' => '3'))->sum('income'); //上个月的拨出的总利息（不包含扣掉的）

        $allprofit = ($allachievement - $allbonus) * $data['zenzhi']; //当月总利润百分之十
        $allintegral = $member_table->sum('integral'); //平台的总积分
        $monovalent = $allprofit / $allintegral; //一积分等于的钱

        $this->assign('monovalent', $monovalent);




        $pricemonth_table = M('pricemonth');
        $count = $pricemonth_table->where($map)->count(); // 查询满足要求的总记录数
        $Page = new \Think\Page($count, 50); // 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show();
        $list = $pricemonth_table->order('month desc')->where($map)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $count = count($list);
        //  $status = array('1' => '使用', '2' => '未用');
        $this->assign('count', $count);
        $this->assign('page', $show);
        $this->assign('list', $list);
        $this->assign('status', $status);
        $this->display();
    }

    public function paylist() {
        $member_table = M('member');
        $paylist_tabel = M('paylist');
        if (!empty($_REQUEST['search_starttime']) && !empty($_REQUEST['search_endtime'])) {
            $startime = strtotime($_REQUEST['search_starttime']);
            $endtime = strtotime($_REQUEST['search_endtime']);

            if ($startime <= $endtime) {
                $times = (strtotime($_REQUEST['search_starttime'] . '00:00:00') . ',' . strtotime($_REQUEST['search_endtime'] . '23:59:59'));
                $search['search_starttime'] = $_REQUEST['search_starttime'];
                $search['search_endtime'] = $_REQUEST['search_endtime'];
            } else {
                $times = (strtotime($_REQUEST['search_endtime'] . '00:00:00') . ',' . strtotime($_REQUEST['search_starttime'] . '23:59:59'));
                $search['search_starttime'] = $_REQUEST['search_endtime'];
                $search['search_endtime'] = $_REQUEST['search_starttime'];
            }
            $map['create_date'] = array('between', $times);
            //$timespan = strtotime(urldecode($_REQUEST['start_time'])) . "," . strtotime(urldecode($_REQUEST['end_time']));
        } elseif (!empty($_REQUEST['search_starttime'])) {
            $xtime = strtotime($_REQUEST['search_starttime'] . '00:00:00');
            $map['create_date'] = array("egt", $xtime);
            $search['search_starttime'] = $_REQUEST['search_starttime'];
        } elseif (!empty($_REQUEST['search_endtime'])) {
            $xtime = strtotime($_REQUEST['search_endtime'] . '23:59:59');
            $map['create_date'] = array("elt", $xtime);
            $search['search_endtime'] = $_REQUEST['search_endtime'];
        }
        $count = $paylist_tabel->where($map)->count(); // 查询满足要求的总记录数
        $Page = new \Think\Page($count, 50); // 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show();
        $list = $paylist_tabel->order('id desc')->where($map)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $lcount = count($list);
        for ($i = 0; $i < $lcount; $i++) {
            $userInfo = $member_table->field('username,name')->find($list[$i]['uid']);
            $list[$i]['username'] = $userInfo['username'];
            $list[$i]['name'] = $userInfo['name'];
        }
        $this->assign('count', $count);
        $this->assign('page', $show);
        $this->assign('list', $list);
        $this->assign('arr', $search);
        $this->display();
    }

    //创建订单随机账号
    public function create_orederno() {

        $paylist_tabel = M('paylist');
        $str = "1,2,3,4,5,6,7,8,9,0";
        $list = explode(",", $str);
        $cmax = count($list) - 1;
        $verifyCode = build_order_no();
        for ($i = 0; $i < 5; $i++) {
            $randnum = mt_rand(0, $cmax);
            $verifyCode .= $list[$randnum];
        }
        $relust = $paylist_tabel->field('billno')->where(array('billno' => $verifyCode))->find();
        if ($relust) {
            self::create_orederno();
        } else {
            return $verifyCode;
        }
    }

    //市场业绩明细查询
    public function shichanglist() {

        $newtotalbonus_table = M('newtotalbonus');
        $member_table = M('member');
        if (!empty($_REQUEST['search_starttime']) && !empty($_REQUEST['search_endtime'])) {
            $startime = strtotime($_REQUEST['search_starttime']);
            $endtime = strtotime($_REQUEST['search_endtime']);

            if ($startime <= $endtime) {
                $times = (strtotime($_REQUEST['search_starttime'] . '00:00:00') . ',' . strtotime($_REQUEST['search_endtime'] . '23:59:59'));
                $search['search_starttime'] = $_REQUEST['search_starttime'];
                $search['search_endtime'] = $_REQUEST['search_endtime'];
            } else {
                $times = (strtotime($_REQUEST['search_endtime'] . '00:00:00') . ',' . strtotime($_REQUEST['search_starttime'] . '23:59:59'));
                $search['search_starttime'] = $_REQUEST['search_endtime'];
                $search['search_endtime'] = $_REQUEST['search_starttime'];
            }
            $map['create_date'] = array('between', $times);
            //$timespan = strtotime(urldecode($_REQUEST['start_time'])) . "," . strtotime(urldecode($_REQUEST['end_time']));
        } elseif (!empty($_REQUEST['search_starttime'])) {
            $xtime = strtotime($_REQUEST['search_starttime'] . '00:00:00');
            $map['create_date'] = array("egt", $xtime);
            $search['search_starttime'] = $_REQUEST['search_starttime'];
        } elseif (!empty($_REQUEST['search_endtime'])) {
            $xtime = strtotime($_REQUEST['search_endtime'] . '23:59:59');
            $map['create_date'] = array("elt", $xtime);
            $search['search_endtime'] = $_REQUEST['search_endtime'];
        }
        if (!empty($_REQUEST['search_username'])) {
            $search_userInfo = $member_table->field('id')->where(array('username' => $_REQUEST['search_username']))->find();
            $map['uid'] = $search_userInfo['id'];
            $search['search_username'] = $_REQUEST['search_username'];
        }

        if (!empty($_REQUEST['search_quyi'])) {
            $dd = $_REQUEST['search_quyi'];
            $map['type'] = $dd - 1;
            $search['search_quyi'] = $_REQUEST['search_quyi'];
        }
        $search_money = $newtotalbonus_table->where($map)->sum('money');
        $search_money = empty($search_money) ? 0 : $search_money;
        $count = $newtotalbonus_table->where($map)->count(); // 查询满足要求的总记录数
        $Page = new \Think\Page($count, 16); // 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show();
        $list = $newtotalbonus_table->order('id desc')->where($map)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $count = count($list);
        $qu = array('左', '中', '右');
        for ($i = 0; $i < $count; $i++) {
            $userInfo = $member_table->field('username,name')->find($list[$i]['uid']);
            $fromInfo = $member_table->field('username,name')->find($list[$i]['fid']);
            $list[$i]['username'] = $userInfo['username'];
            $list[$i]['name'] = $userInfo['name'];
            $list[$i]['qu'] = $qu[$list[$i]['type']];
            $list[$i]['fromname'] = $fromInfo['username'];
        }


        $this->assign('count', $count);
        $this->assign('page', $show);
        $this->assign('list', $list);
        $this->assign('arr', $search);
        $this->assign('search_money', $search_money);
        $this->display();
    }

    public function tuanduilist() {
        $groupday_table = M('groupday');
        $member_table = M('member');
        if (!empty($_REQUEST['search_starttime']) && !empty($_REQUEST['search_endtime'])) {
            $startime = strtotime($_REQUEST['search_starttime']);
            $endtime = strtotime($_REQUEST['search_endtime']);

            if ($startime <= $endtime) {
                $times = (strtotime($_REQUEST['search_starttime'] . '00:00:00') . ',' . strtotime($_REQUEST['search_endtime'] . '23:59:59'));
                $search['search_starttime'] = $_REQUEST['search_starttime'];
                $search['search_endtime'] = $_REQUEST['search_endtime'];
            } else {
                $times = (strtotime($_REQUEST['search_endtime'] . '00:00:00') . ',' . strtotime($_REQUEST['search_starttime'] . '23:59:59'));
                $search['search_starttime'] = $_REQUEST['search_endtime'];
                $search['search_endtime'] = $_REQUEST['search_starttime'];
            }
            $map['create_date'] = array('between', $times);
            //$timespan = strtotime(urldecode($_REQUEST['start_time'])) . "," . strtotime(urldecode($_REQUEST['end_time']));
        } elseif (!empty($_REQUEST['search_starttime'])) {
            $xtime = strtotime($_REQUEST['search_starttime'] . '00:00:00');
            $map['create_date'] = array("egt", $xtime);
            $search['search_starttime'] = $_REQUEST['search_starttime'];
        } elseif (!empty($_REQUEST['search_endtime'])) {
            $xtime = strtotime($_REQUEST['search_endtime'] . '23:59:59');
            $map['create_date'] = array("elt", $xtime);
            $search['search_endtime'] = $_REQUEST['search_endtime'];
        }
        if (!empty($_REQUEST['search_username'])) {
            $search_userInfo = $member_table->field('id')->where(array('username' => $_REQUEST['search_username']))->find();
            $map['uid'] = $search_userInfo['id'];
            $search['search_username'] = $_REQUEST['search_username'];
        }

        $search_money = $groupday_table->where($map)->sum('money');
        $count = $groupday_table->where($map)->count(); // 查询满足要求的总记录数
        $Page = new \Think\Page($count, 16); // 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show();
        $list = $groupday_table->order('id desc')->where($map)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $count = count($list);
        for ($i = 0; $i < $count; $i++) {
            $userInfo = $member_table->field('username,name')->find($list[$i]['uid']);
            $fromInfo = $member_table->field('username,name')->find($list[$i]['fid']);
            $list[$i]['username'] = $userInfo['username'];
            $list[$i]['name'] = $userInfo['name'];
            $list[$i]['fromname'] = $fromInfo['username'];
        }
        $this->assign('count', $count);
        $this->assign('page', $show);
        $this->assign('list', $list);
        $this->assign('arr', $search);
        $this->assign('search_money', $search_money);
        $this->display();
    }

    public function shichanghuizong() {
        $shichangtotal_table = M('shichangtotal');
        $member_table = M('member');
        if (!empty($_REQUEST['search_starttime']) && !empty($_REQUEST['search_endtime'])) {
            $startime = strtotime($_REQUEST['search_starttime']);
            $endtime = strtotime($_REQUEST['search_endtime']);

            if ($startime <= $endtime) {
                $times = (strtotime($_REQUEST['search_starttime'] . '00:00:00') . ',' . strtotime($_REQUEST['search_endtime'] . '23:59:59'));
                $search['search_starttime'] = $_REQUEST['search_starttime'];
                $search['search_endtime'] = $_REQUEST['search_endtime'];
            } else {
                $times = (strtotime($_REQUEST['search_endtime'] . '00:00:00') . ',' . strtotime($_REQUEST['search_starttime'] . '23:59:59'));
                $search['search_starttime'] = $_REQUEST['search_endtime'];
                $search['search_endtime'] = $_REQUEST['search_starttime'];
            }
            $map['create_date'] = array('between', $times);
            //$timespan = strtotime(urldecode($_REQUEST['start_time'])) . "," . strtotime(urldecode($_REQUEST['end_time']));
        } elseif (!empty($_REQUEST['search_starttime'])) {
            $xtime = strtotime($_REQUEST['search_starttime'] . '00:00:00');
            $map['create_date'] = array("egt", $xtime);
            $search['search_starttime'] = $_REQUEST['search_starttime'];
        } elseif (!empty($_REQUEST['search_endtime'])) {
            $xtime = strtotime($_REQUEST['search_endtime'] . '23:59:59');
            $map['create_date'] = array("elt", $xtime);
            $search['search_endtime'] = $_REQUEST['search_endtime'];
        }
        if (!empty($_REQUEST['search_username'])) {
            $search_userInfo = $member_table->field('id')->where(array('username' => $_REQUEST['search_username']))->find();
            $map['uid'] = $search_userInfo['id'];
            $search['search_username'] = $_REQUEST['search_username'];
        }
        if(!empty($_REQUEST['search_money'])){
            $money=  trim($_REQUEST['search_money']);
            if (!empty($_REQUEST['search_quyi'])) {
                $dd = $_REQUEST['search_quyi'];
                if ($dd == 1) {
                    $map['left'] = array('egt',$money);
                }
                if ($dd == 2) {
                     $map['center'] = array('egt',$money);
                }
                if ($dd == 3) {
                     $map['right'] = array('egt',$money);
                }

                $search['search_quyi'] = $_REQUEST['search_quyi'];
                 $search['search_money'] = $_REQUEST['search_money'];
            }
        }
        $count = $shichangtotal_table->where($map)->count(); // 查询满足要求的总记录数
        $Page = new \Think\Page($count, 16); // 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show();
        $list = $shichangtotal_table->order('id desc')->where($map)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $count = count($list);
        for ($i = 0; $i < $count; $i++) {
            $userInfo = $member_table->field('username,name')->find($list[$i]['uid']);
            $list[$i]['username'] = $userInfo['username'];
            $list[$i]['name'] = $userInfo['name'];
            $list[$i]['totalmoney'] = $list[$i]['left'] + $list[$i]['center'] + $list[$i]['right'];
        }
        $this->assign('count', $count);
        $this->assign('page', $show);
        $this->assign('list', $list);
        $this->assign('arr', $search);
        $this->display();
    }

}
