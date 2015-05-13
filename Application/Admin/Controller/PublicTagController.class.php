<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------
namespace Admin\Controller;
use Think\Controller;
/**
 * 后台标签控制器
 * @author jry <598821125@qq.com>
 */
class PublicTagController extends AdminController{
    /**
     * 标签列表
     * @author jry <598821125@qq.com>
     */
    public function index(){
        //搜索
        $keyword = (string)I('keyword');
        $condition = array('like','%'.$keyword.'%');
        $map['id|title'] = array($condition, $condition,'_multi'=>true);

        //获取所有标签
        $map['status'] = array('egt', '0'); //禁用和正常状态
        $data_list = D('PublicTag')->page(!empty($_GET["p"])?$_GET["p"]:1, C('ADMIN_PAGE_ROWS'))->where($map)->order('sort desc,id desc')->select();
        $page = new \Think\Page(D('PublicTag')->where($map)->count(), C('ADMIN_PAGE_ROWS'));

        //使用Builder快速建立列表页面。
        $builder = new \Admin\Builder\AdminListBuilder();
        $builder->title('标签列表')  //设置页面标题
                ->AddNewButton()    //添加新增按钮
                ->addResumeButton() //添加启用按钮
                ->addForbidButton() //添加禁用按钮
                ->addDeleteButton() //添加删除按钮
                ->setSearch('请输入ID/标签标题', U('index'))
                ->addField('id', 'ID', 'text')
                ->addField('title', '标签', 'text')
                ->addField('ctime', '创建时间', 'time')
                ->addField('sort', '排序', 'text')
                ->addField('status', '状态', 'status')
                ->addField('right_button', '操作', 'btn')
                ->dataList($data_list)    //数据列表
                ->addRightButton('edit')   //添加编辑按钮
                ->addRightButton('forbid') //添加禁用/启用按钮
                ->addRightButton('delete') //添加删除按钮
                ->setPage($page->show())
                ->display();
    }

    /**
     * 新增标签
     * @author jry <598821125@qq.com>
     */
    public function add(){
        if(IS_POST){
            $Tag = D('PublicTag');
            $data = $Tag->create();
            if($data){
                $id = $Tag->add();
                if($id){
                    $this->success('新增成功', U('index'));
                }else{
                    $this->error('新增失败');
                }
            }else{
                $this->error($Tag->getError());
            }
        }else{
            $this->meta_title = '新增标签';
            $this->display('edit');
        }
    }

    /**
     * 编辑标签
     * @author jry <598821125@qq.com>
     */
    public function edit(){
        if(IS_POST){
            $Tag = D('PublicTag');
            $data = $Tag->create();
            if($data){
                if($Tag->save()!== false){
                    $this->success('更新成功', U('index'));
                }else{
                    $this->error('更新失败');
                }
            }else{
                $this->error($Tag->getError());
            }
        }else{
            $this->assign('info', D('PublicTag')->find($id));
            $this->meta_title = '编辑标签';
            $this->display();
        }
    }

    /**
     * 搜索相关标签
     *@ param string 搜索关键字
     * @return array 相关标签
     * @author jry <598821125@qq.com>
     */
    public function searchTags(){
        $map["title"] = array("like", "%".I('get.q')."%");
        $tags = D('PublicTag')->field('id,title')->where($map)->select();
        foreach($tags as $value){
            $data[] = array('id' => $value['title'], 'title'=> $value['title']);
        }
        echo json_encode($data);
    }
}
