<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------
namespace Admin\Builder;
use Think\View;
use Admin\Controller\AdminController;
/**
 * 后台数据列表自动生成器
 * @author jry <598821125@qq.com>
 */
class AdminListBuilder extends AdminController{
    private $_title; //页面标题
    private $_button_list = array(); //工具栏按钮组
    private $_search = array(); //搜索参数
    private $_tab_list; //Tab按钮列表
    private $_current_tab = 0; //当前Tab
    private $_field_list = array(); //表格标题字段
    private $_data_list = array(); //表格数据列表
    private $_right_button_list = array(); //表格右侧操作按钮组
    private $_page; //分页
    private $_extra; //额外参数

    /**设置页面标题
     * @param $title 标题文本
     * @return $this
     */
    public function title($title){
        $this->meta_title = $title;
        return $this;
    }

    /**加入一个按钮
     * @param $title
     * @param $attr
     * @return $this
     */
    public function addButton($title, $attr){
        $this->_button_list[] = array('title' => $title, 'attr' => $attr);
        return $this;
    }

    //加一个新增按钮
    public function AddNewButton(){
        $attr['class'] = 'btn';
        $attr['href'] =  U(CONTROLLER_NAME.'/add');
        $this->addButton('新 增', $attr);
        return $this;
    }

    //加一个启用按钮
    public function addResumeButton(){
        $attr['class'] = 'btn ajax-post confirm';
        $attr['href'] = U(CONTROLLER_NAME.'/setStatus', array('status' => 'resume'));
        $attr['target-form'] = 'ids';
        $this->addButton('启 用', $attr);
        return $this;
    }

    //加一个禁用按钮
    public function addForbidButton(){
        $attr['class'] = 'btn ajax-post confirm';
        $attr['href'] = U(CONTROLLER_NAME.'/setStatus', array('status' => 'forbid'));
        $attr['target-form'] = 'ids';
        $this->addButton('禁 用', $attr);
        return $this;
    }

    //加一个删除按钮
    public function addDeleteButton(){
        $attr['class'] = 'btn ajax-post confirm';
        $attr['href'] = U(CONTROLLER_NAME.'/setStatus', array('status' => 'delete'));
        $attr['target-form'] = 'ids';
        $this->addButton('删 除', $attr);
        return $this;
    }

    //加一个回收按钮
    public function addRecycleButton(){
        $attr['class'] = 'btn ajax-post confirm';
        $attr['href'] = U(CONTROLLER_NAME.'/setStatus', array('status' => 'recycle'));
        $attr['target-form'] = 'ids';
        $this->addButton('回 收', $attr);
        return $this;
    }

    //加一个还原按钮
    public function addRestoreButton(){
        $attr['class'] = 'btn ajax-post confirm';
        $attr['href'] = U(CONTROLLER_NAME.'/setStatus', array('status' => 'restore'));
        $attr['target-form'] = 'ids';
        $this->addButton('还 原', $attr);
        return $this;
    }

    /**设置搜索参数
     * @param $title
     * @param $url
     * @return $this
     */
    public function setSearch($title, $url){
        $this->_search = array('title' => $title, 'url' => $url);
        return $this;
    }

    /**设置Tab按钮列表
     * @param $tab_list
     * @return $this
     */
    public function setTabList($tab_list){
        $this->_tab_list = $tab_list;
        return $this;
    }

    /**设置当前Tab
     * @param $tab
     * @return $this
     */
    public function SetCurrentTab($current_tab){
        $this->_current_tab = $current_tab;
        return $this;
    }

    //加一个表格标题字段
    public function addField($name, $title, $type){
        $key = array('name' => $name, 'title' => $title, 'type' => $type);
        $this->_field_list[] = $key;
        return $this;
    }

    //表格数据列表
    public function dataList($data_list){
        $this->_data_list = $data_list;
        return $this;
    }

    /**加入一个按钮
     * @param $title
     * @param $attr
     * @return $this
     */
    public function addRightButton($type, $title = null, $attr = null){
        $this->_right_button_list[] = array('type' => $type, 'title' => $title, 'attr' => $attr);
        return $this;
    }

    /**设置搜索参数
     * @param $page
     * @return $this
     */
    public function setPage($page){
        $this->_page = $page;
        return $this;
    }

