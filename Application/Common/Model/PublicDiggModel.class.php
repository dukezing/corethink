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
 * 万能Digg模型
 * @author jry <598821125@qq.com>
 */
class PublicDiggModel extends Model{
    /**
     * 自动验证规则
     * @author jry <598821125@qq.com>
     */
    protected $_validate = array(
        array('table', 'require', '数据表ID错误', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('data_id', 'require', '数据ID错误', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('type', 'require', '类型错误', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('uid', 'require', '用户ID错误', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
    );

    /**
     * 自动完成规则
     * @author jry <598821125@qq.com>
     */
    protected $_auto = array(
        array('uid', 'is_login', self::MODEL_BOTH, 'function'),
        array('ctime', NOW_TIME, self::MODEL_INSERT),
        array('utime', NOW_TIME, self::MODEL_BOTH),
        array('status', '1', self::MODEL_INSERT),
    );
}
