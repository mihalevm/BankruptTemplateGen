<?php
/**
 * Created by PhpStorm.
 * User: mmv
 * Date: 05.08.2019
 * Time: 11:10
 */

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\UserInitForm;

class UserInitController extends Controller {

    private function _sendJSONAnswer($res){
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $response->data = $res;

        return $response;
    }

    public function actionIndex(){
        $model = new UserInitForm();

        return $this->render('index', [
            'sid' => $model->getNewSID(),
        ]);
    }

    public function actionStart(){
        $model  = new UserInitForm();
        $r      = Yii::$app->request;

        $result = [];

        if (
               null !== $r->post('email')
            && null !== $r->post('sid')
            && $model->saveNewSession($r->post('email'), $r->post('sid'))
        ) {
            $result['redirect'] = 'grab';
        }

        return $this->_sendJSONAnswer($result);
    }

    public function actionGetsession (){
        $model  = new UserInitForm();
        $r      = Yii::$app->request;
        $result = [];

        if ( null !== $r->post('email') ) {
            $result = $model->getSession($r->post('email'));
        }

        return $this->_sendJSONAnswer($result);
    }

}
