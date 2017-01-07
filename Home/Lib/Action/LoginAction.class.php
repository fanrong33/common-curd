<?php
/**
 * 登录管理控制器类
 * @author 肖代敏
 * @version  1.0.0 build 2017.1.6
 */
class LoginAction extends Action{

   /**
    * 方法名：index() 获取登录界面
    * @return[void]
    */
    public function index(){

        $model = D('User');

        if ($this->isPost()) {
            $_POST['username'] = trim($this->_post('username'));
            
            $_validate = array(
                array('username' , 'require' , '用户名为空' , 1),
                array('password' , 'require' , '密码为空' , 1),           
            );
            if(false === $data = $model->validate($_validate)->create()){
                $this->error($model->getError());
            }


            //判断用户名和密码是否正确
            $cond = array(
                'username'   => $data['username'],
                'is_deleted' => 0,
            );
            $user = $model->where($cond)->find();
            if(!$user){
                $this->error('用户不存在');
            }

            if(md5($data['password']) != $user['password']){
                $this->error('密码错误');
            }

            $_SESSION['user'] = $user;
            $this->ajaxReturn(array('id'=>$user['id']),'登录成功',1);
        }
            $this->display('Login/index');
    }  



    /**
    * 方法名：logout() 退出登录
    * @return
    */
    public function logout()
    {
        unset($_SESSION['user']);
        $this->redirect('Login/index');
    }

}