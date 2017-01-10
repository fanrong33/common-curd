<?php
/**
 * 账号管理控制器类
 * @author 肖代敏
 * @version  1.0.3 build 2017106
 */
class MyinfoAction extends Action{

    public function index(){

        $model = D('Users');
        
        if($this->isPost()){

            $_POST['name']     = trim($this->_post('name'));
            $_POST['email']    = trim($this->_post('email'));
            $_POST['postcode'] = trim($this->_post('postcode'));
            $_POST['mobile']   = trim($this->_post('mobile'));
            $_POST['address']  = trim($this->_post('address'));


            $_validate = array(
                array('email' , 'email'   , '邮箱格式错误', 1), // 默认正则regex, email, url, integer, number, double
            );

            if(false === $data = $model->validate($_validate)->create()){
                $this->error($model->getError());
            }
         var_dump($data);die;
           
            $data['user_id'] = $_SESSION['user']['id'];
            $insert_id = $model->add($data);
            if($insert_id){
                $this->ajaxReturn(array('id'=>$insert_id), '保存成功', 1);
            }else{
                $this->success('保存失败');
            }


      
            
        }

        //获取用户信息
         // 获取用户的group列表
      
        $cond['id'] = $_SESSION['user']['id'];
        $user = D('Users')->where($cond)->find();

        $this->assign('model', $user);

        $this->display();
    }


    public function edit_password(){
        $users = D('Users');

        if ($this->isPost()) {

            $_POST['password'] = trim($this->_post('password'));
            
             
       
            if(md5($_POST['password']) != $_SESSION['user']['password']){
                $this->error('原密码错误');
            }else{

                $_POST['new_password'] = trim($this->_post('new_password'));
                $_POST['confirm_password'] = trim($this->_post('confirm_password'));

                $_validate = array(
                    array('new_password' , 'require' , '新密码为空' , 1),
                    array('confirm_password', 'new_password', '确认密码不正确', 1, 'confirm'),
                );

                  if(false === $data = $users->validate($_validate)->create()){
                    $this->error($users->getError());
                }
                var_dump($data);
                $cond['password'] = $data['new_password'];
                var_dump($cond);die;
             
                    if ($users->where($cond)->save()) {
                       $this->ajaxReturn(array('id'=>$data['id']), '修改密码成功', 1);
                    }else{
                        $this->error('修改密码失败');
                    }
                }
        
            }
        $this->display();

    }

}