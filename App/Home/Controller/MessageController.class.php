<?php

namespace Home\Controller;

use Home\Controller\CommonController;

class MessageController extends CommonController {

    //信件列表
    public function index() {
        $class_table = M('class');
        $uid = session('uid');
        $message_table = M('message');
        $count = $message_table->where(array('uid' => $uid))->count();
        $Page = new \Think\Page($count,50);
        $show = $Page->show();
        $list = $message_table->order('addtime desc')->where(array('uid' => $uid))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $count = count($list);
        for ($i = 0; $i < $count; $i++) {
            $classInfo = $class_table->field('type_name')->find($list[$i]['type']);
            $list[$i]['type'] = $classInfo['type_name'];
        }
        $this->assign('page', $show);
        $this->assign('list', $list);
        $this->display();
    }

    //发件箱
    public function messageadd() {
        if (IS_POST) {
            $message_table = M('message');
            $_POST['uid'] = session('uid');
            $_POST['addtime'] = time();
            if (!$message_table->autoCheckToken($_POST)) {
                $json['status'] = 2;
                $json['msg'] = '操作失败';
            }
            $result = $message_table->add($_POST);
            if ($result) {
                $json['status'] = 1;
                $json['msg'] = '操作成功';
            } else {
                $json['status'] = 2;
                $json['msg'] = '操作成功';
            }
            echo json_encode($json);
            exit;
        } else {
            $class_table = M('class');
            $list = $class_table->where(array('type' => '2'))->select();
            $this->assign('list', $list);
            $this->display();
        }
    }

    //内容页
    public function messageshow() {
        if (IS_GET) {
            $class_table = M('class');
            $message_table = M('message');
            $id = $bankno = I('get.id', '', 'htmlspecialchars');
            $row = $message_table->find($id);
            $classInfo = $class_table->field('type_name')->find($row['type']);
            $this->assign('classInfo', $classInfo);
            $this->assign('row', $row);
            $this->assign('type', $array);
        }

        $this->display();
    }

}
