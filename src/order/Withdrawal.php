<?php

namespace Liwei\Guotong\order;

use Liwei\Guotong\Base;

class Withdrawal extends Base
{
    /**
     * 商户额度查询
     * @return void
     */
    public function queryLimit()
    {
        $url = '/yyfsevr/drawcash/queryLimit'; // 生成电子码
        $response = $this->request($url,[]);
        return $response;
    }
}