<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use EasyWeChat\Factory;
use EasyWeChat\Kernel\Messages\Text;
class TestController extends Controller
{
    public function index()
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
        $app = Factory::work($config);
        $message = new Text('FromUserName');
        $result = $app->customer_service->message($message)->to('hd_wangke')->send();

    }
}
