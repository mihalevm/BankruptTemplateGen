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
use app\models\GrabForm;

class GrabController extends Controller {

    private function _sendJSONAnswer($res){
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $response->data = $res;

        return $response;
    }

    public function actionIndex(){
        if (!isset($_REQUEST['sid'])){
            Yii::$app->response->redirect('/');
        }

        $model = new GrabForm();
        return $this->render('index',[
            'model' => $model,
            ]
        );
    }

    public function actionGetcapcha(){
        $model = new GrabForm();
        $res = $model->GetCaptcha();

        return $this->_sendJSONAnswer($res);
    }

    public function actionSendgrab(){
        $r     = Yii::$app->request;
        $res   = null;
        $model = new GrabForm();

        if (   null != $r->post('last_name')
            && null != $r->post('first_name')
            && null != $r->post('patronymic')
            && null != $r->post('date')
            && null != $r->post('sid')
            && null != $r->post('code')
            && null != $r->post('s')
        ){
                        $res = $model->Send_Grab(
                            $r->post('last_name'),
                            $r->post('first_name'),
                            $r->post('patronymic'),
                            $r->post('date'),
                            $r->post('sid'),
                            $r->post('code'),
                            $r->post('s')
                        );
        }

        return $this->_sendJSONAnswer($res);
    }
}
