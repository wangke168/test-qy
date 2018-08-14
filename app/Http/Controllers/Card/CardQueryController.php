<?php

namespace App\Http\Controllers\Card;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use EasyWeChat\Factory;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;

class CardQueryController extends Controller
{
    public $weObj;
    public $config;

    public function __construct()
    {
        $this->config=[
            'corp_id' => 'wwfb1970349326c73f',

            'agent_id' => 1000004,
            'secret' => 'TsbKy9F_yo_d3bXKJ0HNqgcq4FjXW3dPXmXLhyVm918',

            // server config
            'token' => 'CardQuery',
            'aes_key' => 'QUM5w3LOduQ1kXYfmEn3FbmAKyU9OJOfeEpX5A9Ylgw',

            //...
        ];
        $this->weObj=Factory::work($this->config);

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
        $url = env('QY_WECHAT_CARD_URL', 'url');
        $url = $url . $DID;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $json = curl_exec($ch);
        $data = json_decode($json, true);
        $ticketcount = count($data['ticketorder']);
        $i = 0;

        if ($ticketcount <> 0) {
            $str = "您好，该客人的年卡信息如下\n";
            $str = "姓名：".$data['ticketorder'][0]['name']."\n";
            for ($j = 0; $j < $ticketcount; $j++) {
                $i = $i + 1;
//                $str = $str . "\n种类" . $i;
//                $str = $str . "\n姓名：" . $data['ticketorder'][$j]['name'];
                $str = $str . "\n年卡类型:" . $data['ticketorder'][$j]['ticket'];
                $str = $str . "\n年卡状态:" . $data['ticketorder'][$j]['content'] . "\n";
            }
            $str = $str . "\n注意：已挂失及未发卡状态的年卡无法入园。\n\n如有疑问请致电057989600055。";
        } else {
            $str = "该身份证号下无年卡信息，如有疑问请致电057989600055。";
        }
  /*      $newsData = array(
            "0" => array(
                'Title' => '查询结果',
                'Description' => $str,
                'Url' => 'https://wechat.hdyuanmingxinyuan.com/article/detail?id=1482'
            )
        );
        return $newsData;*/

        $items = [
            new NewsItem([
                'title' => '查询结果',
                'description' => $str,
                'url' => 'https://wechat.hdyuanmingxinyuan.com/article/detail?id=1482',
//                'image'       => $image,
                // ...
            ]),

            // ...
        ];
        $news = new News($items);



//        $weObj = Factory::work($this->config());
//       $this->weObj->customer_service->message($news)->to($openId)->send();
        return $news;




    }
}
