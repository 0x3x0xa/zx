<?php

namespace Home\Controller;

use Home\Controller\CommonController;

class MemberController extends CommonController {

    //升级完成马上给接点人加业绩
    public function addtotal($uid, $username, $id, $money, $region) {
        $todayTime = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
        $beginThismonth = mktime(0, 0, 0, date('m'), 1, date('Y'));  //当月第一天时间戳
        $member_table = M('member');
        $newtotal = M('newtotal');
        $newtotalbonus = M('newtotalbonus');
        $list = $member_table->field('junction,region')->find($id);
        $relust = $newtotal->field('id,leftgroupmoney,centergroupmoney,rightgroupmoney,totalmoney')->where(array('uid' => $id, 'addtime' => $todayTime))->find();
        if ($relust) {
            switch ($region) {
                case 0:$allmoney = $relust['leftgroupmoney'] + $money;
                    $newtotal->save(array('id' => $relust['id'], 'leftgroupmoney' => $allmoney, 'totalmoney' => $relust['totalmoney'] + $money));
                    $newtotalbonus->add(array('fid' => $uid, 'uid' => $id, 'type' => $region, 'money' => $money, 'balance' => $allmoney, 'message' => $username, 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime));
                    break;
                case 1:$allmoney = $relust['centergroupmoney'] + $money;
                    $newtotal->save(array('id' => $relust['id'], 'centergroupmoney' => $allmoney, 'totalmoney' => $relust['totalmoney'] + $money));
                    $newtotalbonus->add(array('fid' => $uid, 'uid' => $id, 'type' => $region, 'money' => $money, 'balance' => $allmoney, 'message' => $username, 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime));

                    break;
                case 2:$allmoney = $relust['rightgroupmoney'] + $money;
                    $newtotal->save(array('id' => $relust['id'], 'rightgroupmoney' => $allmoney, 'totalmoney' => $relust['totalmoney'] + $money));
                    $newtotalbonus->add(array('fid' => $uid, 'uid' => $id, 'type' => $region, 'money' => $money, 'balance' => $allmoney, 'message' => $username, 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime));

                    break;
            }
        } else {
            switch ($region) {
                case 0: $newtotal->add(array('uid' => $id, 'leftgroupmoney' => $money, 'totalmoney' => $money, 'month' => $beginThismonth, 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime));
                    $newtotalbonus->add(array('fid' => $uid, 'uid' => $id, 'type' => $region, 'money' => $money, 'balance' => $money, 'message' => $username, 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime));

                    break;
                case 1:$newtotal->add(array('uid' => $id, 'centergroupmoney' => $money, 'totalmoney' => $money, 'month' => $beginThismonth, 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime));
                    $newtotalbonus->add(array('fid' => $uid, 'uid' => $id, 'type' => $region, 'money' => $money, 'balance' => $money, 'message' => $username, 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime));

                    break;
                case 2:$newtotal->add(array('uid' => $id, 'rightgroupmoney' => $money, 'totalmoney' => $money, 'month' => $beginThismonth, 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime));
                    $newtotalbonus->add(array('fid' => $uid, 'uid' => $id, 'type' => $region, 'money' => $money, 'balance' => $money, 'message' => $username, 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime));

                    break;
            }
        }
        if ($list['junction'] != 0) {
            self::addtotal($uid, $username, $list['junction'], $money, $list['region']);
        }
    }

    /**
      每日的团队业绩
     * 
     * @param type $uid  用户id
     * @param type $rid  推荐人id
     * @param type $money 用户的钱
     *      /
     */
    public function groupbonus($uid, $username, $rid, $money) {
        $todayTime = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
        $beginThismonth = mktime(0, 0, 0, date('m'), 1, date('Y'));  //当月第一天时间戳
        $member_table = M('member');
        $groupday_table = M('groupday');
        $userInfo = $member_table->field('recommend')->find($rid);
        $groupday_table->add(array('fid' => $uid, 'uid' => $rid, 'money' => $money, 'message' => $username, 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth)); //团队业绩的来源
        if ($userInfo['recommend'] != 0) {
            self::groupbonus($uid, $username, $userInfo['recommend'], $money);
        }
    }

    //升级完成后马上加总市场业绩(市场历史总业绩)
    public function shichangbonus($uid, $id, $money, $region) {
        $shichangtotal_table = M('shichangtotal');
        $member_table = M('member');
        $list = $member_table->field('junction,region')->find($id); //找到接点人的接点人
        $relust = $shichangtotal_table->where(array('uid' => $id))->find(); //看接点人有没有记录
        if ($relust) {
            switch ($region) {
                case 0:$shichangtotal_table->save(array('id' => $relust['id'], 'left' => $relust['left'] + $money));
                    break;
                case 1:
                    $shichangtotal_table->save(array('id' => $relust['id'], 'center' => $relust['center'] + $money));
                    break;
                case 2:
                    $shichangtotal_table->save(array('id' => $relust['id'], 'right' => $relust['right'] + $money));
                    break;
            }
        } else {
            switch ($region) {
                case 0:$shichangtotal_table->add(array('uid' => $id, 'left' => $money));
                    break;
                case 1:
                    $shichangtotal_table->add(array('uid' => $id, 'center' => $money));
                    break;
                case 2:
                    $shichangtotal_table->add(array('uid' => $id, 'right' => $money));
                    break;
            }
        }
        if ($list['junction'] != 0) {
            self::shichangbonus($uid, $list['junction'], $money, $list['region']);
        }
    }

    public function return_money() {

        if (IS_POST) {
            $memberlevel_table = M('memberlevel');
            //  $name = I('post.name', '', 'trim');
            $param = I('post.param', '', 'trim');
            $relust = $memberlevel_table->field('registermoney')->where(array('id' => $param))->find();
            if ($relust) {
                $json['status'] = 'y';
                $json['info'] = '需要' . $relust['registermoney'] . '￥';
                echo json_encode($json);
                exit;
            } else {
                $json['status'] = 'n';
                $json['info'] = '等级不存在';
                echo json_encode($json);
                exit;
            }
        }
    }

