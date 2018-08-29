<?php

namespace App\Http\Controllers\Test;

use Carbon\Carbon;
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
        $this->config = [


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

    public function temp()
    {
        echo date('Y-m-d', strtotime('-1 month', strtotime(date('Y-m', time()) . '-01 00:00:00')));

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

                $str = $str . "\n订单识别码:" . $data['ticketorder'][$j]['code'];

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

    public function testquery()
    {
        $url = env('YDPT_URL', 'url');
        $url = $url . "SearechOrderUseDetails.aspx?password=962076";
        $data = $this->curl($url);

        echo $data['viewSpotName'].'<br>';
        echo $data['isUse'].'<br>';
        echo $data['playedViewSpot'][0]['playedViewSpotName'].'<br>';
        echo $data['playedViewSpot'][0]['playedTime'].'<br>';
        echo $data['unPlayedViewSpot'][0]['unPlayedViewSpotName'].'<br>';
//        return $data;
/*
        if ($data <> 0) {

            $str = "您好，该客人的预订信息如下";
            for ($j = 0; $j < $ticketcount; $j++) {
                $i = $i + 1;
                $str = $str . "\n订单" . $i;
                $str = $str . "\n姓名：" . $data['ticketorder'][$j]['name'];
                $str = $str . "\n订单号:" . $data['ticketorder'][$j]['sellid'];
                $str = $str . "\n预达日期:" . $data['ticketorder'][$j]['date2'];
                $str = $str . "\n预购景点:" . $data['ticketorder'][$j]['ticket'];
                $str = $str . "\n人数:" . $data['ticketorder'][$j]['numbers'];

                $str = $str . "\n订单识别码:" . $data['ticketorder'][$j]['code'];

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

        return $str;*/
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
