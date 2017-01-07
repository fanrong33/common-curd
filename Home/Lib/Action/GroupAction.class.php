<?php
/**
 * 分组管理控制器类
 * @author 蔡繁荣
 * @version  1.0.2 build 20161013
 */
class GroupAction extends Action{


    public function index(){
        $model = D('Group');

        $keyword   = isset($_GET['keyword']) ? $_GET['keyword'] : '';

        $cond = array();
        if($keyword != ''){
            $cond['name'] = array('like', "%$keyword%");
            $this->assign('keyword', $keyword);
        }
        $cond['is_deleted'] = 0;
        $cond['user_id'] = $_SESSION['user']['id'];

        $group_list = $model->field('is_deleted,update_time', true)->where($cond)->order('id asc')->select();

        $count = $model->where($cond)->count();
        
        import('@.ORG.Util.Page');
        $page = new Page($count, 5);
        $page->setConfig('first', 'First');
        $page->setConfig('last' , 'Last');
        $page->setConfig('prev' , '&laquo;');
        $page->setConfig('next' , '&raquo;');
        $page->setConfig('theme', '%first% %upPage% %nowPage% %downPage% %end%');

        
        $this->assign('list', $group_list);
        $this->assign('page', $page->shows());
        $this->display();
    }


    public function add(){
        $model = D('Group');

        if($this->isPost()){

            $_POST['name'] = trim($_POST['name']);

            $_validate = array(
                array('name'    , 'require'   , '名称为空', 1), // 默认正则regex, email, url, integer, number, double
            );

            if(false === $data = $model->validate($_validate)->create()){
                $this->error($model->getError());
            }


            // 判断group名称不能相同
            $cond = array();
            $cond['user_id'] = $this->_user['id'];
            $cond['name']    = $data['name'];
            if($model->where($cond)->find()){
                $this->error('名称已存在');
            }

            $data['user_id'] = $_SESSION['user']['id'];
            $data['update_time'] = time();
            $data['create_time'] = time();

            $insert_id = $model->add($data);
            if($insert_id){
                $this->success('添加成功');
            }else{
                $this->success('添加失败');
            }
            exit;
        }

        $this->display();
    }


    public function edit(){
        $model = D('Group');

        if($this->isPost()){

            $_POST['name'] = trim($_POST['name']);

            $_validate = array(
                array('name'    , 'require'   , '名称为空', 1), // 默认正则regex, email, url, integer, number, double
            );

            if(false === $data = $model->validate($_validate)->create()){
                $this->error($model->getError());
            }

            $group_id = intval($_GET['id']);
            if(!$group_id){
                $this->error('参数id不存在');
            }

            $group = $model->find($group_id);
            if(!$group || $group['is_deleted']){
                $this->error('分组不存在');
            }


            // 判断group名称不能相同
            $cond = array();
            $cond['id']      = array('neq', $group_id);
            $cond['name']    = $data['name'];
            if($model->where($cond)->find()){
                $this->error('名称已存在');
            }

            $data['update_time'] = time();


            $effect = $model->where(array('id'=>$group_id))->save($data);
            if($effect){
                $this->success('保存成功');
            }else{
                $this->success('保存失败');
            }
            exit;
        }

        $group_id = intval($_GET['id']);

        $source = $model->find($group_id);
        if(!$source || $source['is_deleted']){
            $this->error('分组不存在');
        }


        $this->assign('model', $source);
        $this->display('Group/add');
    }


    public function delete(){
        $model = D('Group');

        if($this->isPost()){

            $group_id = intval($this->_post('id'));

            $group = $model->find($group_id);
            if(!$group || $group['is_deleted']){
                $this->error('分组不存在');
            }

            // 判断是否关联Page
            $cond = array();
            $cond['group_id'] = $group_id;
            $cond['is_deleted'] = 0;
            $page = D('Page')->where($cond)->find();
            if($page){
                $this->error('分组已关联页面，不允许删除');
            }



            $data = array(
                'is_deleted'  => 1,
                'update_time' => time(),
            );
            $effect = D('Group')->where(array('id'=>$group_id))->save($data);
            if($effect){
                $this->success('删除成功');
            }else{
                $this->success('删除失败');
            }
        }
    }


}

?>