<?php

namespace app\index\controller;

use think\Controller;

class Notify extends Controller
{
    public function index()
    {
        $directory = input('directory');
        $assign_arr = array('directory' => $directory,);
        $this->assign($assign_arr);
        return $this->fetch(APP_PATH_INDEX . 'pay/' . $directory . "/notify.php");
    }

    private function getprovider_by_orderid($orderid, $paymoney)
    {
        $orderlist_row = db('orderlist')->where(['orderid' => $orderid, 'is_state' => '1', 'is_trush' => '0',])->field('providerid,ordermoney')->find();
        if (empty($orderlist_row)) {
            return [];
        }
        if ($paymoney < $orderlist_row['ordermoney']) {
            return [];
        }
        $adminpayprovider_row = db('adminpayprovider')->where(['providerid' => $orderlist_row['providerid'], 'is_state' => '1', 'is_trush' => '0',])->field(true)->find();
        if (!empty($adminpayprovider_row)) {
            $adminpayprovider_row['ordermoney'] = $orderlist_row['ordermoney'];
            return $adminpayprovider_row;
        } else {
            return [];
        }
    }

    public function xmlToArray($content)
    {
        libxml_disable_entity_loader(true);
        $xml = simplexml_load_string($content, 'SimpleXMLElement', LIBXML_NOCDATA);
        $array = json_decode(json_encode($xml), true);
        return $array;
    }
} 