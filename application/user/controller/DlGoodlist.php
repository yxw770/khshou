<?php

namespace app\user\controller;

use app\admin\model\Adminconfig;
use app\common\oss\Oss;
use app\user\model\Usergoodcate;
use app\user\model\Usergoodlist;
use app\user\model\Userwholesale;
use think\Db;

class DlGoodlist extends UserBase
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
        $goodcate_model = new Usergoodcate();
        $condition = ['is_trush' => '0',];
        $goodcate_arr = $goodcate_model->get_column_by_session_userid_on_user('goodcateid,catename', $condition);
        if (request()->isGet()) {
            $get = input('get.');
            if (isset($get['do']) && $get['do'] == 'search' && !empty($get['cateid'])) {
                $condition = ['is_trush' => '0', 'cateid' => $get['cateid'], 'is_sub' => '1',];
                $goodlist_arr = $goodlist_model->get_dlgoodlist_list_on_daili($condition);
                $list = $goodlist_arr[0];
                $page = $goodlist_arr[1];
            } else {
                $condition = ['is_trush' => '0', 'is_sub' => '1',];
                $goodlist_arr = $goodlist_model->get_dlgoodlist_list_on_daili($condition);
                $list = $goodlist_arr[0];
                $page = $goodlist_arr[1];
            }
        }
        $assign_arr = array('title' => "代理商品列表", 'list' => $list, 'page' => $page, 'cate_arr' => $goodcate_arr,);
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function add()
    {
        $goodcate_model = new Usergoodcate();
        $goodcate_arr = $goodcate_model->get_column_by_session_userid_on_user('goodcateid,catename', ['is_trush' => '0']);
        $assign_arr = array('title' => "添加代理商品", 'cate_arr' => $goodcate_arr, 'paytheme_arr' => unserialize(paytheme_arr), 'contact_limit_arr' => unserialize(contact_limit_arr),);
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function addsave()
    {
        if (request()->isPost()) {
            $post = input('post.');
            if (!empty($post)) {
                $goodname = $post['goodname'];
                $price = $post['price'];
                $cateid = $post['cateid'];
                if (empty($goodname) || empty($price) || empty($cateid)) {
                    return json_error('请先选择分类，输入商品名称和商品价格');
                }
                $goodname = $post['goodname'];
                $adminconfig_model = new Adminconfig();
                $condition = ['id' => '1',];
                $is_filterkey = $adminconfig_model->get_value_by_condition_on_admin('is_filterkey', $condition);
                if ($is_filterkey == '1') {
                    $fileterkey_row = $adminconfig_model->get_value_by_condition_on_admin('filterkey', $condition);
                    if (!empty($fileterkey_row)) {
                        $fileterkey_arr = explode('|', $fileterkey_row);
                        foreach ($fileterkey_arr as $k => $v) {
                            if (stripos($goodname, $v) !== false) {
                                $goodname = str_replace($v, '', $goodname);
                            }
                        }
                        if ($goodname == '' || !$goodname || empty($goodname)) {
                            return json_error('商品名称检测是被屏蔽的关键字，已拒绝操作。');
                        }
                        $post['goodname'] = $goodname;
                    }
                }
                unset($post['is_sub']);
                unset($post['sub_code']);
                unset($post['parent_id']);
                unset($post['is_author']);
                unset($post['pr_name']);
                unset($post['pr_price']);
                $post['addtime'] = date('Y-m-d H:i:s');
                $post['userid'] = session('user.userid');
                $post['linkid'] = getRandomString(16);
                $goodcate_model = new Usergoodcate();
                $condition = ['goodcateid' => $cateid,];
                $catename = $goodcate_model->get_value_by_session_userid_on_user('catename', $condition);
                if (empty($catename)) {
                    return json_error('请先选择分类');
                }
                $post['catename'] = $catename;
                $goodlist_model = new Usergoodlist();
                $sub_code = htmlspecialchars(trim(input('post.dl_code')));
                $goodlist_row = $goodlist_model->get_one_by_session_userid_on_daili(['sub_code' => $sub_code]);
                if (empty($goodlist_row)) {
                    return json_error('授权码不存在');
                }
                if ($post['price'] < $goodlist_row['price']) {
                    return json_error('单价：' . $post['price'] . ' 不能小于代理商品的单价：' . $goodlist_row['price'] . '元！');
                }
                if ($goodlist_row['userid'] == session('user.userid')) {
                    return json_error('不能代理自己的商品');
                }
                $counts = $goodlist_model->get_count_by_condition_on_daili(['parent_id' => $goodlist_row['goodlistid']]);
                if ($counts != 0) {
                    exit("<script>alert('该商品已经存在代理商品列表中');history.go(-1);</script>");
                }
                $dl_arr = ['is_sub' => 1, 'parent_id' => $goodlist_row['goodlistid'], 'pr_name' => $goodlist_row['goodname'], 'pr_price' => $goodlist_row['price'],];
                $post = array_merge($post, $dl_arr);
                Db::startTrans();
                $goodlist_model = new Usergoodlist();
                $goodlistid = $goodlist_model->insert_one_by_arr_on_user($post);
                if (!$goodlistid) {
                    Db::rollback();
                    return json_error('保存失败');
                }
                if ($post['is_wholesale'] == '1') {
                    $wholesale_load = array();
                    $quantity_arr = $post['define_quantity'];
                    $price_arr = $post['define_price'];
                    foreach ($quantity_arr as $k => $v) {
                        if ($v > 0 && $price_arr[$k] > 0) {
                            $wholesale_load[$k]['userid'] = session('user.userid');
                            $wholesale_load[$k]['goodlistid'] = $goodlistid;
                            $wholesale_load[$k]['define_quantity'] = $v;
                            $wholesale_load[$k]['define_price'] = $price_arr[$k];
                        }
                    }
                    if (!empty($quantity_arr)) {
                        $wholesale_model = new Userwholesale();
                        $res3 = $wholesale_model->insert_all_by_arr_on_user($wholesale_load);
                        if (!$res3) {
                            Db::rollback();
                            return json_error();
                        }
                    }
                    Db::commit();
                    return json_success();
                }
                Db::commit();
                return json_success();
            }
        }
    }

    public function edit()
    {
        $route = request()->route();
        $goodlistid = input('param.goodlistid');
        $goodlist_model = new Usergoodlist();
        $condition = array('goodlistid' => $goodlistid,);
        $goodlist_row = $goodlist_model->get_one_by_session_userid_on_user($condition);
        $goodcate_model = new Usergoodcate();
        $condition = ['is_trush' => '0',];
        $goodcate_arr = $goodcate_model->get_column_by_session_userid_on_user('goodcateid,catename', $condition);
        $wholesale_model = new Userwholesale();
        $condition_wholesale = array('goodlistid' => $goodlistid,);
        $wholesale_arr = $wholesale_model->get_column_by_session_userid_on_user('define_quantity,define_price', $condition_wholesale);
        $assign_arr = array('title' => "修改商品", 'list' => $goodlist_row, 'cate_arr' => $goodcate_arr, 'wholesale_arr' => $wholesale_arr, 'paytheme_arr' => unserialize(paytheme_arr), 'contact_limit_arr' => unserialize(contact_limit_arr),);
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function editsave()
    {
        if (request()->isPost()) {
            $post = input('post.');
            $goodname = $post['goodname'];
            $adminconfig_model = new Adminconfig();
            $condition = ['id' => '1',];
            $is_filterkey = $adminconfig_model->get_value_by_condition_on_admin('is_filterkey', $condition);
            if ($is_filterkey == '1') {
                $fileterkey_row = $adminconfig_model->get_value_by_condition_on_admin('filterkey', $condition);
                if (!empty($fileterkey_row)) {
                    $fileterkey_arr = explode('|', $fileterkey_row);
                    foreach ($fileterkey_arr as $k => $v) {
                        if (stripos($goodname, $v) !== false) {
                            $goodname = str_replace($v, '', $goodname);
                        }
                    }
                    if ($goodname == '' || !$goodname || empty($goodname)) {
                        return json_error('商品名称检测是被屏蔽的关键字，已拒绝操作。');
                    }
                    $post['goodname'] = $goodname;
                }
            }
            unset($post['is_sub']);
            unset($post['sub_code']);
            unset($post['parent_id']);
            unset($post['is_author']);
            unset($post['pr_name']);
            unset($post['pr_price']);
            Db::startTrans();
            $goodlistid = input('param.goodlistid');
            $goodlist_model = new Usergoodlist();
            $condition = array('goodlistid' => $goodlistid,);
            $res1 = $goodlist_model->update_one_by_session_userid_on_user($post, $condition);
            if ($post['is_wholesale'] == '1') {
                $wholesale_model = new Userwholesale();
                $wholesale_arr = $wholesale_model->get_list_by_session_userid_on_user($condition, '', '', '');
                if (!empty($wholesale_arr)) {
                    $res2 = $wholesale_model->del_list_by_condition_on_user($condition);
                    if (!$res2) {
                        Db::rollback();
                        return json_error();
                    }
                }
                $wholesale_load = array();
                $quantity_arr = $post['define_quantity'];
                $price_arr = $post['define_price'];
                foreach ($quantity_arr as $k => $v) {
                    if ($v > 0 && $price_arr[$k] > 0) {
                        $wholesale_load[$k]['userid'] = session('user.userid');
                        $wholesale_load[$k]['goodlistid'] = $goodlistid;
                        $wholesale_load[$k]['define_quantity'] = $v;
                        $wholesale_load[$k]['define_price'] = $price_arr[$k];
                    }
                }
                if (!empty($quantity_arr)) {
                    $res3 = $wholesale_model->insert_all_by_arr_on_user($wholesale_load);
                    if (!$res1 && !$res3) {
                        Db::rollback();
                        return json_error();
                    }
                }
                Db::commit();
                return json_success();
            } else {
                if (!$res1) {
                    Db::rollback();
                    return json_error();
                }
            }
            Db::commit();
            return json_success();
        }
    }

    public function link()
    {
        $route = request()->route();
        $goodlistid = input('param.goodlistid');
        $goodlist_model = new Usergoodlist();
        $condition = array('goodlistid' => $goodlistid,);
        $goodlist_row = $goodlist_model->get_one_by_session_userid_on_user($condition);
        if (empty($goodlist_row['linkid'])) {
            $load_arr['linkid'] = getRandomString(16);
            $goodlist_model = new Usergoodlist();
            $res = $goodlist_model->update_one_by_session_userid_on_user($load_arr, ['goodlistid' => $goodlistid]);
            $goodlist_row['linkid'] = $load_arr['linkid'];
        }
        $base_url = cache('guestconfig')['detailurl'];
        $long_url = "http://" . $base_url . "/detail/" . $goodlist_row['linkid'];
        $short_url = dwz($long_url);
        $goodlist_row['long_url'] = $long_url;
        $goodlist_row['short_url'] = $short_url;
        $assign_arr = array('title' => "商品链接", 'list' => $goodlist_row,);
        $this->layout = false;
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function togglestate()
    {
        if (request()->isPost()) {
            $post = input('post.');
            Db::startTrans();
            $goodlist_model = new Usergoodlist();
            $condition = array('goodlistid' => $post['goodlistid']);
            $load_arr = array('is_state' => $post['is_state'],);
            $res1 = $goodlist_model->update_one_by_session_userid_on_user($load_arr, $condition);
            if (!$res1) {
                Db::rollback();
                return json_error();
            }
            Db::commit();
            return json_success();
        }
    }

    public function resetlink()
    {
        if (request()->isPost()) {
            $post = input('post.');
            Db::startTrans();
            $goodlist_model = new Usergoodlist();
            $condition = array('goodlistid' => $post['goodlistid']);
            $load_arr = array('linkid' => getRandomString(16),);
            $res1 = $goodlist_model->update_one_by_session_userid_on_user($load_arr, $condition);
            if (!$res1) {
                Db::rollback();
                return json_error();
            }
            Db::commit();
            return json_success();
        }
    }

    public function del()
    {
        if (request()->isPost()) {
            $post = input('post.');
            Db::startTrans();
            $goodlist_model = new Usergoodlist();
            $condition = array('goodlistid' => $post['goodlistid']);
            $load_arr = array('is_trush' => '1',);
            $res1 = $goodlist_model->update_one_by_session_userid_on_user($load_arr, $condition);
            if (!$res1) {
                Db::rollback();
                return json_error();
            }
            Db::commit();
            return json_success();
        }
    }
} 