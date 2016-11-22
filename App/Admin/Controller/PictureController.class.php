<?php

namespace Admin\Controller;

use Admin\Controller\CommonController;

class PictureController extends CommonController {
    /*     * *
     *
     * 图片列表 
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
        if (!empty($_REQUEST['search_username'])) {
            $map['title'] = $_REQUEST['search_username'];
            $search['search_username'] = $_REQUEST['search_username'];
        }



        $picture_table = M('picture');
        $count = $picture_table->where($map)->count(); // 查询满足要求的总记录数
        $Page = new \Think\Page($count, 4); // 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show(); //
        $list = $picture_table->order('id desc')->where($map)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('page', $show);
        $this->assign('list', $list);
        $this->assign('count', $count);
        $this->assign('arr', $search);
        $this->display();
    }

    //删除图片
    public function picture_del() {
        $id = I('get.id');
        $picture_table = M('picture');
        $picturesrc = $picture_table->where(array('id' => $id))->find();
        $relust = $picture_table->where(array('id' => $id))->delete();
        if ($relust) {
            unlink(C('MEMBER_UPLOAD_DIR') . "banner/" . $picturesrc['src']);
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

    //批量删除用户
    public function datadel_picture() {
        $picture_table = M('picture');
        $str = I('get.str');
        $str = trim($str, ',');
        $data['id'] = array('in', $str);
        $picturesrc = $picture_table->where($data)->select();
        $relust = $picture_table->where($data)->delete();
        if ($relust) {
            for ($i = 0; $i < count($picturesrc); $i++) {
                unlink(C('MEMBER_UPLOAD_DIR') . "banner/" . $picturesrc[$i]['src']);
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

    //修改图片信息
    public function pictureedit() {
        $picture = M('picture');


        if (IS_POST) {

            if (is_uploaded_file($_FILES['upfile']['tmp_name'])) {
                $photo_types = array('image/jpg', 'image/jpeg', 'image/png', 'image/pjpeg', 'image/gif', 'image/bmp', 'image/x-png'); //定义上传格式
                $max_size = 1700000;    //上传照片大小限制,默认700k
                $photo_folder = C('MEMBER_UPLOAD_DIR') . "banner/" . date("Ymd") . "/"; //上传照片路径
                ///////////////////////////////////////////////////开始处理上传
                if (!file_exists($photo_folder)) {//检查照片目录是否存在
                    mkdir($photo_folder, 0777, true);  //mkdir("temp/sub, 0777, true);
                }

                $upfile = $_FILES['upfile'];
                $name = $upfile['name'];
                $type = $upfile['type'];
                $size = $upfile['size'];
                $tmp_name = $upfile['tmp_name'];

                $file = $_FILES["upfile"];
                $photo_name = $file["tmp_name"];
                //echo $photo_name;
                $photo_size = getimagesize($photo_name);

                if ($max_size < $file["size"]) {//检查文件大小
                    echo "-1";
                    exit;

                    //echo "<script>alert('对不起，文件超过规定大小!');history.go(-1);</script>";
                }
                if (!in_array($file["type"], $photo_types)) {//检查文件类型
                    echo "-2";
                    exit;       //echo "<script>alert('对不起，文件类型不符!');history.go(-1);</script>";
                }
                if (!file_exists($photo_folder)) {//照片目录
                    mkdir($photo_folder);
                }
                $pinfo = pathinfo($file["name"]);
                $photo_type = $pinfo['extension']; //上传文件扩展名
                $time = time();
                $photo_server_folder = $photo_folder . $time . "." . $photo_type; //以当前时间和7位随机数作为文件名，这里是上传的完整路径


                if (!move_uploaded_file($photo_name, $photo_server_folder)) {
                    echo "-3"; //echo "移动文件出错";
                    exit;
                } else {
                    $pinfo = pathinfo($photo_server_folder);
                    $fname = $pinfo['basename'];


                    $pic_src = $picture->field('src')->where(array('id' => $_POST['id']))->find();

                    //保存数据
                    $_POST['src'] = date("Ymd") . "/" . $time . "." . $photo_type;
                    $_POST['create_date'] = time();
                    $relust = M('picture')->save($_POST);
                    unlink(C('MEMBER_UPLOAD_DIR') . "banner/" . $pic_src['src']);
                    echo "1";   //echo " 已经成功上传：".$photo_server_folder."<br />";
                    exit;
                }
            }
        } else {
            $id = I('get.id');
            $data['id'] = $id;
            $picture_row = $picture->where($data)->find();
            $this->assign('id', $id);
            $this->assign('picture_row', $picture_row);
            $this->display();
        }
    }

    //停用图片
    public function picture_stop() {

        $picture_table = M('picture');
        $relsult = $picture_table->where('id=' . I('get.id'))->find();
        if ($relsult['status'] == 1) {
            $data['id'] = I('get.id');
            $data['status'] = 2;
            $rel1 = $picture_table->save($data);
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

//启用图片

    public function picture_start() {

        $picture_table = M('picture');
        $relsult = $picture_table->where('id=' . I('get.id'))->find();
        if ($relsult['status'] == 2) {
            $data['id'] = I('get.id');
            $data['status'] = 1;
            $rel1 = $picture_table->save($data);
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

    //添加图片
    public function pictureadd() {
        /*
         * 1:成功上传
         * -1:文件超过规定大小
         * -2:文件类型不符
         * -3:移动文件出错
         */


