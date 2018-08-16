<?php

namespace App\Http\Controllers\Message;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use EasyWeChat\Factory;
use EasyWeChat\Work;
use EasyWeChat\Kernel\Messages\Text;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;

class MessageController extends Controller
{
    public $weObj;
    public $config;

    public function __construct()
    {
        $this->config = [
            'corp_id' => 'wwfb1970349326c73f',

            'agent_id' => 1000010,
            'secret' => 'jBcAo4eYZr0xTEFAbEVzGw_bHS0zWDRHRUvghmFgOvE',

            // server config
            'token' => 'message',
            'aes_key' => 'JGDBtwV7jgujnJbbfKC1DOEExK7al8lFTM5GkUeLCsI',

            //...
        ];
        $this->weObj = Factory::work($this->config);

    }

    public function message()
    {
        $today = Carbon::now()->toDateString();
//        return $today;
        $url = "http://10.0.61.202/CheckSectionsTurnover.aspx?startdate=" . $today . "&enddate=" . $today;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $json = curl_exec($ch);
        $data = json_decode($json, true);


        $str = $today . "数据如下\n";
        $str = $str . $data['resultList'][0]['section'] . "\n";
        $str = $str . "营收:" . round($data['resultList'][0]['turnover'], 2) . "元\n";
        $str = $str . "人次:" . $data['resultList'][0]['personTime'] . "\n\n";
        $str = $str . $data['resultList'][1]['section'] . "\n";
        $str = $str . "营收:" . round($data['resultList'][1]['turnover'], 2) . "元\n";
        $str = $str . "人次:" . $data['resultList'][1]['personTime'] . "\n\n";
        $str = $str . $data['resultList'][2]['section'] . "\n";
        $str = $str . "营收:" . round($data['resultList'][2]['turnover'], 2) . "元\n";
        $str = $str . "人次:" . $data['resultList'][2]['personTime'] . "\n\n";
        return $str;
    }

    public function sendmessage()
    {
       /* $config = [
            'corp_id' => 'wwfb1970349326c73f',

            'agent_id' => 1000009,
            'secret' => 'kEJJDuCTuSXwf6DyAXFxee1VnNFC5HfEpldCkMRqs9o',

            // server config
            'token' => 'message',
            'aes_key' => 'JGDBtwV7jgujnJbbfKC1DOEExK7al8lFTM5GkUeLCsI',
        ];
        $weObj = Factory::work($config);*/
        $accessToken = $this->weObj->access_token;
        $token = $accessToken->getToken(); // token 数组  token['access_token'] 字符串
        $msg = $this->message();
        $url = "https://qyapi.weixin.qq.com/cgi-bin/message/send?access_token=" . $token['access_token'];
        $data = "{\"touser\":\"hd_wangke\",\"msgtype\":\"text\",\"agentid\":1000009,\"text\":{\"content\":\"$msg\"},\"safe\":0}";
        $this->curlPost($url, $data);

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

    public function index()
    {


        $this->weObj->server->push(function ($message) {

            $today = Carbon::now()->toDateString();
//        return $today;
            $url = "http://10.0.61.202/CheckSectionsTurnover.aspx?startdate=" . $today . "&enddate=" . $today;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            $json = curl_exec($ch);
            $data = json_decode($json, true);


            $str = $today . "数据如下\n";
            $str = $str . $data['resultList'][0]['section'] . "\n";
            $str = $str . "营收:" . round($data['resultList'][0]['turnover'], 2) . "元\n";
            $str = $str . "人次:" . $data['resultList'][0]['personTime'] . "\n\n";
            $str = $str . $data['resultList'][1]['section'] . "\n";
            $str = $str . "营收:" . round($data['resultList'][1]['turnover'], 2) . "元\n";
            $str = $str . "人次:" . $data['resultList'][1]['personTime'] . "\n\n";
            $str = $str . $data['resultList'][2]['section'] . "\n";
            $str = $str . "营收:" . round($data['resultList'][2]['turnover'], 2) . "元\n";
            $str = $str . "人次:" . $data['resultList'][2]['personTime'] . "\n\n";
            return $str;

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
                $str = $str . "\n订单识别码:" . $data['ticketorder'][$j]['code'];
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
