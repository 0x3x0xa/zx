<?php

namespace Admin\Controller;

use Admin\Controller\CommonController;

class WebconfigController extends CommonController {
    /*     * *
     *
     * 系统设置
     */

    public function index() {

        $webconfig = M('webconfig');
        if (IS_POST) {


            $arr = array(
                'id' => '1',
                'value' => json_encode($_POST),
            );

            $rel = $webconfig->save($arr);
            if ($rel) {
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
        $value = $webconfig->find(1);
        $data= json_decode($value['value'], true);
        $this->assign('config', $data);
        $this->display();
    }

    public function banklist() {
        $bank_table = M('bank');
        $bank_list = $bank_table->order('sort desc')->select();
        $count = count($bank_list);
        $this->assign('count', $count);
        $this->assign('banklist', $bank_list);
        $this->display();
    }

    public function bank_start() {
        $bank_table = M('bank');
        $relsult = $bank_table->where(array('id' => I('get.id')))->find();
        if ($relsult['is_hied'] == 2) {
            $data['id'] = I('get.id');
            $data['is_hied'] = 1;
            $rel = $bank_table->save($data);
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

    public function bank_stop() {
        $bank_table = M('bank');
        $id = I('get.id');
        $result = $bank_table->where(array('id' => $id))->find();
        if ($result['is_hied'] == 1) {
            $data['id'] = $id;
            $data['is_hied'] = 2;
            $rel = $bank_table->save($data);
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

    public function bankadd() {
        if (isset($_POST['bankname']) && !empty($_POST['bankname'])) {
            $bank_table = M('bank');
            $bankname = I('post.bankname');
            $banknum = I('post.banknum');
            $sort = I('post.sort');
            $relust = $bank_table->add(array('bankname' => $bankname, 'sort' => $sort,'banknum'=>$banknum));
            if ($relust) {
                $json['status'] = 1;
                $json['msg'] = '添加成功';
                echo json_encode($json);
                exit;
            } else {
                $json['status'] = 2;
                $json['msg'] = '添加失败';
                echo json_encode($json);
                exit;
            }
        } else {
            $this->display();
        }
    }

    public function bankedit() {
        $bank_table = M('bank');
        if (isset($_POST['id']) && !empty($_POST['id'])) {

            $id = I('post.id');
            $bankname = I('post.bankname');
            $banknum= I('post.banknum');
            $sort = I('post.sort');
            $relust = $bank_table->save(array('id' => $id, 'bankname' => $bankname, 'sort' => $sort,'banknum'=>$banknum));
            if ($relust) {
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


            $id = I('get.id');
            $bankinfo = $bank_table->find($id);
            $this->assign('bankinfo', $bankinfo);
            $this->assign('id', $id);
            $this->display();
        }
    }

    public function bankdel() {

        if (isset($_GET['id']) && !empty($_GET['id'])) {
            $bank_table = M('bank');
            $id = $_GET['id'];
            $relust = $bank_table->delete($id);
            if ($relust) {
                $json['status'] = 1;
                $json['msg'] = '删除成功';
                echo json_encode($json);
                exit;
            } else {
                $json['status'] = 2;
                $json['msg'] = '删除失败';
                echo json_encode($json);
                exit;
            }
        }
    }

    public function express() {
        if (!empty($_REQUEST['search_title'])) {
            $map['title'] = $_REQUEST['search_title'];
            $search['search_title'] = $_REQUEST['search_title'];
        }
        $express_table = M('express');
        $count = $express_table->where($map)->count(); // 查询满足要求的总记录数
        $Page = new \Think\Page($count, 30); // 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show(); //
        $list = $express_table->where($map)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('count', $count);
        $this->assign('list', $list);
        $this->assign('count', $count);
        $this->display();
    }

    public function expressadd() {
        if (isset($_POST['title']) && !empty($_POST['expressNum'])) {
            $express_table = M('express');
            $_POST['hid'] = session('userid');
            $_POST['create_date'] = time();
            $relust = $express_table->add($_POST);
            if ($relust) {
                $json['status'] = 1;
                $json['msg'] = '添加成功';
                echo json_encode($json);
                exit;
            } else {
                $json['status'] = 2;
                $json['msg'] = '添加失败';
                echo json_encode($json);
                exit;
            }
        } else {
            $this->display();
        }
    }

    public function expressdel() {

        if (isset($_GET['id']) && !empty($_GET['id'])) {
            $express_table = M('express');
            $id = $_GET['id'];
            $relust = $express_table->delete($id);
            if ($relust) {
                $json['status'] = 1;
                $json['msg'] = '删除成功';
                echo json_encode($json);
                exit;
            } else {
                $json['status'] = 2;
                $json['msg'] = '删除失败';
                echo json_encode($json);
                exit;
            }
        }
    }

    public function expressedit() {
        $express_table = M('express');
        if (isset($_POST['id']) && !empty($_POST['id'])) {

            $_POST['hid'] = session('userid');
            $_POST['create_date'] = time();
            $relust = $express_table->save($_POST);
            if ($relust) {
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


            $id = I('get.id');
            $info = $express_table->find($id);
            $this->assign('info', $info);
            $this->assign('id', $id);
            $this->display();
        }
    }

    public function banklists() {
        $bank_table = M('banks');
        $bank_list = $bank_table->select();
        $count = count($bank_list);
        $this->assign('count', $count);
        $this->assign('banklist', $bank_list);
        $this->display();
    }

    public function banks_start() {
        $bank_table = M('banks');
        $relsult = $bank_table->where(array('id' => I('get.id')))->find();
        if ($relsult['is_hied'] == 2) {
            $data['id'] = I('get.id');
            $data['is_hied'] = 1;
            $rel = $bank_table->save($data);
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

    public function banks_stop() {
        $bank_table = M('banks');
        $id = I('get.id');
        $result = $bank_table->where(array('id' => $id))->find();
        if ($result['is_hied'] == 1) {
            $data['id'] = $id;
            $data['is_hied'] = 2;
            $rel = $bank_table->save($data);
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

    public function bankadds() {
        if (isset($_POST['bankname']) && !empty($_POST['bankname'])) {
            $bank_table = M('banks');
            $bankname = I('post.bankname');
            $bankno = I('post.bankno');
            $name=I('post.name');
            $relust = $bank_table->add(array('bankname' => $bankname, 'bankno' => $bankno,'name'=>$name,'create_date'=>time(),'hid'=>  session('userid')));
            if ($relust) {
                $json['status'] = 1;
                $json['msg'] = '添加成功';
                echo json_encode($json);
                exit;
            } else {
                $json['status'] = 2;
                $json['msg'] = '添加失败';
                echo json_encode($json);
                exit;
            }
        } else {
            $this->display();
        }
    }

    public function bankedits() {
        $bank_table = M('banks');
        if (isset($_POST['id']) && !empty($_POST['id'])) {

            $id = I('post.id');
            $bankname = I('post.bankname');
            $bankno = I('post.bankno');
            $name= I('post.name');
            $relust = $bank_table->save(array('id' => $id, 'bankname' => $bankname, 'bankno' => $bankno,'name'=>$name));
            if ($relust) {
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


            $id = I('get.id');
            $bankinfo = $bank_table->find($id);
            $this->assign('bankinfo', $bankinfo);
            $this->assign('id', $id);
            $this->display();
        }
    }

    public function bankdels() {

        if (isset($_GET['id']) && !empty($_GET['id'])) {
            $bank_table = M('banks');
            $id = $_GET['id'];
            $relust = $bank_table->delete($id);
            if ($relust) {
                $json['status'] = 1;
                $json['msg'] = '删除成功';
                echo json_encode($json);
                exit;
            } else {
                $json['status'] = 2;
                $json['msg'] = '删除失败';
                echo json_encode($json);
                exit;
            }
        }
    }
    
    public function pay_start() {
        $paymentparameter_table = M('paymentparameter');
        $relsult = $paymentparameter_table->where(array('id' => I('get.id')))->find();
        if ($relsult['status'] == 2) {
            $data['id'] = I('get.id');
            $data['status'] = 1;
            $rel = $paymentparameter_table->save($data);
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

    public function pay_stop() {
        $paymentparameter_table = M('paymentparameter');
        $id = I('get.id');
        $result = $paymentparameter_table->where(array('id' => $id))->find();
        if ($result['status'] == 1) {
            $data['id'] = $id;
            $data['status'] = 2;
            $rel = $paymentparameter_table->save($data);
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

    public function payadd() {
        if (isset($_POST['username']) && !empty($_POST['md5pwd'])) {
            $paymentparameter_table = M('paymentparameter');
            $username = I('post.username');
            $md5pwd = I('post.md5pwd');
            $message=I('post.message');
            $relust = $paymentparameter_table->add(array('username' => $username,'md5pwd'=>$md5pwd,'message'=>$message));
            if ($relust) {
                $json['status'] = 1;
                $json['msg'] = '添加成功';
                echo json_encode($json);
                exit;
            } else {
                $json['status'] = 2;
                $json['msg'] = '添加失败';
                echo json_encode($json);
                exit;
            }
        } else {
            $this->display();
        }
    }

    public function payedit() {
      $paymentparameter_table = M('paymentparameter');
        if (isset($_POST['id']) && !empty($_POST['id'])) {
            $id = I('post.id');
            $username= I('post.username');
            $md5pwd= I('post.md5pwd');
             $message=I('post.message');
            $relust = $paymentparameter_table->save(array('id' => $id, 'username' => $username, 'sort' => $sort,'md5pwd'=>$md5pwd,'message'=>$message));
            if ($relust) {
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


            $id = I('get.id');
            $info = $paymentparameter_table->find($id);
            $this->assign('info', $info);
            $this->assign('id', $id);
            $this->display();
        }
    }

    public function paydel() {

        if (isset($_GET['id']) && !empty($_GET['id'])) {
           $paymentparameter_table = M('paymentparameter');
            $id = $_GET['id'];
            $relust = $paymentparameter_table->delete($id);
            if ($relust) {
                $json['status'] = 1;
                $json['msg'] = '删除成功';
                echo json_encode($json);
                exit;
            } else {
                $json['status'] = 2;
                $json['msg'] = '删除失败';
                echo json_encode($json);
                exit;
            }
        }
    }
    public function payparam(){
        
        $paymentparameter_table = M('paymentparameter');
        $paymentparameter_list = $paymentparameter_table->select();
        $count = count($paymentparameter_list);
        $this->assign('count', $count);
        $this->assign('list', $paymentparameter_list);
        $this->display();
    }
    
}
