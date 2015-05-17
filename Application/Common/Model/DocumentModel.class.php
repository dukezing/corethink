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
 * 文档模型
 * @author jry <598821125@qq.com>
 */
class DocumentModel extends Model{
    /**
     * 自动验证规则
     * @author jry <598821125@qq.com>
     */
    protected $_validate = array(
        array('cid', 'require', '分类不能为空', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
        array('title', 'require', '文档标题不能为空', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
        array('title', '1,127', '文档标题长度为1-127个字符', self::EXISTS_VALIDATE, 'length', self::MODEL_BOTH),
        array('title', '', '文档标题已经存在', self::VALUE_VALIDATE, 'unique', self::MODEL_BOTH),
    );

    /**
     * 自动完成规则
     * @author jry <598821125@qq.com>
     */
    protected $_auto = array(
        array('uid', 'is_login', self::MODEL_INSERT, 'function'),
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
     * 新增或更新一个文档
     * @author jry <598821125@qq.com>
     */
    public function update(){
        $base_data = $this->create();
        if($base_data){
            //获取当前分类
            $cid = I('post.cid');
            $category_info = D('Category')->find($cid);
            $doc_type = D('Type')->where(array('id' => $category_info['doc_type']))->getField('name');
            $extend_document_model = D('Document'.$doc_type);
            $extend_data = $extend_document_model->create(); //子模型数据验证
            if(!$extend_data){
                $this->error = $extend_document_model->getError();
            }
            if($extend_data){
                if(empty($base_data['id'])){ //新增基础内容
                    $base_id = $this->add();
                    if($base_id){
                        $extend_data['id'] = $base_id;
                        $extend_id = $extend_document_model->add($extend_data);
                        if(!$extend_id){
                            $this->delete($base_id);
                            $this->error = '新增扩展内容出错！';
                            return false;
                        }
                        return $base_id;
                    }else{
                        $this->error = '新增基础内容出错！';
                        return false;
                    }
                }else{
                    $status = $this->save(); //更新基础内容
                    if($status){
                        $status = $extend_document_model->save(); //更新基础内容
                        if(false === $status){
                            $this->error = '更新扩展内容出错！';
                            return false;
                        }
                        return $extend_data;
                    }else{
                        $this->error = '更新基础内容出错！';
                        return false;
                    }
                }
            }
        }
        return false;
    }
    
    public function detail($id){
        $info = $this->find($id);
        if(!(is_array($info) || 1 !== $info['status'])){
            $this->error = '文档被禁用或已删除！';
            return false;
        }
        $category_info = D('Category')->find($info['cid']);
        $doc_type = D('Type')->where(array('id' => $category_info['doc_type']))->getField('name');
        $extend_document_model = D('Document'.$doc_type);
        $extend_data = $extend_document_model->find($id);
        if(is_array($extend_data)){
            $info = array_merge($info, $extend_data);
        }
        return $info;
    }

    /**
     * 获取当前分类上一篇文档
     * @author jry <598821125@qq.com>
     */
    public function getPreviousDocument($info){
        $map['status'] = array('eq', 1);
        $map['id'] = array('gt', $info['id']);
        $map['cid'] = array('eq', $info['cid']);
        $previous = $this->where($map)->order('id asc')->find();
        if(!$previous){
            $previous['title'] = '没有了';
            $previous['disabled'] = "disabled";
            $previous['link'] = '#';
        }else{
            $previous['link'] = U('Document/detail', array('id' => $previous['id']));
        }
        return $previous;
    }

    /**
     * 获取当前分类下一篇文档
     * @author jry <598821125@qq.com>
     */
    public function getNextDocument($info){
        $map['status'] = array('eq', 1);
        $map['id'] = array('lt', $info['id']);
        $map['cid'] = array('eq', $info['cid']);
        $next = $this->where($map)->order('id desc')->find();
        if(!$next){
            $next['title'] = '没有了';
            $next['disabled'] = "disabled";
            $next['link'] = '#';
        }else{
            $next['link'] = U('Document/detail', array('id' => $next['id']));
        }
        return $next;
    }
}
