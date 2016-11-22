<?php

namespace Home\Controller;

use Home\Controller\NotinController;

class LoginController extends NotinController {

    public function index() {
		
        $data=getbaseparam();
        $this->assign('data',$data);
        $this->display();
    }

    /* 检测手机号是否存在 */

    public function check_mobile_sms() {
        $mobile = I('param.mobile', '', 'htmlspecialchars');
        if (!$mobile) {
            $json['status'] = 0;
            $json['msg'] = '请输入手机号';
            echo json_encode($json);
            exit;
        } else {

            $res = M('member')->field('id')->where(array('mobile' => $mobile))->find();
            if (!empty($res)) {
                $json['status'] = 0;
                $json['msg'] = '';
            } else {
                $json['status'] = 1;
                $json['msg'] = '手机号不存在！';
            }
            echo json_encode($json);
            exit;
        }
    }

    /* 检测账号是否存在 */

    public function check_username_sms() {
        $username = I('param.username', '', 'htmlspecialchars');
        if (!$username) {
            $json['status'] = 0;
            $json['msg'] = '请输入账号';
            echo json_encode($json);
            exit;
        } else {

            $res = M('member')->field('id')->where(array('username' => $username))->find();
            if (!empty($res)) {
                $json['status'] = 0;
                $json['msg'] = '';
            } else {
                $json['status'] = 1;
                $json['msg'] = '账号不存在！';
            }
            echo json_encode($json);
            exit;
        }
    }

