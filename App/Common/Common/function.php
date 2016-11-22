<?php

function ws($message) {
    $text = $message . " 成功了！\n";
    $fp = fopen('test.txt', 'a');
    fwrite($fp, $text);
    fclose($fp);
}

function auto_charset($fContents, $from = "gbk", $to = "utf-8") {
    $from = strtoupper($from) == "UTF8" ? "utf-8" : $from;
    $to = strtoupper($to) == "UTF8" ? "utf-8" : $to;
    if ($to == "utf-8" && is_utf8($fContents) || strtoupper($from) === strtoupper($to) || empty($fContents) || is_scalar($fContents) && !is_string($fContents)) {
        return $fContents;
    }
    if (is_string($fContents)) {
        if (function_exists("mb_convert_encoding")) {
            return mb_convert_encoding($fContents, $to, $from);
        } else if (function_exists("iconv")) {
            return iconv($from, $to, $fContents);
        } else {
            return $fContents;
        }
    } else if (is_array($fContents)) {
        foreach ($fContents as $key => $val) {
            $_key = auto_charset($key, $from, $to);
            $fContents[$_key] = auto_charset($val, $from, $to);
            if ($key != $_key) {
                unset($fContents[$key]);
            }
        }
        return $fContents;
    } else {
        return $fContents;
    }
}

function getbonustype() {
    $type = array('2' => '开发奖', '3' => '管理奖',  '5' => '全球分红', '6' => '电子积分', '7' => '个人所得税', '8' => '名车基金', '9' => '重消积分', '10' => '赠送积分', '11' => '别墅基金', '12' => '红包奖', '13' => '溢价积分', '14' => '分红积分', '15' => '旅游基金', '16' => '公益基金', '17' => '月薪奖', '21' => '月薪奖池', '19' => '市场积分', '20' => '活动积分', '22' => '现金积分','23'=>'领导奖','24'=>'购物积分','25'=>'购物卷');
    return $type;
}

function dongtai() {
    $type = array('2' => '开发奖', '3' => '管理奖', '5' => '全球分红', '12' => '红包奖');
    return $type;
}

function jingtai() {
    $type = array('13' => '溢价积分', '14' => '分红积分');
    return $type;
}

function allmomeytype() {
    $type = array('19' => '市场积分', '20' => '活动积分');
    return $type;
}

function is_utf8($string) {
    return preg_match("%^(?:\r\n\t\t [\\x09\\x0A\\x0D\\x20-\\x7E]            # ASCII\r\n\t   | [\\xC2-\\xDF][\\x80-\\xBF]             # non-overlong 2-byte\r\n\t   |  \\xE0[\\xA0-\\xBF][\\x80-\\xBF]        # excluding overlongs\r\n\t   | [\\xE1-\\xEC\\xEE\\xEF][\\x80-\\xBF]{2}  # straight 3-byte\r\n\t   |  \\xED[\\x80-\\x9F][\\x80-\\xBF]        # excluding surrogates\r\n\t   |  \\xF0[\\x90-\\xBF][\\x80-\\xBF]{2}     # planes 1-3\r\n\t   | [\\xF1-\\xF3][\\x80-\\xBF]{3}          # planes 4-15\r\n\t   |  \\xF4[\\x80-\\x8F][\\x80-\\xBF]{2}     # plane 16\r\n   )*\$%xs", $string);
}

function p($data) {
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}

//获取升级的条件
function getpositionterm($id) {
    $position_table = M('position');
    $data = $position_table->find($id);
    return $data;
}

/**
 * 字符串截取，支持中文和其他编码
 * @param string $str     需要转换的字符串
 * @param int    $start   开始位置
 * @param string $length  截取长度
 * @param string $charset 编码格式
 * @param bool   $suffix  截断显示字符
 * @return string
 */
function msubstr($str, $start = 0, $length, $charset = "utf-8", $suffix = false) {
    return Org\Util\String::msubstr($str, $start, $length, $charset, $suffix);
}

