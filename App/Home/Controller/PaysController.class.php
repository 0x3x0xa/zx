<?php

namespace Home\Controller;

use Home\Controller\PayController;

class PaysController extends PayController {


    protected function create_randnum() {
        $str = "1,2,3,4,5,6,7,8,9,0";
        $list = explode(",", $str);
        $cmax = count($list) - 1;
        for ($i = 0; $i < 10; $i++) {
            $randnum = mt_rand(0, $cmax);
            $verifyCode .= $list[$randnum];
        }

        return $verifyCode;
    }
      public function newpay($billno) {
        $paylist_table = M('paylist');
        $info = $paylist_table->where(array('billno' => $billno,'status'=>'2'))->find();
        if ($info) {
            $datainfo = getpayparam();
            //商户号
            $MerNo = $datainfo['username'];
            //MD5私钥
            $MD5key = $datainfo['md5pwd'];
            $BillNo = $info["billno"];
            $Amount = $info['amount'];    //[必填]订单金额
         
            $ReturnURL = "http://" . $datainfo['message'] . "/Home/Pays/PayResult";    //[必填]返回数据给商户的地址(商户自己填写):::注意请在测试前将该地址告诉我方人员;否则测试通不过

            $AdviceURL = "http://" . $datainfo['message'] . "/Home/Pays/Paylogic";   //[必填]支付完成后，后台接收支付结果，可用来更新数据库值
            $Remark = "";  //[选填]升级。

            $OrderTime = date('YmdHis');   //[必填]交易时间YYYYMMDDHHMMSS
            $BillNo = $BillNo . $this->create_randnum();
            $md5src = "MerNo=" . $MerNo . "&BillNo=" . $BillNo . "&Amount=" . $Amount . "&OrderTime=" . $OrderTime . "&ReturnURL=" . $ReturnURL . "&AdviceURL=" . $AdviceURL . "&" . $MD5key;  //校验源字符串
            $SignInfo = strtoupper(md5($md5src));  //MD5检验结果
            //送货信息(方便维护，请尽量收集！如果没有以下信息提供，请传空值:'')
            //因为关系到风险问题和以后商户升级的需要，如果有相应或相似的内容的一定要收集，实在没有的才赋空值,谢谢。
            $products = "1商品"; // '------------------物品信息

            $data = array(
                '1' => $MerNo,
                '2' => $BillNo,
                '3' => $Amount,
                '4' => $ReturnURL,
                '5' => $AdviceURL,
                '6' => $Remark,
                '7' => $OrderTime,
                '8' => $SignInfo,
                '9' => $products,
                '10' => $info['banknum'],
            );
            C('TOKEN_ON', false);
            $this->assign('data', $data);
            $this->display();
        } else {
            $this->error('操作失败,订单号不存在');
            exit;
        }
    }

    //同步：页面跳转
    public function PayResult() {


        $todayTime = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
        $beginThismonth = mktime(0, 0, 0, date('m'), 1, date('Y'));  //当月第一天时间戳
        $week = mktime(0, 0, 0, date('m'), date('d') - date('w'), date('Y')); //当周第一天
        $paylist_table = M('paylist');
        $bonus_table = M('bonus');
        $member_table = M('member');
        $member_table->startTrans();
        $datainfo = getpayparam();
        //商户号
        $MerNo = $datainfo['username'];
        //MD5私钥
        $MD5key = $datainfo['md5pwd'];
        //订单号
        $BillNo = $_POST["BillNo"];

        //一麻袋支付订单号
        $OrderNo = $_POST["OrderNo"];
        //金额
        $Amount = $_POST["Amount"];
        //支付状态
        $Succeed = $_POST["Succeed"];
        //支付结果
        $Result = $_POST["Result"];
        //取得的MD5校验信息
        $SignInfo = $_POST["SignInfo"];
        //备注
        $Remark = $_POST["Remark"];

        //校验源字符串
        $md5src = "MerNo=" . $MerNo . "&BillNo=" . $BillNo . "&OrderNo=" . $OrderNo . "&Amount=" . $Amount . "&Succeed=" . $Succeed . "&" . $MD5key;
        //MD5检验结果
        $md5sign = strtoupper(md5($md5src));

        if ($SignInfo == $md5sign) {//md5验证成功
            //显示订单号和金额
            if ($Succeed == "88") {
                if ($Succeed == "88") {
                    $BillNo = substr($BillNo, 0, 21);
                    //支付成功
                    $info = $paylist_table->where(array('billno' => $BillNo))->find(); //看状态有没有更新
                    if ($info && $info['status'] == 2) {//未付款成功
                        $rel = $paylist_table->save(array('id' => $info['id'], 'status' => '1')); //付款完成
                        if ($rel) {
                            $userInfo = $member_table->field('id,cash')->find($info['uid']); //会员信息 
                            $allcashmoney = $userInfo['cash'] + $Amount;
                            $rel1 = $member_table->save(array('id' => $userInfo['id'], 'cash' => $allcashmoney));
                            $rel2 = $bonus_table->add(array('uid' => $userInfo['id'], 'type' => 22, 'income' => $Amount, 'status' => 1, 'balance' => $allcashmoney, 'message' => '在线充值', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '6'));
                            if ($rel && $rel1 && $rel2) {
                                $member_table->commit(); //成功充值 

                                $this->assign('info', $info);
                                $this->display('Common:payinfo');
                                return 'ok';
                            }
                            //发放现金积分
                        } else {
                            $member_table->rollback(); //回滚 
                            $this->display('Common:payerrorinfo');
                            exit;
                        }
                    }
                } else {
                    //支付失败
                    $this->display('Common:payerrorinfo');
                    exit;
                }
                //支付成功
            } else {

                $this->display('Common:payerrorinfo');
                exit;
                //支付失败
            }
        } else {
            $this->display('Common:payerrorinfo');
            exit;
            //md5验证失败
        }


        // $this->display();
    }

