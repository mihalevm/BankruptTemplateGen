<?php
/**
 * Created by PhpStorm.
 * User: mmv
 * Date: 05.08.2019
 * Time: 11:15
 */

namespace app\models;

use Yii;
use yii\base\Model;
use yii\httpclient\Client;

class UserInitForm extends Model  {
    protected $db_conn;

    function __construct () {
        $this->db_conn = Yii::$app->db;
    }
/*
    private function addFsspItem ( $uid, $sid, $owner, $doc_num, $doc_id, $doc_edate, $summ, $psumm, $fssp_div, $fssp_ex ) {
        return 0;

        $this->db_conn->createCommand("insert into bg_module_fssp (uid, sid, owner, doc_num, doc_id, doc_edate, summ, psumm, fssp_div, fssp_ex) values (:uid, :sid, :owner, :doc_num, :doc_id, :doc_edate, :summ, :psumm, :fssp_div, :fssp_ex)",
        [
            ':uid'       => null,
            ':sid'       => null,
            ':owner'     => null,
            ':doc_num'   => null,
            ':doc_id'    => null,
            ':doc_edate' => null,
            ':summ'      => null,
            ':psumm'     => null,
            ':fssp_div'  => null,
            ':fssp_ex'   => null
        ])
            ->bindValue(':uid',       $uid       )
            ->bindValue(':sid',       $sid       )
            ->bindValue(':owner',     $owner     )
            ->bindValue(':doc_num',   $doc_num   )
            ->bindValue(':doc_id',    $doc_id    )
            ->bindValue(':doc_edate', $doc_edate )
            ->bindValue(':summ',      $summ      )
            ->bindValue(':psumm',     $psumm     )
            ->bindValue(':fssp_div',  $fssp_div  )
            ->bindValue(':fssp_ex',   $fssp_ex   )
            ->execute();

        return 0;
    }

    private function getHttpClient ( $sid ) {
        $host = 'is.fssprus.ru';
        $http = 'https://'.$host;
        $url  = $http.'/ajax_search';

        $client = new Client();

        $response = $client->createRequest()
            ->setOptions([
                'timeout' => 2
            ])
            ->setMethod('get')
            ->setUrl($url)
            ->setHeaders([
                'Accept'           => 'application/json, text/javascript, /*; q=0.01',
                'Accept-Encoding'  => 'gzip, deflate, br',
                'Accept-Language'  => 'ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3',
                'Cache-Control'    => 'no-cache',
                'Connection'       => 'keep-alive',
                'Content-Type'     => 'application/x-www-form-urlencoded; charset=UTF-8',
                'Host'             => $host,
                'Pragma'           => 'no-cache',
                'Referer'          => $http,
                'User-Agent'       => 'runscope/0.1,Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:39.0) Gecko/20100101 Firefox/39.0',
                'X-Requested-With' => 'XMLHttpRequest',
                'Cookie'           => 'connect.sid='.$sid.';',
            ]);

        return $response;
    }

    private function SumCalc ($html) {
        $dom = new \DOMDocument();
        $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'), LIBXML_NOERROR);
        $data = [];

        $all_tr = $dom->getElementsByTagName('tr');

        foreach ($all_tr as $tr) {
            $node = $tr->childNodes;

            if ($node->length > 1 && $node->item(5)->nodeName == 'td'){
                $matches = null;
                preg_match('/^(.+):\s(\d+\.\d\d)/U', $node->item(5)->nodeValue, $matches);
                if (strcasecmp('Исполнительский сбор', $matches[1]) !== 0){
                    $this->addFsspItem(
                        0,
                        0,
                         $node->item(0)->nodeValue,
                         $node->item(1)->nodeValue,
                         $node->item(2)->nodeValue,
                         $node->item(3)->nodeValue,
                         $node->item(5)->nodeValue,
                         floatval($matches[2]),
                         $node->item(6)->nodeValue,
                         $node->item(7)->nodeValue
                    );

                    array_push($data, [
                        "owner"     => $node->item(0)->nodeValue,
                        "doc_num"   => $node->item(1)->nodeValue,
                        "doc_id"    => $node->item(2)->nodeValue,
                        "doc_edate" => $node->item(3)->nodeValue,
                        "summ"      => $node->item(5)->nodeValue,
                        "psumm"     => floatval($matches[2]),
                        "fssp_div"  => $node->item(6)->nodeValue,
                        "fssp_ex"   => $node->item(7)->nodeValue
                    ]);
                }
            }
        }

        return $data;
    }

    public function Send_Grab( $last_name, $first_name, $patronymic, $date, $sid, $code) {
        $ts       = time();
        $response = $this->getHttpClient($sid);
        $answer   = null;

        $response->setData([
            'is' =>[
                'ip_preg'    => '',
                'variant'    => '1',
                'last_name'  => $last_name,
                'first_name' => $first_name,
                'patronymic' => $patronymic,
                'date'       => $date,
                'drtr_name'  => '',
                'address'    => '',
                'ip_number'  => '',
                'id_number'  => '',
                'id_type'    => [],
                'id_issuer'  => '',
                'region_id'  => [-1],
                'extended'   => 1,
            ],
            'code'     => $code,
            'nocache'  => 1,
            'system'   => 'ip',
            'callback' => 'jQuery340016456929004994936_'.$ts,
            '_'        => $ts,
        ]);

        try {
            $answer = $response->send();
        } catch (\Exception $e) {
            $answer = null;
        }

        if (null != $answer  && $answer->getIsOk() ){
            $matches = null;
            preg_match('/\(\{\"data\":\"(.+)\",\"/', $answer->content, $matches);
            $answer = null;
            $answer['data'] = $matches[1];
            $answer['data'] = str_replace('\r\n','', $answer['data']);
            $answer['data'] = str_replace('  ','', $answer['data']);
            $answer['data'] = str_replace('\"','"', $answer['data']);
            $answer['data'] = $this->SumCalc($answer['data']);

            $answer['error'] = 200;
        } else {
            $answer['data'] = \Yii::t('app','Service unavailable');
            $answer['error'] = 500;
        }

        return $answer;
    }

    public function GetCaptcha() {
        $captcha = [];

        $response = $this->getHttpClient('');

        $response->setData([
            'is' =>[
                'ip_preg'    => '',
                'variant'    => '1',
                'drtr_name'  => '',
                'address'    => '',
                'ip_number'  => '',
                'id_number'  => '',
                'id_type'    => [],
                'id_issuer'  => '',
                'region_id'  => [-1],
                'extended'   => 1,
            ],
            'nocache' => 1,
            'system'  => 'ip',
        ]);

        try {
            $answer = $response->send();
        } catch (\Exception $e) {
            $answer = null;
        }

        if (null != $answer  && $answer->getIsOk() ){
            $matches = null;
            preg_match('/\"(data:image.+)\\\"\sid=\\\"capchaVisual/', $answer->content, $matches);
            $captcha['captcha'] = $matches[1];
            $captcha['cookies'] = $answer->getCookies();
            $captcha['error']   = 200;

            if ($captcha['cookies']->get('connect.sid')) {
                $captcha['cookies'] = $captcha['cookies']->get('connect.sid')->value;
            }
        } else {
            $captcha['captcha'] = '';
            $captcha['cookies'] = '';
            $captcha['error']   = 500;
        }

        return $captcha;
    }
*/

    private function generateRandomString($length = 32) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    private function testUNQSid ($sid) {
        $arr = ($this->db_conn->createCommand("select count(*) as sid from bg_module_userinit where sid=:new_sid",[
            ':new_sid'=>'',
        ])
            ->bindValue(':new_sid', $sid)
            ->queryAll())[0];

        return $arr['sid'];
    }

    public function getNewSID (){
        $new_sid = null;

        do {
            $new_sid = $this->generateRandomString();
        } while($this->testUNQSid($new_sid));

        return $new_sid;
    }

    public function saveNewSession ($email, $sid) {
        $this->db_conn->createCommand("insert into bg_module_userinit (sid, email) values (:sid, :email)",
            [
                ':sid'       => null,
                ':email'     => null,
            ])
            ->bindValue(':sid',   $sid   )
            ->bindValue(':email', $email )
            ->execute();

        return $this->db_conn->getLastInsertID();
    }
}