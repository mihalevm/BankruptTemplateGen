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
use app\models\GibddForm;

class GibddController extends Controller {

    private function _sendJSONAnswer($res){
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $response->data = $res;

        return $response;
    }

    public function actionIndex(){
        $model = new GibddForm();
        $sid = isset($_COOKIE['sid']) ? $_COOKIE['sid'] : null;
        $r = Yii::$app->request;

        $params = [
            'model' => $model,
        ];

        if (null !== $r->get('sid')) {
            $sid = $r->get('sid');
            $model->_addSession($sid);
        }

        if ( ! isset( $sid ) && !$model->getIDbySID($sid)){
            Yii::$app->response->redirect('/');
        }

        return $this->render('index', $params);
    }

    public function actionCheck(){
        $r     = Yii::$app->request;
        $res   = null;
        $model = new GibddForm();

        if (   null != $r->post('dcard')
            && null != $r->post('rdate')
            && null != $_COOKIE['sid']
        ){
                        $res = $model->Check(
                            $r->post('dcard'),
                            $r->post('rdate'),
                            $_COOKIE['sid']
                        );
        }

        return $this->_sendJSONAnswer($res);
    }

    public function actionSaved(){
        $sid = isset($_COOKIE['sid']) ? $_COOKIE['sid'] : null;
        $res = null;

        $model = new GibddForm();

        if ($sid){
            $res = $model->getSavedData($sid);
            if (isset($res['rdata'])){
                $res = $res['rdata'];
            }
        }

        return $this->_sendJSONAnswer($res);
    }
}
