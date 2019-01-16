<?php

namespace app\user\model;

use app\common\model\BaseModel;

class ALL extends BaseModel
{
    public function get_one_by_session_userid_on_user($condition = [])
    {
        if (empty($condition) || $condition == '') {
            $condition = ['userid' => session('user.userid'),];
        } else {
            $condition['userid'] = session('user.userid');
        }
        $res = $this->get_one_by_condition_on_base($condition);
        return $res;
    }

    public function get_value_by_session_userid_on_user($field, $condition = [])
    {
        if (empty($condition) || $condition == '') {
            $condition = ['userid' => session('user.userid'),];
        } else {
            $condition['userid'] = session('user.userid');
        }
        $res = $this->get_value_by_condition_on_base($condition, $field);
        return $res;
    }

    public function update_one_by_session_userid_on_user($post, $condition = [])
    {
        if (empty($condition) || $condition == '') {
            $condition = ['userid' => session('user.userid'),];
        } else {
            $condition['userid'] = session('user.userid');
        }
        $res = $this->update_one_by_condition_on_base($post, $condition);
        return $res;
    }

    public function insert_one_by_arr_on_user($arr)
    {
        $res = $this->insert_one_by_arr_on_base($arr);
        return $res;
    }

    public function get_list_by_session_userid_on_user($condition = [], $order = [], $offset = '', $limit = '20')
    {
        if (empty($condition) || $condition == '') {
            $condition = ['userid' => session('user.userid'),];
        } else {
            $condition['userid'] = session('user.userid');
        }
        $res = $this->get_list_by_condition_on_base($condition, $order, $offset, $limit);
        return $res;
    }

    public function insert_all_by_arr_on_user($arr)
    {
        $res = $this->inser_all_by_arr_on_base($arr);
        return $res;
    }

    public function del_list_by_condition_on_user($condition)
    {
        if (empty($condition) || $condition == '') {
            $condition = ['userid' => session('user.userid'),];
            exit();
        } else {
            $condition['userid'] = session('user.userid');
        }
        $del = $this->del_list_by_condition_on_base($condition);
        return $del;
    }

    public function group_field_by_condition_on_user($condition = [], $field, $group = '')
    {
        if (empty($condition) || $condition == '') {
            $condition = ['userid' => session('user.userid'),];
        } else {
            $condition['userid'] = session('user.userid');
        }
        $res = $this->group_field_by_condition_on_base($condition, $field, $group);
        return $res;
    }

    public function get_column_by_session_userid_on_user($field, $condition = [])
    {
        if (empty($condition) || $condition == '') {
            $condition = ['userid' => session('user.userid'),];
        } else {
            $condition['userid'] = session('user.userid');
        }
        $res = $this->get_column_by_condition_on_base($condition, $field);
        return $res;
    }

    public function get_count_by_condition_on_user($condition)
    {
        if (empty($condition) || $condition == '') {
            $condition = ['userid' => session('user.userid'),];
        } else {
            $condition['userid'] = session('user.userid');
        }
        $return = $this->get_count_by_condition_on_base($condition);
        return $return;
    }

    public function get_kami_list_on_user($condition = [])
    {
        if (empty($condition) || $condition == '') {
            $condition = ['userid' => session('user.userid'),];
        } else {
            $condition['userid'] = session('user.userid');
        }
        $res = $this->get_list_by_condition_on_base($condition);
        $list = $res[0];
        $goodlist_model = new Usergoodlist();
        foreach ($list as $k => $v) {
            $list[$k]['goodname'] = $goodlist_model->get_value_by_session_userid_on_user('goodname', ['goodlistid' => $v['goodlistid']]);
            $list[$k]['price'] = $goodlist_model->get_value_by_session_userid_on_user('price', ['goodlistid' => $v['goodlistid']]);
        }
        $res[0] = $list;
        return $res;
    }

