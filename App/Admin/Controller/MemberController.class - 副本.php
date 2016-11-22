<?php

namespace Admin\Controller;

use Admin\Controller\CommonController;

class MemberController extends CommonController {
    /*     * *
     *
     * 会员中心
     */

    public function index() {

        $member_table = M('member');
        $memberlevel_table = M('memberlevel');
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
        if (!empty($_REQUEST['search_username'])) {
            $map['username'] = $_REQUEST['search_username'];
            $search['search_username'] = $_REQUEST['search_username'];
        }
        if (!empty($_REQUEST['search_status'])) {
            $map['status'] = $_REQUEST['search_status'];
            $search['search_status'] = $_REQUEST['search_status'];
        }
        $map['recommend'] = array('neq', '-1');
        $region = array('左', '中', '右');
        $is_accounts = array('是', '否');
        $count = $member_table->where($map)->count(); // 查询满足要求的总记录数
        $Page = new \Think\Page($count, 30); // 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show(); //
        $list = $member_table->order('id desc')->where($map)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $lcount = count($list);
        for ($i = 0; $i < $lcount; $i++) {

            $recommend = $member_table->field('username')->find($list[$i]['recommend']);
            $junction = $member_table->field('username')->find($list[$i]['junction']);
            $admin = $admin_table->field('username')->find($list[$i]['hid']);
            $list[$i]['level'] = findlevel($list[$i]['level']);
            $list[$i]['recommend'] = $recommend['username'];
            $list[$i]['junction'] = $junction['username'];
            $list[$i]['hid'] = $admin['username'];
            $list[$i]['region'] = $region[$list[$i]['region']];
            $list[$i]['is_accounts'] = $is_accounts[$list[$i]['is_accounts']];
        }
        $memberlevel = $memberlevel_table->select();
        $this->assign('memberlevel', $memberlevel);
        $this->assign('page', $show);
        $this->assign('list', $list);
        $this->assign('count', $count);
        $this->assign('arr', $search);
        $this->display();
    }

    //下载Excel
    public function downloadexcel() {
        $member_table = M('member');
        $xlsName = "会员表"; //设置要导出excel的表头 
        $xlsCell = array(
            array('id', 'ID'),
            array('username', '会员账号'),
            array('name', '会员昵称'),
            array('account_name', '开户姓名'),
            array('bank', '银行名称'),
            array('bankno', '银行号码'),
            array('bank_outlets', '银行网点'),
            array('level', '会员等级'),
            array('mobile', '手机号码'),
            array('recommend', '推荐人'),
            array('junction', '接点人'),
            array('region', '区域'),
            array('regtime', '注册时间'),
            array('kuaishubonus', '快速奖'),
            array('lingdaobonus', '领导奖'),
            array('duidengbonus', '对等奖'),
            array('jinjibonus', '晋级奖'),
            array('fenhongbonus', '全球分红'),
        );

        $xlsModel = M('member');
        $xlsData = $xlsModel->Field('id,username,name,account_name,bankno,bank,bank_outlets,mobile,level,recommend,junction,region,regtime,status,kuaishubonus,lingdaobonus,duidengbonus,jinjibonus,fenhongbonus')->where(array('recommend'=>array('neq','-1')))->select();
        $xlscount = count($xlsData);
        for ($i = 0; $i < $xlscount; $i++) {
            $recommendInfo = $member_table->field('username')->find($xlsData[$i]['recommend']);
            $junctionInfo = $member_table->field('username')->find($xlsData[$i]['junction']);
            $xlsData[$i]['recommend'] = $recommendInfo['username'];
            $xlsData[$i]['junction'] = $junctionInfo['username'];
            $xlsData[$i]['bankno'] = ' ' . $xlsData[$i]['bankno'];
            $xlsData[$i]['regtime'] = date('Y-m-d H:i:s', $xlsData[$i]['regtime']);
        }
        exportExcel($xlsName, $xlsCell, $xlsData);
    }