/**
 * 检测输入的验证码是否正确
 * @param string $code 为用户输入的验证码字符串
 * @param string $id   其他参数
 * @return bool
 */
function check_verify($code, $id = '') {
    $verify = new \Think\Verify();
    return $verify->check($code, $id);
}

//获取支付信息
function getpayparam() {
    $paymentparameter_table = M('paymentparameter');
    $data = $paymentparameter_table->where(array('status' => '1'))->find();
    return $data;
}

/**
 * 解析多行sql语句转换成数组
 * @param string $sql
 * @return array
 */
function sql_split($sql) {
    $sql = str_replace("\r", "\n", $sql);
    $ret = array();
    $num = 0;
    $queriesarray = explode(";\n", trim($sql));
    unset($sql);
    foreach ($queriesarray as $query) {
        $ret[$num] = '';
        $queries = explode("\n", trim($query));
        $queries = array_filter($queries);
        foreach ($queries as $query) {
            $str1 = substr($query, 0, 1);
            if ($str1 != '#' && $str1 != '-')
                $ret[$num] .= $query;
        }
        $num++;
    }
    return($ret);
}

/**
 * 格式化字节大小
 * @param  number $size      字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function format_bytes($size, $delimiter = '') {
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
    for ($i = 0; $size >= 1024 && $i < 5; $i++)
        $size /= 1024;
    return round($size, 2) . $delimiter . $units[$i];
}

/**
 * 取得文件扩展
 * @param string $filename 文件名
 * @return string
 */
function file_ext($filename) {
    return strtolower(trim(substr(strrchr($filename, '.'), 1, 10)));
}

/**
 * 远程文件内容读取
 * @param string $url
 * @return string
 */
function file_read_remote($url) {
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_REFERER, $url); //伪造来路
    curl_setopt($curl, CURLOPT_USERAGENT, 'Alexa (IA Archiver)');
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_NOBODY, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($curl);
    curl_close($curl);
    return $result;
}

//发送验证码
function set_code_sms($username, $mobile, $num, $minute, $code_db, $mian_db, $frequency) {
    if (preg_match('#^(1)[0-9]{10}$#', $mobile)) {
        $todayTime = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
        $tomorrow = $todayTime + 60 * 60 * 24; //明天凌晨

        $member_table = M($mian_db);
        $code_table = M($code_db);
        $userinfo = $member_table->field('id')->where(array('mobile' => $mobile, 'username' => $username))->find();
        if ($userinfo) {
            $codeinfo = $code_table->where(array('uid' => $userinfo['id']))->find();
            $verifyCode = create_code($num);
            if ($codeinfo) {
                if ($codeinfo['updatetime'] != $todayTime) {
                    $code_table->save(array('id' => $codeinfo['id'], 'code' => $verifyCode, 'effectivetime' => time() + 60 * $minute, 'frequency' => '1', 'updatetime' => $todayTime));
                    send_sms($mobile, '尊敬的会员：您本次修改密码的短信验证码为' . $verifyCode . '，(请勿泄露)，请在' . $minute . '分钟内完成验证，如非您本人操作，请忽略该短信！');
                    $json['status'] = 1;
                    $json['msg'] = '发送成功！';
                    echo json_encode($json);
                    exit;
                } else if ($codeinfo['updatetime'] == $todayTime) {
                    if ($codeinfo['frequency'] >= $frequency) {
                        $json['status'] = 1;
                        $json['msg'] = '今天短信验证码的次数用完了！';
                        echo json_encode($json);
                        exit;
                    } else {
                        if ($codeinfo['effectivetime'] > time()) {
                            $json['status'] = 2;
                            $json['msg'] = '请等3分钟后在操作！';
                            echo json_encode($json);
                            exit;
                        } else {
                            $code_table->save(array('id' => $codeinfo['id'], 'code' => $verifyCode, 'effectivetime' => time() + 60 * $minute, 'frequency' => $codeinfo['frequency'] + 1, 'updatetime' => $todayTime));
                            send_sms($mobile, '尊敬的会员：您本次修改密码的短信验证码为' . $verifyCode . '，(请勿泄露)，请在' . $minute . '分钟内完成验证，如非您本人操作，请忽略该短信！');
                            $json['status'] = 1;
                            $json['msg'] = '发送成功！';
                            echo json_encode($json);
                            exit;
                        }
                    }
                }
            } else {
                $code_table->add(array('uid' => $userinfo['id'], 'code' => $verifyCode, 'effectivetime' => time() + 60 * $minute, 'frequency' => 1, 'updatetime' => $todayTime));
                send_sms($mobile, '尊敬的会员：您本次修改密码的短信验证码为' . $verifyCode . '，(请勿泄露)，请在' . $minute . '分钟内完成验证，如非您本人操作，请忽略该短信！');
                $json['status'] = 1;
                $json['msg'] = '发送成功！';
                echo json_encode($json);
                exit;
            }
        } else {
            $json['status'] = 2;
            $json['msg'] = '用户不存在！';
            echo json_encode($json);
            exit;
        }
    } else {
        $json['status'] = 2;
        $json['msg'] = '手机号不存在！';
        echo json_encode($json);
        exit;
    }
}

