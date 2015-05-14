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
 * 文章模型
 * @author jry <598821125@qq.com>
 */
class ArticleModel extends Model{
    /**
     * 自动验证规则
     * @author jry <598821125@qq.com>
     */
    protected $_validate = array(
        array('cid', 'require', '分类不能为空', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
        array('cid', 'check_category_model', '该分类不是文章模型，请选择正确的分类！', self::MUST_VALIDATE , 'callback', self::MODEL_BOTH),
        array('title', 'require', '文章标题不能为空', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
        array('title', '1,127', '文章标题长度为1-127个字符', self::EXISTS_VALIDATE, 'length', self::MODEL_BOTH),
        array('title', '', '文章标题已经存在', self::VALUE_VALIDATE, 'unique', self::MODEL_BOTH),
    );

    /**
     * 自动完成规则
     * @author jry <598821125@qq.com>
     */
    protected $_auto = array(
        array('uid', 'is_login', self::MODEL_INSERT, 'function'),
        array('abstract', 'getAbstract', self::MODEL_BOTH, 'callback'),
        array('ctime', 'getCreateTime', self::MODEL_BOTH, 'callback'),
        array('utime', NOW_TIME, self::MODEL_BOTH),
        array('status', '1', self::MODEL_INSERT),
    );

    /**
     * 创建时间不写则取当前时间
     * @return int 时间戳
     * @author jry <598821125@qq.com>
     */
    protected function getCreateTime(){
        $ctime  = I('post.ctime');
        return $ctime ? strtotime($ctime) : NOW_TIME;
    }

    /**
     * 获取文章描述
     * @return String  文章描述
     * @author jry <598821125@qq.com>
     */
    function getAbstract(){
        if($_POST["abstract"] == ""){
            $abstract = \Org\Util\String::msubstr(\Org\Util\String::html2text($_POST["content"]), 0, 140, "utf-8", false);
        }else{
            $abstract = \Org\Util\String::html2text($_POST["abstract"]);
        }
        return $abstract;
    }

    /**
     * 检测分类是否绑定了指定模型
     * @param  int $cid 分类ID
     * @return boolean  true-绑定了模型，false-未绑定模型
     * @author jry <598821125@qq.com>
     */
    function check_category_model($cid){
        $category = D('Category')->getCategoryById($cid);
        $current_model = D('CategoryModel')->getModelByName(CONTROLLER_NAME);
        return $category['model'] == $current_model['id'];
    }

    /**
     * 根据ID获取文章
     * @author jry <598821125@qq.com>
     */
    public function getArticleById($id){
        $map['id'] = array('eq', $id);
        return $this->where($map)->find();
    }

    /**
     * 根据条件获取文章列表
     * @author jry <598821125@qq.com>
     */
    public function getAllArticle($map, $status = '0,1'){
        $map['status'] = array('in', $status);
        $list = $this->where($map)->order('sort desc,id desc')->select();
        return $list;
    }

    /**
     * 获取当前分类上一篇文档
     * @author jry <598821125@qq.com>
     */
    public function getPreviousArticle($info){
        $map['status'] = array('eq', 1);
        $map['id'] = array('gt', $info['id']);
        $map['cid'] = array('eq', $info['cid']);
        $previous = $this->where($map)->order('id asc')->find();
        if(!$previous){
            $previous['title'] = '没有了';
            $previous['disabled'] = "disabled";
            $previous['link'] = '#';
        }else{
            $previous['link'] = U('Article/detail', array('id' => $previous['id']));
        }
        return $previous;
    }

    /**
     * 获取当前分类下一篇文档
     * @author jry <598821125@qq.com>
     */
    public function getNextArticle($info){
        $map['status'] = array('eq', 1);
        $map['id'] = array('lt', $info['id']);
        $map['cid'] = array('eq', $info['cid']);
        $next = $this->where($map)->order('id desc')->find();
        if(!$next){
            $next['title'] = '没有了';
            $next['disabled'] = "disabled";
            $next['link'] = '#';
        }else{
            $next['link'] = U('Article/detail', array('id' => $next['id']));
        }
        return $next;
    }
}
