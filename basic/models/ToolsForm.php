<?php
/**
 * Created by PhpStorm.
 * User: mmv
 * Date: 23.08.2019
 * Time: 14:17
 */

namespace app\models;


use yii\base\Model;

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
}