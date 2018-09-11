<?php

namespace App\Http\Controllers\JianPiao;

use App\Http\Controllers\Controller;
use EasyWeChat\Factory;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;
class JianPiaoController extends Controller
{
    public $weObj;
    public $config;
    public $client;
    public function __construct()
    {
        $this->config=[
            'corp_id' => env('QY_WECHAT_APPID', 'corp_id'),
            'agent_id' => env('QY_WECHAT_JIANPIAO_AGENTID', 'agent_id'),
            'secret' => env('QY_WECHAT_JIANPIAO_APPSECRET', 'secret'),
            'token' => env('QY_WECHAT_JIANPIAO_TOEKN', 'token'),
            'aes_key' => env('QY_WECHAT_JIANPIAO_ENCODINGAESKEY', 'aes_key'),
        ];
        $this->weObj=Factory::work($this->config);
        $this->client = new \GuzzleHttp\Client();
    }

    public function index()
    {

        $this->weObj->server->push(function ($message) {

            switch ($message['MsgType']) {
                case 'text':
                    $news=$this->Check_tecket($message['Content']);
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
    private function Check_tecket($tel)
    {
        $url = env('YDPT_URL', 'url');
        $url = $url ."searchorder_json.aspx?name=Anonymous&phone=". $tel;
        $data = $this->client->request('GET', $url)->getBody();
//        $data = json_decode($json, true);
//        $data =
        $ticketcount = count($data['ticketorder']);

        $i = 0;

        if ($ticketcount <> 0) {
//            $str = "您好，该客人的预订信息如下\n注意，若是联票+梦幻谷或者三点+梦幻谷的门票仍然需要身份证检票\n";
            $str = "您好，该客人的预订信息如下";
            for ($j = 0; $j < $ticketcount; $j++) {
                $i = $i + 1;
                $str = $str . "\n订单" . $i;
                $str = $str . "\n姓名：" . $data['ticketorder'][$j]['name'];
                $str = $str . "\n订单号:" . $data['ticketorder'][$j]['sellid'];
                $str = $str . "\n预达日期:" . $data['ticketorder'][$j]['date2'];
                $str = $str . "\n预购景点:" . $data['ticketorder'][$j]['ticket'];
                $str = $str . "\n人数:" . $data['ticketorder'][$j]['numbers'];
                /* if ($data['ticketorder'][$j]['ticket'] == '三大点+梦幻谷' || $data['ticketorder'][$j]['ticket'] == '网络联票+梦幻谷') {
                     $str = $str . "\n注意：该票种需要身份证检票";
                 } else {*/
                $str = $str . "\n订单识别码:" . $data['ticketorder'][$j]['code'] ;
//                }
                $str = $str . "\n订单状态:" . $data['ticketorder'][$j]['flag'] . "\n";
            }
        } else {
            $str = "该手机号下无门票订单";
        }


        $items = [
            new NewsItem([
                'title' => '查询结果',
                'description' => $str,
                'url' => 'https://wechat.hdyuanmingxinyuan.com/article/detail?id=1482',
            ]),

        ];
        $news = new News($items);

        return $str;
    }
}
