<?php
/**
 * 忘记密码管理控制器类
 * @author 肖代敏
 * @version  1.0.1 build 20170107
 */
class ForgetpasswordAction extends Action{

   /**
    * 忘记密码
    */

    public function index(){
        
 		$model = D('User');

        if ($this->isPost()) {

        	$_POST['email'] = trim($this->_post('email'));
	        
	        $_vaildate = array(
	        	array('email' , 'email' ,'邮箱格式错误', 1),
	        	);

	        if(false === $data = $model->validate($_validate)->create()){
                $this->error($model->getError());
            }

            $userId = $model->where($data)->find();

            $key= $this->authcode($userId['id'],$operation = '', $key = '', $expiry = 60);
        

             // 自定义邮件发送内容
            $header = "<p>您好！</p>";
            $url = 'http://' . I('server.HTTP_HOST') . __APP__  . '/Forgetpassword/edit_password'.'/'.'id'.'/'.$key;
            // var_dump($url);die;
            $username   = '<p>您于' . date('Y年m月d日 H时i分s秒') . '申请修改密码<strong style="color:#00acff;">' .'请点击以下链接修改密码'. '</strong></p>';        
            $url = '<a href="' . $url .'">' . $url . '</a>';
          
            $content = $header.$username.$url;

	        if($userId>0){
                if ($this->sendMail($userId['email'],$userId['name'],'修改密码消息',$content)) {
                $this->success("我们已将您的验证发送至邮箱，请进入邮箱后修改密码！(请勿关闭此页面)");
                }
                exit;
	            
	        }else{
	            $this->error('发送失败！',U('Forgetpssword/index'));
	        }
        }
       
   	$this->display();

    }
 
    /**
     * 密码修改
     */
    public function edit_password(){

        $model = D('User');
      
        $id =$_GET['id'];

        $user_id= $this->authcode($id, $operation = 'DECODE', $key = '', $expiry = 60);
        if(!$user_id){
            U('Login/index', array(), true, true);
        }

        if ($this->isPost()) {
            
            $_POST['new_password']     = trim($this->_post('new_password'));
            $_POST['confirm_password'] = trim($this->_post('confirm_password'));

              $_validate = array(
                array('new_password'    , 'require'  , '新的密码为空', 1),
                array('confirm_password', 'require'  , '确认密码不能为空', 1),
                array('new_password'    , '6,20'     , '密码必须6-20位字符串', 1, 'length'), // in  , between,  length,  等...
                array('confirm_password', 'new_password', '确认密码与新的密码不一致', 1, 'confirm'),
            );

            $model->setProperty('autoCheckFields', false);
            if(false === $data = $model->validate($_validate)->create()){
                $this->error($model->getError());
            }

            $hash_password = md5($data['new_password']);

            $cond= array();
            $cond['id']          = $data['user_id']; 
            $cond['password']    =  $hash_password;
            $cond['update_time'] = time();
        
            $effect = $model->where(array('id' => $cond['id']))->save($cond);
         
            if($effect){
                $this->success('修改成功');
            }else{
                $this->error('修改失败');
            }

        }
        $user = D('User')->find($user_id);

        $this->assign('user', $user);
        $this->display('edit_password');
    }


