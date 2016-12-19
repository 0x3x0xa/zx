<?php

namespace Home\Controller;

use Home\Controller\CommonController;

class FinanceController extends NotinController {
    /*     * *
     *
     * 财务
     */

    //http://zx.com/index.php/Home/Finance/
//    public function index() {
//        //上个月第一天的时间戳
//        $lastmonth = mktime(0, 0, 0, date('m') - 1, 1, date('Y'));
//        p(date('Y-m-d H:i:s', $lastmonth));
//        //当月第一天时间戳
//        $beginThismonth = mktime(0, 0, 0, date('m'), 1, date('Y'));
//        p(date('Y-m-d H:i:s', $beginThismonth));
//        //  $this->display();
//    }
//
//    //快速奖(每天执行一次)
//    public function kuaiShuBonusSettlement() {
//
//
//
//        set_time_limit(0);
//        $member_table = M('member');
//        $setlevel_table = M('setlevel');
//        $maxlevel = $setlevel_table->field('max')->max('max');
//        $Param = $this->getLevelParam();
//        $junctionLevel = $member_table->field('junctionLevel')->where(array('recommend' => array('neq', '-1'), 'level' => array('neq', '1')))->max('junctionLevel'); //最后一层
//
//        for ($i = $junctionLevel; $i >= 1; $i--) {
//            $list = $member_table->field('id,junction,level,username')->where(array('junctionLevel' => $i, 'recommend' => array('neq', '-1'), 'level' => array('neq', '1')))->select();
//            $count = count($list);
//            for ($j = 0; $j < $count; $j++) {
//
//                $this->kuaiShudigui($list[$j]['junction'], $list[$j]['level'], $level = 1, $Param, $list[$j]['id'], $list[$j]['username'], $maxlevel);
//            }
//        }
//    }
//
//    //获取每层的钱数
//    protected function getLevelParam() {
//        $list = M()->table(array('web_levelvalue' => 'tb1', 'web_setlevel' => 'tb2'))->field('tb1.memberlevelid,tb1.value,tb2.min,tb2.max')->where('tb2.id=tb1.setlevelid ')->select();
//        return $list;
//    }
//
//    /* $junctionid 节点人id
//     * $star    挂在节点人下面的用户的级别
//     * $level   递归到第几层
//     */
//
//    protected function kuaiShudigui($junctionid, $star, $level, $Param, $fid, $fname, $maxlevel) {
//        $todayTime = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
//        $member_table = M('member');
//        $bonus_table = M('bonus');
//        $junctionInfo = $member_table->field('id,junction,level,kuaisuBonus')->find($junctionid); //推荐人信息
//        if ($junctionInfo['level'] >= $star && $junctionInfo['level'] != 1) {//节点人大于等于节点下面的人则算快速奖
//            $count = count($Param);
//            for ($i = 0; $i < $count; $i++) {
//
//                if ($star == $Param[$i]['memberlevelid'] && $level >= $Param[$i]['min'] && $level <= $Param[$i]['max']) {
//                    //更新利息
//                    $kuaisubonus = $junctionInfo['kuaisubonus'] + $Param[$i]['value'];
//                    $bonus_table->add(array('type' => '1', 'uid' => $junctionInfo['id'], 'income' => $Param[$i]['value'], 'balance' => $kuaisubonus, 'create_date' => time(), 'message' => '来自' . $fname . '第' . $level . '层快速奖', 'date' => date('Y-m-d H:i:s'), 'status' => '1', 'fid' => $fid, 'addtime' => $todayTime));
//                    $member_table->save(array('id' => $junctionInfo['id'], 'kuaisuBonus' => $kuaisubonus));
//                }
//            }
//        }
//        if ($junctionInfo['junction'] != 0) {
//            if ($level < $maxlevel) {
//                $level++;
//                $this->kuaiShudigui($junctionInfo['junction'], $star, $level, $Param, $fid, $fname, $maxlevel);
//            }
//        }
//    }
//
//
//
//



    public function jihuarenwu() {

        //   $fp = fopen("test.txt", "a+");
        //  fwrite($fp, date("Y-m-d H:i:s") . " 成功成功了！n");
        //  fclose($fp);


        set_time_limit(0);
        $webconfig_table = M('webconfig');
        $info = $webconfig_table->where(array('id' => array('in', '3,4,5')))->select();

        $todayTime = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
        $nowmonth = mktime(0, 0, 0, date('m'), 1, date('Y')); //当月第一天的时间戳
        $lastmonth = mktime(0, 0, 0, date('m') + 1, 1, date('Y')); //下个月月第一天的时间戳
        $week = mktime(0, 0, 0, date('m'), date('d') - date('w'), date('Y')); //当周第一天
        //$lastweek = $week + 60 * 60 * 24 * 7;
        //p($lastweek);
        //  p(date("Y-m-d H:i:s",'1477929600'));//十月一号1475251200  //十月一号1475251200  //十一月一号1477929600
        // exit;
        //p($lastmonth.'Month');
        // p($lastweek).'周';
        //  p(($todayTime ) .'Day');
        //  exit;
        if ($info['0']['value'] == $todayTime) {
            $webconfig_table->save(array('id' => 3, 'value' => $todayTime + 60 * 60 * 24));
            $this->dayexec();
        }
        $weektime = $week + 86400;
        if ($todayTime == $weektime) {
            $flagtime = $info['1']['value'] + 86400;
            if ($weektime == $flagtime) {
                $webconfig_table->save(array('id' => 4, 'value' => $week + 60 * 60 * 24 * 7));
                $this->weekexec();
            }
        }
        if ($info['2']['value'] == $nowmonth) {
            $webconfig_table->save(array('id' => 5, 'value' => $lastmonth));
            $this->monthexec();
        }

        $this->autozhuanyidongtaimoney();
        $this->autozhuanyijintaimoney();
    }

    //日计划
    protected function dayexec() {
        $this->lingdaoBonusCount(); //市场业绩统计
        $this->lingdaoSettlement(); //开发奖（开发奖）
        $this->hongbaobonus(); //红包奖
        $this->maxfenhong();
    }

    //月计划
    protected function monthexec() {

        $this->staticbonus(); //（按月发放一次）分红奖
        // $this->jinjiBonus(); //发放晋级奖 （给上个月的职位发放。）
        $this->upgrade(); //（按月检查一次）每月检查用户级别，达到条件则升职位。
        $this->groupmonth(); //统计上个月的团队业绩（日明细归纳为月明细）
        $this->guanliBonus(); //管理奖结算
        $this->quanqiufenhong(); //全球分红结算
        $this->yuexin();
    }

    //周计划
    protected function weekexec() {
        //$this->zenZhibonus();

        $this->weekbonus();
    }

    protected function autozhuanyidongtaimoney() {

        $todayTime = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
        $beginThismonth = mktime(0, 0, 0, date('m'), 1, date('Y'));  //当月第一天时间戳
        $week = mktime(0, 0, 0, date('m'), date('d') - date('w'), date('Y')); //当周第一天
        $member_table = M('member');
        $bonus_table = M('bonus');
        $member_table->startTrans();
        //货币类型 1快速奖，2开发奖，3管理奖，4晋升奖，5全球分红,6电子积分，7个人所得税，8车奖，9重复消费,10赠送积分 11.别墅基金12红包奖，13增值奖，14分红奖 15旅游基金16基金17月薪奖18月薪奖池19活动钱包20静态钱包21月薪奖池22线上充值现金流水 
        $userInfo = $member_table->field('id,lingdaoBonus,guanliBonus,jinjiBonus,quanqiufenhongBonus,yuexinBonus,hongbaobonus,allbonus,daishubonus')->select();
        $count = count($userInfo);
        for ($i = 0; $i < $count; $i++) {
            if (!empty($userInfo[$i]['lingdaobonus']) && $userInfo[$i]['lingdaobonus'] > 0) {
                $bonus_table->add(array('uid' => $userInfo[$i]["id"], 'type' => '2', 'expend' => $userInfo[$i]['lingdaobonus'], 'status' => '2', 'balance' => 0, 'message' => '转入到市场积分' . $i, 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '14')); //14合并转出
            }
            if (!empty($userInfo[$i]['guanlibonus']) && $userInfo[$i]['guanlibonus'] > 0) {
                $bonus_table->add(array('uid' => $userInfo[$i]["id"], 'type' => '3', 'expend' => $userInfo[$i]['guanlibonus'], 'status' => '2', 'balance' => 0, 'message' => '转入到市场积分', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '14')); //14合并转出
            }
            if (!empty($userInfo[$i]['jinjibonus']) && $userInfo[$i]['jinjibonus'] > 0) {
                $bonus_table->add(array('uid' => $userInfo[$i]["id"], 'type' => '4', 'expend' => $userInfo[$i]['jinjibonus'], 'status' => '2', 'balance' => 0, 'message' => '转入到市场积分', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '14')); //14合并转出
            }
            if (!empty($userInfo[$i]['quanqiufenhongbonus']) && $userInfo[$i]['quanqiufenhongbonus'] > 0) {
                $bonus_table->add(array('uid' => $userInfo[$i]["id"], 'type' => '5', 'expend' => $userInfo[$i]['quanqiufenhongbonus'], 'status' => '2', 'balance' => 0, 'message' => '转入到市场积分', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '14')); //14合并转出
            }
            if (!empty($userInfo[$i]['yuexinbonus']) && $userInfo[$i]['yuexinbonus'] > 0) {
                $bonus_table->add(array('uid' => $userInfo[$i]["id"], 'type' => '17', 'expend' => $userInfo[$i]['yuexinbonus'], 'status' => '2', 'balance' => 0, 'message' => '转入到市场积分', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '14')); //14合并转出
            }
            if (!empty($userInfo[$i]['hongbaobonus']) && $userInfo[$i]['hongbaobonus'] > 0) {
                $bonus_table->add(array('uid' => $userInfo[$i]["id"], 'type' => '12', 'expend' => $userInfo[$i]['hongbaobonus'], 'status' => '2', 'balance' => 0, 'message' => '转入到市场积分', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '14')); //14合并转出
            }
            if (!empty($userInfo[$i]['daishubonus']) && $userInfo[$i]['daishubonus'] > 0) {
                $bonus_table->add(array('uid' => $userInfo[$i]["id"], 'type' => '23', 'expend' => $userInfo[$i]['daishubonus'], 'status' => '2', 'balance' => 0, 'message' => '转入到市场积分', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '14')); //14合并转出
            }
            $shouru = $userInfo[$i]['lingdaobonus'] + $userInfo[$i]['guanlibonus'] + $userInfo[$i]['jinjibonus'] + $userInfo[$i]['quanqiufenhongbonus'] + $userInfo[$i]['yuexinbonus'] + $userInfo[$i]['hongbaobonus'] + $userInfo[$i]['daishubonus'];
            $userallbonus = $userInfo[$i]['lingdaobonus'] + $userInfo[$i]['guanlibonus'] + $userInfo[$i]['jinjibonus'] + $userInfo[$i]['quanqiufenhongbonus'] + $userInfo[$i]['yuexinbonus'] + $userInfo[$i]['hongbaobonus'] + $userInfo[$i]['allbonus'] + $userInfo[$i]['daishubonus'];
            if ($shouru != 0) {
                $relust = $bonus_table->add(array('uid' => $userInfo[$i]["id"], 'type' => '19', 'income' => $shouru, 'status' => '1', 'balance' => $userallbonus, 'message' => '市场积分合并', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '13')); //13合并转入
                $relust1 = $member_table->save(array('id' => $userInfo[$i]["id"], 'lingdaoBonus' => '0', 'guanliBonus' => '0', 'jinjiBonus' => '0', 'quanqiufenhongBonus' => '0', 'yuexinBonus' => '0', 'hongbaobonus' => '0', 'daishubonus' => '0', 'allbonus' => $userallbonus));
                if ($relust && $relust1) {
                    $member_table->commit();
                } else {
                    $member_table->rollback();
                    exit;
                }
            } else {
                $member_table->rollback();
            }
            unset($userInfo[$i]);
        }
    }