//随机数
function create_code($num) {
    $str = "1,2,3,4,5,6,7,8,9,a,b,c,d,f,g,h,i,z,k,l,m,n,o,p,q,r,s,t,u,v,w,y,x,z";      //要显示的字符，可自己进行增删
    $list = explode(",", $str);
    $cmax = count($list) - 1;
    $verifyCode = '';
    for ($i = 0; $i < $num; $i++) {
        $randnum = mt_rand(0, $cmax);
        $verifyCode .= $list[$randnum];           //取出字符，组合成为我们要的验证码字符
    }

    return $verifyCode;
}

function get_user_id($username) {
    $member_table = M('member');
    $userinfo = $member_table->field('id')->where(array('username' => $username))->find();
    if ($userinfo) {
        return $userinfo['id'];
    } else {
        return FALSE;
    }
}

function get_user_name($id) {
    $member_table = M('member');
    $userinfo = $member_table->field('username')->find($id);
    if ($userinfo) {
        return $userinfo['username'];
    } else {
        return FALSE;
    }
}

// 发短信
function send_sms($mobile, $content) {
    $data = getbaseparam();
    if ($data['sms_status'] == 1) {
        $uid = $data['smsusername'];
        $pwd = $data['smspwd'];
        $sendurl = "http://service.winic.org/sys_port/gateway/?id=" . $uid . "&pwd=" . $pwd . "&to=" . $mobile . "&content=" . iconv('utf-8', 'gb2312', $content);
        $xhr = new COM("MSXML2.XMLHTTP");
        $xhr->open("GET", $sendurl, false);
        $xhr->send();
        return $xhr->responseText;
    }
    //接口官网 吉信通   融合通信2.0，  http://web.900112.com/ 
    // $uid="slszgcy.com"; //分配给你的账号
    //  $pwd="cy12345678"; //密码
    //===========================
    //php.ini  开启扩展
    //[COM]
//; path to a file containing GUIDs, IIDs or filenames of files with TypeLibs
//; http://php.net/com.typelib-file
//;com.typelib_file =
//
//; allow Distributed-COM calls
//; http://php.net/com.allow-dcom
//com.allow_dcom = true
//extension=php_com_dotnet.dll
}

/**
 * 发送邮件
 * @param string $to      收件人
 * @param string $subject 主题
 * @param string $body    内容
 * @param array $config
 * @return bool
 */
