<?php
/**
 * 注册管理控制器类
 * @author 肖代敏, 蔡繁荣
 * @version  1.0.1 build 20170107
 */
class RegisterAction extends Action{

   /**
    * 用户注册
    */
    public function index(){
        $model = D('User');

        //判断提交方式
        if ($this->isPost()) {

            $_POST['username'] = trim($_POST['username']);

            $_validate = array(
                array('username'        , 'require' , '用户名为空' , 1),
                array('password'        , 'require' , '密码为空', 1),
                array('password'        , '6,16'    , '密码必须6-16位', 1, 'length'),
                array('confirm_password', 'password', '确认密码不正确', 1, 'confirm'),
                array('email'           , 'email'   , '邮箱格式有误' , 1),     
            );

            if (false === $data = $model->validate($_validate)->create()) {
                $this->error($model->getError());
            }


            //判断用户名是否唯一
            $cond = array(
                'username'   => $data['username'],
                'is_deleted' => 0,
            );
            if ($model->where($cond)->find()) {
                $this->error('用户名已存在');
            }


            $data['password']    = md5($data['password']);
            $data['update_time'] = time();
            $data['create_time'] = time();

            $insert_id = $model->add($data);
            if ($insert_id) {
                $this->ajaxReturn(array('id'=>$insert_id),'注册成功',1);
            }else{
                $this->error('注册失败');
            }
        }

        $this->display();
    }  


   

}