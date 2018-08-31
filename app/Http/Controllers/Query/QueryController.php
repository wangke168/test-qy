<?php

namespace App\Http\Controllers\Query;

use App\Http\Controllers\Controller;
use EasyWeChat\Factory;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;

class QueryController extends Controller
{
    public $weObj;
    public $config;

    public function __construct()
    {
        $this->config = [
            'corp_id' => env('QY_WECHAT_APPID', 'corp_id'),
            'agent_id' => env('QY_WECHAT_QUERY_AGENTID', 'agent_id'),
            'secret' => env('QY_WECHAT_QUERY_APPSECRET', 'secret'),
            'token' => env('QY_WECHAT_QUERY_TOEKN', 'token'),
            'aes_key' => env('QY_WECHAT_QUERY_ENCODINGAESKEY', 'aes_key'),
        ];
        $this->weObj = Factory::work($this->config);
    }

    public function index()
    {

        $this->weObj->server->push(function ($message) {

            switch ($message['MsgType']) {
                case 'text':
                    $news = $this->Check_tecket($message['Content']);
                    return $news;
                    break;
                default:
                    return '收到其它消息';
                    break;
            }
        });
        $response = $this->weObj->server->serve();
        return $response;
    }

//检票口
    private function Check_tecket($password)
    {

        $url = env('YDPT_URL', 'url');
        $url = $url . "SearechOrderUseDetails.aspx?password=" . $password;
        $data = $this->curl($url);

        if ($data['viewSpotName'] == null) {
            $str = "该识别码七天内无订单";
        } else {
            $str = "门票种类：" . $data['viewSpotName'];

            $str = $str . "\n\n使用情况：" . $data['orderStatus'];

            $count = count($data['playedViewSpot']);
            for ($x = 0; $x < $count; $x++) {
                $str = $str . "\n\n已检景点:" . $data['playedViewSpot'][$x]['playedViewSpotName'];
                $str = $str . "\n\n检票时间:" . $data['playedViewSpot'][$x]['playedTime'];
            }
//            echo $data['unPlayedViewSpot'][0]['unPlayedViewSpotName'].'<br>';
        }
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
}
