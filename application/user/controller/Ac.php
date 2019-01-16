<?php

namespace app\user\controller;

use app\user\model\Usercoupon;
use app\user\model\Usergoodlist;

class Ac extends UserBase
{
    function _initialize()
    {
        parent::_initialize();
        $assign_arr = array();
        $this->assign($assign_arr);
    }

    public function index()
    {
        $goodlist_model = new Usergoodlist();
        $goodlist_arr = $goodlist_model->get_column_by_session_userid_on_user('goodlistid,goodname');
        $assign_arr = array('title' => "加优惠卷", 'arr' => $goodlist_arr,);
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function export()
    {
        if (request()->isGet()) {
            $post = input('get.');
            $addtime = date('Y-m-d H:i:s');
            $gptime_stamp = mktime(date('H'), date('i'), date('s'), date('m'), date('d') + $post['validity'], date('Y'));
            $gptime = date('Y-m-d H:i:s', $gptime_stamp);
            if ($post['goodlistid'] == 'is_all') {
                for ($i = 1; $i <= $post['count']; $i++) {
                    $load_arr[] = ['userid' => session('user.userid'), 'is_all' => '1', 'addtime' => $addtime, 'validity' => $post['validity'], 'gptime' => $gptime, 'amount' => $post['amount'], 'remark' => $post['remark'], 'couponcode' => getRandomString(16), 'is_state' => '0',];
                }
                $goodname = "所有商品";
            } else {
                for ($i = 1; $i <= $post['count']; $i++) {
                    $load_arr[] = ['userid' => session('user.userid'), 'goodlistid' => $post['goodlistid'], 'addtime' => $addtime, 'validity' => $post['validity'], 'gptime' => $gptime, 'amount' => $post['amount'], 'remark' => $post['remark'], 'couponcode' => getRandomString(16), 'is_state' => '0',];
                }
                $goodname = db('usergoodlist')->where('goodlistid', $post['goodlistid'])->value('goodname');
            }
            $usercoupon_model = new Usercoupon();
            $res = $usercoupon_model->insert_all_by_arr_on_user($load_arr);
            $txtname = date('Y') . date('m') . date('d') . $goodname . "（优惠卷）" . ".txt";
            $content = $this->get_export_content($load_arr, $goodname);
            export_txt($content, $txtname);
            exit();
        }
    }

    public function addsave()
    {
        if (request()->isPost()) {
            $post = input('post.');
            $addtime = date('Y-m-d H:i:s');
            $gptime_stamp = mktime(date('H'), date('i'), date('s'), date('m'), date('d') + $post['validity'], date('Y'));
            $gptime = date('Y-m-d H:i:s', $gptime_stamp);
            if ($post['goodlistid'] == 'is_all') {
                for ($i = 1; $i <= $post['count']; $i++) {
                    $load_arr[] = ['userid' => session('user.userid'), 'is_all' => '1', 'addtime' => $addtime, 'validity' => $post['validity'], 'gptime' => $gptime, 'amount' => $post['amount'], 'remark' => $post['remark'], 'couponcode' => getRandomString(16), 'is_state' => '0',];
                }
                $goodname = "所有商品";
            } else {
                for ($i = 1; $i <= $post['count']; $i++) {
                    $load_arr[] = ['userid' => session('user.userid'), 'goodlistid' => $post['goodlistid'], 'addtime' => $addtime, 'validity' => $post['validity'], 'gptime' => $gptime, 'amount' => $post['amount'], 'remark' => $post['remark'], 'couponcode' => getRandomString(16), 'is_state' => '0',];
                }
                $goodname = db('usergoodlist')->where('goodlistid', $post['goodlistid'])->value('goodname');
            }
            $usercoupon_model = new Usercoupon();
            $res = $usercoupon_model->insert_all_by_arr_on_user($load_arr);
            if (count($res)) {
                return json_success();
            } else {
                return json_error();
            }
        }
    }

    public function get_export_content($content, $goodname)
    {
        $string = "商品名称" . "\t" . "优惠码" . "\t" . "有效期时间" . "\t" . "\r\n";
        foreach ($content as $k => $v) {
            $string .= $goodname . "\t" . $v['couponcode'] . "\t" . $v['gptime'] . "\t" . "\r\n";
        }
        return $string;
    }
} 