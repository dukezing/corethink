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
 * 扩展后台管理页面
 * @author jry <598821125@qq.com>
 */
class AddonController extends AdminController {
    /**
     * 插件列表
     * @author jry <598821125@qq.com>
     */
    public function index(){
        //获取所有插件信息
        $addons = D('Addon')->page(!empty($_GET["p"])?$_GET["p"]:1, C('ADMIN_PAGE_ROWS'))->getAllAddon();
        $page = new \Common\Util\Page(D('Addon')->count(), C('ADMIN_PAGE_ROWS'));

        //使用Builder快速建立列表页面。
        $builder = new \Common\Builder\ListBuilder();
        $builder->title('插件列表')  //设置页面标题
                ->addResumeButton() //添加启用按钮
                ->addForbidButton() //添加禁用按钮
                ->addField('name', '标识', 'text')
                ->addField('title', '名称', 'text')
                ->addField('description', '描述', 'text')
                ->addField('status', '状态', 'text')
                ->addField('author', '作者', 'text')
                ->addField('version', '版本', 'text')
                ->dataList($addons) //数据列表
                ->addField('right_button', '操作', 'btn')
                ->setPage($page->show())
                ->display();
    }

    /**
     * 设置插件页面
     * @author jry <598821125@qq.com>
     */
    public function config(){
        if(IS_POST){
            $id     =   (int)I('id');
            $config =   I('config');
            $flag = M('Addon')->where("id={$id}")->setField('config', json_encode($config));
            if($flag !== false){
                $this->success('保存成功', U('index'));
            }else{
                $this->error('保存失败');
            }
        }else{
            $id     =   (int)I('id');
            $addon  =   M('Addon')->find($id);
            if(!$addon)
                $this->error('插件未安装');
            $addon_class = get_addon_class($addon['name']);
            if(!class_exists($addon_class))
                trace("插件{$addon['name']}无法实例化,",'ADDONS','ERR');
            $data  =   new $addon_class;
            $addon['addon_path'] = $data->addon_path;
            $addon['custom_config'] = $data->custom_config;
            $this->meta_title   =   '设置插件-'.$data->info['title'];
            $db_config = $addon['config'];
            $addon['config'] = include $data->config_file;
            if($db_config){
                $db_config = json_decode($db_config, true);
                foreach ($addon['config'] as $key => $value) {
                    if($value['type'] != 'group'){
                        $addon['config'][$key]['value'] = $db_config[$key];
                    }else{
                        foreach ($value['options'] as $gourp => $options) {
                            foreach ($options['options'] as $gkey => $value) {
                                $addon['config'][$key]['options'][$gourp]['options'][$gkey]['value'] = $db_config[$gkey];
                            }
                        }
                    }
                }
            }
            //构造表单名
            foreach($addon['config'] as $key => $val){
                if($val['type'] == 'group'){
                    foreach($val['options'] as $key2 => $val2){
                        foreach($val2['options'] as $key3 => $val3){
                            $addon['config'][$key]['options'][$key2]['options'][$key3]['name'] = 'config['.$key3.']';
                        }
                    }
                }else{
                    $addon['config'][$key]['name'] = 'config['.$key.']';
                }
            }
            $this->assign('data', $addon);
            $this->assign('form_items', $addon['config']);
            if($addon['custom_config']){
                $this->assign('custom_config', $this->fetch($addon['addon_path'].$addon['custom_config']));
                $this->display();
            }else{
                //使用FormBuilder快速建立表单页面。
                $builder = new \Common\Builder\FormBuilder();
                $builder->title('插件设置')  //设置页面标题
                        ->setUrl(U('config')) //设置表单提交地址
                        ->addItem('id', 'hidden', 'ID', 'ID')
                        ->setExtraItems($addon['config']) //直接设置表单数据
                        ->setFormData($addon)
                        ->display();
            }
        }
    }