    protected function getbili() {

        //返回晋级奖的比例
        $position_table = M('position');
        $onescale = $position_table->field('jinjiscale')->find(1); //获取一星比例
        $towscale = $position_table->field('jinjiscale')->find(2); //获取二星比例
        $threescale = $position_table->field('jinjiscale')->find(3); //获取三星比例
        $fourscale = $position_table->field('jinjiscale')->find(4); //获取四星比例
        $fivescale = $position_table->field('jinjiscale')->find(5); //获取五星比例
        $sixscale = $position_table->field('jinjiscale')->find(6); //获取董事比例

        $data = array('1' => $onescale['jinjiscale'], $towscale['jinjiscale'], $threescale['jinjiscale'], $fourscale['jinjiscale'], $fivescale['jinjiscale'], $sixscale['jinjiscale']);
        return $data;
    }

    public function jicha($rid, $money, $level, $dgscale) {


        $todayTime = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
        $beginThismonth = mktime(0, 0, 0, date('m'), 1, date('Y'));  //当月第一天时间戳
        $week = mktime(0, 0, 0, date('m'), date('d') - date('w'), date('Y')); //当周第一天
        $member_table = M('member');
        $bonus_table = M('bonus');
        $scale = $this->getbili();
        $row = $member_table->field('position,recommend,jinjiBonus')->find($rid);
        $Finance = A('Home/Finance');
        if ($level < $row['position']) {
            if ($row['position'] >= 0) {
                $getscale = floatval($scale[$row['position']]);
                $truescale = $getscale - $dgscale;

                $truemoney = $money * $truescale; //等级比例
                $usermoney = $Finance->splitbonus($rid, $truemoney);
                $userallmoney = $row['jinjibonus'] + $usermoney;
                $member_table->save(array('id' => $rid, 'jinjiBonus' => $userallmoney));
                $bonus_table->add(array('uid' => $rid, 'type' => '4', 'income' => $usermoney, 'balance' => $userallmoney, 'status' => '1', 'message' => '晋升奖', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '3'));
            }

            $dgscale = $dgscale + $truescale;
            $level = $row['position'];
        }
        if ($row['recommend'] != 0) {
            self::jicha($row['recommend'], $money, $level, $dgscale);
        }
    }

    //会员升级
    public function upgrade() {
        $data = getbonusparam(); //获取的奖金比例参数
        $uid = session('uid');
        $todayTime = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
        $beginThismonth = mktime(0, 0, 0, date('m'), 1, date('Y'));  //当月第一天时间戳
        $week = mktime(0, 0, 0, date('m'), date('d') - date('w'), date('Y')); //当周第一天
        $member_table = M('member');
        $memberlevel_table = M('memberlevel');
        $bonus_table = M('bonus');
        $upgrade_table = M('upgrade');
        $achievement_table = M('achievement');
        $member_table->startTrans();
        if (IS_POST) {
            $level = I('post.level', '', 'htmlspecialchars');
            $userInfo = $member_table->field('id,level,dianzimoney,integral,junction,region,username,recommend,cash,gouwujifen,gouwujuan')->find($uid);

            $memberlevelmoney = $memberlevel_table->field('title,registermoney')->find($level);
            $oldnamelevel = $memberlevel_table->field('title')->find($userInfo['level']);
            $upgradeInfo = $upgrade_table->field('id')->where(array('uid' => $uid))->find();
            if ($upgradeInfo) {
                $json['status'] = 2;
                $json['msg'] = '该账号已经升级过了 ！';
                echo json_encode($json);
                exit;
            }
            if ($level <= $userInfo['level']) {
                $json['status'] = 2;
                $json['msg'] = '不能等于或低于当前等级！';
                echo json_encode($json);
                exit;
            }
            if (!$memberlevelmoney) {
                $json['status'] = 2;
                $json['msg'] = '级别不存在！';
                echo json_encode($json);
                exit;
            }

            $xiaofeidianzibi = $memberlevelmoney['registermoney'] * 0.5; //需要消费消费的电子积分
            $xiaofeicash = $memberlevelmoney['registermoney'] * 0.5; //消费的现金
            if ($xiaofeidianzibi > $userInfo['dianzimoney']) {
                $buchongbi = $xiaofeidianzibi - $userInfo['dianzimoney']; //电子积分不足需要补充的币
                $xiaofeidianzibi = $userInfo['dianzimoney'];
                $xiaofeicash = $xiaofeicash + $buchongbi;
            }

            if ($xiaofeicash > $userInfo['cash']) {
                $json['status'] = 2;
                $json['msg'] = '现金积分不足！';
                echo json_encode($json);
                exit;
            }
            $alllevel = array('2', '3', '4'); //前三个级别的id号

            $alldianzimoney = $userInfo['dianzimoney'] - $xiaofeidianzibi;
            $allcashmoney = $userInfo['cash'] - $xiaofeicash;

            $allgouwujifen = $userInfo['gouwujifen'] + $memberlevelmoney['registermoney']; //购物积分
            if (in_array($level, $alllevel)) {
                $allintegral = $userInfo['integral'] + $memberlevelmoney['registermoney'] * 0.5; //赠送积分
                $allgouwujuan = $userInfo['gouwujuan'] + $memberlevelmoney['registermoney'] * 0.5; //购物卷
            } else {
                //level==5
                $gudingjifen = $data['zuankacanshu'];
                $allintegral = $userInfo['integral'] + $gudingjifen; //赠送积分
            }


            if ($userInfo['junction'] == '-1') {
                $jinfo = $this->returnid($userInfo['recommend'], $userInfo['region']); //获取到节点人id
                $relust1 = $member_table->save(array('id' => $userInfo['id'], 'dianzimoney' => $alldianzimoney, 'cash' => $allcashmoney, 'level' => $level,'gouwujifen'=>$allgouwujifen,'gouwujuan'=>$allgouwujuan, 'integral' => $allintegral, 'junction' => $jinfo['id'], 'region' => $jinfo['qu'], 'junctionLevel' => $jinfo['level']));
            } else {
                $relust1 = $member_table->save(array('id' => $userInfo['id'], 'dianzimoney' => $alldianzimoney, 'cash' => $allcashmoney, 'level' => $level,'gouwujifen'=>$allgouwujifen,'gouwujuan'=>$allgouwujuan, 'integral' => $allintegral));
            }
            $relust6 = $bonus_table->add(array('uid' => $userInfo['id'], 'type' => 22, 'expend' => $xiaofeicash, 'status' => 2, 'balance' => $allcashmoney, 'message' => '会员升级消耗', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '7'));
            if ($userInfo['dianzimoney'] != 0) {
                $relust2 = $bonus_table->add(array('uid' => $userInfo['id'], 'type' => 6, 'expend' => $xiaofeidianzibi, 'status' => 2, 'balance' => $alldianzimoney, 'message' => '会员升级消耗', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '7'));
            }
            if (in_array($level, $alllevel)) {
                $relust3 = $bonus_table->add(array('uid' => $userInfo['id'], 'type' => 10, 'income' => $memberlevelmoney['registermoney'] * 0.5, 'status' => 1, 'balance' => $allintegral, 'message' => '会员升级赠送积分', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '7'));
                $bonus_table->add(array('uid' => $userInfo['id'], 'type' => 25, 'income' => $memberlevelmoney['registermoney'] * 0.5, 'status' => 1, 'balance' => $allgouwujuan, 'message' => '会员升级赠送购物卷', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '7'));
            } else {
                //level =5 
                $relust3 = $bonus_table->add(array('uid' => $userInfo['id'], 'type' => 10, 'income' => $gudingjifen, 'status' => 1, 'balance' => $allintegral, 'message' => '会员升级赠送积分', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '7'));
            }
            $bonus_table->add(array('uid' => $userInfo['id'], 'type' => 24, 'income' => $memberlevelmoney['registermoney'], 'status' => 1, 'balance' => $allgouwujifen, 'message' => '会员升级赠送购物积分', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '7'));




            $relust4 = $upgrade_table->add(array('uid' => $userInfo['id'], 'money' => $memberlevelmoney['registermoney'], 'status' => '1', 'oldlevel' => $userInfo['level'], 'newlevel' => $level, 'oldname' => $oldnamelevel['title'], 'newname' => $memberlevelmoney['title'], 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'action' => '1'));
            $relust5 = $achievement_table->add(array('uid' => $userInfo['id'], 'money' => $memberlevelmoney['registermoney'], 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '1'));
            if ($userInfo['junction'] == '-1') {
                $this->addtotal($userInfo['id'], $userInfo['username'], $jinfo['id'], $memberlevelmoney['registermoney'], $jinfo['qu']); //每日市场业绩明细
                $this->shichangbonus($userInfo['id'], $jinfo['id'], $memberlevelmoney['registermoney'], $jinfo['qu']); //市场总业绩
                $this->groupbonus($userInfo['id'], $userInfo['username'], $userInfo['recommend'], $memberlevelmoney['registermoney']); //团队业绩每日明细
            } else {
                if ($userInfo['junction'] != '0') {
                    $this->addtotal($userInfo['id'], $userInfo['username'], $userInfo['junction'], $memberlevelmoney['registermoney'], $userInfo['region']); //每日市场业绩明细
                    $this->shichangbonus($userInfo['id'], $userInfo['junction'], $memberlevelmoney['registermoney'], $userInfo['region']); //市场总业绩
                    $this->groupbonus($userInfo['id'], $userInfo['username'], $userInfo['recommend'], $memberlevelmoney['registermoney']); //团队业绩每日明细
                }
            }
//            if ($userInfo['recommend'] != 0) {
//                $this->jicha($userInfo['recommend'], $memberlevelmoney['registermoney'], 0,0); //晋级奖
//            }
            if ($relust1 && $relust3 && $relust4 && $relust5 && $relust6) {

                $member_table->commit();
                $json['status'] = 1;
                $json['msg'] = '升级成功！';
                echo json_encode($json);
                exit;
            } else {
                $member_table->rollback();
                $json['status'] = 2;
                $json['msg'] = '升级失败！';
                echo json_encode($json);
                exit;
            }
        }

        $list = $memberlevel_table->where(array('status'=>'1'))->select();
        unset($list[0]);
        $this->assign('list', $list);
        $this->display();
    }