    //修改用户密码
    public function userpasswordedit() {
        if (IS_POST) {
            $type = I('post.type', '', 'htmlspecialchars');
            $username = I('post.username', '', 'htmlspecialchars');
            $password = I('post.newpassword', '', 'htmlspecialchars');
            $return = checkPwd($password);
            if (!$return) {
                $json['status'] = 2;
                $json['msg'] = '密码格式不正确';
                echo json_encode($json);
                exit;
            }
            $newpwd = fun_md5($password);
            if (!empty($newpwd)) {
                $member_table =M('member');
                $userinfo = $member_table->field('id')->where(array('username' => $username))->find();
                if (!$userinfo) {
                    $json['status'] = 2;
                    $json['msg'] = '用户不存在！';
                    echo json_encode($json);
                    exit;
                }

                switch ($type) {
                    case 1:$relust = $member_table->save(array('id' => $userinfo['id'], 'password' => $newpwd));
                        break;
                    case 2:$relust = $member_table->save(array('id' => $userinfo['id'], 'towpassword' => $newpwd));
                        break;
                    case 3:$relust = $member_table->save(array('id' => $userinfo['id'], 'threepassword' => $newpwd));
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

    //修改推荐人
    public function openEditRecommend() {
        if (IS_POST) {
            $member_table = M('member');
            $username = I('post.username', '', 'htmlspecialchars');
            $recommend = I('post.recommend', '', 'htmlspecialchars');
            $userInfo = $member_table->field('id')->where(array('username' => $username))->find();
            $reconnendInfo = $member_table->field('id')->where(array('username' => $recommend))->find();
            if ($userInfo['id'] == $reconnendInfo['id']) {
                $json['status'] = 2;
                $json['msg'] = '推荐人不能为本人!';
                echo json_encode($json);
                exit;
            }

            $relust = $member_table->save(array('id' => $userInfo['id'], 'recommend' => $reconnendInfo['id']));
            if ($relust) {
                $json['status'] = 1;
                $json['msg'] = '修改成功!';
                echo json_encode($json);
                exit;
            } else {
                $json['status'] = 2;
                $json['msg'] = '修改失败!';
                echo json_encode($json);
                exit;
            }
        }
        $this->display();
    }

    //修改用户信息
    public function useredit() {
        $member = M('member');

        if (IS_AJAX) {
            $username_info = $member->field('id,username')->where(array('username' => $_POST['username']))->find();
            $mobile_info = $member->field('id,mobile')->where(array('mobile' => $_POST['mobile']))->find();
            if ($username_info) {
                if ($username_info['id'] != $_POST['id']) {
                    $json['status'] = 0;
                    $json['msg'] = '账号已经存在，请换一个';
                    echo json_encode($json);
                    exit;
                }
            }
            if ($mobile_info) {
                if ($mobile_info['id'] != $_POST['id']) {
                    $json['status'] = 0;
                    $json['msg'] = '手机号已经存在，请换一个';
                    echo json_encode($json);
                    exit;
                }
            }
            $bankno_info = $member->field('id,bankno')->where(array('bankno' => $_POST['bankno']))->find();
            if ($bankno_info) {
                if ($bankno_info['id'] != $_POST['id']) {
                    $json['status'] = 0;
                    $json['msg'] = '银行号已经存在，请换一个';
                    echo json_encode($json);
                    exit;
                }
            }
            $rel = $member->save($_POST);
            if ($rel) {
                $json['status'] = 1;
                $json['msg'] = '修改成功';
                echo json_encode($json);
                exit;
            } else {
                $json['status'] = 2;
                $json['msg'] = '修改失败';
                echo json_encode($json);
                exit;
            }
        } else {


            $id = I('get.id', '', 'htmlspecialchars');
            $userInfo = $member->field('id,username,name,bank,bankno,bank_outlets,mobile,account_name,province,city,area,detailed_address,id_card,post_code')->find($id);
            $banklist = findbank();
            $this->assign('banklist', $banklist);
            $this->assign('userInfo', $userInfo);
            $this->display();
        }
    }

    //用户详情
    public function usershow($id) {
        $member_table = M('member');
        $userInfo = $member_table->field('username,kuaishubonus,lingdaobonus,duidengbonus,jinjibonus,fenhongbonus,lognum,logip')->find($id);
        $this->assign('userInfo', $userInfo);
        $this->display();
    }

    //停用用户
    public function user_stop($id) {

        $member = M('member');
        $relust = $member->field('status')->find($id);
        if ($relust['status'] == 1) {
            $rel = $member->save(array('id' => $id, 'status' => '3'));
            if ($rel) {
                $json['status'] = 1;
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

//启用用户

    public function user_start($id) {
        $member = M('member');
        $relust = $member->field('status')->find($id);
        if ($relust['status'] == 3 or $relust['status'] == 2) {
            $rel = $member->save(array('id' => $id, 'status' => '1'));
            if ($rel) {
                $json['status'] = 1;
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

    /*     * ****************插入判断*************************** */

    public function check_input_unique() {
        if (IS_POST) {
            $member_table = M('member');
            $name = I('post.name', '', 'trim');
            $param = I('post.param', '', 'trim');
            $relust = $member_table->field('id')->where(array($name => $param))->find();
            if ($relust) {
                $json['status'] = 'n';
                $json['info'] = '会员账号已经存在，请换一个！';
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

    public function check_input_recommend_exist() {
        if (IS_POST) {

            $member_table = M('member');
            $name = I('post.name', '', 'trim');
            $param = I('post.param', '', 'trim');
            $haveDate = $member_table->find();
            if (!$haveDate) {
                $json['status'] = 'y';
                $json['info'] = '添加首个会员无需推荐人';
                echo json_encode($json);
                exit;
            }
            $relust = $member_table->field('name')->where(array('username' => $param))->find();
            if ($relust) {
                $json['status'] = 'y';
                $json['info'] = '该推荐人姓名为：' . $relust['name'];
                echo json_encode($json);
                exit;
            } else {
                $json['status'] = 'n';
                $json['info'] = '该推荐人不存在';
                echo json_encode($json);
                exit;
            }
        }
    }

    public function check_input_junction_exist() {
        if (IS_POST) {

            $member_table = M('member');
            $name = I('post.name', '', 'trim');
            $param = I('post.param', '', 'trim');
            $haveDate = $member_table->find();
            if (!$haveDate) {
                $json['status'] = 'y';
                $json['info'] = '添加首个会员无需接点人';
                echo json_encode($json);
                exit;
            }
            $relust = $member_table->field('name,id')->where(array('username' => $param))->find();
            $count = $member_table->field('id')->where(array('junction' => $relust['id'], 'recommend' => array('neq', '-1')))->count();
            if ($relust) {
                if ($count >= 3) {
                    $json['status'] = 'n';
                    $json['info'] = '该接点人姓名为：' . $relust['name'] . '不可接入';
                    echo json_encode($json);
                    exit;
                } else {
                    $json['status'] = 'y';
                    $json['info'] = '该接点人姓名为：' . $relust['name'] . '可接入';
                    echo json_encode($json);
                    exit;
                }
            } else {
                $json['status'] = 'n';
                $json['info'] = '该接点人不存在';
                echo json_encode($json);
                exit;
            }
        }
    }

    //获取注册级别
    public function getregistJibie() {
        if (IS_POST) {

            $totalProductAmount = I('post.totalProductAmount', '', 'trim'); //获取到总金额
            $memberlevel_table = M('memberlevel');
            $relust = $memberlevel_table->order('id desc')->where(array('registermoney' => array('ELT', $totalProductAmount)))->find();
            if (!$relust) {
                $json['status'] = 0;
                $json['msg'] = '没达到最低注册级别';
                echo json_encode($json);
                exit;
            } else {
                $json['status'] = 1;
                $json['msg'] = $relust['title'];
                echo json_encode($json);
                exit;
            }
        }
    }

    //检验订单的有效值
    public function checkorder($array, $uid, $delivery_mode, $receiver, $mobile, $address, $post_code) {
        $member_tablel = M('member');
        $product_table = M('product');
        $order_table = M('order');
        $todayTime = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
        $count = count($array);
        $totalProductAmount = 0;
        $totalNum = 0;
        for ($i = 0; $i < $count; $i++) {
            $info = $product_table->field('member_price,product_pv')->where(array('id' => $array[$i]['id']))->find();
            if (!$info) {
                $json['status'] = 0;
                $json['msg'] = '没找到该商品';
                echo json_encode($json);
                exit;
            }
            $totalNum+= $array[$i]['num'];
            $totalProductAmount+=$info['member_price'] * $array[$i]['num'];
        }
        $memberlevel_table = M('memberlevel');
        $relust = $memberlevel_table->order('id desc')->where(array('registermoney' => array('ELT', $totalProductAmount)))->find();
        if (!$relust) {
            $json['status'] = 0;
            $json['msg'] = '没达到最低注册级别';
            echo json_encode($json);
            exit;
        } else {
            if ($totalProductAmount % $relust['registermoney'] != 0) {
                $json['status'] = 0;
                $json['msg'] = '选择的产品总金额必须要等于' . $relust['registermoney'];
                echo json_encode($json);
                exit;
            } else {

                $rel = $member_tablel->save(array('id' => $uid, 'level' => $relust['id'], 'hid' => session('userid')));
                if ($rel) {
                    $order = array(
                        'uid' => $uid,
                        'delivery_mode' => $delivery_mode,
                        'receiver' => $receiver,
                        'mobile' => $mobile,
                        'address' => $address,
                        'create_date' => time(),
                        'order_eg' => build_order_no(),
                        'order_type' => 1,
                        'total_num' => $totalNum,
                        'total_sum' => $totalProductAmount,
                        'hid' => session('userid'),
                        'delivery_mode' => $delivery_mode,
                        'post_code' => $post_code,
                        'addtime'=>$todayTime,
                    );

                    return $order;
                } else {
                    $json['status'] = 0;
                    $json['msg'] = '用户不存在';
                    echo json_encode($json);
                    exit;
                }
            }
        }
    }

    //生成订单商品记录
    public function productorderlistadd($array, $oid, $uid) {
        $productorderlist_table = M('productorderlist');
        $product_table = M('product');
        $time = time();
        $count = count($array);
        for ($i = 0; $i < $count; $i++) {
            $productinfo = $product_table->find($array[$i]['id']);
            $productorderlist_table->add(
                    array(
                        'orderId' => $oid,
                        'productId' => $array[$i]['id'],
                        'productNum' => $array[$i]['num'],
                        'uid' => $uid,
                        'productTitle' => $productinfo['product_title'],
                        'productMoney' => $productinfo['member_price'],
                        'productTotalMoney' => $productinfo['member_price'] * $array[$i]['num'],
                        'createDate' => $time,
                    )
            );
        }
    }

    //生成三个直推(左，中，右)
    public function direcPush($uid) {
        $member_table = M('member');
        $rel = $member_table->field('junctionLevel')->find($uid);
        for ($i = 0; $i < 3; $i++) {
            $member_table->add(array('junction' => $uid, 'region' => $i, 'recommend' => '-1', 'junctionLevel' => $rel['junctionlevel'] + 1));
        }
    }

    //验证节点人信息
    public function checkjunction($junctionid, $region) {
        $member_table = M('member');
        $row = $member_table->field('id,recommend')->where(array('junction' => $junctionid, 'region' => $region))->find();
        if ($row) {
            if ($row['recommend'] == '-1') {
                return $row['id'];
            } else {
                $json['status'] = 2;
                $json['msg'] = '位置已经被注册，请换一个位置';
                echo json_encode($json);
                exit;
            }
        } else {
            $json['status'] = 2;
            $json['msg'] = '接点人没找到';
            echo json_encode($json);
            exit;
        }
    }

    //查找A区是否已经挂满一个属于自己的推荐人
    public function returnrecommend($junctionid) {
        $member_table = M('member');
        $row = $member_table->field('id,recommend')->where(array('region'=>'0','junction'=>$junctionid))->find();
        if ($row) {
            if ($row['recommend'] == $junctionid) {
                return true;
            } else {
                self::returnrecommend($row['id']);
            }
        } else {
            return FALSE;
        }
    }

    //注册挂区规则验证
    public function regrule($recommend, $junction, $region) {
        if ($recommend == $junction) {
            $rel=$this->returnrecommend($junction);
            if(!$rel)
            {
                $json['status'] = 2; 
                $json['msg'] = '请在左区先发展一个自己玩家';
                echo json_encode($json);
                exit;
            }
        } else {
            $json['status'] = 2; 
            $json['msg'] = '中区和右区只能接点人自己注册';
            echo json_encode($json);
            exit;
        }
    }

    //添加用户
    public function useradd() {
        $member_table = M('member');
        $order_table = M('order');
        $member_table->startTrans();
        if (IS_POST) {
            $product = I('post.product', '', 'trim');
            if (empty($product)) {
                $json['status'] = 0;
                $json['msg'] = '请选择商品！';
                echo json_encode($json);
                exit;
            }
            $region = I('post.region', '', 'trim');
            $delivery_mode = I('post.delivery_mode', '', 'trim');
            $receiver = I('post.name', '', 'trim');
            $mobile = I('post.mobile', '', 'trim');
            $address = I('post.province', '', 'trim') . I('post.city', '', 'trim') . I('post.area', '', 'trim') . I('post.detailed_address', '', 'trim');
            $post_code = I('post.post_code', '', 'trim');
            $_POST['password'] = fun_md5(I('post.password', '', 'trim'));
            $_POST['towpassword'] = fun_md5(I('post.towpassword', '', 'trim'));
            $_POST['threepassword'] = fun_md5(I('post.threepassword', '', 'trim'));
            $_POST['regtime'] = time();
            $_POST['regip'] = get_client_ip();
            $_POST['recommend'] = get_user_id(I('post.recommend', '', 'trim'));
            $_POST['junction'] = get_user_id(I('post.junction', '', 'trim'));

            $relust = $member_table->field('id')->find();
            if ($relust) {

                if ($region != 0) {
                    $this->regrule($_POST['recommend'], $_POST['junction'], $region);
                }

                //注册用户
                $uid = $this->checkjunction($_POST['junction'], $region);
                $_POST['id'] = $uid;
                $rel = $member_table->save($_POST);
                if ($rel)
                    $this->direcPush($uid);
            } else {
                //添加首个用户
                $_POST['junctionLevel'] = 1;
                $rel = $member_table->add($_POST);
                $uid = $rel;
                if ($rel)
                    $this->direcPush($uid);
            }
            $arr_order = $this->checkorder($product, $uid, $delivery_mode, $receiver, $mobile, $address, $post_code);
            $oid = $order_table->add($arr_order); //生成订单
            if ($oid)//生成商品订单
                $this->productorderlistadd($product, $oid, $uid);
            if (!$member_table->autoCheckToken($_POST)) {
                $json['status'] = 0;
                $json['msg'] = '请不要重复提交';
                echo json_encode($json);
                exit;
            }

            if ($rel && $oid) {
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

        if (isset($_GET['id']) && !empty($_GET['id'])) {
            $info = $member_table->field('junction,region')->find($_GET['id']);
            $junctioninfo = $member_table->field('username')->find($info['junction']);
        }

        $username = create_usercode(); //生成随机数
        $productlist = findproductlist(); //获取商品
        $banklist = findbank(); //获取银行
        $this->assign('info', $info);
        $this->assign('junctioninfo', $junctioninfo);
        $this->assign('pro_list', $productlist);
        $this->assign('banklist', $banklist);
        $this->assign('username', $username);
        $this->display();
    }

    //异步加载节点
    public function listrecommend() {


        if (IS_AJAX) {

            $member_table = M('member');
            $pId = "0";
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
                    $userinfo = $member_table->field('id,recommend')->where(array('username' => $search_username))->find();
                    if ($userinfo) {
                        $pId = $userinfo['id'];
                    } else {
                        $json['status'] = 0;
                        $json['msg'] = '会员不存在';
                        echo json_encode($json);
                        exit;
                    }
                }
            } else {
                $pId = $search_id;
            }

            if ($pId == null || $pId == "")
                $pId = "0";
            if ($pLevel == null || $pLevel == "")
                $pLevel = "0";
            if ($pName == null)
                $pName = "";
            else
                $pName = $pName . ".";

            $list = $member_table->field('id,recommend,username,name,level,regtime')->where(array('recommend' => $pId))->select();
            $count = count($list);
            echo '[';
            for ($i = 1; $i <= $count; $i++) {
                $level = findlevel($list[$i - 1]['level']);
                $regtime = date('Y-m-d H:i:s', $list[$i - 1]['regtime']);
                $nId = $list[$i - 1]['id'];
                $nName = $list[$i - 1]['username'] . '[' . $list[$i - 1]['name'] . ']' . '[' . $level . ']' . '[' . $regtime . ']';
                $info = $member_table->field('id')->where(array('recommend' => $nId))->select();
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

    /*
      //一次性加载所有节点
      public function usertree()
      {

      if(IS_POST)
      {

      //父节点数组
      $arr=array();
      $arr_str = array("name" =>'首个用户','file'=>'usertreeinfo?id=1','children'=>$this->SelectSon(1));       //父节点  Pid=1;
      array_push($arr, $arr_str);
      echo(json_encode($arr)); //这是最后返回给页面，也就是返回给AJAX请求后所得的返回数据 JSON数据
      exit;
      }
      $this->display();
      }
     */

    //查找子节点        Pid=父节点ID
    /* private function SelectSon($Pid){
      $member_table=M('member');

      if(($info=$member_table->field('id,name')->where("recommend='$Pid'")->select())) //查找该父ID下的子ID
      {
      $data=array();
      $count=count($info) ;
      for ($i=0; $i <$count ; $i++)
      {
      $da=array("name" =>$info[$i]['name'],'file'=>'usertreeinfo?id='.$info[$i]["id"].'','children'=>$this->SelectSon($info[$i]['id']));  //递归算法！
      array_push($data, $da);//加入子节点数组
      };
      return $data;//一次性返回子节点数组，他们成为同级子节点。
      }
      else
      {
      return null;
      }

      }
     */

    public function listcontactman() {

        if (IS_AJAX) {

            $member_table = M('member');
            $pId = "0";
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
                        $pId = $userinfo['id'];
                    } else {
                        $json['status'] = 0;
                        $json['msg'] = '会员不存在';
                        echo json_encode($json);
                        exit;
                    }
                }
            } else {
                $pId = $search_id;
            }

            if ($pId == null || $pId == "")
                $pId = "0";
            if ($pLevel == null || $pLevel == "")
                $pLevel = "0";
            if ($pName == null)
                $pName = "";
            else
                $pName = $pName . ".";

            $list = $member_table->field('id,junction,username,name,level,regtime')->where(array('junction' => $pId, 'recommend' => array('neq', '-1')))->select();
            $count = count($list);
            echo '[';
            for ($i = 1; $i <= $count; $i++) {
                $level = findlevel($list[$i - 1]['level']);
                $regtime = date('Y-m-d H:i:s', $list[$i - 1]['regtime']);
                $nId = $list[$i - 1]['id'];
                $nName = $list[$i - 1]['username'] . '[' . $list[$i - 1]['name'] . ']' . '[' . $level . ']' . '[' . $regtime . ']';
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

    //组织图首个用户
    public function firsthead($id,$num) {
        $member_table = M('member');
        $relust = $userInfo = $member_table->find($id);
        if ($relust) {
            $star = findlevel($userInfo[$i]['level']);
            $str.="<li> "
                    . "   <table border=\"1\" cellspacing=\"0\" cellpadding=\"0\" style=\"font-size:13px;border:0px solid #12A3F1;\" class=\"Table_xt\" align=\"center\" width=\"100%\"> "
                    . "    <tbody> "
                    . "     <tr> "
                    . "      <td align=\"left\" bgcolor=\"#d4e8fa\" height=\"20\"> "
                    . "       <table border=\"0\" cellpadding=\"3\" cellspacing=\"1\" width=\"100%\"> "
                    . "        <tbody> "
                    . "         <tr> "
                    . "          <td style=\"background-color:#f7c212;font-weight:500;\" colspan=\"3\" align=\"center\" bgcolor=\"#FFFFFF\" height=\"20\"> <span><a href=\"/Admin/Member/chart/search_username/{$userInfo['username']}/level/{$num}\">{$userInfo['username']}</a></span> </td> "
                    . "         </tr> "
                    . "         <tr> "
                    . "          <td colspan=\"2\" align=\"center\" bgcolor=\"#FFFFFF\" height=\"20\"> <span>{$userInfo['name']}</span> </td> "
                    . "         </tr> "
                    . "         <tr> "
                    . "          <td colspan=\"2\" align=\"center\" bgcolor=\"#FFFFFF\" height=\"20\"> <span>{$star}</span> </td> "
                    . "         </tr> "
                    . "         <tr> "
                    . "          <td colspan=\"2\" align=\"center\" bgcolor=\"#FFFFFF\" height=\"20\"> <span>总业绩</span> </td> "
                    . "         </tr> "
                    . "         <tr> "
                    . "          <td align=\"center\" width=\"50%\" bgcolor=\"#FFFFFF\" height=\"20\"> <span>0</span> </td> "
                    . "          <td align=\"center\" width=\"50%\" bgcolor=\"#FFFFFF\" height=\"20\"> <span style=\"color:red;\">0</span> </td> "
                    . "         </tr> "
                    . "         <tr> "
                    . "          <td colspan=\"2\" style=\"background-color:#f7c212;\" align=\"center\" bgcolor=\"#FFFFFF\" height=\"20\"> <span>第{$userInfo['junctionlevel']}层</span> </td> "
                    . "         </tr> "
                    . "        </tbody> "
                    . "       </table> </td> "
                    . "     </tr> "
                    . "    </tbody> "
                    . "   </table> ";

            return $str;
        }
    }

    //组织图
    public function chart() {
         
        $member_table = M('member');
        if (isset($_GET['search_username']) && !empty($_GET['search_username'])) {
            $userInfo = $member_table->field('id,junction')->where(array('username' => $_GET['search_username']))->find();
            $uid = $userInfo['id'];
            $junctionInfo =  get_user_name($userInfo['junction']); 
            $arr['search_junction'] = $junctionInfo;
        }  else {
            $firstid=$member_table->order('id asc ')->field('id')->find();
            $uid = $firstid['id'];
        }
        if (isset($_GET['level']) && !empty($_GET['level'])) {

            $allowlevel = $_GET['level'];
            $arr['search_level'] = $allowlevel;
        } else {
            $allowlevel = 3;
            $arr['search_level'] = $allowlevel;
        }


        $head = $this->firsthead($uid,$allowlevel);
        $relust = $this->digui($uid, $level = 1, $allowlevel);
        $this->assign('head', $head);
        $this->assign('html', $relust);

        $this->assign('arr', $arr);
        $this->display();
    }

    public function digui($id, $level, $num) {
        $member_table = M('member');
        $userInfo = $member_table->order('region asc')->field('id,username,name,level,junctionlevel,region,recommend,junction')->where(array('junction' => $id))->select();
        $count = count($userInfo);

        if ($level < $num) {
            if ($count != 0)
                $str = "<ul>" . $str;
            for ($i = 0; $i < $count; $i++) {
                if ($userInfo[$i]['recommend'] == '-1') {
                    $str.= "<li> "
                            . "  <div style=\"border: 1px #12A3F1 solid;height:125px;\"> "
                            . "   <div style=\"margin-top:10px;\"> "
                            . "    <span style=\"font-size: 15px;\">[空位]</span> "
                            . "   </div> "
                            . "   <div style=\"margin-top:20px;\"> "
                            . "    <a href=\"/Admin/Member/useradd/id/{$userInfo[$i]['id']}\"><span style=\"font-size: 15px;color:red;\">添加用户</span></a> "
                            . "   </div> "
                            . "  </div></li>";
                } else {
                    $star = findlevel($userInfo[$i]['level']);
                    $recommend=  get_user_name($userInfo[$i]['recommend']);
                    $str.="<li> "
                            . "   <table border=\"1\" cellspacing=\"0\" cellpadding=\"0\" style=\"font-size:13px;border:0px solid #12A3F1;\" class=\"Table_xt\" align=\"center\" width=\"100%\"> "
                            . "    <tbody> "
                            . "     <tr> "
                            . "      <td align=\"left\" bgcolor=\"#d4e8fa\" height=\"20\"> "
                            . "       <table border=\"0\" cellpadding=\"3\" cellspacing=\"1\" width=\"100%\"> "
                            . "        <tbody> "
                            . "         <tr> "
                            . "          <td style=\"background-color:#f7c212;font-weight:500;\" colspan=\"3\" align=\"center\" bgcolor=\"#FFFFFF\" height=\"20\"> <span><a href=\"/Admin/Member/chart/search_username/{$userInfo[$i]['username']}/level/{$num}\">{$userInfo[$i]['username']}</a></span> </td> "
                            . "         </tr> "
                            . "         <tr> "
                            . "          <td colspan=\"2\" align=\"center\" bgcolor=\"#FFFFFF\" height=\"20\"> <span>{$userInfo[$i]['name']}</span> </td> "
                            . "         </tr> "
                            . "         <tr> "
                            . "          <td colspan=\"2\" align=\"center\" bgcolor=\"#FFFFFF\" height=\"20\"> <span>{$star}</span> </td> "
                            . "         </tr> "
                            . "         <tr> "
                            . "          <td colspan=\"2\" align=\"center\" bgcolor=\"#FFFFFF\" height=\"20\"> <span>推荐人</span> </td> "
                            . "         </tr> "
                            . "         <tr> "
                           . "          <td colspan=\"2\" align=\"center\" bgcolor=\"#FFFFFF\" height=\"20\"> <span>{$recommend}</span> </td> "
                        
                            . "         </tr> "
//                            . "         <tr> "
//                            . "          <td colspan=\"2\" align=\"center\" bgcolor=\"#FFFFFF\" height=\"20\"> <span>总业绩</span> </td> "
//                            . "         </tr> "
//                            . "         <tr> "
//                            . "          <td align=\"center\" width=\"50%\" bgcolor=\"#FFFFFF\" height=\"20\"> <span>0</span> </td> "
//                            . "          <td align=\"center\" width=\"50%\" bgcolor=\"#FFFFFF\" height=\"20\"> <span style=\"color:red;\">0</span> </td> "
//                            . "         </tr> "
                            . "         <tr> "
                            . "          <td colspan=\"2\" style=\"background-color:#f7c212;\" align=\"center\" bgcolor=\"#FFFFFF\" height=\"20\"> <span>第{$userInfo[$i]['junctionlevel']}层</span> </td> "
                            . "         </tr> "
                            . "        </tbody> "
                            . "       </table> </td> "
                            . "     </tr> "
                            . "    </tbody> "
                            . "   </table> ";



                    $level++;
                    if ($count != 0)
                        $str.=self::digui($userInfo[$i]['id'], $level, $num);
                    "</li>";
                }
            }
            if ($count != 0)
                $str = $str . " </ul>";
        }
        return $str;
    }

    //充值
    public function addMemberCoin() {
        if (IS_POST) {
            
        }
        $this->display();
    }

    //扣币
    public function subtractMemberCoin() {

        $this->display();
    }

}
