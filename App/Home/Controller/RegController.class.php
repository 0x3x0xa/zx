<?php

namespace Home\Controller;

use Home\Controller\NotinController;

class RegController extends CommonController {

    public function index() {
        $uid = session('uid');
        $table_member = M('member');
        $username = $table_member->field('username')->find($uid);
        $bank_table = M('bank');
        $bank_list = $bank_table->order('sort desc')->where(array('is_hied' => '1'))->select();
        $this->assign('banklist', $bank_list);
        $this->assign('username', $username['username']);

        $this->display();
    }

    /* 处理注册数据 */

    public function register() {



        if (IS_POST) {
            $uid = session('uid');
            $member_table = M('member');
            $total_table = M('total');
            $all_table = M('all');
            $member_table->startTrans();

            $user_info = $member_table->field('truedirectnum,username,estate')->find($uid);
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
            /* $code = I('post.code', '', 'htmlspecialchars');
              if(isset($_SESSION['code'])&&!empty($_SESSION['code']))
              {
              if($_SESSION['code']!=$code)
              {
              $json['status'] = 2;
              $json['msg'] = '短信验证码错误';
              echo json_encode($json);
              exit;
              }
              }
              else
              {
              $json['status'] = 2;
              $json['msg'] = '请获取短信验证码';
              echo json_encode($json);
              exit;
              } */


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
            $data['recommend'] = $uid;
            $data['frozentime'] = $times + 60 * 60 * 24; //新用户冻结的时间
            $data['month_start_time'] = $times; //月结的开始时间
            $id = $member_table->add($data);

            if ($id) {
                $member_table->commit();
                //初始化2张表
                $total_table->add(array('user_id' => $id));
                $all_table->add(array('user_id' => $id));


                unset($_SESSION['code']);
                $this->encourage($id); //新用户注册赠送互助金额
                //注册成功，发送短信
                //$this->sms($mobile);

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

    public function code() {
        $mobile = I('post.mobile', '', 'htmlspecialchars');
        $ergm = "/^(1)[0-9]{10}$/";
        if (preg_match($ergm, $mobile)) {
            cookie('code_num', -3);
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

}
