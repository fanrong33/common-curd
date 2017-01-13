<?php
/**
 * 上传文件管理控制器类
 * @author 肖代敏
 * @version  1.0.1 build 20170112
 */
class UploadfileAction extends Action{

	public function index(){
		$img = D('Images');
		$sel=$img->order('create_time desc')->find();
        $this->assign('data', $sel);	
		$this->display();
	}

    public function upload(){
        $upload_img=M('Images');
          	if(!empty($_FILES)){
              //上传单个图像
                import('ORG.Net.UploadFile');
				$upload                    = new UploadFile();// 实例化上传类
                $upload->maxSize           = 1*1024*1024 ;// 设置附件上传大小
                $upload->exts              = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
                $upload->savePath          = 'home/Uploads/'; // 设置附件上传根目录
                $upload->saveName          = array('uniqid','');//上传文件的保存规则
                $upload->subName           = array('date','Ymd');
                $upload->thumb             = true;
			    $upload->thumbMaxWidth     = '50,200';
			    $upload->thumbMaxHeight    = '50,200';
			    $upload->thumbPrefix       = 'm_,s_';			    
				$upload->thumbRemoveOrigin = true;//设置生成缩略图后移除原图
                // 上传单个图片
                $info   =   $upload->uploadOne($_FILES['image']);       
                if(!$info) {// 上传错误提示错误信息
                    $this->error($upload->getErrorMsg());
                }else{// 上传成功 获取上传文件信息
                     $img_url=$info[0]['savepath'].$info[0]['savename'];
                     $data['img_url']=$img_url;
                     $data['img_name']=$info[0]['savename'];
                     $data['create_time']=NOW_TIME;
                     // var_dump($data);die;
                     $upload_img->create($data);
                     $result=$upload_img->add();
                     if(!$result){
                         $this->error('上传失败！');
                     }else{
                         $this->ajaxReturn('','上传成功',1);
                     }
                }
            }
    }

}