    protected function autozhuanyijintaimoney() {

        $todayTime = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
        $beginThismonth = mktime(0, 0, 0, date('m'), 1, date('Y'));  //当月第一天时间戳
        $week = mktime(0, 0, 0, date('m'), date('d') - date('w'), date('Y')); //当周第一天
        $member_table = M('member');
        $bonus_table = M('bonus');
        $member_table->startTrans();
        //货币类型 1快速奖，2开发奖，3管理奖，4晋升奖，5全球分红,6电子积分，7个人所得税，8车奖，9重复消费,10赠送积分 11.别墅基金12红包奖，13增值奖，14分红奖 15旅游基金16基金17月薪奖18月薪奖池19活动钱包20静态钱包21月薪奖池22线上充值现金流水 

        $userInfo = $member_table->field('id,zengzhibonus,fenhongbonus,alljingtaibonus')->select();
        $count = count($userInfo);
        for ($i = 0; $i < $count; $i++) {
            if (!empty($userInfo[$i]['zengzhibonus']) && $userInfo[$i]['zengzhibonus'] > 0) {
                $bonus_table->add(array('uid' => $userInfo[$i]["id"], 'type' => '13', 'expend' => $userInfo[$i]['zengzhibonus'], 'status' => '2', 'balance' => 0, 'message' => '转入到静态钱袋', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '14')); //14合并转出
            }
            if (!empty($userInfo[$i]['fenhongbonus']) && $userInfo[$i]['fenhongbonus'] > 0) {
                $bonus_table->add(array('uid' => $userInfo[$i]["id"], 'type' => '14', 'expend' => $userInfo[$i]['fenhongbonus'], 'status' => '2', 'balance' => 0, 'message' => '转入到静态钱袋', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '14')); //14合并转出
            }
            $shouru = $userInfo[$i]['zengzhibonus'] + $userInfo[$i]['fenhongbonus'];
            $userallbonus = $userInfo[$i]['zengzhibonus'] + $userInfo[$i]['fenhongbonus'] + $userInfo[$i]['alljingtaibonus'];
            if ($shouru != 0) {
                $relust = $bonus_table->add(array('uid' => $userInfo[$i]["id"], 'type' => '20', 'income' => $shouru, 'status' => '1', 'balance' => $userallbonus, 'message' => '静态奖金合并', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '13')); //13合并转入
                $relust1 = $member_table->save(array('id' => $userInfo[$i]["id"], 'zengzhibonus' => '0', 'fenhongbonus' => '0', 'alljingtaibonus' => $userallbonus));
                if ($relust && $relust1) {
                    $member_table->commit();
                } else {
                    $member_table->rollback();
                    exit;
                }
            } else {
                $member_table->rollback();
            }
        }
    }

    //统计市场业绩
    /* protected function lingdaoBonusCount() {

      $time = time();
      $todayTime = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
      //$tomorrow = $todayTime + 60 * 60 * 24; //明天凌晨
      $yesterday = $todayTime - 60 * 60 * 24; //昨天凌晨
      //
      //p($tomorrow);
      //exit;
      $member_table = M('member');
      $total_table = M('total');

      $achievement_table = M('achievement');
      $balance_table = M('balance');
      $member_table->startTrans();
      $junctionLevel = $member_table->field('junctionLevel')->max('junctionLevel'); //最后一层

      for ($i = $junctionLevel; $i >= 1; $i--) {

      $list = $member_table->field('id,junction,level,position')->where(array('junctionLevel' => $i, 'level' => array('neq', '1')))->select(); //最后一层的用户个数

      $count = count($list);
      for ($j = 0; $j < $count; $j++) {

      $balance = $balance_table->where(array('uid' => $list[$j]['id'], 'addtime' => $todayTime))->find(); //昨天剩余下来的奖金
      $balance['balance'] = (empty($balance)) ? 0 : $balance['balance'];
      $achievements = $achievement_table->where(array('uid' => $list[$j]['id'], 'addtime' => $yesterday))->sum('money'); //获取到昨天的业绩
      $achievement = (!empty($achievements)) ? $achievements : 0;
      $selfrelust = $total_table->where(array('uid' => $list[$j]['id'], 'addtime' => $todayTime))->find(); //判断本人今天有没有插入数据
      $junctionrelust = $total_table->field('id,selfmoney,groupmoney')->where(array('uid' => $list[$j]['junction'], 'addtime' => $todayTime))->find(); //判断父级今天有没有插入数据
      $junlist = $member_table->field('position,level,junction')->find($list[$j]['junction']);

      $allmoney = $balance['balance'] + $achievement + $selfrelust['groupmoney']; //该会员的业绩
      $self_ach_bal = $balance['balance'] + $achievement; //今天有没钱要更新
      $groupmoneys = $achievement + $selfrelust['groupmoney'];
      // if ($allmoney != 0) {
      //判断有没有记录，有就加，没有就插入
      if ($selfrelust) {

      $rel1 = $total_table->save(array('id' => $selfrelust['id'], 'selfmoney' => $achievement, 'balance' => $balance['balance'], 'allmoney' => $allmoney, 'pid' => $list[$j]['junction'], 'level' => $list[$j]['level'], 'position' => $list[$j]['position']));
      } else {
      //                    p($list[$j]['id']);
      //                    p($achievement);
      //                    p($list[$j]['junction']);
      //                    exit;
      $rel1 = $total_table->add(array('uid' => $list[$j]['id'], 'selfmoney' => $achievement, 'balance' => $balance['balance'], 'allmoney' => $allmoney, 'pid' => $list[$j]['junction'], 'level' => $list[$j]['level'], 'position' => $list[$j]['position'], 'groupmoney' => '0', 'create_date' => $time, 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime));
      }
      //如果父接点有就更新，没有就插入
      if ($list[$j]['junction'] != 0) {
      // if ($groupmoneys != 0) {
      if ($junctionrelust) {
      $rel2 = $total_table->save(array('id' => $junctionrelust['id'], 'selfmoney' => '0', 'groupmoney' => $achievement + $selfrelust['groupmoney'] + $junctionrelust['groupmoney']));
      } else {
      //  if($achievement != 0){
      //   if ($selfrelust) {

      $rel2 = $total_table->add(array('uid' => $list[$j]['junction'], 'selfmoney' => '0', 'groupmoney' => $selfrelust['groupmoney'] + $achievement, 'balance' => 0, 'position' => $junlist['position'], 'level' => $junlist['level'], 'pid' => $junlist['junction'], 'create_date' => $time, 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime));
      //  } else {
      //      $total_table->add(array('uid' => $list[$j]['junction'], 'selfmoney' => '0', 'groupmoney' => $achievement+$selfrelust['groupmoney'], 'create_date' => $time, 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime));
      //     }
      // }
      }
      // }
      }
      //  }
      if ($list[$j]['junction'] != 0) {
      if ($rel1 && $rel2) {
      $member_table->commit();
      } else {
      $member_table->rollback();
      exit;
      }
      } else {
      if ($rel1) {
      $member_table->commit();
      } else {
      $member_table->rollback();
      exit;
      }
      }
      }
      }
      }

     */

