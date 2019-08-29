<?php
/**
 * Created by PhpStorm.
 * User: mmv
 * Date: 05.08.2019
 * Time: 11:15
 */

namespace app\models;

use Yii;

class UserInitForm extends ToolsForm  {
    protected $db_conn;

    function __construct () {
        $this->db_conn = Yii::$app->db;
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