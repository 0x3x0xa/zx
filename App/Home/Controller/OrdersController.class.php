<?php

namespace Home\Controller;

use Home\Controller\CommonController;

class OrdersController extends CommonController {

    //产品列表
    public function index() {

        $uid = session('uid');
        $member_table = M('member');
        $order_table=M('order');
        $orderInfo=$order_table->where(array('uid'=>$uid))->find();
        $flag=($orderInfo)?1:0;
      
        $userInfo = $member_table->field('name,mobile,post_code,province,city,area,detailed_address,gouwujifen,gouwujuan,dianzimoney,cash')->find($uid);
        $productlist = findproductlist(); //获取商品
        $this->assign('userInfo', $userInfo);
        $this->assign('flag',$flag);
        $this->assign('pro_list', $productlist);
        $this->display();
    }

    //产品详情
    public function productInfo($id) {

        $productimg_table = M('productimg');
        $product_table = M('product');
        $productInfo = $product_table->find($id);
        $list = $productimg_table->field('image_path,image_path_middle')->where(array('product_id' => $id))->select();
        $row = $productimg_table->field('image_path,image_path_middle')->where(array('product_id' => $id, 'master_map' => 1))->find();
        $this->assign('row', $row);
        $this->assign('list', $list);
        $this->assign('productInfo', $productInfo);
        $this->display();
    }

    //检查金额
    public function checkMoney($totalProductAmount) {

        $uid = session('uid');
        $member_table = M('member');
        $order_table = M('order');
        $rel = $order_table->where(array('uid' => $uid))->find();
        if ($rel) {
            $json['status'] = 1;
            $json['msg'] = '';
            echo json_encode($json);
            exit;
        } else {
            //首次只能使用购物积分购买
            $relust = $member_table->field('gouwujifen')->find($uid); //购物积分
            if ($relust['gouwujifen'] < $totalProductAmount) {
                $json['status'] = 0;
                $json['msg'] = '你没有足够的购物积分！' . $relust['gouwujifen'];
                echo json_encode($json);
                exit;
            }
        }
    }