function send_email($to, $sender, $subject, $body, $mailtype = 'HTML') {

    $data = getbaseparam();
    if ($data['email_status'] == 1) {
        $port = $data['smtpport'];
        $smtpserver = $data['smtpserver'];
        $smtpuser = $data['smtpuser'];
        $smtppwd = $data['smtppwd'];
        $sender = $data['smtpuser'];

        $email = new \Common\Plugin\Email($smtpserver, $port, true, $smtpuser, $smtppwd, $sender);
        $email2 = $email->smtp($smtpserver, $port, true, $smtpuser, $smtppwd, $sender);
        $send = $email->sendmail($to, $sender, $subject, $body, $mailtype);
        return $send;
    }
}

/* 检测密码 */

function checkPwd($pwd) {
    $ergp = "/^[A-Za-z0-9]{6,16}$/";
    if (preg_match($ergp, $pwd) && strlen($pwd) >= 6 && strlen($pwd) <= 16) {
        return true;
    } else {
        return FALSE;
    }
}

function fun_md5($password) {
    $password = md5(md5($password . 'fdsfdsfdsf4324243'));
    return $password;
}

//获取基本设置
function getbaseparam() {
    $webconfig = M('webconfig');
    $value = $webconfig->find('1');
    $data = json_decode($value['value'], true);
    return $data;
}

//获取奖金参数
function getbonusparam() {
    $webconfig = M('webconfig');
    $value = $webconfig->find('2');
    $data = json_decode($value['value'], true);
    return $data;
}

/* 生成随机字符串 */

function rand_string($ukey = "", $len = 6, $type = "1", $utype = "1", $addChars = "", $temail = '') {
    $str = "";
    switch ($type) {
        case 0 :
            $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz" . $addChars;
            break;
        case 1 :
            $chars = str_repeat("0123456789", 3);
            break;
        case 2 :
            $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ" . $addChars;
            break;
        case 3 :
            $chars = "abcdefghijklmnopqrstuvwxyz" . $addChars;
            break;
        default :
            $chars = "ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789" . $addChars;
            break;
    }
    if (10 < $len) {
        $chars = $type == 1 ? str_repeat($chars, $len) : str_repeat($chars, 5);
    }
    $chars = str_shuffle($chars);
    $str = substr($chars, 0, $len);
    return $str;
}

/* 生成订单号 */

function build_order_no() {
    return date('Ymd') . substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
}

/* 验证链接是否有效 */

function is_verify($uid, $code, $utype, $timespan) {
    if (!empty($uid)) {
        $vd['ukey'] = $uid;
    }
    $vd['type'] = $utype;
    $vd['send_time'] = array(
        "gt",
        time() - $timespan
    );
    $vd['code'] = $code;
    $vo = m("verify")->field("ukey,hasset")->where($vd)->order('send_time desc')->find();
    if (is_array($vo)) {
        if ($utype == 2) {
            if ($vo['hasset'] == 1) {
                return false;
            } else {
                return $vo['ukey'];
            }
        } else {
            return $vo['ukey'];
        }
    } else {
        return false;
    }
}

/* 截取字符串 */

function cnsubstr($str, $length, $start = 0, $charset = "utf-8", $suffix = true) {
    $str = strip_tags($str);
    if (function_exists("mb_substr")) {
        if (mb_strlen($str, $charset) <= $length) {
            return $str;
        }
        $slice = mb_substr($str, $start, $length, $charset);
    } else {
        $re['utf-8'] = "/[\x01-]|[-][-]|[-][-]{2}|[-][-]{3}/";
        $re['gb2312'] = "/[\x01-]|[-][-]/";
        $re['gbk'] = "/[\x01-]|[-][@-]/";
        $re['big5'] = "/[\x01-]|[-]([@-~]|-])/";
        preg_match_all($re[$charset], $str, $match);
        if (count($match[0]) <= $length) {
            return $str;
        }
        $slice = join("", array_slice($match[0], $start, $length));
    }
    if ($suffix) {
        return $slice . "…";
    }
    return $slice;
}

/* 截取字符串 end */

/**
 * 检测用户是否登录
 * @return integer 0-未登录，大于0-当前登录用户ID
 */
function is_login() {
    $user = session('user_auth');
    if (empty($user)) {
        return 0;
    } else {
        return session('user_auth_sign') == data_auth_sign($user) ? $user['uid'] : 0;
    }
}

