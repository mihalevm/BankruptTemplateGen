<?php
/**
 * Created by PhpStorm.
 * User: mmv
 * Date: 05.08.2019
 * Time: 11:15
 */

namespace app\models;

use Yii;

class DoclistForm extends ToolsForm {
    protected $db_conn;

    function __construct () {
        $this->db_conn = Yii::$app->db;
    }

    public function getDocList() {
        $arr = $this->db_conn->createCommand("select uid, rdate, email from bg_module_userinit")
            ->queryAll();

        return $arr;
    }

    public function getDocAttrs($id) {
        $attrs = [];

        $arr = $this->db_conn->createCommand("select rdate, email from bg_module_userinit where uid=:uid",[
            ':uid' => null,
        ])
        ->bindValue(':uid', $id)
        ->queryAll();

        $attrs['rdate'] = $arr[0]['rdate'];
        $attrs['email'] = $arr[0]['email'];

        $arr = $this->db_conn->createCommand("select validate from bg_module_passport_verifed where sid=:uid",[
            ':uid' => null,
        ])
            ->bindValue(':uid', $id)
            ->queryAll();

        $attrs['pvalidate'] = null;

        if (sizeof($arr)){
            $attrs['pvalidate'] = $arr[0]['validate'];
        }

        $arr = $this->db_conn->createCommand("select rdata from bg_module_egrul where sid=:uid",[
            ':uid' => null,
        ])
            ->bindValue(':uid', $id)
            ->queryAll();

        $attrs['egrul'] = null;

        if (sizeof($arr)){
            $attrs['egrul'] = $arr[0]['rdata'];
        }

        $arr = $this->db_conn->createCommand("select rdata from bg_module_gibdd where sid=:uid",[
            ':uid' => null,
        ])
            ->bindValue(':uid', $id)
            ->queryAll();

        $attrs['gibdd'] = null;

        if (sizeof($arr)){
            $attrs['gibdd'] = $arr[0]['rdata'];
        }


        $arr = $this->db_conn->createCommand("select * from bg_module_fssp where sid=:uid",[
            ':uid' => null,
        ])
            ->bindValue(':uid', $id)
            ->queryAll();

        $attrs['fssp'] = null;

        if (sizeof($arr)){
            $attrs['fssp'] = $arr;
        }

        return $attrs;

    }
}