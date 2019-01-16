<?php
namespace app\index\controller;
use think\Controller;

class ReturnUrl extends Controller
{

    /**
     * 同步跳转
     * @return \think\response\Redirect
     */
    public function index()
    {
        if (request()->isGet()) {
            //1.获取数据
            $orderid = (string)(input('get.out_trade_no'));
            if (!empty($orderid)) {
                $return_url = request()->domain() . '/orderquery?kw=' . $orderid;
                return redirect($return_url);
            }
            $this->error('缺少订单号', url('/index'));
        }
    }
}
