<?php

namespace Home\Controller;

use Home\Controller\CommonController;

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

        $type = array('1' => '快速奖', '2' => '领导奖', '3' => '对等奖', '4' => '晋级奖', '5' => '分红积分', '6' => '电子积分', '7' => '个人所得税', '8' => '名车基金', '9' => '重复消费', '10' => '赠送积分', '11' => '别墅基金');
        $status = array('1' => '收入', '2' => '支出');

        $count = $bonus_table->where($map)->count(); // 查询满足要求的总记录数
        $Page = new \Think\Page($count, 15); // 实例化分页类 传入总记录数和每页显示的记录数(25)
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

    //提现中心
    public function listcashreq() {
        $uid = session('uid');
        $type = allmomeytype();
        $withdrawals_table = M('withdrawals');
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
        $map['uid'] = $uid;
        $member_table = M('member');
        $count = $withdrawals_table->where($map)->count(); // 查询满足要求的总记录数
        $Page = new \Think\Page($count, 15); // 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show();
        $list = $withdrawals_table->order('id desc')->where($map)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $count = count($list);
        for ($i = 0; $i < $count; $i++) {
            $info = $member_table->field('username')->find($list[$i]['uid']);
            $list[$i]['username'] = $info['username'];
        }
        $status = array('1' => '等待审核', '2' => '审核通过', '3' => '拒绝通过', '4' => '已经发放');
        $this->assign('count', $count);
        $this->assign('page', $show);
        $this->assign('list', $list);
        $this->assign('arr', $search);
        $this->assign('type', $type);
        $this->assign('status', $status);
        $this->display();
    }

    //兑换中心
    public function bonuscoin() {
      
        $uid = session('uid');
        $member_table = M('member');
        $userInfo = $member_table->field('cash,dianzimoney,allbonus,alljingtaibonus')->find($uid);
        $this->assign('userInfo', $userInfo);
        $this->display();
    }

    //奖金变换电子积分
    public function currencyConversion() {
        if (IS_POST) {
            $todayTime = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
            $beginThismonth = mktime(0, 0, 0, date('m'), 1, date('Y'));  //当月第一天时间戳
            $week = mktime(0, 0, 0, date('m'), date('d') - date('w'), date('Y')); //当周第一天
            $sum = I('post.sum', '', 'htmlspecialchars');
            $bizhong = I('post.bizhong', '', 'htmlspecialchars');
            $threepassword = I('post.threepassword', '', 'htmlspecialchars');

            $uid = session('uid');
            $member_table = M('member');
            $bonus_table = M('bonus');
            $member_table->startTrans();

            $userInfo = $member_table->field('dianzimoney,allbonus,alljingtaibonus,threepassword')->find($uid);
            if ($sum < 0 || floor($sum) != $sum || !is_numeric($sum)) {
                $json['status'] = 2;
                $json['msg'] = '请输入有效的数字';
                echo json_encode($json);
                exit;
            }
            if ($bizhong != '19' && $bizhong != '20') {
                $json['status'] = 2;
                $json['msg'] = '货币类型不存在';
                echo json_encode($json);
                exit;
            }
            if ($userInfo['threepassword'] != fun_md5($threepassword)) {
                $json['status'] = 2;
                $json['msg'] = '三级密码不正确';
                echo json_encode($json);
                exit;
            }

            if ($bizhong == 19) {
                if ($sum <= $userInfo['allbonus']) {

                    $allusermoney = $userInfo['dianzimoney'] + $sum;
                    $balanceusermoney = $userInfo['allbonus'] - $sum;
                    $relust1 = $member_table->save(array('id' => $uid, 'dianzimoney' => $allusermoney, 'allbonus' => $balanceusermoney));
                    $relust2 = $bonus_table->add(array('uid' => $uid, 'type' => '6', 'income' => $sum, 'status' => '1', 'balance' => $allusermoney, 'message' => '市场积分转换电子积分', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '4'));
                    $relust3 = $bonus_table->add(array('uid' => $uid, 'type' => '19', 'expend' => $sum, 'status' => '2', 'balance' => $balanceusermoney, 'message' => '市场积分转换电子积分', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '5'));
                    if ($relust1 && $relust2 && $relust3) {
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
                } else {
                    $json['status'] = 2;
                    $json['msg'] = '市场积分金额不足';
                    echo json_encode($json);
                    exit;
                }
            }
            if ($bizhong == 20) {
                if ($sum <= $userInfo['alljingtaibonus']) {
                  
                    $allusermoney = $userInfo['dianzimoney'] + $sum;
                    $balanceusermoney = $userInfo['alljingtaibonus'] - $sum;
                    $relust1 = $member_table->save(array('id' => $uid, 'dianzimoney' => $allusermoney, 'alljingtaibonus' => $balanceusermoney));
                    $relust2 = $bonus_table->add(array('uid' => $uid, 'type' => '6', 'income' => $sum, 'status' => '1', 'balance' => $allusermoney, 'message' => '活动积分转换电子积分', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '4'));
                    $relust3 = $bonus_table->add(array('uid' => $uid, 'type' => '20', 'expend' => $sum, 'status' => '2', 'balance' => $balanceusermoney, 'message' => '活动积分转换电子积分', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '5'));
                    if ($relust1 && $relust2 && $relust3) {
                        if (!$member_table->autoCheckToken($_POST)) {
                            $json['status'] = 2;
                            $json['msg'] = '不要重复提交';
                            echo json_encode($json);
                            exit;
                        }
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
                } else {
                    $json['status'] = 2;
                    $json['msg'] = '活动积分金额不足';
                    echo json_encode($json);
                    exit;
                }
            }
        }
    }

    //会员之间电子积分互转
    public function onMemberExchangeCoin() {


        if (IS_POST) {
            $todayTime = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
            $beginThismonth = mktime(0, 0, 0, date('m'), 1, date('Y'));  //当月第一天时间戳
            $week = mktime(0, 0, 0, date('m'), date('d') - date('w'), date('Y')); //当周第一天
            $uid = session('uid');
            $member_table = M('member');
            $bonus_table = M('bonus');
            $member_table->startTrans();
            $othername = I('post.othername', '', 'htmlspecialchars');
            $type = I('post.type', '', 'htmlspecialchars');
            $money = I('post.money', '', 'htmlspecialchars');
            $password = I('post.password', '', 'htmlspecialchars');

            $otherInfo = $member_table->field('id,dianzimoney,username,cash')->where(array('username' => $othername))->find();
            $userInfo = $member_table->field('threepassword,dianzimoney,username,cash')->find($uid);
            if (!$otherInfo) {
                $json['status'] = 2;
                $json['msg'] = '汇入会员号不存在';
                echo json_encode($json);
                exit;
            }
            if ($money < 0 || floor($money) != $money || !is_numeric($money)) {
                $json['status'] = 2;
                $json['msg'] = '请输入有效的数字';
                echo json_encode($json);
                exit;
            }



            if ($otherInfo['id'] == $uid) {
                $json['status'] = 2;
                $json['msg'] = '不能自己给自己转账';
                echo json_encode($json);
                exit;
            }
            if ($type == 22) {
                if ($userInfo['cash'] < $money) {
                    $json['status'] = 2;
                    $json['msg'] = '你的现金积分不足';
                    echo json_encode($json);
                    exit;
                }
            } else if ($type == 6) {
                if ($userInfo['dianzimoney'] < $money) {
                    $json['status'] = 2;
                    $json['msg'] = '你的电子积分不足';
                    echo json_encode($json);
                    exit;
                }
            } else {
                $json['status'] = 2;
                $json['msg'] = '积分类型不存在';
                echo json_encode($json);
                exit;
            }

            if ($userInfo['threepassword'] != fun_md5($password)) {
                $json['status'] = 2;
                $json['msg'] = '三级密码不正确';
                echo json_encode($json);
                exit;
            }

            if ($type == 6) {
                $otherallmoney = $money + $otherInfo['dianzimoney'];
                $relust1 = $member_table->save(array('id' => $otherInfo['id'], 'dianzimoney' => $otherallmoney));
                $relust2 = $bonus_table->add(array('uid' => $otherInfo['id'], 'type' => '6', 'income' => $money, 'status' => '1', 'balance' => $otherallmoney, 'message' => '来自：' . $userInfo['username'] . '转账', 'fid' => $uid, 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '4'));
                $userallmoney = $userInfo['dianzimoney'] - $money;
                $relust3 = $member_table->save(array('id' => $uid, 'dianzimoney' => $userallmoney));
                $relust4 = $bonus_table->add(array('uid' => $uid, 'type' => '6', 'expend' => $money, 'status' => '2', 'balance' => $userallmoney, 'message' => '转账给' . $otherInfo['username'], 'gid' => $otherInfo['id'], 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '5'));
            }
            if ($type == 22) {
                $otherallmoney = $money + $otherInfo['cash'];
                $relust1 = $member_table->save(array('id' => $otherInfo['id'], 'cash' => $otherallmoney));
                $relust2 = $bonus_table->add(array('uid' => $otherInfo['id'], 'type' => '22', 'income' => $money, 'status' => '1', 'balance' => $otherallmoney, 'message' => '来自：' . $userInfo['username'] . '转账', 'fid' => $uid, 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '4'));
                $userallmoney = $userInfo['cash'] - $money;
                $relust3 = $member_table->save(array('id' => $uid, 'cash' => $userallmoney));
                $relust4 = $bonus_table->add(array('uid' => $uid, 'type' => '22', 'expend' => $money, 'status' => '2', 'balance' => $userallmoney, 'message' => '转账给' . $otherInfo['username'], 'gid' => $otherInfo['id'], 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '5'));
            }


            if ($relust1 && $relust2 && $relust3 && $relust4) {
                if (!$member_table->autoCheckToken($_POST)) {
                    $json['status'] = 2;
                    $json['msg'] = '不要重复提交';
                    echo json_encode($json);
                    exit;
                }
                $member_table->commit();
                $json['status'] = 1;
                $json['msg'] = '操作成功';
                $json['token'] = $key . '_' . $value;
                echo json_encode($json);
                exit;
            } else {
                $member_table->rollback();
                $json['status'] = 2;
                $json['msg'] = '操作失败';
                $json['token'] = $key . '_' . $value;
                echo json_encode($json);
                exit;
            }
        }
    }

    //汇款中心
    public function remittancelist() {
        $uid = session('uid');
        $remittance_table = M('remittance');
        $map['uid'] = $uid;
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
        if (!empty($_REQUEST['search_status'])) {
            $map['status'] = $_REQUEST['search_status'];
            $search['search_status'] = $_REQUEST['search_status'];
        }
        $count = $remittance_table->where($map)->count(); // 查询满足要求的总记录数
        $Page = new \Think\Page($count, 15); // 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show();
        $list = $remittance_table->order('id desc')->where($map)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('count', $count);
        $this->assign('page', $show);
        $this->assign('list', $list);
        $this->assign('arr', $search);
        $this->display();
    }

    //电子积分
    public function listcoinbillitem() {

        $uid = session('uid');
        $bonus_table = M('bonus');
        $map['uid'] = $uid;
        $map['type'] = 6;
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
        if (!empty($_REQUEST['search_status'])) {
            $map['status'] = $_REQUEST['search_status'];
            $search['search_status'] = $_REQUEST['search_status'];
        }
        $count = $bonus_table->where($map)->count(); // 查询满足要求的总记录数
        $Page = new \Think\Page($count, 15); // 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show();
        $list = $bonus_table->order('id desc')->where($map)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('count', $count);
        $this->assign('page', $show);
        $this->assign('list', $list);
        $this->assign('arr', $search);
        $this->display();
    }

    //奖金明细
    public function listbonusbillitem() {
        $uid = session('uid');
        $bonus_table = M('bonus');
        $map['uid'] = $uid;
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
        if (!empty($_REQUEST['search_type'])) {
            $map['type'] = $_REQUEST['search_type'];
            $search['search_type'] = $_REQUEST['search_type'];
        }
        
        $currency = getbonustype();
        $count = $bonus_table->where($map)->count(); // 查询满足要求的总记录数
        $Page = new \Think\Page($count, 15); // 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show();
        $list = $bonus_table->order('id desc')->where($map)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('count', $count);
        $this->assign('page', $show);
        $this->assign('list', $list);
        $this->assign('arr', $search);
        $this->assign('currency', $currency);
        $this->display();
    }

    //业绩明细查询
    public function listnewtotal() {
        $uid = session('uid');
        $newtotalbonus_table = M('newtotalbonus');
        $map['uid'] = $uid;
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
        $count = $newtotalbonus_table->where($map)->count(); // 查询满足要求的总记录数
        $Page = new \Think\Page($count, 16); // 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show();
        $list = $newtotalbonus_table->order('id desc')->where($map)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $count = count($list);
        for ($i = 0; $i < $count; $i++) {
            $list[$i]['message'] = substr_replace($list[$i]['message'], "***", 3, 3);
        }
        $this->assign('count', $count);
        $this->assign('page', $show);
        $this->assign('list', $list);
        $this->assign('arr', $search);
        $this->display();
    }

    //业绩汇总查询
    public function listnewpv() {
        $uid = session('uid');
        $addtotal_table = M('newtotal');
        $map['uid'] = $uid;
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
        $count = $addtotal_table->where($map)->count(); // 查询满足要求的总记录数
        $Page = new \Think\Page($count, 16); // 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show();
        $list = $addtotal_table->order('id desc')->where($map)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('count', $count);
        $this->assign('page', $show);
        $this->assign('list', $list);
        $this->assign('arr', $search);
        $this->display();
    }

    //团队业绩明细查询
    public function grouptotal() {
        $uid = session('uid');
        $groupday_table = M('groupday');
        $map['uid'] = $uid;
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
        $count = $groupday_table->where($map)->count(); // 查询满足要求的总记录数
        $Page = new \Think\Page($count, 16); // 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show();
        $list = $groupday_table->order('id desc')->where($map)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $count = count($list);
        for ($i = 0; $i < $count; $i++) {
            $list[$i]['message'] = substr_replace($list[$i]['message'], "***", 3, 3);
        }
        $this->assign('count', $count);
        $this->assign('page', $show);
        $this->assign('list', $list);
        $this->assign('arr', $search);
        $this->display();
    }

    //团队月业绩汇总查询
    public function groupnewpv() {
        $uid = session('uid');
        $groupmoth_table = M('groupmoth');
        $map['uid'] = $uid;
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
        $count = $groupmoth_table->where($map)->count(); // 查询满足要求的总记录数
        $Page = new \Think\Page($count, 16); // 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show();
        $list = $groupmoth_table->order('id desc')->where($map)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('count', $count);
        $this->assign('page', $show);
        $this->assign('list', $list);
        $this->assign('arr', $search);
        $this->display();
    }

    //添加提现
    public function addWithdrawals() {
        $type = allmomeytype();
        $todayTime = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
        $beginThismonth = mktime(0, 0, 0, date('m'), 1, date('Y'));  //当月第一天时间戳
        $week = mktime(0, 0, 0, date('m'), date('d') - date('w'), date('Y')); //当周第一天
        $uid = session('uid');
        $member_table = M('member');
        $withdrawals_table = M('withdrawals');
        $bonus_table = M('bonus');
        $member_table->startTrans();
        $userInfo = $member_table->field('id,bank,bankno,account_name,integral,allbonus,alljingtaibonus,threepassword')->find($uid);
        $data = getbonusparam(); //获取的奖金比例参数
        if (IS_POST) {
            $bizhong = I('post.bizhong', '', 'htmlspecialchars');
            $money = I('post.money', '', 'htmlspecialchars');
            $threepassword = I('post.threepassword', '', 'htmlspecialchars');
            $poundage = $data['tixian'] * $money; //提现扣款金额
            $truemoney = $poundage + $money;

            if (empty($userInfo['bankno'])) {
                $json['status'] = 2;
                $json['msg'] = '银行卡号不存在，请先完善个人资料';
                echo json_encode($json);
                exit;
            }

            if ($money < 0 || floor($money) != $money || !is_numeric($money)) {
                $json['status'] = 2;
                $json['msg'] = '请输入有效的值';
                echo json_encode($json);
                exit;
            }
            if (empty($type[$bizhong])) {
                $json['status'] = 2;
                $json['msg'] = '货币类型不存在';
                echo json_encode($json);
                exit;
            }
            if ($userInfo['threepassword'] != fun_md5($threepassword)) {
                $json['status'] = 2;
                $json['msg'] = '三级密码不正确';
                echo json_encode($json);
                exit;
            }
            if (!$member_table->autoCheckToken($_POST)) {
                $json['status'] = 2;
                $json['msg'] = '不要重复提交';
                echo json_encode($json);
                exit;
            }
            switch ($bizhong) {
                case 19:
                    if ($truemoney > $userInfo['allbonus']) {
                        $json['status'] = 2;
                        $json['msg'] = '金额不足，无法提现';
                        echo json_encode($json);
                        exit;
                    }
                    $allusermoney = $userInfo['allbonus'] - $truemoney;
                    $relust1 = $member_table->save(array('id' => $uid, 'allbonus' => $allusermoney));
                    $relust2 = $bonus_table->add(array('uid' => $uid, 'type' => $bizhong, 'expend' => $truemoney, 'status' => '2', 'balance' => $allusermoney, 'message' => '会员提现', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '10'));

                    $relust3 = $withdrawals_table->add(array('type' => $bizhong, 'bankname' => $userInfo['bank'], 'bankno' => $userInfo['bankno'], 'uid' => $uid, 'account_name' => $userInfo['account_name'], 'poundage' => $poundage, 'money' => $money, 'truemoney' => $truemoney, 'create_date' => time(), 'status' => 1));

                    break;
                case 20:
                    if ($truemoney > $userInfo['alljingtaibonus']) {
                        $json['status'] = 2;
                        $json['msg'] = '金额不足，无法提现';
                        echo json_encode($json);
                        exit;
                    }
                    $allusermoney = $userInfo['alljingtaibonus'] - $truemoney;
                    $relust1 = $member_table->save(array('id' => $uid, 'alljingtaibonus' => $allusermoney));
                    $relust2 = $bonus_table->add(array('uid' => $uid, 'type' => $bizhong, 'expend' => $truemoney, 'status' => '2', 'balance' => $allusermoney, 'message' => '会员提现', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '10'));

                    $relust3 = $withdrawals_table->add(array('type' => $bizhong, 'bankname' => $userInfo['bank'], 'bankno' => $userInfo['bankno'], 'uid' => $uid, 'account_name' => $userInfo['account_name'], 'poundage' => $poundage, 'money' => $money, 'truemoney' => $truemoney, 'create_date' => time(), 'status' => 1));


                    break;
            }
            if ($relust1 && $relust2 && $relust3) {
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



        $this->assign('type', $type);
        $this->assign('data', $data);
        $this->assign('userInfo', $userInfo);
        $this->display();
    }

    //添加汇款通知
    public function addRemit() {
        $uid = session('uid');
        $banks_table = M('banks');
        $remittance_table = M('remittance');
        if (IS_AJAX) {
            $bank = I('post.bankno', '', 'htmlspecialchars');
            $remitter = I('post.remitter', '', 'htmlspecialchars');
            $money = I('post.money', '', 'htmlspecialchars');
            $remittance_date = I('post.remittance_date', '', 'htmlspecialchars');
            $message = I('post.message', '', 'htmlspecialchars');
            $bankno = $banks_table->where(array('is_hied' => '1'))->find($bank);
            if (!$bankno) {
                $json['status'] = 2;
                $json['msg'] = '汇入银行号不存在';
                echo json_encode($json);
                exit;
            }
            if (!$remittance_table->autoCheckToken($_POST)) {
                $json['status'] = 2;
                $json['msg'] = '请不要重复提交';
                echo json_encode($json);
                exit;
            }
            $relust = $remittance_table->add(array('receive' => $bankno['name'], 'uid' => $uid, 'bankno' => $bankno['bankno'], 'bank' => $bankno['bankname'], 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'remittance_date' => $remittance_date, 'remitter' => $remitter, 'money' => $money, 'message' => $message));
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


        $bankslist = $banks_table->where(array('is_hied' => 1))->select();
        $this->assign('bankslist', $bankslist);
        $this->display();
    }

//分红积分
    public function jingtaibonus() {
        $uid = session('uid');
        $nowmonth = mktime(0, 0, 0, date('m'), 1, date('Y')); //当月第一天的时间戳
        $mothtime = array(); //十二个月的时间
        $m = date('m');
        for ($i = 1; $i < 13; $i++) {
            $times = mktime(0, 0, 0, $i, 1, date('Y'));

            array_push($mothtime, $times);
        }
        $bonus_table = M('bonus');
        $pricemonth_table = M('pricemonth');
        $monthmoney = '';

        for ($i = 0; $i < 12; $i++) {
            $list = $bonus_table->where(array('type' => '14', 'status' => '1', 'month' => $mothtime[$i], 'uid' => $uid, 'action' => '3'))->sum('income'); //获取到今年收入的分红积分 

            $list1 = $pricemonth_table->where(array('month' => $mothtime[$i]))->find();
            if ($list1['month'] > $nowmonth) {
                $list1 = null;
            }
            if ($i < $m) {
                $null = '0';
                $money = (empty($list)) ? $null : $list;

                $price = (empty($list1)) ? $null : $list1['price'];

                $monthmoney.=$money . ',';
                $monthprice.=$price . ',';
            } else {
                $null = 'null';
                $money = (empty($list)) ? $null : $list;
                $price = (empty($list1)) ? $null : $list1['price'];
                $monthmoney.=$money . ',';
                $monthprice.=$price . ',';
            }
        }
        $monthjson = trim($monthmoney, ',');
        $pricejson = trim($monthprice, ',');
        for ($i = 1; $i < 13; $i++) {
            if ($i < $m) {
                $times = mktime(0, 0, 0, $i, 1, date("Y"));
                array_push($mothtime, $times);
            }
        }
        $this->assign('monthjson', $monthjson);
        $this->assign('pricejson', $pricejson);
        $this->display();
    }

    //溢价积分
    public function zenzhibonus() {
        //$uis=  session('uid');

        $bonus_table = M('bonus');
        $priceweek_table = M('priceweek');
        $weektime = array();
        $week = mktime(0, 0, 0, date('m'), date('d') - date('w'), date('Y')); //当周第一天(星期日)
        $weeks = $week - 60 * 60 * 24 * 7 * 7;
        for ($i = 0; $i < 7; $i++) {
            $weeks+=60 * 60 * 24 * 7;
            $weektime[$i] = $weeks;
            $date.="'" . date('Y-m-d ', $weeks) . "'" . ',';
        }
        for ($j = 0; $j < 7; $j++) {

            $list1 = $priceweek_table->field('price')->where(array('week' => $weektime[$j]))->find();
            $null = '0';
            $price = (empty($list1)) ? $null : $list1['price'];
            $weekprice.=$price . ',';
        }
        $pricejson = trim($weekprice, ',');
        $weekdate = trim($date, ',');

        $this->assign('weekdate', $date);
        $this->assign('pricejson', $pricejson);




        $uid = session('uid');
        $integral_table = M('integral');
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
        $map['uid'] = $uid;
        $member_table = M('member');
        $count = $integral_table->where($map)->count(); // 查询满足要求的总记录数
        $Page = new \Think\Page($count, 5); // 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show();
        $list = $integral_table->order('id desc')->where($map)->limit($Page->firstRow . ',' . $Page->listRows)->select();

        $count = count($list);
        for ($i = 0; $i < $count; $i++) {
            $info = $member_table->field('username')->find($list[$i]['uid']);
            $list[$i]['username'] = $info['username'];
        }
        $status = array('1' => '等待审核', '2' => '处理完成', '3' => '拒绝通过');
        $this->assign('count', $count);
        $this->assign('page', $show);
        $this->assign('list', $list);
        $this->assign('arr', $search);
        $this->assign('status', $status);
        $this->display();
    }

    //合并到动态钱包
//    public function mergedongtaimoney() {
//        $todayTime = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
//        $beginThismonth = mktime(0, 0, 0, date('m'), 1, date('Y'));  //当月第一天时间戳
//        $week = mktime(0, 0, 0, date('m'), date('d') - date('w'), date('Y')); //当周第一天
//        $member_table = M('member');
//        $bonus_table = M('bonus');
//        $member_table->startTrans();
//        $uid = session('uid');
//        $type = dongtai();
//        if (IS_POST) {
//            $bizhong = I('post.bizhong', '', 'htmlspecialchars');
//            $money = I('post.money', '', 'htmlspecialchars');
//            $threepassword = I('post.threepassword', '', 'htmlspecialchars');
//            $userInfo = $member_table->field('lingdaoBonus,guanliBonus,jinjiBonus,quanqiufenhongBonus,hongbaobonus,allbonus,threepassword')->find($uid);
//            if ($money < 0 || floor($money) != $money || !is_numeric($money)) {
//                $json['status'] = 2;
//                $json['msg'] = '请输入有效的值';
//                echo json_encode($json);
//                exit;
//            }
//            if (empty($type[$bizhong])) {
//
//                $json['status'] = 2;
//                $json['msg'] = '积分类型不存在';
//                echo json_encode($json);
//                exit;
//            }
//            if ($userInfo['threepassword'] != fun_md5($threepassword)) {
//                $json['status'] = 2;
//                $json['msg'] = '三级密码不正确';
//                echo json_encode($json);
//                exit;
//            }
//            if (!$member_table->autoCheckToken($_POST)) {
//                $json['status'] = 2;
//                $json['msg'] = '不要重复提交';
//                echo json_encode($json);
//                exit;
//            }
//
//            $userallbonus = $userInfo['allbonus'] + $money;
//            switch ($bizhong) {
//                case 2:
//                    if ($money > $userInfo['lingdaobonus']) {
//                        $json['status'] = 2;
//                        $json['msg'] = '金额不足';
//                        echo json_encode($json);
//                        exit;
//                    }
//
//                    $usermoeny = $userInfo['lingdaoBonus'] - $money;
//                    $res1 = $member_table->save(array('id' => $uid, 'allbonus' => $userallbonus, 'lingdaoBonus' => $usermoeny));
//                    $res2 = $bonus_table->add(array('uid' => $uid, 'type' => '19', 'income' => $money, 'status' => '1', 'balance' => $userallbonus, 'message' => '开发奖转入', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '13')); //13合并转入
//                    $res3 = $bonus_table->add(array('uid' => $uid, 'type' => $bizhong, 'expend' => $money, 'status' => '2', 'balance' => $usermoeny, 'message' => '转入到市场积分', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '14')); //14合并转出
//
//                    break;
//                case 3:
//                    if ($money > $userInfo['guanlibonus']) {
//                        $json['status'] = 2;
//                        $json['msg'] = '金额不足';
//                        echo json_encode($json);
//                        exit;
//                    }
//
//                    $usermoeny = $userInfo['guanlibonus'] - $money;
//                    $res1 = $member_table->save(array('id' => $uid, 'allbonus' => $userallbonus, 'guanliBonus' => $usermoeny));
//                    $res2 = $bonus_table->add(array('uid' => $uid, 'type' => '19', 'income' => $money, 'status' => '1', 'balance' => $userallbonus, 'message' => '管理奖转入', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '13')); //13合并转入
//                    $res3 = $bonus_table->add(array('uid' => $uid, 'type' => $bizhong, 'expend' => $money, 'status' => '2', 'balance' => $usermoeny, 'message' => '转入到市场积分', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '14')); //14合并转出
//
//
//                    break;
//                case 4:
//                    if ($money > $userInfo['jinjibonus']) {
//                        $json['status'] = 2;
//                        $json['msg'] = '金额不足';
//                        echo json_encode($json);
//                        exit;
//                    }
//
//                    $usermoeny = $userInfo['jinjibonus'] - $money;
//                    $res1 = $member_table->save(array('id' => $uid, 'allbonus' => $userallbonus, 'jinjiBonus' => $usermoeny));
//                    $res2 = $bonus_table->add(array('uid' => $uid, 'type' => '19', 'income' => $money, 'status' => '1', 'balance' => $userallbonus, 'message' => '晋升奖转入', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '13')); //13合并转入
//                    $res3 = $bonus_table->add(array('uid' => $uid, 'type' => $bizhong, 'expend' => $money, 'status' => '2', 'balance' => $usermoeny, 'message' => '转入到市场积分', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '14')); //14合并转出
//
//
//                    break;
//                case 5:
//                    if ($money > $userInfo['quanqiufenhongbonus']) {
//                        $json['status'] = 2;
//                        $json['msg'] = '金额不足';
//                        echo json_encode($json);
//                        exit;
//                    }
//
//                    $usermoeny = $userInfo['quanqiufenhongbonus'] - $money;
//                    $res1 = $member_table->save(array('id' => $uid, 'allbonus' => $userallbonus, 'quanqiufenhongBonus' => $usermoeny));
//                    $res2 = $bonus_table->add(array('uid' => $uid, 'type' => '19', 'income' => $money, 'status' => '1', 'balance' => $userallbonus, 'message' => '全球分红转入', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '13')); //13合并转入
//                    $res3 = $bonus_table->add(array('uid' => $uid, 'type' => $bizhong, 'expend' => $money, 'status' => '2', 'balance' => $usermoeny, 'message' => '转入到市场积分', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '14')); //14合并转出
//
//
//                    break;
//                case 12:
//                    if ($money > $userInfo['hongbaobonus']) {
//                        $json['status'] = 2;
//                        $json['msg'] = '金额不足';
//                        echo json_encode($json);
//                        exit;
//                    }
//
//                    $usermoeny = $userInfo['hongbaobonus'] - $money;
//                    $res1 = $member_table->save(array('id' => $uid, 'allbonus' => $userallbonus, 'hongbaobonus' => $usermoeny));
//                    $res2 = $bonus_table->add(array('uid' => $uid, 'type' => '19', 'income' => $money, 'status' => '1', 'balance' => $userallbonus, 'message' => '红包奖转入', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '13')); //13合并转入
//                    $res3 = $bonus_table->add(array('uid' => $uid, 'type' => $bizhong, 'expend' => $money, 'status' => '2', 'balance' => $usermoeny, 'message' => '转入到市场积分', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '14')); //14合并转出
//
//
//                    break;
//            }
//            if ($res1 || $res2 || $res3) {
//                $member_table->commit();
//                $json['status'] = 1;
//                $json['msg'] = '操作成功';
//                echo json_encode($json);
//                exit;
//            } else {
//                $member_table->rollback();
//                $json['status'] = 2;
//                $json['msg'] = '操作失败';
//                echo json_encode($json);
//                exit;
//            }
//        }
//
//
//        $this->assign('type', $type);
//        $this->display();
//    }
    //一键合并动态钱包
    public function mergedongtaimoney() {

        $member_table = M('member');
        $uid = session('uid');
        $userInfo = $member_table->field('threepassword,lingdaoBonus,guanliBonus,jinjiBonus,quanqiufenhongBonus,yuexinBonus,hongbaobonus,allbonus')->find($uid);
        if (IS_POST) {
            $threepassword = I('post.threepassword', '', 'htmlspecialchars');

            if ($userInfo['threepassword'] != fun_md5($threepassword)) {
                $json['status'] = 2;
                $json['msg'] = '三级密码不正确';
                echo json_encode($json);
                exit;
            }
            if (!$member_table->autoCheckToken($_POST)) {
                $json['status'] = 2;
                $json['msg'] = '不要重复提交';
                echo json_encode($json);
                exit;
            }
            $this->hebindongtaibonus();
        }
        $this->assign('userInfo', $userInfo);
        $this->display();
    }

    protected function hebindongtaibonus() {
        $todayTime = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
        $beginThismonth = mktime(0, 0, 0, date('m'), 1, date('Y'));  //当月第一天时间戳
        $week = mktime(0, 0, 0, date('m'), date('d') - date('w'), date('Y')); //当周第一天
        $member_table = M('member');
        $bonus_table = M('bonus');
        $member_table->startTrans();
        $uid = session('uid'); //货币类型 1快速奖，2开发奖，3管理奖，4晋升奖，5全球分红,6电子积分，7个人所得税，8车奖，9重复消费,10赠送积分 11.别墅基金12红包奖，13溢价积分，14分红积分 15旅游基金16基金17月薪奖18月薪奖池19活动钱包20静态钱包21月薪奖池22线上充值现金流水 
        $userInfo = $member_table->field('lingdaoBonus,guanliBonus,jinjiBonus,quanqiufenhongBonus,yuexinBonus,hongbaobonus,allbonus,daishubonus')->find($uid);
        if (!empty($userInfo['lingdaobonus']) && $userInfo['lingdaobonus'] > 0) {
            $bonus_table->add(array('uid' => $uid, 'type' => '2', 'expend' => $userInfo['lingdaobonus'], 'status' => '2', 'balance' => 0, 'message' => '转入到市场积分', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '14')); //14合并转出
        }
        if (!empty($userInfo['guanlibonus']) && $userInfo['guanlibonus'] > 0) {
            $bonus_table->add(array('uid' => $uid, 'type' => '3', 'expend' => $userInfo['guanlibonus'], 'status' => '2', 'balance' => 0, 'message' => '转入到市场积分', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '14')); //14合并转出
        }
        if (!empty($userInfo['jinjibonus']) && $userInfo['jinjibonus'] > 0) {
            $bonus_table->add(array('uid' => $uid, 'type' => '4', 'expend' => $userInfo['jinjibonus'], 'status' => '2', 'balance' => 0, 'message' => '转入到市场积分', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '14')); //14合并转出
        }
        if (!empty($userInfo['quanqiufenhongbonus']) && $userInfo['quanqiufenhongbonus'] > 0) {
            $bonus_table->add(array('uid' => $uid, 'type' => '5', 'expend' => $userInfo['quanqiufenhongbonus'], 'status' => '2', 'balance' => 0, 'message' => '转入到市场积分', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '14')); //14合并转出
        }
        if (!empty($userInfo['yuexinbonus']) && $userInfo['yuexinbonus'] > 0) {
            $bonus_table->add(array('uid' => $uid, 'type' => '17', 'expend' => $userInfo['yuexinbonus'], 'status' => '2', 'balance' => 0, 'message' => '转入到市场积分', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '14')); //14合并转出
        }
        if (!empty($userInfo['hongbaobonus']) && $userInfo['hongbaobonus'] > 0) {
            $bonus_table->add(array('uid' => $uid, 'type' => '12', 'expend' => $userInfo['hongbaobonus'], 'status' => '2', 'balance' => 0, 'message' => '转入到市场积分', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '14')); //14合并转出
        }
        if (!empty($userInfo['daishubonus']) && $userInfo['daishubonus'] > 0) {
            $bonus_table->add(array('uid' => $uid, 'type' => '23', 'expend' => $userInfo['daishubonus'], 'status' => '2', 'balance' => 0, 'message' => '转入到市场积分', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '14')); //14合并转出
        }
        $shouru = $userInfo['lingdaobonus'] + $userInfo['guanlibonus'] + $userInfo['jinjibonus'] + $userInfo['quanqiufenhongbonus'] + $userInfo['yuexinbonus'] + $userInfo['hongbaobonus'] + $userInfo['daishubonus'];
        $userallbonus = $userInfo['lingdaobonus'] + $userInfo['guanlibonus'] + $userInfo['jinjibonus'] + $userInfo['quanqiufenhongbonus'] + $userInfo['yuexinbonus'] + $userInfo['hongbaobonus'] + $userInfo['daishubonus'] + $userInfo['allbonus'];
        if ($shouru != 0) {
            $relust = $bonus_table->add(array('uid' => $uid, 'type' => '19', 'income' => $shouru, 'status' => '1', 'balance' => $userallbonus, 'message' => '动态奖金合并', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '13')); //13合并转入
            $relust1 = $member_table->save(array('id' => $uid, 'lingdaoBonus' => '0', 'guanliBonus' => '0', 'jinjiBonus' => '0', 'quanqiufenhongBonus' => '0', 'yuexinBonus' => '0', 'hongbaobonus', 'allbonus' => $userallbonus));
            if ($relust && $relust1) {
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
        } else {
            $member_table->rollback();
            $json['status'] = 2;
            $json['msg'] = '没有要合并的金额';
            echo json_encode($json);
            exit;
        }
    }

    //合并到静态钱包
//    public function mergejingtaimoney() {
//        $todayTime = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
//        $beginThismonth = mktime(0, 0, 0, date('m'), 1, date('Y'));  //当月第一天时间戳
//        $week = mktime(0, 0, 0, date('m'), date('d') - date('w'), date('Y')); //当周第一天
//        $member_table = M('member');
//        $bonus_table = M('bonus');
//        $member_table->startTrans();
//        $uid = session('uid');
//        $type = jingtai();
//        if (IS_POST) {
//            $bizhong = I('post.bizhong', '', 'htmlspecialchars');
//            $money = I('post.money', '', 'htmlspecialchars');
//            $threepassword = I('post.threepassword', '', 'htmlspecialchars');
//            $userInfo = $member_table->field('zengzhibonus,fenhongbonus,allbonus,threepassword')->find($uid);
//            if ($money < 0 || floor($money) != $money || !is_numeric($money)) {
//                $json['status'] = 2;
//                $json['msg'] = '请输入有效的值';
//                echo json_encode($json);
//                exit;
//            }
//            if (empty($type[$bizhong])) {
//
//                $json['status'] = 2;
//                $json['msg'] = '积分类型不存在';
//                echo json_encode($json);
//                exit;
//            }
//            if ($userInfo['threepassword'] != fun_md5($threepassword)) {
//                $json['status'] = 2;
//                $json['msg'] = '三级密码不正确';
//                echo json_encode($json);
//                exit;
//            }
//            if (!$member_table->autoCheckToken($_POST)) {
//                $json['status'] = 2;
//                $json['msg'] = '不要重复提交';
//                echo json_encode($json);
//                exit;
//            }
//
//            $userallbonus = $userInfo['allbonus'] + $money;
//            switch ($bizhong) {
//                case 13:
//                    if ($money > $userInfo['zengzhibonus']) {
//                        $json['status'] = 2;
//                        $json['msg'] = '金额不足';
//                        echo json_encode($json);
//                        exit;
//                    }
//
//                    $usermoeny = $userInfo['zengzhibonus'] - $money;
//                    $res1 = $member_table->save(array('id' => $uid, 'allbonus' => $userallbonus, 'zengzhibonus' => $usermoeny));
//                    $res2 = $bonus_table->add(array('uid' => $uid, 'type' => '20', 'income' => $money, 'status' => '1', 'balance' => $userallbonus, 'message' => '溢价积分转入', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '13')); //13合并转入
//                    $res3 = $bonus_table->add(array('uid' => $uid, 'type' => $bizhong, 'expend' => $money, 'status' => '2', 'balance' => $usermoeny, 'message' => '转入到活动积分', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '14')); //14合并转出
//
//                    break;
//                case 14:
//                    if ($money > $userInfo['fenhongbonus']) {
//                        $json['status'] = 2;
//                        $json['msg'] = '金额不足';
//                        echo json_encode($json);
//                        exit;
//                    }
//
//                    $usermoeny = $userInfo['fenhongbonus'] - $money;
//                    $res1 = $member_table->save(array('id' => $uid, 'allbonus' => $userallbonus, 'fenhongbonus' => $usermoeny));
//                    $res2 = $bonus_table->add(array('uid' => $uid, 'type' => '20', 'income' => $money, 'status' => '1', 'balance' => $userallbonus, 'message' => '分红积分转入', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '13')); //13合并转入
//                    $res3 = $bonus_table->add(array('uid' => $uid, 'type' => $bizhong, 'expend' => $money, 'status' => '2', 'balance' => $usermoeny, 'message' => '转入到活动积分', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '14')); //14合并转出
//
//
//                    break;
//            }
//            if ($res1 || $res2 || $res3) {
//                $member_table->commit();
//                $json['status'] = 1;
//                $json['msg'] = '操作成功';
//                echo json_encode($json);
//                exit;
//            } else {
//                $member_table->rollback();
//                $json['status'] = 2;
//                $json['msg'] = '操作失败';
//                echo json_encode($json);
//                exit;
//            }
//        }
//
//
//        $this->assign('type', $type);
//        $this->display();
//    }
    //一键合并到静态钱包
    public function mergejingtaimoney() {

        $member_table = M('member');
        $uid = session('uid');
        $userInfo = $member_table->field('threepassword,zengzhibonus,fenhongbonus,alljingtaibonus')->find($uid);
        if (IS_POST) {
            $threepassword = I('post.threepassword', '', 'htmlspecialchars');

            if ($userInfo['threepassword'] != fun_md5($threepassword)) {
                $json['status'] = 2;
                $json['msg'] = '三级密码不正确';
                echo json_encode($json);
                exit;
            }
            if (!$member_table->autoCheckToken($_POST)) {
                $json['status'] = 2;
                $json['msg'] = '不要重复提交';
                echo json_encode($json);
                exit;
            }
            $this->hebinjintaimoney();
        }
        $this->assign('userInfo', $userInfo);
        $this->display();
    }

    //合并静态奖
    protected function hebinjintaimoney() {
        $todayTime = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
        $beginThismonth = mktime(0, 0, 0, date('m'), 1, date('Y'));  //当月第一天时间戳
        $week = mktime(0, 0, 0, date('m'), date('d') - date('w'), date('Y')); //当周第一天
        $member_table = M('member');
        $bonus_table = M('bonus');
        $member_table->startTrans();
        $uid = session('uid'); //货币类型 1快速奖，2开发奖，3管理奖，4晋升奖，5全球分红,6电子积分，7个人所得税，8车奖，9重复消费,10赠送积分 11.别墅基金12红包奖，13溢价积分，14分红积分 15旅游基金16基金17月薪奖18月薪奖池19活动钱包20静态钱包21月薪奖池22线上充值现金流水 

        $userInfo = $member_table->field('zengzhibonus,fenhongbonus,alljingtaibonus')->find($uid);
        if (!empty($userInfo['zengzhibonus']) && $userInfo['zengzhibonus'] > 0) {
            $bonus_table->add(array('uid' => $uid, 'type' => '13', 'expend' => $userInfo['zengzhibonus'], 'status' => '2', 'balance' => 0, 'message' => '转入到活动积分', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '14')); //14合并转出
        }
        if (!empty($userInfo['fenhongbonus']) && $userInfo['fenhongbonus'] > 0) {
            $bonus_table->add(array('uid' => $uid, 'type' => '14', 'expend' => $userInfo['fenhongbonus'], 'status' => '2', 'balance' => 0, 'message' => '转入到活动积分', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '14')); //14合并转出
        }
        $shouru = $userInfo['zengzhibonus'] + $userInfo['fenhongbonus'];
        if ($shouru != 0) {
            $userallbonus = $userInfo['zengzhibonus'] + $userInfo['fenhongbonus'] + $userInfo['alljingtaibonus'];
            $relust = $bonus_table->add(array('uid' => $uid, 'type' => '20', 'income' => $shouru, 'status' => '1', 'balance' => $userallbonus, 'message' => '静态奖金合并', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '13')); //13合并转入
            $relust1 = $member_table->save(array('id' => $uid, 'zengzhibonus' => '0', 'fenhongbonus' => '0', 'alljingtaibonus' => $userallbonus));
            if ($relust && $relust1) {
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
        } else {
            $member_table->rollback();
            $json['status'] = 2;
            $json['msg'] = '没有要合并的金额';
            echo json_encode($json);
            exit;
        }
    }

    //添加卖出
    public function jifentixian() {
        $type = allmomeytype();
        $data = getbonusparam(); //获取的奖金比例参数
        $todayTime = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
        $beginThismonth = mktime(0, 0, 0, date('m'), 1, date('Y'));  //当月第一天时间戳
        $week = mktime(0, 0, 0, date('m'), date('d') - date('w'), date('Y')); //当周第一天
        $uid = session('uid');
        $member_table = M('member');
        $integral_table = M('integral');
        $bonus_table = M('bonus');
        $memberlevel_table = M('memberlevel');
        $priceweek_table = M('priceweek');
        $member_table->startTrans();
        $weekInfo = $priceweek_table->where(array('week' => $week))->find(); //获取到当周的单价信息


        $userInfo = $member_table->field('id,bank,bankno,account_name,integral,threepassword,level')->find($uid);

        $memberLevelInfo = $memberlevel_table->find($userInfo['level']); //获取到会员等级释放的比例
        $integral = intval($userInfo['integral'] * $memberLevelInfo['zhengzhiplace']); //本周释放的积分
        if (IS_POST) {
            $bizhong = 10;
            // $money = I('post.money', '', 'htmlspecialchars');
            $threepassword = I('post.threepassword', '', 'htmlspecialchars');
            $int_rel = $integral_table->field('id')->where(array('uid' => $uid, 'status' => 1))->find();
            $int_rel1 = $integral_table->field('id')->where(array('uid' => $uid, 'week' => $week, 'status' => array('in', '1,2')))->find();
            if (!$weekInfo) {
                $json['status'] = 2;
                $json['msg'] = '暂无报价，无法操作';
                echo json_encode($json);
                exit;
            }
            if ($int_rel) {
                $json['status'] = 2;
                $json['msg'] = '有一条单未完成，无法操作';
                echo json_encode($json);
                exit;
            }
            if ($int_rel1) {
                $json['status'] = 2;
                $json['msg'] = '本周已经申请过了';
                echo json_encode($json);
                exit;
            }

            if ($integral < 0 || floor($integral) != $integral || !is_numeric($integral)) {
                $json['status'] = 2;
                $json['msg'] = '请输入有效的值';
                echo json_encode($json);
                exit;
            }

            if ($userInfo['threepassword'] != fun_md5($threepassword)) {
                $json['status'] = 2;
                $json['msg'] = '三级密码不正确';
                echo json_encode($json);
                exit;
            }
            if (!$member_table->autoCheckToken($_POST)) {
                $json['status'] = 2;
                $json['msg'] = '不要重复提交';
                echo json_encode($json);
                exit;
            }
            switch ($bizhong) {
                case 10:
                    if ($integral > $userInfo['integral']) {
                        $json['status'] = 2;
                        $json['msg'] = '积分不足，无法提现';
                        echo json_encode($json);
                        exit;
                    }
                    $allusermoney = $userInfo['integral'] - $integral;
                    $relust1 = $member_table->save(array('id' => $uid, 'integral' => $allusermoney));
                    $relust2 = $bonus_table->add(array('uid' => $uid, 'type' => $bizhong, 'expend' => $integral, 'status' => '2', 'balance' => $allusermoney, 'message' => '卖出', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '10'));
                    $relust3 = $integral_table->add(array('univalent' => $weekInfo['price'], 'totalmoney' => $weekInfo['price'] * $integral, 'uid' => $uid, 'poundage' => $poundage, 'totalnum' => $integral, 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => time(), 'status' => 1, 'week' => $week));

                    break;
            }
            if ($relust1 && $relust2 && $relust3) {
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
        $price = (empty($weekInfo)) ? 暂无报价 : $weekInfo['price'];
        $this->assign('price', $price);
        $this->assign('poundage', $poundage);
        $this->assign('truemoney', $truemoney);
        $this->assign('integral', $integral);
        $this->assign('type', $type);
        $this->assign('data', $data);
        $this->assign('userInfo', $userInfo);
        $this->display();
    }

    //线上充值
    public function paylist() {
        $uid = session('uid');
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
        $map['uid'] = $uid;
        $count = $paylist_tabel->where($map)->count(); // 查询满足要求的总记录数
        $Page = new \Think\Page($count, 15); // 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show();
        $list = $paylist_tabel->order('id desc')->where($map)->limit($Page->firstRow . ',' . $Page->listRows)->select();
     
        $datainfo = getpayparam();
        $this->assign('url',$datainfo['message']);
        $this->assign('count', $count);
        $this->assign('page', $show);
        $this->assign('list', $list);
        $this->assign('arr', $search);
        $this->display();
    }

    //创建订单随机账号
    function create_orederno() {

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

    function create_randnum() {
        $str = "1,2,3,4,5,6,7,8,9,0";
        $list = explode(",", $str);
        $cmax = count($list) - 1;
        for ($i = 0; $i < 10; $i++) {
            $randnum = mt_rand(0, $cmax);
            $verifyCode .= $list[$randnum];
        }

        return $verifyCode;
    }

    public function check_money() {

        if (IS_POST) {

            $data = getbonusparam(); //获取的奖金比例参数
            //  $name = I('post.name', '', 'trim');
            $param = I('post.param', '', 'trim');
            if ($param % $data['chongzhibeishu'] != 0) {
                $json['status'] = 'n';
                $json['info'] = '请输入' . $data['chongzhibeishu'] . '倍数';
                echo json_encode($json);
                exit;
            }
            if ($param < $data['chongzhimin']) {
                $json['status'] = 'n';
                $json['info'] = '最低不能低于' . $data['chongzhimin'] . '￥';
                echo json_encode($json);
                exit;
            }
            if ($param > $data['chongzhimax']) {
                $json['status'] = 'n';
                $json['info'] = '最高不能高于' . $data['chongzhimax'] . '￥';
                echo json_encode($json);
                exit;
            } else {
                $json['status'] = 'y';
                $json['info'] = '信息校验成功';
                echo json_encode($json);
                exit;
            }
        }
    }

    public function pay() {

        if (IS_POST) {
            $data = getbonusparam(); //获取的奖金比例参数
            $uid = session('uid');
            $money = I('post.money', '', 'htmlspecialchars');
            $bank = I('post.bank', '', 'htmlspecialchars');
            $message = I('post.message', '', 'htmlspecialchars');
            $paylist_table = M('paylist');
            $bank_table = M('bank');
            $bankrel = $bank_table->where(array('bankname' => $bank, 'is_hide' => '1'))->find();
            $datainfo = getpayparam();

            if (!$bankrel) {
                $json['status'] = 2;
                $json['msg'] = '支付银行不存在';
                echo json_encode($json);
                exit;
            }
            if (empty($bankrel['banknum'])) {
                $json['status'] = 2;
                $json['msg'] = '机构代码未找到';
                echo json_encode($json);
                exit;
            }
            // floor($money) != $money ||
            if ($money < 0 || !is_numeric($money)) {
                $json['status'] = 2;
                $json['msg'] = '请输入有效的值';
                echo json_encode($json);
                exit;
            }
            if ($money % $data['chongzhibeishu'] != 0) {
                $json['status'] = 2;
                $json['msg'] = '必须是' . $data['chongzhibeishu'] . '倍数';
                echo json_encode($json);
                exit;
            }
            if ($money < $data['chongzhimin']) {
                $json['status'] = 2;
                $json['msg'] = '金额不能低于' . $data['chongzhimin'];
                echo json_encode($json);
                exit;
            }
            if ($money > $data['chongzhimax']) {
                $json['status'] = 2;
                $json['msg'] = '金额不能高于' . $data['chongzhimax'];
                echo json_encode($json);
                exit;
            }

            $BillNo = $this->create_orederno();  //[必填]订单号(商户自己产生：要求不重复)
            $rel = $paylist_table->add(array('uid' => $uid, 'billno' => $BillNo, 'amount' => $money, 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'status' => '2', 'message' => $message, 'banknum' => $bankrel['banknum'])); //添加该订单记录
            if ($rel) {

                $json['status'] = 1;
                $json['billno'] = $BillNo;
                $json['url']='http://'.$datainfo['message'];
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

        $banklist = findbank(); //获取银行
        $this->assign('banklist', $banklist);
        $this->display();
    }

  

}
