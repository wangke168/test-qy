<?php

namespace App\Http\Controllers\Message;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use EasyWeChat\Factory;

class MessageController extends Controller
{
    public $weObj;
    public $config;
    public $token;

    public function __construct()
    {
        $this->config = [
            'corp_id' => env('QY_WECHAT_APPID', 'corp_id'),
            'agent_id' => env('QY_WECHAT_MESSAGE_AGENTID', 'agent_id'),
            'secret' => env('QY_WECHAT_MESSAGE_APPSECRET', 'secret'),
            'token' => env('QY_WECHAT_MESSAGE_TOEKN', 'token'),
            'aes_key' => env('QY_WECHAT_MESSAGE_ENCODINGAESKEY', 'aes_key'),
        ];
        $this->weObj = Factory::work($this->config);
        $this->token = $this->weObj->access_token->getToken();

    }

    public function index()
    {
        $this->weObj->server->push(function ($message) {
            switch ($message['MsgType']) {
                case 'text':
                    $str = explode(" ", $message['Content']);
                    $StartDate = $str[0];
                    $EndDate = $str[1];
                    return $this->message($StartDate, $EndDate);
                    break;
                case 'event':
                    # 事件消息...
                    switch ($message['Event']) {
                        case 'click':
                            switch ($message['EventKey']) {
                                case "1":
                                    $today = Carbon::now()->toDateString();
                                    return $this->message($today, $today);
                                    break;
                                case "2":
                                    $StartDate = date('Y-m-d', strtotime('-1 monday', time()));
                                    $EndDate = Carbon::now()->toDateString();
                                    return $this->message($StartDate, $EndDate);
                                    break;
                                case "3":
                                    $StartDate = date('Y-m-d', strtotime('-2 monday', time()));
                                    $EndDate = date('Y-m-d', strtotime('-1 sunday', time()));
                                    return $this->message($StartDate, $EndDate);
                                    break;
                                case "4":
                                    $StartDate = date('Y-m-d', strtotime(date('Y-m', time()) . '-01 00:00:00'));
                                    $EndDate = Carbon::now()->toDateString();
                                    return $this->message($StartDate, $EndDate);
                                    break;
                                case "5":
                                    $StartDate = date('Y-m-d', strtotime('-1 month', strtotime(date('Y-m', time()) . '-01 00:00:00')));
                                    $EndDate = date('Y-m-d', strtotime(date('Y-m', time()) . '-01 00:00:00') - 86400);
                                    return $this->message($StartDate, $EndDate);
                                    break;
                                case "6":
                                    $StartDate = "2018-01-01";
                                    $EndDate = Carbon::now()->toDateString();
                                    return $this->message($StartDate, $EndDate);
                                    break;
                                default:
                                    break;
                            }
                            break;
                    }
                    break;
                default:
                    return '收到其它消息';
                    break;
            }
        });
        $response = $this->weObj->server->serve();
        return $response;
    }


    public function SendMessage()
    {
        $today = Carbon::now()->toDateString();
        $msg = $this->Message($today, $today);
        $url = "https://qyapi.weixin.qq.com/cgi-bin/message/send?access_token=" . $this->token['access_token'];
        $data = "{\"touser\":\"hd_wangke\",\"msgtype\":\"text\",\"agentid\":1000009,\"text\":{\"content\":\"$msg\"},\"safe\":0}";
        $this->curlPost($url, $data);
    }

    /**
     *发送游览车未用数据
     */
    public function SendCarMessage()
    {
        $msg = $this->CarMessage();
        $url = "https://qyapi.weixin.qq.com/cgi-bin/message/send?access_token=" . $this->token['access_token'];
        $data = "{\"touser\":\"hd_wangke\",\"msgtype\":\"text\",\"agentid\":1000009,\"text\":{\"content\":\"$msg\"},\"safe\":0}";
        $this->curlPost($url, $data);
    }


    /**
     * 报表数据
     * @param $StartDate
     * @param $EndDate
     * @return string
     */
    public function Message($StartDate, $EndDate)
    {
        $url = env('YDPT_URL', 'url');
        $url = $url . "CheckSectionsTurnover.aspx?startdate=" . $StartDate . "&enddate=" . $EndDate;
        $data = $this->curl($url);
        if ($StartDate == $EndDate) {
            $str = $StartDate . "数据如下\n";
        } else {
            $str = $StartDate . "---" . $EndDate . "数据如下\n";
        }
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
        $number=0;
        for ($x = 0; $x < $count; $x++) {
            $str = $str . '识别码' . $data[$x]['password'] . "  人数 " . $data[$x]['number'] . "\n";
            $number=$number+$data[$x]['number'];
        }
        $str=$str."\n总共".$count."笔订单，".$number.'人。';
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

    public function Temp()
    {
        var_dump($this->weObj->menu->get());
    }
}