    public function get_goodlist_list_on_user($condition = [])
    {
        if (empty($condition) || $condition == '') {
            $condition = ['userid' => session('user.userid'),];
        } else {
            $condition['userid'] = session('user.userid');
        }
        $res = $this->get_list_by_condition_on_base($condition, 'sortid,goodlistid desc');
        $list = $res[0];
        $goodlistid_in = [];
        foreach ($list as $k => $v) {
            $product_model = new Userproduct();
            $goodlistid_in[$k] = $v['goodlistid'];
        }
        $count_onsell_kami_in = $product_model->group_field_by_condition_on_base(array_merge($condition, ['goodlistid' => ['in', $goodlistid_in], 'is_state' => '1', 'is_trush' => '0']), 'goodlistid,count(*) AS count', 'goodlistid');
        $count_selled_kami_in = $product_model->group_field_by_condition_on_base(array_merge($condition, ['goodlistid' => ['in', $goodlistid_in], 'is_state' => '0', 'is_trush' => '0']), 'goodlistid,count(*) AS count', 'goodlistid');
        foreach ($list as $k => $v) {
            foreach ($count_onsell_kami_in as $k2 => $v2) {
                if ($v['goodlistid'] == $v2['goodlistid']) {
                    $list[$k]['count_onsell_kami'] = $v2['count'];
                } else {
                    $list[$k]['count_onsell_kami'] = 0;
                }
            }
            foreach ($count_selled_kami_in as $k2 => $v2) {
                if ($v['goodlistid'] == $v2['goodlistid']) {
                    $list[$k]['count_selled_kami'] = $v2['count'];
                } else {
                    $list[$k]['count_selled_kami'] = 0;
                }
            }
        }
        $res[0] = $list;
        return $res;
    }

    public function get_sold_list_on_user($condition = [])
    {
        if (empty($condition) || $condition == '') {
            $condition = ['userid' => session('user.userid'),];
        } else {
            $condition['userid'] = session('user.userid');
        }
        $res = $this->get_list_by_condition_on_base($condition);
        $list = $res[0];
        $goodlist_model = new Usergoodlist();
        foreach ($list as $k => $v) {
            $list[$k]['goodname'] = $goodlist_model->get_value_by_session_userid_on_user('goodname', ['goodlistid' => $v['goodlistid']]);
        }
        $res[0] = $list;
        return $res;
    }

    public function get_export_sold_on_user($condition = [])
    {
        if (empty($condition) || $condition == '') {
            $condition = ['userid' => session('user.userid'),];
        } else {
            $condition['userid'] = session('user.userid');
        }
        $res = $this->get_list_by_condition_on_base($condition, [], [], []);
        if (!empty($res)) {
            $goodlist_model = new Usergoodlist();
            foreach ($res as $k => $v) {
                $res[$k]['goodname'] = $goodlist_model->get_value_by_session_userid_on_user('goodname', ['goodlistid' => $v['goodlistid']]);
            }
            return $res;
        } else {
            return [];
        }
    }

    public function get_seo_list_on_user($condition = [])
    {
        $adminpaychennel_model = new Adminpaychannel();
        $adminpaychennel_arr = $adminpaychennel_model->get_list_by_condition_on_admin([], [], [], []);
        if (empty($condition) || $condition == '') {
            $condition = ['userid' => session('user.userid'),];
        } else {
            $condition['userid'] = session('user.userid');
        }
        $return = [];
        foreach ($adminpaychennel_arr as $k => $v) {
            $return[$k]['channelname'] = $v['channelname'];
            $return[$k]['order_count'] = $this->get_count_by_condition_on_base(array_merge($condition, ['channelid' => $v['channelid']]));
            $return[$k]['pay_count'] = $this->get_count_by_condition_on_base(array_merge($condition, ['channelid' => $v['channelid'], 'is_buylist' => '1']));
            $return[$k]['unpay_count'] = $this->get_count_by_condition_on_base(array_merge($condition, ['channelid' => $v['channelid'], 'is_buylist' => '0']));
            $return[$k]['ordermoney_sum'] = number_format($this->sum_by_condition_on_user(array_merge($condition, ['channelid' => $v['channelid']]), 'ordermoney'), 2, '.', '');
            $return[$k]['paymoney_sum'] = number_format($this->sum_by_condition_on_user(array_merge($condition, ['channelid' => $v['channelid'], 'is_buylist' => '1']), 'paymoney'), 2, '.', '');
            $return[$k]['usergain_sum'] = number_format($this->sum_by_condition_on_user(array_merge($condition, ['channelid' => $v['channelid'], 'is_buylist' => '1']), 'paymoney') * $v['userproportion'], 2, '.', '');
        }
        if (!empty($return)) {
            return $return;
        } else {
            return [];
        }
    }