    /**
     * 安装插件
     * @author jry <598821125@qq.com>
     */
    public function install(){
        $addon_name = trim(I('addon_name'));
        $class = get_addon_class($addon_name);
        if(!class_exists($class))
            $this->error('插件不存在');
        $addons  = new $class;
        $info = $addons->info;
        if(!$info || !$addons->checkInfo())//检测信息的正确性
            $this->error('插件信息缺失');
        session('addons_install_error',null);
        $install_flag = $addons->install();
        if(!$install_flag){
            $this->error('执行插件预安装操作失败'.session('addons_install_error'));
        }
        $addonsModel = D('Addon');
        $data = $addonsModel->create($info);
        if(is_array($addons->admin_list) && $addons->admin_list !== array()){
            $data['adminlist'] = 1;
        }else{
            $data['adminlist'] = 0;
        }
        if(!$data)
            $this->error($addonsModel->getError());
        if($addonsModel->add($data)){
            $config = array('config'=>json_encode($addons->getConfig()));
            $addonsModel->where("name='{$addon_name}'")->save($config);
            $hooks_update = D('AddonHook')->updateHooks($addon_name);
            if($hooks_update){
                S('hooks', null);
                $this->success('安装成功');
            }else{
                $addonsModel->where("name='{$addon_name}'")->delete();
                $this->error('更新钩子处插件失败,请卸载后尝试重新安装');
            }
        }else{
            $this->error('写入插件数据失败');
        }
    }

    /**
     * 卸载插件
     * @author jry <598821125@qq.com>
     */
    public function uninstall(){
        $addonsModel = M('Addon');
        $id = trim(I('id'));
        $db_addons = $addonsModel->find($id);
        $class = get_addon_class($db_addons['name']);
        $this->assign('jumpUrl',U('index'));
        if(!$db_addons || !class_exists($class))
            $this->error('插件不存在');
        session('addons_uninstall_error',null);
        $addons = new $class;
        $uninstall_flag = $addons->uninstall();
        if(!$uninstall_flag)
            $this->error('执行插件预卸载操作失败'.session('addons_uninstall_error'));
        $hooks_update = D('AddonHook')->removeHooks($db_addons['name']);
        if($hooks_update === false){
            $this->error('卸载插件所挂载的钩子数据失败');
        }
        S('hooks', null);
        $delete = $addonsModel->where("name='{$db_addons['name']}'")->delete();
        if($delete === false){
            $this->error('卸载插件失败');
        }else{
            $this->success('卸载成功');
        }
    }

    /**
     * 外部执行插件方法
     * @author jry <598821125@qq.com>
     */
    public function execute($_addons = null, $_controller = null, $_action = null){
        if(C('URL_CASE_INSENSITIVE')){
            $_addons        =   ucfirst(parse_name($_addons, 1));
            $_controller    =   parse_name($_controller,1);
        }

        $TMPL_PARSE_STRING = C('TMPL_PARSE_STRING');
        $TMPL_PARSE_STRING['__ADDONROOT__'] = __ROOT__ . "/Addons/{$_addons}";
        C('TMPL_PARSE_STRING', $TMPL_PARSE_STRING);

        if(!empty($_addons) && !empty($_controller) && !empty($_action)){
            $Addons = A("Addons://{$_addons}/{$_controller}")->$_action();
        } else {
            $this->error('没有指定插件名称，控制器或操作！');
        }
    }

    /**
     * 插件后台显示页面
     * @param string $name 插件名
     * @author jry <598821125@qq.com>
     */
    public function adminList($name, $tab = 1){
        //获取插件实例
        $addon_class = get_addon_class($name);
        if(!class_exists($addon_class)){
            $this->error('插件不存在');
        }else{
            $addon = new $addon_class();
        }

        //获取插件的$admin_list配置
        $admin_list = $addon->admin_list;
        $tab_list = array();
        foreach($admin_list as $key => $val){
            $tab_list[$key] = $val['title'];
        }
        $admin = $admin_list[$tab];
        $param = D('Addons://'.$name.'/'.$admin['model'].'')->adminList;
        if($param){
            //搜索
            $keyword = (string)I('keyword');
            $condition = array('like','%'.$keyword.'%');
            $map['id|'.$param['search_key']] = array($condition, $condition,'_multi'=>true);

            //获取数据列表
            $data_list = M($param['model'])->page(!empty($_GET["p"])?$_GET["p"]:1, C('ADMIN_PAGE_ROWS'))
                                           ->where($map)->field(true)->order($param['order'])->select();
            $page = new \Common\Util\Page(M($param['model'])->where($map)->count(), C('ADMIN_PAGE_ROWS'));

            //使用Builder快速建立列表页面。
            $builder = new \Common\Builder\ListBuilder();
            $builder->title($addon->info['title']) //设置页面标题
                    ->AddNewButton('Admin/Addon/adminAdd/name/'.$name.'/tab/'.$tab) //添加新增按钮
                    ->addResumeButton($param['model']) //添加启用按钮
                    ->addForbidButton($param['model']) //添加禁用按钮
                    ->setSearch('请输入关键字', U('Admin/Addon/adminlist/name/'.$name, array('tab' => $tab)))
                    ->SetTablist($tab_list) //设置Tab按钮列表
                    ->setTabUrl('Admin/Addon/adminlist/name/'.$name)
                    ->SetCurrentTab($tab) //设置当前Tab
                    ->setPage($page->show()) //分页
                    ->dataList($data_list); //数据列表

            //根据插件的list_grid设置后台列表字段信息
            foreach($param['list_grid'] as $key => $val){
                $builder->addField($key, $val['title'], $val['type']);
            }

            $attr['title'] = '编辑';
            $attr['href'] = 'Admin/Addon/adminEdit/name/'.$name.'/tab/'.$tab.'/id/';

            //显示列表
            $builder->addField('right_button', '操作', 'btn')
                    ->addRightButton('self', $attr) //添加编辑按钮
                    ->addRightButton('forbid', $param['model']) //添加禁用/启用按钮
                    ->addRightButton('delete', $param['model']) //添加删除按钮
                    ->display();
        }else{
            $this->error('插件列表信息不正确');
        }
    }

