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
        $model = new GrabForm();
        $sid = $_REQUEST['sid'];
        $r = Yii::$app->request;

        $params = [
            'firstName'  => '',
            'sureName'   => '',
            'secondName' => '',
            'birthDate'  => '',
            'summ'       => '',
            'model'      => $model
        ];

        if (null !== $r->get('sid')) {
            $sid = $r->get('sid');
        }

        if ( ! isset( $sid ) ){
            Yii::$app->response->redirect('/');
        } else {
            $user_attr = $model->getSavedData($sid);

            if (null !== $user_attr) {
                $params['summ']       = round(floatval($user_attr[0]), 2);
                $params['sureName']   = $user_attr[1];
                $params['firstName']  = $user_attr[2];
                $params['secondName'] = $user_attr[3];
                $params['birthDate']  = $user_attr[4];
                $model->_addSession($sid);
            } else {
                if (!$model->getIDbySID($sid)) {
                    Yii::$app->response->redirect('/');
                }
            }
        }

        return $this->render('index', $params);
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
            && null != $_REQUEST['sid']
        ){
                        $res = $model->Send_Grab(
                            $r->post('last_name'),
                            $r->post('first_name'),
                            $r->post('patronymic'),
                            $r->post('date'),
                            $r->post('sid'),
                            $r->post('code'),
                            $_REQUEST['sid']
                        );
        }

        return $this->_sendJSONAnswer($res);
    }
}