    public function sum_by_condition_on_user($condition = [], $sumfield)
    {
        if (empty($condition) || $condition == '') {
            $condition = ['userid' => session('user.userid'),];
        } else {
            $condition['userid'] = session('user.userid');
        }
        $res = $this->sum_by_condition_on_base($condition, $sumfield);
        if (!empty($res)) {
            return $res;
        } else {
            return 0;
        }
    }

    public function get_export_seo_on_user($condition = [])
    {
        if (empty($condition) || $condition == '') {
            $condition = ['userid' => session('user.userid'),];
        } else {
            $condition['userid'] = session('user.userid');
        }
        $res = $this->get_list_by_condition_on_base($condition, [], [], []);
        if (!empty($res)) {
            $goodlist_model = new Usergoodlist();
            foreach ($res as $k => $v) {
                $res[$k]['goodname'] = $goodlist_model->get_value_by_session_userid_on_user('goodname', ['goodlistid' => $v['goodlistid']]);
            }
            return $res;
        } else {
            return [];
        }
    }

    public function get_income_list_on_user($condition = [], $order = [], $offset = '', $limit = '20')
    {
        if (empty($condition) || $condition == '') {
            $condition = ['userid' => session('user.userid'),];
        } else {
            $condition['userid'] = session('user.userid');
        }
        $res = $this->get_list_by_condition_on_base($condition, $order, $offset, $limit);
        $list = $res[0];
        $goodlist_model = new Usergoodlist();
        foreach ($list as $k => $v) {
            $row = $goodlist_model->get_column_by_condition_on_base(['goodlistid' => $v['goodlistid']], 'goodname,cost');
            $list[$k]['goodname'] = $row[0]['goodname'];
            $list[$k]['cost'] = $row[0]['cost'];
        }
        $res[0] = $list;
        return $res;
    }

    public function get_export_income_on_user($condition = [], $order = [], $offset = '', $limit = '20')
    {
        if (empty($condition) || $condition == '') {
            $condition = ['userid' => session('user.userid'),];
        } else {
            $condition['userid'] = session('user.userid');
        }
        $res = $this->get_list_by_condition_on_base($condition, $order, $offset, $limit);
        $goodlist_model = new Usergoodlist();
        foreach ($res as $k => $v) {
            $row = $goodlist_model->get_column_by_condition_on_base(['goodlistid' => $v['goodlistid']], 'goodname,cost');
            $res[$k]['goodname'] = $row[0]['goodname'];
            $res[$k]['cost'] = $row[0]['cost'];
        }
        return $res;
    }

    public function get_order_list_on_user($condition = [])
    {
        if (empty($condition) || $condition == '') {
            $condition = ['userid' => session('user.userid'),];
        } else {
            $condition['userid'] = session('user.userid');
        }
        $res = $this->get_list_by_condition_on_base($condition);
        $list = $res[0];
        $goodlist_model = new Usergoodlist();
        foreach ($list as $k => $v) {
            $list[$k]['goodname'] = $goodlist_model->get_value_by_session_userid_on_user('goodname', ['goodlistid' => $v['goodlistid']]);
        }
        $res[0] = $list;
        return $res;
    }

    public function get_export_order_on_user($condition = [])
    {
        if (empty($condition) || $condition == '') {
            $condition = ['userid' => session('user.userid'),];
        } else {
            $condition['userid'] = session('user.userid');
        }
        $res = $this->get_one_by_condition_on_base($condition);
        if (!empty($res)) {
            $goodlist_model = new Usergoodlist();
            $res['goodname'] = $goodlist_model->get_value_by_session_userid_on_user('goodname', ['goodlistid' => $res['goodlistid']]);
            return $res;
        } else {
            return [];
        }
    }

    public function get_one_list_kemi_on_user($condition = [])
    {
        if (empty($condition) || $condition == '') {
            $condition = ['userid' => session('user.userid'),];
        } else {
            $condition['userid'] = session('user.userid');
        }
        $res = $this->get_column_by_condition_on_base($condition, 'productid');
        $kami_model = new Userproduct();
        foreach ($res as $k => $v) {
            $condition['productid'] = $v['productid'];
            $res[$k]['cardnumber'] = $kami_model->get_value_by_session_userid_on_user('cardnumber', $condition);
            $res[$k]['cardpassword'] = $kami_model->get_value_by_session_userid_on_user('cardpassword', $condition);
        }
        return $res;
    }
} 