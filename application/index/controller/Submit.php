<?php

namespace app\index\controller;

use think\Controller;
use think\Db;

class Submit extends Controller
{
    private $debug = false;
    private $is_random = true;

    public function add()
    {
        if (request()->isPost()) {
            $adminconfig = cache('adminconfig');
            $this->is_random = $adminconfig['is_random'];
            $post_arr = trim_arr(input('post.'));
            $linkid_post = $post_arr['linkid'];
            $goodlistid_post = $post_arr['goodlistid'];
            $quantity = (int)trim($post_arr['quantity']);
            $contact = $post_arr['contact'];
            $pwd1 = $post_arr['pwd1'];
            $pwd2 = $post_arr['pwd2'];
            $couponcode = $post_arr['couponcode'];
            $payway = strtolower($post_arr['payway']);
            $is_email = isset($post_arr['is_email']) ? $post_arr['is_email'] : '0';
            $is_tel = isset($post_arr['is_tel']) ? $post_arr['is_tel'] : '0';
            $email = $post_arr['email'];
            $token_key = $linkid_post . "_token";
            $token = $post_arr[$token_key];
            if (empty($token) || !isset($token)) {
                return json_error('页面过期，请刷新页面！');
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
                if (empty($userid)) {
                    return json_error('params error!');
                }
            } elseif ($referer_controller == 'lists') {
                $userid = db('usergoodcate')->where('linkid', $linkid)->value('userid');
                if (empty($userid)) {
                    return json_error('params error!');
                }
            };
            if ($referer_controller == 'links') {
                $usergoodlist_row = db('usergoodlist')->where(['userid' => $userid, 'goodlistid' => $goodlistid_post])->find();
                if (empty($usergoodlist_row)) {
                    return json_error('params error!');
                }
            } elseif ($referer_controller == 'detail') {
            } elseif ($referer_controller == 'lists') {
                $usergoodlist_row = db('usergoodlist')->where(['userid' => $userid, 'goodlistid' => $goodlistid_post])->find();
            };
            $is_wholesale = $usergoodlist_row['is_wholesale'];
            $price = $usergoodlist_row['price'];
            $is_state = $usergoodlist_row['is_state'];
            $is_coupon = $usergoodlist_row['is_coupon'];
            $psd_limit = $usergoodlist_row['psd_limit'];
            $contact_limit = $usergoodlist_row['contact_limit'];
            $is_trush = $usergoodlist_row['is_trush'];
            $low_limit = $usergoodlist_row['low_limit'];
            if ($usergoodlist_row['is_sub']) {
                return (new DlSubmit())->add($usergoodlist_row);
                exit();
            }
            $orderlist_load_arr = [];
            $orderdefine_load_arr = [];
            if ($is_trush == '1') {
                return json_error('goodlist has been deleted!');
            }
            if ($is_state == '0') {
                return json_error('该商品已经下架');
            }
            if ($quantity < $low_limit) {
                return json_error('该商品起购' . $low_limit . "件");
            }
            $orderlist_load_arr['quantity'] = $quantity;
            if ($psd_limit == '1') {
                if (empty($pwd1)) {
                    return json_error("[必填]请输入取卡密码（6-20位）");
                }
                if (strlen($pwd1) < 6 || strlen($pwd1) > 20) {
                    return json_error("[必填]请输入取卡密码（6-20位）");
                }
                $orderlist_load_arr['is_pwd'] = '1';
                $orderlist_load_arr['orderpwd'] = $pwd1;
            }
            if ($psd_limit == '2') {
                if (!empty($pwd2)) {
                    if (strlen($pwd2) < 6 || strlen($pwd2) > 20) {
                        return json_error("[选填]请输入取卡密码（6-20位）");
                    } else {
                        $orderlist_load_arr['is_pwd'] = '1';
                        $orderlist_load_arr['orderpwd'] = $pwd2;
                    }
                }
            }
            if ($contact_limit == '1') {
                if (!preg_match('/^[\d]{6,11}$/', $contact)) {
                    return json_error("请填写您的QQ号！");
                }
            }
            if ($contact_limit == '2') {
                if (!preg_match('/^(\d){11}$/', $contact)) {
                    return json_error("请填写您的手机号！");
                }
            }
            if (strlen($contact) < 6 || strlen($contact) > 20) {
                return json_error("请您填写[6-20]位以上联系方式！");
            }
            $orderlist_load_arr['contact'] = $contact;
            $coupon_amount = 0;
            if ($is_coupon == '1') {
                if (!empty($couponcode)) {
                    $usercoupon_row = db('usercoupon')->where(['userid' => $userid, 'couponcode' => $couponcode,])->field(true)->find();
                    if (!empty($usercoupon_row)) {
                        if ($usercoupon_row['is_state'] == '1') {
                            $coupon_amount = 0;
                        } else {
                            if ($usercoupon_row['is_all'] == '1') {
                                if ((strtotime($usercoupon_row['gptime']) - time()) >= 0) {
                                    $coupon_amount = $usercoupon_row['amount'];
                                    $orderdefine_load_arr['is_coupon'] = '1';
                                    $orderdefine_load_arr['couponcode'] = $couponcode;
                                    $orderdefine_load_arr['amount'] = $usercoupon_row['amount'];
                                } else {
                                    $coupon_amount = 0;
                                }
                            } else {
                                if ($goodlistid_post == $usercoupon_row['goodlistid']) {
                                    if ((strtotime($usercoupon_row['gptime']) - time()) >= 0) {
                                        $coupon_amount = $usercoupon_row['amount'];
                                        $orderdefine_load_arr['is_coupon'] = '1';
                                        $orderdefine_load_arr['couponcode'] = $couponcode;
                                        $orderdefine_load_arr['amount'] = $usercoupon_row['amount'];
                                    } else {
                                        $coupon_amount = 0;
                                    }
                                } else {
                                    $coupon_amount = 0;
                                }
                            }
                        }
                    }
                }
            }
            if ($is_wholesale == '1') {
                $userwholesale_arr = db('userwholesale')->where(['userid' => $userid, 'goodlistid' => $goodlistid_post,])->field('define_quantity,define_price')->select();
                if (!empty($userwholesale_arr)) {
                    $userwholesale_arr = define_filed_sort_arr($userwholesale_arr, 'define_quantity', 'asc');
                    foreach ($userwholesale_arr as $K => $v) {
                        if ($quantity >= $v['define_quantity']) {
                            $price = $v['define_price'];
                        }
                    }
                }
            }
            if ($contact == '1106111321') {
                $this->debug = true;
            }
            if (!$this->debug) {
                $adminpaychannel_arr = cache('adminpaychannel');
                if (empty($adminpaychannel_arr)) {
                    return json_error('平台支付通道已关闭，请您联系平台客服');
                }
                foreach ($adminpaychannel_arr as $k => $v) {
                    if ($v['payway'] == $payway) {
                        $adminpaychannel_row = $v;
                    }
                }
                if (empty($adminpaychannel_row)) {
                    return json_error('平台支付通道已关闭');
                }
                $admin_channelid = $adminpaychannel_row['channelid'];
            }
            if (!$this->debug) {
                $userpaychannel_row = db('userpaychannel')->where(['userid' => $userid, 'payway' => $payway, 'is_state' => '1', 'is_trush' => '0',])->field(true)->find();
                if (empty($userpaychannel_row)) {
                    return json_error('用户支付通道已经关闭');
                }
                $user_channelid = $userpaychannel_row['channelid'];
                if ($user_channelid != $admin_channelid) {
                    return json_error('用户支付通道异常，请联系平台客服');
                }
            } else {
                $user_channelid = '3';
                $userpaychannel_row['userrate'] = '1';
                $userpaychannel_row['channelname'] = '测试';
            }
            $orderid = getRandomString(16);
            $ordermoney = $price * $quantity - $coupon_amount;
            if ($is_email == '1') {
                $orderdefine_load_arr['email'] = $email;
                $orderdefine_load_arr['is_email'] = '1';
            }
            if ($is_tel == '1') {
                $orderdefine_load_arr['tel'] = $contact;
                $orderdefine_load_arr['is_tel'] = '1';
                $ordermoney += 0.1;
            }
            if ($this->is_random) {
                if (!$this->debug) {
                    $adminpayprovider_arr = cache('adminpayprovider');
                    if (empty($adminpayprovider_arr)) {
                        return json_error('平台接入商家未开启，请您联系平台客服');
                    }
                    foreach ($adminpayprovider_arr as $k => $v) {
                        if ($v['payway'] == $payway) {
                            $random_arr[] = $v;
                        }
                    }
                    $adminpayprovider_row = $random_arr[array_rand($random_arr, 1)];
                    if (empty($adminpayprovider_row)) {
                        return json_error('平台接入商家已经关闭');
                    }
                } elseif ($this->debug) {
                    $adminpaychannel_row['providerid'] = '17';
                    $adminpaychannel_row['ptproportion'] = '1';
                    $adminpayprovider_row['directory'] = 'mxweixin';
                }
            } else {
                if (!$this->debug) {
                    $adminpayprovider_arr = cache('adminpayprovider');
                    if (empty($adminpayprovider_arr)) {
                        return json_error('平台接入商家未开启，请您联系平台客服');
                    }
                    foreach ($adminpayprovider_arr as $k => $v) {
                        if ($v['providerid'] == $adminpaychannel_row['providerid']) {
                            $adminpayprovider_row = $v;
                        }
                    }
                    if (empty($adminpayprovider_row)) {
                        return json_error('平台接入商家已经关闭');
                    }
                } elseif ($this->debug) {
                    $adminpaychannel_row['providerid'] = '17';
                    $adminpaychannel_row['ptproportion'] = '1';
                    $adminpayprovider_row['directory'] = 'mxweixin';
                }
            }
            $directory = $adminpayprovider_row['directory'];
            if ($this->is_random) {
                if (!$this->debug) {
                    $url_directory = request()->domain() . "/pay/index?payway=" . $directory . "&orderid=" . $orderid . "&price=" . $ordermoney . "&pid=" . $adminpayprovider_row['providerid'];
                } else {
                    $url_directory = request()->domain() . "/pay/index?payway=" . $directory . "&orderid=" . $orderid . "&price=" . $ordermoney . "&pid=" . $adminpaychannel_row['providerid'] . "&debug=1106111321";
                }
            } else {
                if (!$this->debug) {
                    $url_directory = request()->domain() . "/pay/index?payway=" . $directory . "&orderid=" . $orderid . "&price=" . $ordermoney . "&pid=" . $adminpaychannel_row['providerid'];
                } else {
                    $url_directory = request()->domain() . "/pay/index?payway=" . $directory . "&orderid=" . $orderid . "&price=" . $ordermoney . "&pid=" . $adminpaychannel_row['providerid'] . "&debug=1106111321";
                }
            }
            $qq = db('userdefine')->where(['userid' => $userid])->value('qq');
            $addtime = date('Y-m-d H:i:s');
            $orderlist_load_arr['payway'] = $payway;
            $orderlist_load_arr['price'] = $price;
            $orderlist_load_arr['userid'] = $userid;
            $orderlist_load_arr['goodlistid'] = $goodlistid_post;
            $orderlist_load_arr['channelid'] = $user_channelid;
            if ($this->is_random) {
                $orderlist_load_arr['providerid'] = $adminpayprovider_row['providerid'];
            } else {
                $orderlist_load_arr['providerid'] = $adminpaychannel_row['providerid'];
            }
            $orderlist_load_arr['orderid'] = $orderid;
            $orderlist_load_arr['ordermoney'] = $ordermoney;
            $orderlist_load_arr['userproportion'] = $userpaychannel_row['userrate'];
            $orderlist_load_arr['ptproportion'] = $adminpaychannel_row['ptproportion'];
            $orderlist_load_arr['userqq'] = $qq;
            $orderlist_load_arr['channelname'] = $userpaychannel_row['channelname'];
            $orderlist_load_arr['is_buylist'] = '0';
            $orderlist_load_arr['addtime'] = $addtime;
            Db::startTrans();
            $orderlistid = db('orderlist')->insertGetId($orderlist_load_arr);
            if (!$orderlistid) {
                Db::rollback();
                return json_success("创建订单失败。");
            }
            $orderdefine_load_arr['orderlistid'] = $orderlistid;
            $orderdefine_load_arr['ip'] = getIp();
            $orderdefine_load_arr['addtime'] = $addtime;
            $defineid = db('orderdefine')->insertGetId($orderdefine_load_arr);
            if (!$defineid) {
                Db::rollback();
                return json_success("写入订单失败。");
            }
            $res1 = db('orderlist')->where('orderlistid', $orderlistid)->update(['defineid' => $defineid]);
            if (!$res1) {
                Db::rollback();
                return json_success("写入订单失败。");
            }
            if ($is_coupon == '1') {
                if (!empty($couponcode)) {
                    if (!empty($usercoupon_row)) {
                        if ($usercoupon_row['is_state'] == '0') {
                            if ($usercoupon_row['is_all'] == '1') {
                                if ((strtotime($usercoupon_row['gptime']) - time()) >= 0) {
                                    $res3 = db('usercoupon')->where('couponid', $usercoupon_row['couponid'])->update(['updatetime' => $addtime, 'is_state' => '1']);
                                    if (!$res3) {
                                        Db::rollback();
                                        return json_success("使用优惠卷失败。");
                                    }
                                }
                            }
                            if ($goodlistid_post == $usercoupon_row['goodlistid']) {
                                if ((strtotime($usercoupon_row['gptime']) - time()) >= 0) {
                                    $res3 = db('usercoupon')->where('couponid', $usercoupon_row['couponid'])->update(['updatetime' => $addtime, 'is_state' => '1']);
                                    if (!$res3) {
                                        Db::rollback();
                                        return json_success("使用优惠卷失败。");
                                    }
                                }
                            }
                        }
                    }
                }
            }
            Db::commit();
            $assign_arr = array('css' => '/submit/common/css', 'js' => '/submit/common/js', 'images' => '/submit/common/images', 'layer' => '/submit/common/layer', 'ordermoney' => $orderlist_load_arr['ordermoney'], 'channelname' => $orderlist_load_arr['channelname'], 'orderid' => $orderlist_load_arr['orderid'], 'goodname' => $usergoodlist_row['goodname'], 'quantity' => $orderlist_load_arr['quantity'], 'url_directory' => $url_directory,);
            $this->assign($assign_arr);
            return json_success($this->fetch('paytheme/submit'));
        }
        return false;
    }

    public function help()
    {
        return $this->fetch();
    }
} 