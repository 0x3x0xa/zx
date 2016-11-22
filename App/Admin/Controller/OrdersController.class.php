<?php

namespace Admin\Controller;

use Admin\Controller\CommonController;

class OrdersController extends CommonController {
    /*     * *
     *
     * 订单
     */

    //已审核订单
    public function listaudited() {
        $order_table = M('order');
        $member_table = M('member');
        $admin_table = M('admin');
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
        if (!empty($_REQUEST['search_value'])) {
            if ($_REQUEST['searchCondition'] == 'receiver') {
                $map['receiver'] = $_REQUEST['search_value'];
            } else if ($_REQUEST['searchCondition'] == 'order_eg') {
                $map['order_eg'] = $_REQUEST['search_value'];
            } else {
                $checkUserInfo = $member_table->field('id')->where(array('username' => $_REQUEST['search_value']))->find();
                $map['uid'] = $checkUserInfo['id'];
            }

            $search['search_value'] = $_REQUEST['search_value'];
        }
        $search['searchCondition'] = $_REQUEST['searchCondition'];
        if (!empty($_REQUEST['orderType'])) {
            $map['order_type'] = $_REQUEST['orderType'];
            $search['orderType'] = $_REQUEST['orderType'];
        }
        if (!empty($_REQUEST['delivery_mode'])) {
            $map['delivery_mode'] = $_REQUEST['delivery_mode'];
            $search['delivery_mode'] = $_REQUEST['delivery_mode'];
        }
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
            $adminInfo = $admin_table->field('username')->find($list[$i]['hid']);
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

    //发货列表
    public function showFahuo() {
        $id = I('get.id', '', 'trim');
        $id = rtrim($id, ',');
        $order_table = M('order');
        $member_table = M('member');
        if (IS_POST) {
            $orders = I('post.orders', '', 'trim');
            $count = count($orders);
            for ($i = 0; $i < $count; $i++) {

                $order_table->save(array(
                    'id' => $orders[$i]['id'],
                    'message' => $orders[$i]['message'],
                    'status' => '2',
                    'fahuotime' => time(),
                ));
            }
            $json['status'] = 1;
            $json['msg'] = '发货成功';
            echo json_encode($json);
            exit;
        }
        $list = $order_table->where(array('id' => array('in', $id)))->select();
        $listcount = count($list);
        for ($i = 0; $i < $listcount; $i++) {
           
            $userInfo = $member_table->field('username,name')->find($list[$i]['uid']);
            $list[$i]['username'] = $userInfo['username'];
            $list[$i]['name'] = $userInfo['name'];
        }

        $this->assign('list', $list);
        $this->display();
    }

    //查看订单
    public function detailOrder($id) {
        $order_table = M('order');
        $productorderlist_table = M('productorderlist');
        $list = $productorderlist_table->field('productTitle,productNum,productTotalMoney,productMoney')->where(array('orderId' => $id))->select();
        $row = $order_table->field('order_eg,status,message,total_sum,total_num')->find($id);

        $this->assign('row', $row);
        $this->assign('list', $list);
        $this->display();
    }

    //修改订单资料
    public function editOrderRemak($id) {
        $order_table = M('order');
        $admin_table = M('admin');
        $member_talbe = M('member');
        if (IS_POST) {
            $relust = $order_table->save($_POST);
            if ($relust) {
                $json['status'] = 1;
                $json['msg'] = '修改成功';
                echo json_encode($json);
                exit;
            } else {
                $upgradelog_table->rollback();
                $json['status'] = 2;
                $json['msg'] = '修改失败';
                echo json_encode($json);
                exit;
            }
        }

        $express_table = M('express');
        $express_list = $express_table->select();
        $row = $order_table->find($id);
        $userInfo = $member_talbe->field('username')->find($row['uid']);
        $adminInfo = $admin_table->field('username')->find($row['hid']);
        $this->assign('exp_list', $express_list);
        $this->assign('row', $row);
        $this->assign('admin', $adminInfo);
        $this->assign('user', $userInfo);
        $this->display();
    }

    //已发货订单
    public function listsended() {
        $order_table = M('order');
        $member_table = M('member');
        $admin_table = M('admin');
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
        if (!empty($_REQUEST['search_value'])) {
            if ($_REQUEST['searchCondition'] == 'receiver') {
                $map['receiver'] = $_REQUEST['search_value'];
            } else if ($_REQUEST['searchCondition'] == 'order_eg') {
                $map['order_eg'] = $_REQUEST['search_value'];
            } else {
                $checkUserInfo = $member_table->field('id')->where(array('username' => $_REQUEST['search_value']))->find();
                $map['uid'] = $checkUserInfo['id'];
            }

            $search['search_value'] = $_REQUEST['search_value'];
        }
        $search['searchCondition'] = $_REQUEST['searchCondition'];
        if (!empty($_REQUEST['orderType'])) {
            $map['ordertype'] = $_REQUEST['orderType'];
        }
        $search['orderType'] = $_REQUEST['orderType'];
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
            $adminInfo = $admin_table->field('username')->find($list[$i]['hid']);
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

    public function shouhuo($id) {
        $order_table = M('order');
        $relust = $order_table->save(array('id' => $id, 'status' => '3', 'shouhutime' => time()));
        if ($relust) {
            $json['status'] = 1;
            $json['msg'] = '完成收货';
            echo json_encode($json);
            exit;
        } else {
            $json['status'] = 0;
            $json['msg'] = '操作失败';
            echo json_encode($json);
            exit;
        }
    }

    //已收货订单
    public function listfinish() {

        $order_table = M('order');
        $member_table = M('member');
        $admin_table = M('admin');
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
        if (!empty($_REQUEST['search_value'])) {
            if ($_REQUEST['searchCondition'] == 'receiver') {
                $map['receiver'] = $_REQUEST['search_value'];
            } else if ($_REQUEST['searchCondition'] == 'order_eg') {
                $map['order_eg'] = $_REQUEST['search_value'];
            } else {
                $checkUserInfo = $member_table->field('id')->where(array('username' => $_REQUEST['search_value']))->find();
                $map['uid'] = $checkUserInfo['id'];
            }

            $search['search_value'] = $_REQUEST['search_value'];
        }
        $search['searchCondition'] = $_REQUEST['searchCondition'];
        if (!empty($_REQUEST['orderType'])) {
            $map['ordertype'] = $_REQUEST['orderType'];
        }
        $search['orderType'] = $_REQUEST['orderType'];
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
            $adminInfo = $admin_table->field('username')->find($list[$i]['hid']);
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

    public function wuliu($id) {
        $order_table = M('order');
        $express_table = M('express');
        $row = $order_table->field('express_no,express')->find($id);
        if ($row) {
            $expressInfo = $express_table->field('expressNum')->find($row['express']);
            $data = file_get_contents('http://www.kuaidi100.com/query?type=' . $expressInfo['expressnum'] . '&postid=' . $row['express_no'] . '&id=1&valicode=&temp=0.16163090997514507'); //快递100接口
        }
        $magInfo = json_decode($data, true);
        if ($magInfo['status'] == '403') {
            $this->assign('status', $magInfo['status']);
        } else {
            $this->assign('list', $magInfo['data']);
        }
        $this->display();
    }

    //购买商品
    public function purchase() {

        $orderDate = $this->orderDade($array, $delivery_mode, $receiver, $mobile, $address, $post_code);
        $relust = $this->ProductOrderListAdd($array, $orderDate);
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

    /* array 商品id 和数量
     * uid 用户id 
     * delivery_mode 提货方式
     * receiver 收货人姓名
     */

    protected function orderDade($array, $delivery_mode, $receiver, $mobile, $address, $post_code) {
        $todayTime = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
        $product_table = M('product');
        $totalProductAmount = 0;
        $totalNum = 0;
        $count = count($array);
        for ($i = 0; $i < $count; $i++) {
            $info = $product_table->field('id')->where(array('status' => 1))->find($array[$i]['id']);
            if (!$info) {
                $json['status'] = 2;
                $json['msg'] = '商品不存在或下架了！';
                echo json_encode($json);
                exit;
            }
            $totalNum+= $array[$i]['num']; //产品数量
            $totalProductAmount+=$info['member_price'] * $array[$i]['num']; //产品总金额
        }
        $order = array(
            'uid' => session('user_id'),
            'delivery_mode' => $delivery_mode,
            'receiver' => $receiver,
            'mobile' => $mobile,
            'address' => $address,
            'create_date' => time(),
            'order_eg' => build_order_no(),
            'order_type' => 1,
            'total_num' => $totalNum,
            'total_sum' => $totalProductAmount,
            'hid' => 0,
            'delivery_mode' => $delivery_mode,
            'post_code' => $post_code,
            'addtime' => $todayTime,
        );
        return $order;
    }
    
    

    //生成订单商品记录
    protected function ProductOrderListAdd($array, $orderDate) {
        $productorderlist_table = M('productorderlist');
        $product_table = M('product');
        $order_table = M('order');
        $order_table->startTrans();


        $uid = session('user_id');
        $userInfo = getuserInfo($uid);
        //判断订单金额是否超过积分
        if ($userInfo['integral'] < $orderDate['total_sum']) {
            $json['status'] = 2;
            $json['msg'] = '积分不足！';
            echo json_encode($json);
            exit;
        };


        $oid = $order_table->add($orderDate); //生成订单记录
        $count = count($array);
        $flag = true;
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
                $flag = FALSE;
            }
        }
        if ($flag) {
            $order_table->commit();
            return TRUE;
        } else {
            $order_table->rollback();
            return FALSE;
        }
    }

}