/**
 * 获取系统信息
 *
 * @return array
 */
function getSystemInfo() {
    $systemInfo = array();

    // 系统
    $systemInfo['os'] = PHP_OS;

    // PHP版本
    $systemInfo['phpversion'] = PHP_VERSION;

    // Apache版本
    // $systemInfo['apacheversion'] = apache_get_version();
    // ZEND版本
    $systemInfo['zendversion'] = zend_version();

    // GD相关
    if (function_exists('gd_info')) {
        $gdInfo = gd_info();
        $systemInfo['gdsupport'] = true;
        $systemInfo['gdversion'] = $gdInfo['GD Version'];
    } else {
        $systemInfo['gdsupport'] = false;
        $systemInfo['gdversion'] = '';
    }
    //现在的时间
    $systemInfo['nowtime'] = date('Y-m-d H:i:s', time());
    //客户端ip
    $systemInfo['remote_addr'] = getenv('REMOTE_ADDR');
    //服务器端
    $systemInfo['server_name'] = gethostbyname($_SERVER["SERVER_NAME"]);
    // 安全模式
    $systemInfo['safemode'] = ini_get('safe_mode');

    // 注册全局变量
    $systemInfo['registerglobals'] = ini_get('register_globals');

    // 开启魔术引用
    $systemInfo['magicquotes'] = (function_exists("get_magic_quotes_gpc") && get_magic_quotes_gpc());

    // 最大上传文件大小
    $systemInfo['maxuploadfile'] = ini_get('upload_max_filesize');

    // 脚本运行占用最大内存
    $systemInfo['memorylimit'] = get_cfg_var("memory_limit") ? get_cfg_var("memory_limit") : '-';

    return $systemInfo;
}

function check_ip($ip_data, $ip) {
    $ALLOWED_IP = explode(',', $ip_data);

    $check_ip_arr = explode('.', $ip); //要检测的ip拆分成数组  
    #限制IP  
    if (!in_array($IP, $ALLOWED_IP)) {
        foreach ($ALLOWED_IP as $val) {
            if (strpos($val, '*') !== false) {//发现有*号替代符  
                $arr = array(); //  
                $arr = explode('.', $val);
                $bl = true; //用于记录循环检测中是否有匹配成功的  
                for ($i = 0; $i < 4; $i++) {
                    if ($arr[$i] != '*') {//不等于*  就要进来检测，如果为*符号替代符就不检查  
                        if ($arr[$i] != $check_ip_arr[$i]) {
                            $bl = false;
                            break; //终止检查本个ip 继续检查下一个ip  
                        }
                    }
                }//end for   
                if ($bl) {//如果是true则找到有一个匹配成功的就返回  
                    return true;
                    die;
                }
            }
        }//end foreach  
        return false;
        die;
    }
}

//加解密函数encrypt()：
// 函数
// encrypt($string,$operation,$key)中$string：需要加密解密的字符串；$operation：判断是加密还是解密，E表示加密，D表示解密；$key：密匙。
// 用法：
//$str = 'abc'; 
//$key = 'www.helloweba.com'; 
//$token = encrypt($str, 'E', $key); 
//echo '加密:'.encrypt($str, 'E', $key); 
//echo '解密：'.encrypt($str, 'D', $key); 

function encrypt($string, $operation, $key = '') {
    $key = md5($key);
    $key_length = strlen($key);
    $string = $operation == 'D' ? base64_decode($string) : substr(md5($string . $key), 0, 8) . $string;
    $string_length = strlen($string);
    $rndkey = $box = array();
    $result = '';
    for ($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($key[$i % $key_length]);
        $box[$i] = $i;
    }
    for ($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }
    for ($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        $result.=chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }
    if ($operation == 'D') {
        if (substr($result, 0, 8) == substr(md5(substr($result, 8) . $key), 0, 8)) {
            return substr($result, 8);
        } else {
            return'';
        }
    } else {
        return str_replace('=', '', base64_encode($result));
    }
}