   /** 
     * 发送邮件方法
     * @param $to：[接收人邮箱]  $toName：[接收人名]  $title：[标题]  $content：[邮件内容]
     * @return bool true:发送成功 false:发送失败
     */
    function sendMail($to, $toName, $title, $content){

        //引入PHPMailer的核心文件 使用require_once包含避免出现PHPMailer类重复定义的警告
        require_once(LIB_PATH."ORG/PHPMailer/class.phpmailer.php"); 
        require_once(LIB_PATH."ORG/PHPMailer/class.smtp.php"); 
        //实例化PHPMailer核心类
        $mail = new \PHPMailer();

        //是否启用smtp的debug进行调试 开发环境建议开启 生产环境注释掉即可 默认关闭debug调试模式
        // $mail->SMTPDebug = 1;

        //使用smtp鉴权方式发送邮件
        $mail->isSMTP();

        //smtp需要鉴权 这个必须是true
        $mail->SMTPAuth = true;

        //链接qq域名邮箱的服务器地址
        $mail->Host = 'smtp.qq.com';

        //设置使用ssl加密方式登录鉴权
        $mail->SMTPSecure = 'ssl';

        //设置ssl连接smtp服务器的远程服务器端口号，以前的默认是25，但是现在新的好像已经不可用了 可选465或587
        $mail->Port = 465;

        //设置smtp的helo消息头 这个可有可无 内容任意
        // $mail->Helo = 'Hello smtp.qq.com Server';

        //设置发件人的主机域 可有可无 默认为localhost 内容任意，建议使用你的域名
        $mail->Hostname = 'localhost';

        //设置发送的邮件的编码 可选GB2312 我喜欢utf-8 据说utf8在某些客户端收信下会乱码
        $mail->CharSet = 'UTF-8';

        //设置发件人姓名（昵称） 任意内容，显示在收件人邮件的发件人邮箱地址前的发件人姓名
        $mail->FromName = 'common-curd';

        //smtp登录的账号 这里填入字符串格式的qq号即可
        $mail->Username ='1393170420@qq.com';

        //smtp登录的密码 使用生成的授权码（就刚才叫你保存的最新的授权码）
        $mail->Password = 'cabxgafeypwiggcc';

        //设置发件人邮箱地址 这里填入上述提到的“发件人邮箱”
        $mail->From = '1393170420@qq.com';

        //邮件正文是否为html编码 注意此处是一个方法 不再是属性 true或false
        $mail->isHTML(true); 

        //设置收件人邮箱地址 该方法有两个参数 第一个参数为收件人邮箱地址 第二参数为给该地址设置的昵称 不同的邮箱系统会自动进行处理变动 这里第二个参数的意义不大
        $mail->addAddress($to, $toName);

        //添加多个收件人 则多次调用方法即可
        // $mail->addAddress('xxx@163.com','lsgo在线通知');

        //添加该邮件的主题
        $mail->Subject = $title;

        //添加邮件正文 上方将isHTML设置成了true，则可以是完整的html字符串 如：使用file_get_contents函数读取本地的html文件
        $mail->Body = $content;

        //为该邮件添加附件 该方法也有两个参数 第一个参数为附件存放的目录（相对目录、或绝对目录均可） 第二参数为在邮件附件中该附件的名称
        // $mail->addAttachment('./d.jpg','mm.jpg');
        //同样该方法可以多次调用 上传多个附件
        // $mail->addAttachment('./Jlib-1.1.0.js','Jlib.js');

        $status = $mail->send();

        //简单的判断与提示信息
        if($status) {
            return true;
        }else{
            return false;
        }
    }


    /**s
     * Discuz! 经典加密解密函数
     * @param  string  $string      明文 或 密文
     * @param  string  $operation   DECODE表示解密,其它表示加密
     * @param  string  $key         密钥
     * @param  integer $expiry      密文有效期
     */
    function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
        // 动态密匙长度，相同的明文会生成不同密文就是依靠动态密匙
        $ckey_length = 4;

        // 密匙
        $key = md5($key ? $key : '.#CLOAK#.');

        // 密匙a会参与加解密
        $keya = md5(substr($key, 0, 16));
        // 密匙b会用来做数据完整性验证
        $keyb = md5(substr($key, 16, 16));
        // 密匙c用于变化生成的密文

        $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';
        // 参与运算的密匙
        $cryptkey = $keya.md5($keya.$keyc);
        $key_length = strlen($cryptkey);
        // 明文，前10位用来保存时间戳，解密时验证数据有效性，10到26位用来保存$keyb(密匙b)，解密时会通过这个密匙验证数据完整性
        // 如果是解码的话，会从第$ckey_length位开始，因为密文前$ckey_length位保存 动态密匙，以保证解密正确
        $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
        $string_length = strlen($string);
        $result = '';
        $box = range(0, 255);
        $rndkey = array();
        // 产生密匙簿
        for($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($cryptkey[$i % $key_length]);
        }
        // 用固定的算法，打乱密匙簿，增加随机性，好像很复杂，实际上对并不会增加密文的强度
        for($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];

            $box[$j] = $tmp;
        }
        // 核心加解密部分
        for($a = $j = $i = 0; $i < $string_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];

            $box[$j] = $tmp;
            // 从密匙簿得出密匙进行异或，再转成字符
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }
        if($operation == 'DECODE') {
            // substr($result, 0, 10) == 0 验证数据有效性
            // substr($result, 0, 10) - time() > 0 验证数据有效性
            // substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16) 验证数据完整性
            // 验证数据有效性，请看未加密明文的格式
            if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
                return substr($result, 26);
            } else {
                return '';
            }
        } else {
            // 把动态密匙保存在密文里，这也是为什么同样的明文，生产不同密文后能解密的原因
            // 因为加密后的密文可能是一些特殊字符，复制过程可能会丢失，所以用base64编码
            return $keyc.str_replace('=', '', base64_encode($result));
        }
    }

  }
