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
        array('title', '', '名称已经存在', self::VALUE_VALIDATE, 'unique', self::MODEL_BOTH),
        array('doc_type', 'require', '内容模型不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
    );

    /**
     * 自动完成规则
     * @author jry <598821125@qq.com>
     */
    protected $_auto = array(
        array('ctime', NOW_TIME, self::MODEL_INSERT),
        array('utime', NOW_TIME, self::MODEL_BOTH),
        array('sort', '0', self::MODEL_INSERT),
        array('status', '1', self::MODEL_INSERT),
    );

    /**
     * 根据ID获取分类
     * @author jry <598821125@qq.com>
     */
    public function getCategoryById($id, $field){
        $map['id'] = array('eq', $id);
        $category_info = $this->where($map)->find();
        if($field){
            return $category_info[$field];
        }
        return $category_info;
    }

    /**
     * 获取所有分类
     * @author jry <598821125@qq.com>
     */
    public function getAllCategory($map, $status = '0,1'){
        $map['status'] = array('in', $status);
        $list = $this->where($map)->select();
        return $this->getLinkByModel($list);
    }

    /**
     * 获取参数的所有父级分类
     * @param int $cid 分类id
     * @return array 参数分类和父类的信息集合
     * @author jry <598821125@qq.com>
     */
    public function getParentCategory($cid){
        if(empty($cid)){
            return false;
        }
        $cates = $this->where(array('status'=>1))->field('id,pid,title,url')->order('sort')->select();
        $child = $this->field('id,pid,title,url')->find($cid); //获取参数分类的信息
        $pid   = $child['pid'];
        $temp  = array();
        $res[] = $child;
        while(true){
            foreach ($cates as $key=>$cate){
                if($cate['id'] == $pid){
                    $pid = $cate['pid'];
                    array_unshift($res, $cate); //将父分类插入到数组第一个元素前
                }
            }
            if($pid == 0){
                break;
            }
        }
        return $this->getLinkByModel($res);
    }

    /**
     * 获取分类树，指定分类则返回指定分类极其子分类，不指定则返回所有分类树
     * @param  integer $id    分类ID
     * @param  boolean $field 查询字段
     * @return array          分类树
     * @author jry <598821125@qq.com>
     */
    public function getCategoryTree($id = 0, $field = true){
        //获取当前分类信息
        if((int)$id > 0){
            $info = $this->find($id);
            $id   = $info['id'];
        }
        //获取所有分类
        $map  = array('status' => 1);
        $tree = new \Common\Util\Tree();
        $list = $this->field($field)->where($map)->order('sort')->select();
        $list = $this->getLinkByModel($list); //获取分类链接
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

    /**
     * 根据分类模型获取分类链接
     * @return array 分类列表
     * @author jry <598821125@qq.com>
     */
    public function getLinkByModel($list){
        foreach($list as $key => $val){
            switch($val['type']){
                case 1:
                    $list[$key]['link'] = '<a target="_blank" href="'.$val['url'].'">'.$val['title'].'</a>';
                    $list[$key]['title_link'] = $val['title'];
                    break;
                case 2:
                    $list[$key]['link'] = '<a href="'.U('Category/detail', array('id' => $val['id'])).'">'.$val['title'].'</a>';
                    $list[$key]['title_link'] = $val['title'];
                    break;
                default :
                    $list[$key]['link'] = '<a href="'.U('Document/index', array('cid' => $val['id'])).'">'.$val['title'].'</a>';
                    $list[$key]['title_link'] = $list[$key]['title_prefix'].$list[$key]['link'];
            }
        }
        return $list;
    }
}
