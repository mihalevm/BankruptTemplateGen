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

class GrabForm extends ToolsForm {
    protected $db_conn;

    function __construct () {
        $this->db_conn = Yii::$app->db;
    }

    private function cleanFsspItem ($sid) {
        $this->db_conn->createCommand("delete from bg_module_fssp where sid=:sid",
            [
                ':sid' => null
            ])
            ->bindValue(':sid', $sid )
            ->execute();
    }

    private function addFsspItem ( $sid, $owner, $doc_num, $doc_id, $doc_edate, $summ, $psumm, $fssp_div, $fssp_ex ) {
        $this->db_conn->createCommand("insert into bg_module_fssp (sid, owner, doc_num, doc_id, doc_edate, summ, psumm, fssp_div, fssp_ex) values (:sid, :owner, :doc_num, :doc_id, :doc_edate, :summ, :psumm, :fssp_div, :fssp_ex)",
        [
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
                'Accept'           => 'application/json, text/javascript, */*; q=0.01',
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

    private function parseContent ($html, $session) {
        $session = $this->getIDbySID($session);
        $dom = new \DOMDocument();
        $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'), LIBXML_NOERROR);
        $data = [];

        $all_tr = $dom->getElementsByTagName('tr');
        $this->cleanFsspItem($session);

        foreach ($all_tr as $tr) {
            $node = $tr->childNodes;

            if ($node->length > 1 && $node->item(5)->nodeName == 'td'){
                $matches = null;
                preg_match('/^(.+):\s(\d+\.\d\d)/U', $node->item(5)->nodeValue, $matches);
                if (strcasecmp('Исполнительский сбор', $matches[1]) !== 0){
                    $this->addFsspItem(
                         $session,
                         $node->item(0)->nodeValue,
                         $node->item(1)->nodeValue,
                         $node->item(2)->nodeValue,
                         $node->item(3)->nodeValue,
                         $node->item(5)->nodeValue,
                         floatval($matches[2]),
                         $node->item(6)->nodeValue,
                         $node->item(7)->nodeValue
                    );

                    $data["sum"] += floatval($matches[2]);
                    $data["cnt"]++;
                }
            }
        }

        return $data;
    }

    public function Send_Grab( $last_name, $first_name, $patronymic, $date, $sid, $code, $session) {
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
            if (!strstr($answer->content, "Неверно введен код") ) {
                $matches = null;
                preg_match('/\(\{\"data\":\"(.+)\",\"/', $answer->content, $matches);
                $answer = null;
                $answer['data'] = $matches[1];
                $answer['data'] = str_replace('\r\n', '', $answer['data']);
                $answer['data'] = str_replace('  ', '', $answer['data']);
                $answer['data'] = str_replace('\"', '"', $answer['data']);
                $answer['data'] = $this->parseContent($answer['data'], $session);

                $answer['error'] = 200;
            } else {
                $answer = null;
                $answer['data'] = \Yii::t('app','Captcha code error');
                $answer['error'] = 400;
            }
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

    public function getSavedData ($sid) {
        $sid = $this->getIDbySID($sid);

        $user_attr = $this->db_conn->createCommand("select owner from bg_module_fssp where sid=:sid limit 1",[
            ':sid' => null,
        ])
            ->bindValue(':sid', $sid)
            ->queryAll();

        $user_attr = sizeof($user_attr) ? $user_attr[0]['owner'] : null;

        $user_summ = $this->db_conn->createCommand("select sum(psumm) as summ from bg_module_fssp where sid=:sid",[
            ':sid' => null,
        ])
            ->bindValue(':sid', $sid)
            ->queryAll();

        $user_summ = sizeof($user_summ) ? $user_summ[0]['summ'] : null;

        $user_attr = $user_attr && $user_summ ? preg_split('/\s/', $user_summ.' '.$user_attr):null;

        return $user_attr;
    }
}