<?php

namespace app\common\model;

use think\Model;

class BasePage extends Model
{
    public function get_page($count, $pageSize = '20')
    {
        $now_page = request()->get('page') ? request()->get('page') : '1';
        $total_page = ceil($count / $pageSize);
        $param = request()->param();
        unset($param['page']);
        $url_param = http_build_query($param);
        $pathinfo = "/center/" . request()->pathinfo();
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
        $page_content = '';
        $page_count = 0;
        if ($now_page >= 10) {
            if (($now_page + 5 - $total_page) >= 0) {
                $limit_page = $total_page;
            } else {
                $limit_page = $now_page + 5;
            }
            for ($i = ($now_page - 5) > 0 ? $now_page - 5 : '1'; $i <= $limit_page; $i++) {
                $li_class = $now_page == $i ? '' : '';
                $li_a_class = $now_page == $i ? '' : '';
                $li_a_style = $now_page == $i ? 'color:#409fff;' : '';
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
                $li_a_class = $now_page == $i ? '' : '';
                $li_a_style = $now_page == $i ? 'color:#409fff;' : '';
                $i_page = $pathinfo . $page_params_string . $i;
                $page_content .= '
                    <a class="' . $li_a_class . '" style="' . $li_a_style . '" href="' . $i_page . '"><i>' . $i . '</i></a>
                ';
                $page_count++;
            }
        }
        $page_content_left = '
          <div class="m-pages" style="vertical-align:top;font-size:0;overflow:hidden;display:inline-block;">
            <div style="zoom:1;">
              <a style="width: 60px;" href="' . $shangyiye_url . '"><i>上一页</i></a>
        ';
        $page_content_right = '
              <a style="width: 60px;border-right:1px solid #e3e5eb;" href="' . $xiayiye_url . '"><i>下一页</i></a>
            </div>
          </div>
        ';
        $page_content = $page_content_left . $page_content . $page_content_right;
        if ($now_page != $total_page) {
            $now_count = 20;
        } else {
            $now_count = $count - ($now_page - 1) * 20;
        }
        $page_right = '
            <p style="float:right;">
                本页：<span class="blue">' . $now_count . '</span>条数据&nbsp;&nbsp;&nbsp;总共：<span class="blue">' . $count . '</span>条数据
            </p>
        ';
        $page_string = $page_content . $page_right;
        return $page_string;
    }
}