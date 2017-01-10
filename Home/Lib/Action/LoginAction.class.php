<?php
/**
 * 登录管理 控制器类
 * @author 肖代敏, 蔡繁荣
 * @version  1.0.1 build 20170110
 */
class LoginAction extends Action{

    /**
     * 用户登录
     */
    public function index(){
        $model = D('User');

        if ($this->isPost()) {

            $_POST['username'] = trim($this->_post('username'));

            
            $_validate = array(
                array('username', 'require', '登录名为空', 1),
                array('password', 'require', '登录密码为空', 1),           
            );
            if(false === $data = $model->validate($_validate)->create()){
                $this->error($model->getError());
            }


            $cond = array();
            if($model->check($data['username'], 'email', 'regex')){
                $cond['email'] = $data['username'];
            }else{
                $cond['username'] = $data['username'];
            }
            $cond['is_deleted'] = 0;


            //判断用户名和密码是否正确
            $user = $model->where($cond)->find();
            if(!$user || md5($data['password']) != $user['password']){
                $this->error('登录名或者密码错误');
            }

            $_SESSION['user'] = $user;

            // 记住登录用户名
            cookie('remeber_me', $data['username'], 3600*24*30);

            $this->ajaxReturn('','登录成功', 1);
        }

        // 如果已经登录，则直接跳转到面板首页
        $user = session('user');
        if($user){
            U('Dashboard/index', array(), true, true);
        }


        $remeber_me = cookie('remeber_me');
        $this->assign('remeber_me', $remeber_me);
        $this->display();
    }  



    /**
     * 退出登录
     */
    public function logout(){
        session_destroy();
        session('user', null);
        U('Login/index', array(), true, true);
    }

}