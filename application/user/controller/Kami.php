<?php

namespace app\user\controller;

use app\user\model\Usergoodlist;
use app\user\model\Userproduct;
use think\Db;

class Kami extends UserBase
{
    function _initialize()
    {
        parent::_initialize();
        $safekey_model = new Safekey();
        $safekey_model->checkSafeKey();
        $assign_arr = array();
        $this->assign($assign_arr);
    }

    public function index()
    {
        $assign_arr = array();
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function add()
    {
        $goodlist_model = new Usergoodlist();
        $goodlist_arr = $goodlist_model->get_column_by_session_userid_on_user('goodlistid,goodname', ['is_trush' => '0', 'is_sub' => 0]);
        $assign_arr = array('title' => "添加卡密", 'goodlist_arr' => $goodlist_arr,);
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function addsave()
    {
        if (request()->isPost()) {
            $post = input('post.');
            if (!empty($post)) {
                $goodlistid = $post['goodlistid'];
                $ktype = $post['ktype'];
                $format = 1;
                $userid = session('user.userid');
                $addtime = date('Y-m-d H:i:s');
                if (empty($goodlistid) || empty($ktype)) {
                    return json_error('请先选择商品、上传方式');
                }
                $goodlist_model = new Usergoodlist();
                $goodlist_row = $goodlist_model->get_one_by_session_userid_on_user(['goodlistid' => $goodlistid]);
                if ($goodlist_row['userid'] != $userid) {
                    return json_error('参数错误，不是当前用户');
                }
                if ($ktype == 'input') {
                    $product_content = $post['kami'] ? explode("\r\n", $post['kami']) : array();
                    if (empty($product_content)) {
                        return json_error('请先输入卡密');
                    }
                    $product_content = unsert_null_value($product_content);
                    $allowQuantity = 100000;
                    $kami_model = new Userproduct();
                    $condition_count = ['goodlistid' => $goodlistid, 'is_state' => '1', 'is_trush' => '0',];
                    $user_count = $kami_model->get_count_by_condition_on_base($condition_count);
                    $post_count = count($product_content);
                    $last_count = $allowQuantity - $user_count;
                    if ($post_count > $last_count) {
                        return json_error('单个商品库存最大10W张卡密，本次上传：' . $post_count . '张卡密。' . '还可上传：' . $last_count . '张卡密！');
                    } elseif ($last_count <= 0) {
                        return json_error('当前商品库存量已超过系统允许库存量（' . $allowQuantity . '）张，无法继续添加!');
                    }
                    if ($post_count >= 10000) {
                        return json_error('单次上传限制（10000）张，请重新调整添加!');
                    }
                    $load_data = array();
                    if ($format == '1') {
                        foreach ($product_content as $k => $v) {
                            $trimval = trim($v);
                            $nbsp = '';
                            if (substr_count($trimval, " ") > 1) {
                                for ($i = 1; $i <= substr_count($trimval, " "); $i++) {
                                    $nbsp .= " ";
                                }
                                $trimval = str_replace($nbsp, ' ', $trimval);
                            }
                            if (strpos($trimval, " ")) {
                                $arr_cards = explode(' ', $trimval);
                                $cardnum = $arr_cards[0];
                                $cardpwd = $arr_cards[1];
                            } else {
                                $cardnum = $trimval;
                                $cardpwd = '';
                            }
                            $load_data[$k]['userid'] = $userid;
                            $load_data[$k]['addtime'] = $addtime;
                            $load_data[$k]['goodlistid'] = $goodlistid;
                            $load_data[$k]['cardnumber'] = $cardnum;
                            $load_data[$k]['cardpassword'] = $cardpwd;
                        }
                    } else {
                        switch ($format) {
                            case 2:
                                $nbsp = ',';
                                break;
                            case 3:
                                $nbsp = '|';
                                break;
                            case 4:
                                $nbsp = '----';
                                break;
                        }
                        foreach ($product_content as $k => $v) {
                            $trimval = trim($v);
                            if (strpos($trimval, $nbsp)) {
                                $arr_cards = explode($nbsp, $trimval);
                                $cardnum = $arr_cards[0];
                                $cardpwd = $arr_cards[1];
                            } else {
                                $cardnum = $trimval;
                                $cardpwd = '';
                            }
                            $load_data[$k]['userid'] = $userid;
                            $load_data[$k]['addtime'] = $addtime;
                            $load_data[$k]['goodlistid'] = $goodlistid;
                            $load_data[$k]['cardnumber'] = $cardnum;
                            $load_data[$k]['cardpassword'] = $cardpwd;
                        }
                    }
                    $chunk_arr = array_chunk($load_data, 1000);
                    $card_quantity = count($load_data);
                    Db::startTrans();
                    foreach ($chunk_arr as $k => $v) {
                        $res1 = $kami_model->insert_all_by_arr_on_user($v);
                        if (is_null($res1)) {
                            Db::rollback();
                            return json_error();
                        }
                    }
                    if ($card_quantity) {
                        Db::commit();
                        return json_success('上传（' . $post_count . '）张卡密，成功导入（' . $card_quantity . '）张卡密。');
                    } else {
                        Db::rollback();
                        return json_error();
                    }
                } elseif ($ktype == 'txt') {
                    $file = request()->file('uploadfile');
                    $info = $file->getInfo();
                    $file_type = $info['type'];
                    if ($file_type <> 'text/plain') {
                        return json_success('仅支持TXT格式的文件上传');
                    }
                    $file_size = $info['size'];
                    if ($file_size <= 0 || $file_size > 2 * 1024 * 1024) {
                        return json_success('上传的文件最大支持2MB');
                    }
                    $file_error = $info['error'];
                    if ($file_error <> 0) {
                        return json_success('上传出现意外错误，请稍候重试...');
                    }
                    $file_tmp = $info['tmp_name'];
                    $goods = file($file_tmp);
                    $product_content = unsert_null_value($goods);
                    $allowQuantity = 100000;
                    $kami_model = new Userproduct();
                    $condition_count = ['goodlistid' => $goodlistid, 'is_state' => '1', 'is_trush' => '0',];
                    $user_count = $kami_model->get_count_by_condition_on_base($condition_count);
                    $post_count = count($product_content);
                    $last_count = $allowQuantity - $user_count;
                    if ($post_count > $last_count) {
                        return json_error('单个商品库存最大10W张卡密，本次上传：' . $post_count . '张卡密。' . '还可上传：' . $last_count . '张卡密！');
                    } elseif ($last_count <= 0) {
                        return json_error('当前商品库存量已超过系统允许库存量（' . $allowQuantity . '）张，无法继续添加!');
                    }
                    if ($post_count >= 10000) {
                        return json_error('单次上传限制（10000）张，请重新调整添加!');
                    }
                    $load_data = array();
                    if ($format == '1') {
                        foreach ($product_content as $k => $v) {
                            $trimval = trim($v);
                            $nbsp = '';
                            if (substr_count($trimval, " ") > 1) {
                                for ($i = 1; $i <= substr_count($trimval, " "); $i++) {
                                    $nbsp .= " ";
                                }
                                $trimval = str_replace($nbsp, ' ', $trimval);
                            }
                            if (strpos($trimval, " ")) {
                                $arr_cards = explode(' ', $trimval);
                                $cardnum = $arr_cards[0];
                                $cardpwd = $arr_cards[1];
                            } else {
                                $cardnum = $trimval;
                                $cardpwd = '';
                            }
                            $load_data[$k]['userid'] = $userid;
                            $load_data[$k]['addtime'] = $addtime;
                            $load_data[$k]['goodlistid'] = $goodlistid;
                            if ($encode = mb_detect_encoding($cardnum, array("ASCII", "GB2312", "GBK", 'BIG5'))) {
                                $cardnum = iconv($encode, "UTF-8", $cardnum);
                            }
                            if ($encode = mb_detect_encoding($cardpwd, array("ASCII", "GB2312", "GBK", 'BIG5'))) {
                                $cardpwd = iconv($encode, "UTF-8", $cardpwd);
                            }
                            $load_data[$k]['cardnumber'] = $cardnum;
                            $load_data[$k]['cardpassword'] = $cardpwd;
                        }
                    } else {
                        switch ($format) {
                            case 2:
                                $nbsp = ',';
                                break;
                            case 3:
                                $nbsp = '|';
                                break;
                            case 4:
                                $nbsp = '----';
                                break;
                        }
                        foreach ($product_content as $k => $v) {
                            $trimval = trim($v);
                            if (strpos($trimval, $nbsp)) {
                                $arr_cards = explode($nbsp, $trimval);
                                $cardnum = $arr_cards[0];
                                $cardpwd = $arr_cards[1];
                            } else {
                                $cardnum = $trimval;
                                $cardpwd = '';
                            }
                            $load_data[$k]['userid'] = $userid;
                            $load_data[$k]['addtime'] = $addtime;
                            $load_data[$k]['goodlistid'] = $goodlistid;
                            if ($encode = mb_detect_encoding($cardnum, array("ASCII", "GB2312", "GBK", 'BIG5'))) {
                                $cardnum = iconv($encode, "UTF-8", $cardnum);
                            }
                            if ($encode = mb_detect_encoding($cardpwd, array("ASCII", "GB2312", "GBK", 'BIG5'))) {
                                $cardpwd = iconv($encode, "UTF-8", $cardpwd);
                            }
                            $load_data[$k]['cardnumber'] = $cardnum;
                            $load_data[$k]['cardpassword'] = $cardpwd;
                        }
                    }
                    $chunk_arr = array_chunk($load_data, 1000);
                    $card_quantity = count($load_data);
                    Db::startTrans();
                    foreach ($chunk_arr as $k => $v) {
                        $res1 = $kami_model->insert_all_by_arr_on_user($v);
                        if (is_null($res1)) {
                            Db::rollback();
                            return json_error();
                        }
                    }
                    if ($card_quantity) {
                        Db::commit();
                        return json_success('上传（' . $post_count . '）张卡密，成功导入（' . $card_quantity . '）张卡密。');
                    } else {
                        Db::rollback();
                        return json_error();
                    }
                }
            }
        }
    }
}