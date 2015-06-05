<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------
namespace Home\Controller;
use Think\Controller;
/**
 * 分类控制器
 * @author jry <598821125@qq.com>
 */
class CategoryController extends HomeController{
    /**
     * 获取分类树，指定分类则返回指定分类极其子分类，不指定则返回所有分类树(返回JSON)
     * @param  integer $id    分类ID
     * @param  boolean $field 查询字段
     * @return array          分类树
     * @author jry <598821125@qq.com>
     */
    public function getCategoryTree($id = 0, $field = true){
        $list = D('Category')->getCategoryTree($id, $field);
        if($list){
            $data['status']  = 1;
            $data['info']  = '获取数据成功';
            $data['data']  = json_encode($list);
        }else{
            $data['status']  = 0;
            $data['info']  = '获取数据失败';
        }
        $this->ajaxReturn($data);
    }

    /**
     * 分类详情
     * @author jry <598821125@qq.com>
     */
    public function detail($id){
        $info = D('Category')->find($id);
        $template = $category['template'] ? 'Document/'.$info['template'] : 'Document/detail_category';
        $this->assign('info', $info);
        $this->assign('__CURRENT_CATEGORY__', $info['id']);
        $this->assign('meta_title', $info['title']);
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $this->display($template);
    }

    /**
     * 分类详情(返回JSON)
     * @author jry <598821125@qq.com>
     */
    public function getDetail($id){
        $info = D('Category')->find($id);
        if($info){
            $data['status']  = 1;
            $data['info']  = '获取数据成功';
            $data['data']  = json_encode($info);
        }else{
            $data['status']  = 0;
            $data['info']  = '获取数据失败';
        }
        $this->ajaxReturn($data);
    }
}
