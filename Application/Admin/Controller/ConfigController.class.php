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
class ConfigController extends AdminController{
    /**
     * 配置列表
     * @author jry <598821125@qq.com>
     */
    public function index($group = 1){
        $map['group'] = array('eq', $group);
        $page = new \Think\Page(D('Config')->where($map)->count(), C('ADMIN_PAGE_ROWS'));
        $this->assign('page', $page->show());
        $this->assign('current_group', $group);
        $this->assign('config_groups', C('CONFIG_GROUP_LIST'));
        $this->assign('volist', $this->int_to_icon(D('Config')->page(!empty($_GET["p"])?$_GET["p"]:1, C('ADMIN_PAGE_ROWS'))->getAllConfigByGroup($group)));
        $this->meta_title = '配置管理';
        $this->display();
    }

    /**
     * 新增配置
     * @author jry <598821125@qq.com>
     */
    public function add(){
        if(IS_POST){
            $Config = D('Config');
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
            $Config = D('Config');
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
            $info = D('Config')->getConfigById($id);
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
        $config_list = D('Config')->getAllConfigByGroup($group);
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
            $config_model = D('Config');
            foreach ($config as $name => $value){
                $map = array('name' => $name);
                $config_model->where($map)->setField('value', $value);
            }
        }
        S('DB_CONFIG_DATA',null);
        $this->success('保存成功！');
    }
}
