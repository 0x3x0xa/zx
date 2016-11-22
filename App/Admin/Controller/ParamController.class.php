<?php

namespace Admin\Controller;

use Admin\Controller\CommonController;

class ParamController extends CommonController {
    /*     * *
     *
     * 参数管理
     */

    public function memberlevel() {



        if (!empty($_REQUEST['search_title'])) {
            $map['title'] = array('eq', $_REQUEST['search_title']);
            $search['title'] = $_REQUEST['title'];
        }
        $memberlevel_table = M('memberlevel');
        $count = $memberlevel_table->where($map)->count(); // 查询满足要求的总记录数
        $Page = new \Think\Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show(); //
        $list = $memberlevel_table->where($map)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('page', $show);
        $this->assign('list', $list);
        $this->assign('count', $count);
        $this->assign('arr', $search);
        $this->display();
    }

    //编辑注册会员等级参数
    public function memberleveledit($id) {

        $memberlevel_table = M('memberlevel');
        if (IS_POST) {
            $relust = $memberlevel_table->save($_POST);
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
        }

        $row = $memberlevel_table->find($id);
        $this->assign('row', $row);
        $this->assign('id', $id);
        $this->display();
    }

    //快速奖参数管理
    public function levelvalue() {

        $list = M()->table(array('web_levelvalue' => 'tb1', 'web_setlevel' => 'tb2', 'web_memberlevel' => 'tb3'))->field('tb1.id,tb1.value,tb2.min,tb2.max,tb3.title')->where('tb2.id=tb1.setlevelid and tb3.id=tb1.memberlevelid ')->select();

        $this->assign('list', $list);
        $this->display();
    }

    public function levelvalueadd() {
        if (IS_POST) {
            $levelvalue_table = M('levelvalue');
            $relust = $levelvalue_table->add($_POST);

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
        }
        $memberlevel_table = M('memberlevel');
        $setlevel_table = M('setlevel');
        $memberlevellist = $memberlevel_table->field('title,id')->select();
        $setlevellist = $setlevel_table->select();
        $this->assign('memberlevellist', $memberlevellist);
        $this->assign('setlevellist', $setlevellist);
        $this->display();
    }

    public function levelvalueedit() {
        $levelvalue_table = M('levelvalue');
        if (IS_POST) {

            $relust = $levelvalue_table->save($_POST);

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
        }

        $id = I('get.id', '', 'htmlspecialchars');
        $memberlevel_table = M('memberlevel');
        $setlevel_table = M('setlevel');
        $levelvalueInfo = $levelvalue_table->find($id);
        $memberlevellist = $memberlevel_table->field('title,id')->select();
        $setlevellist = $setlevel_table->select();
        $this->assign('memberlevellist', $memberlevellist);
        $this->assign('setlevellist', $setlevellist);
        $this->assign('row', $levelvalueInfo);
        $this->display();
    }

    public function setlevel() {
        $setlevel_table = M('setlevel');
        $list = $setlevel_table->select();
        $this->assign('list', $list);
        $this->display();
    }

    public function setleveladd() {
        if (IS_POST) {
            $setlevel_table = M('setlevel');
            $relust = $setlevel_table->add($_POST);
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
        }
        $this->display();
    }

    public function setleveledit() {
        $setlevel_table = M('setlevel');

        if (IS_POST) {
            $relust = $setlevel_table->save($_POST);
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
        }
        $id = I('get.id', '', 'htmlspecialchars');
        $row = $setlevel_table->find($id);
        $this->assign('row', $row);
        $this->display();
    }

    public function setparam() {
        $webconfig = M('webconfig');
        if (IS_POST) {

            if (!$webconfig->autoCheckToken($_POST)) {
                $this->error('请不要重复提交');
                exit;
            }
            unset($_POST['token']);
            $arr = array(
                'id' => '2',
                'value' => json_encode($_POST),
            );
            $result = $webconfig->save($arr);
            if ($result) {
                $this->success('操作成功');
                exit;
            } else {
                $this->error('操作失败');
                exit;
            }
        }

        $value = $webconfig->find(2);
        $data = json_decode($value['value'], true);
        $this->assign('setparam', $data);
        $this->display();
    }

    public function position() {
        $position_table = M('position');

        if (!empty($_REQUEST['search_title'])) {
            $map['title'] = array('eq', $_REQUEST['search_title']);
            $search['title'] = $_REQUEST['title'];
        }

        $count = $position_table->where($map)->count(); // 查询满足要求的总记录数
        $Page = new \Think\Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show(); //
        $list = $position_table->field('id,title')->where($map)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('page', $show);
        $this->assign('list', $list);
        $this->assign('count', $count);
        $this->assign('arr', $search);
        $this->display();
    }

    public function positionedit() {
        $position_table=M('position');
        if (IS_POST) {
            $relust = $position_table->save($_POST);
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
        }
        $id = I('get.id', '', 'htmlspecialchars');
        $row = $position_table->find($id);
        $this->assign('row', $row);
        $this->display();
    }

}
