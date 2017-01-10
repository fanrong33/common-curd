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


    /** 
     * 修改密码
     */
    public function edit_password(){
        $model = D('Users');

        if ($this->isPost()) {

            $_validate = array(
                array('current_password', 'require'  , '当前密码为空', 1),
                array('new_password'    , 'require'  , '新的密码为空', 1),
                array('confirm_password', 'require'  , '确认密码不能为空', 1),
                array('new_password'    , '6,20'     , '密码必须6-20位字符串', 1, 'length'), // in  , between,  length,  等...
                array('confirm_password', 'new_password', '确认密码与新的密码不一致', 1, 'confirm'),
            );

            $model->setProperty('autoCheckFields', false);
            if(false === $data = $model->validate($_validate)->create()){
                $this->error($model->getError());
            }


            $user = $model->find($this->$_SESSION['user']['id']);
            if(md5($data['current_password']) != $user['password']){
                $this->error('当前密码错误');
            }

            $hash_password = md5($data['new_password']);


            $data = array();
            $data['password']   = $hash_password;
            $data['updateTime'] = time();
            $effect = $model->where(array('id'=>$this->$_SESSION['user']['id']))->save($data);
            
            if($effect){
                $this->success('修改成功');
            }else{
                $this->error('修改失败');
            }
        
        }

        $this->display();
    }

}