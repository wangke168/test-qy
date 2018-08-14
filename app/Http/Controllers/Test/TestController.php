<?php

namespace App\Http\Controllers\Test;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use EasyWeChat\Factory;
use EasyWeChat\Work;
use EasyWeChat\Kernel\Messages\Text;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;
class TestController extends Controller
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
            'token' => 'test',
            'aes_key' => 'uY5rLOibklJSaHt8suAz861k7jQdUc8a0vrv4crvNq8',

            //...
        ];
        $this->weObj=Factory::work($this->config);

    }

    public function test()
    {
        $url = "http://10.0.61.202/CheckSectionsTurnover.aspx?startdate=2018-07-01&enddate=2018-07-31";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $json = curl_exec($ch);
        $data = json_decode($json, true);
        return $data;
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
        $url = env('QY_WECHAT_JIANPIAO_URL', 'url');
        $url = $url . $tel;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $json = curl_exec($ch);
        $data = json_decode($json, true);
        $ticketcount = count($data['ticketorder']);
        $inclusivecount = count($data['inclusiveorder']);
        $hotelcount = count($data['hotelorder']);


        $i = 0;

        //    $str=$str."姓名：".$name."   电话：".$tel."\n";
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
        /*      $newsData = array(
                  "0" => array(
                      'Title' => '查询结果',
                      'Description' => $str,
                      'Url' => 'https://wechat.hdyuanmingxinyuan.com/article/detail?id=1482'
                  )
              );*/

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
        return $str;
    }
}
