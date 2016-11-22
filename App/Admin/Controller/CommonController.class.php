<?php

namespace Admin\Controller;

use Think\Controller;

/**
 * 会员模块公共控制器
 * @author 285734743
 * 
 */
class CommonController extends Controller {

    function _initialize() {


        $this->check_login();
        $this->verify();
        $this->reseterrorlogin();

        $webconfig = M('webconfig')->where('id=1')->find();
        $basedata = json_decode($webconfig['value'], true);

        $ip = get_client_ip(0, true);
        if (!empty($basedata['ip'])) {
            $relust = check_ip($basedata['ip'], $ip);
            if (!$relust) {
                $this->display('Common:error');
                die;
            }
        }
        if (session("userid") > 0) {
            $data=getbaseparam();
            if($data['overtime']==1)
            $this->checkAdminSession();
            $userinfo = $this->userinfo(session("userid")); //管理员信息
            if ($userinfo['id'] == 1) {
                $role['rolename'] = '超级管理员';
            } else {
                $role = M('role')->where(array('id' => session('groupid')))->find();
            }
            $this->assign('role', $role);

            $this->assign('config', $basedata);
            $this->assign('userinfo', $userinfo);
        }
    }

    //重置登录错误次数和时间
    function reseterrorlogin() {
        $admin_table = M('admin');
        $list = $admin_table->field('id,errorlognum,errorlogtime')->where(array('errorlognum' => array('neq', '0')))->select();
        $count = count($list);
        for ($i = 0; $i < $count; $i++) {
            if ($list[$i]['errorlogtime'] < time()) {
                $admin_table->save(array('id' => $list[$i]['id'], 'errorlognum' => '0', 'errorlogtime' => '0'));
            }
        }
    }

    //登录超时验证
    function checkAdminSession() {
        //设置超时为20分
        $nowtime = time();
        $s_time = $_SESSION['timeout'];
        if (($nowtime - $s_time) > 60 * 60 * 2) {
            unset($_SESSION['timeout']);
            unset($_SESSION['userid']);
            $this->error('登录超时，请重新登录', U('Index/login'));
            exit;
        } else {
            $_SESSION['timeout'] = $nowtime;
        }
    }

    final public function check_login() {

        if (CONTROLLER_NAME == 'Index' && in_array(ACTION_NAME, array('login', 'code'))) {
            return true;
        }

        if (!isset($_SESSION['userid']) || empty($_SESSION['userid'])) {
            $this->redirect('/Admin/Index/login');
        }
    }

    public function verify() {

        $control_action = CONTROLLER_NAME . '/' . ACTION_NAME;
        $role = M('role')->where(array('id' => session('groupid')))->find();
        $role = explode(',', $role['power_control_action']);
        $allower = array(
            'Index/welcome',
            'Index/code',
            'Index/index',
            'Index/login',
            'Index/logout',
            'Rbac/set_code',
            'Rbac/password',
            'check_input_username',
            'check_input_unique',
            'check_input_junction_exist',
            'check_input_recommend_exist',
        );
        $allpower = array_merge($allower, $role);
        if (in_array($control_action, $allpower, false) || $_SESSION['userid'] == 1) {

            return true;
        } else {


            exit('<div style="position: absolute;top:40%;text-align:center;width:95%;color:red;font-size: 24px;">权限不足，无法操作</div>');
        }
    }

    public function userinfo($uid) {
        $userinfo = M('admin')->find($uid);
        return $userinfo;
    }

}
