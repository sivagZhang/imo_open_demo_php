<?php
/**
 * IMO AES 加解密类
 *
 * @author Kael Li <llj1589@vip.qq.com>
 * @date 2016-5-24
 *
 */
class IMOCryptAES {

    const BLOCK_SIZE = 16;

    const CIPHER = MCRYPT_RIJNDAEL_128;
    const MODE = MCRYPT_MODE_CBC;

    private $token = '';
    private $secretKey = '';

    /**
     * 构造方法
     *
     * @param string  $token           随机字符串
     * @param string  $secretKey       ISV获取的suiteKey
     */
    public function __construct($token, $secretKey) {
        $this->token = $token;
        $this->secretKey = $secretKey;
    }

    /**
     * IMO AES加密方法
     *
     * @param  string       $data             待加密字符串
     * @param  string       $timeStamp        时间戳(用于生成签名)
     * @param  string       $nonce            随机字符串(用于生成签名)
     *
     * @return mix
     */
    public function encrypt($data, $timeStamp, $nonce) {
        $encrypt = $this->AESencrypt($data);
        if (!$encrypt) {
            return false;
        }

        $timeStamp = empty($timeStamp) ? time() : $timeStamp;
        $signature = $this->getSHA1($this->token, $timeStamp, $nonce, $encrypt);
        $encryptMsg = array(
            'msgSignature' => $signature,
            'encrypt' => $encrypt,
            'timeStamp' => $timeStamp,
            'nonce' => $nonce
        );

        return json_encode($encryptMsg);
    }

    /**
     * IMO AES解密方法
     *
     * @param  string   $signature     签名
     * @param  string   $timeStamp     时间戳(用于签名校验)
     * @param  string   $nonce         随机字符串(用于签名校验)
     * @param  string   $encrypt       待解密字符串
     *
     * @return mix
     */
    public function decrypt($signature, $timeStamp, $nonce, $encrypt) {
        $timeStamp = empty($timeStamp) ? time() : $timeStamp;

        $verifySignature= $this->getSHA1($this->token, $timeStamp, $nonce, $encrypt);
        if ($verifySignature != $signature) {
            echo 'signature invalid';
            return false;
        }

        $decryptMsg = $this->AESdecrypt($encrypt);

        return $decryptMsg;
    }

    public function AESencrypt($data) {
        if (strlen($this->secretKey) < self::BLOCK_SIZE) {
            echo 'secretKey invalid';
            return false;
        }

        $blocksize = mcrypt_get_block_size(self::CIPHER, self::MODE);
        $paddedData = self::pkcs5Pad($data, $blocksize);
        $iv = substr(md5($this->secretKey), 0, 16);
        $encrypted = mcrypt_encrypt(self::CIPHER, $this->secretKey, $paddedData, self::MODE, $iv);
        $encryptedData = base64_encode($encrypted);
        return $encryptedData;
    }

    public function AESdecrypt($encryptedData) {
        if (strlen($this->secretKey) < self::BLOCK_SIZE) {
            echo 'secretKey invalid';
            return false;
        }

        $encryptedData = base64_decode($encryptedData);
        $iv = substr(md5($this->secretKey), 0, 16);
        $decrypted =  mcrypt_decrypt(self::CIPHER, $this->secretKey, $encryptedData, self::MODE, $iv);
        $decryptedData = self::pkcs5Unpad($decrypted, "\0");
        return $decryptedData;
    }

    private static function pkcs5Pad($text, $blocksize) {
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }

    private static function pkcs5Unpad($text) {
        $pad = ord($text{strlen($text)-1});

        if ($pad > strlen($text))
            return false;

        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad)
            return false;

        return substr($text, 0, -1 * $pad);
    }

    public static function getSHA1($token, $timestamp, $nonce, $encryptMsg) {
        $array = array($encryptMsg, $token, $timestamp, $nonce);
        sort($array, SORT_STRING);
        return sha1(implode($array));
    }
}
