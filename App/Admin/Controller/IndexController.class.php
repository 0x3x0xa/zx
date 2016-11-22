<?php

namespace Admin\Controller;

use Admin\Controller\CommonController;

class IndexController extends CommonController {
    /*     * *
     *
     * 框架
     */

    public function index() {

        //左侧菜单

        $data['id'] = session('groupid');
        $role_row = M('role')->where($data)->find();
        if (session('userid') == 1) {
            $list1 = M('power')->order('sort ASC')->where(array('level' => 0))->select();
            $list2 = M('power')->where(array('level' => 1))->select();
        } else if (!empty($role_row['power_id'])) {
            $map['level'] = 0;
            $map['id'] = array('in', $role_row['power_id']);
            $list1 = M('power')->order('sort ASC')->where($map)->select();

            $info['level'] = 1;
            $info['id'] = array('in', $role_row['power_id']);
            $list2 = M('power')->where($info)->select();
        }
        $this->assign('list1', $list1);
        $this->assign('list2', $list2);

        $this->display();
    }

    public function welcome() {

        $systeminfo = getSystemInfo();
        $this->assign('systeminfo', $systeminfo);

        //获取各项统计
        //总人数
        $todayTime = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
        $yesterday = $todayTime - 60 * 60 * 24;
        $week = mktime(0, 0, 0, date('m'), date('d') - date('w'), date('Y')); //当周第一天
        $beginThismonth = mktime(0, 0, 0, date('m'), 1, date('Y'));  //当月第一天时间戳
        $member_table = M('member');
        //所有会员人头
        $allcount = $member_table->count();
        $allusercount = $member_table->where(array('status' => '1'))->count();
        $allusercount1 = $member_table->where(array('regtime' => array('egt', $todayTime)))->count();
        $allusercount2 = $member_table->where(array('regtime' => array('between', $yesterday . ',' . $todayTime)))->count();
        $allusercount7 = $member_table->where(array('regtime' => array('egt', $week)))->count();
        $allusercount31 = $member_table->where(array('regtime' => array('egt', $beginThismonth)))->count();



        //所有会员业绩
        $achievement_table = M('achievement');
        $allyeji = $achievement_table->sum('money');
        $alluseryeji = $allyeji;
        $alluseryeji1 = $achievement_table->where(array('create_date' => array('egt', $todayTime)))->sum('money');
        $alluseryeji2 = $achievement_table->where(array('create_date' => array('between', $yesterday . ',' . $todayTime)))->sum('money');
        $alluseryeji7 = $achievement_table->where(array('create_date' => array('egt', $week)))->sum('money');
        $alluseryeji31 = $achievement_table->where(array('create_date' => array('egt', $beginThismonth)))->sum('money');
        $allyeji = (empty($allyeji)) ? 0 : $allyeji;
        $alluseryeji = (empty($alluseryeji)) ? 0 : $alluseryeji;
        $alluseryeji1 = (empty($alluseryeji1)) ? 0 : $alluseryeji1;
        $alluseryeji2 = (empty($alluseryeji2)) ? 0 : $alluseryeji2;
        $alluseryeji7 = (empty($alluseryeji7)) ? 0 : $alluseryeji7;
        $alluseryeji31 = (empty($alluseryeji31)) ? 0 : $alluseryeji31;


        //所有拨出去的奖金
        $bonus_table = M('bonus');
        $allbonus = $bonus_table->where(array('action' => '3'))->sum('income');
        $alluserbonus = $allbonus;
        $alluserbonus1 = $bonus_table->where(array('action' => '3', 'create_date' => array('egt', $todayTime)))->sum('income');
        $alluserbonus2 = $bonus_table->where(array('action' => '3', 'create_date' => array('between', $yesterday . ',' . $todayTime)))->sum('income');
        $alluserbonus7 = $bonus_table->where(array('action' => '3', 'create_date' => array('egt', $week)))->sum('income');
        $alluserbonus31 = $bonus_table->where(array('action' => '3', 'create_date' => array('egt', $beginThismonth)))->sum('income');
        $allbonus = (empty($allbonus)) ? 0 : $allbonus;
        $alluserbonus = (empty($alluserbonus)) ? 0 : $alluserbonus;
        $alluserbonus1 = (empty($alluserbonus1)) ? 0 : $alluserbonus1;
        $alluserbonus2 = (empty($alluserbonus2)) ? 0 : $alluserbonus2;
        $alluserbonus7 = (empty($alluserbonus7)) ? 0 : $alluserbonus7;
        $alluserbonus31 = (empty($alluserbonus31)) ? 0 : $alluserbonus31;

        //所有会员的现金积分
        $allcash = $bonus_table->where(array('type' => '22', 'action' => '15'))->sum('income');
        $allusercash = $member_table->sum('cash');
        $allusercash1 = $bonus_table->where(array('type' => '22', 'action' => '15', 'create_date' => array('egt', $todayTime)))->sum('income');
        $allusercash2 = $bonus_table->where(array('type' => '22', 'action' => '15', 'create_date' => array('between', $yesterday . ',' . $todayTime)))->sum('income');
        $allusercash7 = $bonus_table->where(array('type' => '22', 'action' => '15', 'create_date' => array('egt', $week)))->sum('income');
        $allusercash31 = $bonus_table->where(array('type' => '22', 'action' => '15', 'create_date' => array('egt', $beginThismonth)))->sum('income');
        $allcash = (empty($allcash)) ? 0 : $allcash;
        $allusercash = (empty($allusercash)) ? 0 : $allusercash;
        $allusercash1 = (empty($allusercash1)) ? 0 : $allusercash1;
        $allusercash2 = (empty($allusercash2)) ? 0 : $allusercash2;
        $allusercash7 = (empty($allusercash7)) ? 0 : $allusercash7;
        $allusercash31 = (empty($allusercash31)) ? 0 : $allusercash31;


        //开发奖
        $allkaifa = $bonus_table->where(array('type' => '2', 'action' => '3'))->sum('income');
        $alluserkaifa = $member_table->sum('lingdaoBonus');
        $alluserkaifa1 = $bonus_table->where(array('type' => '2', 'action' => '3', 'create_date' => array('egt', $todayTime)))->sum('income');
        $alluserkaifa2 = $bonus_table->where(array('type' => '2', 'action' => '3', 'create_date' => array('between', $yesterday . ',' . $todayTime)))->sum('income');
        $alluserkaifa7 = $bonus_table->where(array('type' => '2', 'action' => '3', 'create_date' => array('egt', $week)))->sum('income');
        $alluserkaifa31 = $bonus_table->where(array('type' => '2', 'action' => '3', 'create_date' => array('egt', $beginThismonth)))->sum('income');
        $allkaifa = (empty($allkaifa)) ? 0 : $allkaifa;
        $alluserkaifa = (empty($alluserkaifa)) ? 0 : $alluserkaifa;
        $alluserkaifa1 = (empty($alluserkaifa1)) ? 0 : $alluserkaifa1;
        $alluserkaifa2 = (empty($alluserkaifa2)) ? 0 : $alluserkaifa2;
        $alluserkaifa7 = (empty($alluserkaifa7)) ? 0 : $alluserkaifa7;
        $alluserkaifa31 = (empty($alluserkaifa31)) ? 0 : $alluserkaifa31;


        //管理奖
        $allguanli = $bonus_table->where(array('type' => '3', 'action' => '3'))->sum('income');
        $alluserguanli = $member_table->sum('guanliBonus');
        $alluserguanli1 = $bonus_table->where(array('type' => '3', 'action' => '3', 'create_date' => array('egt', $todayTime)))->sum('income');
        $alluserguanli2 = $bonus_table->where(array('type' => '3', 'action' => '3', 'create_date' => array('between', $yesterday . ',' . $todayTime)))->sum('income');
        $alluserguanli7 = $bonus_table->where(array('type' => '3', 'action' => '3', 'create_date' => array('egt', $week)))->sum('income');
        $alluserguanli31 = $bonus_table->where(array('type' => '3', 'action' => '3', 'create_date' => array('egt', $beginThismonth)))->sum('income');
        $allguanli = (empty($allguanli)) ? 0 : $allguanli;
        $alluserguanli = (empty($alluserguanli)) ? 0 : $alluserguanli;
        $alluserguanli1 = (empty($alluserguanli1)) ? 0 : $alluserguanli1;
        $alluserguanli2 = (empty($alluserguanli2)) ? 0 : $alluserguanli2;
        $alluserguanli7 = (empty($alluserguanli7)) ? 0 : $alluserguanli7;
        $alluserguanli31 = (empty($alluserguanli31)) ? 0 : $alluserguanli31;

        //代数奖（领导奖）
        $alljinji = $bonus_table->where(array('type' => '23', 'action' => '3'))->sum('income');
        $alluserjinji = $member_table->sum('daishubonus');
        $alluserjinji1 = $bonus_table->where(array('type' => '23', 'action' => '3', 'create_date' => array('egt', $todayTime)))->sum('income');
        $alluserjinji2 = $bonus_table->where(array('type' => '23', 'action' => '3', 'create_date' => array('between', $yesterday . ',' . $todayTime)))->sum('income');
        $alluserjinji7 = $bonus_table->where(array('type' => '23', 'action' => '3', 'create_date' => array('egt', $week)))->sum('income');
        $alluserjinji31 = $bonus_table->where(array('type' => '23', 'action' => '3', 'create_date' => array('egt', $beginThismonth)))->sum('income');
        $alljinji = (empty($alljinji)) ? 0 : $alljinji;
        $alluserjinji = (empty($alluserjinji)) ? 0 : $alluserjinji;
        $alluserjinji1 = (empty($alluserjinji1)) ? 0 : $alluserjinji1;
        $alluserjinji2 = (empty($alluserjinji2)) ? 0 : $alluserjinji2;
        $alluserjinji7 = (empty($alluserjinji7)) ? 0 : $alluserjinji7;
        $alluserjinji31 = (empty($alluserjinji31)) ? 0 : $alluserjinji31;


        //全球分红
        $allquanqiu = $bonus_table->where(array('type' => '5', 'action' => '3'))->sum('income');
        $alluserquanqiu = $member_table->sum('quanqiufenhongBonus');
        $alluserquanqiu1 = $bonus_table->where(array('type' => '5', 'action' => '3', 'create_date' => array('egt', $todayTime)))->sum('income');
        $alluserquanqiu2 = $bonus_table->where(array('type' => '5', 'action' => '3', 'create_date' => array('between', $yesterday . ',' . $todayTime)))->sum('income');
        $alluserquanqiu7 = $bonus_table->where(array('type' => '5', 'action' => '3', 'create_date' => array('egt', $week)))->sum('income');
        $alluserquanqiu31 = $bonus_table->where(array('type' => '5', 'action' => '3', 'create_date' => array('egt', $beginThismonth)))->sum('income');
        $allquanqiu = (empty($allquanqiu)) ? 0 : $allquanqiu;
        $alluserquanqiu = (empty($alluserquanqiu)) ? 0 : $alluserquanqiu;
        $alluserquanqiu1 = (empty($alluserquanqiu1)) ? 0 : $alluserquanqiu1;
        $alluserquanqiu2 = (empty($alluserquanqiu2)) ? 0 : $alluserquanqiu2;
        $alluserquanqiu7 = (empty($alluserquanqiu7)) ? 0 : $alluserquanqiu7;
        $alluserquanqiu31 = (empty($alluserquanqiu31)) ? 0 : $alluserquanqiu31;

        //月薪奖
        $allyuexin = $bonus_table->where(array('type' => '17', 'action' => '3'))->sum('income');
        $alluseryuexin = $member_table->sum('quanqiufenhongBonus');
        $alluseryuexin1 = $bonus_table->where(array('type' => '17', 'action' => '3', 'create_date' => array('egt', $todayTime)))->sum('income');
        $alluseryuexin2 = $bonus_table->where(array('type' => '17', 'action' => '3', 'create_date' => array('between', $yesterday . ',' . $todayTime)))->sum('income');
        $alluseryuexin7 = $bonus_table->where(array('type' => '17', 'action' => '3', 'create_date' => array('egt', $week)))->sum('income');
        $alluseryuexin31 = $bonus_table->where(array('type' => '17', 'action' => '3', 'create_date' => array('egt', $beginThismonth)))->sum('income');
        $allyuexin = (empty($allyuexin)) ? 0 : $allyuexin;
        $alluseryuexin = (empty($alluseryuexin)) ? 0 : $alluseryuexin;
        $alluseryuexin1 = (empty($alluseryuexin1)) ? 0 : $alluseryuexin1;
        $alluseryuexin2 = (empty($alluseryuexin2)) ? 0 : $alluseryuexin2;
        $alluseryuexin7 = (empty($alluseryuexin7)) ? 0 : $alluseryuexin7;
        $alluseryuexin31 = (empty($alluseryuexin31)) ? 0 : $alluseryuexin31;

        //溢价积分
        $allzengzhi = $bonus_table->where(array('type' => '13', 'action' => '3'))->sum('income');
        $alluserzengzhi = $member_table->sum('zengzhibonus');
        $alluserzengzhi1 = $bonus_table->where(array('type' => '13', 'action' => '3', 'create_date' => array('egt', $todayTime)))->sum('income');
        $alluserzengzhi2 = $bonus_table->where(array('type' => '13', 'action' => '3', 'create_date' => array('between', $yesterday . ',' . $todayTime)))->sum('income');
        $alluserzengzhi7 = $bonus_table->where(array('type' => '13', 'action' => '3', 'create_date' => array('egt', $week)))->sum('income');
        $alluserzengzhi31 = $bonus_table->where(array('type' => '13', 'action' => '3', 'create_date' => array('egt', $beginThismonth)))->sum('income');
        $allzengzhi = (empty($allzengzhi)) ? 0 : $allzengzhi;
        $alluserzengzhi = (empty($alluserzengzhi)) ? 0 : $alluserzengzhi;
        $alluserzengzhi1 = (empty($alluserzengzhi1)) ? 0 : $alluserzengzhi1;
        $alluserzengzhi2 = (empty($alluserzengzhi2)) ? 0 : $alluserzengzhi2;
        $alluserzengzhi7 = (empty($alluserzengzhi7)) ? 0 : $alluserzengzhi7;
        $alluserzengzhi31 = (empty($alluserzengzhi31)) ? 0 : $alluserzengzhi31;

        //分红积分
        $allfenhong = $bonus_table->where(array('type' => '14', 'action' => '3'))->sum('income');
        $alluserfenhong = $member_table->sum('fenhongbonus');
        $alluserfenhong1 = $bonus_table->where(array('type' => '14', 'action' => '3', 'create_date' => array('egt', $todayTime)))->sum('income');
        $alluserfenhong2 = $bonus_table->where(array('type' => '14', 'action' => '3', 'create_date' => array('between', $yesterday . ',' . $todayTime)))->sum('income');
        $alluserfenhong7 = $bonus_table->where(array('type' => '14', 'action' => '3', 'create_date' => array('egt', $week)))->sum('income');
        $alluserfenhong31 = $bonus_table->where(array('type' => '14', 'action' => '3', 'create_date' => array('egt', $beginThismonth)))->sum('income');
        $allfenhong = (empty($allfenhong)) ? 0 : $allfenhong;
        $alluserfenhong = (empty($alluserfenhong)) ? 0 : $alluserfenhong;
        $alluserfenhong1 = (empty($alluserfenhong1)) ? 0 : $alluserfenhong1;
        $alluserfenhong2 = (empty($alluserfenhong2)) ? 0 : $alluserfenhong2;
        $alluserfenhong7 = (empty($alluserfenhong7)) ? 0 : $alluserfenhong7;
        $alluserfenhong31 = (empty($alluserfenhong31)) ? 0 : $alluserfenhong31;


        //动态钱包
        $alldongtaibonus = $member_table->sum('allbonus');
        $alldongtaibonus = (empty($alldongtaibonus)) ? 0 : $alldongtaibonus;
        //静态钱包
        $alljingtaibonus = $member_table->sum('alljingtaibonus');
        $alljingtaibonus = (empty($alljingtaibonus)) ? 0 : $alljingtaibonus;
        
        $data1 = array($allusercount, $allusercount1, $allusercount2, $allusercount7, $allusercount31, $allcount);
        $data2 = array($alluseryeji, $alluseryeji1, $alluseryeji2, $alluseryeji7, $alluseryeji31, $allyeji);
        $data3 = array($alluserbonus, $alluserbonus1, $alluserbonus2, $alluserbonus7, $alluserbonus31, $allbonus);
        $data4 = array($allusercash, $allusercash1, $allusercash2, $allusercash7, $allusercash31, $allcash);
        $data5 = array($alluserkaifa, $alluserkaifa1, $alluserkaifa2, $alluserkaifa7, $alluserkaifa31, $allkaifa);
        $data6 = array($alluserguanli, $alluserguanli1, $alluserguanli2, $alluserguanli7, $alluserguanli31, $allguanli);
        $data7 = array($alluserjinji, $alluserjinji1, $alluserjinji2, $alluserjinji7, $alluserjinji31, $alljinji);
        $data8 = array($alluserquanqiu, $alluserquanqiu1, $alluserquanqiu2, $alluserquanqiu7, $alluserquanqiu31, $allquanqiu);
        $data9 = array($alluseryuexin, $alluseryuexin1, $alluseryuexin2, $alluseryuexin7, $alluseryuexin31, $allyuexin);
        $data10 = array($alluserzengzhi, $alluserzengzhi1, $alluserzengzhi2, $alluserzengzhi7, $alluserzengzhi31, $allzengzhi);
        $data11 = array($alluserfenhong, $alluserfenhong1, $alluserfenhong2, $alluserfenhong7, $alluserfenhong31, $allfenhong);
        $data12 = array($alldongtaibonus, $alljingtaibonus);
        $this->assign('data1', $data1);
        $this->assign('data2', $data2);
        $this->assign('data3', $data3);
        $this->assign('data4', $data4);
        $this->assign('data5', $data5);
        $this->assign('data6', $data6);
        $this->assign('data7', $data7);
        $this->assign('data8', $data8);
        $this->assign('data9', $data9);
        $this->assign('data10', $data10);
        $this->assign('data11', $data11);
        $this->assign('data12', $data12);



        $this->display();
    }