    //统计市场业绩
    protected function lingdaoBonusCount() {

        $time = time();
        $todayTime = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
        //$tomorrow = $todayTime + 60 * 60 * 24; //明天凌晨
        $yesterday = $todayTime - 60 * 60 * 24; //昨天凌晨
        //
        //p($tomorrow);
        //exit;
        $member_table = M('member');
        $total_table = M('total');

        $achievement_table = M('achievement');
        $balance_table = M('balance');
        // $member_table->startTrans();
        $junctionLevel = $member_table->field('junctionLevel')->max('junctionLevel'); //最后一层

        for ($i = $junctionLevel; $i >= 1; $i--) {

            $list = $member_table->field('id,junction,level,position')->where(array('junctionLevel' => $i, 'level' => array('neq', '1')))->select(); //最后一层的用户个数

            $count = count($list);
            for ($j = 0; $j < $count; $j++) {

                $balance = $balance_table->where(array('uid' => $list[$j]['id'], 'addtime' => $todayTime))->find(); //昨天剩余下来的奖金
                $balance['balance'] = (empty($balance)) ? 0 : $balance['balance'];
                $achievements = $achievement_table->where(array('uid' => $list[$j]['id'], 'addtime' => $yesterday))->sum('money'); //获取到昨天的业绩
                $achievement = (!empty($achievements)) ? $achievements : 0;
                $selfrelust = $total_table->where(array('uid' => $list[$j]['id'], 'addtime' => $todayTime))->find(); //判断本人今天有没有插入数据
                $junctionrelust = $total_table->field('id,selfmoney,groupmoney')->where(array('uid' => $list[$j]['junction'], 'addtime' => $todayTime))->find(); //判断父级今天有没有插入数据
                $junlist = $member_table->field('position,level,junction')->find($list[$j]['junction']);

                $allmoney = $balance['balance'] + $achievement + $selfrelust['groupmoney']; //该会员的业绩
                $self_ach_bal = $balance['balance'] + $achievement; //今天有没钱要更新
                $groupmoneys = $achievement + $selfrelust['groupmoney'];
                // if ($allmoney != 0) {
                //判断有没有记录，有就加，没有就插入
                if ($selfrelust) {

                    $rel1 = $total_table->save(array('id' => $selfrelust['id'], 'selfmoney' => $achievement, 'balance' => $balance['balance'], 'allmoney' => $allmoney, 'pid' => $list[$j]['junction'], 'level' => $list[$j]['level'], 'position' => $list[$j]['position']));
                } else {
//                    p($list[$j]['id']);
//                    p($achievement);
//                    p($list[$j]['junction']);
//                    exit;
                    $rel1 = $total_table->add(array('uid' => $list[$j]['id'], 'selfmoney' => $achievement, 'balance' => $balance['balance'], 'allmoney' => $allmoney, 'pid' => $list[$j]['junction'], 'level' => $list[$j]['level'], 'position' => $list[$j]['position'], 'groupmoney' => '0', 'create_date' => $time, 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime));
                }
                //如果父接点有就更新，没有就插入
                if ($list[$j]['junction'] != 0) {
                    // if ($groupmoneys != 0) {
                    if ($junctionrelust) {
                        $rel2 = $total_table->save(array('id' => $junctionrelust['id'], 'selfmoney' => '0', 'groupmoney' => $achievement + $selfrelust['groupmoney'] + $junctionrelust['groupmoney']));
                    } else {
                        //  if($achievement != 0){
                        //   if ($selfrelust) {

                        $rel2 = $total_table->add(array('uid' => $list[$j]['junction'], 'selfmoney' => '0', 'groupmoney' => $selfrelust['groupmoney'] + $achievement, 'balance' => 0, 'position' => $junlist['position'], 'level' => $junlist['level'], 'pid' => $junlist['junction'], 'create_date' => $time, 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime));
                        //  } else {
                        //      $total_table->add(array('uid' => $list[$j]['junction'], 'selfmoney' => '0', 'groupmoney' => $achievement+$selfrelust['groupmoney'], 'create_date' => $time, 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime));
                        //     }
                        // }
                    }
                    // }
                }
                //  }
//                if ($list[$j]['junction'] != 0) {
//                    if ($rel1 && $rel2) {
//                        $member_table->commit();
//                    } else {
//                        $member_table->rollback();
//                        exit;
//                    }
//                } else {
//                    if ($rel1) {
//                        $member_table->commit();
//                    } else {
//                        $member_table->rollback();
//                        exit;
//                    }
//                }
            }
        }
    }

    //开发奖结算
    protected function lingdaoSettlement() {

        $todayTime = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
        $beginThismonth = mktime(0, 0, 0, date('m'), 1, date('Y'));  //当月第一天时间戳
        $week = mktime(0, 0, 0, date('m'), date('d') - date('w'), date('Y')); //当周第一天
        $tomorrow = $todayTime + 60 * 60 * 24; //明天凌晨
        $total_table = M('total');
        $memberlevel_table = M('memberlevel');
        $bonus_table = M('bonus');
        $member_table = M('member');
        $balance_table = M('balance');
        $lingdaobonus_table = M('lingdaobonus');
        // $member_table->startTrans();
        $pageallcount = $total_table->field('uid,level')->where(array('addtime' => $todayTime))->count();
        $pagerows = 200; //每次只取200条数据
        $pagecount = ceil($pageallcount / $pagerows); //总页数
        for ($z = 0; $z < $pagecount; $z++) {
            $pagestar = $z * $pagerows;
            $userlist = $total_table->order('id desc')->field('uid,level')->where(array('addtime' => $todayTime))->limit($pagestar . ',' . $pagerows)->select(); //找到前一天的数据（今天统计的新增业绩）
            $usercount = count($userlist);
            for ($i = 0; $i < $usercount; $i++) {
                $memberlevelInfo = $memberlevel_table->field('minmarketplace,middlemarketplace,daycap')->find($userlist[$i]['level']); //获取小市场，中市场，日封顶参数
                $userInfo = $member_table->field('lingdaoBonus,recommend,username')->find($userlist[$i]['uid']); //开发奖
                $list = $total_table->order('allmoney desc')->field('allmoney,uid')->where(array('pid' => $userlist[$i]['uid'], 'addtime' => $todayTime, 'allmoney' => array('neq', '0')))->select();
                $num = count($list);
                if ($num == 2) {
                    //计算中区利息

                    $middlebonus = $list[1]['allmoney'] * $memberlevelInfo['middlemarketplace'];
                    $kouchujiangjin = $middlebonus - $memberlevelInfo['daycap'];
                    if ($middlebonus <= $memberlevelInfo['daycap']) {//未到封顶值
                        $middlebonus = $this->splitbonus($userlist[$i]['uid'], $middlebonus, $list['1']['uid']); //奖金分割
                        $lingdaobonus = $userInfo['lingdaobonus'] + $middlebonus;
                        $bonus_table->add(array('type' => '2', 'uid' => $userlist[$i]['uid'], 'income' => $middlebonus, 'balance' => $lingdaobonus, 'create_date' => time(), 'message' => '两条线中市场开发奖', 'date' => date('Y-m-d H:i:s'), 'status' => '1', 'fid' => $list[1]['uid'], 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '3'));
                        $member_table->save(array('id' => $userlist[$i]['uid'], 'lingdaoBonus' => $lingdaobonus));
                        $lingdaobonus_table->add(array('uid' => $userlist[$i]['uid'], 'lingdaobonus' => $middlebonus, 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'message' => '两条线中市场开发奖')); //今天产生的开发奖

                        if ($userInfo['recommend'] > 0) {
                            $level = 1;
                            $this->daishujiang($middlebonus, $level, $userInfo['recommend'], $userlist[$i]['uid'], $userInfo['username']); //领导奖
                        }
                    } else {

                        //达到封顶值
                        $middlebonus = $this->splitbonus($userlist[$i]['uid'], $memberlevelInfo['daycap'], $list['1']['uid']); //奖金分割
                        $lingdaobonus = $userInfo['lingdaobonus'] + $middlebonus; //按最大封顶值计算
                        $bonus_table->add(array('type' => '2', 'uid' => $userlist[$i]['uid'], 'income' => $middlebonus, 'balance' => $lingdaobonus, 'create_date' => time(), 'message' => '两条线中市场开发奖（封顶值结算）', 'date' => date('Y-m-d H:i:s'), 'status' => '1', 'fid' => $list[1]['uid'], 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '3'));

                        if ($userInfo['recommend'] > 0) {
                            $level = 1;
                            $this->daishujiang($memberlevelInfo['daycap'], $level, $userInfo['recommend'], $userlist[$i]['uid'], $userInfo['username']); //领导奖
                        }
                    }
                    $balance = $list[0]['allmoney'] - $list[1]['allmoney']; //比大小余额
                    $balance_table->add(array('uid' => $list[0]['uid'], 'balance' => $balance, 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $tomorrow));
                } else if ($num == 3) {
                    //计算中区和小区利息
                    $middlebonus = $list[1]['allmoney'] * $memberlevelInfo['middlemarketplace']; //中市场利息
                    // $middlebonus = $this->splitbonus($userlist[$i]['uid'], $middlebonus, $list['1']['uid']); //奖金分割
                    $balance1 = $list[0]['allmoney'] - $list[1]['allmoney'];
                    $balance2 = ($balance1 >= $list[2]['allmoney']) ? $list[2]['allmoney'] : $balance1;
                    $uid = ($balance1 >= $list[2]['allmoney']) ? $list[0]['uid'] : $list[2]['uid'];
                    $minbonus = $balance2 * $memberlevelInfo['minmarketplace']; //小市场利息
                    // $minbonus = $this->splitbonus($userlist[$i]['uid'], $minbonus, $uid); //奖金分割
                    $TotalBonus = $middlebonus + $minbonus; //大小市场合计利息
                    if ($TotalBonus <= $memberlevelInfo['daycap']) {//未达到封顶值
                        $middlebonus = $this->splitbonus($userlist[$i]['uid'], $TotalBonus); //奖金分割

                        $middlelingdaobonus = $userInfo['lingdaobonus'] + $middlebonus; //结算中小市场
                        $bonus_table->add(array('type' => '2', 'uid' => $userlist[$i]['uid'], 'income' => $middlebonus, 'balance' => $middlelingdaobonus, 'create_date' => time(), 'message' => '三条线中小市场开发奖', 'date' => date('Y-m-d H:i:s'), 'status' => '1', 'fid' => $list[1]['uid'], 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '3'));

                        $member_table->save(array('id' => $userlist[$i]['uid'], 'lingdaoBonus' => $middlelingdaobonus));
                        $lingdaobonus_table->add(array('uid' => $userlist[$i]['uid'], 'lingdaobonus' => $TotalBonus, 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'message' => '三条线中小市场开发奖')); //今天产生的开发奖
                        if ($userInfo['recommend'] > 0) {
                            $level = 1;
                            $this->daishujiang($TotalBonus, $level, $userInfo['recommend'], $userlist[$i]['uid'], $userInfo['username']); //领导奖
                        }
                    } else {
                        //超出按照封顶值来计算
                        $middlebonus = $this->splitbonus($userlist[$i]['uid'], $memberlevelInfo['daycap']); //奖金分割

                        $middlelingdaobonus = $userInfo['lingdaobonus'] + $middlebonus; //结算中小市场
                        $bonus_table->add(array('type' => '2', 'uid' => $userlist[$i]['uid'], 'income' => $middlebonus, 'balance' => $middlelingdaobonus, 'create_date' => time(), 'message' => '三条线中小市场开发奖（封顶值结算）', 'date' => date('Y-m-d H:i:s'), 'status' => '1', 'fid' => $list[1]['uid'], 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '3'));



                        $member_table->save(array('id' => $userlist[$i]['uid'], 'lingdaoBonus' => $middlelingdaobonus));
                        //达到封顶值按封顶值计算

                        $lingdaobonus_table->add(array('uid' => $userlist[$i]['uid'], 'lingdaobonus' => $memberlevelInfo['daycap'], 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'message' => '三条线开发奖（封顶值）')); //今天产生的开发奖

                        if ($userInfo['recommend'] > 0) {
                            $level = 1;
                            $this->daishujiang($memberlevelInfo['daycap'], $level, $userInfo['recommend'], $userlist[$i]['uid'], $userInfo['username']); //领导奖
                        }
                    }
                    //  $lingdaobonus_table->add('uid'=>$userlist[$i]['uid'],'lingdaobonus'=>);
                    $balance = ($balance1 >= $list[2]['allmoney']) ? ($balance1 - $list[2]['allmoney']) : ($list[2]['allmoney'] - $balance1); //比大小余额
                    $balance_table->add(array('uid' => $uid, 'balance' => $balance, 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $tomorrow));
                } else if ($num == 1) {
                    $balance_table->add(array('uid' => $list[0]['uid'], 'balance' => $list[0]['allmoney'], 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $tomorrow));
                }
            }
        }
    }

