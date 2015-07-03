<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------
namespace Common\Model;
use Think\Model;
/**
 * 分类模型
 * @author jry <598821125@qq.com>
 */
class CategoryModel extends Model{
    /**
     * 自动验证规则
     * @author jry <598821125@qq.com>
     */
    protected $_validate = array(
        array('title', 'require', '名称不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('title', '1,32', '名称长度为1-32个字符', self::EXISTS_VALIDATE, 'length', self::MODEL_BOTH),
        array('title', 'checkTitle', '名称已经存在', self::MUST_VALIDATE, 'callback', self::MODEL_INSERT),
        array('group', 'require', '分组不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('doc_type', 'require', '内容模型不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
    );

    /**
     * 自动完成规则
     * @author jry <598821125@qq.com>
     */
    protected $_auto = array(
        array('ctime', NOW_TIME, self::MODEL_INSERT),
        array('utime', NOW_TIME, self::MODEL_BOTH),
        array('status', '1', self::MODEL_INSERT),
    );

    /**
     * 检查同一分组下是否有相同的字段
     * @author jry <598821125@qq.com>
     */
    protected function checkTitle(){
        $map['title'] = array('eq', I('post.title'));
        $map['group'] = array('eq', I('post.group'));
        $result = $this->where($map)->find();
        return empty($result);
    }

    /**
     * 获取参数的所有父级分类
     * @param int $cid 分类id
     * @return array 参数分类和父类的信息集合
     * @author jry <598821125@qq.com>
     */
    public function getParentCategory($cid, $group = 1){
        if(empty($cid)){
            return false;
        }
        $con['status'] = 1;
        $con['group']  = $group;
        $category_list = $this->where($con)->field('id,pid,group,title,url')->select();
        $current_category = $this->field('id,pid,group,title,url')->find($cid); //获取当前分类的信息
        $result[] = $current_category;
        $pid = $current_category['pid'];
        while(true){
            foreach($category_list as $key => $val){
                if($val['id'] == $pid){
                    $pid = $val['pid'];
                    array_unshift($result, $val); //将父分类插入到数组第一个元素前
                }
            }
            if($pid == 0 || count($result) == 1){ //已找到顶级分类或者没有任何父分类
                break;
            }
        }
        return $result;
    }

    /**
     * 获取分类树，指定分类则返回指定分类极其子分类，不指定则返回所有分类树
     * @param  integer $id    分类ID
     * @param  boolean $field 查询字段
     * @return array          分类树
     * @author jry <598821125@qq.com>
     */
    public function getCategoryTree($id = 0, $group = 1, $field = true){
        //获取当前分类信息
        if((int)$id > 0){
            $info = $this->find($id);
            $id   = $info['id'];
        }
        //获取所有分类
        $map['status']  = array('eq', 1);
        $map['group']  = array('eq', $group);
        $tree = new \Common\Util\Tree();
        $list = $this->field($field)->where($map)->order('sort')->select();
        $list = $this->getCategoryCount($list); //获取分类的文档数目
        $list = $tree->list_to_tree($list, $pk = 'id', $pid = 'pid', $child = '_child', $root = (int)$id);
        return $list;
    }

    /**
     * 获取分类的文档统计数据
     * @return array 分类列表
     * @author jry <598821125@qq.com>
     */
    public function getCategoryCount($list){
        foreach($list as &$val){
            $map = array();
            $map['cid'] = array('eq', $val['id']);
            $val['count_sum_document'] = D('Document')->where($map)->count();
            $val['count_sum_comment'] = D('Document')->where($map)->sum('comment');
            $today = strtotime(date('Y-m-d', time())); //今天
            $map['ctime'] = array(array('gt', $today), array('lt', $today+86400));
            $val['count_new_document'] = D('Document')->where($map)->count();
        }
        return $list;
    }
}
