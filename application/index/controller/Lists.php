<?php

namespace app\index\controller;

use app\common\controller\Webconfig;
use app\user\model\Usergoodlist;
use think\Session;

class Lists extends Webconfig
{
    public function index()
    {
        $linkid = request()->param('linkid');
        $usergoodcate_row = db('usergoodcate')->where('linkid', $linkid)->field(true)->find();
        if (empty($usergoodcate_row)) {
            header('Content-Type: text/html; charset=utf-8');
            echo "没有该商品！";
            exit();
        }
        $userid = $usergoodcate_row['userid'];
        $cateid = $usergoodcate_row['goodcateid'];
        $userdefine_row = db('userdefine')->where('userid', $userid)->field(true)->find();
        if (!empty($userdefine_row)) {
            if (isMobile()) {
                $paytheme = 'mobile';
            } else {
                $paytheme = $userdefine_row['paytheme'];
            }
            $gonggao = $userdefine_row['usernotice'];
        } else {
            header('Content-Type: text/html; charset=utf-8');
            echo "该卖家不存在！";
            exit();
        }
        $catename = $usergoodcate_row['catename'];
        if ($usergoodcate_row['is_trush'] == '1') {
            header('Content-Type: text/html; charset=utf-8');
            echo "该分类商品已经不存在！";
            exit();
        }
        if ($usergoodcate_row['is_state'] == '0') {
            header('Content-Type: text/html; charset=utf-8');
            echo "该分类商品已被卖家下架！";
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
        $goodlist_model = new Usergoodlist();
        $condition = ['userid' => $userid, 'cateid' => $cateid, 'is_state' => '1', 'is_trush' => '0',];
        $goodlist_arr = $goodlist_model->get_column_by_condition_on_index('goodlistid,goodname', $condition);
        $token = time();
        Session::init(['expire' => 300, 'prefix' => '1106111321_',]);
        session($linkid . '_token', $token);
        $userdefine_row['token'] = $token;
        $guestconfig_arr = cache('guestconfig');
        $copyright = $guestconfig_arr['copyright'];
        $sitetile = $guestconfig_arr['sitetile'];
        $sitekeyword = $guestconfig_arr['sitekeyword'];
        $description = $guestconfig_arr['description'];
        $paychannel_arr = db('adminpaychannel')->where(['is_state' => '1', 'is_trush' => '0'])->field('payway')->select();
        $payway_arr = [];
        foreach ($paychannel_arr as $k => $v) {
            $payway_arr[$k] = $v['payway'];
        }
        $assign_arr = array('css' => '/paytheme/' . $paytheme . '/css', 'js' => '/paytheme/' . $paytheme . '/js', 'images' => '/paytheme/' . $paytheme . '/images', 'layer' => '/paytheme/' . $paytheme . '/layer', 'common' => '/paytheme/common', 'list' => $userdefine_row, 'goodlist_arr' => $goodlist_arr, 'copyright' => $copyright, 'sitetile' => $sitetile, 'sitekeyword' => $sitekeyword, 'description' => $description, 'gonggao' => $gonggao, 'payway_arr' => $payway_arr, 'flag' => 'lists', 'linkid' => $linkid, 'catename' => $catename,);
        $this->assign($assign_arr);
        return $this->fetch('paytheme/' . $paytheme);
    }
} 