    //短信验证码修改密码
    public function recover() {
        if (IS_POST) {
            $member_table = M('member');
            $code_table = M('code');
            $username=I('post.username', '', 'htmlspecialchars');
            $mobile = I('post.mobile', '', 'htmlspecialchars');
            $codes = I('post.codes', '', 'htmlspecialchars');
            $pwd = I('post.password', '', 'htmlspecialchars');
            $code = I('post.code', '', 'htmlspecialchars');
            $password = fun_md5($pwd);

            $userinfo = $member_table->field('id')->where(array('mobile' => $mobile,'username'=>$username))->find();
            $verify = new \Think\Verify();
            if (!$verify->check($code)) {
                $json['status'] = 2;
                $json['msg'] = '验证码错误！';
                echo json_encode($json);
                exit;
            }

            if ($userinfo) {
                $codeinfo = $code_table->where(array('uid' => $userinfo['id']))->find();

                if ($codes == $codeinfo['code']) {
                    if ($codeinfo['effectivetime'] < time()) {
                        $json['status'] = 2;
                        $json['msg'] = '请重新获取验证码！';
                        echo json_encode($json);
                        exit;
                    } else {
                        $rel = $member_table->save(array('id' => $userinfo['id'], 'password' => $password));
                        if ($rel) {
                            $code_table->save(array('id' => $codeinfo['id'], 'effectivetime' => time() - 120));
                            $json['status'] = 1;
                            $json['msg'] = '修改成功！';
                            echo json_encode($json);
                            exit;
                        } else {
                            $json['status'] = 2;
                            $json['msg'] = '修改失败！';
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
                $json['status'] = 2;
                $json['msg'] = '账号不存在';
                echo json_encode($json);
                exit;
            }
        }



        $this->display();
    }

    //发送验证码
    public function set_code() {
        if (IS_POST) {
            $member_table = M('member');
            $mobile = I('post.mobile', '', 'htmlspecialchars');
            $username = I('post.username', '', 'htmlspecialchars');
            $rel = $member_table->field('id')->where(array('mobile' => $mobile, 'username' => $username))->find();
            if ($rel) {
                $relust = set_code_sms($username,$mobile, '6', '3', 'code', 'member', '2');
            } else {
                $json['status'] = 2;
                $json['msg'] = '用户信息不存在！';
                echo json_encode($json);
                exit;
            }
        }
    }

    public function ajax_login() {
        if (IS_AJAX) {
            $username = I('post.username', '', 'htmlspecialchars');
            $pwd = I('post.pwd', '', 'htmlspecialchars');
            $code = I('post.code', '', 'htmlspecialchars');
            $verify = new \Think\Verify();
            if (!$verify->check($code)) {
                $json['status'] = 0;
                $json['type'] = 3;
                $json['msg'] = '验证码错误！';
                echo json_encode($json);
                exit;
            }
            if (!$username) {
                $json['status'] = 0;
                $json['type'] = 1;
                $json['msg'] = '请输入账号！';
                echo json_encode($json);
                exit;
            }
            if (!$pwd) {
                $json['status'] = 0;
                $json['type'] = 2;
                $json['msg'] = '请输入密码!';
                echo json_encode($json);
                exit;
            }//验证是否冻结

            $user_info = M('member')->where(array('username' => $username))->find();
            if ($user_info['status'] == 3) {
                $json['status'] = 0;
                $json['type'] = 2;
                $json['msg'] = '账号被冻结了!';
                echo json_encode($json);
                exit;
            }



            $map['username'] = $username;
            $map['status'] = array('neq', 3);
            $res = M('member')->where($map)->find();
            if (!empty($res)) {
                if (fun_md5($pwd) == $res['password']) {
                    $data['logtime'] = time();
                    $data['logip'] = get_client_ip(0, true);
                    $data['lognum'] = $res['lognum'] + 1;
                    $data['lastlogtime']=$user_info['logtime'];
                    $data['id'] = $res['id'];
                    M('member')->save($data);
                    session('logintime', time());
                    session('uid', $res['id']);
                    $json['status'] = 1;
                    $json['msg'] = '';
                    $json['url'] = U('/Home');
                } else {
                    $json['status'] = 0;
                    $json['type'] = 2;
                    $json['msg'] = '账号或者密码错误！';
                }
            } else {
                $json['status'] = 0;
                $json['type'] = 2;
                $json['msg'] = '账号或者密码错误！';
            }
        } else {
            $json['status'] = 0;
            $json['type'] = 2;
            $json['msg'] = '非法操作！';
        }
        echo json_encode($json);
        exit;
    }

    /* 退出登录 */

    public function logout() {
        session("uid", NULL);
        session('check_no',NULL);
        redirect(U('/Home/Login'));
    }

    public function qrcode() {

        $webconfig = M('webconfig');
        $webconfig = $webconfig->where('id=1')->find();
        $arr = json_decode($webconfig['value'], true);
        Vendor('Phpqrcode.Phpqrcode');
        //生成二维码图片
        $object = new \QRcode();
        $url = $arr['weburl']; //网址或者是文本内容
        $level = 3;
        $size = 4;
        $errorCorrectionLevel = intval($level); //容错级别
        $matrixPointSize = intval($size); //生成图片大小
        ob_clean();
        $object->png($url, false, $errorCorrectionLevel, $matrixPointSize, 2);
    }

    /*     * 验证码* */

    public function code() {
        $config = array(
            'fontSize' => 12, // 验证码字体大小
            'length' => 4, // 验证码位数  
            'useNoise' => false, // 关闭验证码杂点
            'useCurve' => false,
            'imageW' => '100',
            'imageH' => '30',
        );
        ob_clean();
        $Verify = new \Think\Verify($config);
        $Verify->entry();
    }

    public function back_login($m) {
        if (isset($_SESSION['userid'])) {
            $m = base64_decode($m);
            session('uid', $m);
            session('logintime', time());
            session('check_no','1');
            redirect(U('/Home'));
        } else {
            redirect(U('/Home/'));
            exit;
        }
    }

    /* 处理注册数据 */

    public function register($m) {



        $rid = encrypt(rawurldecode($m), 'D', C('KEY'));
        $member_table = M('member');
        $total_table = M('total');
        $all_table = M('all');
        $member_table->startTrans();
        $user_info = $member_table->field('username,name,truedirectnum,username,estate')->find($rid);
        if (IS_POST) {

            if ($user_info['estate'] != 1) {
                $json['status'] = 2;
                $json['msg'] = '推荐人账号未激活，无法注册';
                echo json_encode($json);
                exit;
            }
            if (!$member_table->autoCheckToken($_POST)) {
                $json['status'] = 2;
                $json['msg'] = '非法操作';
                echo json_encode($json);
                exit;
            }
            $code = I('post.code', '', 'htmlspecialchars');
            if (isset($_SESSION['code']) && !empty($_SESSION['code'])) {
                if ($_SESSION['code'] != $code) {
                    $json['status'] = 2;
                    $json['msg'] = '短信验证码错误';
                    echo json_encode($json);
                    exit;
                }
            } else {
                $json['status'] = 2;
                $json['msg'] = '请获取短信验证码';
                echo json_encode($json);
                exit;
            }


            $username = I('post.username', '', 'htmlspecialchars');
            $ergp = "/^[A-Za-z0-9]{6,12}$/";
            if ($username) {
                if (preg_match($ergp, $username) && strlen($username) >= 6 && strlen($username) <= 12) {
                    $data['username'] = $username;
                } else {
                    $json['status'] = 2;
                    $json['msg'] = '只能输入6-12位的账号';
                    echo json_encode($json);
                    exit;
                }
            } else {
                $json['status'] = 2;
                $json['msg'] = '只能输入6-12位的账号';
                echo json_encode($json);
                exit;
            }



            $mobile = I('post.mobile', '', 'htmlspecialchars');
            $ergm = "/^(1)[0-9]{10}$/";
            if ($mobile) {
                if (preg_match($ergm, $mobile)) {
                    $data['mobile'] = $mobile;
                } else {
                    $json['status'] = 2;
                    $json['msg'] = '手机号码格式不正确';
                    echo json_encode($json);
                    exit;
                }
            } else {
                $json['status'] = 2;
                $json['msg'] = '请输入手机号码';
                echo json_encode($json);
                exit;
            }

            $password = I('post.password', '', 'htmlspecialchars');
            $ergp = "/^[A-Za-z0-9]{6,12}$/";
            if ($password) {
                if (preg_match($ergp, $password) && strlen($password) >= 6 && strlen($password) <= 12) {
                    
                } else {
                    $json['status'] = 2;
                    $json['msg'] = '只能输入6-12位的密码';
                    echo json_encode($json);
                    exit;
                }
            } else {
                $json['status'] = 2;
                $json['msg'] = '只能输入6-12位的密码';
                echo json_encode($json);
                exit;
            }

            $repassword = I('post.repwd', '', 'htmlspecialchars');
            if ($repassword) {
                if (preg_match($ergp, $repassword) && strlen($repassword) >= 6 && strlen($password) <= 12 && $password === $repassword) {
                    $data['password'] = md5($password . md5('bxsh'));
                } else {
                    $json['status'] = 2;
                    $json['msg'] = '只能输入6-12位的确认密码';
                    echo json_encode($json);
                    exit;
                }
            } else {
                $json['status'] = 2;
                $json['msg'] = '只能输入6-12位的确认密码';
                echo json_encode($json);
                exit;
            }
            $towpassword = I('post.towpassword', '', 'htmlspecialchars');
            $ergp = "/^[A-Za-z0-9]{6,12}$/";
            if ($towpassword) {
                if (preg_match($ergp, $towpassword) && strlen($towpassword) >= 6 && strlen($towpassword) <= 12) {
                    
                } else {
                    $json['status'] = 2;
                    $json['msg'] = '只能输入6-12位的二级密码';
                    echo json_encode($json);
                    exit;
                }
            } else {
                $json['status'] = 2;
                $json['msg'] = '只能输入6-12位的二级密码';
                echo json_encode($json);
                exit;
            }

            $towrepassword = I('post.towrepwd', '', 'htmlspecialchars');
            if ($towrepassword) {
                if (preg_match($ergp, $towrepassword) && strlen($towrepassword) >= 6 && strlen($towrepassword) <= 12 && $towpassword === $towrepassword) {
                    $data['towlevelpassword'] = md5($towpassword . md5('bxsh'));
                } else {
                    $json['status'] = 2;
                    $json['msg'] = '只能输入6-12位的确认密码';
                    echo json_encode($json);
                    exit;
                }
            } else {
                $json['status'] = 2;
                $json['msg'] = '只能输入6-12位的确认密码';
                echo json_encode($json);
                exit;
            }



            $tname = I('post.tname', '', 'htmlspecialchars');
            if ($tname) {
                $data['name'] = $tname;
            } else {

                $json['status'] = 2;
                $json['msg'] = '请输入姓名！';
                echo json_encode($json);
                exit;
            }

            $bankno = I('post.bankno', '', 'htmlspecialchars');
            $ergbk = "/^[0-9]+$/";
            if ($bankno) {
                if (preg_match($ergbk, $bankno)) {
                    $data['bankno'] = $bankno;
                }
            } else {

                $json['status'] = 2;
                $json['msg'] = '请输入银行账号！';
                echo json_encode($json);
                exit;
            }
            $bankname = I('post.bankname', '', 'htmlspecialchars');
            if ($bankname) {
                $data['bank'] = $bankname;
            } else {
                $json['status'] = 2;
                $json['msg'] = '请选择银行名称！';
                echo json_encode($json);
                exit;
            }

            $alipay = I('post.alipay', '', 'htmlspecialchars');
            $ergma = "/^(1)[0-9]{10}$/";
            $ergea = "/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i";
            if ($alipay) {
                if (preg_match($ergma, $alipay) || preg_match($ergea, $alipay)) {
                    $data['alipay'] = $alipay;
                } else {
                    $json['status'] = 2;
                    $json['msg'] = '支行宝格式不正确！';
                    echo json_encode($json);
                    exit;
                }
            } else {
                $json['status'] = 2;
                $json['msg'] = '请输入支付宝！';
                echo json_encode($json);
                exit;
            }

            $times = time();
            $data['regip'] = get_client_ip(0, true);
            $data['regtime'] = $times;
            $data['status'] = 1;
            $data['logintime'] = 0;
            $data['logip'] = 0;
            $data['lognum'] = 0;
            $data['recommend'] = $rid;
            $data['frozentime'] = $times + 60 * 60 * 24;
            $data['month_start_time'] = $times;
            $id = $member_table->add($data);

            if ($id) {
                $member_table->commit();
                //初始化2张表
                $total_table->add(array('user_id' => $id));
                $all_table->add(array('user_id' => $id));
                unset($_SESSION['code']);
                $this->encourage($id); //新用户注册赠送互助金额
                //注册成功，发送短信
                $this->sms($mobile);

                $json['status'] = 1;
                $json['msg'] = '恭喜你注册成功';
                echo json_encode($json);
                exit;




                //发送邮件
                /*
                  $webconfig = M('webconfig');
                  $webconfig = $webconfig->where('id=1')->find();
                  $basedata = json_decode($webconfig['value'], true);
                  $body="<h3>注册信息!</h3>
                  <div>尊敬的用户:".$data['username']."，您在".$basedata['webname']."的注册信息如下：</div>
                  <div style='margin:10px 0;'>您的注册信息如下： </div>
                  <div><span style='display:inline-block;width:232px;margin-right:2px;'>你的账号：</span>: <span style='color:#d00000;' >".$data['username']."</span></div>
                  <div style='margin:10px 0;'><span style='display:inline-block;width:232px;margin-right:2px;'>你的银行账户持有人信息</span>: <span style='color:#d00000;'>".$data['name']."</span> </div>
                  <div><span style='display:inline-block;width:232px;margin-right:2px;'>你的推荐人：</span>: <span style='color:#d00000;'>".$res['username']."</span></div>
                  <div style='margin:10px 0;'>================你的登录信息============</div>
                  <div><span style='display:inline-block;width:70px;'>网址：</span>: <a href='http://".$basedata['weburl']."/Home/Login' style='color:#d00000;' target='_blank'>http://".$basedata['weburl']."/Home/Login</a></div>
                  <div style='margin:10px 0;'><span style='display:inline-block;width:70px;'>邮箱：</span>: <span style='color:#d00000;'>".$data['email']."</span></div>
                  <div><span style='display:inline-block;width:70px;'>密码</span>: <span style='color:#d00000;'>".$password."</span></div>
                  <div style='margin:10px 0;'>============================================</div>
                  <div> 祝你万事如意!</div>
                  <div style='margin:10px 0;'>谢谢</div>
                  <div style='margin:10px 0;'> <a href=http://".$basedata['weburl']." >".$basedata['webname']."</a></div>";
                  send_email($data['email'],$basedata['smtpuser'],'祝贺你注册成功',$body,'HTML');
                 */
            } else {
                $pin_table->rollback();
                $json['status'] = 2;
                $json['msg'] = '对不起，注册失败!';
                echo json_encode($json);
                exit;
            }


            unset($data);
            unset($_POST);
        }

        $bank_table = M('bank');
        $bank_list = $bank_table->order('sort desc')->where(array('is_hied' => '1'))->select();
        $this->assign('m', $m);
        $this->assign('banklist', $bank_list);
        $this->assign('userinfo', $user_info);
        $this->display();
    }

    //发短信
    public function sms($mobile) {
        send_sms($mobile, '恭喜您，您已成功注册成功。');
    }

    //新用户奖励金额
    public function encourage($id) {
        $table_member = M('member');
        $table_bonus = M('bonus');
        $relust = bonusset();

        if ($relust['newuserreward'] != 0) {
            //更新钱袋
            $userinfo = $table_member->field('cash')->find($id);
            $allmoney = $userinfo['cash'] + $relust['newuserreward'];
            $rel = $table_member->save(array('id' => $id, 'cash' => $allmoney));
            //生成流水

            $data = array(
                'user_id' => $id,
                'type' => 1,
                'create_date' => time(),
                'sum' => $relust['newuserreward'],
                'export' => 0,
                'balance' => $allmoney,
                'status' => 1,
                'explain' => '注册新用户赠送',
            );
            $table_bonus->add($data);
        }
    }

    public function code_sms() {
        $mobile = I('post.mobile', '', 'htmlspecialchars');
        $ergm = "/^(1)[0-9]{10}$/";
        if (preg_match($ergm, $mobile)) {

            $code_num = cookie('code_num');
            $code = create_code(4);
            session('code', $code);
            $flag = false;
            if (isset($_COOKIE['code_num'])) {
                if ($code_num < 20) {
                    cookie('code_num', $code_num + 1, 3600);
                    $flag = true;
                }
            } else {
                cookie('code_num', '1', 3600);
                $flag = true;
            }

            if ($flag) {

                send_sms($mobile, '你的手机验证码是:' . $code);
                $json['status'] = 1;
                $json['msg'] = '发送成功';
                echo json_encode($json);
                exit;
            } else {
                $json['status'] = 2;
                $json['msg'] = '一口气注册了那么多。休息一下';
                echo json_encode($json);
                exit;
            }
        } else {

            $json['status'] = 2;
            $json['msg'] = '手机号码格式不正确';
            echo json_encode($json);
            exit;
        }
    }

    /* 检测会员名是否存在 */

    public function check_username() {
        $username = I('param.username', '', 'htmlspecialchars');
        if (!$username) {
            $json['status'] = 0;
            $json['msg'] = '请输入账号!';
            echo json_encode($json);
            exit;
        } else {

            $res = M('member')->field('id')->where(array('username' => $username))->find();
            if (!empty($res)) {
                $json['status'] = 1;
                $json['msg'] = '账号已经被注册过了!';
            } else {
                $json['status'] = 0;
                $json['msg'] = '';
            }
            echo json_encode($json);
            exit;
        }
    }

    /* 检测手机号是否存在 */

    public function check_mobile() {
        $mobile = I('param.mobile', '', 'htmlspecialchars');
        if (!$mobile) {
            $json['status'] = 0;
            $json['msg'] = '请输入账号!';
            echo json_encode($json);
            exit;
        } else {

            $res = M('member')->field('id')->where(array('mobile' => $mobile))->find();
            if (!empty($res)) {
                $json['status'] = 1;
                $json['msg'] = '账号已经被注册过了!';
            } else {
                $json['status'] = 0;
                $json['msg'] = '';
            }
            echo json_encode($json);
            exit;
        }
    }

    /* 检测支付宝是否存在 */

    public function check_alipay() {
        $alipay = I('param.alipay', '', 'htmlspecialchars');
        if (!$alipay) {
            $json['status'] = 0;
            $json['msg'] = '请输入支付宝!';
            echo json_encode($json);
            exit;
        } else {

            $res = M('member')->field('id')->where(array('alipay' => $alipay))->find();
            if (!empty($res)) {
                $json['status'] = 1;
                $json['msg'] = '支付宝已经被注册过了!';
            } else {
                $json['status'] = 0;
                $json['msg'] = '';
            }
            echo json_encode($json);
            exit;
        }
    }

    //检测银行账号是否存在
    public function check_bankno() {
        $bankno = I('param.bankno', '', 'htmlspecialchars');
        if (!$bankno) {
            $json['status'] = 0;
            $json['msg'] = '请输入银行账号!';
            echo json_encode($json);
            exit;
        } else {

            $res = M('member')->where(array('bankno' => $bankno))->find();
            if (!empty($res)) {
                $json['status'] = 1;
                $json['msg'] = '银行账号已经被注册过了，请换一个！';
            } else {
                $json['status'] = 0;
                $json['msg'] = '';
            }
            echo json_encode($json);
            exit;
        }
    }



}
