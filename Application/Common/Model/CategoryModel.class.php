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
        array('model', 'require', '内容模型不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
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
        return $this->getLinkByCategoryModel($list);
    }

    /**
     * 根据分类模型获取分类链接
     * @return array 分类列表
     * @author jry <598821125@qq.com>
     */
    public function getLinkByCategoryModel($list){
        foreach($list as $key => $val){
            switch($val['model']){
                case 1:
                    $list[$key]['link'] = '<a target="_blank" href="'.$val['url'].'">'.$val['title'].'</a>';
                    $list[$key]['title_link'] = $val['title'];
                    break;
                case 2:
                    $list[$key]['link'] = '<a href="'.U('Category/detail', array('id' => $val['id'])).'">'.$val['title'].'</a>';
                    $list[$key]['title_link'] = $val['title'];
                    break;
                default :
                    $curent_model = D('CategoryModel')->getModelNameById($val['model']);
                    $list[$key]['link'] = '<a href="'.U($curent_model.'/index', array('cid' => $val['id'])).'">'.$val['title'].'</a>';
                    $list[$key]['title_link'] = $list[$key]['title_prefix'].$list[$key]['link'];
            }
        }
        return $list;
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
        $cates = $this->where(array('status'=>1))->field('id,pid,model,title,url')->order('sort')->select();
        $child = $this->field('id,pid,model,title,url')->getCategoryById($cid); //获取参数分类的信息
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
        return $this->getLinkByCategoryModel($res);
    }
}
