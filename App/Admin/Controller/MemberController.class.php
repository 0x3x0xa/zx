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
        if (!empty($_REQUEST['search_level'])) {
            $map['level'] = $_REQUEST['search_level'];
            $search['search_level'] = $_REQUEST['search_level'];
        }
        if (!empty($_REQUEST['search_status'])) {
            $map['status'] = $_REQUEST['search_status'];
            $search['search_status'] = $_REQUEST['search_status'];
        }

        $region = array('左', '中', '右');
        $is_accounts = array('是', '否');
        $count = $member_table->where($map)->count(); // 查询满足要求的总记录数
        $Page = new \Think\Page($count, 30); // 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show(); //
        $list = $member_table->order('id desc')->where($map)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $lcount = count($list);
        for ($i = 0; $i < $lcount; $i++) {
            $list[$i]['account_name'] = (empty($list[$i]['account_name'])) ? 无 : $list[$i]['account_name'];

            $recommend = $member_table->field('username')->find($list[$i]['recommend']);
            $junction = $member_table->field('username')->find($list[$i]['junction']);
            $admin = $admin_table->field('username')->find($list[$i]['hid']);
            $list[$i]['level'] = findlevel($list[$i]['level']);
            $list[$i]['position'] = findposition($list[$i]['position']);
            $list[$i]['recommend'] = $recommend['username'];
            $list[$i]['recommend'] = (empty($list[$i]['recommend'])) ? 无 : $recommend['username'];
            $list[$i]['junction'] = $junction['username'];
            $list[$i]['junction'] = (empty($list[$i]['junction'])) ? 无 : $junction['username'];
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
            array('lingdaobonus', '开发奖'),
            array('daishubonus', '领导奖'),
            array('fenhongbonus', '全球分红'),
            array('dianzimoney', '电子积分'),
            array('gerensuodeshui', '个人所得税'),
            array('mingchebonus', '名车基金'),
            array('chongfuxiaofei', '重复消费'),
            array('integral', '赠送积分'),
            array('bieshubonus', '别墅基金'),
        );
        $region = array('左区', '中区', '右区');
        $xlsModel = M('member');
        $xlsData = $xlsModel->Field('id,username,name,account_name,bankno,bank,bank_outlets,mobile,level,'
                        . 'recommend,junction,region,regtime,status,lingdaobonus,'
                        . 'daishubonus,fenhongbonus,dianzimoney,mingcheBonus,bieshuBonus,chongfuxiaofei,gerensuodeshui,integral')->where(array('recommend' => array('neq', '-1')))->select();
        $xlscount = count($xlsData);
        for ($i = 0; $i < $xlscount; $i++) {
            $recommendInfo = $member_table->field('username')->find($xlsData[$i]['recommend']);
            $junctionInfo = $member_table->field('username')->find($xlsData[$i]['junction']);
            $xlsData[$i]['recommend'] = $recommendInfo['username'];
            $xlsData[$i]['region'] = $region[$xlsData[$i]['region']];
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
                $member_table = M('member');
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
        $userInfo = $member_table->field('gouwujifen,gouwujuan,daishubonus,allbonus,alljingtaibonus,hongbaobonus,cash,chendianchi,bieshuBonus,username,dianzimoney,kuaisuBonus,lingdaoBonus,guanliBonus,jinjiBonus,quanqiufenhongBonus,chongfuxiaofei,gerensuodeshui,mingcheBonus,zengzhibonus,fenhongbonus,lvyouBonus,chendianbonus,yuexinBonus,integral,lognum,logip')->find($id);
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
            $haveDate = $member_table->find();
            if (!$haveDate) {
                $json['status'] = 'y';
                $json['info'] = '添加首个会员无需推荐人';
                echo json_encode($json);
                exit;
            }
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

    //验证接点人
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

//    //生成三个直推(左，中，右)
//    public function direcPush($uid) {
//        $member_table = M('member');
//        $rel = $member_table->field('junctionLevel')->find($uid);
//        for ($i = 0; $i < 3; $i++) {
//            $member_table->add(array('junction' => $uid, 'region' => $i, 'recommend' => '-1', 'junctionLevel' => $rel['junctionlevel'] + 1));
//        }
//    }
//    //验证节点人信息
//    public function checkjunction($junctionid, $region) {
//        $member_table = M('member');
//        $row = $member_table->field('id,recommend')->where(array('junction' => $junctionid, 'region' => $region))->find();
//        if ($row) {
//            if ($row['recommend'] == '-1') {
//                return $row['id'];
//            } else {
//                $json['status'] = 2;
//                $json['msg'] = '位置已经被注册，请换一个位置';
//                echo json_encode($json);
//                exit;
//            }
//        } else {
//            $json['status'] = 2;
//            $json['msg'] = '接点人没找到';
//            echo json_encode($json);
//            exit;
//        }
//    }
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
            p($data);
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

    //添加用户
    public function useradd() {
        $member_table = M('member');
        $member_table->startTrans();
        if (IS_POST) {
            $region = I('post.region', '', 'trim');
            $_POST['password'] = fun_md5(I('post.password', '', 'trim'));
            $_POST['regtime'] = time();
            $_POST['regip'] = get_client_ip();
            $_POST['recommend'] = get_user_id(I('post.recommend', '', 'trim'));
            $relust = $member_table->field('id')->find();
            if ($relust) {
                //注册用户
                if ($region != 0) {
                    //判断区域
                    $this->check_region($_POST['recommend'], $_POST['recommend'], $region);
                }
                $_POST['action'] = '2';
                $rel = $member_table->add($_POST);
            } else {
                //添加首个用户
                $_POST['junctionLevel'] = 1;
                $_POST['junction'] = '0';
                $_POST['action'] = '2';
                $rel = $member_table->add($_POST);
            }

            if (!$member_table->autoCheckToken($_POST)) {
                $json['status'] = 0;
                $json['msg'] = '请不要重复提交';
                echo json_encode($json);
                exit;
            }

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



        $username = create_usercode(); //生成随机数
        //$productlist = findproductlist(); //获取商品
        $banklist = findbank(); //获取银行
        $this->assign('region', $_GET['region']);
        $this->assign('junctioninfo', $_GET['username']);
        // $this->assign('pro_list', $productlist);
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

            $list = $member_table->field('id,junction,username,name,level,regtime')->where(array('junction' => $pId))->select();
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

    //组织图
    public function chart() {

        $member_table = M('member');
        $oneInfo = $member_table->field('junction,region')->where(array('id' => '1'))->find();
        if (isset($_GET['search_username']) && !empty($_GET['search_username'])) {
            $userInfo = $member_table->field('id,junction,region')->where(array('username' => $_GET['search_username']))->find();
            if ($userInfo) {
                $uid = $userInfo['junction'];
                $region = $userInfo['region'];
            } else {
                $uid = $oneInfo['junction'];
                $region = $oneInfo['region'];
            }
            $junctionInfo = get_user_name($userInfo['junction']);
            $arr['search_junction'] = $junctionInfo;
        } else {

            $uid = $oneInfo['junction'];
            $region = $oneInfo['region'];
        }
        if (isset($_GET['level']) && !empty($_GET['level'])) {

            $allowlevel = $_GET['level'];
            $arr['search_level'] = $allowlevel;
        } else {
            $allowlevel = 3;
            $arr['search_level'] = $allowlevel;
        }


        //$head = $this->firsthead($uid, $allowlevel);
        //$relust = $this->digui($uid, $level = 1, $allowlevel);
        // first 节点人id ,tow 节点人下面要查找的人 ，three 要取出的层级
        $row = $member_table->find();
        if ($row) {
            $relust = $this->diguiquyu($uid, $region, $allowlevel - 1, $allowlevel);
        }

        /// $this->assign('head', $head);
        $this->assign('html', $relust);

        $this->assign('arr', $arr);
        $this->display();
    }

    //查询市场业绩
    public function yijichaxun($uid) {
        $todayTime = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
        $yesterdayTime = $todayTime - 60 * 60 * 24;
        $tomorrow = $todayTime + 60 * 60 * 24;
        $newtotal_table = M('newtotal');
        $balance_table = M('balance');
        $achievement_table = M('achievement');
        $allleft = $newtotal_table->where(array('uid' => $uid))->sum('leftgroupmoney'); //总左市场业绩
        $allcenter = $newtotal_table->where(array('uid' => $uid))->sum('centergroupmoney'); //总中市场业绩
        $allright = $newtotal_table->where(array('uid' => $uid))->sum('rightgroupmoney'); //总右市场业绩

        $left = $newtotal_table->where(array('uid' => $uid, 'addtime' => $todayTime))->sum('leftgroupmoney'); //今天左边新增业绩
        $center = $newtotal_table->where(array('uid' => $uid, 'addtime' => $todayTime))->sum('centergroupmoney'); //今天中边新增业绩
        $right = $newtotal_table->where(array('uid' => $uid, 'addtime' => $todayTime))->sum('rightgroupmoney'); //今天右边新增业绩
        $balance = $balance_table->where(array('uid' => $uid, 'addtime' => $tomorrow))->find(); //上一天的余额

        $upgradetime = $achievement_table->order('create_date')->field('create_date')->where(array('uid' => $uid))->find();
        $allleft = (empty($allleft)) ? 0 : $allleft;
        $allcenter = (empty($allcenter)) ? 0 : $allcenter;
        $allright = (empty($allright)) ? 0 : $allright;
        $left = (empty($left)) ? 0 : $left;
        $center = (empty($center)) ? 0 : $center;
        $right = (empty($right)) ? 0 : $right;
        $balance = (empty($balance['balance'])) ? 0 : $balance['balance'];
        $data = array(
            'allleft' => $allleft,
            'allcenter' => $allcenter,
            'allright' => $allright,
            'left' => $left,
            'center' => $center,
            'right' => $right,
            'balance' => $balance,
            'upgradetime' => $upgradetime['create_date'],
        );

        return $data;
    }

    public function diguiquyu($id, $quyu, $level, $num) {
        //查找结点id=id，且区域=$quyu的数据
        $retunStr2 = '';
        $panduan = "&lt;table";
        $member_table = M('member');
        $row = $member_table->where(array('junction' => $id, 'region' => $quyu))->find();
        $userInfo = $member_table->field('username')->find($id);
        if ($row) {
            if ($level == 0) {
                return $this->new_Sites($row['id'], $num);
            }
            $temp = self::diguiquyu($row['id'], 0, $level - 1, $num);
            if (substr(htmlspecialchars($temp), 0, 9) == $panduan) {
                $retunStr2.= "<td class=\"node-container\" colspan=\"2\">" . $temp . "</td>";
            } else {
                $retunStr2.=$temp;
            }

            $temp = self::diguiquyu($row['id'], 1, $level - 1, $num);
            if (substr(htmlspecialchars($temp), 0, 9) == $panduan) {
                $retunStr2.= "<td class=\"node-container\" colspan=\"2\">" . $temp . " </td>";
            } else {
                $retunStr2.=$temp;
            }

            $temp = self::diguiquyu($row['id'], 2, $level - 1, $num);
            if (substr(htmlspecialchars($temp), 0, 9) == $panduan) {
                $retunStr2.= "<td class=\"node-container\" colspan=\"2\">" . $temp . " </td>";
            } else {
                $retunStr2.=$temp;
            }

            $str_head = $this->new_head($row['id'], $num);
            $retunStr2 = $str_head . $retunStr2 . "</tr></tbody></table>";
            return $retunStr2;
        } else {
            return $this->new_empty($userInfo['username'], $quyu);
        }
    }

    public function new_head($id, $num) {

        $member_table = M('member');
        $userInfo = $member_table->find($id);
        $data = $this->yijichaxun($userInfo['id']);
        $star = findlevel($userInfo['level']);
        $recommend = get_user_name($userInfo['recommend']);
        $recommend = (empty($recommend)) ? 无 : $recommend;
        $upgrodetime = date('Y-m-d', $data['upgradetime']);
        $str = "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\">"
                . " <tbody>"
                . "   <tr class=\"node-cells\">"
                . "     <td class=\"node-cell\" colspan=\"6\">"
                . "       <div class=\"node\" style=\"cursor: n-resize;\">"
                . "         <table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"1\" align=\"center\" class=\"Table_xt\" style=\"font-size:13px;border:0px solid #12A3F1;\">"
                . "           <tbody>"
                . "             <tr>"
                . "               <td height=\"20\" bgcolor=\"#d4e8fa\" align=\"left\">"
                . "                 <table width=\"100%\" cellspacing=\"1\" cellpadding=\"3\" border=\"1\">"
                . "                   <tbody>"
                . "                     <tr>"
                . "                       <td height=\"20\" bgcolor=\"#FFFFFF\" align=\"center\" colspan=\"3\" style=\"background-color:#f7c212;font-weight:500;\">"
                . "                         <span>"
                . "                           <a href=\"/Admin/Member/chart/search_username/{$userInfo['username']}/level/{$num}\">{$userInfo['username']}</a></span>"
                . "                       </td>"
                . "                     </tr>"
                . "                     <tr>"
                . "                       <td height=\"20\" bgcolor=\"#FFFFFF\" align=\"center\" colspan=\"3\">"
                . "                         <span>{$userInfo['name']}{$userInfo['id']}</span></td>"
                . "                     </tr>"
                . "                     <tr>"
                . "                       <td height=\"20\" bgcolor=\"#FFFFFF\" align=\"left\" colspan=\"1\">"
                . "                         <span>推荐人</span></td>"
                . "                       <td height=\"20\" bgcolor=\"#FFFFFF\" align=\"left\" colspan=\"2\">"
                . "                         <span>{$recommend}</span></td>"
                . "                     </tr>"
                . "                     <tr>"
                . "                       <td height=\"20\" bgcolor=\"#FFFFFF\" align=\"left\" colspan=\"1\">"
                . "                         <span>级别</span></td>"
                . "                       <td height=\"20\" bgcolor=\"#FFFFFF\" align=\"center\" colspan=\"2\">"
                . "                         <span>{$star}</span></td>"
                . "                     </tr>"
                . "                     <tr>"
                . "                       <td height=\"20\" bgcolor=\"#FFFFFF\" align=\"left\">"
                . "                         <span>升级时间</span></td>"
                . "                       <td height=\"20\" bgcolor=\"#FFFFFF\" align=\"left\" colspan=\"2\">"
                . "                         <span>{$upgrodetime}</span></td>"
                . "                     </tr>"
                . "                     <tr>"
                . "                       <td height=\"20\" bgcolor=\"#FFFFFF\" align=\"center\">"
                . "                         <span>区域</span></td>"
                . "                       <td height=\"20\" bgcolor=\"#FFFFFF\" align=\"center\">"
                . "                         <span>总</span></td>"
                . "                       <td height=\"20\" bgcolor=\"#FFFFFF\" align=\"center\">"
                . "                         <span>新</span></td>"
                . "                     </tr>"
                . "                     <tr>"
                . "                       <td height=\"20\" bgcolor=\"#FFFFFF\" align=\"center\">"
                . "                         <span>左</span></td>"
                . "                       <td height=\"20\" bgcolor=\"#FFFFFF\" align=\"center\">"
                . "                         <span style=\"color:#04580E;\">{$data['allleft']}</span></td>"
                . "                       <td height=\"20\" bgcolor=\"#FFFFFF\" align=\"center\">"
                . "                         <span style=\"color:#04580E;\">{$data['left']}</span></td>"
                . "                     </tr>"
                . "                     <tr>"
                . "                       <td height=\"20\" bgcolor=\"#FFFFFF\" align=\"center\">"
                . "                         <span>中</span></td>"
                . "                       <td height=\"20\" bgcolor=\"#FFFFFF\" align=\"center\">"
                . "                         <span>{$data['allcenter']}</span></td>"
                . "                       <td height=\"20\" bgcolor=\"#FFFFFF\" align=\"center\">"
                . "                         <span>{$data['center']}</span></td>"
                . "                     </tr>"
                . "                     <tr>"
                . "                       <td height=\"20\" width=\"50%\" bgcolor=\"#FFFFFF\" align=\"center\">"
                . "                         <span>右</span></td>"
                . "                       <td height=\"20\" width=\"50%\" bgcolor=\"#FFFFFF\" align=\"center\">"
                . "                         <span style=\"color:red;\">{$data['allright']}</span></td>"
                . "                       <td height=\"20\" width=\"50%\" bgcolor=\"#FFFFFF\" align=\"center\">"
                . "                         <span style=\"color:red;\">{$data['right']}</span></td>"
                . "                     </tr>"
                . "                     <tr>"
                . "                       <td height=\"20\" width=\"50%\" bgcolor=\"#FFFFFF\" align=\"center\">"
                . "                         <span>余额</span></td>"
                . "                       <td height=\"20\" width=\"50%\" bgcolor=\"#FFFFFF\" align=\"center\" colspan=\"2\">"
                . "                         <span style=\"color:red;\">{$data['balance']}</span></td>"
                . "                     </tr>"
                . "                     <tr>"
                . "                       <td height=\"20\" bgcolor=\"#FFFFFF\" align=\"center\" style=\"background-color:#f7c212;\" colspan=\"3\">"
                . "                         <span>第{$userInfo['junctionlevel']}层</span></td>"
                . "                     </tr>"
                . "                   </tbody>"
                . "                 </table>"
                . "               </td>"
                . "             </tr>"
                . "           </tbody>"
                . "         </table>"
                . "       </div>"
                . "     </td>"
                . "   </tr>"
                . "   <tr>"
                . "     <td colspan=\"6\">"
                . "       <div class=\"line down\"></div>"
                . "     </td>"
                . "   </tr>"
                . "   <tr>"
                . "     <td class=\"line left\">&nbsp;</td>"
                . "     <td class=\"line right top\">&nbsp;</td>"
                . "     <td class=\"line left top\">&nbsp;</td>"
                . "     <td class=\"line right top\">&nbsp;</td>"
                . "     <td class=\"line left top\">&nbsp;</td>"
                . "     <td class=\"line right\">&nbsp;</td></tr>"
                . "   <tr>"
                . "";

        return $str;
    }

    public function new_empty($jname, $quyu) {

        $str = "<td class=\"node-container\" colspan=\"2\">"
                . "       <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\">"
                . "         <tbody>"
                . "           <tr class=\"node-cells\">"
                . "             <td class=\"node-cell\" colspan=\"2\">"
                . "               <div class=\"node\">"
                . "                 <div style=\"border: 1px #12A3F1 solid;height:230px;\">"
                . "                   <div style=\"margin-top:10px;\">"
                . "                     <span style=\"font-size: 15px;\">[空位]</span></div>"
                . "                   <div style=\"margin-top:20px;\">"
                . "                     <a href=\"/Admin/Member/useradd/username/{$jname}/region/\">"
                . "                       <span style=\"font-size: 15px;color:red;\">添加用户</span></a>"
                . "                   </div>"
                . "                 </div>"
                . "               </div>"
                . "             </td>"
                . "           </tr>"
                . "         </tbody>"
                . "       </table>"
                . "     </td>"
                . "";


        return $str;
    }

    public function new_Sites($id, $num) {

        $member_table = M('member');
        $userInfo = $member_table->find($id);
        $data = $this->yijichaxun($userInfo['id']);
        $star = findlevel($userInfo['level']);
        $recommend = get_user_name($userInfo['recommend']);
        $recommend = (empty($recommend)) ? 无 : $recommend;
        $upgrodetime = date('Y-m-d', $data['upgradetime']);

        $str = "<td class=\"node-container\" colspan=\"2\">"
                . "       <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\">"
                . "         <tbody>"
                . "           <tr class=\"node-cells\">"
                . "             <td class=\"node-cell\" colspan=\"2\">"
                . "               <div class=\"node\">"
                . "                 <table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"1\" align=\"center\" class=\"Table_xt\" style=\"font-size:13px;border:0px solid #12A3F1;\">"
                . "                   <tbody>"
                . "                     <tr>"
                . "                       <td height=\"20\" bgcolor=\"#d4e8fa\" align=\"left\">"
                . "                         <table width=\"100%\" cellspacing=\"1\" cellpadding=\"3\" border=\"1\">"
                . "                           <tbody>"
                . "                             <tr>"
                . "                               <td height=\"20\" bgcolor=\"#FFFFFF\" align=\"center\" colspan=\"3\" style=\"background-color:#f7c212;font-weight:500;\">"
                . "                                 <span>"
                . "                                   <a href=\"/Admin/Member/chart/search_username/{$userInfo['username']}/level/{$num}\">{$userInfo['username']}</a></span>"
                . "                               </td>"
                . "                             </tr>"
                . "                             <tr>"
                . "                               <td height=\"20\" bgcolor=\"#FFFFFF\" align=\"center\" colspan=\"3\">"
                . "                                 <span>{$userInfo['name']}{$userInfo['id']}</span></td>"
                . "                             </tr>"
                . "                             <tr>"
                . "                               <td height=\"20\" bgcolor=\"#FFFFFF\" align=\"left\" colspan=\"1\">"
                . "                                 <span>推荐人</span></td>"
                . "                               <td height=\"20\" bgcolor=\"#FFFFFF\" align=\"left\" colspan=\"2\">"
                . "                                 <span>{$recommend}</span></td>"
                . "                             </tr>"
                . "                             <tr>"
                . "                               <td height=\"20\" bgcolor=\"#FFFFFF\" align=\"left\" colspan=\"1\">"
                . "                                 <span>级别</span></td>"
                . "                               <td height=\"20\" bgcolor=\"#FFFFFF\" align=\"center\" colspan=\"2\">"
                . "                                 <span>{$star}</span></td>"
                . "                             </tr>"
                . "                             <tr>"
                . "                               <td height=\"20\" bgcolor=\"#FFFFFF\" align=\"left\">"
                . "                                 <span>升级时间</span></td>"
                . "                               <td height=\"20\" bgcolor=\"#FFFFFF\" align=\"left\" colspan=\"2\">"
                . "                                 <span>{$upgrodetime}</span></td>"
                . "                             </tr>"
                . "                             <tr>"
                . "                               <td height=\"20\" bgcolor=\"#FFFFFF\" align=\"center\">"
                . "                                 <span>区域</span></td>"
                . "                               <td height=\"20\" bgcolor=\"#FFFFFF\" align=\"center\">"
                . "                                 <span>总</span></td>"
                . "                               <td height=\"20\" bgcolor=\"#FFFFFF\" align=\"center\">"
                . "                                 <span>新</span></td>"
                . "                             </tr>"
                . "                             <tr>"
                . "                               <td height=\"20\" bgcolor=\"#FFFFFF\" align=\"center\">"
                . "                                 <span>左</span></td>"
                . "                               <td height=\"20\" bgcolor=\"#FFFFFF\" align=\"center\">"
                . "                                 <span style=\"color:#04580E;\">{$data['allleft']}</span></td>"
                . "                               <td height=\"20\" bgcolor=\"#FFFFFF\" align=\"center\">"
                . "                                 <span style=\"color:#04580E;\">{$data['left']}</span></td>"
                . "                             </tr>"
                . "                             <tr>"
                . "                               <td height=\"20\" bgcolor=\"#FFFFFF\" align=\"center\">"
                . "                                 <span>中</span></td>"
                . "                               <td height=\"20\" bgcolor=\"#FFFFFF\" align=\"center\">"
                . "                                 <span>{$data['allcenter']}</span></td>"
                . "                               <td height=\"20\" bgcolor=\"#FFFFFF\" align=\"center\">"
                . "                                 <span>{$data['center']}</span></td>"
                . "                             </tr>"
                . "                             <tr>"
                . "                               <td height=\"20\" width=\"50%\" bgcolor=\"#FFFFFF\" align=\"center\">"
                . "                                 <span>右</span></td>"
                . "                               <td height=\"20\" width=\"50%\" bgcolor=\"#FFFFFF\" align=\"center\">"
                . "                                 <span style=\"color:red;\">{$data['allright']}</span></td>"
                . "                               <td height=\"20\" width=\"50%\" bgcolor=\"#FFFFFF\" align=\"center\">"
                . "                                 <span style=\"color:red;\">{$data['right']}</span></td>"
                . "                             </tr>"
                . "                             <tr>"
                . "                               <td height=\"20\" width=\"50%\" bgcolor=\"#FFFFFF\" align=\"center\">"
                . "                                 <span>余额</span></td>"
                . "                               <td height=\"20\" width=\"50%\" bgcolor=\"#FFFFFF\" align=\"center\" colspan=\"2\">"
                . "                                 <span style=\"color:red;\">{$data['balance']}</span></td>"
                . "                             </tr>"
                . "                             <tr>"
                . "                               <td height=\"20\" bgcolor=\"#FFFFFF\" align=\"center\" style=\"background-color:#f7c212;\" colspan=\"3\">"
                . "                                 <span>第{$userInfo['junctionlevel']}层</span></td>"
                . "                             </tr>"
                . "                           </tbody>"
                . "                         </table>"
                . "                       </td>"
                . "                     </tr>"
                . "                   </tbody>"
                . "                 </table>"
                . "               </div>"
                . "             </td>"
                . "           </tr>"
                . "         </tbody>"
                . "       </table>"
                . "     </td>"
                . "";

        return $str;
    }

    //充值
    public function addMemberCoin() {
        $type = getbonustype();
        if (IS_POST) {
            $todayTime = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
            $beginThismonth = mktime(0, 0, 0, date('m'), 1, date('Y'));  //当月第一天时间戳
            $week = mktime(0, 0, 0, date('m'), date('d') - date('w'), date('Y')); //当周第一天
            $member_table = M('member');
            $bonus_table = M('bonus');
            $recharge_table = M('recharge');
            $member_table->startTrans();
            $type = I('post.type', '', 'htmlspecialchars');
            $username = I('post.username', '', 'htmlspecialchars');
            $income = I('post.income', '', 'htmlspecialchars');
            $message = I('post.message', '', 'htmlspecialchars');

            $userInfo = $member_table->field('gouwujifen,gouwujuan,chendianchi,cash,id,dianzimoney,kuaisuBonus,lingdaoBonus,guanliBonus,jinjiBonus,quanqiufenhongBonus,bieshuBonus,chongfuxiaofei,hongbaobonus,gerensuodeshui,mingcheBonus,fenhongbonus,zengzhibonus,integral,gongyijijin,lvyouBonus,yuexinBonus,chendianbonus,allbonus,alljingtaibonus')->where(array('username' => $username))->find();
            if (!$userInfo) {
                $json['status'] = 2;
                $json['msg'] = '用户不存在！';
                echo json_encode($json);
                exit;
            }

            switch ($type) {
                case 1:
                    $allmoney = $userInfo['kuaisubonus'] + $income;
                    $relust1 = $member_table->save(array('id' => $userInfo['id'], 'kuaisuBonus' => $allmoney));
                    break;
                case 2:
                    $allmoney = $userInfo['lingdaobonus'] + $income;
                    $relust1 = $member_table->save(array('id' => $userInfo['id'], 'lingdaoBonus' => $allmoney));
                    break;
                case 3:
                    $allmoney = $userInfo['guanlibonus'] + $income;
                    $relust1 = $member_table->save(array('id' => $userInfo['id'], 'guanliBonus' => $allmoney));
                    break;
                case 4:
                    $allmoney = $userInfo['jinjibonus'] + $income;
                    $relust1 = $member_table->save(array('id' => $userInfo['id'], 'jinjiBonus' => $allmoney));
                    break;
                case 5:
                    $allmoney = $userInfo['quanqiufenhongbonus'] + $income;
                    $relust1 = $member_table->save(array('id' => $userInfo['id'], 'quanqiufenhongBonus' => $allmoney));
                    break;
                case 6:
                    $allmoney = $userInfo['dianzimoney'] + $income;
                    $relust1 = $member_table->save(array('id' => $userInfo['id'], 'dianzimoney' => $allmoney));
                    break;
                case 7:
                    $allmoney = $userInfo['gerensuodeshui'] + $income;
                    $relust1 = $member_table->save(array('id' => $userInfo['id'], 'gerensuodeshui' => $allmoney));
                    break;
                case 8:
                    $allmoney = $userInfo['mingchebonus'] + $income;
                    $relust1 = $member_table->save(array('id' => $userInfo['id'], 'mingcheBonus' => $allmoney));
                    break;
                case 9:
                    $allmoney = $userInfo['chongfuxiaofei'] + $income;
                    $relust1 = $member_table->save(array('id' => $userInfo['id'], 'chongfuxiaofei' => $allmoney));
                    break;
                case 10:
                    $allmoney = $userInfo['integral'] + $income;
                    $relust1 = $member_table->save(array('id' => $userInfo['id'], 'integral' => $allmoney));
                    break;
                case 11:
                    $allmoney = $userInfo['bieshubonus'] + $income;
                    $relust1 = $member_table->save(array('id' => $userInfo['id'], 'bieshuBonus' => $allmoney));
                    break;
                case 12:
                    $allmoney = $userInfo['hongbaobonus'] + $income;
                    $relust1 = $member_table->save(array('id' => $userInfo['id'], 'hongbaobonus' => $allmoney));
                    break;
                case 13:
                    $allmoney = $userInfo['zengzhibonus'] + $income;
                    $relust1 = $member_table->save(array('id' => $userInfo['id'], 'zengzhibonus' => $allmoney));
                    break;
                case 14:
                    $allmoney = $userInfo['fenhongbonus'] + $income;
                    $relust1 = $member_table->save(array('id' => $userInfo['id'], 'fenhongbonus' => $allmoney));
                    break;
                case 15:
                    $allmoney = $userInfo['lvyoubonus'] + $income;
                    $relust1 = $member_table->save(array('id' => $userInfo['id'], 'lvyouBonus' => $allmoney));
                    break;
                case 16:
                    $allmoney = $userInfo['gongyijijin'] + $income;
                    $relust1 = $member_table->save(array('id' => $userInfo['id'], 'gongyijijin' => $allmoney));
                    break;
                case 17:
                    $allmoney = $userInfo['yuexinbonus'] + $income;
                    $relust1 = $member_table->save(array('id' => $userInfo['id'], 'yuexinBonus' => $allmoney));
                    break;
//                case 18:
//                    $allmoney = $userInfo['chendianbonus'] + $income;
//                    $relust1 = $member_table->save(array('id' => $userInfo['id'], 'chendianbonus' => $allmoney));
//                    break;
                case 19:
                    $allmoney = $userInfo['allbonus'] + $income;
                    $relust1 = $member_table->save(array('id' => $userInfo['id'], 'allbonus' => $allmoney));
                    break;
                case 20:
                    $allmoney = $userInfo['alljingtaibonus'] + $income;
                    $relust1 = $member_table->save(array('id' => $userInfo['id'], 'alljingtaibonus' => $allmoney));
                    break;
                case 21:
                    $allmoney = $userInfo['chendianchi'] + $income;
                    $relust1 = $member_table->save(array('id' => $userInfo['id'], 'chendianchi' => $allmoney));
                    break;
                case 22:
                    $allmoney = $userInfo['cash'] + $income;
                    $relust1 = $member_table->save(array('id' => $userInfo['id'], 'cash' => $allmoney));
                    break;
                case 23:
                    $allmoney = $userInfo['daishubonus'] + $income;
                    $relust1 = $member_table->save(array('id' => $userInfo['id'], 'daishubonus' => $allmoney));
                    break;
                 case 24:
                    $allmoney = $userInfo['gouwujifen'] + $income;
                    $relust1 = $member_table->save(array('id' => $userInfo['id'], 'gouwujifen' => $allmoney));
                    break;
                case 25:
                    $allmoney = $userInfo['gouwujuan'] + $income;
                    $relust1 = $member_table->save(array('id' => $userInfo['id'], 'gouwujuan' => $allmoney));
                    break;
                default:
                    $json['status'] = 2;
                    $json['msg'] = '积分类型不存在';
                    echo json_encode($json);
                    exit;
            }


            $relust2 = $bonus_table->add(array('uid' => $userInfo['id'], 'type' => $type, 'income' => $income, 'status' => 1, 'balance' => $allmoney, 'message' => $message, 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '1'));
            $relust3 = $recharge_table->add(array('uid' => $userInfo['id'], 'hid' => session('userid'), 'money' => $income, 'type' => $type, 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'status' => '1'));
            if ($relust1 && $relust2 && $relust3) {
                $member_table->commit();
                $json['status'] = 1;
                $json['msg'] = '充值成功！';
                echo json_encode($json);
                exit;
            } else {
                $member_table->rollback();
                $json['status'] = 2;
                $json['msg'] = '充值失败！';
                echo json_encode($json);
                exit;
            }
        }
        $this->assign('type', $type);
        $this->display();
    }

    //扣币
    public function subtractMemberCoin() {
        $type = getbonustype();
        if (IS_POST) {
            $todayTime = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
            $beginThismonth = mktime(0, 0, 0, date('m'), 1, date('Y'));  //当月第一天时间戳
            $week = mktime(0, 0, 0, date('m'), date('d') - date('w'), date('Y')); //当周第一天
            $member_table = M('member');
            $bonus_table = M('bonus');
            $recharge_table = M('recharge');
            $member_table->startTrans();
            $type = I('post.type', '', 'htmlspecialchars');
            $username = I('post.username', '', 'htmlspecialchars');
            $expend = I('post.expend', '', 'htmlspecialchars');
            $message = I('post.message', '', 'htmlspecialchars');

            $userInfo = $member_table->field('gouwujifen,gouwujuan,chendianchi,cash,id,dianzimoney,kuaisuBonus,lingdaoBonus,guanliBonus,jinjiBonus,quanqiufenhongBonus,bieshuBonus,chongfuxiaofei,hongbaobonus,gerensuodeshui,mingcheBonus,fenhongbonus,zengzhibonus,integral,gongyijijin,lvyouBonus,yuexinBonus')->where(array('username' => $username))->find();
            if (!$userInfo) {
                $json['status'] = 2;
                $json['msg'] = '用户不存在！';
                echo json_encode($json);
                exit;
            }

            switch ($type) {
                case 1:
                    $allmoney = $userInfo['kuaisubonus'] - $expend;
                    $relust1 = $member_table->save(array('id' => $userInfo['id'], 'kuaisuBonus' => $allmoney));
                    break;
                case 2:
                    $allmoney = $userInfo['lingdaobonus'] - $expend;
                    $relust1 = $member_table->save(array('id' => $userInfo['id'], 'lingdaoBonus' => $allmoney));
                    break;
                case 3:
                    $allmoney = $userInfo['guanlibonus'] - $expend;
                    $relust1 = $member_table->save(array('id' => $userInfo['id'], 'guanliBonus' => $allmoney));
                    break;
                case 4:
                    $allmoney = $userInfo['jinjibonus'] - $expend;
                    $relust1 = $member_table->save(array('id' => $userInfo['id'], 'jinjiBonus' => $allmoney));
                    break;
                case 5:
                    $allmoney = $userInfo['quanqiufenhongbonus'] - $expend;
                    $relust1 = $member_table->save(array('id' => $userInfo['id'], 'quanqiufenhongBonus' => $allmoney));
                    break;
                case 6:
                    $allmoney = $userInfo['dianzimoney'] - $expend;
                    $relust1 = $member_table->save(array('id' => $userInfo['id'], 'dianzimoney' => $allmoney));
                    break;
                case 7:
                    $allmoney = $userInfo['gerensuodeshui'] - $expend;
                    $relust1 = $member_table->save(array('id' => $userInfo['id'], 'gerensuodeshui' => $allmoney));
                    break;
                case 8:
                    $allmoney = $userInfo['mingchebonus'] - $expend;
                    $relust1 = $member_table->save(array('id' => $userInfo['id'], 'mingcheBonus' => $allmoney));
                    break;
                case 9:
                    $allmoney = $userInfo['chongfuxiaofei'] - $expend;
                    $relust1 = $member_table->save(array('id' => $userInfo['id'], 'chongfuxiaofei' => $allmoney));
                    break;
                case 10:
                    $allmoney = $userInfo['integral'] - $expend;
                    $relust1 = $member_table->save(array('id' => $userInfo['id'], 'integral' => $allmoney));
                    break;
                case 11:
                    $allmoney = $userInfo['bieshubonus'] - $expend;
                    $relust1 = $member_table->save(array('id' => $userInfo['id'], 'bieshuBonus' => $allmoney));
                    break;
                case 12:
                    $allmoney = $userInfo['hongbaobonus'] - $expend;
                    $relust1 = $member_table->save(array('id' => $userInfo['id'], 'hongbaobonus' => $allmoney));
                    break;
                case 13:
                    $allmoney = $userInfo['zengzhibonus'] - $expend;
                    $relust1 = $member_table->save(array('id' => $userInfo['id'], 'zengzhibonus' => $allmoney));
                    break;
                case 14:
                    $allmoney = $userInfo['fenhongbonus'] - $expend;
                    $relust1 = $member_table->save(array('id' => $userInfo['id'], 'fenhongbonus' => $allmoney));
                    break;
                case 15:
                    $allmoney = $userInfo['lvyoubonus'] - $expend;
                    $relust1 = $member_table->save(array('id' => $userInfo['id'], 'lvyouBonus' => $allmoney));
                    break;
                case 16:
                    $allmoney = $userInfo['gongyijijin'] - $expend;
                    $relust1 = $member_table->save(array('id' => $userInfo['id'], 'gongyijijin' => $allmoney));
                    break;
                case 17:
                    $allmoney = $userInfo['yuexinbonus'] - $expend;
                    $relust1 = $member_table->save(array('id' => $userInfo['id'], 'yuexinBonus' => $allmoney));
                    break;
//                case 18:
//                    $allmoney = $userInfo['chendianbonus'] - $expend;
//                    $relust1 = $member_table->save(array('id' => $userInfo['id'], 'chendianbonus' => $allmoney));
//                    break;
                case 19:
                    $allmoney = $userInfo['allbonus'] - $expend;
                    $relust1 = $member_table->save(array('id' => $userInfo['id'], 'allbonus' => $allmoney));
                    break;
                case 20:
                    $allmoney = $userInfo['alljingtaibonus'] - $expend;
                    $relust1 = $member_table->save(array('id' => $userInfo['id'], 'alljingtaibonus' => $allmoney));
                    break;
                case 21:
                    $allmoney = $userInfo['chendianchi'] - $expend;
                    $relust1 = $member_table->save(array('id' => $userInfo['id'], 'chendianchi' => $allmoney));
                    break;
                case 22:
                    $allmoney = $userInfo['cash'] - $expend;
                    $relust1 = $member_table->save(array('id' => $userInfo['id'], 'cash' => $allmoney));
                    break;
                case 23:
                    $allmoney = $userInfo['daishubonus'] - $expend;
                    $relust1 = $member_table->save(array('id' => $userInfo['id'], 'daishubonus' => $allmoney));
                    break;
                 case 24:
                    $allmoney = $userInfo['gouwujifen'] - $expend;
                    $relust1 = $member_table->save(array('id' => $userInfo['id'], 'gouwujifen' => $allmoney));
                    break;
                case 25:
                    $allmoney = $userInfo['gouwujuan'] - $expend;
                    $relust1 = $member_table->save(array('id' => $userInfo['id'], 'gouwujuan' => $allmoney));
                    break;
                default:
                    $json['status'] = 2;
                    $json['msg'] = '积分类型不存在';
                    echo json_encode($json);
                    exit;
            }

            $relust2 = $bonus_table->add(array('uid' => $userInfo['id'], 'type' => $type, 'expend' => $expend, 'status' => 2, 'balance' => $allmoney, 'message' => $message, 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '2'));
            $relust3 = $recharge_table->add(array('uid' => $userInfo['id'], 'hid' => session('userid'), 'money' => $expend, 'type' => $type, 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'status' => '2'));

            if ($relust1 && $relust2 && $relust3) {
                $member_table->commit();
                $json['status'] = 1;
                $json['msg'] = '扣币成功！';
                echo json_encode($json);
                exit;
            } else {
                $member_table->rollback();
                $json['status'] = 2;
                $json['msg'] = '扣币失败！';
                echo json_encode($json);
                exit;
            }
        }
        $this->assign('type', $type);
        $this->display();
    }

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

    //会员升级
    public function upgrade() {

         $data = getbonusparam(); //获取的奖金比例参数
        $todayTime = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
        $beginThismonth = mktime(0, 0, 0, date('m'), 1, date('Y'));  //当月第一天时间戳
        $week = mktime(0, 0, 0, date('m'), date('d') - date('w'), date('Y')); //当周第一天
        $member_table = M('member');
        $memberlevel_table = M('memberlevel');
        $bonus_table = M('bonus');
        $upgrade_table = M('upgrade');
        $achievement_table = M('achievement');
        $recharge_table = M('recharge');
        $upgrade_table = M('upgrade');
        $member_table->startTrans();
        if (IS_POST) {
            $status = I('post.status', '', 'htmlspecialchars');
            $level = I('post.level', '', 'htmlspecialchars');
            $username = I('post.username', '', 'htmlspecialchars');
            $userInfo = $member_table->field('id,level,dianzimoney,integral,integral,junction,region,recommend,username,cash,gouwujifen,gouwujuan')->where(array('username' => $username))->find();
            //获取到原来级别等级的钱
            $historymoney = $upgrade_table->where(array('uid' => $userInfo['id']))->sum('money'); //获取到历史消费的金额
            $historymoney = (empty($historymoney)) ? 0 : $historymoney;
            $memberlevelmoney = $memberlevel_table->field('registermoney,title')->find($level);
            $memberlevelmoney['registermoney'] = $memberlevelmoney['registermoney'] - $historymoney;
            $oldnamelevel = $memberlevel_table->field('title')->find($userInfo['level']);
            if (!$userInfo) {
                $json['status'] = 2;
                $json['msg'] = '用户不存在！';
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
            $alllevel=array('2','3','4');//前三个级别的id号
            if ($status == 1) {
                $xiaofeidianzibi = $memberlevelmoney['registermoney'] * 0.5; //消费的电子积分
                $xiaofeicash = $memberlevelmoney['registermoney'] * 0.5; //消费的现金
                       
                     if( $userInfo['cash']>= $memberlevelmoney['registermoney']&&$userInfo['dianzimoney']==0){
                       
                        $allcashmoney = $userInfo['cash'] - ($xiaofeicash+$xiaofeidianzibi);
                       
                        $allgouwujifen= $userInfo['gouwujifen'] + $memberlevelmoney['registermoney'];//购物积分
                        if(in_array($level, $alllevel))
                        {
                            $allintegral = $userInfo['integral'] + $memberlevelmoney['registermoney']*0.5;//赠送积分
                            $allgouwujuan = $userInfo['gouwujuan'] + $memberlevelmoney['registermoney']*0.5;//购物卷
                          
                        }
                        else{
                            //level==5
                            $gudingjifen=$data['zuankacanshu'];
                            $allintegral = $userInfo['integral'] +$gudingjifen;//赠送积分
                        }
                        
                        if ($userInfo['junction'] == '-1') {
                            $jinfo = $this->returnid($userInfo['recommend'], $userInfo['region']); //获取到节点人id 
                            $relust1 = $member_table->save(array('id' => $userInfo['id'], 'level' => $level,'gouwujifen'=>$allgouwujifen,'gouwujuan'=>$allgouwujuan, 'integral' => $allintegral, 'cash' => $allcashmoney, 'junction' => $jinfo['id'], 'region' => $jinfo['qu'], 'junctionLevel' => $jinfo['level']));
                        } else {
                            $relust1 = $member_table->save(array('id' => $userInfo['id'],'level' => $level,'gouwujifen'=>$allgouwujifen,'gouwujuan'=>$allgouwujuan, 'integral' => $allintegral, 'cash' => $allcashmoney));
                        }
                   
                        $relust6 = $bonus_table->add(array('uid' => $userInfo['id'], 'type' => 22, 'expend' => $memberlevelmoney['registermoney'], 'status' => 2, 'balance' => $allcashmoney, 'message' => '会员升级', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '7'));
                        if(in_array($level, $alllevel)){
                        $relust3 = $bonus_table->add(array('uid' => $userInfo['id'], 'type' => 10, 'income' => $memberlevelmoney['registermoney']*0.5, 'status' => 1, 'balance' => $allintegral, 'message' => '会员升级赠送积分', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '7'));
                        $bonus_table->add(array('uid' => $userInfo['id'], 'type' => 25, 'income' => $memberlevelmoney['registermoney']*0.5, 'status' => 1, 'balance' => $allgouwujuan, 'message' => '会员升级赠送购物卷', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '7'));
                     
                        }
                        else{
                            //level =5 
                             $relust3 = $bonus_table->add(array('uid' => $userInfo['id'], 'type' => 10, 'income' => $gudingjifen, 'status' => 1, 'balance' => $allintegral, 'message' => '会员升级赠送积分', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '7'));
                             
                        }
                        $bonus_table->add(array('uid' => $userInfo['id'], 'type' => 24, 'income' => $memberlevelmoney['registermoney'], 'status' => 1, 'balance' => $allgouwujifen, 'message' => '会员升级赠送购物积分', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '7'));
                       
                        $relust4 = $upgrade_table->add(array('uid' => $userInfo['id'], 'money' => $memberlevelmoney['registermoney'], 'status' => '1', 'oldlevel' => $userInfo['level'], 'newlevel' => $level, 'oldname' => $oldnamelevel['title'], 'newname' => $memberlevelmoney['title'], 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'action' => '2', 'hid' => $_SESSION['userid']));
                        $relust5 = $achievement_table->add(array('uid' => $userInfo['id'], 'money' => $memberlevelmoney['registermoney'], 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '2'));
                   
                        
                    }
                    else if( $userInfo['cash']>= $memberlevelmoney['registermoney']&& $userInfo['dianzimoney']>0&& $userInfo['dianzimoney']<$xiaofeidianzibi ){
                        $chongzhimoney = $xiaofeidianzibi - $userInfo['dianzimoney'];
                        
                        $allcashmoney = $userInfo['cash'] - ($xiaofeicash+$chongzhimoney);
                        
                        $allgouwujifen= $userInfo['gouwujifen'] + $memberlevelmoney['registermoney'];//购物积分
                        if(in_array($level, $alllevel))
                        {
                            $allintegral = $userInfo['integral'] + $memberlevelmoney['registermoney']*0.5;//赠送积分
                            $allgouwujuan = $userInfo['gouwujuan'] + $memberlevelmoney['registermoney']*0.5;//购物卷
                          
                        }
                        else{
                            //level==5
                            $gudingjifen=$data['zuankacanshu'];
                            $allintegral = $userInfo['integral'] +$gudingjifen;//赠送积分
                        }
                        
                        if ($userInfo['junction'] == '-1') {
                            $jinfo = $this->returnid($userInfo['recommend'], $userInfo['region']); //获取到节点人id 
                            $relust1 = $member_table->save(array('id' => $userInfo['id'], 'dianzimoney' => 0, 'level' => $level, 'gouwujifen'=>$allgouwujifen,'gouwujuan'=>$allgouwujuan,'integral' => $allintegral, 'cash' => $allcashmoney, 'junction' => $jinfo['id'], 'region' => $jinfo['qu'], 'junctionLevel' => $jinfo['level']));
                        } else {
                            $relust1 = $member_table->save(array('id' => $userInfo['id'], 'dianzimoney' => 0,'level' => $level,'gouwujifen'=>$allgouwujifen,'gouwujuan'=>$allgouwujuan, 'integral' => $allintegral, 'cash' => $allcashmoney));
                        }
                        $relust2 = $bonus_table->add(array('uid' => $userInfo['id'], 'type' => 6, 'expend' => $userInfo['dianzimoney'], 'status' => 2, 'balance' => 0, 'message' => '会员升级', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '7'));
                       
                        $relust6 = $bonus_table->add(array('uid' => $userInfo['id'], 'type' => 22, 'expend' =>$xiaofeicash+$chongzhimoney, 'status' => 2, 'balance' => $allcashmoney, 'message' => '会员升级', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '7'));
                        if(in_array($level, $alllevel)){
                        $relust3 = $bonus_table->add(array('uid' => $userInfo['id'], 'type' => 10, 'income' => $memberlevelmoney['registermoney']*0.5, 'status' => 1, 'balance' => $allintegral, 'message' => '会员升级赠送积分', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '7'));
                        
                        $bonus_table->add(array('uid' => $userInfo['id'], 'type' => 25, 'income' => $memberlevelmoney['registermoney']*0.5, 'status' => 1, 'balance' => $allgouwujuan, 'message' => '会员升级赠送购物卷', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '7'));
                     
                        }
                        else{
                            //level =5 
                             $relust3 = $bonus_table->add(array('uid' => $userInfo['id'], 'type' => 10, 'income' => $gudingjifen, 'status' => 1, 'balance' => $allintegral, 'message' => '会员升级赠送积分', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '7'));
                             
                        }
                        $bonus_table->add(array('uid' => $userInfo['id'], 'type' => 24, 'income' => $memberlevelmoney['registermoney'], 'status' => 1, 'balance' => $allgouwujifen, 'message' => '会员升级赠送购物积分', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '7'));
                       
                        $relust4 = $upgrade_table->add(array('uid' => $userInfo['id'], 'money' => $memberlevelmoney['registermoney'], 'status' => '1', 'oldlevel' => $userInfo['level'], 'newlevel' => $level, 'oldname' => $oldnamelevel['title'], 'newname' => $memberlevelmoney['title'], 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'action' => '2', 'hid' => $_SESSION['userid']));
                        $relust5 = $achievement_table->add(array('uid' => $userInfo['id'], 'money' => $memberlevelmoney['registermoney'], 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '2'));
                   
                        
                    }
                   else if ($xiaofeidianzibi > $userInfo['dianzimoney'] && $xiaofeicash > $userInfo['cash']) {
                        $chongzhimoney = $xiaofeidianzibi - $userInfo['dianzimoney'];
                        $chongzhicash = $xiaofeicash - $userInfo['cash'];
                         
                        $bonus_table->add(array('uid' => $userInfo['id'], 'type' => 6, 'income' => $chongzhimoney, 'status' => 1, 'balance' => $xiaofeidianzibi, 'message' => '平台充值', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '1'));
                        
                        $bonus_table->add(array('uid' => $userInfo['id'], 'type' => 22, 'income' => $chongzhicash, 'status' => 1, 'balance' => $xiaofeicash, 'message' => '平台充值', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '1'));
                        
                        //充值明细
                        $recharge_table->add(array('uid' => $userInfo['id'], 'hid' => session('userid'), 'money' => $chongzhimoney, 'type' => 6, 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'status' => '1'));
                        $recharge_table->add(array('uid' => $userInfo['id'], 'hid' => session('userid'), 'money' => $chongzhicash, 'type' => 22, 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'status' => '1'));
                        
                        $allgouwujifen= $userInfo['gouwujifen'] + $memberlevelmoney['registermoney'];//购物积分
                        if(in_array($level, $alllevel))
                        {
                            $allintegral = $userInfo['integral'] + $memberlevelmoney['registermoney']*0.5;//赠送积分
                            $allgouwujuan = $userInfo['gouwujuan'] + $memberlevelmoney['registermoney']*0.5;//购物卷
                          
                        }
                        else{
                            //level==5
                            $gudingjifen=$data['zuankacanshu'];
                            $allintegral = $userInfo['integral'] +$gudingjifen;//赠送积分
                        }
                        
                        if ($userInfo['junction'] == '-1') {
                            $jinfo = $this->returnid($userInfo['recommend'], $userInfo['region']); //获取到节点人id 
                            $relust1 = $member_table->save(array('id' => $userInfo['id'], 'dianzimoney' => 0, 'level' => $level,'gouwujifen'=>$allgouwujifen,'gouwujuan'=>$allgouwujuan, 'integral' => $allintegral, 'cash' => 0, 'junction' => $jinfo['id'], 'region' => $jinfo['qu'], 'junctionLevel' => $jinfo['level']));
                        } else {
                            $relust1 = $member_table->save(array('id' => $userInfo['id'], 'dianzimoney' => 0, 'level' => $level, 'gouwujifen'=>$allgouwujifen,'gouwujuan'=>$allgouwujuan,'integral' => $allintegral, 'cash' => 0));
                        }
                        //if($userInfo['dianzimoney']!=0){
                        $relust2 = $bonus_table->add(array('uid' => $userInfo['id'], 'type' => 6, 'expend' => $xiaofeidianzibi, 'status' => 2, 'balance' => 0, 'message' => '会员升级', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '7'));
                        //}
                        $relust6 = $bonus_table->add(array('uid' => $userInfo['id'], 'type' => 22, 'expend' => $xiaofeicash, 'status' => 2, 'balance' => 0, 'message' => '会员升级', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '7'));
                        if(in_array($level, $alllevel)){
                        $relust3 = $bonus_table->add(array('uid' => $userInfo['id'], 'type' => 10, 'income' => $memberlevelmoney['registermoney']*0.5, 'status' => 1, 'balance' => $allintegral, 'message' => '会员升级赠送积分', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '7'));
                        $bonus_table->add(array('uid' => $userInfo['id'], 'type' => 25, 'income' => $memberlevelmoney['registermoney']*0.5, 'status' => 1, 'balance' => $allgouwujuan, 'message' => '会员升级赠送购物卷', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '7'));
                     
                        }
                        else{
                            //level =5 
                             $relust3 = $bonus_table->add(array('uid' => $userInfo['id'], 'type' => 10, 'income' => $gudingjifen, 'status' => 1, 'balance' => $allintegral, 'message' => '会员升级赠送积分', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '7'));
                             
                        }
                        $bonus_table->add(array('uid' => $userInfo['id'], 'type' => 24, 'income' => $memberlevelmoney['registermoney'], 'status' => 1, 'balance' => $allgouwujifen, 'message' => '会员升级赠送购物积分', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '7'));
                       
                        $relust4 = $upgrade_table->add(array('uid' => $userInfo['id'], 'money' => $memberlevelmoney['registermoney'], 'status' => '1', 'oldlevel' => $userInfo['level'], 'newlevel' => $level, 'oldname' => $oldnamelevel['title'], 'newname' => $memberlevelmoney['title'], 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'action' => '2', 'hid' => $_SESSION['userid']));
                        $relust5 = $achievement_table->add(array('uid' => $userInfo['id'], 'money' => $memberlevelmoney['registermoney'], 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '2'));
                    } else if ($xiaofeidianzibi > $userInfo['dianzimoney']) {
                        $chongzhimoney = $xiaofeidianzibi - $userInfo['dianzimoney'];
                        $allcashmoney = $userInfo['cash'] - $xiaofeicash;
                        $bonus_table->add(array('uid' => $userInfo['id'], 'type' => 6, 'income' => $chongzhimoney, 'status' => 1, 'balance' => $xiaofeidianzibi, 'message' => '平台充值', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '1'));
                        //充值明细
                        $recharge_table->add(array('uid' => $userInfo['id'], 'hid' => session('userid'), 'money' => $chongzhimoney, 'type' => 6, 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'status' => '1'));

                      $allgouwujifen= $userInfo['gouwujifen'] + $memberlevelmoney['registermoney'];//购物积分
                        if(in_array($level, $alllevel))
                        {
                            $allintegral = $userInfo['integral'] + $memberlevelmoney['registermoney']*0.5;//赠送积分
                            $allgouwujuan = $userInfo['gouwujuan'] + $memberlevelmoney['registermoney']*0.5;//购物卷
                          
                        }
                        else{
                            //level==5
                            $gudingjifen=$data['zuankacanshu'];
                            $allintegral = $userInfo['integral'] +$gudingjifen;//赠送积分
                        }

                        if ($userInfo['junction'] == '-1') {
                            $jinfo = $this->returnid($userInfo['recommend'], $userInfo['region']); //获取到节点人id 
                            $relust1 = $member_table->save(array('id' => $userInfo['id'], 'dianzimoney' => 0, 'level' => $level,'gouwujifen'=>$allgouwujifen,'gouwujuan'=>$allgouwujuan, 'integral' => $allintegral, 'cash' => $allcashmoney, 'junction' => $jinfo['id'], 'region' => $jinfo['qu'], 'junctionLevel' => $jinfo['level']));
                        } else {
                            $relust1 = $member_table->save(array('id' => $userInfo['id'], 'dianzimoney' => 0, 'level' => $level, 'gouwujifen'=>$allgouwujifen,'gouwujuan'=>$allgouwujuan,'integral' => $allintegral, 'cash' => $allcashmoney));
                        }
                         //if($userInfo['dianzimoney']!=0){
                        $relust2 = $bonus_table->add(array('uid' => $userInfo['id'], 'type' => 6, 'expend' => $xiaofeidianzibi, 'status' => 2, 'balance' => 0, 'message' => '会员升级', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '7'));
                         //}
                        $relust6 = $bonus_table->add(array('uid' => $userInfo['id'], 'type' => 22, 'expend' => $xiaofeicash, 'status' => 2, 'balance' => $allcashmoney, 'message' => '会员升级', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '7'));

                        if(in_array($level, $alllevel)){
                        $relust3 = $bonus_table->add(array('uid' => $userInfo['id'], 'type' => 10, 'income' => $memberlevelmoney['registermoney']*0.5, 'status' => 1, 'balance' => $allintegral, 'message' => '会员升级赠送积分', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '7'));
                        $bonus_table->add(array('uid' => $userInfo['id'], 'type' => 25, 'income' => $memberlevelmoney['registermoney']*0.5, 'status' => 1, 'balance' => $allgouwujuan, 'message' => '会员升级赠送购物卷', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '7'));
                     
                        }
                        else{
                            //level =5 
                             $relust3 = $bonus_table->add(array('uid' => $userInfo['id'], 'type' => 10, 'income' => $gudingjifen, 'status' => 1, 'balance' => $allintegral, 'message' => '会员升级赠送积分', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '7'));
                             
                        }
                        $bonus_table->add(array('uid' => $userInfo['id'], 'type' => 24, 'income' => $memberlevelmoney['registermoney'], 'status' => 1, 'balance' => $allgouwujifen, 'message' => '会员升级赠送购物积分', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '7'));
                       
                        
                        $relust4 = $upgrade_table->add(array('uid' => $userInfo['id'], 'money' => $memberlevelmoney['registermoney'], 'status' => '1', 'oldlevel' => $userInfo['level'], 'newlevel' => $level, 'oldname' => $oldnamelevel['title'], 'newname' => $memberlevelmoney['title'], 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'action' => '2', 'hid' => $_SESSION['userid']));
                        $relust5 = $achievement_table->add(array('uid' => $userInfo['id'], 'money' => $memberlevelmoney['registermoney'], 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '2'));
                    } else if ($xiaofeicash > $userInfo['cash']) {
                        $chongzhicash = $xiaofeicash - $userInfo['cash'];
                        $bonus_table->add(array('uid' => $userInfo['id'], 'type' => 22, 'income' => $chongzhicash, 'status' => 1, 'balance' => $xiaofeicash, 'message' => '平台充值', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '1'));
                        $recharge_table->add(array('uid' => $userInfo['id'], 'hid' => session('userid'), 'money' => $chongzhicash, 'type' => 22, 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'status' => '1'));
                        $alldianzimoney = $userInfo['dianzimoney'] - $xiaofeidianzibi;
                        
                        $allgouwujifen= $userInfo['gouwujifen'] + $memberlevelmoney['registermoney'];//购物积分
                        if(in_array($level, $alllevel))
                        {
                            $allintegral = $userInfo['integral'] + $memberlevelmoney['registermoney']*0.5;//赠送积分
                            $allgouwujuan = $userInfo['gouwujuan'] + $memberlevelmoney['registermoney']*0.5;//购物卷
                          
                        }
                        else{
                            //level==5
                            $gudingjifen=$data['zuankacanshu'];
                            $allintegral = $userInfo['integral'] +$gudingjifen;//赠送积分
                        }
                        
                        if ($userInfo['junction'] == '-1') {
                            $jinfo = $this->returnid($userInfo['recommend'], $userInfo['region']); //获取到节点人id 
                            $relust1 = $member_table->save(array('id' => $userInfo['id'], 'dianzimoney' => $alldianzimoney, 'level' => $level,'gouwujifen'=>$allgouwujifen,'gouwujuan'=>$allgouwujuan, 'integral' => $allintegral, 'cash' => 0, 'junction' => $jinfo['id'], 'region' => $jinfo['qu'], 'junctionLevel' => $jinfo['level']));
                        } else {
                            $relust1 = $member_table->save(array('id' => $userInfo['id'], 'dianzimoney' => $alldianzimoney, 'level' => $level,'gouwujifen'=>$allgouwujifen,'gouwujuan'=>$allgouwujuan, 'integral' => $allintegral, 'cash' => 0));
                        }
                        /// if($userInfo['dianzimoney']!=0){
                        $relust2 = $bonus_table->add(array('uid' => $userInfo['id'], 'type' => 6, 'expend' => $xiaofeidianzibi, 'status' => 2, 'balance' => $alldianzimoney, 'message' => '会员升级', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '7'));
                        /// }
                        $relust6 = $bonus_table->add(array('uid' => $userInfo['id'], 'type' => 22, 'expend' => $xiaofeicash, 'status' => 2, 'balance' => 0, 'message' => '会员升级', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '7'));

                       if(in_array($level, $alllevel)){
                        $relust3 = $bonus_table->add(array('uid' => $userInfo['id'], 'type' => 10, 'income' => $memberlevelmoney['registermoney']*0.5, 'status' => 1, 'balance' => $allintegral, 'message' => '会员升级赠送积分', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '7'));
                        $bonus_table->add(array('uid' => $userInfo['id'], 'type' => 25, 'income' => $memberlevelmoney['registermoney']*0.5, 'status' => 1, 'balance' => $allgouwujuan, 'message' => '会员升级赠送购物卷', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '7'));
                     
                        }
                        else{
                            //level =5 
                             $relust3 = $bonus_table->add(array('uid' => $userInfo['id'], 'type' => 10, 'income' => $gudingjifen, 'status' => 1, 'balance' => $allintegral, 'message' => '会员升级赠送积分', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '7'));
                             
                        }
                        $bonus_table->add(array('uid' => $userInfo['id'], 'type' => 24, 'income' => $memberlevelmoney['registermoney'], 'status' => 1, 'balance' => $allgouwujifen, 'message' => '会员升级赠送购物积分', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '7'));
                       
                        $relust4 = $upgrade_table->add(array('uid' => $userInfo['id'], 'money' => $memberlevelmoney['registermoney'], 'status' => '1', 'oldlevel' => $userInfo['level'], 'newlevel' => $level, 'oldname' => $oldnamelevel['title'], 'newname' => $memberlevelmoney['title'], 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'action' => '2', 'hid' => $_SESSION['userid']));
                        $relust5 = $achievement_table->add(array('uid' => $userInfo['id'], 'money' => $memberlevelmoney['registermoney'], 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '2'));
                    } else {
                        $alldianzimoney = $userInfo['dianzimoney'] - $xiaofeidianzibi;
                        $allcashmoney = $userInfo['cash'] - $xiaofeicash;
                        $allgouwujifen= $userInfo['gouwujifen'] + $memberlevelmoney['registermoney'];//购物积分
                        if(in_array($level, $alllevel))
                        {
                            $allintegral = $userInfo['integral'] + $memberlevelmoney['registermoney']*0.5;//赠送积分
                            $allgouwujuan = $userInfo['gouwujuan'] + $memberlevelmoney['registermoney']*0.5;//购物卷
                          
                        }
                        else{
                            //level==5
                            $gudingjifen=$data['zuankacanshu'];
                            $allintegral = $userInfo['integral'] +$gudingjifen;//赠送积分
                        }
                        
                        if ($userInfo['junction'] == '-1') {
                            $relust1 = $member_table->save(array('id' => $userInfo['id'], 'dianzimoney' => $alldianzimoney, 'level' => $level,'gouwujifen'=>$allgouwujifen,'gouwujuan'=>$allgouwujuan, 'integral' => $allintegral, 'cash' => $allcashmoney, 'junction' => $jinfo['id'], 'junctionLevel' => $jinfo['level']));
                        } else {
                            $relust1 = $member_table->save(array('id' => $userInfo['id'], 'dianzimoney' => $alldianzimoney, 'level' => $level,'gouwujifen'=>$allgouwujifen,'gouwujuan'=>$allgouwujuan, 'integral' => $allintegral, 'cash' => $allcashmoney));
                        }
                        /// if($userInfo['dianzimoney']!=0){
                        $relust2 = $bonus_table->add(array('uid' => $userInfo['id'], 'type' => 6, 'expend' => $xiaofeidianzibi, 'status' => 2, 'balance' => $alldianzimoney, 'message' => '会员升级', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '7'));
                       //  }
                        $relust6 = $bonus_table->add(array('uid' => $userInfo['id'], 'type' => 22, 'expend' => $xiaofeicash, 'status' => 2, 'balance' => $allcashmoney, 'message' => '会员升级', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '7'));
                        
                         if(in_array($level, $alllevel)){
                        $relust3 = $bonus_table->add(array('uid' => $userInfo['id'], 'type' => 10, 'income' => $memberlevelmoney['registermoney']*0.5, 'status' => 1, 'balance' => $allintegral, 'message' => '会员升级赠送积分', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '7'));
                        $bonus_table->add(array('uid' => $userInfo['id'], 'type' => 25, 'income' => $memberlevelmoney['registermoney']*0.5, 'status' => 1, 'balance' => $allgouwujuan, 'message' => '会员升级赠送购物卷', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '7'));
                     
                        }
                        else{
                            //level =5 
                             $relust3 = $bonus_table->add(array('uid' => $userInfo['id'], 'type' => 10, 'income' => $gudingjifen, 'status' => 1, 'balance' => $allintegral, 'message' => '会员升级赠送积分', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '7'));
                             
                        }
                        $bonus_table->add(array('uid' => $userInfo['id'], 'type' => 24, 'income' => $memberlevelmoney['registermoney'], 'status' => 1, 'balance' => $allgouwujifen, 'message' => '会员升级赠送购物积分', 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '7'));
                       
                        
                        $relust4 = $upgrade_table->add(array('uid' => $userInfo['id'], 'money' => $memberlevelmoney['registermoney'], 'status' => '1', 'oldlevel' => $userInfo['level'], 'newlevel' => $level, 'oldname' => $oldnamelevel['title'], 'newname' => $memberlevelmoney['title'], 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'action' => '2', 'hid' => $_SESSION['userid']));
                        $relust5 = $achievement_table->add(array('uid' => $userInfo['id'], 'money' => $memberlevelmoney['registermoney'], 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime, 'month' => $beginThismonth, 'week' => $week, 'action' => '2'));
                    }


                    if ($userInfo['junction'] == '-1') {
                        $this->addtotal($userInfo['id'], $username, $jinfo['id'], $memberlevelmoney['registermoney'], $jinfo['qu']); //每日市场业绩明细
                        $this->shichangbonus($userInfo['id'], $jinfo['id'], $memberlevelmoney['registermoney'], $jinfo['qu']); //市场总业绩
                        $this->groupbonus($userInfo['id'], $userInfo['username'], $userInfo['recommend'], $memberlevelmoney['registermoney']); //团队业绩每日明细
                    } else {
                        if ($userInfo['junction'] != '0') {
                            $this->addtotal($userInfo['id'], $username, $userInfo['junction'], $memberlevelmoney['registermoney'], $userInfo['region']); //每日市场业绩明细
                            $this->shichangbonus($userInfo['id'], $userInfo['junction'], $memberlevelmoney['registermoney'], $userInfo['region']); //市场总业绩
                            $this->groupbonus($userInfo['id'], $userInfo['username'], $userInfo['recommend'], $memberlevelmoney['registermoney']); //团队业绩每日明细
                        }
                    }
//                if ($userInfo['recommend'] != 0) {
//                  $this->jicha($userInfo['recommend'], $memberlevelmoney['registermoney'], 0); //晋级奖
//                }
                    if ($relust1 &&  $relust3 && $relust4 && $relust5 && $relust6) {
                        $member_table->commit();
                        ob_clean();
                        $json['status'] = 1;
                        $json['msg'] = '升级成功！';
                        echo json_encode($json);
                        exit;
                    } else {
                        $member_table->rollback();
                        ob_clean();
                        $json['status'] = 2;
                        $json['msg'] = '升级失败！';
                        echo json_encode($json);
                        exit;
                    }
             
            } else {
                //纯升级
                if ($userInfo['junction'] == '-1') {
                    $jinfo = $this->returnid($userInfo['recommend'], $userInfo['region']); //获取到节点人id 
                    $relust1 = $member_table->save(array('id' => $userInfo['id'], 'level' => $level, 'junction' => $jinfo['id'], 'region' => $jinfo['qu'], 'junctionLevel' => $jinfo['level']));
                } else {
                    $relust1 = $member_table->save(array('id' => $userInfo['id'], 'level' => $level));
                }
                $relust2 = $upgrade_table->add(array('uid' => $userInfo['id'], 'money' => 0, 'status' => 2, 'oldlevel' => $userInfo['level'], 'newlevel' => $level, 'oldname' => $oldnamelevel['title'], 'newname' => $memberlevelmoney['title'], 'create_date' => time(), 'date' => date('Y-m-d H:i:s'), 'addtime' => $todayTime));
                if ($relust1 && $relust2) {
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
        }

        $list = $memberlevel_table->where(array('status'=>'1'))->select();
        unset($list[0]);
        $this->assign('list', $list);
        $this->display();
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

    //会员升级记录
    public function listupgradelog() {
        $upgrade_table = M('upgrade');
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
            $info = $member_table->field('id')->where(array('username' => $_REQUEST['search_username']))->find();
            $map['uid'] = $info['id'];
            $search['search_username'] = $_REQUEST['search_username'];
        }

        $count = $upgrade_table->where($map)->count(); // 查询满足要求的总记录数
        $Page = new \Think\Page($count, 30); // 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show(); //
        $list = $upgrade_table->order('id desc')->where($map)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $lcount = count($list);
        for ($i = 0; $i < $lcount; $i++) {
            $userInfo = $member_table->field('username')->find($list[$i]['uid']);
            $list[$i]['username'] = $userInfo['username'];
        }
        $this->assign('page', $show);
        $this->assign('list', $list);
        $this->assign('count', $count);
        $this->assign('arr', $search);
        $this->display();
    }

}
