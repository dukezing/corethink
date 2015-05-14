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
use Think\Db;
use Org\Util\Database;
/**
 * 数据库管理控制器
 * @author jry <598821125@qq.com>
 */
class SystemDatebaseController extends AdminController{
    /**
     * 数据库备份/还原路径
     * @author jry <598821125@qq.com>
     */
    public static $backup_path = './Backup/';

    /**
     * 数据字典
     * @author jry <598821125@qq.com>
     */
    public function index($tab = 'ct_addon'){
        $database   = C('DB_NAME'); //数据库名 
        //取得所有表
        $tables = M()->query('show tables');
        foreach($tables as $key => $val){
            $tables_result[$val['tables_in_'.$database]]['name'] = $val['tables_in_'.$database];
        }

        //获取表信息
        foreach ($tables_result as $key => $val){
            //获取所有表的备注
            $sql  = 'SELECT * FROM ';
            $sql .= 'INFORMATION_SCHEMA.TABLES ';
            $sql .= 'WHERE ';
            $sql .= "table_name = '{$val['name']}'  AND table_schema = '{$database}'";
            $table_result = M()->query($sql);
            $tables_result[$key]['title'] = $table_result[0]['table_comment'];
            $tabs[$key] = $table_result[0]['table_comment'].'('.$key.')';

            //获取所有表的字段信息
            $sql  = 'SELECT * FROM ';
            $sql .= 'INFORMATION_SCHEMA.COLUMNS ';
            $sql .= 'WHERE ';
            $sql .= "table_name = '{$val['name']}'  AND table_schema = '{$database}'";
            $field_result = M()->query($sql);
            $tables_result[$key]['fields'] = $field_result;
        }

        //使用Builder快速建立列表页面。
        $builder = new \Admin\Builder\AdminListBuilder();
        $builder->title('数据字典')  //设置页面标题
                ->SetTablist($tabs) //设置Tab按钮列表
                ->SetCurrentTab($tab) //设置当前Tab
                ->addField('column_name', '字段名', 'text')
                ->addField('column_type', '数据类型', 'text')
                ->addField('column_default', '默认值', 'text')
                ->addField('is_nullable', '允许非空', 'text')
                ->addField('extra', '自动递增', 'text')
                ->addField('column_comment', '备注', 'text')
                ->dataList($tables_result[$tab]['fields'])    //数据列表
                ->display();
    }

    /**
     * 数据库备份
     * @author jry <598821125@qq.com>
     */
    public function export(){
        $Db   = Db::getInstance();
        $list = $Db->query('SHOW TABLE STATUS');
        $list = array_map('array_change_key_case', $list);
        $this->assign('meta_title', "数据备份");
        $this->assign('list', $list);
        $this->display();
    }
    /**
     * 数据库还原
     * @author jry <598821125@qq.com>
     */
    public function import(){
        //列出备份文件列表
        $path = self::$backup_path;
        if(!is_dir($path)){
            mkdir($path, 0755, true);
        }
        $path = realpath($path);
        $flag = \FilesystemIterator::KEY_AS_FILENAME;
        $glob = new \FilesystemIterator($path,  $flag);

        $list = array();
        foreach ($glob as $name => $file) {
            if(preg_match('/^\d{8,8}-\d{6,6}-\d+\.sql(?:\.gz)?$/', $name)){
                $name = sscanf($name, '%4s%2s%2s-%2s%2s%2s-%d');
                $date = "{$name[0]}-{$name[1]}-{$name[2]}";
                $time = "{$name[3]}:{$name[4]}:{$name[5]}";
                $part = $name[6];
                if(isset($list["{$date} {$time}"])){
                    $info = $list["{$date} {$time}"];
                    $info['part'] = max($info['part'], $part);
                    $info['size'] = $info['size'] + $file->getSize();
                }else{
                    $info['part'] = $part;
                    $info['size'] = $file->getSize();
                }
                $extension        = strtoupper(pathinfo($file->getFilename(), PATHINFO_EXTENSION));
                $info['compress'] = ($extension === 'SQL') ? '-' : $extension;
                $info['time']     = strtotime("{$date} {$time}");
                $list["{$date} {$time}"] = $info;
            }
        }
        $this->assign('meta_title', "数据还原");
        $this->assign('list', $list);
        $this->display($type);
    }

    /**
     * 优化表
     * @param  String $tables 表名
     * @author jry <598821125@qq.com>
     */
    public function optimize($tables = null){
        if($tables) {
            $Db   = Db::getInstance();
            if(is_array($tables)){
                $tables = implode('`,`', $tables);
                $list = $Db->query("OPTIMIZE TABLE `{$tables}`");

                if($list){
                    $this->success("数据表优化完成！");
                }else{
                    $this->error("数据表优化出错请重试！");
                }
            }else{
                $list = $Db->query("OPTIMIZE TABLE `{$tables}`");
                if($list){
                    $this->success("数据表'{$tables}'优化完成！");
                }else{
                    $this->error("数据表'{$tables}'优化出错请重试！");
                }
            }
        }else{
            $this->error("请指定要优化的表！");
        }
    }

    /**
     * 修复表
     * @param  String $tables 表名
     * @author jry <598821125@qq.com>
     */
    public function repair($tables = null){
        if($tables) {
            $Db   = Db::getInstance();
            if(is_array($tables)){
                $tables = implode('`,`', $tables);
                $list = $Db->query("REPAIR TABLE `{$tables}`");

                if($list){
                    $this->success("数据表修复完成！");
                }else{
                    $this->error("数据表修复出错请重试！");
                }
            }else{
                $list = $Db->query("REPAIR TABLE `{$tables}`");
                if($list){
                    $this->success("数据表'{$tables}'修复完成！");
                }else{
                    $this->error("数据表'{$tables}'修复出错请重试！");
                }
            }
        }else{
            $this->error("请指定要修复的表！");
        }
    }