    //服务器处理是否成功 异步：更新状态，完成业务逻辑完成现金积分的发放
    public function Paylogic() {
        $todayTime = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
        $beginThismonth = mktime(0, 0, 0, date('m'), 1, date('Y'));  //当月第一天时间戳
        $week = mktime(0, 0, 0, date('m'), date('d') - date('w'), date('Y')); //当周第一天
        $paylist_table = M('paylist');
        $bonus_table = M('bonus');
        $member_table = M('member');
        $member_table->startTrans();
        $datainfo = getpayparam();
        //商户号
        $MerNo = $datainfo['username'];
        //MD5私钥
        $MD5key = $datainfo['md5pwd'];
        //订单号
        $BillNo = $_POST["BillNo"];

        //一麻袋支付订单号
        $OrderNo = $_POST["OrderNo"];
        //金额
        $Amount = $_POST["Amount"];
        //支付状态
        $Succeed = $_POST["Succeed"];
        //支付结果
        $Result = $_POST["Result"];
        //取得的MD5校验信息
        $SignInfo = $_POST["SignInfo"];
        //备注
        $Remark = $_POST["Remark"];

        //校验源字符串
        $md5src = "MerNo=" . $MerNo . "&BillNo=" . $BillNo . "&OrderNo=" . $OrderNo . "&Amount=" . $Amount . "&Succeed=" . $Succeed . "&" . $MD5key;
        //MD5检验结果
        $md5sign = strtoupper(md5($md5src));

        if ($SignInfo == $md5sign) {//md5验证成功
            //显示订单号和金额
            if ($Succeed == "88") {
                $BillNo = substr($BillNo, 0, 21);
                //支付成功
                $info = $paylist_table->where(array('billno' => $BillNo))->find(); //看状态有没有更新
                if ($info && $info['status'] == 2) {//未付款成功
                    $rel = $paylist_table->save(array('id' => $info['id'], 'status' => '1')); //付款完成
                    if ($rel) {
                        $userInfo = $member_table->field('id,cash')->find($info['uid']); //会员信息 
                        $allcashmoney = $userInfo['cash'] + $Amount;
                        $rel1 = $member_table->save(array('id' => $userInfo['id'], 'cash' => $allcashmoney));
                        $rel2 = $bonus_table->add(array('uid' => $userInfo['id'], 'type' => 22, 'income' => $Amount, 'status' => 1, 'balance' => $allcashmoney, 'message' => '在线充值', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '6'));
                        if ($rel && $rel1 && $rel2) {
                            $member_table->commit(); //成功充值
                            return 'ok';
                        }
                        //发放现金积分
                    } else {
                        $member_table->rollback(); //回滚
                    }
                }
            } else {
                //支付失败
                $this->error('支付失败 ');
                exit;
            }
        } else {
            //md5验证失败
            $this->error('校验失败');
            exit;
        }
    }

}
