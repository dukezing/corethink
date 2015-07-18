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
 * 功能模块控制器
 * @author jry <598821125@qq.com>
 */
class StoreModuleController extends AdminController{
    /**
     * 默认方法
     * @author jry <598821125@qq.com>
     */
    public function index(){
        $data_list = D('StoreModule')->page(!empty($_GET["p"])?$_GET["p"]:1, C('ADMIN_PAGE_ROWS'))->getAllModule();
        $page = new \Common\Util\Page(D('StoreModule')->count(), C('ADMIN_PAGE_ROWS'));

        //使用Builder快速建立列表页面。
        $builder = new \Common\Builder\ListBuilder();
        $builder->title('模块列表')  //设置页面标题
                ->AddNewButton()    //添加新增按钮
                ->addResumeButton() //添加启用按钮
                ->addForbidButton() //添加禁用按钮
                ->setSearch('请输入ID/标题', U('index'))
                ->addField('name', '名称', 'text')
                ->addField('title', '标题', 'text')
                ->addField('description', '描述', 'text')
                ->addField('developer', '开发者', 'text')
                ->addField('version', '版本', 'text')
                ->addField('ctime', '创建时间', 'date')
                ->addField('status', '状态', 'text')
                ->addField('right_button', '操作', 'btn')
                ->dataList($data_list) //数据列表
                ->setPage($page->show())
                ->display();
    }

    /**
     * 安装模块
     * @author jry <598821125@qq.com>
     */
    public function install($name){
        //获取当前模块信息
        $config_file = realpath(APP_PATH.$name).'/corethink.php';
        if(!$config_file){
            $this->error('安装失败');
        }
        $config = include $config_file;
        $data = $config['info'];
        $data['admin_menu'] = json_encode($config['admin_menu']);

        //安装数据库
        $sql_status = execute_sql_from_file(realpath(APP_PATH.$name).'/Sql/install.sql');
        if($sql_status){
            //写入数据库记录
            $store_module_object = D('StoreModule');
            $data = $store_module_object->create($data);
            if($data){
                $id = $store_module_object->add();
                if($id){
                    $this->success('安装成功', U('index'));
                }else{
                    $this->error('安装失败');
                }
            }else{
                $this->error($store_module_object->getError());
            }
        }else{
            $sql_status = execute_sql_from_file(realpath(APP_PATH.$name).'/Sql/uninstall.sql');
            $this->error('安装失败');
        }
    }

    /**
     * 更新模块信息
     * @author jry <598821125@qq.com>
     */
    public function updateModuleInfo($id){
        $store_module_object = D('StoreModule');
        $name = $store_module_object->getFieldById($id, 'name');
        $config_file = realpath(APP_PATH.$name).'/corethink.php';
        if(!$config_file){
            $this->error('不存在安装文件');
        }
        $config = include $config_file;
        $data = $config['info'];
        $data['admin_menu'] = json_encode($config['admin_menu']);
        $data['id'] = $id;
        $data = $store_module_object->create($data);
        if($data){
            $id = $store_module_object->save();
            if($id){
                $this->success('更新成功', U('index'));
            }else{
                $this->error('更新失败');
            }
        }else{
            $this->error($store_module_object->getError());
        }
    }

    /**
     * 设置一条或者多条数据的状态
     * @author jry <598821125@qq.com>
     */
    public function setStatus($model = CONTROLLER_NAME){
        $ids    = I('request.ids');
        $status = I('request.status');
        if(empty($ids)){
            $this->error('请选择要操作的数据');
        }
        $map['id'] = array('eq',$ids);
        switch($status){
            case 'uninstall' : //卸载
                $name = D($model)->where($map)->getField('name');
                $result = D($model)->where($map)->delete();
                if($result){
                    $sql_status = execute_sql_from_file(realpath(APP_PATH.$name).'/Sql/uninstall.sql');
                    if($sql_status){
                        $this->success('卸载成功！');
                    }
                }else{
                    $this->error('卸载失败');
                }
                break;
            default :
                parent::setStatus($model);
                break;
        }
    }
}
