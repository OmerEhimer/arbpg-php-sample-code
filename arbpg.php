<?php

class ArbPg{

    const ARB_HOSTED_ENDPOINT = 'https://securepayments.alrajhibank.com.sa/pg/payment/hosted.htm';
    const ARB_PAYMENT_ENDPOINT = 'https://securepayments.alrajhibank.com.sa/pg/paymentpage.htm?PaymentID=';

    const ARB_MERCHANT_HOSTED_ENDPOINT_TESTING = 'https://securepayments.alrajhibank.com.sa/pg/payment/tranportal.htm';
    const ARB_MERCHANT_HOSTED_ENDPOINT_PRODUCTION = 'https://digitalpayments.alrajhibank.com.sa/pg/payment/hosted.htm';

    const ARB_SUCCESS_STATUS = 'CAPTURED';
    const Tranportal_ID= "";
    const Tranportal_Password = "";
    const resource_key="";

public function test(){
    echo "working";
}

public function getmerchanthostedPaymentid($card_number, $expiry_month, $expiry_year, $cvv, $card_holder){
    $exp_year = "20".$expiry_year;
    $amount = 100;

    $trackId = (string)rand(1, 1000000); // TODO: Change to real value

    $data = [
        "id" => self::Tranportal_ID,
        "password" => self::Tranportal_Password,
        "expYear" => $exp_year,
        "expMonth" => $expiry_month,
        "member" => $card_holder,
        "cvv2" => $cvv,
        "cardNo" => $card_number,
        "cardType"=>"C",
        "action" => "1",
        "currencyCode" => "682",
        "errorURL" => "http://localhost/result.php",
        "responseURL" => "http://localhost/result.php",
        "trackId" => $trackId,
        "amt" => $amount,
        
    ];

    $data = json_encode($data, JSON_UNESCAPED_SLASHES);

    $wrappedData = $this->wrapData($data);

    $encData = [
        "id" => self::Tranportal_ID,
        "trandata" => $this->aesEncrypt($wrappedData),
        "errorURL" => "http://localhost:8888/result.php",
        "responseURL" => "http://localhost:8888/result.php",
    ];

    $wrappedData = $this->wrapData(json_encode($encData, JSON_UNESCAPED_SLASHES));

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => self::ARB_MERCHANT_HOSTED_ENDPOINT_PRODUCTION,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $wrappedData,

        CURLOPT_HTTPHEADER => array(
            'Accept: application/json',
            'Accept-Language: application/json',
            'Content-Type: application/json',
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);

    // parse response and get id
    $response_data = json_decode($response, true)[0];
    var_dump($response);
    // if ($response_data["status"] == "1") {
    //     $url ="https:" . explode(":", $response_data["result"])[2];
    //     return $url;
    // } else {
    //     // handle error either refresh on contact merchant
    //     return null;
    // }

}

public function getPaymentId()
{

    $plainData = $this->getRequestData();
 
    $wrappedData = $this->wrapData($plainData);



    $encData = [
        "id" => self::Tranportal_ID,
        "trandata" => $this->aesEncrypt($wrappedData),
        "errorURL" => "http://localhost/result.php",
        "responseURL" => "http://localhost/result.php",
    ];
    
    $wrappedData = $this->wrapData(json_encode($encData, JSON_UNESCAPED_SLASHES));

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => self::ARB_MERCHANT_HOSTED_ENDPOINT_PRODUCTION,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $wrappedData,

        CURLOPT_HTTPHEADER => array(
            'Accept: application/json',
            'Accept-Language: application/json',
            'Content-Type: application/json',
        ),
    ));

    $response = curl_exec($curl);
    print_r($response);
    curl_close($curl);

    // parse response and get id
    $data = json_decode($response, true)[0];
   print_r($data);
    if ($data["status"] == "1") {
        $id = explode(":", $data["result"])[0];
        return $id;
    } else {
        // handle error either refresh on contact merchant
        return -1;
    }
}


public function getResult($trandata)
    {


        $decrypted = $this->aesDecrypt($trandata);
        $raw = urldecode($decrypted);
        $dataArr = json_decode($raw, true);
        var_dump($dataArr);
      //  $sessionId = $dataArr[0]["udf2"];
        $paymentStatus = $dataArr[0]["result"];
       // $orderId = $dataArr[0]["udf1"];

       // header('Set-Cookie: ' . $this->config->get('session_name') . '=' . $sessionId . '; HttpOnly; Secure');

        if (isset($paymentStatus) && $paymentStatus === self::ARB_SUCCESS_STATUS) {
            return "success";
        }

        return "rejected";

    }

    private function getRequestData()
    {

       // $this->load->model('checkout/order');

        $amount = 100;

        $trackId = (string)rand(1, 1000000); // TODO: Change to real value

        $data = [
            "id" => self::Tranportal_ID,
            "password" => self::Tranportal_Password,
            "action" => "1",
            "currencyCode" => "682",
            "errorURL" => "http://localhost:8888/result.php",
            "responseURL" => "http://localhost:8888/result.php",
            "trackId" => $trackId,
            "amt" => $amount,
            
        ];

        $data = json_encode($data, JSON_UNESCAPED_SLASHES);
        //var_dump($data);
        return $data;
    }

    

    private function wrapData($data)
    {
        $data = <<<EOT
[$data]
EOT;
        return $data;
    }

    private function aesEncrypt($plainData)
    {
        $key = self::resource_key;
        $iv = "PGKEYENCDECIVSPC";
        $str = $this->pkcs5_pad($plainData);
        $encrypted = openssl_encrypt($str, "aes-256-cbc", $key, OPENSSL_ZERO_PADDING, $iv);
        $encrypted = base64_decode($encrypted);
        $encrypted = unpack('C*', ($encrypted));
        $encrypted = $this->byteArray2Hex($encrypted);
        $encrypted = urlencode($encrypted);
        return $encrypted;
    }

    private function pkcs5_pad($text, $blocksize = 16)
    {
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }

    private function byteArray2Hex($byteArray)
    {
        $chars = array_map("chr", $byteArray);
        $bin = join($chars);
        return bin2hex($bin);
    }

    private function aesDecrypt($code)
    {  
        $code = $this->hex2ByteArray(trim($code));
        $code = $this->byteArray2String($code);
        $iv = "PGKEYENCDECIVSPC";
        $key = self::resource_key;
        $code = base64_encode($code);
        $decrypted = openssl_decrypt($code, 'AES-256-CBC', $key, OPENSSL_ZERO_PADDING,
            $iv);
            
        return $this->pkcs5_unpad($decrypted);
    }

    private function pkcs5_unpad($text)
    {
        $pad = ord($text[strlen($text) - 1]);
        if ($pad > strlen($text)) return false;
        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad) return false;
        return substr($text, 0, -1 * $pad);
    }

    private function hex2ByteArray($hexString)
    {
        $string = hex2bin($hexString);
        return unpack('C*', $string);
    }

    private function byteArray2String($byteArray)
    {
        $chars = array_map("chr", $byteArray);
        return join($chars);
    }
}