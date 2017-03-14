<?php
/**
 * 通行证控制器类
 * @author 蔡繁荣 <fanrong33@qq.com>
 * @version 1.0.8 build 20170204
 */
class PassportAction extends Action{


    /**
     * 注册帐号
     */
    public function register(){
        $model = D('User');

        if($this->isPost()){

            $_validate = array(
                array('username' , 'require', '用户名为空', 1),
                array('password' , 'require', '密码为空', 1),
                array('email'    , 'require', '邮箱为空', 1),
                array('username' , '/^([a-zA-Z]){1}([a-zA-Z0-9_\-]){5,17}$/', '用户名格式错误', 1),
                array('email'    , 'email'  , '邮箱格式错误', 1),
                array('email'    , ''       , '该邮箱已经存在', 1, 'unique'), // in  , between,  length,  等...
                array('password' , '6,20'   , '密码必须为6-20位字符串', 1, 'length'),
                array('confirm_password', 'password', '确认密码与新的密码不一致', 1, 'confirm'),
            );
            // 关闭字段信息的自动检测
            // $model->setProperty('autoCheckFields', false);
            if(false === $data = $model->validate($_validate)->create()){
                $this->error($model->getError());
            }
            
            $data['password']    = encrypt_pwd($data['password']);
            $data['update_time'] = time();
            $data['create_time'] = time();

            $user_id = $model->add($data);
            if($user_id){

                // 记住用户名
                // cookie('remeber_me', $data['email'], 3600*24*30);


                // 发送注册成功邮件
                // import('@.ORG.Api.Email.EmailApiClient', '', '.php');
                // $client = new EmailApiClient('', '');

                // $method = 'xiaobai.email.send_email';
                // $email_params = array('email'=>$data['email']);
                // $params = array(
                //     'email_address'  => $data['email'],
                //     'template_alias' => 'register_user',
                //     'subject'        => 'register success',
                //     'email_params'   => json_encode($email_params),
                // );

                // $json = $client->post($method, $params);
                
                $this->success('帐号创建成功');
            }else{
                $this->error('帐号创建失败');
            }
            exit;
        }

        $this->assign('_title', '注册新用户 - bricks');
        $this->display();
    }


    /**
     * 账号登录
     */
    public function login(){
        $model = D('User');

        if($this->isPost()){

            $username   = $_POST['username'];
            $password   = $_POST['password'];
            $remeber_me = $_POST['remeber_me'];

            if($username == ''){
                $this->error('登录名为空');
            }
            if($password == ''){
                $this->error('登录密码为空');
            }

            $cond = array();
            if($model->check($username, 'email', 'regex')){
                $cond['email'] = $username;
            }else{
                $cond['username'] = $username;
            }

            $user = $model->where($cond)->find();
            if(!$user || encrypt_pwd($password) != $user['password']){
                $this->error('登录名或者密码错误');
            }

            session('user', $user);

            // 更新开发者上次登录时间
            $data = array(
                'last_login_time' => time(),
                'update_time'     => time(),
            );
            $model->where(array('id'=>$user['id']))->save($data);

            // 记住我
            if($remeber_me == 1){
                import('@.ORG.Util.RemeberMe');
                RemeberMe::set('remeber_me', $user['id'], 3600*24*10);
            }
            $this->success('登录成功', U('Dashboard/index'));
        }
        

        // 如果已经登录，则直接跳转到面板首页
        $developer = session('developer');
        if($developer){
            U('Dashboard/index', array(), array(), true);
        }

        // 未登录，则判断remeber_me cookie是否已登录
        import('@.ORG.Util.RemeberMe');
        $developer_id = RemeberMe::get('remeber_me');

        if($developer_id){
            $developer = $model->find($developer_id);
            if($developer){
                session('developer', $developer);

                // 更新开发者上次登录时间
                $data = array(
                    'lastLoginTime' => time(),
                    'updateTime'    => time(),
                );
                $model->where(array('id'=>$developer['id']))->save($data);

                U('Dashboard/index', array(), array(), true);
            }
        }

        $this->assign('_title', '账号登录 - Superlinkin');
        $this->display();
    }


    /**
     * 忘记密码，发送重置密码邮件
     */
    public function forget(){
        $model = D('Developer');

        if($this->isPost()){
            $email = trim($this->_post('email'));

            if($email == ''){
                $this->ajaxReturn('', '电子邮箱为空', 0);
            }

            if(!$model->check($email, 'email', 'regex')){
                $this->ajaxReturn('', '邮箱格式错误', 0);
            }

            $cond = array(
                'email' => $email,
            );
            $developer = $model->where($cond)->find();
            if(!$developer){
                $this->ajaxReturn('', '账号不存在', 0);
            }


            // 1、生成验证码，保存到数据库
            $verify_code = rand_string(10);

            $data = array(
                'verifyCode' => $verify_code,
            );
            $effect = $model->where(array('id'=>$developer['id']))->save($data);
            

            $verify_code = urlencode(authcode($verify_code, 'ENCODE', '', 60*30));

            $reset_password_url = U('Passport/reset_password', array(), true, false, true);
            $reset_password_url .= '?email='.$email.'&verify_code='.$verify_code;

            // 并发送重置密码邮件到广告主邮箱
            import('@.ORG.Api.Email.EmailApiClient', '', '.php');
            $client = new EmailApiClient('', '');

            $method = 'xiaobai.email.send_email';
            $email_params = array('reset_password_url'=>$reset_password_url);
            $params = array(
                'email_address'  => $email,
                'template_alias' => 'reset_password',
                'subject'        => 'bricks - 账户密码重置',
                'email_params'   => json_encode($email_params),
            );

            $json = $client->post($method, $params);
            if($json['status'] == 1){
                $this->ajaxReturn('', '重置密码邮件已发送成功，请查收邮箱！', 1);
            }else{
                $this->ajaxReturn('', '邮件发送失败', 0);
            }
        }

        $this->assign('_title', '找回密码 - Superlinkin');
        $this->display();
    }


    /**
     * 重置密码
     */
    public function reset_password(){

        $model = D('Developer');

        $email       = $this->_get('email');
        $verify_code = $this->_get('verify_code');

        $verify_code = str_replace(' ', '+', $verify_code);
        $verify_code = authcode($verify_code, 'DECODE');


        if(!$verify_code){
            redirect(U('Passport/login'));
        }
        $developer = $model->where(array('email'=>$email))->find();

        if($verify_code != $developer['verifyCode']){
            redirect(U('Passport/login'));   
        }


        if($this->isPost()){

            $_validate = array(
                array('password' , '6,20', '密码必须为6-20位字符串', 1, 'length'),
                array('confirm_password', 'password', '确认密码与新的密码不一致', 1, 'confirm'),
            );

            // 关闭字段信息的自动检测
            // $model->setProperty('autoCheckFields', false);
            if(false === $data = $model->validate($_validate)->create()){
                $this->error($model->getError());
            }

            $data = array(
                'password'   => encrypt_pwd($data['password']),
                'verifyCode' => '',
                'updateTime' => time(),
            );
            $effect = $model->where(array('id'=>$developer['id']))->save($data);
            if($effect){
                $this->ajaxReturn('', '修改密码成功', 1);
            }else{
                $this->ajaxReturn('', '修改密码失败', 0);
            }
        }


        $this->assign('email', $email);
        $this->assign('_title', '重置密码 - Superlinkin');
        $this->display();
    }


    /**
     * 退出登录
     */
    public function logout(){
        session('user', null);
        cookie('remeber_me', null); // 删除记住我cookie
        redirect('/');
    }


}

?>