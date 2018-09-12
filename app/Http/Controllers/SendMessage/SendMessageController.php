<?php

namespace App\Http\Controllers\SendMessage;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use EasyWeChat\Factory;

class SendMessageController extends Controller
{
    public $weObj;
    public $config;
    public $token;
    public $getCarMessage;

    public function __construct()
    {
        $this->config = [
            'corp_id' => env('QY_WECHAT_APPID', 'corp_id'),
            'agent_id' => env('QY_WECHAT_SENDMESSAGE_AGENTID', 'agent_id'),
            'secret' => env('QY_WECHAT_SENDMESSAGE_APPSECRET', 'secret'),
            'token' => env('QY_WECHAT_SENDMESSAGE_TOEKN', 'token'),
            'aes_key' => env('QY_WECHAT_SENDMESSAGE_ENCODINGAESKEY', 'aes_key'),
        ];
        $this->weObj = Factory::work($this->config);
        $this->token = $this->weObj->access_token->getToken();
        $this->getCarMessage = env('Get_CarMessage', 'hd_wangke');;

    }

    public function index()
    {
        $this->weObj->server->push(function ($message) {
            switch ($message['MsgType']) {
                case 'text':
                    return "其他功能陆续完善中";
                    break;
            }

        });
        $response = $this->weObj->server->serve();

        $response->send();
    }

    /**
     *发送游览车未用数据
     */

    public function SendCarMessage()
    {
        $msg = $this->CarMessage();
        $url = "https://qyapi.weixin.qq.com/cgi-bin/message/send?access_token=" . $this->token['access_token'];
        $data = "{\"touser\":\"$this->getCarMessage\",\"msgtype\":\"text\",\"agentid\":1000011,\"text\":{\"content\":\"$msg\"},\"safe\":0}";
        $this->curlPost($url, $data);
    }


    /**
     * 每日游览车数据
     * @return string
     */
    public function CarMessage()
    {
        $today = Carbon::now()->toDateString();
        $url = env('YDPT_URL', 'url');
        $url = $url . "SearchNotCheckedTouristcarTiceket.aspx";
        $data = $this->curl($url);
        $count = count($data);
        $str = $today . "游览车未检票数据\n\n";
        $number = 0;
        for ($x = 0; $x < $count; $x++) {
            $str = $str . '识别码' . $data[$x]['password'] . "  人数 " . $data[$x]['number'] . "\n";
            $number = $number + $data[$x]['number'];
        }
        $str = $str . "\n总共" . $count . "笔订单，" . $number . '人。';
        return $str;
    }

    private function curl($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $json = curl_exec($ch);
        $data = json_decode($json, true);
        return $data;
    }

    private function curlPost($url, $data = "")
    {
        $ch = curl_init();
        $opt = array(
            CURLOPT_URL => $url,
            CURLOPT_HEADER => 0,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_TIMEOUT => 20
        );
        $ssl = substr($url, 0, 8) == "https://" ? TRUE : FALSE;
        if ($ssl) {
            $opt[CURLOPT_SSL_VERIFYHOST] = 2;
            $opt[CURLOPT_SSL_VERIFYPEER] = FALSE;
        }
        curl_setopt_array($ch, $opt);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
}
