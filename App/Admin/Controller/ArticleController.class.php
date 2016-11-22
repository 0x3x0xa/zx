<?php

namespace Admin\Controller;

use Admin\Controller\CommonController;

class ArticleController extends CommonController {
    /*     * *
     *
     * 公告中心
     */

    public function index() {


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
            $map['art_time'] = array('between', $times);
            //$timespan = strtotime(urldecode($_REQUEST['start_time'])) . "," . strtotime(urldecode($_REQUEST['end_time']));
        } elseif (!empty($_REQUEST['search_starttime'])) {
            $xtime = strtotime($_REQUEST['search_starttime'] . '00:00:00');
            $map['art_time'] = array("egt", $xtime);
            $search['search_starttime'] = $_REQUEST['search_starttime'];
        } elseif (!empty($_REQUEST['search_endtime'])) {
            $xtime = strtotime($_REQUEST['search_endtime'] . '23:59:59');
            $map['art_time'] = array("elt", $xtime);
            $search['search_endtime'] = $_REQUEST['search_endtime'];
        }
        if (!empty($_REQUEST['search_title'])) {
            $map['art_title'] = array('like', '%' . $_REQUEST['search_title'] . '%');
            $search['search_title'] = $_REQUEST['search_title'];
        }
        if (!empty($_REQUEST['search_type'])) {
            $map['art_type'] = array('eq', $_REQUEST['search_type']);
            $search['search_type'] = $_REQUEST['search_type'];
        }
        $article_table = M('article');
        $class_table = M('class');
        $list1 = $class_table->where(array('type' => '1'))->select();
        $count = $article_table->where($map)->count(); // 查询满足要求的总记录数
        $Page = new \Think\Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show(); //
        $list = $article_table->order('id desc')->where($map)->limit($Page->firstRow . ',' . $Page->listRows)->select();

        $lcount = count($list);
        for ($i = 0; $i < $lcount; $i++) {

            $class_row = $class_table->where(array('type' => '1'))->find($list[$i]['art_type']);
            if ($class_row) {
                $list[$i]['type'] = $class_row['type_name'];
            } else {
                $list[$i]['type'] = '该分类已经被删除，请重新指定分类';
            }
        }
        $this->assign('page', $show);
        $this->assign('list', $list);
        $this->assign('list1', $list1);
        $this->assign('count', $count);
        $this->assign('arr', $search);
        $this->display();
    }

    //分类管理
    public function articleclass() {
        $class_table = M('class');
        $map['type'] = '1';
        $count = $class_table->where($map)->count(); // 查询满足要求的总记录数
        $Page = new \Think\Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show(); //
        $list = $class_table->order('id desc')->where($map)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('page', $show);
        $this->assign('list', $list);
        $this->assign('count', $count);
        $this->display();
    }

//编辑分类

    public function articleclassedit($id) {

        $class_table = M('class');
        if (IS_POST) {

            $result = $class_table->save($_POST);
            if ($result) {
                $json['status'] = 1;
                $json['msg'] = '编辑成功';
                echo json_encode($json);
                exit;
            } else {
                $json['status'] = 2;
                $json['msg'] = '编辑失败';
                echo json_encode($json);
                exit;
            }
        }
        $row = $class_table->find($id);
        $this->assign('row', $row);
        $this->display();
    }

//添加分类
    public function article_class_add() {
        $class_table = M('class');
        if (IS_POST) {
            if (empty($_POST['type_name'])) {
                $json['status'] = 2;
                $json['msg'] = '请输入分类名称';
                echo json_encode($json);
                exit;
            }
            $_POST['type'] = '1';
            $relust = $class_table->add($_POST);
            if ($relust) {
                $json['status'] = 1;
                $json['msg'] = '添加成功！';
                echo json_encode($json);
                exit;
            } else {
                $json['status'] = 2;
                $json['msg'] = '添加失败！';
                echo json_encode($json);
                exit;
            }
        }
        $this->display();
    }

    //删除分类
    public function article_class_del($id) {
        $class_table = M('class');
        $relsult = $class_table->delete($id);
        if ($relsult) {
            $json['status'] = 1;
            $json['msg'] = '已经删除！';
            echo json_encode($json);
            exit;
        } else {
            $json['status'] = 2;
            $json['msg'] = '删除失败！';
            echo json_encode($json);
            exit;
        }
    }

    public function articleadd() {

        $article_table = M('article');
        if (IS_POST) {

            $_POST['art_content'] = $_POST['editorvalue'];
            $_POST['art_time'] = time();

            $relust = $article_table->add($_POST);
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

            $list1 = M('class')->where(array('type' => '1'))->select();
            $this->assign('list1', $list1);
            $this->display();
        }
    }

    public function articleedit($id) {

        $article_table = M('article');
        if (IS_POST) {
            $_POST['art_content'] = $_POST['editorvalue'];
            $_POST['art_time'] = time();
            $relust = $article_table->save($_POST);
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


        $article_row = $article_table->find($id);
        $list1 = M('class')->where(array('type' => '1'))->select();
        $this->assign('list1', $list1);
        $this->assign('article_row', $article_row);
        $this->assign('id', $id);
        $this->display();
    }

    public function articlezhang($id) {
        if (IS_GET) {
            $article_row = M('article')->where($data)->find($id);
        }
        $this->assign('article_row', $article_row);
        $this->display();
    }

    //删除文章
    public function article_del($id) {
        $article_table = M('article');
        $relsult = $article_table->delete($id);
        if ($relsult) {
            $json['status'] = 1;
            $json['msg'] = '删除成功！';
            echo json_encode($json);
            exit;
        } else {
            $json['status'] = 2;
            $json['msg'] = '删除失败！';
            echo json_encode($json);
            exit;
        }
    }

    //批量删除文章
    public function datadel_article() {
        $article_table = M('article');
        $str = I('get.str');
        $str = rtrim($str, ',');
        $relsult = $article_table->where(array('id'=>array('in',$str)))->delete();
        if ($relsult) {
            $json['status'] = 1;
            $json['msg'] = '删除失败！';
            echo json_encode($json);
            exit;
        } else {
            $json['status'] = 2;
            $json['msg'] = '删除失败！';
            echo json_encode($json);
            exit;
        }
    }

    //下架
    public function article_stop($id) {

        $article_table = M('article');
        $relsult = $article_table->find($id);
        if ($relsult['art_status'] == 1) {
            $data['id'] = I('get.id');
            $data['art_status'] = 2;
            $rel1 = $article_table->save($data);
            if ($rel1) {
                $json['art_status'] = 1;
                $json['msg'] = '操作成功！';
                echo json_encode($json);
                exit;
            } else {
                $json['art_status'] = 2;
                $json['msg'] = '操作失败！';
                echo json_encode($json);
                exit;
            }
        }
    }

//上架

    public function article_start($id) {

        $article_table = M('article');
        $relsult = $article_table->find($id);
        if ($relsult['art_status'] == 2) {
            $data['id'] = I('get.id');
            $data['art_status'] = 1;
            $rel1 = $article_table->save($data);
            if ($rel1) {
                $json['art_status'] = 1;
                $json['msg'] = '操作成功！';
                echo json_encode($json);
                exit;
            } else {
                $json['art_status'] = 2;
                $json['msg'] = '操作失败！';
                echo json_encode($json);
                exit;
            }
        }
    }

}
