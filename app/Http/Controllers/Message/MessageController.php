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
            'agent_id' => 1000009,
            'secret' => 'kEJJDuCTuSXwf6DyAXFxee1VnNFC5HfEpldCkMRqs9o',
            'token' => 'message',
            'aes_key' => 'JGDBtwV7jgujnJbbfKC1DOEExK7al8lFTM5GkUeLCsI',

        ];
        $this->weObj = Factory::work($this->config);

    }

    public function index()
    {
        $this->weObj->server->push(function ($message) {
            switch ($message['MsgType']) {
                case 'text':
                    return $this->message();
                    break;
                case 'event':
                    # 事件消息...
                    switch ($message['Event']) {
                        case 'CLICK':
                            switch ($message['EventKey']) {
                                case "1":
                                    return $this->message();
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

    public function sendmessage()
    {
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

    public function message()
    {
        $today = Carbon::now()->toDateString();
        $url = env('YDPT_URL', 'url');
        $url = $url . "CheckSectionsTurnover.aspx?startdate=" . $today . "&enddate=" . $today;
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
}
