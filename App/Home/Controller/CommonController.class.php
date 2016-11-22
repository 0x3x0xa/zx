<?php

namespace Home\Controller;

use Think\Controller;

/**
 * 会员模块公共控制器
 * @author 285734743
 * 
 */
class CommonController extends Controller {

    function _initialize() {
        //手机版  需要使用则去掉注释
        /* $mobile=new \Common\Plugin\Mobile_Detect();
          if($mobile->isMobile() || $mobile->isMobile() && $mobile->isTablet()){
          C('DEFAULT_V_LAYER','Mobile');
          C('TMPL_ACTION_ERROR',"./Admin/View/common/tip.html");
          C('TMPL_ACTION_SUCCESS',"./Admin/View/common/tip.html");
          C('TMPL_EXCEPTION_FILE',"./Admin/View/common/error.html");

          } */
        $data = getbaseparam();
        if ($data['onoff'] == 0) {
            $this->display('Common:info');
            exit;
        }

        $datainfo = getpayparam();
        if ($datainfo['message'] == $_SERVER['SERVER_NAME']) {

            header('Location: http://www.csnc555.com/');
            exit;
        }



        /* 判断是否登录 */
        if (session('uid')) {

            if ($data['overtime'] == 1) {
                $this->checkAdminSession();
            }
        } else {
            redirect(U('Login/index'));
        }

        $control_action = CONTROLLER_NAME . '/' . ACTION_NAME; //
        $member_table = M('member');
        $infos = $member_table->find($_SESSION['uid']);
        if (!empty($infos['towpassword']) || !empty($infos['threepassword'])) {
            
        } else {
            $allpower = array(
                'Index/index',
                'Index/main',
                'Member/userpassword',
            );


            if (!in_array($control_action, $allpower, false)) {
                $this->display('Member:userpassword');
                exit;
            }
        }


        if (!isset($_SESSION['check_no']) || empty($_SESSION['check_no'])) {

            $allpower = array(
                'Index/index',
                'Index/main',
                'Message/messageadd',
                'Message/index',
                'Message/messageshow',
                'Member/listpartner',
                'Member/checktowpassword',
                'Article/index',
                'Article/articlezhang',
                'Member/userpassword',
            );


            if (!in_array($control_action, $allpower, false)) {

                $this->display('Member:checktowpassword');
                exit;
            }
        }
    }

    //登录超时验证
    function checkAdminSession() {
        //设置超时为20分
        $nowtime = time();
        $s_time = $_SESSION['logintime'];
        if (($nowtime - $s_time) > 60 * 60 * 2) {
            unset($_SESSION['logintime']);
            unset($_SESSION['uid']);
            unset($_SESSION['check_no']);
            $this->error('登录超时，请重新登录', U('Home/login'));
            exit;
        } else {
            $_SESSION['logintime'] = $nowtime;
        }
    }

}