//Execl方法调用
function exportExcel($expTitle, $expCellName, $expTableData) {
    $xlsTitle = iconv('utf-8', 'gb2312', $expTitle); //文件名称
    $fileName = date('EXcel_YmdHis'); //or $xlsTitle 文件名称可根据自己情况设定
    $cellNum = count($expCellName);
    $dataNum = count($expTableData);
    import("Org.Util.PHPExcel");

    $objPHPExcel = new \PHPExcel();
    $cellName = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ');

    $objPHPExcel->getActiveSheet(0)->mergeCells('A1:' . $cellName[$cellNum - 1] . '1'); //合并单元格
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $expTitle . '  下载时间:' . date('Y-m-d H:i:s'));
    for ($i = 0; $i < $cellNum; $i++) {
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i] . '2', $expCellName[$i][1]);
    }
    // Miscellaneous glyphs, UTF-8   
    for ($i = 0; $i < $dataNum; $i++) {
        for ($j = 0; $j < $cellNum; $j++) {
            $objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j] . ($i + 3), $expTableData[$i][$expCellName[$j][0]]);
        }
    }
    ob_clean();
    header('pragma:public');
    header('Content-type:application/vnd.ms-excel;charset=utf-8;name="' . $xlsTitle . '.xls"');
    header("Content-Disposition:attachment;filename=$fileName.xls"); //attachment新窗口打印inline本窗口打印
    $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output'); //文件通过浏览器下载         
    exit;
}

//后台操作日志
function writeAdminLog($message, $type = 0, $table = '0', $tb_id = '0') {
    $adminlog_table = M('adminlog');
    $Ip = new \Org\Net\IpLocation('UTFWry.dat');
    $Ipinfo = $Ip->getlocation(get_client_ip()); // 获取某个IP地址所在的位置
    $uid = session('userid');
    $adminlog_table->add(array('uid' => $uid, 'message' => $message, 'create_date' => date('Y-m-d H:i:s'), 'type' => $type, 'ip' => $Ipinfo['ip'], 'country' => $Ipinfo['country'], 'area' => $Ipinfo['area'], 'table' => $table, 'tb_id' => $tb_id));
}

//查找级别
function findlevel($level) {
    $memberlevel_table = M('memberlevel');
    $levelInfo = $memberlevel_table->field('title')->find($level);
    return $levelInfo['title'];
}

//查找星级
function findposition($level) {
    $position_table = M('position');
    $Info = $position_table->field('title')->find($level);
    $Info['title'] = (empty($Info)) ? 无 : $Info['title'];
    return $Info['title'];
}

//查找银行
function findbank() {
    $bank_table = M('bank');
    $banklist = $bank_table->order('sort desc')->where(array('is_hied' => '1'))->select();
    return $banklist;
}

//查找产品列表
function findproductlist() {
    $product_table = M('product');
    $productlist = $product_table->order('id desc')->where(array('status' => '1'))->select();
    return $productlist;
}

//创建用户随机账号
function create_usercode() {
    $data = getbonusparam();
    $member_tabel = M('member');
    $pre = $data['memberAccountPrefix']; //前缀
    $num = $data['maxlength']; //位数
    $str = "1,2,3,4,5,6,7,8,9,0";
    $list = explode(",", $str);
    $cmax = count($list) - 1;
    $verifyCode = '';
    for ($i = 0; $i < $num; $i++) {
        $randnum = mt_rand(0, $cmax);
        $verifyCode .= $list[$randnum];
    }
    $relust = $member_tabel->field('id')->where(array('username' => $pre . $verifyCode))->find();
    if ($relust) {
        self::create_usercode();
    } else {
        return $pre . $verifyCode;
    }
}

//获取用户信息
function getuserInfo($uid) {
    $member_tabel = M('member');
    $userInfo = $member_tabel->find($uid);
    return $userInfo;
}

?>