    //领导奖
    protected function daishujiang($money, $level, $uid, $fid, $fname) {
        $member_table = M('member');
        $bonus_table = M('bonus');
        $todayTime = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
        $beginThismonth = mktime(0, 0, 0, date('m'), 1, date('Y'));  //当月第一天时间戳
        $week = mktime(0, 0, 0, date('m'), date('d') - date('w'), date('Y')); //当周第一天
        $userinfo = $member_table->field('recommend,daishubonus')->find($uid); //用户信息
        $count = $member_table->where(array('recommend' => $uid))->count(); //直线人数
        $counts = (empty($count)) ? 1 : $count + 1;
        $data = getbonusparam(); //获取的奖金比例参数
        $scale = array('1' => $data['daishuone'], $data['daishutwo'], $data['daishuthree'], $data['daishufour'], $data['daishufive'], $data['daishusix'], $data['daishuseven']); //奖金比例
        //$scale = C('DAISHULEVEL');
        if ($counts >= $level) {
            //  if (!empty($scale[$level]) && $scale[$level] != 0) {
            $userscale = $scale[$level] * $money;
            $userscales = $this->splitbonus($uid, $userscale, $fid); //奖金分割
            $allusermoney = $userinfo['daishubonus'] + $userscales;
            $member_table->save(array('id' => $uid, 'daishubonus' => $allusermoney));

            $bonus_table->add(array('uid' => $uid, 'type' => '23', 'income' => $userscales, 'balance' => $allusermoney, 'status' => '1', 'message' => '来自' . $fname . ':' . $level . '代领导奖', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '3'));
            //  }
        }
        if ($userinfo['recommend'] > 0 && $level <= 7) {
            $level++;
            self::daishujiang($money, $level, $userinfo['recommend'], $fid, $fname);
        }
    }

    //红包奖（算完开发奖后执行）
    protected function hongbaobonus() {
        $data = getbonusparam(); //获取的奖金比例参数
        $todayTime = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
        //$tomorrow = $todayTime + 60 * 60 * 24; //明天凌晨
        $yesterday = $todayTime - 60 * 60 * 24; //昨天凌晨
        $beginThismonth = mktime(0, 0, 0, date('m'), 1, date('Y'));  //当月第一天时间戳
        $week = mktime(0, 0, 0, date('m'), date('d') - date('w'), date('Y')); //当周第一天
        $member_table = M('member');
        $achievement_table = M('achievement');
        $lingdaobonus_table = M('lingdaobonus');
        $hongbaobonus_table = M('hongbaobonus');
        $bonus_table = M('bonus');
        if ($data['hongbao'] != 0) {
            $list = $achievement_table->order('create_date asc')->field('id,uid')->select(); //获取到的业绩表排单(最后一条算起（第一个人头）)
            if (!empty($list)) {
                $count = count($list);
                for ($i = ($count - 1); $i >= 0; $i--) {

                    $info1 = $lingdaobonus_table->field('lingdaobonus')->where(array('uid' => $list[$i + 1]['uid'], 'addtime' => $todayTime))->find(); //当前会员个人的开发奖
                    $info2 = $hongbaobonus_table->field('allsum')->where(array('uid' => $list[$i + 1]['uid'], 'addtime' => $todayTime))->find(); //该玩家那10层的总钱数
                    $info3 = $lingdaobonus_table->field('lingdaobonus')->where(array('uid' => $list[$i + $data['hongbaolevel'] + 1]['uid'], 'addtime' => $todayTime))->find(); //最后一个玩家的开发奖钱数
                    $money1 = (empty($info1)) ? 0 : $info1['lingdaobonus'];
                    $money2 = (empty($info2)) ? 0 : $info2['allsum'];
                    $money3 = (empty($info3)) ? 0 : $info3['lingdaobonus'];

                    $allsum = $money1 + $money2 - $money3; //下面十个会员的开发奖总和
                    if ($allsum != 0) {
                        $money = $allsum * $data['hongbao']; //该用户的红包奖

                        $usermoney = $this->splitbonus($list[$i]['uid'], $money); //奖金分割(实发奖金)

                        $hongbaobonus_table->add(array('uid' => $list[$i]['uid'], 'money' => $usermoney, 'allsum' => $allsum, 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime));
                        if ($allsum != 0) {
                            //更新钱包总数
                            $userinfo = $member_table->field('hongbaobonus')->find($list[$i]['uid']);
                            $allusermoney = $usermoney + $userinfo['hongbaobonus'];
                            $member_table->save(array('id' => $list[$i]['uid'], 'hongbaobonus' => $allusermoney));
                            //生成流水记录
                            $bonus_table->add(array('uid' => $list[$i]['uid'], 'type' => '12', 'income' => $usermoney, 'balance' => $allusermoney, 'status' => '1', 'message' => '红包奖', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '3'));
                        }
                    }
                }
            }
        }
    }

    //分红奖(每个月1号执行)
    protected function staticbonus() {
        $data = getbonusparam(); //获取的奖金比例参数
        $todayTime = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
        $lastmonth = mktime(0, 0, 0, date('m') - 1, 1, date('Y')); //上个月第一天的时间戳
        $beginThismonth = mktime(0, 0, 0, date('m'), 1, date('Y'));  //当月第一天时间戳
        $week = mktime(0, 0, 0, date('m'), date('d') - date('w'), date('Y')); //当周第一天
        //  $lastweek = mktime(0, 0, 0, date('m'), date('d') - date('w')-1, date('Y')); //
        $achievement_table = M('achievement');
        $member_table = M('member');
        $bonus_table = M('bonus');
        $pricemonth_table = M('pricemonth');
        $priceInfo = $pricemonth_table->where(array('month' => $beginThismonth))->find();
        if ($priceInfo) {
            $allachievement = $achievement_table->where(array('month' => $lastmonth))->sum('money'); //上个月的总市场业绩
            $allbonus = $bonus_table->where(array('month' => $lastmonth, 'status' => '1', 'action' => '3'))->sum('income'); //上个月的拨出的总利息（不包含扣掉的）

            $allprofit = ($allachievement - $allbonus) * $data['fenhongzonglirunhuafenbili']; //当月总利润百分之十
            $allintegral = $member_table->sum('integral'); //平台的总积分
            $monovalent = $priceInfo['price'];
            //$monovalent = $allprofit / $allintegral; //一积分等于的钱
            //$pricemonth_table->add(array('price' => $monovalent, 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth)); //保存每个月的积分单价
            //'regtime' => array('lt', $lastmonth), 
            $list = $member_table->field('id,fenhongbonus,integral')->where(array('regtime' => array('lt', $lastmonth), 'level' => array('neq', '1')))->select(); //取出上上个月的注册的会员，也就是必须满足注册了一个月的会员

            $count = count($list);
            for ($i = 0; $i < $count; $i++) {
                $allintegralmoney = $monovalent * $list[$i]['integral']; //

                $usermoney = $this->splitbonus($list[$i]['id'], $allintegralmoney); //奖金分割(实发奖金)

                $allusermoney = $list[$i]['fenhongbonus'] + $usermoney;
                $member_table->save(array('id' => $list[$i]['id'], 'fenhongbonus' => $allusermoney));
                $bonus_table->add(array('uid' => $list[$i]['id'], 'type' => '14', 'income' => $usermoney, 'balance' => $allusermoney, 'status' => '1', 'message' => '分红奖', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '3'));
            }
        }
    }

