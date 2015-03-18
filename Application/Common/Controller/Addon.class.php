<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: ijry <ijry@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------
namespace Common\Controller;
/**
 * 插件类
 * @author jry <598821125@qq.com>
 */
abstract class Addon{
    /**
     * 视图实例对象
     * @var view
     * @access protected
     * @author jry <598821125@qq.com>
     */
    protected $view           =  null;
    public $info              =  array();
    public $addon_path        =  '';
    public $config_file       =  '';
    public $custom_config     =  '';
    public $admin_list        =  array();
    public $custom_adminlist  =  '';
    public $access_url        =  array();

    /**
     * 构造方法
     * @author jry <598821125@qq.com>
     */
    public function __construct(){
        $this->view         =   \Think\Think::instance('Think\View');
        $this->addon_path   =   THINK_ADDON_PATH.$this->getName().'/';
        $TMPL_PARSE_STRING = C('TMPL_PARSE_STRING');
        $TMPL_PARSE_STRING['__ADDONROOT__'] = __ROOT__ . '/Addons/'.$this->getName();
        C('TMPL_PARSE_STRING', $TMPL_PARSE_STRING);
        if(is_file($this->addon_path.'config.php')){
            $this->config_file = $this->addon_path.'config.php';
        }
    }

    /**
     * 模板主题设置
     * @access protected
     * @param string $theme 模版主题
     * @return Action
     * @author jry <598821125@qq.com>
     */
    final protected function theme($theme){
        $this->view->theme($theme);
        return $this;
    }

    /**
     * 显示方法
     * @author jry <598821125@qq.com>
     */
    final protected function display($template=''){
        if($template == '')
            $template = CONTROLLER_NAME;
        echo ($this->fetch($template));
    }

    /**
     * 模板变量赋值
     * @access protected
     * @param mixed $name 要显示的模板变量
     * @param mixed $value 变量的值
     * @return Action
     * @author jry <598821125@qq.com>
     */
    final protected function assign($name,$value='') {
        $this->view->assign($name,$value);
        return $this;
    }

    /**
     * 用于显示模板的方法
     * @author jry <598821125@qq.com>
     */
    final protected function fetch($templateFile = CONTROLLER_NAME){
        if(!is_file($templateFile)){
            $templateFile = $this->addon_path.$templateFile.C('TMPL_TEMPLATE_SUFFIX');
            if(!is_file($templateFile)){
                throw new \Exception("模板不存在:$templateFile");
            }
        }
        return $this->view->fetch($templateFile);
    }

    /**
     * 获取名称
     * @author jry <598821125@qq.com>
     */
    final public function getName(){
        $class = get_class($this);
        return substr($class,strrpos($class, '\\')+1, -5);
    }

    final public function checkInfo(){
        $info_check_keys = array('name','title','description','status','author','version');
        foreach ($info_check_keys as $value) {
            if(!array_key_exists($value, $this->info))
                return FALSE;
        }
        return TRUE;
    }

    /**
     * 获取插件的配置数组
     * @author jry <598821125@qq.com>
     */
    final public function getConfig($name=''){
        static $_config = array();
        if(empty($name)){
            $name = $this->getName();
        }
        if(isset($_config[$name])){
            return $_config[$name];
        }
        $config =   array();
        $map['name'] = $name;
        $map['status'] = 1;
        $config = M('Addon')->where($map)->getField('config');
        if($config){
            $config = json_decode($config, true);
        }else{
            $temp_arr = include $this->config_file;
            foreach ($temp_arr as $key => $value) {
                if($value['type'] == 'group'){
                    foreach ($value['options'] as $gkey => $gvalue) {
                        foreach ($gvalue['options'] as $ikey => $ivalue) {
                            $config[$ikey] = $ivalue['value'];
                        }
                    }
                }else{
                    $config[$key] = $temp_arr[$key]['value'];
                }
            }
        }
        $_config[$name] = $config;
        return $config;
    }

    /**
     * 必须实现安装
     * @author jry <598821125@qq.com>
     */
    abstract public function install();

    /**
     * 必须卸载插件方法
     * @author jry <598821125@qq.com>
     */
    abstract public function uninstall();
}