    //会员升级
    public function reupgrade() {
        $data = getbonusparam(); //获取的奖金比例参数
        $uplinename = I('post.username', '', 'htmlspecialchars');
        $ids = I('get.username', '', 'htmlspecialchars');
        $uid = session('uid');
        $todayTime = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
        $beginThismonth = mktime(0, 0, 0, date('m'), 1, date('Y'));  //当月第一天时间戳
        $week = mktime(0, 0, 0, date('m'), date('d') - date('w'), date('Y')); //当周第一天
        $member_table = M('member');
        $memberlevel_table = M('memberlevel');
        $bonus_table = M('bonus');
        $upgrade_table = M('upgrade');
        $achievement_table = M('achievement');
        $member_table->startTrans();
        $trueuserinfo = $member_table->where(array('recommend' => $uid, 'username' => $uplinename))->find(); //被升级会员的信息

        if (IS_POST) {
            if (!$trueuserinfo) {
                exit('会员不是你的直推');
            }
            $level = I('post.level', '', 'htmlspecialchars');
            $userInfo = $member_table->field('id,level,dianzimoney,integral,junction,region,username,recommend,cash,gouwujifen,gouwujuan')->find($uid); //本人的信息

            $memberlevelmoney = $memberlevel_table->field('title,registermoney')->find($level); //获取到升级的金额
            $oldnamelevel = $memberlevel_table->field('title')->find($userInfo['level']);
            $upgradeInfo = $upgrade_table->field('id')->where(array('uid' => $trueuserinfo['id']))->find(); //判断被升级会员
            if ($upgradeInfo) {
                $json['status'] = 2;
                $json['msg'] = '该账号已经升级过了 ！';
                echo json_encode($json);
                exit;
            }
            if ($level <= $trueuserinfo['level']) {
                $json['status'] = 2;
                $json['msg'] = '不能等于或低于当前等级！';
                echo json_encode($json);
                exit;
            }
            if (!$memberlevelmoney) {
                $json['status'] = 2;
                $json['msg'] = '级别不存在！';
                echo json_encode($json);
                exit;
            }

            $xiaofeidianzibi = $memberlevelmoney['registermoney'] * 0.5; //需要消费消费的电子积分
            $xiaofeicash = $memberlevelmoney['registermoney'] * 0.5; //消费的现金
            if ($xiaofeidianzibi > $userInfo['dianzimoney']) {
                $buchongbi = $xiaofeidianzibi - $userInfo['dianzimoney']; //电子积分不足需要补充的币
                $xiaofeidianzibi = $userInfo['dianzimoney'];
                $xiaofeicash = $xiaofeicash + $buchongbi;
            }

            if ($xiaofeicash > $userInfo['cash']) {
                $json['status'] = 2;
                $json['msg'] = '现金积分不足！';
                echo json_encode($json);
                exit;
            }

            $alllevel = array('2', '3', '4'); //前三个级别的id号
            $alldianzimoney = $userInfo['dianzimoney'] - $xiaofeidianzibi;
            $allcashmoney = $userInfo['cash'] - $xiaofeicash;

            $allgouwujifen = $trueuserinfo['gouwujifen'] + $memberlevelmoney['registermoney']; //购物积分
            if (in_array($level, $alllevel)) {
                $allintegral = $trueuserinfo['integral'] + $memberlevelmoney['registermoney'] * 0.5; //赠送积分
                $allgouwujuan = $trueuserinfo['gouwujuan'] + $memberlevelmoney['registermoney'] * 0.5; //购物卷
            } else {
                //level==5
                $gudingjifen = $data['zuankacanshu'];
                $allintegral = $trueuserinfo['integral'] + $gudingjifen; //赠送积分
            }


            if ($trueuserinfo['junction'] == '-1') {
                $jinfo = $this->returnid($trueuserinfo['recommend'], $trueuserinfo['region']); //获取到节点人id
                $relust1 = $member_table->save(array('id' => $trueuserinfo['id'], 'level' => $level,'gouwujifen'=>$allgouwujifen,'gouwujuan'=>$allgouwujuan, 'integral' => $allintegral, 'junction' => $jinfo['id'], 'region' => $jinfo['qu'], 'junctionLevel' => $jinfo['level']));
                $relust7 = $member_table->save(array('id' => $userInfo['id'], 'dianzimoney' => $alldianzimoney, 'cash' => $allcashmoney));
            }
            $relust6 = $bonus_table->add(array('uid' => $userInfo['id'], 'type' => 22, 'expend' => $xiaofeicash, 'status' => 2, 'balance' => $allcashmoney, 'message' => '升级' . $trueuserinfo['username'] . '会员消耗', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '7'));
            if ($userInfo['dianzimoney'] != 0) {
                $relust2 = $bonus_table->add(array('uid' => $userInfo['id'], 'type' => 6, 'expend' => $xiaofeidianzibi, 'status' => 2, 'balance' => $alldianzimoney, 'message' => '升级' . $trueuserinfo['username'] . '会员消耗', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '7'));
            }
            if (in_array($level, $alllevel)) {
                $relust3 = $bonus_table->add(array('uid' => $trueuserinfo['id'], 'type' => 10, 'income' => $memberlevelmoney['registermoney'] * 0.5, 'status' => 1, 'balance' => $allintegral, 'message' => '推荐人帮会员升级赠送积分', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '7'));
                $bonus_table->add(array('uid' => $trueuserinfo['id'], 'type' => 25, 'income' => $memberlevelmoney['registermoney'] * 0.5, 'status' => 1, 'balance' => $allgouwujuan, 'message' => '推荐人帮会员升级赠送购物卷', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '7'));
            } else {
                //level =5 
                $relust3 = $bonus_table->add(array('uid' => $trueuserinfo['id'], 'type' => 10, 'income' => $gudingjifen, 'status' => 1, 'balance' => $allintegral, 'message' => '会员升级赠送积分', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '7'));
            }
            $bonus_table->add(array('uid' => $trueuserinfo['id'], 'type' => 24, 'income' => $memberlevelmoney['registermoney'], 'status' => 1, 'balance' => $allgouwujifen, 'message' => '推荐人帮会员升级赠送购物积分', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '7'));




            $relust4 = $upgrade_table->add(array('uid' => $trueuserinfo['id'], 'money' => $memberlevelmoney['registermoney'], 'status' => '1', 'oldlevel' => $trueuserinfo['level'], 'newlevel' => $level, 'oldname' => $oldnamelevel['title'], 'newname' => $memberlevelmoney['title'], 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'action' => '1'));
            $relust5 = $achievement_table->add(array('uid' => $trueuserinfo['id'], 'money' => $memberlevelmoney['registermoney'], 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '1'));
            if ($trueuserinfo['junction'] == '-1') {
                $this->addtotal($trueuserinfo['id'], $trueuserinfo['username'], $jinfo['id'], $memberlevelmoney['registermoney'], $jinfo['qu']); //每日市场业绩明细
                $this->shichangbonus($trueuserinfo['id'], $jinfo['id'], $memberlevelmoney['registermoney'], $jinfo['qu']); //市场总业绩
                $this->groupbonus($trueuserinfo['id'], $trueuserinfo['username'], $trueuserinfo['recommend'], $memberlevelmoney['registermoney']); //团队业绩每日明细
            }
            if ($trueuserinfo['recommend'] != 0) {
                $this->jicha($trueuserinfo['recommend'], $memberlevelmoney['registermoney'], 0, 0); //晋级奖
            }
            if ($relust1 && $relust3 && $relust4 && $relust5 && $relust6 && $relust7) {

                $member_table->commit();
                $json['status'] = 1;
                $json['msg'] = '升级成功！';
                echo json_encode($json);
                exit;
            } else {
                $member_table->rollback();
                $json['status'] = 2;
                $json['msg'] = '升级失败！';
                echo json_encode($json);
                exit;
            }
        }

        $list = $memberlevel_table->where(array('status'=>'1'))->select();
        $uplineinfo = $member_table->where(array('recommend' => $uid, 'username' => $ids))->find(); //被升级会员的信息
        
        unset( $list[0]);
        $this->assign('trueuserinfo', $uplineinfo);
        $this->assign('list', $list);
        $this->display();
    }

