<?php

namespace Liwei\Guotong;

class Base
{
    protected $config;


    public function __construct()
    {
        $this->config = new Config();
    }


    /**
     * 虚拟机关联商户号
     * @return void
     */
    public function connectCust()
    {
        $url = '/yyfsevr/order/connectCust';
        $data = [
            'agetId' => $this->config->agetId,
            'custId' => $this->config->custId,
            'opType' => 00, //00新增关联、01删除关联，(未传时默认00)
        ];
        $response = $this->request($url, $data);
        return $response;
    }

    /**
     * 获取签名
     * @param $data
     * @return string
     */
    public function getSign($data)
    {
        $arr = $data;
        ksort($arr);
        $str = '';
        foreach ($arr as $k=>$val) {
            $str .= "&".$k."=".$val;
        }

        $public_key = trim($this->config->PublicKey);
        $sha256 = hash("sha256", trim($str,"&"));
        $key_pem =
            "-----BEGIN PUBLIC KEY-----
" .
            chunk_split(
                $public_key,
                64,
                "
"
            ) .
            "-----END PUBLIC KEY-----";

        $privateKey = openssl_pkey_get_public($key_pem); //检测是否公钥
        openssl_public_encrypt($sha256, $sign, $privateKey); //公钥加密
        $sign = base64_encode($sign);
        return $sign;
    }


    /**
     * 验证 sign
     * @param string $plainText 返回数据报文（JSON 格式字符串）
     * @param string $publicKey 公钥（Base64 编码）
     * @return bool
     */
    public function checkSign($plainText) {
        // 将 JSON 转换为关联数组
        $map = json_decode($plainText, true);
        // dd($map);exit;
        if (!is_array($map)) {
            return false;
        }
        ksort($map);
        $sb = "";
        $sign = "";
        foreach ($map as $key => $value) {
            if ($key === "sign") {
                $sign = $value;
                continue;
            }
            $sb .= $key . "=" . $value . "&";
        }
        // 去掉末尾的 &
        $res = rtrim($sb, "&");
        // 计算 SHA256 哈希
        $sha256 = hash("sha256", $res);
       
        $decodeSha256 = $this->decrypt($sign);
        return $sha256 === $decodeSha256;
    }

   
    public function decrypt($sign)
    {
        $RSA_DECRYPT_BLOCK_SIZE = 256;
        $sign = base64_decode($sign);
        $data = str_split($sign, $RSA_DECRYPT_BLOCK_SIZE);
        $key_pem =
            "-----BEGIN PUBLIC KEY-----
" .
            chunk_split(
                trim($this->config->PublicKey),
                64,
                "
"
            ) .
            "-----END PUBLIC KEY-----"; // 公钥
        $pubKey = openssl_pkey_get_public($key_pem);
        $result = "";
        foreach ($data as $block) {
            openssl_public_decrypt($block, $dataDecrypt, $pubKey);
            $result .= $dataDecrypt;
        }
        return $dataDecrypt;
    }
   
  


    protected function request($url, $data)
    {
        //插入公共参数
        $postData = [
            'agetId' => $this->config->agetId,//星译机构编号
            'custId' => $this->config->custId,//星译机构商户号
            'version' => '1.0.0',//版本号
            'timeStamp' => date('YmdHis'),//版本号
        ];
        $postData = array_merge($postData, $data);
        //生成签名
        $postData['sign'] = $this->getSign($postData);

        $ch = curl_init(); //用curl发送数据给api
        $header = [
            'content-type:application/json;charset=UTF-8'
        ];
        // echo(json_encode($postData));exit;
        file_put_contents('pay_log/'.date('YmdHis',time()).'/baoweng'.date('YmdHis',time()). '.log',json_encode($postData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $this->config->APIURL . $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        $response = curl_exec($ch);
        curl_close($ch);
        $response = urldecode($response);
       
        $response = json_decode($response, true);
       
        return $response;
    }

}