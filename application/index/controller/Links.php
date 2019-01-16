<?php

namespace app\index\controller;

use app\common\controller\Webconfig;
use app\index\model\Usergoodcate;
use think\Session;

class Links extends Webconfig
{
    public function index()
    {
        $linkid = request()->param('linkid');
        $userdefine_row = db('userdefine')->where('linkid', $linkid)->field(true)->find();
        if (empty($userdefine_row)) {
            header('Content-Type: text/html; charset=utf-8');
            echo "没有该商品！";
            exit();
        }
        $userid = $userdefine_row['userid'];
        if (isMobile()) {
            $paytheme = 'mobile';
        } else {
            $paytheme = $userdefine_row['paytheme'];
        }
        $linkid = $userdefine_row['linkid'];
        if ($userdefine_row['link_state'] == '0') {
            header('Content-Type: text/html; charset=utf-8');
            echo "卖家已关闭店铺！";
            exit();
        }
        $user_state = db('user')->where('userid', $userid)->value('is_state');
        if ($user_state == '0') {
            header('Content-Type: text/html; charset=utf-8');
            echo "该卖家正在后台审核中！";
            exit();
        } elseif ($user_state == '2') {
            header('Content-Type: text/html; charset=utf-8');
            echo "该卖家已被系统冻结！";
            exit();
        }
        $goodcate_model = new Usergoodcate();
        $condition = ['userid' => $userid, 'is_state' => '1', 'is_trush' => '0',];
        $goodcate_arr = $goodcate_model->get_column_by_condition_on_index('goodcateid,catename', $condition);
        $token = time();
        Session::init(['expire' => 300, 'prefix' => '1106111321_',]);
        session($linkid . '_token', $token);
        $userdefine_row['token'] = $token;
        $guestconfig_arr = cache('guestconfig');
        $copyright = $guestconfig_arr['copyright'];
        $sitetile = $guestconfig_arr['sitetile'];
        $sitekeyword = $guestconfig_arr['sitekeyword'];
        $description = $guestconfig_arr['description'];
        $gonggao = $userdefine_row['usernotice'];
        $paychannel_arr = db('adminpaychannel')->where(['is_state' => '1', 'is_trush' => '0'])->field('payway')->select();
        $payway_arr = [];
        foreach ($paychannel_arr as $k => $v) {
            $payway_arr[$k] = $v['payway'];
        }
        $assign_arr = array('css' => '/paytheme/' . $paytheme . '/css', 'js' => '/paytheme/' . $paytheme . '/js', 'images' => '/paytheme/' . $paytheme . '/images', 'layer' => '/paytheme/' . $paytheme . '/layer', 'common' => '/paytheme/common', 'list' => $userdefine_row, 'cate_arr' => $goodcate_arr, 'copyright' => $copyright, 'sitetile' => $sitetile, 'sitekeyword' => $sitekeyword, 'description' => $description, 'gonggao' => $gonggao, 'payway_arr' => $payway_arr, 'flag' => 'links', 'linkid' => $linkid,);
        $this->assign($assign_arr);
        return $this->fetch('paytheme/' . $paytheme);
    }
} 