    /**
     * 插件后台数据增加
     * @param string $name 插件名
     * @author jry <598821125@qq.com>
     */
     public function adminAdd($name, $tab){
        //获取插件实例
        $addon_class = get_addon_class($name);
        if(!class_exists($addon_class)){
            $this->error('插件不存在');
        }else{
            $addon = new $addon_class();
        }

        //获取插件的$admin_list配置
        $admin_list = $addon->admin_list;
        $admin = $admin_list[$tab];
        $addon_model_object = D('Addons://'.$name.'/'.$admin['model']);
        $param = $addon_model_object->adminList;
        if($param){
            if(IS_POST){
                $data = $addon_model_object->create();
                if($data){
                    $result = $addon_model_object->add($data);
                }else{
                    $this->error($addon_model_object->getError());
                }
                if($result){
                    $this->success('新增成功', U('Admin/Addon/adminlist/name/'.$name.'/tab/'.$tab));
                }else{
                    $this->error('更新错误');
                }
            }else{
                //使用FormBuilder快速建立表单页面。
                $builder = new \Common\Builder\FormBuilder();
                $builder->title('新增数据')  //设置页面标题
                        ->setUrl(U('admin/addon/adminAdd/name/'.$name.'/tab/'.$tab.'/')) //设置表单提交地址
                        ->setExtraItems($param['field'])
                        ->display();
            }
        }else{
            $this->error('插件列表信息不正确');
        }
     }

    /**
     * 插件后台数据编辑
     * @param string $name 插件名
     * @author jry <598821125@qq.com>
     */
     public function adminEdit($name, $tab, $id){
        //获取插件实例
        $addon_class = get_addon_class($name);
        if(!class_exists($addon_class)){
            $this->error('插件不存在');
        }else{
            $addon = new $addon_class();
        }

        //获取插件的$admin_list配置
        $admin_list = $addon->admin_list;
        $admin = $admin_list[$tab];
        $addon_model_object = D('Addons://'.$name.'/'.$admin['model']);
        $param = $addon_model_object->adminList;
        //dump($addon_model_object);exit();
        if($param){
            if(IS_POST){
                $data = $addon_model_object->create();
                if($data){
                    $result = $addon_model_object->save($data);
                }else{
                    $this->error($addon_model_object->getError());
                }
                if($result){
                    $this->success('更新成功', U('Admin/Addon/adminlist/name/'.$name.'/tab/'.$tab));
                }else{
                    $this->error('更新错误');
                }
            }else{
                //使用FormBuilder快速建立表单页面。
                $builder = new \Common\Builder\FormBuilder();
                $builder->title('编辑数据')  //设置页面标题
                        ->setUrl(U('admin/addon/adminedit/name/'.$name.'/tab/'.$tab.'/')) //设置表单提交地址
                        ->addItem('id', 'hidden', 'ID', 'ID')
                        ->setExtraItems($param['field'])
                        ->setFormData(M($param['model'])->find($id))
                        ->display();
            }
        }else{
            $this->error('插件列表信息不正确');
        }
     }
}