    public function userpassword() {
        //设置二级和三级密码
        if (IS_POST) {
            $uid = session('uid');
            $member_table = M('member');
            $towpassword = I('post.towpassword', '', 'htmlspecialchars');
            $threepassword = I('post.threepassword', '', 'htmlspecialchars');
            if (empty($towpassword)) {
                $json['status'] = 2;
                $json['msg'] = '二级密码不能为空';
                echo json_encode($json);
                exit;
            }
            if (empty($threepassword)) {
                $json['status'] = 2;
                $json['msg'] = '三级密码不能为空';
                echo json_encode($json);
                exit;
            }
            if (!checkPwd($towpassword) || !checkPwd($threepassword)) {
                $json['status'] = 2;
                $json['msg'] = '密码格式不正确';
                echo json_encode($json);
                exit;
            }
            if (!$member_table->autoCheckToken($_POST)) {

                $json['status'] = 2;
                $json['msg'] = '不要重复提交';
                echo json_encode($json);
                exit;
            }
            $relust = $member_table->save(array('id' => $uid, 'towpassword' => fun_md5($towpassword), 'threepassword' => fun_md5($threepassword)));
            if ($relust) {
                $_SESSION['check_no'] = 1;
                $json['status'] = 1;
                $json['msg'] = '操作成功！';
                echo json_encode($json);
                exit;
            } else {
                $json['status'] = 2;
                $json['msg'] = '操作失败！';
                echo json_encode($json);
                exit;
            }
        } else {

            $this->display();
        }
    }

