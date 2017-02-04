<?php
/**
 * 登录管理 控制器类
 * @author 肖代敏, 蔡繁荣
 * @version  1.0.2 build 20170204
 */
class LoginAction extends Action{

    /**
     * 用户登录
     */
    public function login(){
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

            $remeber_me = $_POST['remeber_me'];



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

            session('user', $user);

            // 记住我, 10天
            if($remeber_me == 1){
                import('@.ORG.Util.RemeberMe');
                RemeberMe::set('remeber_me', $user['id'], 3600*24*10);
            }

            $this->success('登录成功', U('Page/index'));
        }


        // 如果已经登录，则直接跳转到面板首页
        $user = session('user');
        if($user){
            U('Page/index', array(), true, true);
        }

        // 未登录，则判断remeber_me cookie是否已登录
        import('@.ORG.Util.RemeberMe');
        $user_id = RemeberMe::get('remeber_me');

        if($user_id){
            $user = $model->find($user_id);
            if($user){
                session('user', $user);

                U('Dashboard/index', array(), array(), true);
            }
        }

        $this->display();
    }  



    /**
     * 退出登录
     */
    public function logout(){
        session('user', null);
        cookie('remeber_me', null); // 删除记住我cookie
        U('Login/login', array(), true, true);
    }

}