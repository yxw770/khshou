<?php

namespace app\user\model;

use app\admin\model\Adminpaychannel;
use app\common\model\BaseModel;

class DlOrderlist extends BaseModel
{
    protected $table = '1106111321_orderlist';

    public function get_order_list_on_daili($condition = [])
    {
        $res = $this->get_list_by_condition_on_base($condition);
        $list = $res[0];
        $goodlist_model = new Usergoodlist();
        foreach ($list as $k => $v) {
            $list[$k]['goodname'] = $goodlist_model->get_value_by_session_userid_on_user('goodname', ['goodlistid' => $v['goodlistid']]);
        }
        $res[0] = $list;
        return $res;
    }
}