    public function checktowpassword() {
        if (IS_POST) {
            $uid = session('uid');
            $member_table = M('member');
            $towpassword = I('post.towpassword', '', 'htmlspecialchars');
            $userInfo = $member_table->field('towpassword')->find($uid);
            if (!$member_table->autoCheckToken($_POST)) {
                $json['status'] = 2;
                $json['msg'] = '不要重复提交';
                echo json_encode($json);
                exit;
            }
            if ($userInfo['towpassword'] == fun_md5($towpassword)) {
                session('check_no', 'no');
                $json['status'] = 1;
                $json['msg'] = '验证成功';
                echo json_encode($json);
                exit;
            } else {
                $json['status'] = 2;
                $json['msg'] = '二级密码不正确';
                echo json_encode($json);
                exit;
            }
        }
        $this->display();
    }

    //修改用户密码
    public function userpasswordedit() {
        if (IS_POST) {
            $member_table = M('member');
            $uid = session('uid');
            $type = I('post.type', '', 'htmlspecialchars');
            $oldpassword = I('post.oldpassword', '', 'htmlspecialchars');
            $password = I('post.newpassword', '', 'htmlspecialchars');
            $return = checkPwd($password);
            $return1 = checkPwd($oldpassword);
            if (!$return || !$return1) {
                $json['status'] = 2;
                $json['msg'] = '密码格式不正确';
                echo json_encode($json);
                exit;
            }
            if (!$member_table->autoCheckToken($_POST)) {
                $json['status'] = 2;
                $json['msg'] = '不要重复提交';
                echo json_encode($json);
                exit;
            }
            $newpwd = fun_md5($password);
            $oldpwd = fun_md5($oldpassword);
            if (!empty($newpwd)) {

                $userinfo = $member_table->field('password,towpassword,threepassword')->find($uid);
                switch ($type) {
                    case 1: if ($userinfo['password'] == $oldpwd) {
                            $relust = $member_table->save(array('id' => $uid, 'password' => $newpwd));
                        } else {
                            $json['status'] = 2;
                            $json['msg'] = '旧密码不正确,修改失败！';
                            echo json_encode($json);
                            exit;
                        }
                        break;
                    case 2:if ($userinfo['towpassword'] == $oldpwd) {
                            $relust = $member_table->save(array('id' => $uid, 'towpassword' => $newpwd));
                        } else {
                            $json['status'] = 2;
                            $json['msg'] = '旧密码不正确,修改失败！';
                            echo json_encode($json);
                            exit;
                        }
                        break;
                    case 3:if ($userinfo['threepassword'] == $oldpwd) {
                            $relust = $member_table->save(array('id' => $uid, 'threepassword' => $newpwd));
                        } else {
                            $json['status'] = 2;
                            $json['msg'] = '旧密码不正确,修改失败！';
                            echo json_encode($json);
                            exit;
                        }
                        break;
                    default :
                        $json['status'] = 2;
                        $json['msg'] = '非法操作！';
                        echo json_encode($json);
                        exit;
                }

                if ($relust) {
                    $json['status'] = 1;
                    $json['msg'] = '操作成功！';
                    echo json_encode($json);
                    exit;
                } else {
                    $json['status'] = 2;
                    $json['msg'] = '操作失败！';
                    echo json_encode($json);
                    exit;
                }
            } else {
                $json['status'] = 2;
                $json['msg'] = '密码不能为空!';
                echo json_encode($json);
                exit;
            }
        } else {

            $this->display();
        }
    }

//短信找回密码
    public function finduserpassword() {
        $member_table = M('member');
        if (IS_POST) {

            $code_table = M('code');
            $uid = session('uid');

            $type = I('post.type', '', 'htmlspecialchars');
            $password = I('post.password', '', 'htmlspecialchars');
            $code = I('post.code', '', 'htmlspecialchars');
            $return = checkPwd($password);
            if (!$return) {
                $json['status'] = 2;
                $json['msg'] = '密码格式不正确';
                echo json_encode($json);
                exit;
            }

            $codeinfo = $code_table->where(array('uid' => $uid))->find();
            if ($code == $codeinfo['code']) {
                if ($codeinfo['effectivetime'] < time()) {
                    $json['status'] = 2;
                    $json['msg'] = '请重新获取验证码！';
                    echo json_encode($json);
                    exit;
                } else {

                    $newpwd = fun_md5($password);
                    if (!empty($newpwd)) {
                        $userinfo = $member_table->field('password,towpassword,threepassword')->find($uid);
                        switch ($type) {
                            case 1:
                                $relust = $member_table->save(array('id' => $uid, 'password' => $newpwd));
                                break;
                            case 2:
                                $relust = $member_table->save(array('id' => $uid, 'towpassword' => $newpwd));
                                break;
                            case 3:
                                $relust = $member_table->save(array('id' => $uid, 'threepassword' => $newpwd));
                                break;
                            default :
                                $json['status'] = 2;
                                $json['msg'] = '非法操作！';
                                echo json_encode($json);
                                exit;
                        }

                        if ($relust) {
                            $json['status'] = 1;
                            $json['msg'] = '操作成功！';
                            echo json_encode($json);
                            exit;
                        } else {
                            $json['status'] = 2;
                            $json['msg'] = '操作失败！';
                            echo json_encode($json);
                            exit;
                        }
                    } else {
                        $json['status'] = 2;
                        $json['msg'] = '密码不能为空!';
                        echo json_encode($json);
                        exit;
                    }
                }
            } else {
                $json['status'] = 2;
                $json['msg'] = '验证码错误！';
                echo json_encode($json);
                exit;
            }
        } else {

            $this->display();
        }
    }

