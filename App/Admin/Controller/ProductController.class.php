<?php

namespace Admin\Controller;

use Admin\Controller\CommonController;

class ProductController extends CommonController {
    /*     * *
     *
     * 产品中心
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
        if (!empty($_REQUEST['search_title'])) {
            $map['product_title'] = array('like', '%' . $_REQUEST['search_title'] . '%');
            $search['search_title'] = $_REQUEST['search_title'];
        }
        if (!empty($_REQUEST['search_type'])) {
            $map['product_type'] = array('eq', $_REQUEST['search_type']);
            $search['search_type'] = $_REQUEST['search_type'];
        }
        $product_table = M('product');
        $class_table = M('class');
        $count = $product_table->where($map)->count(); // 查询满足要求的总记录数
        $Page = new \Think\Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show(); //
        $list = $product_table->order('id desc')->where($map)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $list1 = $class_table->where(array('type' => '3'))->select();
        $lcount = count($list);
        for ($i = 0; $i < $lcount; $i++) {

            $class_row = $class_table->where(array('type' => '3'))->find($list[$i]['product_type']);
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
    public function productclass() {
        $class_table = M('class');
        $map['type'] = '3';
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

    public function productclassedit($id) {


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
    public function product_class_add() {
        $class_table = M('class');
        if (IS_POST) {
            if (empty($_POST['type_name'])) {
                $json['status'] = 2;
                $json['msg'] = '请输入分类名称';
                echo json_encode($json);
                exit;
            }
            $_POST['type'] = '3';
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
    public function product_class_del($id) {
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

    //添加咨讯
    public function productadd() {
        $product_table = M('product');
        if (IS_AJAX) {
            $_POST['create_date'] = time();
            $relust = $product_table->add($_POST);
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

            $list1 = M('class')->where(array('type' => '3'))->select();
            $this->assign('list1', $list1);
            $this->display();
        }
    }

    public function productimg($id) {
        $productimg_table = M('productimg');
        $map['product_id'] = $id;
        $count = $productimg_table->where($map)->count(); // 查询满足要求的总记录数
        $Page = new \Think\Page($count, 4); // 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show(); //
        $list = $productimg_table->where($map)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('page', $show);
        $this->assign('count', $count);
        $this->assign('list', $list);
        $this->assign('id', $id);
        $this->display();
    }

    public function productaddimg($id) {
        if (IS_POST) {


            $product_id = $_REQUEST['product_id'];
            $upload = new \Think\Upload();
            $upload->maxSize = 3145728; // 设置附件上传大小
            $upload->exts = array('jpg', 'gif', 'png', 'jpeg'); // 设置附件上传类型
            $upload->rootPath = 'Public/upload/'; // 设置附件上传根目录
            $upload->savePath = 'product/'; // 设置附件上传（子）目录
            $info = $upload->upload();
            foreach ($info as $file) {
                $file_path = 'Public/upload/' . $file['savepath'] . $file['savename'];
                $file_min = 'Public/upload/' . $file['savepath'] . 'thumb_' . $file['savename'];
                $file_middle='Public/upload/' . $file['savepath'] . 'thumb_middle_' . $file['savename'];
                $productimg_table = M('productimg');
                $relust = $productimg_table->add(array('image_path' => $file_path, 'image_path_thumb' => $file_min,'image_path_middle'=>$file_middle, 'product_id' => $product_id));
                
            }
            $image = new \Think\Image();
            $image->open($file_path);
            $image->thumb(120, 100)->save($file_min);
            $image->open($file_path);
            $image->thumb(300, 300)->save($file_middle);
            if (!$info && !$relust) {
                $this->ajaxReturn($upload->getError());
            } else {

                $this->ajaxReturn($info);
            }
        } else {
            $this->assign('id', $id);
            $this->display();
        }
    }

    //编辑咨讯
    public function productedit($id) {

        $product_table = M('product');
        if (IS_POST) {
            $relust = $product_table->save($_POST);
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


        $product_row = $product_table->find($id);
        $list1 = M('class')->where(array('type' => '3'))->select();
        $this->assign('list1', $list1);
        $this->assign('product_row', $product_row);
        $this->assign('id', $id);
        $this->display();
    }

    public function productzhang($id) {
        if (IS_GET) {

            $product_row = M('product')->where($data)->find($id);
        }

        $this->assign('product_row', $product_row);
        $this->display();
    }

    //删除产品
    public function product_del($id) {
        $product_table = M('product');
        $productimg_table = M('productimg');
        $relsult = $product_table->delete($id);
        if ($relsult) {
            $list = $productimg_table->field('id,image_path,image_path_thumb,image_path_middle')->where(array('product_id' => $id))->select();
            if ($list) {
                $count = count($list);
                for ($i = 0; $i < $count; $i++) {
                    $productimg_table->delete($list[$i]['id']);
                     unlink($list[$i]['image_path']);
                     unlink($list[$i]['image_path_thumb']);
                      unlink($list[$i]['image_path_middle']);
                }
            }
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

    //删除产品图片
    public function product_img_del($id) {
        $productimg_table = M('productimg');
        $rel=$productimg_table->field('image_path,image_path_thumb,image_path_middle,image_path_middle')->find($id);
        $relsult = $productimg_table->delete($id);
        unlink($rel['image_path']);
        unlink($rel['image_path_thumb']);
        unlink($rel['image_path_middle']);
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

    //批量删除产品
    public function datadel_product() {
        $product_table = M('product');
        $productimg_table = M('productimg');
        $str = I('get.str');
        $str = rtrim($str, ',');
        unset($data);
        $data['id'] = array('in', $str);
        $relsult = $product_table->where($data)->delete();
        if ($relsult) {
            $list = $productimg_table->field('id,image_path,image_path_thumb,image_path_middle')->where(array('product_id' => array('in', $str)))->select();
            $count = count($list);
            for ($i = 0; $i < $count; $i++) {
                $productimg_table->delete($list[$i]['id']);
                  unlink($list[$i]['image_path']);
                  unlink($list[$i]['image_path_thumb']);
                  unlink($list[$i]['image_path_middle']);
            }
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

    //下架
    public function product_stop($id) {

        $product_table = M('product');
        $relsult = $product_table->find($id);
        if ($relsult['status'] == 1) {
            $data['id'] = I('get.id');
            $data['status'] = 0;
            $rel1 = $product_table->save($data);
            if ($rel1) {
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
    }

//产品图片主图

    public function product_img_start($id) {

        $productimg_table = M('productimg');
        $rel = $productimg_table->where(array('master_map' => '1'))->find();
        $relsult = $productimg_table->find($id);
        if ($relsult['master_map'] == 0) {
            $data['id'] = I('get.id');
            $data['master_map'] = 1;
            $rel1 = $productimg_table->save($data);
            if ($rel) {
                $rel2 = $productimg_table->save(array('id' => $rel['id'], 'master_map' => '0'));
            }
            if ($rel1) {
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
    }

    public function product_img_stop($id) {

        $productimg_table = M('productimg');
        $relsult = $productimg_table->find($id);
        if ($relsult['master_map'] == 1) {
            $data['id'] = I('get.id');
            $data['master_map'] = 0;
            $rel1 = $productimg_table->save($data);
            if ($rel1) {
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
    }

//上架

    public function product_start($id) {

        $product_table = M('product');
        $relsult = $product_table->find($id);
        if ($relsult['status'] == 0) {
            $data['id'] = I('get.id');
            $data['status'] = 1;
            $rel1 = $product_table->save($data);
            if ($rel1) {
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
    }

}
