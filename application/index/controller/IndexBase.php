<?php

namespace app\index\controller;

use app\common\controller\Webconfig;

class IndexBase extends Webconfig
{
    function _initialize()
    {
        parent::_initialize();
        if (isMobile()) {
            $qttheme = 'mobile';
        } else {
            $qttheme = cache('adminconfig')['qttheme'];
        }
        $this->view->config('view_path', APP_PATH_INDEX . $qttheme . "/");
        $assign_arr = array('css' => '/index/' . $qttheme . '/css', 'js' => '/index/' . $qttheme . '/js', 'images' => '/index/' . $qttheme . '/images', 'layer' => '/index/' . $qttheme . '/layer',);
        $this->assign($assign_arr);
    }
} 