        if (is_uploaded_file($_FILES['upfile']['tmp_name'])) {

            $photo_types = array('image/jpg', 'image/jpeg', 'image/png', 'image/pjpeg', 'image/gif', 'image/bmp', 'image/x-png'); //定义上传格式
            $max_size = 1700000;    //上传照片大小限制,默认700k
            $photo_folder = C('MEMBER_UPLOAD_DIR') . "banner/" . date("Ymd") . "/"; //上传照片路径
            ///////////////////////////////////////////////////开始处理上传
            if (!file_exists($photo_folder)) {//检查照片目录是否存在
                mkdir($photo_folder, 0777, true);  //mkdir("temp/sub, 0777, true);
            }

            $upfile = $_FILES['upfile'];
            $name = $upfile['name'];
            $type = $upfile['type'];
            $size = $upfile['size'];
            $tmp_name = $upfile['tmp_name'];

            $file = $_FILES["upfile"];
            $photo_name = $file["tmp_name"];
            //echo $photo_name;
            $photo_size = getimagesize($photo_name);

            if ($max_size < $file["size"]) {//检查文件大小
                echo "-1";
                exit;

                //echo "<script>alert('对不起，文件超过规定大小!');history.go(-1);</script>";
            }
            if (!in_array($file["type"], $photo_types)) {//检查文件类型
                echo "-2";
                exit;       //echo "<script>alert('对不起，文件类型不符!');history.go(-1);</script>";
            }
            if (!file_exists($photo_folder)) {//照片目录
                mkdir($photo_folder);
            }
            $pinfo = pathinfo($file["name"]);
            $photo_type = $pinfo['extension']; //上传文件扩展名
            $time = time();
            $photo_server_folder = $photo_folder . $time . "." . $photo_type; //以当前时间和7位随机数作为文件名，这里是上传的完整路径


            if (!move_uploaded_file($photo_name, $photo_server_folder)) {
                echo "-3"; //echo "移动文件出错";
                exit;
            } else {
                $pinfo = pathinfo($photo_server_folder);
                $fname = $pinfo['basename'];


                //保存数据
                $_POST['src'] = date("Ymd") . "/" . $time . "." . $photo_type;
                $_POST['create_date'] = time();
                $relust = M('picture')->add($_POST);
                echo "1";   //echo " 已经成功上传：".$photo_server_folder."<br />";
                exit;
            }
        }

        $this->display();
    }

}