    //增值奖(每周执行一次)
    protected function zenZhibonus() {


        $week = mktime(0, 0, 0, date('m'), date('d') - date('w'), date('Y'));
        $lasweek = $week - 60 * 60 * 24 * 7;
        $todayTime = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
        $data = getbonusparam(); //获取的奖金比例参数
        //$achievement_table = M('achievement');
        //$bonus_table = M('bonus');
        //$integral_table = M('integral');//积分提现记录
        $priceweek_table = M('priceweek');
        //$allachievement = $achievement_table->where(array('week' => $lasweek))->sum('money'); //上-周的总业绩
        //$allbonus = $bonus_table->where(array('week' => $lasweek, 'status' => '1', 'action' => '3'))->sum('income'); //上-周的拨出的总利息
        //$allprofit = ($allachievement - $allbonus) * 0.10; //当周总利润百分之十
        //$number = $integral_table->where(array('status' => '1'))->sum('number'); //挂单的未完成的总数量
        if ($todayTime == $week) {
            $weekprice = $priceweek_table->where(array('week' => $lasweek))->find();
            if ($weekprice) {
                $Proportion = $data['fenhongzenzhang']; //自调比例
                $monovalent = $weekprice['price'] + $weekprice['price'] * $Proportion; //这个星期的单价
            } else {
                $monovalent = $data['fenhongdanjia']; //第一次的时候直接设置单价
            }
            $priceweek_table->add(array('price' => $monovalent, 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'week' => $week));
        }
    }

//月薪奖
    protected function yuexin() {
        $todayTime = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
        $beginThismonth = mktime(0, 0, 0, date('m'), 1, date('Y'));  //当月第一天时间戳
        $week = mktime(0, 0, 0, date('m'), date('d') - date('w'), date('Y')); //当周第一天
        $data = getbonusparam(); //获取的奖金比例参数
        $member_table = M('member');
        $bonus_table = M('bonus');
        $this->zhuanyichendianchi(); //先转移月薪奖池的钱到月薪奖池里

        $list = $member_table->field('id,yuexinBonus')->where(array('position' => array('egt', '1')))->select(); //-星才拿
        $count = count($list);
        for ($i = 0; $i < $count; $i++) {

            $level = 0;
            $o = 0;
            $bonus = $this->diguirecommend($list[$i]['id'], $level, $o); //下面十五层推荐关系的会员的月薪奖池的总金额
            if ($bonus != 0) {
                $money = $bonus * $data['yuexin']; //奖金
                $rels = $this->splitbonus($list[$i]['id'], $money);
                $allusermoney = $list[$i]['yuexinbonus'] + $rels;
                $member_table->save(array('id' => $list[$i]['id'], 'yuexinBonus' => $allusermoney));
                $bonus_table->add(array('uid' => $list[$i]['id'], 'income' => $rels, 'balance' => $allusermoney, 'type' => '17', 'status' => '1', 'message' => '月薪奖', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '3'));
            }
        }
    }

    protected function zhuanyichendianchi() {
        $member_table = M('member');
        $list = $member_table->field('id,chendianchi')->select();
        $count = count($list);
        for ($i = 0; $i < $count; $i++) {
            $member_table->save(array('id' => $list[$i]['id'], 'chendianbonus' => $list[$i]['chendianchi'], 'chendianchi' => '0'));
        }
    }

    protected function diguirecommend($rid, $level, $money) {
        if ($level >= 15) {//拿十五层
            return $money;
        }
        $level++;
        $member_table = M('member');
        $list = $member_table->field('id,recommend,chendianbonus')->where(array('recommend' => $rid))->select();
        $count = count($list);
        if (!empty($count)) {
            for ($i = 0; $i < $count; $i++) {
                $money+= self::diguirecommend($list[$i]['id'], $level, $list[$i]['chendianbonus']);
            }
        }
        return $money;
    }

    //奖金拆分
    public function splitbonus($uid, $bonusmoney, $fid = 0) {
        $todayTime = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
        $beginThismonth = mktime(0, 0, 0, date('m'), 1, date('Y'));  //当月第一天时间戳
        $week = mktime(0, 0, 0, date('m'), date('d') - date('w'), date('Y')); //当周第一天
        $data = getbonusparam(); //获取的奖金比例参数
        $member_table = M('member');
        $bonus_table = M('bonus');
        $userInfo = $member_table->field('gerensuodeshui,chongfuxiaofei,bieshuBonus,mingcheBonus,position,lvyouBonus,gongyijijin,yuexinBonus,chendianbonus,chendianchi')->find($uid);
        $chongfuxiaofei = 0;
        $gerensuodeshui = 0;
        $gongyijijin = 0;
        $lvyouBonus = 0;
        $chebonus = 0;
        $fangbonus = 0;

        $allchongfuxiaofei = $userInfo['chongfuxiaofei'];
        $allgerensuodeshui = $userInfo['gerensuodeshui'];
        $allgongyijijin = $userInfo['gongyijijin'];
        $alllvyouBonus = $userInfo['lvyoubonus'];
        $allchebonus = $userInfo['mingchebonus'];
        $allfangbonus = $userInfo['bieshubonus'];
        $allyuexin = $userInfo['yuexinbonus'];
        $allchendian = $userInfo['chendianbonus'];
        $allchendianchi = $userInfo['chendianchi'];
        if ($data['yuexinhuafen'] != 0) {

            $yuexinbonus = $bonusmoney * $data['yuexinhuafen'];
            //  $fafangyuexin = $yuexinbonus * $data['yuexin'];
            //  if ($fafangyuexin != 0) {
            //      $allyuexin = $allyuexin + $fafangyuexin;
            //      $bonus_table->add(array('uid' => $uid, 'income' => $fafangyuexin, 'balance' => $allyuexin, 'type' => '17', 'status' => '1', 'message' => '月薪奖', 'fid' => $fid, 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '12'));
            //  }
            //   $chendian = $yuexinbonus - $fafangyuexin;
            //    if ($chendian != 0) {
            //       $allchendian = $allchendian + $chendian;
            //      $bonus_table->add(array('uid' => $uid, 'income' => $chendian, 'balance' => $allchendian, 'type' => '18', 'status' => '1', 'message' => '月薪奖池', 'fid' => $fid, 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '12'));
            //  }  
            //月薪奖池（累计发月薪奖 发完后清空）
            $allchendianchi = $yuexinbonus + $userInfo['chendianchi'];
            $bonus_table->add(array('uid' => $uid, 'income' => $yuexinbonus, 'balance' => $allchendianchi, 'type' => '21', 'status' => '1', 'message' => '月薪奖池', 'fid' => $fid, 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '12'));

            // $bonus = $bonusmoney - $yuexinbonus; 
            $bonus = $bonusmoney;
        } else {
            $bonus = $bonusmoney;
        }

        $chongfuxiaofei = $bonus * $data['chongfuxiaofei']; //重消积分
        $gerensuodeshui = $bonus * $data['gerensuodeshui']; //个人所得税
        $gongyijijin = $bonus * $data['gongyijijin']; //公益基金
        $allchongfuxiaofei = $allchongfuxiaofei + $chongfuxiaofei;
        $allgerensuodeshui = $allgerensuodeshui + $gerensuodeshui;
        $allgongyijijin = $allgongyijijin + $gongyijijin;
        if ($userInfo['position'] >= 1) {//大于等于三星享受旅游基金
            $lvyouBonus = $bonus * $data['lvyouBonus']; //旅行奖
            $alllvyouBonus = $lvyouBonus + $alllvyouBonus;
            if ($lvyouBonus != 0) {
                $bonus_table->add(array('uid' => $uid, 'income' => $lvyouBonus, 'balance' => $alllvyouBonus, 'type' => '15', 'status' => '1', 'message' => '旅游基金', 'fid' => $fid, 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '12'));
            }
        }
        if ($userInfo['position'] >= 2) {//大于等于四星享受名车基金
            $chebonus = $bonus * $data['mingchejiang']; //名车基金
            $allchebonus = $chebonus + $allchebonus;
            if ($chebonus != 0) {
                $bonus_table->add(array('uid' => $uid, 'income' => $chebonus, 'balance' => $allchebonus, 'type' => '8', 'status' => '1', 'message' => '名车积分', 'fid' => $fid, 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '12'));
            }
        }
        if ($userInfo['position'] >= 3) {//大于等于五星享受别墅基金
            $fangbonus = $bonus * $data['bieshujiang']; //别墅基金
            $allfangbonus = $fangbonus + $allfangbonus;
            if ($fangbonus != 0) {
                $bonus_table->add(array('uid' => $uid, 'income' => $fangbonus, 'balance' => $allfangbonus, 'type' => '11', 'status' => '1', 'message' => '别墅基金', 'fid' => $fid, 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '12'));
            }
        }
        if ($chongfuxiaofei != 0) {
            $bonus_table->add(array('uid' => $uid, 'income' => $chongfuxiaofei, 'balance' => $allchongfuxiaofei, 'type' => '9', 'status' => '1', 'message' => '重消积分', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '12'));
        }
        if ($gerensuodeshui != 0) {
            $bonus_table->add(array('uid' => $uid, 'income' => $gerensuodeshui, 'balance' => $allgerensuodeshui, 'type' => '7', 'status' => '1', 'message' => '个人所得税', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '12'));
        }
        if ($gongyijijin != 0) {
            $bonus_table->add(array('uid' => $uid, 'income' => $gongyijijin, 'balance' => $allgongyijijin, 'type' => '16', 'status' => '1', 'message' => '公益基金', 'fid' => $fid, 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '12'));
        }
        $member_table->save(array('id' => $uid, 'chongfuxiaofei' => $allchongfuxiaofei, 'gerensuodeshui' => $allgerensuodeshui, 'gongyijijin' => $allgongyijijin, 'lvyouBonus' => $alllvyouBonus, 'mingcheBonus' => $allchebonus, 'bieshuBonus' => $allfangbonus, 'yuexinBonus' => $allyuexin, 'chendianbonus' => $allchendian, 'chendianchi' => $allchendianchi));
        $userbonus = $bonus - ($chongfuxiaofei + $gerensuodeshui + $gongyijijin + $lvyouBonus + $chebonus + $fangbonus + $yuexinbonus);
        return $userbonus;
    }

