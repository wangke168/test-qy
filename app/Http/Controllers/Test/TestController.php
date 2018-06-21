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
            'token' => 'jianpiao',
            'aes_key' => 'X5HFXA537wZkVwUicueeuPlsGgvgftDPdyv9pnNMaMp',

            //...
        ];
        $this->weObj=Factory::work($this->config);

    }

    /*    public function config()
        {
            $config = [
                'corp_id' => 'wwfb1970349326c73f',

                'agent_id' => 1000004,
                'secret' => 'TsbKy9F_yo_d3bXKJ0HNqgcq4FjXW3dPXmXLhyVm918',

                // server config
                'token' => 'jianpiao',
                'aes_key' => 'X5HFXA537wZkVwUicueeuPlsGgvgftDPdyv9pnNMaMp',

                //...
            ];
            return $config;
        }*/
    public function index()
    {

        $this->weObj->server->push(function ($message) {
            $openid=$message['FromUserName'];
            switch ($message['MsgType']) {
                case 'text':

                    /*     $text = new Text($message['FromUserName']);
                         return $text;*/
                    /*    $items = [
                            new NewsItem([
                                'title' => '查询结果',
                                'description' => 'asdas',
                                'url' => 'https://wechat.hdyuanmingxinyuan.com/article/detail?id=1482',
    //                'image'       => $image,
                                // ...
                            ]),

                            // ...
                        ];
                        $news = new News($items);*/
                    $config = [
                        'corp_id' => 'wwfb1970349326c73f',

                        'agent_id' => 1000004,
                        'secret' => 'TsbKy9F_yo_d3bXKJ0HNqgcq4FjXW3dPXmXLhyVm918',

                        // server config
                        'token' => 'jianpiao',
                        'aes_key' => 'X5HFXA537wZkVwUicueeuPlsGgvgftDPdyv9pnNMaMp',

                        //...
                    ];
                    /*  $app = Factory::work($config);
                          $text = new Text('Hello world!');
                $result = $app->customer_service->message($text)->to('hd_wangke')->send();*/


                    $news=$this->Check_tecket($message['Content'],$openid);



                    return $news;
                    break;
                default:
                    return '收到其它消息';
                    break;
            }



            //            return $this->Check_tecket($message['Content']);
//            $weObj->news($this->Check_tecket($c))->reply()
        });

        $response = $this->weObj->server->serve();

        return $response;

//        $weObj->news($this->Check_tecket($c))->reply()

        /*   if (!$ret) {
               \Log::info($ret);
           }*/
        /*
        $f = $weObj->getRev()->getRevFrom();	//获取发送者微信号
        $t = $weObj->getRevType();				//获取发送的类型
        $d = $weObj->getRevData();				//获取发送的data
        if ($t=="text")
        {
            $c = $weObj->getRevContent();			//获取发送的内容
            $weObj->news(Check_tecket($c))->reply();
        }
        */

        /*     $f = $weObj->getRev()->getRevFrom();	//获取发送者微信号
             $t = $weObj->getRevType();				//获取发送的类型
             $d = $weObj->getRevData();				//获取发送的data
             $c = $weObj->getRevContent();			//获取发送的内容
             if ($t=="text")
             {
     //            $c = $weObj->getRevContent();			//获取发送的内容
     //           $weObj->text("你好！来自星星的：")->reply();
     //            $c = $weObj->getRevContent();			//获取发送的内容
                 $weObj->news($this->Check_tecket($c))->reply();

             }*/
//logg("-----------------------------------------");
//        $weObj->valid();


    }

//检票口
    private function Check_tecket($tel,$openId)
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
                $str = $str . "\n订单识别码:" . $data['ticketorder'][$j]['code'] . "（在检票口出示此识别码可直接进入景区。）";
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
        return $news;
    }
}
