<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------
namespace Admin\Controller;
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
        $addons = D('Addon')->page(!empty($_GET["p"])?$_GET["p"]:1, C('ADMIN_PAGE_ROWS'))->getAllAddon();
        $addons = $this->int_to_icon($addons, array('status'=>array(-1=>'损坏', 0=>'<i class="icon-ban-circle" style="color:red"></i>', 1=>'<i class="icon-ok" style="color:green"></i>', null=>'未安装')));
        $page = new \Think\Page(D('Addon')->count(), C('ADMIN_PAGE_ROWS'));
        $this->assign('page', $page->show());
        $this->assign('volist', $addons);
        $this->meta_title = '插件列表';
        $this->display();
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
                $builder = new \Admin\Builder\AdminFormBuilder();
                $builder->title('插件设置')  //设置页面标题
                        ->setUrl(U('config')) //设置表单提交地址
                        ->addItem('hidden', 'ID', 'ID', 'id')
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
    public function adminList($name){
        $this->assign('name', $name);
        $addon_class = get_addon_class($name);
        if(!class_exists($addon_class))
            $this->error('插件不存在');
        $addon = new $addon_class();
        $this->assign('addon', $addon);
        $param = $addon->admin_list; //获取插件的$admin_list配置
        if(!$param)
            $this->error('插件列表信息不正确');
        $this->meta_title = $addon->info['title'];
        extract($param);
        $this->assign('title', $addon->info['title']);
        $this->assign($param);
        if(!isset($fields))
            $fields = '*';
        $key = $search_key;
        if(isset($_REQUEST[$key])){
            $map[$key] = array('like', '%'.$_GET[$key].'%');
            unset($_REQUEST[$key]);
        }
        if(isset($model)){
            $model  =  D("Addons://{$name}/{$model}");
            // 条件搜索
            $map = array();
            foreach($_REQUEST as $name=>$val){
                if($fields == '*'){
                    $fields = $model->getDbFields();
                }
                if(in_array($name, $fields)){
                    $map[$name] = $val;
                }
            }
            if(!isset($order)) $order = '';
            $list = $this->lists($model->field($fields),$map,$order);
            $fields = array();
            foreach ($list_grid as &$value) {
                // 字段:标题:链接
                $val = explode(':', $value);
                // 支持多个字段显示
                $field = explode(',', $val[0]);
                $value = array('field' => $field, 'title' => $val[1]);
                if(isset($val[2])){
                    // 链接信息
                    $value['href'] = $val[2];
                    // 搜索链接信息中的字段信息
                    preg_replace_callback('/\[([a-z_]+)\]/', function($match) use(&$fields){$fields[]=$match[1];}, $value['href']);
                }
                if(strpos($val[1],'|')){
                    //显示格式定义
                    list($value['title'],$value['format']) = explode('|',$val[1]);
                }
                foreach($field as $val){
                    $array = explode('|',$val);
                    $fields[] = $array[0];
                }
            }
            $this->assign('model', $model->model);
            $this->assign('list_grid', $list_grid);
        }
        $this->assign('_list', $list);
        if($addon->custom_adminlist)
            $this->assign('custom_adminlist', $this->fetch($addon->addon_path.$addon->custom_adminlist));
        $this->display('adminlist');
    }
}
