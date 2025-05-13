<?php

namespace Liwei\Guotong\order;

use Liwei\Guotong\Base;
use Liwei\Guotong\Config;

class Pay extends Base
{

    /**
     * 统一下单
     * @param $pay_type 支付方式 1.微信支付（小程序） 2.支付宝支付 3.银联支付
     * @param $params
     * @return void
     */
    public function orderPay($data)
    {

        $url = '/yyfsevr/order/pay';
        $data = [
            'orderNo' => $data['orderNo'],
            'txamt' => $data['txamt'] * 100,
            'openid' =>  $data['openid'] , // 根据微信、支付宝、银联给出的官方文档获取，微信是openid，支付宝是userId（以 2088 开头），银联用户标识看前往对接前准备-“获取银联用户标识”接口获取
            'payWay' => $data['payWay'],
            'ip' => $data['ip'],
            'outTime' => $data['outTime'] ?? 5,  //未传输时默认15，最小1分钟，最大值15分钟，单位（分）
            'asyncNotify' => $data['asyncNotify'],
            'title' => $data['title'] ?? '', // 订单标题,
            'wxAppid' => $this->config->WxAppId ?? '', // 微信公众账号app_id 	微信支付必传
            'traType' => $data['traType'], // 微信支付必传，5公众号 8小程序。（京东白条可传，因白条是基于微信公众号或小程序来跳转白条支付的，未传默认为公众号）
            'zfbappid' => $data['zfbappid'] ?? '', // 支付宝appid 支付宝支付必传
            'qrCode' => $data['qrCode'] ?? '', // 银联支付  交易二维码链接
            'qrCodeType' => $data['qrCodeType'] ?? '', // 银联支付，收款二维码为动态码时，则上送值为0；当收款二维码为静态码时，则上送值为1
            'remark' => $data['remark'] ?? '', // 订单备注
            'asyncNotify' => $this->config->AsyncNotify . $data['asyncNotify'] ?? '', // 异步通知地址
        ];


        $response = $this->request($url, $data);
        
        return $response;
    }



    public function  scanByMerchant($data)
    {
        
        $url = '/yyfsevr/order/scanByMerchant';
        $data = [
            'orderNo' => $data['orderNo'],
            'txamt' => $data['txamt'] * 100,
            'code' => $data['code'], //设备读取用户微信或支付宝中的条码或者二维码信息（付款码）
            'type' => $data['type'],
            'tradingIp' => $_SERVER['SERVER_ADDR'], // 商户端终端设备 IP 地址
            'driveNo' => $data['driveNo'] ?? '', // 自定义设备编号
            'isPop' => $data['isPop'] ?? 0, // 是否为碰一碰
            'asyncNotify' => $data['asyncNotify'] ?? '', // 自定义设备编号
        ];
        
        $response = $this->request($url, $data);
        
        return $response;
    }


    /**
     * 扫码支付
     * @return void
     */
    public function getCodeUrl($order_no,$txamt,$asyncNotify)
    {
        $url = '/yyfsevr/order/getCodeUrl'; // 生成电子码
        $data = [
            'orderNo' => $order_no,
//            'txamt' => $txamt*100, // 分
            'txamt' => 1, // 分
            'outTime'=> 5,//过期时间5分钟,
            'asyncNotify' => $asyncNotify

        ];

        $response = $this->request($url,$data);

        return $response;
    }


    public function getQrcode($url,$orderno)
    {
        $path = public_path() . 'qrcode';
        $qrCode = \Endroid\QrCode\QrCode::create($url);

        $qrCode->setEncoding(new \Endroid\QrCode\Encoding\Encoding('UTF-8'));
        $qrCode->setSize(200);
        $qrCode->setMargin(10);

        $result = (new \Endroid\QrCode\Writer\PngWriter())->write($qrCode);
        if(!is_dir($path)){
            mkdir($path,0777,true);
        }
        $qrcode_file = $path."/". $orderno . '.png';
        $result->saveToFile($qrcode_file);

        return $qrcode_file;
    }

    /**
     * @param $orderNo      退款订单号
     * @param $tOrderNo     第三方平台订单号
     * @param $refundAmount 退款金额(分)
     * @param $tag          订单类型:1支付宝 2微信 9银联（类型必须填写正确，与正向交易订单类型一致）12数币支付
     * @param $asyncNotifyUrl 回调地址
     * @param $remark        备注
     * @return mixed
     */
    public function refund($orderNo, $tOrderNo, $refundAmount, $tag, $asyncNotifyUrl, $remark = '')
    {
        $url = '/yyfsevr/order/refund';
        $data = [
            'orderNo' => $orderNo,
            'oldTOrderNo' => $tOrderNo,
            'refundAmount' => $refundAmount * 100, // 分
            'tag' => $tag,
            'asyncNotifyUrl' => '', // 回调地址
            'remark' => '', // 回调地址
        ];
        
        $response = $this->request($url, $data);
        // dd($response);exit;
        return $response;
    }


    /**
     * 查询订单支付状态
     * @param $orderTime 订单交易日期
     * @param $orderNo 第三方订单号
     * @return mixed
     */
    public function payQuery($orderTime, $orderNo)
    {
        $url = '/yyfsevr/order/orderQuery';
        $data = [
            'orderTime' => $orderTime,
            'orderNo' => $orderNo
        ];
        $response = $this->request($url, $data);
        return $response;
    }


}