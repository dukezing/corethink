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
            $config_model = D('SystemConfig');
            $data = $config_model->create();
            if($data){
                if($config_model->add()){
                    S('DB_CONFIG_DATA',null);
                    $this->success('新增成功', U('index'));
                }else{
                    $this->error('新增失败');
                }
            }else{
                $this->error($config_model->getError());
            }
        }else{
            //使用FormBuilder快速建立表单页面。
            $builder = new \Admin\Builder\AdminFormBuilder();
            $builder->title('新增配置')  //设置页面标题
                    ->setUrl(U('add')) //设置表单提交地址
                    ->addItem('select', '配置分组', '配置所属的分组', 'group', C('CONFIG_GROUP_LIST'))
                    ->addItem('select', '配置类型', '配置类型的分组', 'type', C('FORM_ITEM_TYPE'))
                    ->addItem('text', '配置名称', '配置名称', 'name')
                    ->addItem('text', '配置标题', '配置标题', 'title')
                    ->addItem('textarea', '配置值', '配置值', 'value')
                    ->addItem('textarea', '配置项', '如果是单选、多选、下拉等类型 需要配置该项', 'options')
                    ->addItem('textarea', '配置说明', '配置说明', 'tip')
                    ->addItem('num', '排序', '用于显示的顺序', 'sort')
                    ->display();
        }
    }

    /**
     * 编辑配置
     * @author jry <598821125@qq.com>
     */
    public function edit($id){
        if(IS_POST){
            $config_model = D('SystemConfig');
            $data = $config_model->create();
            if($data){
                if($config_model->save()){
                    S('DB_CONFIG_DATA',null);
                    $this->success('更新成功', U('index'));
                }else{
                    $this->error('更新失败');
                }
            }else{
                $this->error($config_model->getError());
            }
        }else{
            //使用FormBuilder快速建立表单页面。
            $builder = new \Admin\Builder\AdminFormBuilder();
            $builder->title('编辑配置')  //设置页面标题
                    ->setUrl(U('edit')) //设置表单提交地址
                    ->addItem('hidden', 'ID', 'ID', 'id')
                    ->addItem('select', '配置分组', '配置所属的分组', 'group', C('CONFIG_GROUP_LIST'))
                    ->addItem('select', '配置类型', '配置类型的分组', 'type', C('FORM_ITEM_TYPE'))
                    ->addItem('text', '配置名称', '配置名称', 'name')
                    ->addItem('text', '配置标题', '配置标题', 'title')
                    ->addItem('textarea', '配置值', '配置值', 'value')
                    ->addItem('textarea', '配置项', '如果是单选、多选、下拉等类型 需要配置该项', 'options')
                    ->addItem('textarea', '配置说明', '配置说明', 'tip')
                    ->addItem('num', '排序', '用于显示的顺序', 'sort')
                    ->setFormData(D('SystemConfig')->find($id))
                    ->display();
        }
    }

    /**
     * 获取某个分组的配置参数
     * @author jry <598821125@qq.com>
     */
    public function group($tab = 1){
        //根据分组获取配置
        $map['status'] = array('egt', '0'); //禁用和正常状态
        $map['group'] = array('eq', $tab);
        $data_list = D('SystemConfig')->where($map)->order('sort asc,id asc')->select();

        //构造表单名、解析options
        foreach($data_list as &$data){
            $data['name'] = 'config['.$data['name'].']';
            $data['options'] = D('SystemConfig')->parse_attr($data['options']);
        }

        //使用FormBuilder快速建立表单页面。
        $builder = new \Admin\Builder\AdminFormBuilder();
        $builder->title('系统设置')  //设置页面标题
                ->SetTablist(C('CONFIG_GROUP_LIST')) //设置Tab按钮列表
                ->SetCurrentTab($tab) //设置当前Tab
                ->setUrl(U('save')) //设置表单提交地址
                ->setExtraItems($data_list) //直接设置表单数据
                ->display();
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
