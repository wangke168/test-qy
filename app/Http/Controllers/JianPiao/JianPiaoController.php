<?php

namespace App\Http\Controllers\JianPiao;

use App\Http\Controllers\Controller;
use EasyWeChat\Factory;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;
use Illuminate\Http\Request;

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
                    $news=$this->Check_Ticket($message['Content'],"1");
                    return $news;
                    break;
                default:
                    return '收到其它消息';
                    break;
            }
        });
        $response = $this->weObj->server->serve();
        return $response;

//        echo($this->Check_ticket("15074704357"));
    }


    /**
     * 检票查询
     * @param $tel
     * @param $type 1、公众号直接推送；2、详情页
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function Check_Ticket($tel,$type)
    {
        switch ($type){
            case "1":
                if ($this->Check_Tiket_Old($tel,$type))
                {
                    $str= $this->Check_Tiket_Old($tel,$type);
                }
                elseif($this->Check_Ticket_New($tel,$type)){
                    $str= $this->Check_Ticket_New($tel,$type);
                }
                else{
                    $str = "该手机号下无门票订单,若需进一步确认信息，请联系客服。";
                }
            case "2":
                if ($this->Check_Tiket_Old($tel,$type))
                {
                    $str= $this->Check_Tiket_Old($tel,$type);
                }
                elseif($this->Check_Ticket_New($tel,$type)){
                    $str= $this->Check_Ticket_New($tel,$type);
                }
        }
    /*    if ($this->Check_Tiket_Old($tel))
        {
            $str= $this->Check_Tiket_Old($tel);
        }
        elseif($this->Check_Ticket_New($tel)){
            $str= $this->Check_Ticket_New($tel);
        }
        else{
            $str = "该手机号下无门票订单,若需进一步确认信息，请联系客服。";
        }*/
        return $str;
    }

    /**
     * 新系统订单查询
     * @param $tel
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function Check_Ticket_New($tel,$type)
    {
        $url = env('CHECK_ORDER_URL_NEW', 'url');
        $url = $url ."WXOrderQuery?phone=".$tel."&name=Anonymous&tdsourcetag=s_pctim_aiomsg";//        $url="http://192.168.100.206:8089/Order/api/Order/WXOrderQuery?phone=".$tel."&name=Anonymous&tdsourcetag=s_pctim_aiomsg";
        $json = $this->client->request('GET', $url)->getBody();
        $data = json_decode($json, true);
        $ticketcount = count($data['ticketorder']);
        $i = 0;//        return $data['ticketorder'][0]['name'];
        if ($ticketcount <> 0) {
            $str = "您好，该客人的预订信息如下\n注意，若是联票或活动门票仍然需要身份证检票\n";
            for ($j = 0; $j < $ticketcount; $j++) {
                $i = $i + 1;
                $str = $str . "\n订单" . $i;
                $str = $str . "\n姓名：" . $data['ticketorder'][$j]['name'];
                $str = $str . "\n订单号:" . $data['ticketorder'][$j]['sellid'];
                $str = $str . "\n预达日期:" . $data['ticketorder'][$j]['date2'];
                $str = $str . "\n预购景点:" . $data['ticketorder'][$j]['ticket'];
                $str = $str . "\n人数:" . $data['ticketorder'][$j]['numbers'];
                if ($data['ticketorder'][$j]['flag']=='全部退单')
                {
                    $str = $str . "\n订单状态:已取消\n";
                }
                else {
                    $str = $str . "\n订单状态:" . $data['ticketorder'][$j]['flag'];
                    $str = $str . "\n订单识别码:" . $data['ticketorder'][$j]['code'] . "\n";
                }
            }
        } else {
            $str = null;
        }
        $str_detail =str_replace("\n","<br>",$str);
        $items = [

            new NewsItem([
                'title' => '查询结果',
                'description' => $str,
                'url' => 'https://weix.hengdiaworld.com/jianpiao/detail?str='.$str_detail,
            ]),

        ];
        $news = new News($items);

        return $str;
    }


    /**
     * 老系统订单查询
     * @param $tel
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function Check_Tiket_Old($tel,$type)
    {
        $url = env('CHECK_ORDER_URL_OLD', 'url');
        $url = $url ."searchorder_json.aspx?name=Anonymous&phone=". $tel;
        $json = $this->client->request('GET', $url)->getBody();
        $data = json_decode($json, true);
//        $data =
        $ticketcount = count($data['ticketorder']);

        $i = 0;

        if ($ticketcount <> 0) {
            $str = "您好，该客人的预订信息如下\n注意，若是联票或活动门票仍然需要身份证检票\n";
            for ($j = 0; $j < $ticketcount; $j++) {
                $i = $i + 1;
                $str = $str . "\n订单" . $i;
                $str = $str . "\n姓名：" . $data['ticketorder'][$j]['name'];
                $str = $str . "\n订单号:" . $data['ticketorder'][$j]['sellid'];
                $str = $str . "\n预达日期:" . $data['ticketorder'][$j]['date2'];
                $str = $str . "\n预购景点:" . $data['ticketorder'][$j]['ticket'];
                $str = $str . "\n人数:" . $data['ticketorder'][$j]['numbers'];
                $str = $str . "\n订单识别码:" . $data['ticketorder'][$j]['code'] ;
                $str = $str . "\n订单状态:" . $data['ticketorder'][$j]['flag'] . "\n";
            }

        } else {
            return null;
        }
        /*-------------------输出给html页面开始-----------------------*/
        if ($type==2){
            $str_detail = str_replace("\n", "<br>", $str);
            return $str_detail;
        }
        else{
            $items = [
                new NewsItem([
                    'title' => '查询结果',
                    'description' => $str,
                    'url' => 'https://weix.hengdiaworld.com/jianpiao/detail?tel=' . $tel,
                ]),

            ];
            $news = new News($items);
            return $news;
        }
        /*-------------------输出给html页面结束-----------------------*/
/*        if ($str) {
            if ($type==2){
                $str_detail = str_replace("\n", "<br>", $str);
                return $str_detail;
            }
            else{
                $items = [
                    new NewsItem([
                        'title' => '查询结果',
                        'description' => $str,
                        'url' => 'https://weix.hengdiaworld.com/jianpiao/detail?tel=' . $tel,
                    ]),

                ];
                $news = new News($items);
                return $news;
            }
        }*/

    }

    public function detail(Request $request)
    {
        $tel=$request->input("tel");

        echo($this->Check_Ticket($tel,"2"));
    }
}
