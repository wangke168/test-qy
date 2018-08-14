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
        $config=[
            'corp_id' => 'wwfb1970349326c73f',
            'agent_id' => 1000004,
            'secret' => 'TsbKy9F_yo_d3bXKJ0HNqgcq4FjXW3dPXmXLhyVm918',
            'token' => 'test',
            'aes_key' => 'uY5rLOibklJSaHt8suAz861k7jQdUc8a0vrv4crvNq8',
        ];
        $weObj=Factory::work($config);
        $accessToken = $weObj->access_token;
        $token = $accessToken->getToken(); // token 数组  token['access_token'] 字符串
        $msg=$this->message();
        $url = "https://qyapi.weixin.qq.com/cgi-bin/message/send?access_token=" . $token['access_token'];
        $data="{\"touser\":\"hd_wangke\",\"msgtype\":\"text\",\"agentid\":1000004,\"text\":{\"content\":\"$msg\"},\"safe\":0}";
        $res = $this->curlPost($url,$data);
/*
        $response = $weObj->server->serve();

        return $response;*/



    }
    private function curlPost($url,$data=""){
        $ch = curl_init();
        $opt = array(
            CURLOPT_URL     => $url,
            CURLOPT_HEADER  => 0,
            CURLOPT_POST    => 1,
            CURLOPT_POSTFIELDS      => $data,
            CURLOPT_RETURNTRANSFER  => 1,
            CURLOPT_TIMEOUT         => 20
        );
        $ssl = substr($url,0,8) == "https://" ? TRUE : FALSE;
        if ($ssl){
            $opt[CURLOPT_SSL_VERIFYHOST] = 2;
            $opt[CURLOPT_SSL_VERIFYPEER] = FALSE;
        }
        curl_setopt_array($ch,$opt);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
    private  function  message()
    {
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
    }
}
