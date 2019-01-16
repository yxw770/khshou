<?php

namespace app\index\controller;

use think\Controller;

class Ajax extends Controller
{
    public function getlists()
    {
        if (request()->isPost()) {
            $post_arr = trim_arr(input('post.'));
            $linkid_post = $post_arr['linkid'];
            $userid_post = $post_arr['userid'];
            $cateid = $post_arr['cateid'];
            $token = $post_arr['token'];
            if (empty($linkid_post) || empty($userid_post) || empty($cateid) || empty($token)) {
                return json_error('params error!');
            }
            if (empty($token) || $token != session($linkid_post . '_token')) {
                return json_error($token . 'params error!' . session($linkid_post . '_token'));
            }
            $head = request()->header();
            $referer = $head['referer'];
            $referer_arr = parse_url($referer);
            $path = $referer_arr['path'];
            $path_arr = explode('/', $path);
            $referer_controller = $path_arr[1];
            $linkid = $path_arr[2];
            if (empty($linkid) || $linkid != $linkid_post) {
                return json_error();
            }
            if ($referer_controller == 'links') {
                $userid = db('userdefine')->where('linkid', $linkid)->value('userid');
                if (empty($userid)) {
                    return json_error('params error!');
                }
            } elseif ($referer_controller == 'detail') {
            } elseif ($referer_controller == 'lists') {
            }
            $condition = ['userid' => $userid, 'cateid' => $cateid, 'is_state' => 1, 'is_trush' => 0,];
            $usergoodlist_row = db('usergoodlist')->where($condition)->field('goodlistid,goodname')->order('sortid,goodlistid desc')->select();
            return $usergoodlist_row ? json_success($usergoodlist_row) : json_error($usergoodlist_row);
        }
        return false;
    }

    public function getdetail()
    {
        if (request()->isPost()) {
            $post_arr = trim_arr(input('post.'));
            $linkid_post = $post_arr['linkid'];
            $userid_post = $post_arr['userid'];
            $goodlistid = $post_arr['goodlistid'];
            $token = $post_arr['token'];
            if (empty($linkid_post) || empty($userid_post) || empty($goodlistid) || empty($token)) {
                return json_error('params error!');
            }
            if (empty($token) || $token != session($linkid_post . '_token')) {
                return json_error('params error!');
            }
            $head = request()->header();
            $referer = $head['referer'];
            $referer_arr = parse_url($referer);
            $path = $referer_arr['path'];
            $path_arr = explode('/', $path);
            $referer_controller = $path_arr[1];
            $linkid = $path_arr[2];
            if (empty($linkid) || $linkid != $linkid_post) {
                return json_error();
            }
            if ($referer_controller == 'links') {
                $userid = db('userdefine')->where('linkid', $linkid)->value('userid');
                if (empty($userid)) {
                    return json_error('params error!');
                }
            } elseif ($referer_controller == 'detail') {
                $usergoodlist_row = db('usergoodlist')->where('linkid', $linkid)->field(true)->find();
                $userid = $usergoodlist_row['userid'];
            } elseif ($referer_controller == 'lists') {
                $userid = db('usergoodcate')->where('linkid', $linkid)->value('userid');
            };
            $condition = ['userid' => $userid, 'goodlistid' => $goodlistid,];
            $usergoodlist_row = db('usergoodlist')->where(array_merge($condition, ['is_state' => '1', 'is_trush' => '0']))->field(true)->find();
            $is_sub = $usergoodlist_row['is_sub'];
            if ($is_sub == '1') {
                $kucun_count = db('userproduct')->where(array_merge(['goodlistid' => $usergoodlist_row['parent_id']], ['is_state' => '1', 'is_trush' => '0']))->count();
            } else {
                $kucun_count = db('userproduct')->where(array_merge($condition, ['is_state' => '1', 'is_trush' => '0']))->count();
            }
            $usergoodlist_row['kucun'] = $kucun_count;
            if ($usergoodlist_row['is_wholesale'] == '1') {
                $userwholesale_arr = db('userwholesale')->where($condition)->field('define_quantity,define_price')->select();
                if (!empty($userwholesale_arr)) {
                    $usergoodlist_row['wholesale'] = $userwholesale_arr;
                }
            }
            $kucun_show = db('userdefine')->where(['userid' => $userid])->value('kucun_show');
            $usergoodlist_row['kucun_status'] = $kucun_show == 1 ? 2 : 1;
            return json_success($usergoodlist_row);
        }
        return false;
    }

    public function checkcoupon()
    {
        if (request()->isPost()) {
            $post_arr = trim_arr(input('post.'));
            $linkid_post = $post_arr['linkid'];
            $userid_post = $post_arr['userid'];
            $couponcode_post = $post_arr['couponcode'];
            $goodlistid = $post_arr['goodlistid'];
            $token = $post_arr['token'];
            if (empty($linkid_post) || empty($userid_post) || empty($goodlistid) || empty($token) || empty($couponcode_post)) {
                return json_error('params error!');
            }
            if (empty($token) || $token != session($linkid_post . '_token')) {
                return json_error('params error!');
            }
            $head = request()->header();
            $referer = $head['referer'];
            $referer_arr = parse_url($referer);
            $path = $referer_arr['path'];
            $path_arr = explode('/', $path);
            $referer_controller = $path_arr[1];
            $linkid = $path_arr[2];
            if (empty($linkid) || $linkid != $linkid_post) {
                return json_error();
            }
            if ($referer_controller == 'links') {
                $userid = db('userdefine')->where('linkid', $linkid)->value('userid');
                if (empty($userid)) {
                    return json_error('params error!');
                }
            } elseif ($referer_controller == 'detail') {
            } elseif ($referer_controller == 'lists') {
            };
            $condition = ['userid' => $userid, 'couponcode' => $couponcode_post,];
            $usercoupon_row = db('usercoupon')->where($condition)->field(true)->find();
            if (empty($usercoupon_row)) {
                return json_error("没有此优惠卷");
            }
            if ($usercoupon_row['is_state'] == '1') {
                return json_error("优惠卷已使用");
            } else {
                if ($usercoupon_row['is_all'] == '1') {
                    if ((strtotime($usercoupon_row['gptime']) - time()) >= 0) {
                        return json_success('优惠卷可以使用', $usercoupon_row['amount']);
                    } else {
                        return json_error("优惠卷已过期");
                    }
                } else {
                    if ($goodlistid == $usercoupon_row['goodlistid']) {
                        if ((strtotime($usercoupon_row['gptime']) - time()) >= 0) {
                            return json_success('优惠卷可以使用', $usercoupon_row['amount']);
                        } else {
                            return json_error("优惠卷已过期");
                        }
                    } else {
                        return json_error("没有此优惠卷");
                    }
                }
            }
        }
        return false;
    }

    public function checkOrderid()
    {
        if (request()->isPost()) {
            $post_arr = trim_arr(input('post.'));
            $time = $post_arr['time'];
            $orderid = $post_arr['orderid'];
            if (empty($time)) {
                return json_error();
            }
            if ((time() - $time) > 300) {
                return json_error();
            }
            $is_buylist = db('orderlist')->where(['orderid' => $orderid])->value('is_buylist');
            if ($is_buylist == '1' || $is_buylist == '11') {
                return json_success();
            } else {
                return json_error();
            }
        }
    }

    public function help()
    {
        return $this->fetch();
    }
} 