<?php

/**
 * 用户表模型
 */

namespace Admin\Model;

use Think\Model;

class MemberModel extends Model {

    // 字段映射
    protected $_map = array(
            //
    );
    // 自动验证
    Protected $_validate = array(
        array('username','require','会员账号不能为空'),
        array('username', '', '会员帐号已经存在，请换一个！', 0, 'unique', 3),
        array('mobile', '', '手机号已经存在，请换一个！', 0, 'unique', 3),
        array('bankno', '', '银行卡号已经存在,请换一个！', 0, 'unique', 3),
        array('product_id','require','请选择产品'),
    );
    // 自动完成
    Protected $_auto = array(
        array('password', 'fun_md5', 3, 'function'),
        array('towpassword', 'fun_md5', 3, 'function'),
        array('threepassword', 'fun_md5', 3, 'function'),
        array('regtime', 'time',3, 'function'),
        array('regip', 'get_client_ip', 3, 'function'),
        array('recommend','get_user_id',3,'function'),
        array('junction','get_user_id',3,'function'),
    );

    

}

?>