    //发送验证码
    public function set_codes() {
        if (IS_POST) {

            $member_table = M('member');
            $uid = session('uid');
            $rel = $member_table->field('username,mobile')->find($uid);
            if ($rel) {
                $relust = set_code_sms($rel['username'], $rel['mobile'], '6', '3', 'code', 'member', '2');
            } else {
                $json['status'] = 2;
                $json['msg'] = '用户信息不存在！';
                echo json_encode($json);
                exit;
            }
        }
    }

    //用户信息
    public function userinfo() {
        $member_tabe = M('member');
        $memberlevel_table = M('memberlevel');
        $uid = session('uid');
        if (IS_POST) {
            $id_card = I('post.id_card', '', 'htmlspecialchars');
            $post_code = I('post.post_code', '', 'htmlspecialchars');
            $province = I('post.province', '', 'htmlspecialchars');
            $city = I('post.city', '', 'htmlspecialchars');
            $area = I('post.area', '', 'htmlspecialchars');
            $detailed_address = I('post.detailed_address', '', 'htmlspecialchars');
            $bank = I('post.bank', '', 'htmlspecialchars');
            $account_name = I('post.account_name', '', 'htmlspecialchars');
            $bankno = I('post.bankno', '', 'htmlspecialchars');
            $bank_outlets = I('post.bank_outlets', '', 'htmlspecialchars');
            if (!$member_tabe->autoCheckToken($_POST)) {
                $json['status'] = '2';
                $json['msg'] = '不要重复提交';
                echo json_encode($json);
                exit;
            }
            $data = array(
                'id' => $uid,
                'id_card' => $id_card,
                'post_code' => $post_code,
                'province' => $province,
                'city' => $city,
                'area' => $area,
                'detailed_address' => $detailed_address,
                'bank' => $bank,
                'account_name' => $account_name,
                'bankno' => $bankno,
                'bank_outlets' => $bank_outlets,
                'saveinfonum' => '1',
            );

            $relust = $member_tabe->save($data);
            if ($relust) {
                $json['status'] = '1';
                $json['msg'] = '操作成功';
                echo json_encode($json);
                exit;
            } else {
                $json['status'] = '2';
                $json['msg'] = '操作失败！';
                echo json_encode($json);
                exit;
            }
        }




        $userInfo = $member_tabe->find($uid);
        $userInfo['mobile'] = (empty($userInfo['mobile']))? : substr_replace($userInfo['mobile'], "****", 3, 3);
        $userInfo['id_card'] = (empty($userInfo['id_card']))? : substr_replace($userInfo['id_card'], "******", 6, 5);
        $userInfo['bankno'] = (empty($userInfo['bankno']))? : substr_replace($userInfo['bankno'], "*****", 6, 6);
        $reInfo = $member_tabe->find($userInfo['recommend']);
        $juInfo = $member_tabe->find($userInfo['junction']);
        $level = $memberlevel_table->field('title')->find($userInfo['level']);
        $banklist = findbank();
        $this->assign('banklist', $banklist);
        $this->assign('level', $level);
        $this->assign('recommend', $reInfo);
        $this->assign('junction', $juInfo);
        $this->assign('userInfo', $userInfo);
        $this->display();
    }

    //节点关系图
    public function listcontactman() {

        if (IS_AJAX) {
            $uid = session('uid');
            $member_table = M('member');
            $pId = $uid;
            $pName = "";
            $pLevel = "";
            $pCheck = "";
            if (array_key_exists('id', $_REQUEST)) {
                $pId = $_REQUEST['id'];
            }
            if (array_key_exists('lv', $_REQUEST)) {
                $pLevel = $_REQUEST['lv'];
            }
            if (array_key_exists('n', $_REQUEST)) {
                $pName = $_REQUEST['n'];
            }
            if (array_key_exists('chk', $_REQUEST)) {
                $pCheck = $_REQUEST['chk'];
            }
            $search_username = $_GET['name'];
            $search_id = $_GET['id'];

            if (empty($search_id)) {
                if (!empty($search_username)) {
                    $userinfo = $member_table->field('id,junction')->where(array('username' => $search_username))->find();
                    if ($userinfo) {
                        if ($userinfo['id'] < $pId) {
                            $pId = $uid;
                        } else {
                            $pId = $userinfo['id'];
                        }
                    } else {
                        $pId = $uid;
                    }
                }
            } else {
                $pId = $search_id;
            }

            if ($pId == null || $pId == "")
                $pId = $uid;
            if ($pLevel == null || $pLevel == "")
                $pLevel = "0";
            if ($pName == null)
                $pName = "";
            else
                $pName = $pName . ".";

            $list = $member_table->field('id,junction,username,level')->where(array('junction' => $pId))->select();
            $count = count($list);
            echo '[';
            for ($i = 1; $i <= $count; $i++) {
                $level = findlevel($list[$i - 1]['level']);
                $nId = $list[$i - 1]['id'];
                $nName = substr_replace($list[$i - 1]['username'], '***', 3, 3) . '[' . $level . ']';
                $info = $member_table->field('id')->where(array('junction' => $nId, 'recommend' => array('neq', '-1')))->select();
                $flag = 'false';
                if ($info) {
                    $flag = 'true';
                }

                echo "{ id:'" . $nId . "',	name:'" . $nName . "',isParent:'" . $flag . "'}";
                if ($i < $count) {
                    echo ",";
                }
            }
            echo ']';
        } else {

            $this->display();
        }
    }

    //市场拓展
    public function listpartner() {
        $member_table = M('member');
        $id = session('uid');
        if (isset($_GET['username']) && !empty($_GET['username'])) {
            $userinfo = $member_table->field('id')->where(array('username' => $_GET['username']))->find();
            if ($userinfo['id'] < $id) {
                $uid = $id;
            } else {
                $uid = $userinfo['id'];
            }
        } else {
            $uid = $id;
        }

        $region = array('左', '中', '右');
        $map['junction'] = $uid;
        $list = $member_table->where($map)->select();
        $count = count($list);
        for ($i = 0; $i < $count; $i++) {
            $recommend = $member_table->field('username')->find($list[$i]['recommend']);
            $junction = $member_table->field('username')->find($list[$i]['junction']);
            $list[$i]['level'] = findlevel($list[$i]['level']);
            $list[$i]['recommend'] = $recommend['username'];
            $list[$i]['junction'] = $junction['username'];
            $list[$i]['regions'] = $region[$list[$i]['region']];
        }
        $this->assign('username', $_GET['username']);
        $this->assign('list', $list);
        $this->display();
    }