    //购买商品
    public function productadd() {

        if (IS_POST) {
            $uid = session('uid');
            $member_table = M('member');
            $order_table = M('order');
            if (!$member_table->autoCheckToken($_POST)) {
                $json['status'] = 0;
                $json['msg'] = '操作失败！';
                echo json_encode($json);
                exit;
            }
            $product = I('post.product', '', 'trim');
            if (empty($product)) {
                $json['status'] = 0;
                $json['msg'] = '请选择商品！';
                echo json_encode($json);
                exit;
            }





            $receiver = I('post.receiver', '', 'trim');
            $mobile = I('post.mobile', '', 'trim');
            $address = I('post.province', '', 'trim') . I('post.city', '', 'trim') . I('post.area', '', 'trim') . I('post.detailed_address', '', 'trim');
            $post_code = I('post.post_code', '', 'trim');
            $message = I('post.message', '', 'trim');
            $orderDate = $this->orderDade($product, $receiver, $mobile, $address, $post_code, $message);
             
            $rel = $order_table->where(array('uid' => $uid))->find();
            //首次购买(只扣购物积分)
            if (!$rel) {
                $relust = $this->ProductOrderListAdd($product, $orderDate);
            } else {
                $gouwujuan = I('post.gwj', '', 'trim');
                $gouwujifen = I('post.gwjf', '', 'trim');
                $dianzhijifen = I('post.dzjf', '', 'trim');
                $cash = I('post.xjjf', '', 'trim');
                $userInfo=$member_table->find($uid);
                if($gouwujifen>$userInfo['gouwujifen'])
                {
                    $json['status'] = 2;
                    $json['msg'] = '购物积分不足';
                    echo json_encode($json);
                    exit;
                }
                if($gouwujuan>$userInfo['gouwujuan'])
                {
                    $json['status'] = 2;
                    $json['msg'] = '购物卷不足';
                    echo json_encode($json);
                    exit;
                }
                $allowsum=$orderDate['total_sum']*0.5;
                if($gouwujuan>$allowsum){
                    $json['status'] = 2;
                    $json['msg'] = '购物卷超过最大使用，操作失败';
                    echo json_encode($json);
                    exit;
                }
                if($cash>$userInfo['cash'])
                {
                    $json['status'] = 2;
                    $json['msg'] = '现金积分不足';
                    echo json_encode($json);
                    exit;
                }
                if($dianzhijifen>$userInfo['dianzimoney'])
                {
                    $json['status'] = 2;
                    $json['msg'] = '电子积分不足';
                    echo json_encode($json);
                    exit;
                }
                
                //首次购买之后
                $relust=$this->selllast($gouwujifen, $cash, $dianzhijifen, $gouwujuan, $orderDate,$product);
            }

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

    //第二次之后购买产品
    protected function selllast($gouwujifen, $cash, $dianzhijifen, $gouwujuan, $orderDate,$array) {
        $todayTime = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
        $uid = session('uid');
        $member_table = M('member');
        $bonus_table = M('bonus');
        $order_table = M('order');
        $product_table=M('product');
        $productorderlist_table=M('productorderlist');
        $userInfo = $member_table->find($uid);
        $xiaofei=$gouwujifen+$cash+$dianzhijifen+ $gouwujuan;
        if ($xiaofei!= $orderDate['total_sum']) {
            $json['status'] = 2;
            $json['msg'] = '积分和价格不相等,操作失败！';
            echo json_encode($json);
            exit;
        }
        $allgouwujifen=$userInfo['gouwujifen'];
        $allcash=$userInfo['cash'];
        $alldianzhijifen=$userInfo['dianzimoney'];
        $allgouwujuan=$userInfo['gouwujuan'];
        $oid = $order_table->add($orderDate); //生成订单记录
        if ($gouwujifen != 0 && !empty($gouwujifen)) {
            //扣购物积分
            $allgouwujifen = $userInfo['gouwujifen'] - $gouwujifen;
            $bonus_table->add(array('uid' => $uid, 'type' => '24', 'expend' => $gouwujifen, 'status' => '2', 'balance' => $allgouwujifen, 'message' => '购买商品（订单号：' . $orderDate['order_eg'] . '）', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime));
        }
        if ($cash != 0 && !empty($cash)) {
            //扣现金积分
            $allcash = $userInfo['cash'] - $cash;
            $bonus_table->add(array('uid' => $uid, 'type' => '22', 'expend' => $cash, 'status' => '2', 'balance' => $allcash, 'message' => '购买商品（订单号：' . $orderDate['order_eg'] . '）', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime));
        }
        if ($dianzhijifen != 0 && !empty($dianzhijifen)) {
            //扣电子积分
            $alldianzhijifen = $userInfo['dianzimoney'] - $dianzhijifen;
            $bonus_table->add(array('uid' => $uid, 'type' => '6', 'expend' => $dianzhijifen, 'status' => '2', 'balance' => $alldianzhijifen, 'message' => '购买商品（订单号：' . $orderDate['order_eg'] . '）', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime));
        }
        if ($gouwujuan != 0 && !empty($gouwujuan)) {
            //扣购物卷
            $allgouwujuan = $userInfo['gouwujuan'] - $gouwujuan;
            $bonus_table->add(array('uid' => $uid, 'type' => '25', 'expend' => $gouwujuan, 'status' => '2', 'balance' => $allgouwujuan, 'message' => '购买商品（订单号：' . $orderDate['order_eg'] . '）', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime));
        }
        $member_table->save(array('id'=>$uid,'gouwujifen'=>$allgouwujifen,'cash'=>$allcash,'dianzimoney'=>$alldianzhijifen,'gouwujuan'=>$allgouwujuan));
          $count = count($array);
        for ($i = 0; $i < $count; $i++) {
            $productinfo = $product_table->find($array[$i]['id']);
            $relust = $productorderlist_table->add(
                    array(
                        'orderId' => $oid,
                        'productId' => $array[$i]['id'],
                        'productNum' => $array[$i]['num'],
                        'uid' => $uid,
                        'productTitle' => $productinfo['product_title'],
                        'productMoney' => $productinfo['member_price'],
                        'productTotalMoney' => $productinfo['member_price'] * $array[$i]['num'],
                        'createDate' => time(),
                    )
            );
            if (!$relust) {
                $order_table->rollback();
                return FALSE;
            }
        }
        if ($relust) {
            $order_table->commit();
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /* array 商品id 和数量
     * uid 用户id 
     * delivery_mode 提货方式
     * receiver 收货人姓名
     */

    protected function orderDade($array, $receiver, $mobile, $address, $post_code, $message) {
        $todayTime = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
        $product_table = M('product');
        $totalProductAmount = 0;
        $totalNum = 0;
        $count = count($array);
        for ($i = 0; $i < $count; $i++) {
            $info = $product_table->field('id,member_price')->where(array('status' => 1))->find($array[$i]['id']);
            if (!$info) {
                $json['status'] = 2;
                $json['msg'] = '商品不存在或下架了！';
                echo json_encode($json);
                exit;
            }
            $totalNum+= $array[$i]['num']; //产品数量
            $totalProductAmount+=$info['member_price'] * $array[$i]['num']; //产品总金额
        }
        //$this->checkMoney($totalProductAmount);
        $order = array(
            'uid' => session('uid'),
            'receiver' => $receiver,
            'mobile' => $mobile,
            'address' => $address,
            'create_date' => time(),
            'order_eg' => $this->build_order_no(),
            'order_type' => 1,
            'total_num' => $totalNum,
            'total_sum' => $totalProductAmount,
            'hid' => 0,
            'post_code' => $post_code,
            'addtime' => $todayTime,
            'message' => $message,
        );
        return $order;
    }

    protected function build_order_no() {
        $order_table = M('order');
        $orderno = date('Ymd') . substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
        $orderInfo = $order_table->field('order_eg')->where(array('order_eg' => $orderno))->find();
        if ($orderInfo) {
            $this->build_order_no();
        } else {
            return $orderno;
            ;
        }
    }

    //生成订单商品记录
    protected function ProductOrderListAdd($array, $orderDate) {
        $todayTime = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
        $member_table = M('member');
        $bonus_table = M('bonus');
        $productorderlist_table = M('productorderlist');
        $product_table = M('product');
        $order_table = M('order');
        $order_table->startTrans();
        $uid = session('uid');
        $userInfo = $member_table->field('gouwujifen')->find($uid);
        if ($userInfo['gouwujifen'] < $orderDate['total_sum']) {
            $json['status'] = 2;
            $json['msg'] = '购物积分不足！';
            echo json_encode($json);
            exit;
        }
        $oid = $order_table->add($orderDate); //生成订单记录
        $userallmoney = $userInfo['gouwujifen'] - $orderDate['total_sum'];
        $relust1 = $member_table->save(array('id' => $uid, 'gouwujifen' => $userallmoney));
        $relust2 = $bonus_table->add(array('uid' => $uid, 'type' => '24', 'expend' => $orderDate['total_sum'], 'status' => '2', 'balance' => $userallmoney, 'message' => '购买商品（订单号：' . $orderDate['order_eg'] . '）', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime));

        $count = count($array);
        for ($i = 0; $i < $count; $i++) {
            $productinfo = $product_table->find($array[$i]['id']);
            $relust = $productorderlist_table->add(
                    array(
                        'orderId' => $oid,
                        'productId' => $array[$i]['id'],
                        'productNum' => $array[$i]['num'],
                        'uid' => $uid,
                        'productTitle' => $productinfo['product_title'],
                        'productMoney' => $productinfo['member_price'],
                        'productTotalMoney' => $productinfo['member_price'] * $array[$i]['num'],
                        'createDate' => time(),
                    )
            );
            if (!$relust) {
                $order_table->rollback();
                return FALSE;
            }
        }
        if ($relust && $relust1 && $relust2) {
            $order_table->commit();
            return TRUE;
        } else {
            return FALSE;
        }
    }

    //查看订单
    public function detailOrder($id) {
        $order_table = M('order');
        $productorderlist_table = M('productorderlist');
        $express_table = M('express');
        $list = $productorderlist_table->field('productTitle,productNum,productTotalMoney,productMoney')->where(array('orderId' => $id))->select();
        $row = $order_table->field('order_eg,status,message,total_sum,total_num,express_no,express')->find($id);
        $expressInfo = $express_table->field('title')->find($row['express']);
        $this->assign('expressInfo', $expressInfo);
        $this->assign('row', $row);
        $this->assign('list', $list);
        $this->display();
    }

    //已审核订单
    public function listaudited() {
        $uid = session('uid');
        $order_table = M('order');
        $member_table = M('member');

        $map['uid'] = $uid;
        $map['status'] = array('eq', '1');
        $delivery_mode = array('1' => '自提', '发货');
        $status = array('1' => '已经付款', '已经发货', '已经收货');
        $orderType = array('1' => '注册报单', '重复购买');
        $count = $order_table->where($map)->count(); // 查询满足要求的总记录数
        $Page = new \Think\Page($count, 30); // 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show(); //
        $list = $order_table->order('id desc')->where($map)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $listCount = count($list);
        for ($i = 0; $i < $listCount; $i++) {
            $userInfo = $member_table->field('username,name')->find($list[$i]['uid']);
            $list[$i]['username'] = $userInfo['username'];
            $list[$i]['name'] = $userInfo['name'];
            $list[$i]['adminname'] = $adminInfo['username'];
            $list[$i]['delivery_mode'] = $delivery_mode[$list[$i]['delivery_mode']];
            $list[$i]['status'] = $status[$list[$i]['status']];
            $list[$i]['order_type'] = $orderType[$list[$i]['order_type']];
        }
        $this->assign('page', $show);
        $this->assign('list', $list);
        $this->assign('count', $count);
        $this->assign('arr', $search);
        $this->display();
    }

    //已发货订单
    public function listsended() {
        $uid = session('uid');
        $order_table = M('order');
        $member_table = M('member');
        $map['uid'] = $uid;
        $map['status'] = array('eq', '2');
        $delivery_mode = array('1' => '自提', '发货');
        $status = array('1' => '已经付款', '已经发货', '已经收货');
        $orderType = array('1' => '注册报单', '重复购买');
        $count = $order_table->where($map)->count(); // 查询满足要求的总记录数
        $Page = new \Think\Page($count, 30); // 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show(); //
        $list = $order_table->order('id desc')->where($map)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $listCount = count($list);
        for ($i = 0; $i < $listCount; $i++) {
            $userInfo = $member_table->field('username,name')->find($list[$i]['uid']);
            $list[$i]['username'] = $userInfo['username'];
            $list[$i]['name'] = $userInfo['name'];
            $list[$i]['adminname'] = $adminInfo['username'];
            $list[$i]['delivery_mode'] = $delivery_mode[$list[$i]['delivery_mode']];
            $list[$i]['status'] = $status[$list[$i]['status']];
            $list[$i]['order_type'] = $orderType[$list[$i]['order_type']];
        }
        $this->assign('page', $show);
        $this->assign('list', $list);
        $this->assign('count', $count);
        $this->assign('arr', $search);
        $this->display();
    }

    //物流信息
    public function wuliu($id) {
        $order_table = M('order');
        $express_table = M('express');
        $row = $order_table->field('express_no,express')->find($id);
        if (!empty($row['express_no'])) {
            $expressInfo = $express_table->field('expressNum')->find($row['express']);

            $data = file_get_contents('http://www.kuaidi100.com/query?type=' . $expressInfo['expressnum'] . '&postid=' . $row['express_no'] . '&id=1&valicode=&temp=0.16163090997514507'); //快递100接口
        } else {
            echo '没有快递单号';
        }
        $magInfo = json_decode($data, true);
        if ($magInfo['status'] == '403') {
            $this->assign('status', $magInfo['status']);
        } else {
            $this->assign('list', $magInfo['data']);
        }
        $this->display();
    }

    //已收货订单
    public function listfinish() {

        $order_table = M('order');
        $member_table = M('member');
        $map['uid'] = session('uid');
        $map['status'] = array('eq', '3');
        $delivery_mode = array('1' => '自提', '发货');
        $status = array('1' => '已经付款', '已经发货', '已经收货');
        $orderType = array('1' => '注册报单', '重复购买');
        $count = $order_table->where($map)->count(); // 查询满足要求的总记录数
        $Page = new \Think\Page($count, 30); // 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show(); //
        $list = $order_table->order('id desc')->where($map)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $listCount = count($list);
        for ($i = 0; $i < $listCount; $i++) {
            $userInfo = $member_table->field('username,name')->find($list[$i]['uid']);
            $list[$i]['username'] = $userInfo['username'];
            $list[$i]['name'] = $userInfo['name'];
            $list[$i]['adminname'] = $adminInfo['username'];
            $list[$i]['delivery_mode'] = $delivery_mode[$list[$i]['delivery_mode']];
            $list[$i]['status'] = $status[$list[$i]['status']];
            $list[$i]['order_type'] = $orderType[$list[$i]['order_type']];
        }
        $this->assign('page', $show);
        $this->assign('list', $list);
        $this->assign('count', $count);
        $this->assign('arr', $search);
        $this->display();
    }

}
