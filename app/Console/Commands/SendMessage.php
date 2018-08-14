<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use EasyWeChat\Factory;
use EasyWeChat\Kernel\Messages\Text;
class SendMessage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SendMessage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'SendMessage';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {


     /*   $config=[
            'corp_id' => 'wwfb1970349326c73f',
            'agent_id' => 1000004,
            'secret' => 'TsbKy9F_yo_d3bXKJ0HNqgcq4FjXW3dPXmXLhyVm918',
            'token' => 'test',
            'aes_key' => 'uY5rLOibklJSaHt8suAz861k7jQdUc8a0vrv4crvNq8',
        ];
        $weObj=Factory::work($config);

        $weObj->server->push(function(){
        $today=Carbon::now()->toDateString();
        $url = "http://10.0.61.202/CheckSectionsTurnover.aspx?startdate=".$today."&enddate=".$today;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $json = curl_exec($ch);
        $data = json_decode($json, true);


        $str=$today."数据如下\n";
        $str=$str.$data['resultList'][0]['section']."\n";
        $str=$str."营收:".round($data['resultList'][0]['turnover'],2)."元\n";
        $str=$str."人次:".$data['resultList'][0]['personTime']."\n\n";
        $str=$str.$data['resultList'][1]['section']."\n";
        $str=$str."营收:".round($data['resultList'][1]['turnover'],2)."元\n";
        $str=$str."人次:".$data['resultList'][1]['personTime']."\n\n";
        $str=$str.$data['resultList'][2]['section']."\n";
        $str=$str."营收:".round($data['resultList'][2]['turnover'],2)."元\n";
        $str=$str."人次:".$data['resultList'][2]['personTime']."\n\n";
        return $str;

    });*/
        $options = array(
            'token' => "test",    //填写应用接口的Token
            'encodingaeskey' => "uY5rLOibklJSaHt8suAz861k7jQdUc8a0vrv4crvNq8",//填写加密用的EncodingAESKey
            'appid' => "wwfb1970349326c73f",   //填写高级调用功能的appid
            'appsecret' => "TsbKy9F_yo_d3bXKJ0HNqgcq4FjXW3dPXmXLhyVm918",
            'debug' => env('QY_WECHAT_DEBUG', 'debug'),
            'logcallback' => env('QY_WECHAT_LOGCALLBACK', 'logcallback'),
            'agentid' => "1000004", //应用的id
        );
        $weObj = new \Wechat($options);
        $date = array(
            'touser' => 'hd_wangke',
            "toparty" => "",
            "totag" => "",
            'agentid' => '1000004',    //应用id
            'msgtype' => 'news',  //根据信息类型，选择下面对应的信息结构体

            "news" => array(            //不支持保密
                "articles" => [
                    array(
                        "title" => "title",
                        "description" => "description",
                        "url" => "http://weix2.hengdianworld.com/enterprise/article/articledetail_ldjl.php?date=".date("Y-m-d"),
                        "picurl" => "https://weix.hengdianworld.com/images/ldjl_data.jpg",
                    ),
                ]
            ),

        );
        $weObj->sendMessage($date);
/*
        $response = $weObj->server->serve();

        return $response;*/



    }
}