    //检验账号是否存在
    public function check_input_username() {
        if (IS_POST) {
            $member_table = M('member');
            $name = I('post.name', '', 'trim');
            $param = I('post.param', '', 'trim');
            $relust = $member_table->field('id')->where(array($name => $param))->find();
            if ($relust) {
                $json['status'] = 'y';
                $json['info'] = '数据验证成功';
                echo json_encode($json);
                exit;
            } else {
                $json['status'] = 'n';
                $json['info'] = '会员账号不存在！';
                echo json_encode($json);
                exit;
            }
        }
    }

    //验证手机号银行卡号会员号
    public function check_input_unique() {
        if (IS_POST) {
            $member_table = M('member');
            $name = I('post.name', '', 'trim');
            $param = I('post.param', '', 'trim');
            $data = getbonusparam();
            $count = $member_table->field('id')->where(array($name => $param))->count();
            if ($name == 'mobile') {
                if ($count >= $data['registMobileNum'] && $data['registMobileNum'] != 0) {
                    $json['status'] = 'n';
                    $json['info'] = '该手机号已经存' . $count . '个！';
                    echo json_encode($json);
                    exit;
                } else {
                    $json['status'] = 'y';
                    $json['info'] = '数据验证成功';
                    echo json_encode($json);
                    exit;
                }
            }
            if ($name == 'id_card') {
                if ($count >= $data['registIdcardNum'] && $data['registIdcardNum'] != 0) {
                    $json['status'] = 'n';
                    $json['info'] = '该身份证号已经存' . $count . '个！';
                    echo json_encode($json);
                    exit;
                } else {
                    $json['status'] = 'y';
                    $json['info'] = '数据验证成功';
                    echo json_encode($json);
                    exit;
                }
            }
            if ($name == 'bankno') {
                if ($count >= $data['registBanknoNum'] && $data['registBanknoNum'] != 0) {
                    $json['status'] = 'n';
                    $json['info'] = '该银行卡号已经存' . $count . '个！';
                    echo json_encode($json);
                    exit;
                } else {
                    $json['status'] = 'y';
                    $json['info'] = '数据验证成功';
                    echo json_encode($json);
                    exit;
                }
            }
            if ($name == 'username') {
                if ($count) {
                    $json['status'] = 'n';
                    $json['info'] = '会员账号已经存在';
                    echo json_encode($json);
                    exit;
                } else {
                    $json['status'] = 'y';
                    $json['info'] = '数据验证成功';
                    echo json_encode($json);
                    exit;
                }
            }
        }
    }

    //验证推荐人
    public function check_input_recommend_exist() {
        if (IS_POST) {

            $member_table = M('member');
            $name = I('post.name', '', 'trim');
            $param = I('post.param', '', 'trim');
            $relust = $member_table->field('name,junction,level')->where(array('username' => $param))->find();
            if ($relust) {
                if ($relust['level'] == 1) {
                    $json['status'] = 'n';
                    $json['info'] = '临时会员不能做推荐人';
                    echo json_encode($json);
                    exit;
                } else {
                    $json['status'] = 'y';
                    $json['info'] = '该推荐人姓名为：' . $relust['name'];
                    echo json_encode($json);
                    exit;
                }
            } else {
                $json['status'] = 'n';
                $json['info'] = '该推荐人不存在';
                echo json_encode($json);
                exit;
            }
        }
    }

    //返回接点人id
    public function returnid($rid, $qu) {

        $member_table = M('member');
        $info = $member_table->field('junctionLevel')->find($rid);
        $row = $member_table->field('id')->where(array('junction' => $rid, 'region' => $qu))->find();
        if ($row) {
            return self::returnid($row['id'], 0);
        } else {

            $data = array(
                'id' => $rid,
                'qu' => $qu,
                'level' => $info['junctionlevel'] + 1
            );
            return $data;
        }
    }

    //查找A区是否已经挂满一个属于自己的推荐人
    public function returnrecommend($recommend, $region) {
        $member_table = M('member');
        $recommendInfo = $member_table->field('id')->where(array('username' => $recommend))->find();
        $this->diguireturnrecommend($recommendInfo['id'], $recommendInfo['id'], $region);
    }

    protected function diguireturnrecommend($rid, $rids, $region) {
        $member_table = M('member');
        $shichangtotal_table = M('shichangtotal');
        $data = getbonusparam(); //获取的奖金比例参数
        $row = $member_table->field('id,recommend')->where(array('region' => '0', 'junction' => $rid))->find(); //判断推荐人有没有在左区发展一个会员
        if ($row) {
            //有发展就解放 左中右区
            if ($row['recommend'] == $rids) {

                $centerinfo = $shichangtotal_table->where(array('uid' => $rids))->sum('center'); //获取推荐人的中市场总业绩
                if ($centerinfo >= $data['centermoney']) {
                    $json['status'] = 1;
                    $json['msg'] = '可放到左 中 右区';
                    echo json_encode($json);
                    exit;
                } else {
                    if ($region == 2) {
                        $json['status'] = 2;
                        $json['msg'] = '中市场业绩达到' . $data['centermoney'] . '￥开放右区';
                        echo json_encode($json);
                        exit;
                    } else {
                        $json['status'] = 1;
                        $json['msg'] = '可放到左 中区';
                        echo json_encode($json);
                        exit;
                    }
                }
            } else {
                self::diguireturnrecommend($row['id'], $rids, $region);
            }
        } else {
            if ($region == 0) {
                $json['status'] = 1;
                $json['msg'] = '第一个只能放到左区';
                echo json_encode($json);
                exit;
            } else {
                //没有就只解放左区
                $json['status'] = 2;
                $json['msg'] = '第一个只能放到左区';
                echo json_encode($json);
                exit;
            }
        }
    }