    /*
     *
     * 检查线下的用户等级（直推用户）
     * uid 用户id
     * money 团队业绩
     */

    //0星升级一星
    protected function oneFindLineLevel($uid, $money) {
        $member_table = M('member');
        $data = getpositionterm('1');

        if ($money >= $data['middle']) {
            $member_table->save(array('id' => $uid, 'position' => '1'));
        }
    }

    //一星升级二星
    protected function towFindLineLevel($uid, $money) {
        $member_table = M('member');
        $data = getpositionterm('2');
        if ($money >= $data['middle']) {
            $member_table->save(array('id' => $uid, 'position' => '2'));
        }
    }

    //二星升级三星
    protected function threeFindLineLevel($uid, $money) {
        $member_table = M('member');
        $data = getpositionterm('3');
        if ($money >= $data['middle']) {
            $member_table->save(array('id' => $uid, 'position' => '3'));
        }
    }

    //三星升级四星
    protected function fourFindLineLevel($uid, $money, $minmony) {
        $member_table = M('member');
        $data = getpositionterm('4');
        if ($money >= $data['middle'] && $minmony >= $data['min']) {
            $member_table->save(array('id' => $uid, 'position' => '4'));
        }
    }

    //四星升五星
    protected function fiveFindLineLevel($uid, $money, $minmony) {
        $member_table = M('member');
        $data = getpositionterm('5');
        if ($money >= $data['middle'] && $minmony >= $data['min']) {
            $member_table->save(array('id' => $uid, 'position' => '5'));
        }
    }

    //五星升董事
    protected function sixFindLineLevel($uid, $money, $minmony) {
        $member_table = M('member');
        $data = getpositionterm('6');
        if ($money >= $data['middle'] && $minmony >= $data['min']) {
            $member_table->save(array('id' => $uid, 'position' => '6'));
        }
    }

    // 晋级奖 (月结)（执行后在升级，每个用户第一次升级是不计算晋级奖的，升完级的下个月才有晋级奖）
    protected function jinjiBonus() {
        $lastmonth = mktime(0, 0, 0, date('m') - 1, 1, date('Y')); //上个月第一天的时间戳
        $todayTime = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
        $beginThismonth = mktime(0, 0, 0, date('m'), 1, date('Y'));  //当月第一天时间戳
        $week = mktime(0, 0, 0, date('m'), date('d') - date('w'), date('Y')); //当周第一天
        $member_table = M('member');
        $achievement = M('achievement');
        $position_table = M('position');
        $bonus_table = M('bonus');
        $yiji = $achievement->where(array('month' => $lastmonth))->sum('money'); //上个月的新增业绩


        $list = $member_table->field('id,position,jinjiBonus')->where(array('position' => array('neq', '0')))->select(); //获取到有职位的用户
        $onestarcount = $member_table->where(array('position' => '1'))->count(); //一星用户个数
        $towstarcount = $member_table->where(array('position' => '2'))->count(); //二星用户个数
        $threestarcount = $member_table->where(array('position' => '3'))->count(); //三星用户个数
        $fourstarcount = $member_table->where(array('position' => '4'))->count(); //四星用户个数
        $fivestarcount = $member_table->where(array('position' => '5'))->count(); //五星用户个数
        $sixstarcount = $member_table->where(array('position' => '6'))->count(); //董事用户个数

        $onescale = $position_table->field('jinjiscale')->find(1); //获取一星比例
        $towscale = $position_table->field('jinjiscale')->find(2); //获取二星比例
        $threescale = $position_table->field('jinjiscale')->find(3); //获取三星比例
        $fourscale = $position_table->field('jinjiscale')->find(4); //获取四星比例
        $fivescale = $position_table->field('jinjiscale')->find(5); //获取五星比例
        $sixscale = $position_table->field('jinjiscale')->find(6); //获取董事比例

        $onemoney = $yiji * $onescale['jinjiscale'] / $onestarcount; //一个人分的钱（一星用户一个人分的钱）
        $towmoney = $yiji * $towscale['jinjiscale'] / $towstarcount; //一个人分的钱（二星用户一个人分的钱）
        $threemoney = $yiji * $threescale['jinjiscale'] / $threestarcount; //一个人分的钱（三星用户一个人分的钱）
        $fourmoney = $yiji * $fourscale['jinjiscale'] / $fourstarcount; //一个人分的钱（四星用户一个人分的钱）
        $fivemoney = $yiji * $fivescale['jinjiscale'] / $fivestarcount; //一个人分的钱（五星用户一个人分的钱）
        $sixmoney = $yiji * $sixscale['jinjiscale'] / $sixstarcount; //一个人分的钱（董事星用户一个人分的钱）

        $count = count($list);
        for ($i = 0; $i < $count; $i++) {

            switch ($list[$i]['position']) {
                case 1:
                    $usermoney = $this->splitbonus($list[$i]['id'], $onemoney);
                    $userallmoney = $list[$i]['jinjibonus'] + $usermoney;
                    $member_table->save(array('id' => $list[$i]['id'], 'jinjiBonus' => $userallmoney));
                    $bonus_table->add(array('uid' => $list[$i]['id'], 'type' => '4', 'income' => $usermoney, 'balance' => $userallmoney, 'status' => '1', 'message' => '晋升奖', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '3'));

                    break;
                case 2:
                    $usermoney = $this->splitbonus($list[$i]['id'], $towmoney);
                    $userallmoney = $list[$i]['jinjibonus'] + $usermoney;
                    $member_table->save(array('id' => $list[$i]['id'], 'jinjiBonus' => $userallmoney));
                    $bonus_table->add(array('uid' => $list[$i]['id'], 'type' => '4', 'income' => $usermoney, 'balance' => $userallmoney, 'status' => '1', 'message' => '晋升奖', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '3'));

                    break;
                case 3:
                    $usermoney = $this->splitbonus($list[$i]['id'], $threemoney);
                    $userallmoney = $list[$i]['jinjibonus'] + $usermoney;
                    $member_table->save(array('id' => $list[$i]['id'], 'jinjiBonus' => $userallmoney));
                    $bonus_table->add(array('uid' => $list[$i]['id'], 'type' => '4', 'income' => $usermoney, 'balance' => $userallmoney, 'status' => '1', 'message' => '晋升奖', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '3'));

                    break;
                case 4:
                    $usermoney = $this->splitbonus($list[$i]['id'], $fourmoney);
                    $userallmoney = $list[$i]['jinjibonus'] + $usermoney;
                    $member_table->save(array('id' => $list[$i]['id'], 'jinjiBonus' => $userallmoney));
                    $bonus_table->add(array('uid' => $list[$i]['id'], 'type' => '4', 'income' => $usermoney, 'balance' => $userallmoney, 'status' => '1', 'message' => '晋升奖', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '3'));

                    break;
                case 5:
                    $usermoney = $this->splitbonus($list[$i]['id'], $fivemoney);
                    $userallmoney = $list[$i]['jinjibonus'] + $usermoney;
                    $member_table->save(array('id' => $list[$i]['id'], 'jinjiBonus' => $userallmoney));
                    $bonus_table->add(array('uid' => $list[$i]['id'], 'type' => '4', 'income' => $usermoney, 'balance' => $userallmoney, 'status' => '1', 'message' => '晋升奖', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '3'));

                    break;
                case 6:
                    $usermoney = $this->splitbonus($list[$i]['id'], $sixmoney);
                    $userallmoney = $list[$i]['jinjibonus'] + $usermoney;
                    $member_table->save(array('id' => $list[$i]['id'], 'jinjiBonus' => $userallmoney));
                    $userallmoney = $list[$i]['jinjibonus'] + $sixmoney;
                    $bonus_table->add(array('uid' => $list[$i]['id'], 'type' => '4', 'income' => $usermoney, 'balance' => $userallmoney, 'status' => '1', 'message' => '晋升奖', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '3'));


                    break;
            }
        }
    }

