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
use Overtrue\Http\Client;
use Overtrue\Http\Config;

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

    public function testquery()
    {
        $url = env('YDPT_URL', 'url');
        $url = $url . "SearechOrderUseDetails.aspx?password=962076";
        $data = $this->curl($url);
        if ($data['viewSpotName'] == null) {
            $str = "该识别码七天内无订单";
        } else {
            $str = "门票种类：" . $data['viewSpotName'];

            $str = $str . "\n使用情况：" . $data['isUse'];

            $count = count($data['playedViewSpot']);
            for ($x = 0; $x < $count; $x++) {
                $str = $str . "\n已检景点" . $data['playedViewSpot'][0]['playedViewSpotName'];
                $str = $str . "\n检票时间" . $data['playedViewSpot'][0]['playedTime'];
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

    public function test1()
    {
        $data = "{\"touser\":\"hd_wangke\",\"msgtype\":\"text\",\"agentid\":1000009,\"text\":{\"content\":\"msg\"},\"safe\":0}";
        echo $data;
        echo "<br/>";
        $new_data = array(
            "touer" => "hd_wangke",
            "msgtype" => "text",
            "agentid" => 1000009,
            "text" => (["content" => "msg"]),
            "safe" => 0
        );
        echo($new_data);
    }
}