    public function check_region($rid, $rids, $region) {
        $member_table = M('member');
        $shichangtotal_table = M('shichangtotal');
        $data = getbonusparam(); //获取的奖金比例参数
        $row = $member_table->field('id,recommend')->where(array('region' => '0', 'junction' => $rid))->find(); //判断推荐人有没有在左区发展一个会员
        if ($row) {
            //有发展就解放 左中右区
            if ($row['recommend'] == $rids) {
                $centerinfo = $shichangtotal_table->where(array('uid' => $rids))->sum('center'); //获取推荐人的中市场总业绩
                if ($centerinfo >= $data['centermoney']) {
                    
                } else {
                    if ($region == 2) {
                        $json['status'] = 2;
                        $json['msg'] = '中市场业绩达到' . $data['centermoney'] . '￥开放右区';
                        echo json_encode($json);
                        exit;
                    } else {
                        
                    }
                }
            } else {
                self::check_region($row['id'], $rids);
            }
        } else {
            if ($region == 0) {
                
            } else {
                //没有就只解放左区
                $json['status'] = 2;
                $json['msg'] = '第一个只能放到左区';
                echo json_encode($json);
                exit;
            }
        }
    }

    //注册会员
    public function useradd() {
        $data = getbonusparam();
        $pre = $data['memberAccountPrefix']; //前缀
        $prenum = strlen($pre);
        $member_table = M('member');
        $member_table->startTrans();
        if (IS_POST) {
            $region = I('post.region', '', 'trim');
            $receiver = I('post.name', '', 'trim');
            $_POST['password'] = fun_md5(I('post.password', '', 'trim'));
            $_POST['regtime'] = time();
            $_POST['regip'] = get_client_ip();
            $_POST['recommend'] = get_user_id(I('post.recommend', '', 'trim'));
            if (substr($_POST['username'], 0, $prenum) != $pre) {
                $json['status'] = 0;
                $json['msg'] = '前缀不对';
                echo json_encode($json);
                exit;
            }
            $rinfo = $member_table->field('level')->find($_POST['recommend']);
            if ($rinfo['level'] == 1) {
                $json['status'] = 0;
                $json['msg'] = '临时会员不能作为推荐人';
                echo json_encode($json);
                exit;
            }

            if (!$member_table->autoCheckToken($_POST)) {
                $json['status'] = 0;
                $json['msg'] = '请不要重复提交';
                echo json_encode($json);
                exit;
            }
            if ($region != 0) {
                ///判断区域
                $this->check_region($_POST['recommend'], $_POST['recommend'], $region);
            }
            //注册用户
            $_POST['action'] = '1';
            $rel = $member_table->add($_POST);

            if ($rel) {
                $member_table->commit();
                $json['status'] = 1;
                $json['msg'] = '注册成功';
                echo json_encode($json);
                exit;
            } else {
                $member_table->rollback();
                $json['status'] = 0;
                $json['msg'] = '注册失败';
                echo json_encode($json);
                exit;
            }
        }
        $id = session('uid');
        $userInfo = $member_table->find($id);
        $username = create_usercode(); //生成随机数
        $banklist = findbank(); //获取银行
        $this->assign('region', $_GET['region']);
        $this->assign('junctioninfo', $_GET['username']);
        $this->assign('banklist', $banklist);
        $this->assign('username', $username);
        $this->assign('userInfo', $userInfo);
        $this->display();
    }

    public function recommend() {
        $member_table = M('member');
        $memberlevel_table = M('memberlevel');
        $uid = session('uid');
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
            $map['regtime'] = array('between', $times);
            //$timespan = strtotime(urldecode($_REQUEST['start_time'])) . "," . strtotime(urldecode($_REQUEST['end_time']));
        } elseif (!empty($_REQUEST['search_starttime'])) {
            $xtime = strtotime($_REQUEST['search_starttime'] . '00:00:00');
            $map['regtime'] = array("egt", $xtime);
            $search['search_starttime'] = $_REQUEST['search_starttime'];
        } elseif (!empty($_REQUEST['search_endtime'])) {
            $xtime = strtotime($_REQUEST['search_endtime'] . '23:59:59');
            $map['regtime'] = array("elt", $xtime);
            $search['search_endtime'] = $_REQUEST['search_endtime'];
        }

        $map['recommend'] = $uid;
        //$region = array('左', '中', '右');
        // $is_accounts = array('是', '否');
        $count = $member_table->where($map)->count(); // 查询满足要求的总记录数
        $Page = new \Think\Page($count, 30); // 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show(); //
        $list = $member_table->order('id desc')->where($map)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $lcount = count($list);
        for ($i = 0; $i < $lcount; $i++) {
            $list[$i]['account_name'] = (empty($list[$i]['account_name'])) ? 无 : $list[$i]['account_name'];

            // $recommend = $member_table->field('username')->find($list[$i]['recommend']);
            // $junction = $member_table->field('username')->find($list[$i]['junction']);
            $list[$i]['level'] = findlevel($list[$i]['level']);

            // $list[$i]['junction'] = $junction['username'];
            // $list[$i]['junction'] = (empty($list[$i]['junction'])) ? 无 : $junction['username'];
            // $list[$i]['region'] = $region[$list[$i]['region']];
            // $list[$i]['is_accounts'] = $is_accounts[$list[$i]['is_accounts']];
        }
        $memberlevel = $memberlevel_table->select();
        $this->assign('memberlevel', $memberlevel);
        $this->assign('page', $show);
        $this->assign('list', $list);
        $this->assign('count', $count);
        $this->assign('arr', $search);
        $this->display();
    }
    public function  del(){
        $uid=  session('uid');
        $member_table=M('member');
        $username = I('post.username', '', 'trim');
        $relust=$member_table->field('id,recommend')->where(array('username'=>$username,'level'=>array('eq','1')))->find(); 
        if($relust&&$relust['recommend']==$uid){
            $rel=$member_table->delete($relust['id']);
            if($rel){
                $json['status'] = 1;
                $json['msg'] = '操作成功';
                echo json_encode($json);
                exit;
            }
            else{
                $json['status'] = 0;
                $json['msg'] = '操作失败';
                echo json_encode($json);
                exit;
            }
            
        }else{
                $json['status'] = 0;
                $json['msg'] = '非法操作';
                echo json_encode($json);
                exit;
        }
    }

}
