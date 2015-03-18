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
 * 数据库控制器
 * @author jry <598821125@qq.com>
 */
class DatebaseController extends AdminController{
    /**
     * 数据字典
     * @author jry <598821125@qq.com>
     */
    public function index(){
        $database   = C('DB_NAME');     //数据库名 
        //取得所有表
        $tables = M()->query('show tables');
        foreach($tables as $key => $val){
            $tables_result[$key]['name'] = $val['tables_in_'.$database];
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

            //获取所有表的字段信息
            $sql  = 'SELECT * FROM ';
            $sql .= 'INFORMATION_SCHEMA.COLUMNS ';
            $sql .= 'WHERE ';
            $sql .= "table_name = '{$val['name']}'  AND table_schema = '{$database}'";
            $field_result = M()->query($sql);
            $tables_result[$key]['fields'] = $field_result;
        }

        $this->assign('volist', $tables_result); 
        $this->assign('meta_title', "数据字典");
        $this->display();
    }
}