    //每月检查升级（每月检查一次）（1）
    protected function upgrade() {
        $shichangtotal_table = M('shichangtotal');
        $member_table = M('member');
        $list = $shichangtotal_table->select(); //每个用户的市场总业绩
        $count = count($list);
        for ($i = 0; $i < $count; $i++) {
            $userInfo = $member_table->field('position')->find($list[$i]['uid']); //会员信息
            // if ($list[$i]['right'] != 0) {//左中右区都开方了。
            $achievement = array($list[$i]['left'], $list[$i]['center'], $list[$i]['right']);
            arsort($achievement); //降序排序
            switch ($userInfo['position']) {
                case 0:$this->oneFindLineLevel($list[$i]['uid'], $achievement[1]);
                    break;
                case 1:$this->towFindLineLevel($list[$i]['uid'], $achievement[1]);
                    break;
                case 2:$this->threeFindLineLevel($list[$i]['uid'], $achievement[1]);
                    break;
                case 3:$this->fourFindLineLevel($list[$i]['uid'], $achievement[1], $achievement[2]);
                    break;
                case 4:$this->fiveFindLineLevel($list[$i]['uid'], $achievement[1], $achievement[2]);
                    break;
                case 5:$this->sixFindLineLevel($list[$i]['uid'], $achievement[1], $achievement[2]);
                    break;
            }
            //   } 
//            else {
//                //只开放了左中区
//                $achievement = array($list[$i]['left'], $list[$i]['center']);
//                arsort($achievement); //降序排序
//                switch ($userInfo['position']) {
//                    case 0:$this->oneFindLineLevel($list[$i]['uid'], $achievement[1]);
//                        break;
//                    case 1:$this->towFindLineLevel($list[$i]['uid'], $achievement[1]);
//                        break;
//                    case 2:$this->threeFindLineLevel($list[$i]['uid'], $achievement[1]);
//                        break;
//                    case 3:$this->fourFindLineLevel($list[$i]['uid'], $achievement[1]);
//                        break;
//                    case 4:$this->fiveFindLineLevel($list[$i]['uid'], $achievement[1]);
//                        break;
//                    case 5:$this->sixFindLineLevel($list[$i]['uid'], $achievement[1]);
//                        break;
//                }
//            }
        }
    }

    //管理奖（月结）（3）
    protected function guanliBonus() {
        $lastmonth = mktime(0, 0, 0, date('m') - 1, 1, date('Y')); //上个月第一天的时间戳
        $member_table = M('member');
        $list = $member_table->field('id,recommend,position,username')->where(array('position' => array('neq', '0')))->select(); //有职位的会员

        $groupmonth_table = M('groupmoth');
        $count = count($list);
        for ($i = 0; $i < $count; $i++) {
            $money = $groupmonth_table->where(array('uid' => $list[$i]['id'], 'month' => $lastmonth))->sum('groupmoney'); //当前用户的上个月的团队业绩
            if (!empty($money)) {
                if ($list[$i]['recommend'] != 0) {
                    $level = 1;
                    $this->guanlidigui($list[$i]['id'], $list[$i]['position'], $list[$i]['username'], $list[$i]['recommend'], $money, $level);
                }
            }
        }
    }

    //管理奖递归函数
    protected function guanlidigui($uid, $uposition, $username, $rid, $money, $level) {
        $todayTime = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
        $beginThismonth = mktime(0, 0, 0, date('m'), 1, date('Y'));  //当月第一天时间戳
        $week = mktime(0, 0, 0, date('m'), date('d') - date('w'), date('Y')); //当周第一天
        $member_table = M('member');
        $bonus_table = M('bonus');
        $userInfo = $member_table->field('recommend,guanliBonus,position')->find($rid); //获取到推荐人的推荐人信息，自己的管理奖和职位
        $data = getbonusparam(); //获取的奖金比例参数
        $scale = array('2' => $data['guanlitwo'], $data['guanlithree'], $data['guanlifour'], $data['guanlifive'], $data['guanlisix']); //奖金比例
        if ($userInfo['position'] >= 2) {//二级以上职位才有拿
            if ($level == 1) {//判断直推跟他等级是否相等
                if ($uposition == $userInfo['position']) {//平级
                    $scalemoney = $money * $data['guanlione'];
                    if ($scalemoney != 0) {
                        $usermoney = $this->splitbonus($rid, $scalemoney);
                        $userallmoney = $userInfo['guanlibonus'] + $usermoney;
                        $member_table->save(array('id' => $rid, 'guanliBonus' => $userallmoney));
                        $bonus_table->add(array('uid' => $rid, 'type' => '3', 'income' => $usermoney, 'balance' => $userallmoney, 'fid' => $uid, 'status' => '1', 'message' => '来自' . $username . '第1代管理奖（平级）', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '3'));
                    }
                } else {//不平级
                    $scalemoney = $money * $scale[$userInfo['position']]; //利息
                    if ($scalemoney != 0) {
                        $usermoney = $this->splitbonus($rid, $scalemoney);
                        $userallmoney = $userInfo['guanlibonus'] + $usermoney;
                        $member_table->save(array('id' => $rid, 'guanliBonus' => $userallmoney));
                        $bonus_table->add(array('uid' => $rid, 'type' => '3', 'income' => $usermoney, 'balance' => $userallmoney, 'fid' => $uid, 'status' => '1', 'message' => '来自' . $username . '第' . ($userInfo['position'] - $uposition + 1) . '代管理奖', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '3'));
                    }
                }
            } else {
                $scalemoney = $money * $scale[$userInfo['position']]; //利息
                if ($scalemoney != 0) {
                    $usermoney = $this->splitbonus($rid, $scalemoney);
                    $userallmoney = $userInfo['guanlibonus'] + $usermoney;
                    $member_table->save(array('id' => $rid, 'guanliBonus' => $userallmoney));
                    $bonus_table->add(array('uid' => $rid, 'type' => '3', 'income' => $usermoney, 'balance' => $userallmoney, 'fid' => $uid, 'status' => '1', 'message' => '来自' . $username . '第' . ($userInfo['position'] - $uposition + 1) . '代管理奖', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '3'));
                }
            }
            $level++; //用户每拿一次就加-，一共可以拿七个
        }
        if ($level < 7 && $userInfo['recommend'] != 0) {

            self::guanlidigui($uid, $uposition, $username, $userInfo['recommend'], $money, $level);
        }
    }

    //全球分红（月结）（4）
    protected function quanqiufenhong() {
        $lastmonth = mktime(0, 0, 0, date('m') - 1, 1, date('Y')); //上个月第一天的时间戳
        $todayTime = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
        $beginThismonth = mktime(0, 0, 0, date('m'), 1, date('Y'));  //当月第一天时间戳
        $week = mktime(0, 0, 0, date('m'), date('d') - date('w'), date('Y')); //当周第一天
        $data = getbonusparam(); //获取的奖金比例参数
        $achievement = M('achievement');
        $member_table = M('member');
        $bonus_table = M('bonus');
        $yiji = $achievement->where(array('month' => $lastmonth))->sum('money'); //上个月的新增业绩
        $fourposition = $member_table->field('id,quanqiufenhongBonus')->where(array('position' => '4'))->select();
        $fiveposition = $member_table->field('id,quanqiufenhongBonus')->where(array('position' => '5'))->select();
        $sixposition = $member_table->field('id,quanqiufenhongBonus')->where(array('position' => '6'))->select();
        $fourcount = count($fourposition);
        $fivecount = count($fiveposition);
        $sixcount = count($sixposition);
        $fourmoney = $yiji * $data['quanqiufenhongfour']; //
        $fivemoney = $yiji * $data['quanqiufenhongfive'];
        $sixmoney = $yiji * $data['quanqiufenhongdongshi'];
        $forureverybodymoney = $fourmoney / $fourcount; //每个人分的钱
        $fiveeverybodymoney = $fivemoney / $fivecount; //每个人分的钱
        $sixeverybodymoney = $sixmoney / $sixcount; //每个人分的钱
        if ($fourmoney != 0) {
            for ($i = 0; $i < $fourcount; $i++) {
                $usermoney = $this->splitbonus($fourposition[$i]['id'], $forureverybodymoney);
                $userallmoney = $fourposition[$i]['quanqiufenhongbonus'] + $usermoney;
                $member_table->save(array('id' => $fourposition[$i]['id'], 'quanqiufenhongBonus' => $userallmoney));
                $bonus_table->add(array('uid' => $fourposition[$i]['id'], 'type' => '5', 'income' => $usermoney, 'balance' => $userallmoney, 'status' => '1', 'message' => '全球分红', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '3'));
            }
        }
        if ($fivemoney != 0) {
            for ($j = 0; $j < $fivecount; $j++) {
                $usermoney = $this->splitbonus($fiveposition[$j]['id'], $fiveeverybodymoney);
                $userallmoney = $fourposition[$j]['quanqiufenhongbonus'] + $usermoney;
                $member_table->save(array('id' => $fiveposition[$j]['id'], 'quanqiufenhongBonus' => $userallmoney));
                $bonus_table->add(array('uid' => $fiveposition[$j]['id'], 'type' => '5', 'income' => $usermoney, 'balance' => $userallmoney, 'status' => '1', 'message' => '全球分红', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '3'));
            }
        }
        if ($sixmoney != 0) {
            for ($k = 0; $k < $sixcount; $k++) {
                $usermoney = $this->splitbonus($sixposition[$k]['id'], $sixeverybodymoney);
                $userallmoney = $fourposition[$k]['quanqiufenhongbonus'] + $usermoney;
                $member_table->save(array('id' => $sixposition[$k]['id'], 'quanqiufenhongBonus' => $userallmoney));
                $bonus_table->add(array('uid' => $sixposition[$k]['id'], 'type' => '5', 'income' => $usermoney, 'balance' => $userallmoney, 'status' => '1', 'message' => '全球分红', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '3'));
            }
        }
    }

    //每个月的团队新增业绩(推荐关系)（上个月的新增团队业绩）（计算号每个用户当月的业绩后计算管理奖）
    protected function groupmonth() {
        $lastmonth = mktime(0, 0, 0, date('m') - 1, 1, date('Y')); //上个月第一天的时间戳
        $todayTime = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
        $member_table = M('member');
        $list = $member_table->select();
        $groupday_table = M('groupday'); //每日业绩
        $groupmonth_table = M('groupmoth');
        $count = count($list);
        for ($i = 0; $i < $count; $i++) {
            $monthgroupmoney = $groupday_table->where(array('uid' => $list[$i]['id'], 'month' => $lastmonth))->sum('money'); //上个月的团队业绩
            if (!empty($monthgroupmoney)) {
                $groupmonth_table->add(array('uid' => $list[$i]['id'], 'groupmoney' => $monthgroupmoney, 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $lastmonth));
            }
        }
    }

