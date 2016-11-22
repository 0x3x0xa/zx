<?php

namespace Home\Controller;

use Home\Controller\CommonController;

class IndexController extends CommonController {

    

    public function index() {
        ///echo date('Y-m-d H:i:s','1480521600');
        
        $this->display();
    }

    public function main() {

        $uid = session('uid');
        $member_table = M('member');
        $upgrade_table = M('upgrade');
        $userInfo = $member_table->find($uid);
        $this->assign('userInfo', $userInfo);

        $memberlevel_table = M('memberlevel');
        $level = $memberlevel_table->field('title')->find($userInfo['level']);
  
        $this->assign('level', $level);

        $article_table = M('article');
        $count = $article_table->where(array('art_status' => '1'))->count();
        $Page = new \Think\Page($count, 2);
        $show = $Page->show();
        $list = $article_table->order('art_time desc')->where(array('art_status' => '1'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $upgradeInfo = $upgrade_table->field('id')->where(array('uid' => $uid))->find();
       
        $upgradestatus = (empty($upgradeInfo)) ? 0 : 1;
        $this->assign('upgrade', $upgradestatus);
        $this->assign('page', $show);
        $this->assign('list', $list);
        

        $this->display();
    }

}
