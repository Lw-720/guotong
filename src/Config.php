<?php

namespace Liwei\Guotong;

class Config
{
    public $agetId = 'FWH000029001'; // 机构号


    public $custId = '60000010166528'; // 商户号


    public $APIURL = 'https://yyfsvxm.postar.cn'; // //测试环境


    public $WxAppId = 'wxf818fa0b707b57e3'; //

    /**
     * 回调地址  已处理 
https://dc.ruifuren528.com/映射后地址为
http://192.168.130.163:8009/yyfToIsvtjyjtkj3
尾缀自行添加
     * @var string
     */
    public $AsyncNotify = 'http://192.168.130.163:8009/yyfToIsvtjyjtkj3/index.php/'; // 

    /**
     * 公钥
     * @var string
     */
    public $PublicKey = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA17JxyM4iCwWUBPCqa+xW2qrEsbO4po+ATZ821M0FZAPMycsmFMIwKEQpty9crWxFXENiib7gm5e7vBxeZcLi/8/6k/EV8ad30vQubHU4ICNaukZNoxJWEHyBCmlC16R55Vc8UyRU6oNiz+FXbd+Ix1XI/tiCGRb+JdrwvorV3buQOsReu06+7ZQWMdZZPwO7AhB9xRwUcOunHKLrF0nmOE1kW12WCD3gMiO9l6fV7Nv/AXURCHmQ+LCu/PficyfDxXj53mHlAf/HbQRQIbDA5ds4G4VCCEwEoJD3+Oqa2pQI+8j0ugc26EZaW4B8CBTo7DsvbTiwAgl7R6mTKRFyhwIDAQAB';




}