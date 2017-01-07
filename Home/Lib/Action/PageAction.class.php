<?php
/**
 * 页面管理控制器类
 * @author 蔡繁荣
 * @version  1.0.3 build 20161206
 */
class PageAction extends Action{


    public function index(){
        $model = D('Page');

        // 获取请求参数
        $keyword   = isset($_GET['keyword']) ? $_GET['keyword'] : '';
        $group_id  = isset($_GET['group_id']) ? $_GET['group_id'] : '';
        $order_by  = $_GET['order_by'] ? $_GET['order_by'] : 'id';
        $direction = $_GET['direction'] ? $_GET['direction'] : 'desc';


        // 组装过滤条件
        $cond = array();
        if($keyword != ''){
            $cond['name'] = array('like', "%$keyword%");
            $this->assign('keyword', $keyword);
        }
        if($group_id != ''){
            $cond['group_id'] = $group_id;
        }
        $cond['is_deleted'] = 0;
        $cond['user_id'] = $_SESSION['user']['id'];


        $count = $model->where($cond)->count();
        
        import('@.ORG.Util.Page');
        $page = new Page($count, 5);
        $page->setConfig('first', 'First');
        $page->setConfig('last' , 'Last');
        $page->setConfig('prev' , '&laquo;');
        $page->setConfig('next' , '&raquo;');
        $page->setConfig('theme', '%first% %upPage% %nowPage% %downPage% %end%');

        
        if($count > 0){
            $page_list = D('PageView')->where($cond)->order($order_by.' '.$direction)->limit($page->firstRow, $page->listRows)->select();
        }else{
            $page_list = array();
        }

        

        // 获取group列表 (cache？), 后台压根就不需要做什么缓存，好吗！
        $cond = array();
        $cond['is_deleted'] = 0;
         $cond['user_id'] = $_SESSION['user']['id'];
        $group_list = D('Group')->where($cond)->select();
        $group_map = array_to_map($group_list);

        
        $group_id_text = '';
        if($group_id != ''){
            $group = $group_map[$group_id];

            $group_id_text = $group['name'];
        }
        // var_dump($group_id_text);
        
        $this->assign('list', $page_list);
        $this->assign('page', $page->shows());
        $this->assign('group_list', $group_list);
        
        $this->assign('group_id', $group_id);
        $this->assign('group_id_text', $group_id_text);
        $this->assign('order_by', $order_by);
        $this->assign('direction', $direction);
        $this->display();
    }


    public function add(){
        $model = D('Page');

        if($this->isPost()){

            $_POST['name'] = trim($this->_post('name'));
            $_POST['url']  = trim($this->_post('url'));

            $_validate = array(
                array('name' , 'require'   , '名称为空', 1), // 默认正则regex, email, url, integer, number, double
                array('url'  , 'require'   , 'url为空', 1),
                array('url'  , '/^http|https:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"\"])*$/', 'url格式错误', 1),
            );

            if(false === $data = $model->validate($_validate)->create()){
                $this->error($model->getError());
            }


            // 判断page名称是否唯一
            $cond = array();
            $cond['name']       = $data['name'];
            $cond['is_deleted'] = 0;
            if($model->where($cond)->find()){
                $this->error('名称已经存在');
            }

            $data['update_time'] = time();
            $data['create_time'] = time();
            $data['user_id'] = $_SESSION['user']['id'];

            $insert_id = $model->add($data);
            if($insert_id){
                $this->ajaxReturn(array('id'=>$insert_id), '添加成功', 1);
            }else{
                $this->success('添加失败');
            }
        }


        // 获取用户的group列表
        $cond = array();
        $cond['is_deleted'] = 0;
        $cond['user_id'] = $_SESSION['user']['id'];
        $group_list = D('Group')->field('id,name')->where($cond)->order('id asc')->select();


        $this->assign('group_list', $group_list);
        $this->display();
    }


    public function edit(){
        $model = D('Page');

        if($this->isPost()){

            $_POST['name'] = trim($this->_post('name'));
            $_POST['url']  = trim($this->_post('url'));

            $_validate = array(
                array('name' , 'require'   , '名称为空', 1), // 默认正则regex, email, url, integer, number, double
                array('url'  , 'require'   , 'url为空', 1),
                array('url'  , '/^http|https:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"\"])*$/', 'url格式错误', 1),
            );

            if(false === $data = $model->validate($_validate)->create()){
                $this->error($model->getError());
            }

            $page = $model->find($data['id']);
            if(!$page || $page['is_deleted']){
                $this->error('页面不存在');
            }

 
            // 判断page名称是否唯一
            $cond = array();
            $cond['id']         = array('neq', $data['id']);
            $cond['name']       = $data['name'];
            $cond['is_deleted'] = 0;
            if($model->where($cond)->find()){
                $this->error('名称已经存在');
            }


            $data['update_time'] = time();
            $effect = $model->where(array('id'=>$data['id']))->save($data);
            if($effect){
                // $this->success('save success');
                $this->ajaxReturn(array('id'=>$data['id']), '保存成功', 1);
            }else{
                $this->success('保存失败');
            }
            exit;
        }

        $page_id = intval($_GET['id']);

        $page = $model->find($page_id);
        if(!$page || $page['is_deleted']){
            $this->error('页面不存在');
        }


        // 获取用户的group列表
        $cond = array();
        $cond['is_deleted'] = 0;
         $cond['user_id'] = $_SESSION['user']['id'];
        $group_list = D('Group')->field('id,name')->where($cond)->order('id asc')->select();


        $this->assign('model', $page);
        $this->assign('group_list', $group_list);
        $this->display('Page/add');
    }


    public function delete(){
        $model = D('Page');
        if($this->isPost()){

            $page_id = intval ($this->_post('page_id'));

            $page = $model->find($page_id);
            if(!$page || $page['is_deleted']){
                $this->error('页面不存在');
            }

            $data = array(
                'is_deleted'  => 1,
                'update_time' => time(),
            );
            $effect = $model->where(array('id'=>$page_id))->save($data);
            if($effect){
                $this->success('删除成功');
            }else{
                $this->success('删除失败');
            }
        }
    }


}

?>