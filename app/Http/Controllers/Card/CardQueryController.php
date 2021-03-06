<?php

namespace App\Http\Controllers\Card;

use App\Http\Controllers\Controller;
use EasyWeChat\Factory;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;

class CardQueryController extends Controller
{
    public $weObj;
    public $config;
    public $client;
    public function __construct()
    {
        $this->config=[
            'corp_id' => env('QY_WECHAT_APPID', 'corp_id'),
            'agent_id' => env('QY_WECHAT_CARD_AGENTID', 'agent_id'),
            'secret' => env('QY_WECHAT_CARD_APPSECRET', 'secret'),
            'token' => env('QY_WECHAT_CARD_TOEKN', 'token'),
            'aes_key' => env('QY_WECHAT_CARD_ENCODINGAESKEY', 'aes_key'),
        ];
        $this->weObj=Factory::work($this->config);
        $this->client = new \GuzzleHttp\Client();
    }

    public function index()
    {
        $this->weObj->server->push(function ($message) {

            switch ($message['MsgType']) {
                case 'text':
                    $news=$this->CheckTicket($message['Content']);
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

    private function CheckTicket($DID)
    {
        $url = env('YDPT_URL', 'url');
        $url = $url ."searchorder_json.aspx?id=". $DID;
        $json = $this->client->request('GET', $url)->getBody();
        $data = json_decode($json, true);

        $ticketcount = count($data['ticketorder']);
        $i = 0;

        if ($ticketcount <> 0) {
            $str = "您好，该客人的年卡信息如下\n";
            $str = $str."姓名：".$data['ticketorder'][0]['name']."\n";
            for ($j = 0; $j < $ticketcount; $j++) {
                $i = $i + 1;

                $str = $str . "\n年卡类型:" . $data['ticketorder'][$j]['ticket'];
                $str = $str . "\n年卡状态:" . $data['ticketorder'][$j]['content'] . "\n";
            }
            $str = $str . "\n注意：已挂失及未发卡状态的年卡无法入园。\n\n如有疑问请致电057989600055。";
        } else {
            $str = "该身份证号下无年卡信息，如有疑问请致电057989600055。";
        }

        $items = [
            new NewsItem([
                'title' => '查询结果',
                'description' => $str,
                'url' => 'https://wechat.hdyuanmingxinyuan.com/article/detail?id=1482',
//                'image'       => $image,

            ]),


        ];
        $news = new News($items);

        return $news;




    }
}