    /**
     * 用户登录
     */
    public function login() {
        if (session("userid") > 0) {
            $this->redirect('index');
            exit;
        }
        if (IS_AJAX) {

            $data['username'] = I('post.username', '', 'trim') ? I('post.username', '', 'trim') : $this->error('账号不能为空', HTTP_REFERER);
            $data['password'] = I('post.password', '', 'trim') ? md5(I('post.password', '', 'trim') . md5('bxsh')) : $this->error('密码不能为空', HTTP_REFERER);
            $code = I('post.code', '', 'trim') ? I('post.code', '', 'trim') : $this->error('验证码不能为空', HTTP_REFERER);
            $verify = new \Think\Verify();
            $rel = M('admin')->where(array('username' => $data['username']))->find();
            $webconfig = M('webconfig')->where('id=1')->find();
            $basedata = json_decode($webconfig['value'], true);
            if ($verify->check($code)) {

                $admin = M('admin')->field('id,lognum,status,groupid')->where($data)->find();
                if (!empty($admin)) {

                    if ($rel['errorlognum'] < $basedata['num']) {
                        if ($admin['status'] == 1) {

                            $data['id'] = $admin['id'];
                            $data['lognum'] = $admin['lognum'] + 1;
                            //$data['logip']=get_client_ip(0, true);
                            $data['errorlognum'] = 0;
                            $data['errorlogtime'] = 0;
                            $rel = M('admin')->save($data);
                            if ($rel) {
                                session('userid', $admin['id']);
                                session('groupid', $admin['groupid']);
                                session('timeout', time());
                                writeAdminLog('登录成功', 1);
                                $json['status'] = 1;
                                $json['url'] = U('index');
                                $json['msg'] = '';
                                echo json_encode($json);
                                exit;
                            }
                        } else {
                            $json['type'] = 3;
                            $json['msg'] = '账号被冻结了';
                            echo json_encode($json);
                            exit;
                        }
                    } else {
                        $json['type'] = 3;
                        $json['msg'] = '登录错误超过最大次数';
                        echo json_encode($json);
                        exit;
                    }
                } else {
                    if ($rel) {
                        if ($rel['errorlognum'] < $basedata['num']) {
                            writeAdminLog($data['username'] . '登录失败', 1);
                            M('admin')->save(array('id' => $rel['id'], 'errorlognum' => $rel['errorlognum'] + 1, 'errorlogtime' => time() + 60 * 60 * 1));
                        } else {
                            $json['type'] = 3;
                            $json['msg'] = '登录错误超过最大次数';
                            echo json_encode($json);
                            exit;
                        }
                    }

                    $json['type'] = 1;
                    $json['msg'] = '账号或密码不对';
                    echo json_encode($json);
                    exit;
                }
            } else {

                $json['type'] = 2;
                $json['msg'] = '验证码错误';
                echo json_encode($json);
                exit;
            }
        } else {

            $this->assign('config', $basedata);
            $this->display();
        }
    }

    /*     * 验证码* */

    public function code() {
        $config = array(
            'fontSize' => 30, // 验证码字体大小
            'length' => 4, // 验证码位数  
            'useNoise' => true, // 关闭验证码杂点
            'useCurve' => false,
        );
        ob_clean();
        $Verify = new \Think\Verify($config);
        $Verify->entry();
    }

    /**
     * 退出登录
     */
    public function logout() {

        $this->add_ip_lasttime(session('userid'));
        $vo = array("userid", "roleid");
        foreach ($vo as $v) {
            session("$v", NULL);
        }
        redirect(U('Admin/Index/login'));
    }

    //更新退出的时间和ip
    public function add_ip_lasttime($id) {
        $data['id'] = $id;
        $data['logip'] = get_client_ip();
        $data['lasttime'] = time();
        M('admin')->save($data);
    }

}
