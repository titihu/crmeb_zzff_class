<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

/**
 * 敏感词过滤
 *
 * @param  string
 * @return string
 */
function sensitive_words_filter($str)
{
    header('content-type:text/html;charset=utf-8');
    if (!$str) return '';
    $file = ROOT_PATH . 'public/static/plug/censorwords/CensorWords';
    $words = file($file);
    foreach ($words as $word) {
        $word = str_replace(array("\r\n", "\r", "\n", " "), '', $word);
        if (!$word) continue;

        $ret = @preg_match("/$word/", $str, $match);
        if ($ret) {
            return $match[0];
        }
    }
    return '';
}

function getController()
{
    return strtolower(preg_replace('/((?<=[a-z])(?=[A-Z]))/', '_', think\Request::instance()->controller()));
}

function getModule()
{
    return strtolower(preg_replace('/((?<=[a-z])(?=[A-Z]))/', '_', think\Request::instance()->module()));
}

/**
 * 获取图片库链接地址
 * @param $key
 * @return string
 */
function get_image_Url($key)
{
    return think\Url::build('admin/widget.images/index', ['fodder' => $key]);
}


/**
 * 获取链接对应的key
 * @param $value
 * @param bool $returnType
 * @param string $rep
 * @return array|string
 * @throws \think\db\exception\DataNotFoundException
 * @throws \think\db\exception\ModelNotFoundException
 * @throws \think\exception\DbException
 */
function get_key_attr($value, $returnType = true, $rep = '')
{
    if (!$value) return '';
    $inif = \app\admin\model\system\SystemAttachment::where('att_dir', $value)->find();
    if ($inif) {
        return [
            'key' => $inif->name,
            'pic' => $value,
        ];
    } else {
        if ($returnType) {
            return [
                'key' => '',
                'pic' => $value,
            ];
        } else {
            return '';
        }
    }
}

/**
 * 获取系统配置内容
 * @param $name
 * @param string $default
 * @return string
 */
function get_config_content($name, $default = '')
{
    try {
        return \app\admin\model\system\SystemConfigContent::getValue($name);
    } catch (\Throwable $e) {
        return $default;
    }
}

/**
 * 打印日志
 * @param $name
 * @param $data
 * @param int $type
 */
function live_log($name, $data, $type = 8)
{
    file_put_contents($name . '.txt', '[' . date('Y-m-d H:i:s', time()) . ']' . print_r($data, true) . "\r\n", $type);
}

/**获取当前登录用户的角色信息
 * @return mixed
 */
function get_login_role() {
    $role['role_id'] = \think\Session::get("adminInfo")['roles'];
    $role['role_sign'] = \think\Session::get("adminInfo")['role_sign'];
    return $role;
}

/**获取登录用户账户信息
 * @return mixed
 */
function get_login_id() {
    $admin['admin_id'] = \think\Session::get("adminId");
    return $admin;
}

/**全局修改数据
 * @param string $field要修改的字段
 * @param array $where条件 数组
 * @param string $value修改值
 * @param $model_type 数据库表  不带前缀
 */
function set_field_value(array $update, array $where , $value = '', $model_type) {
    if (!$update || !$where || $model_type == '') {
        return \service\JsonService::fail('缺少参数');
    }
    $model_type = \service\ModeService::switch_model($model_type);
    //print_r($model_type);die;
    if (!$model_type)  return \service\JsonService::fail('缺少参数');

    $res = $model_type::where($where)->update($update);
    if ($res) return \service\JsonService::successful('保存成功');
    return \service\JsonService::fail('保存失败');

}

function money_rate_num($money, $type) {
    if (!$money || is_numeric($money)) return \service\JsonService::fail('非法参数');
    if (!$type) return \service\JsonService::fail('非法参数');
    switch ($type) {
        case "gold":
            $goldRate = \service\SystemConfigService::get("gold_rate");
                $num = ($money * 10) * (int) $goldRate;
            return $num;
        default:
            return \service\JsonService::fail('汇率类型缺失');

    }
}