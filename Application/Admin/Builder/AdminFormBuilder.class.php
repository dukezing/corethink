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
 * 后台表单页面自动生成器
 * @author jry <598821125@qq.com>
 */
class AdminFormBuilder extends AdminController{
    private $_title; //页面标题
    private $_tab_list; //Tab按钮列表
    private $_current_tab = 0; //当前Tab
    private $_url; //表单提交地址
    private $_form_items = array(); //表单项目
    private $_extra_items = array(); //额外已经构造好的表单项目
    private $_form_data = array(); //表单数据
    private $_extra; //额外参数

    /**设置页面标题
     * @param $title 标题文本
     * @return $this
     */
    public function setType($type){
        $this->_type = $type;
        return $this;
    }

    /**设置页面标题
     * @param $title 标题文本
     * @return $this
     */
    public function title($title){
        $this->meta_title = $title;
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

    /**直接设置表单项数组
     * @param $form_items 表单项数组
     * @return $this
     */
    public function setExtraItems($extra_items){
        $this->_extra_items = $extra_items;
        return $this;
    }

    /**设置表单提交地址
     * @param $url 提交地址
     * @return $this
     */
    public function setUrl($url){
        $this->_url = $url;
        return $this;
    }

    /**加入一个表单项
     * @param $type 表单类型(取值参考系统配置FORM_ITEM_TYPE)
     * @param $title 表单标题
     * @param $tip 表单提示说明
     * @param $name 表单名
     * @param $options 表单options
     * @param $extra_class 表单额外CSS类名
     * @return $this
     */
    public function addItem($type, $title, $tip, $name, $options = array(), $extra_class = ''){
        $item['type'] = $type;
        $item['title'] = $title;
        $item['tip'] = $tip;
        $item['name'] = $name;
        $item['options'] = $options;
        $item['extra_class'] = $extra_class;
        $this->_form_items[] = $item;
        return $this;
    }

    /**设置表单表单数据
     * @param $form_data 表单数据
     * @return $this
     */
    public function setFormData($form_data){
        $this->_form_data = $form_data;
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
        //编译表单值
        foreach($this->_form_items as &$item){
            $item['value'] = $this->_form_data[$item['name']];
        }

        //额外已经构造好的表单项目与单个组装的的表单项目进行合并
        $this->_form_items = array_merge($this->_form_items, $this->_extra_items);

        $this->assign('title', $this->_title);
        $this->assign('tab_list', $this->_tab_list);
        $this->assign('current_tab', $this->_current_tab);
        $this->assign('url', $this->_url);
        $this->assign('form_items', $this->_form_items);
        $this->assign('extra', $this->_extra);
        parent::display(dirname(__FILE__) . '/adminformbuilder.html');
    }
}
