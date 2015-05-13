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
 * 系统配置控制器
 * @author jry <598821125@qq.com>
 */
class SystemConfigController extends AdminController{
    /**
     * 配置列表
     * @param $tab 配置分组ID
     * @author jry <598821125@qq.com>
     */
    public function index($tab = 1){
        //搜索
        $keyword = (string)I('keyword');
        $condition = array('like','%'.$keyword.'%');
        $map['id|name|title'] = array($condition, $condition, $condition,'_multi'=>true);

        //获取所有配置
        $map['status'] = array('egt', '0'); //禁用和正常状态
        $map['group'] = array('eq', $tab);
        $data_list = D('SystemConfig')->page(!empty($_GET["p"])?$_GET["p"]:1, C('ADMIN_PAGE_ROWS'))->where($map)->order('sort asc,id asc')->select();
        $page = new \Think\Page(D('SystemConfig')->where($map)->count(), C('ADMIN_PAGE_ROWS'));

        //使用Builder快速建立列表页面。
        $builder = new \Admin\Builder\AdminListBuilder();
        $builder->title('配置列表')  //设置页面标题
                ->AddNewButton()    //添加新增按钮
                ->addResumeButton() //添加启用按钮
                ->addForbidButton() //添加禁用按钮
                ->addDeleteButton() //添加删除按钮
                ->setSearch('请输入ID/配置名称/配置标题', U('index', array('group' => $group)))
                ->SetTablist(C('CONFIG_GROUP_LIST')) //设置Tab按钮列表
                ->SetCurrentTab($tab) //设置当前Tab
                ->addField('id', 'UID', 'text')
                ->addField('name', '名称', 'text')
                ->addField('title', '标题', 'text')
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
     * 新增配置
     * @author jry <598821125@qq.com>
     */
    public function add(){
        if(IS_POST){
            $Config = D('SystemConfig');
            $data = $Config->create();
            if($data){
                if($Config->add()){
                    S('DB_CONFIG_DATA',null);
                    $this->success('新增成功', U('index'));
                }else{
                    $this->error('新增失败');
                }
            }else{
                $this->error($Config->getError());
            }
        }else{
            $this->assign('config_groups', C('CONFIG_GROUP_LIST'));
            $this->assign('config_types', C('FORM_ITEM_TYPE'));
            $this->assign('info',null);
            $this->meta_title = '新增配置';
            $this->display('edit');
        }
    }

    /**
     * 编辑配置
     * @author jry <598821125@qq.com>
     */
    public function edit($id){
        if(IS_POST){
            $Config = D('SystemConfig');
            $data = $Config->create();
            if($data){
                if($Config->save()){
                    S('DB_CONFIG_DATA',null);
                    $this->success('更新成功', U('index'));
                }else{
                    $this->error('更新失败');
                }
            }else{
                $this->error($Config->getError());
            }
        }else{
            $info = D('SystemConfig')->getConfigById($id);
            $this->assign('info', $info);
            $this->assign('config_groups', C('CONFIG_GROUP_LIST'));
            $this->assign('config_types', C('FORM_ITEM_TYPE'));
            $this->meta_title = '编辑配置';
            $this->display();
        }
    }

    /**
     * 获取某个分组的配置参数
     * @author jry <598821125@qq.com>
     */
    public function group($group = 1){
        $config_list = D('SystemConfig')->getAllConfigByGroup($group);
        foreach($config_list as $key => $val){
            $config_list[$key]['name'] = 'config['.$val['name'].']';
        }
        $this->assign('form_items', $config_list);
        $this->assign('config_groups', C('CONFIG_GROUP_LIST'));
        $this->assign('current_group', $group);
        $this->meta_title = $config_groups[$group].'设置';
        $this->display();
    }

    /**
     * 批量保存配置
     * @author jry <598821125@qq.com>
     */
    public function save($config){
        if($config && is_array($config)){
            $config_model = D('SystemConfig');
            foreach ($config as $name => $value){
                $map = array('name' => $name);
                $config_model->where($map)->setField('value', $value);
            }
        }
        S('DB_CONFIG_DATA',null);
        $this->success('保存成功！');
    }
}