    /**设置额外参数
     * @param $extra  额外参数
     * @return $this
     */
    public function setExtra($extra){
        $this->_extra = $extra;
        return $this;
    }

    //显示页面
    public function display(){
        //编译button_list中的HTML属性
        foreach ($this->_button_list as &$button){
            $button['attr'] = $this->compileHtmlAttr($button['attr']);
        }

        //编译data_list中的值
        foreach($this->_data_list as &$data){
            //编译表格右侧按钮
            foreach($this->_right_button_list as $right_button){
                switch($right_button['type']){
                    case 'edit':
                        $right_button['link'] = '<a href="'.U(CONTROLLER_NAME.'/edit', array('id' => $data['id'])).'">编辑</a> ';
                        break;
                    case 'forbid':
                        switch($data['status']){
                            case '1':
                                $right_button['link'] = ' <a href="'.U(CONTROLLER_NAME.'/setStatus', array('status'=>'forbid', 'ids' => $data['id'])).'" class="ajax-get confirm">禁用</a> ';
                                break;
                            case '0':
                                $right_button['link'] = ' <a href="'.U(CONTROLLER_NAME.'/setStatus', array('status'=>'resume', 'ids' => $data['id'])).'" class="ajax-get confirm">启用</a> ';
                                break;
                            case '-1':
                                $right_button['link'] = ' <a href="'.U(CONTROLLER_NAME.'/setStatus', array('status'=>'restore', 'ids' => $data['id'])).'" class="ajax-get confirm">还原</a> ';
                                break;
                        }
                        break;
                    case 'delete':
                        $right_button['link'] = '<a href="'.U(CONTROLLER_NAME.'/setStatus', array('status'=>'delete', 'ids' => $data['id'])).'" class="ajax-get confirm">删除</a> ';
                        break;
                    case 'recycle':
                        $right_button['link'] = '<a href="'.U(CONTROLLER_NAME.'/setStatus', array('status'=>'recycle', 'ids' => $data['id'])).'" class="ajax-get confirm">回收</a> ';
                        break;
                    case 'self':
                        $right_button['attr']['href'] = U($right_button['attr']['href'].$data['id']);
                        $attr = $this->compileHtmlAttr($right_button['attr']);
                        $right_button['link'] = '<a '.$attr .'>'.$right_button['title'].'</a> ';
                        break;
                }
                $data['right_button'] .= $right_button['link'];
            }

            //根据表格标题字段指定类型编译列表数据
            foreach($this->_field_list as &$field){
                switch($field['type']){
                    case 'status':
                        switch($data[$field['name']]){
                            case '-1':
                                $data[$field['name']] = '<i class="icon-trash" style="color:red"></i>';
                                break;
                            case '0':
                                $data[$field['name']] = '<i class="icon-ban-circle" style="color:red"></i>';
                                break;
                            case '1':
                                $data[$field['name']] = '<i class="icon-ok" style="color:green"></i>';
                                break;
                        }
                        break;
                    case 'icon':
                        $data[$field['name']] = '<i class="'.$data[$field['name']].'"></i>';
                        break;
                    case 'time':
                        $data[$field['name']] = time_format($data[$field['name']]);
                        break;
                    case 'image':
                        $data[$field['name']] = '<img src="'.get_cover($data[$field['name']]).'">';
                        break;
                    case 'type':
                        $data[$field['name']] = C('FORM_ITEM_TYPE')[$data[$field['name']]];
                        break;
                }
            }
        }

        $this->assign('title', $this->_title);
        $this->assign('button_list', $this->_button_list);
        $this->assign('search', $this->_search);
        $this->assign('tab_list', $this->_tab_list);
        $this->assign('current_tab', $this->_current_tab);
        $this->assign('field_list', $this->_field_list);
        $this->assign('data_list', $this->_data_list);
        $this->assign('right_button_list', $this->_right_button_list);
        $this->assign('page', $this->_page);
        $this->assign('extra', $this->_extra);

        parent::display(dirname(__FILE__) . '/adminlistbuilder.html');
    }

    //编译HTML属性
    protected function compileHtmlAttr($attr){
        $result = array();
        foreach($attr as $key=>$value) {
            $value = htmlspecialchars($value);
            $result[] = "$key=\"$value\"";
        }
        $result = implode(' ', $result);
        return $result;
    }
}