    //每周的积分自动产生奖金 （按积分的百分之二的比例转入到分红钱包）
    /* protected function weekbonus() {
      $todayTime = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
      $beginThismonth = mktime(0, 0, 0, date('m'), 1, date('Y'));  //当月第一天时间戳
      $week = mktime(0, 0, 0, date('m'), date('d') - date('w'), date('Y')); //当周第一天
      $member_table = M('member');
      $bonus_table = M('bonus');
      $weekfenhong = M('weekfenhong');
      $memberlevel = M('memberlevel');
      $list = $member_table->field('id,integral,fenhongbonus,level')->where(array('status' => '1', 'level' => array('in', '2,3,4')))->select(); //取正常用户的积分
      $count = count($list);
      for ($i = 0; $i < $count; $i++) {

      $rel = $weekfenhong->where(array('uid' => $list[$i]['id']))->find();
      if ($rel) {
      $info = $memberlevel->field('registermoney')->find($list[$i]['level']);
      if ($rel['money'] < $info['registermoney']) {
      $usermoneys = $list[$i]['integral'] * 0.02; //积分的百分之二
      $usermoney = $this->splitbonus($list[$i]['id'], $usermoneys);
      $allusermoney = $list[$i]['fenhongbonus'] + $usermoney;

      $weekfenhong->save(array('id' => $rel['id'], 'money' => $rel['money'] + $usermoneys, 'create_date' => time()));
      $member_table->save(array('id' => $list[$i]['id'], 'fenhongbonus' => $allusermoney));

      $bonus_table->add(array('uid' => $list[$i]['id'], 'type' => '14', 'income' => $usermoney, 'balance' => $allusermoney, 'status' => '1', 'message' => '分红奖.', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '3'));
      }
      } else {


      $usermoneys = $list[$i]['integral'] * 0.02; //积分的百分之二
      $usermoney = $this->splitbonus($list[$i]['id'], $usermoneys);
      $allusermoney = $list[$i]['fenhongbonus'] + $usermoney;

      $weekfenhong->add(array('uid' => $list[$i]['id'], 'money' => $usermoneys, 'create_date' => time()));

      $member_table->save(array('id' => $list[$i]['id'], 'fenhongbonus' => $allusermoney));
      $bonus_table->add(array('uid' => $list[$i]['id'], 'type' => '14', 'income' => $usermoney, 'balance' => $allusermoney, 'status' => '1', 'message' => '分红奖.', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '3'));
      }
      }
      } */

    //每周的积分自动产生奖金 （按积分的百分之二的比例转入到分红钱包）
    protected function weekbonus() {
        $todayTime = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
        $beginThismonth = mktime(0, 0, 0, date('m'), 1, date('Y'));  //当月第一天时间戳
        $week = mktime(0, 0, 0, date('m'), date('d') - date('w'), date('Y')); //当周第一天
        $member_table = M('member');
        $bonus_table = M('bonus');
        $weekzengsong_table = M('weekzengsong');
        $achievement_table = M('achievement');
        ///$memberlevel = M('memberlevel');
        $list = $member_table->field('id,integral,level')->where(array('status' => '1', 'level' => array('in', '2,3,4,5')))->select(); //取正常用户的积分

        $bounslist = array('1800' => '18', '9000' => '90', '18000' => '180', '36000' => '360'); //对应等级每周赠送的积分

        $count = count($list);
        for ($i = 0; $i < $count; $i++) {
            $xiaofeimoney =  intval( $achievement_table->where(array('uid' => $list[$i]['id']))->sum('money'));
            $rel = $weekzengsong_table->where(array('uid' => $list[$i]['id']))->find();
            if ($rel) {
                //$info = $memberlevel->field('registermoney')->find($list[$i]['level']);
                if ($rel['total'] <= 50) {//五十周
                    //$usermoneys = $list[$i]['integral'] * 0.02; //积分的百分之二 
                    // $usermoney = $this->splitbonus($list[$i]['id'], $usermoneys);
                    // $allusermoney = $list[$i]['fenhongbonus'] + $usermoney;
                    $usermoneys = $bounslist[$xiaofeimoney]; //赠送积分
                    if (!empty($usermoneys)) {
                        $allusermoney = $list[$i]['integral'] + $usermoneys;

                        $weekzengsong_table->save(array('id' => $rel['id'], 'money' => $rel['money'] + $usermoneys, 'create_date' => time(), 'total' => $rel['total'] + 1));
                        $member_table->save(array('id' => $list[$i]['id'], 'integral' => $allusermoney));

                        $bonus_table->add(array('uid' => $list[$i]['id'], 'type' => '10', 'income' => $usermoneys, 'balance' => $allusermoney, 'status' => '1', 'message' => '赠送积分（周）.', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '3'));
                    }
                }
            } else {


                //$usermoneys = $list[$i]['integral'] * 0.02; //积分的百分之二 
                //$usermoney = $this->splitbonus($list[$i]['id'], $usermoneys);
                //$allusermoney = $list[$i]['fenhongbonus'] + $usermoney;

                $usermoneys = $bounslist[$xiaofeimoney]; //赠送积分
                $allusermoney = $list[$i]['integral'] + $usermoneys;
                if (!empty($usermoneys)) {
                    $weekzengsong_table->add(array('uid' => $list[$i]['id'], 'money' => $usermoneys, 'create_date' => time(), 'total' => '1'));

                    $member_table->save(array('id' => $list[$i]['id'], 'integral' => $allusermoney));
                    $bonus_table->add(array('uid' => $list[$i]['id'], 'type' => '10', 'income' => $usermoneys, 'balance' => $allusermoney, 'status' => '1', 'message' => '赠送积分（周）.', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '3'));
                }
            }
        }
    }

    //memberlevel id=5 的用户，一百二十天发一次奖金后每隔二十天发一次奖金，一共一年时间
    protected function maxfenhong() {
        $todayTime = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
        $member_table = M('member');
        $achievement_table = M('achievement');
        $list = $member_table->field('id,integral,fenhongbonus')->where(array('status' => '1', 'level' => 5))->select(); //取正常用户的积分
        $count = count($list);
        for ($i = 0; $i < $count; $i++) {
            $info = $achievement_table->field('addtime')->where(array('uid' => $list[$i]['id']))->find();
            $day = ($todayTime - $info['addtime']) / 86400;
            $this->bonus($list[$i]['id'], $list[$i]['fenhongbonus'], $day);
        }
    }

    protected function bonus($uid, $fenhongbonus, $day) {
        $data = getbonusparam(); //获取的奖金比例参数
        $todayTime = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
        $beginThismonth = mktime(0, 0, 0, date('m'), 1, date('Y'));  //当月第一天时间戳
        $week = mktime(0, 0, 0, date('m'), date('d') - date('w'), date('Y')); //当周第一天
        $member_table = M('member');
        $bonus_table = M('bonus');
        switch ($day) {
            case 120:
                if ($data['zengsong_status'] == 1) {
                    $usermoneys = $data['zengsongfenhong'];
                } else {
                    $usermoneys = 6000;
                }
                $usermoney = $this->splitbonus($list[$i]['id'], $usermoneys);
                $allusermoney = $fenhongbonus + $usermoney;
                $member_table->save(array('id' => $uid, 'fenhongbonus' => $allusermoney));
                $bonus_table->add(array('uid' => $uid, 'type' => '14', 'income' => $usermoney, 'balance' => $allusermoney, 'status' => '1', 'message' => '120天分红奖', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '3'));

                break;
            case 180:
                $usermoneys = 6000;
                $usermoney = $this->splitbonus($list[$i]['id'], $usermoneys);
                $allusermoney = $fenhongbonus + $usermoney;
                $member_table->save(array('id' => $uid, 'fenhongbonus' => $allusermoney));
                $bonus_table->add(array('uid' => $uid, 'type' => '14', 'income' => $usermoney, 'balance' => $allusermoney, 'status' => '1', 'message' => '180天分红奖.', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '3'));

                break;
            case 240:
                $usermoneys = 6000;
                $usermoney = $this->splitbonus($list[$i]['id'], $usermoneys);
                $allusermoney = $fenhongbonus + $usermoney;
                $member_table->save(array('id' => $uid, 'fenhongbonus' => $allusermoney));
                $bonus_table->add(array('uid' => $uid, 'type' => '14', 'income' => $usermoney, 'balance' => $allusermoney, 'status' => '1', 'message' => '240天分红奖', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '3'));

                break;
            case 300:
                $usermoneys = 6000;
                $usermoney = $this->splitbonus($list[$i]['id'], $usermoneys);
                $allusermoney = $fenhongbonus + $usermoney;
                $member_table->save(array('id' => $uid, 'fenhongbonus' => $allusermoney));
                $bonus_table->add(array('uid' => $uid, 'type' => '14', 'income' => $usermoney, 'balance' => $allusermoney, 'status' => '1', 'message' => '300天分红奖.', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '3'));

                break;
            case 360:
                $usermoneys = 6000;
                $usermoney = $this->splitbonus($list[$i]['id'], $usermoneys);
                $allusermoney = $fenhongbonus + $usermoney;
                $member_table->save(array('id' => $uid, 'fenhongbonus' => $allusermoney));
                $bonus_table->add(array('uid' => $uid, 'type' => '14', 'income' => $usermoney, 'balance' => $allusermoney, 'status' => '1', 'message' => '360天分红奖.', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '3'));

                break;
        }
    }

}
