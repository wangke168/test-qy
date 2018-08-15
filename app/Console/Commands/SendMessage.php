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

        $url = "https://" . $_SERVER['HTTP_HOST'] . "/sendmessage";
        $this->curlPost($url);

    }
    private function curlPost($url,$data="")
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
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
