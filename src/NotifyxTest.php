<?php

namespace Liwei\Guotong;

class NotifyxTest
{
    public function notifyx()
    {
        $input = file_get_contents('php://input');
        $pay = new \Liwei\Guotong\Base();

        if($pay->checkSign($input) === false) { // 验证签名
            return false;
        }
        $data = json_decode($input,true);

        // $data['THREE_ORDER_NO'] 自己的订单号
        $order = []; // 查询订单

        if (!$order || $order['state']!=1) { // 如果订单不存在 或者 订单已经支付过了
            return json_encode(['rspCod' => 0, 'rspMsg' => 'success']);// 我已经处理完了，订单没找到，别再通知我了
        }

        if ($data['ORDER_STATUS'] === '1') { // ORDER_STATUS 支付状态 1代表成功

        }
        return json_encode(['rspCod' => 0, 'rspMsg' => 'success']);
    }

}