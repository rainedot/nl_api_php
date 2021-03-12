<?php
require_once ('const.php');
require_once ('telegram.php');


class validators{

    private function arrToStr(array $data): string
    {
        $string = "";
        for($i = 0; $i < count($data, true); $i++){
            if($i == 0) {
                $current = current($data);
                $key = key($data);
                if(is_float($current)){
                    $current = number_format($current, 1);
                }
                $string = $string . $key . $current;
            }
            $current = next($data);
            $key = key($data);
            if(is_float($current)){
                $current = number_format($current, 1);
            }
            $string = $string. $key . $current;
        }
        return $string;
    }

    function genSig(array $data, string $secret): string
    {
        ksort($data);
        $string = $this->arrToStr($data) . $secret;
        return hash('sha256', $string);
    }

    function validateSig($data, $secret): bool
    {
        $nl_sig = $data['signature'];
        unset($data['signature']);
        $our_sig = $this->genSig($data, $secret);
        return $nl_sig == $our_sig;
    }

}

class request{

    private function curl_request($data, $url){
        $curl = curl_init();
        $headers[] = 'Content-Type: application/json';
        curl_setopt($curl, CURLOPT_POSTFIELDS,"$data" );
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $data = curl_exec($curl);
        curl_close($curl);
        return $data;
    }

    function transfer_money(int $amount, string $username, int $user_id, int $id){
        $validator = new validators();

        $data = array (
            'amount' => $amount,        // amount of money
            'username' => $username,    // to whom sending
            'user_id' => $user_id,      // your uid
            'id' => $id                 // unique request id
        );
        $signature = $validator->genSig($data, TOKEN);
        $data['signature'] = $signature;
        $data = json_encode($data);
        return $this->curl_request($data, "https://neverlose.cc/api/market/transfer-money");

    }
    function give_for_free(string $username, int $user_id, int $uniqueid, string $code){
        $validator = new validators();

        $data = array (
            'username' => $username,        // to whom give for free
            'user_id' => $user_id,          // your user ID
            'id' => $uniqueid,              // unique request id
            'code' => $code                 // market item code /item?id=*****
        );
        $signature = $validator->genSig($data, TOKEN);
        $data['signature'] = $signature;
        $data = json_encode($data);
        return $this->curl_request($data, "https://neverlose.cc/api/market/give-for-free");

    }
}

class callback{

    function isJson(string $string): bool
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    function balance_transfer(string $data): void
    {
        $validators = new validators();
        //$tg = new telegramBot();
        $data = json_decode($data,true);
        if($validators->validateSig($data, TOKEN)) {
            /* Anything you want
            *  For example $tg->writeMessage($data['username'] . " sent you " . $data['amount'] . " euro with transaction number: " . $data["unique_id"]);
            */
        } else {
            echo "wrong sig";
        }
    }

    function item_purchase(string $data):void
    {
        $validators = new validators();
        //$tg = new telegramBot();
        $data = json_decode($data,true);

        if($validators->validateSig($data, TOKEN)) {
            /* Anything you want
            * For example $tg->writeMessage($data['username'] . " bought your market item for " . $data['amount'] . " euro. Item ID: <code>" . $data['item_id'] . "</code>. Transaction number: " . $data["unique_id"]);
            */
        } else {
            echo "wrong sig";
        }
    }

}

