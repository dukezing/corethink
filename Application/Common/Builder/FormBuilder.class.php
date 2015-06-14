<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------
namespace Common\Builder;
use Think\View;
use Think\Controller;
/**
 * 表单页面自动生成器
 * @author jry <598821125@qq.com>
 */
class FormBuilder extends Controller{
    private $_title; //页面标题
    private $_tab_list; //Tab按钮列表
    private $_tab_url; //Tab按钮地址
    private $_current_tab = 0; //当前Tab
    private $_url; //表单提交地址
    private $_form_items = array(); //表单项目
    private $_extra_items = array(); //额外已经构造好的表单项目
    private $_form_data = array(); //表单数据
    private $_extra; //额外参数
    private $_builder_class; //Builder最外层div样式
    private $_template = 'Builder/formbuilder'; //模版

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

    /**设置Tab按钮地址
     * @param $tab_list
     * @return $this
     */
    public function setTabUrl($tab_url){
        $this->_tab_url = $tab_url;
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
     * @param $extra_class 表单项是否隐藏
     * @param $extra_attr 表单项额外属性
     * @return $this
     */
    public function addItem($name, $type, $title, $tip, $options = array(), $extra_class = '', $extra_attr = ''){
        $item['name'] = $name;
        $item['type'] = $type;
        $item['title'] = $title;
        $item['tip'] = $tip;
        $item['options'] = $options;
        $item['extra_class'] = $extra_class;
        $item['extra_attr'] = $extra_attr;
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

    /**设置页面模版
     * @param $template 模版
     * @return $this
     */
    public function setTemplate($template){
        $this->_template = $template;
        return $this;
    }

    /**Builder最外层div样式
     * @param $builder_class 样式
     * @return $this
     */
    public function setBuilderClass($builder_class){
        $this->_builder_class = $builder_class;
        return $this;
    }

    //显示页面
    public function display(){
        //额外已经构造好的表单项目与单个组装的的表单项目进行合并
        $this->_form_items = array_merge($this->_form_items, $this->_extra_items);

        //编译表单值
        if($this->_form_data){
            foreach($this->_form_items as &$item){
                if($this->_form_data[$item['name']]){
                    $item['value'] = $this->_form_data[$item['name']];
                }
            }
        }

        $this->assign('title', $this->_title);
        $this->assign('tab_list', $this->_tab_list);
        $this->assign('tab_url', $this->_tab_url);
        $this->assign('current_tab', $this->_current_tab);
        $this->assign('url', $this->_url);
        $this->assign('form_items', $this->_form_items);
        $this->assign('extra', $this->_extra);
        $this->assign('builder_class', $this->_builder_class);
        parent::display($this->_template);
    }
}
