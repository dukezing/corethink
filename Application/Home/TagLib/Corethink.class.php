<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------
namespace Home\TagLib;
use Think\Template\TagLib;
/**
 * 标签库
 * @author jry <598821125@qq.com>
 */
class CoreThink extends TagLib{
    /**
     * 定义标签列表
     * @author jry <598821125@qq.com>
     */
    protected $tags = array(
        'breadcrumb'  => array('attr' => 'name,cid', 'close' => 1), //面包屑导航列表
        'category' => array('attr' => 'name,pid', 'close' => 1), //栏目分类列表
        'navlink'  => array('attr' => 'name,pid', 'close' => 1), //导航链接列表
        'slider'   => array('attr' => 'name,group', 'close' => 1), //幻灯列表
        'comment'  => array('attr' => 'name,model,doc_id', 'close' => 1), //评论列表
        'article'  => array('attr' => 'name,cid,limit', 'close' => 1), //文章列表
    );

    /**
     * 面包屑导航列表
     * @author jry <598821125@qq.com>
     */
    public function _breadcrumb($tag, $content){
        $name   = $tag['name'];
        $cid   = $tag['cid'];
        $parse  = '<?php ';
        $parse .= '$__PARENT_CATEGORY__ = D(\'Category\')->getParentCategory('.$cid.');';
        $parse .= ' ?>';
        $parse .= '<volist name="__PARENT_CATEGORY__" id="'. $name .'">';
        $parse .= $content;
        $parse .= '</volist>';
        return $parse;
    }

    /**
     * 栏目分类列表
     * @author jry <598821125@qq.com>
     */
    public function _category($tag, $content){
        $name   = $tag['name'];
        $parse  = '<?php ';
        $parse .= '$__CATEGORYLIST__ = D(\'Category\')->getAllCategory($map, "1");';
        $parse .= '$__CATEGORYLIST__ = D(\'Common/Tree\')->list_to_tree($__CATEGORYLIST__);';
        $parse .= ' ?>';
        $parse .= '<volist name="__CATEGORYLIST__" id="'. $name .'">';
        $parse .= $content;
        $parse .= '</volist>';
        return $parse;
    }

    /**
     * 导航链接列表
     * @author jry <598821125@qq.com>
     */
    public function _navlink($tag, $content){
        $name   = $tag['name'];
        $parse  = '<?php ';
        $parse .= '$__NAVLINK_LIST__ = D(\'Navlink\')->getAllNavlink($map, "1");';
        $parse .= '$__NAVLINK_LIST__ = D(\'Common/Tree\')->list_to_tree($__NAVLINK_LIST__);';
        $parse .= ' ?>';
        $parse .= '<volist name="__NAVLINK_LIST__" id="'. $name .'">';
        $parse .= $content;
        $parse .= '</volist>';
        return $parse;
    }

    /**
     * 幻灯片列表
     * @author jry <598821125@qq.com>
     */
    public function _slider($tag, $content){
        $name   = $tag['name'];
        $group  = $tag['group'];
        $parse  = '<?php ';
        $parse .= '$__SLIDER_LIST__ = D(\'Slider\')->getSliderByGroup('.$group.', "1");';
        $parse .= ' ?>';
        $parse .= '<volist name="__SLIDER_LIST__" id="'. $name .'">';
        $parse .= $content;
        $parse .= '</volist>';
        return $parse;
    }

    /**
     * 评论列表
     * @author jry <598821125@qq.com>
     */
    public function _comment($tag, $content){
        $name   = $tag['name'];
        $model  = $tag['model'];
        $doc_id  = $tag['doc_id'];
        $parse  = '<?php ';
        $parse .= '$__COMMENT_LIST__ = D(\'Comment\')->getAllCommentByDocument('.$model.','.$doc_id.');';
        $parse .= ' ?>';
        $parse .= '<volist name="__COMMENT_LIST__" id="'. $name .'">';
        $parse .= $content;
        $parse .= '</volist>';
        return $parse;
    }

    /**
     * 文章列表
     * @author jry <598821125@qq.com>
     */
    public function _article($tag, $content){
        $name   = $tag['name'];
        $cid  = $tag['cid'];
        $parse  = '<?php ';
        $parse .= '$map["cid"] = array("eq", '.$cid.');';
        $parse .= '$__ARTICLE_LIST__ = D(\'Article\')->getAllArticle($map,"1");';
        $parse .= ' ?>';
        $parse .= '<volist name="__ARTICLE_LIST__" id="'. $name .'">';
        $parse .= $content;
        $parse .= '</volist>';
        return $parse;
    }
}