    /**
     * 删除备份文件
     * @param  Integer $time 备份时间
     * @author jry <598821125@qq.com>
     */
    public function del($time = 0){
        if($time){
            $name  = date('Ymd-His', $time) . '-*.sql*';
            $path  = realpath(self::$backup_path) . DIRECTORY_SEPARATOR . $name;
            array_map("unlink", glob($path));
            if(count(glob($path))){
                $this->error('备份文件删除失败，请检查权限！');
            }else{
                $this->success('备份文件删除成功！');
            }
        }else{
            $this->error('参数错误！');
        }
    }

    /**
     * 备份数据库
     * @param  String  $tables 表名
     * @param  Integer $id     表ID
     * @param  Integer $start  起始行数
     * @author jry <598821125@qq.com>
     */
    public function do_export($tables = null, $id = null, $start = null){
        if(IS_POST && !empty($tables) && is_array($tables)){ //初始化
            $path = self::$backup_path;
            if(!is_dir($path)){
                mkdir($path, 0755, true);
            }
            //读取备份配置
            $config = array(
                'path'     => realpath($path) . DIRECTORY_SEPARATOR,
                'part'     => 20971520,
                'compress' => 1,
                'level'    => 9,
            );

            //检查是否有正在执行的任务
            $lock = "{$config['path']}backup.lock";
            if(is_file($lock)){
                $this->error('检测到有一个备份任务正在执行，请稍后再试！');
            }else{
                //创建锁文件
                file_put_contents($lock, NOW_TIME);
            }

            //检查备份目录是否可写
            is_writeable($config['path']) || $this->error('备份目录不存在或不可写，请检查后重试！');
            session('backup_config', $config);

            //生成备份文件信息
            $file = array(
                'name' => date('Ymd-His', NOW_TIME),
                'part' => 1,
            );
            session('backup_file', $file);

            //缓存要备份的表
            session('backup_tables', $tables);

            //创建备份文件
            $Database = new Database($file, $config);
            if(false !== $Database->create()){
                $tab = array('id' => 0, 'start' => 0);
                $this->success('初始化成功！', '', array('tables' => $tables, 'tab' => $tab));
            }else{
                $this->error('初始化失败，备份文件创建失败！');
            }
        } elseif (IS_GET && is_numeric($id) && is_numeric($start)) { //备份数据
            $tables = session('backup_tables');
            //备份指定表
            $Database = new Database(session('backup_file'), session('backup_config'));
            $start  = $Database->backup($tables[$id], $start);
            if(false === $start){ //出错
                $this->error('备份出错！');
            }elseif (0 === $start) { //下一表
                if(isset($tables[++$id])){
                    $tab = array('id' => $id, 'start' => 0);
                    $this->success('备份完成！', '', array('tab' => $tab));
                }else{ //备份完成，清空缓存
                    unlink(session('backup_config.path') . 'backup.lock');
                    session('backup_tables', null);
                    session('backup_file', null);
                    session('backup_config', null);
                    $this->success('备份完成！');
                }
            }else{
                $tab  = array('id' => $id, 'start' => $start[0]);
                $rate = floor(100 * ($start[0] / $start[1]));
                $this->success("正在备份...({$rate}%)", '', array('tab' => $tab));
            }

        }else{ //出错
            $this->error('参数错误！');
        }
    }

    /**
     * 还原数据库
     * @author jry <598821125@qq.com>
     */
    public function do_import($time = 0, $part = null, $start = null){
        if(is_numeric($time) && is_null($part) && is_null($start)){ //初始化
            //获取备份文件信息
            $name  = date('Ymd-His', $time) . '-*.sql*';
            $path  = realpath(self::$backup_path) . DIRECTORY_SEPARATOR . $name;
            $files = glob($path);
            $list  = array();
            foreach($files as $name){
                $basename = basename($name);
                $match    = sscanf($basename, '%4s%2s%2s-%2s%2s%2s-%d');
                $gz       = preg_match('/^\d{8,8}-\d{6,6}-\d+\.sql.gz$/', $basename);
                $list[$match[6]] = array($match[6], $name, $gz);
            }
            ksort($list);

            //检测文件正确性
            $last = end($list);
            if(count($list) === $last[0]){
                session('backup_list', $list); //缓存备份列表
                $this->success('初始化完成！', '', array('part' => 1, 'start' => 0));
            }else{
                $this->error('备份文件可能已经损坏，请检查！');
            }
        }elseif(is_numeric($part) && is_numeric($start)) {
            $list  = session('backup_list');
            $db = new Database($list[$part], array(
                'path'     => realpath($this->backup_path) . DIRECTORY_SEPARATOR,
                'compress' => $list[$part][2]));

            $start = $db->import($start);

            if(false === $start){
                $this->error('还原数据出错！');
            }elseif(0 === $start) { //下一卷
                if(isset($list[++$part])){
                    $data = array('part' => $part, 'start' => 0);
                    $this->success("正在还原...#{$part}", '', $data);
                }else{
                    session('backup_list', null);
                    $this->success('还原完成！');
                }
            }else{
                $data = array('part' => $part, 'start' => $start[0]);
                if($start[1]){
                    $rate = floor(100 * ($start[0] / $start[1]));
                    $this->success("正在还原...#{$part} ({$rate}%)", '', $data);
                }else{
                    $data['gz'] = 1;
                    $this->success("正在还原...#{$part}", '', $data);
                }
            }
        }else{
            $this->error('参数错误！');
        }
    }
}
