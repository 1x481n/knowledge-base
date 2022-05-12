<?php
/**
 * Created by IntelliJ IDEA.
 * User: 1x481n
 * Date: 2022/4/30
 * Time: 4:57 PM
 */


class Base64
{
    const BASE64_CHARS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';

    const ALLOWED_ENCODING = ['UTF-8', 'ASCII', 'GB2312', 'GBK'];

    const ENCODING_MAP = [
        'EUC-CN' => 'GB2312',
        'CP936' => 'GBK',
    ];


    public static function encode($string, $encoding = 'UTF-8', $debug = false)
    {
        $output = '';
        $stringEncoding = mb_detect_encoding($string, self::ALLOWED_ENCODING);
        $stringEncodingAlias = self::ENCODING_MAP[$stringEncoding] ?? '';
        $encodingString = mb_convert_encoding($string, $encoding, $stringEncoding);
        $encodingStringLength = strlen($encodingString);

        $encodingBytes = [];
        $encodingAsciiInteger = [];
        $chrBin = [];
        $chrBin8bit = [];
        for ($i = 0; $i < $encodingStringLength; $i++) {
            $encodingBytes[] = $encodingString[$i];
            $encodingAsciiInteger[] = ord($encodingString[$i]);
            $chrBin[] = decbin(ord($encodingString[$i]));
            $chrBin8bit[] = str_pad(decbin(ord($encodingString[$i])), 8, 0, STR_PAD_LEFT);
        }
        $chr_bytes_num = count($chrBin8bit);
        $mod = $chr_bytes_num % 3;
        $missing_bytes = $mod ? 3 - $mod : 0;

        $missing_bytes && $zero_padding = implode(' ', array_fill(0, $missing_bytes, str_repeat('0', 8)));
        $zero_padding = !empty($zero_padding) ? $zero_padding : '';
        $zero_padding_show = $zero_padding ? " [$zero_padding](不足3字节，零填充部分)" : '';

        $chr_full_bin = implode('', $chrBin8bit);


        $chr_6bit = [];
        $times = ceil(strlen($chr_full_bin) / 6);
        for ($i = 0; $i < $times; $i++) {
            $chr_6bit['original'][] = substr($chr_full_bin, $i * 6, 6);
            $chr_6bit['zero_padding'][] = str_pad(substr($chr_full_bin, $i * 6, 6), 6, 0);
            $chr_6bit['decimal'][] = bindec(str_pad(substr($chr_full_bin, $i * 6, 6), 6, 0));
            $chr_6bit['base64_code'][] = substr(self::BASE64_CHARS, bindec(str_pad(substr($chr_full_bin, $i * 6, 6), 6, 0)), 1);
        }

        $mod_6bit = count($chr_6bit['zero_padding']) % 4;
        $equal_nums = $mod_6bit ? 4 - $mod_6bit : 0;

        $base64Encoding = implode('', $chr_6bit['base64_code']) . ($equal_nums ? str_repeat('=', $equal_nums) : '');

        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        $output .= '=== 默认可能编码:' . $stringEncoding . $stringEncodingAlias . ' ===' . PHP_EOL;
        $output .= sprintf('字符：%s | 字符长度：%s | 字节长度：%s', $string, mb_strlen($string, $stringEncoding), strlen($string));
        $output .= PHP_EOL . PHP_EOL;
        ////////////////////////////////////////////////////////////
        $output .= '=== 转换为' . $encoding . '编码 ===' . PHP_EOL;
        $output .= '字符：' . $encodingString;
        $output .= PHP_EOL;
        $output .= '字节长度：' . $encodingStringLength;
        $output .= PHP_EOL;


        $output .= '每个字节的字符：' . implode(' ', $encodingBytes) . PHP_EOL;
        $output .= '每个字节的ascii码整数：' . implode(' ', $encodingAsciiInteger) . PHP_EOL;
        $output .= '字符每个字节的二进制：' . implode(' ', $chrBin) . PHP_EOL;
        $output .= '字符每个字节的8位二进制： ' . implode(' ', $chrBin8bit) . $zero_padding_show . PHP_EOL;
        $output .= '字符完整的8位二进制：' . $chr_full_bin . PHP_EOL;
        $output .= PHP_EOL;
        $output .= '=== 转化为4个6位的字节 === ' . PHP_EOL;
        $output .= '原始数据：' . implode(' ', $chr_6bit['original']) . PHP_EOL;
        $output .= '不足6位字节补零后：' . implode(' ', $chr_6bit['zero_padding']) . PHP_EOL;
        $output .= '不足6位字节补零后对应十进制数：' . implode(' ', $chr_6bit['decimal']) . PHP_EOL;
        $output .= '每个字节分别对应的base64编码：' . implode(' ', $chr_6bit['base64_code']) . PHP_EOL;
        $output .= '不足4字节部分，需要用' . $equal_nums . '个"="补上' . PHP_EOL;
        $output .= $encoding . '编码字符的最终base64编码为：' . $base64Encoding . PHP_EOL;

        $output = mb_convert_encoding($output, 'UTF-8', $stringEncoding);

        if ($debug) echo $output;

        return $base64Encoding;
    }

}


//echo Base64::encode('啊', 'UTF-8');
//echo PHP_EOL;
echo Base64::encode('啊', 'GB2312', true);
//echo PHP_EOL;

//echo Base64::encode('好', 'UTF-8');
//echo PHP_EOL;
//echo Base64::encode('好', 'GB2312');
//echo PHP_EOL;

//echo Base64::encode('￥￥￥￥￥￥', 'UTF-8');
//echo PHP_EOL;
//echo Base64::encode('¥¥¥¥¥¥', 'UTF-8');
//echo PHP_EOL;


//echo Base64::encode('好sdfsd!!79234##sdfdsf水电费水电费0374', 'UTF-8');
//echo PHP_EOL;
//echo Base64::encode('好sdfsd!!79234##sdfdsf水电费水电费0374', 'GB2312');
//echo PHP_EOL;


//echo Base64::encode('0123456789`?~～!！@@##$￥%%……^&&**（(）)??_=++[]{}【】「」\\|、｜；：；：\'"\'"，《,<。》.>/？/?qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM', 'UTF-8');
//echo PHP_EOL;
//echo Base64::encode('0123456789`?~～!！@@##$￥%%……^&&**（(）)??_=++[]{}【】「」\\|、｜；：；：\'"\'"，《,<。》.>/？/?qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM', 'GB2312');
//echo PHP_EOL;

