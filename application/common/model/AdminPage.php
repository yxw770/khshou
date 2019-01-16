<?php

namespace app\common\model;

use think\Model;

class AdminPage extends Model
{
    public function get_page($count, $pageSize = '20')
    {
        $now_page = request()->get('page') ? request()->get('page') : '1';
        $total_page = ceil($count / $pageSize);
        $param = request()->param();
        unset($param['page']);
        $url_param = http_build_query($param);
        $pathinfo = "/" . urlRoot . "/" . request()->pathinfo();
        if (empty($url_param)) {
            $page_params_string = "?page=";
        } else {
            $page_params_string = "?" . $url_param . "&page=";
        }
        $shouye_url = $pathinfo;
        $weiye_url = $pathinfo . $page_params_string . $total_page;
        $shagnyiye_page = ($now_page - 1) > 0 ? $now_page - 1 : 1;
        $shangyiye_url = $pathinfo . $page_params_string . $shagnyiye_page;
        $xiayiye_page = ($now_page + 1) <= $total_page ? $now_page + 1 : $total_page;
        $xiayiye_url = $pathinfo . $page_params_string . $xiayiye_page;
        $page_content = '
            <div class="pagelist fr">
			<a href="' . $shouye_url . '" class="first">«</a>
			<a href="' . $shangyiye_url . '" class="prev">‹</a>
        ';
        $page_count = 0;
        if ($now_page >= 10) {
            if (($now_page + 5 - $total_page) >= 0) {
                $limit_page = $total_page;
            } else {
                $limit_page = $now_page + 5;
            }
            for ($i = ($now_page - 5) > 0 ? $now_page - 5 : '1'; $i <= $limit_page; $i++) {
                $li_class = $now_page == $i ? '' : '';
                $li_a_class = $now_page == $i ? 'cur' : '';
                $li_a_style = $now_page == $i ? '' : '';
                $i_page = $pathinfo . $page_params_string . $i;
                if ($now_page >= 1000) {
                    $page_content .= '
                    <a class="' . $li_a_class . '" style="' . $li_a_style . '" href="' . $i_page . '"><i>' . $i . '</i></a>
                ';
                }
                if ($now_page < 1000) {
                    $page_content .= '
                    <a class="' . $li_a_class . '" style="' . $li_a_style . '" href="' . $i_page . '"><i>' . $i . '</i></a>
                ';
                }
                $page_count++;
            }
        } else {
            for ($i = 1; $i <= 10; $i++) {
                if ($i > $total_page) {
                    break;
                }
                $li_class = $now_page == $i ? '' : '';
                $li_a_class = $now_page == $i ? 'cur' : '';
                $li_a_style = $now_page == $i ? '' : '';
                $i_page = $pathinfo . $page_params_string . $i;
                $page_content .= '
                    <a class="' . $li_a_class . '" style="' . $li_a_style . '" href="' . $i_page . '"><i>' . $i . '</i></a>
                ';
                $page_count++;
            }
        }
        $page_content = $page_content . '
			<a href="' . $weiye_url . '" class="next">&rsaquo;</a>
			<a href="' . $xiayiye_url . '" class="last">&raquo;</a>
		</div>
        ';
        if ($now_page != $total_page) {
            $now_count = 20;
        } else {
            $now_count = $count - ($now_page - 1) * 20;
        }
        $page_content_left = '
            <div class="pagefont fl">
                本页：' . $now_count . '条数据 &nbsp;&nbsp;&nbsp;&nbsp;总共：' . $count . '条数据
            </div>
        ';
        $page_content = '<div class="page_bottom clearfix">' . $page_content_left . $page_content . '</div>';
        return $page_content;
    }
}