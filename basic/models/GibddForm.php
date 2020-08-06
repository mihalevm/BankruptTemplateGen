<?php
/**
 * Created by PhpStorm.
 * User: mmv
 * Date: 05.08.2019
 * Time: 11:15
 */

namespace app\models;

use Yii;

class GibddForm extends ToolsForm {
    protected $db_conn;

    function __construct () {
        $this->db_conn = Yii::$app->db;
    }

    private function gibdd_fetch ( $dcard, $rdate ) {
        $script = '/usr/bin/node /opt/gibdd_fetch/gibdd_dev.js';
        $ans = null;

        if ($dcard && $rdate) {
            exec($script.' '.$dcard.' '.$rdate, $ans);
        }

        return array_pop($ans);
    }

    public function Check( $dcard, $rdate, $session ) {
        $answer = null;
        $sid = $this->getIDbySID($session);

        $answer = $this->gibdd_fetch($dcard, $rdate);

        $janswer = json_decode($answer);

        if(property_exists($janswer, 'code') && intval($janswer->code) == 100) {
            $this->db_conn->createCommand("insert into bg_module_gibdd (sid, dcard, rdate, rdata) values (:sid, :dcard, :rdate, :rdata) on duplicate key update sid=:sid, dcard=:dcard, rdate=:rdate, rdata=:rdata",
                [
                    ':sid'   => null,
                    ':dcard' => null,
                    ':rdate' => null,
                    ':rdata' => null,
                ])
                ->bindValue(':sid',   $sid )
                ->bindValue(':dcard', $dcard   )
                ->bindValue(':rdate', date('Y-m-d', strtotime($rdate)))
                ->bindValue(':rdata', $answer  )
                ->execute();
        }

        return $answer;
    }

     public function getSavedData ($sid) {
        $sid = $this->getIDbySID($sid);

        $user_attr = $this->db_conn->createCommand("select dcard, rdate, rdata from bg_module_gibdd where sid=:sid limit 1",[
            ':sid' => null,
        ])
            ->bindValue(':sid', $sid)
            ->queryAll();

        if ( sizeof($user_attr) > 0 ) {
            $user_attr = $user_attr[0];
        } else {
            $user_attr = null;
        }

        return $user_attr;
    }
}