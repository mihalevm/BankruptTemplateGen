<?php
/**
 * Created by PhpStorm.
 * User: mmv
 * Date: 23.08.2019
 * Time: 14:17
 */

namespace app\models;

use yii\base\Model;
use \yii\web\Cookie;

class ToolsForm extends Model {
    protected $db_conn;

    function __construct () {
        $this->db_conn = Yii::$app->db;
    }

    public function getIDbySID ($sid){
        $id_ref = ($this->db_conn->createCommand("SELECT uid FROM bg_module_userinit WHERE sid=:sid",
            [
                'sid'=>'',
            ])
            ->bindValue(':sid', $sid)
            ->queryAll())[0];

        return $id_ref['uid'];
    }

    public function generateRandomString($length = 32) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function _addSession($sid) {
        $cookie = new Cookie([
            'name'  => 'sid',
            'value' => $sid
        ]);

        \Yii::$app->getResponse()->getCookies()->add($cookie);

        return true;
    }

}