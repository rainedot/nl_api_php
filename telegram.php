<?php
require_once('const.php');

class telegramBot{

    public static string $teleg_url = "https://api.telegram.org/bot" . TELEG_TOKEN . "/";

    public static array $chats = [
        ""
    ];

    private function curl($data, $url){
        $curl = curl_init();
        $headers[] = 'Content-Type: application/json';
        curl_setopt($curl, CURLOPT_POSTFIELDS,"$data" );
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl,  CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($curl);
        curl_close($curl);
        return $data;
    }

    public function test():array
    {
        $teleg_url = self::$teleg_url;
        $chats = self::$chats;
        $error = array();
        if(empty($chats)){
            $error[] = "Chats are not loaded";
            $error['status'] = 1;
        }
        if(json_decode($this->curl("" ,$teleg_url . "getMe"), true)['ok'] == 0){
            $error[] = "Wrong token";
            $error['status'] = 1;
        }
        return $error;
    }

    public function writeMessage($message): void
    {
        $teleg_url = self::$teleg_url;
        $chats = self::$chats;
        if(!$this->test()['status']) {
            for($i = 0; $i <= count($chats); $i++){
                $data = [
                    'chat_id' => $chats[$i],
                    'text' => $message,
                    'parse_mode' => 'html',
                    'no_webpage' => 'true'
                ];
                $this->curl(json_encode($data), $teleg_url . "sendMessage");
            }
        } else {
            print_r($this->test());
        }
    }
}
