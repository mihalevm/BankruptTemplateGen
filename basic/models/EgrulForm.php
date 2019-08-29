<?php
/**
 * Created by PhpStorm.
 * User: mmv
 * Date: 05.08.2019
 * Time: 11:15
 */

namespace app\models;

use Yii;
use yii\httpclient\Client;

class EgrulForm extends ToolsForm  {
    protected $db_conn;

    function __construct () {
        $this->db_conn = Yii::$app->db;
    }

    private function _sendFirstRequest($key) {
        $client = new Client();

        $response = $client->createRequest()
            ->setMethod('post')
            ->setUrl('https://egrul.nalog.ru/')
            ->setHeaders([
                'Accept' => 'application/json, text/javascript, */*; q=0.01',
                'Accept-Encoding' => 'gzip, deflate, br',
                'Accept-Language' => 'ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3',
                'Cache-Control' => 'no-cache',
                'Connection' => 'keep-alive',
                'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
                'Host' => 'egrul.nalog.ru',
                'Pragma' => 'no-cache',
                'Referer' => 'https://egrul.nalog.ru/',
                'User-Agent' => 'runscope/0.1,Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:39.0) Gecko/20100101 Firefox/39.0',
                'X-Requested-With' => 'XMLHttpRequest'
            ]);

        $response->setData([
            'vyp3CaptchaToken' => '',
            'query' =>	$key,
            'region' => '',
            'PreventChromeAutocomplete' => '',
        ]);

        return $response->send();
    }

    private function _sendSecondRequest($key, $t1, $t2) {
        $client = new Client();

        $response = $client->createRequest()
            ->setMethod('get')
            ->setUrl('https://egrul.nalog.ru/search-result/'.$key)
            ->setHeaders([
                'Accept' => 'application/json, text/javascript, */*; q=0.01',
                'Accept-Encoding' => 'gzip, deflate, br',
                'Accept-Language' => 'ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3',
                'Cache-Control' => 'no-cache',
                'Connection' => 'keep-alive',
                'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
                'Host' => 'egrul.nalog.ru',
                'Pragma' => 'no-cache',
                'Referer' => 'https://egrul.nalog.ru/',
                'User-Agent' => 'runscope/0.1,Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:39.0) Gecko/20100101 Firefox/39.0',
                'X-Requested-With' => 'XMLHttpRequest'
            ]);

        $response->setData([
            'r' => $t1,
            '_' => $t2,
        ]);

        return $response->send();
    }

    public function EgrulRequest($key){
        $res = 0;
        $first_time_mark = round(microtime(true) * 1000);

        $res = $this->_sendFirstRequest($key);

        if ( $res->getIsOk() ) {
            $res = json_decode($res->content);
            if (property_exists($res, 't')){
                $second_time_mark = round(microtime(true) * 1000);
                $res = $this->_sendSecondRequest($res->t, $first_time_mark, $second_time_mark);
                if ( $res->getIsOk() ) {
                    $res = json_decode($res->content);
                    if (property_exists($res, 'rows')){
                        if (sizeof($res->rows)> 0){
                            $res = $res->rows[0];
                        } else {
                            $res = 0;
                        }
                    } else {
                        $res = 0;
                    }
                } else {
                    $res = 0;
                }
            } else {
                $res = 0;
            }
        } else {
            $res = 0;
        }

        return $res;
    }
}