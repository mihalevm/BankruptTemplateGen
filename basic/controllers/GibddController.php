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
                $params['dcard'] = $user_attr['dcard'];
                $params['rdate'] = $user_attr['rdate'];
                $params['rdata'] = json_decode($user_attr['rdata']);
                $model->_addSession($sid);
            } else {
                if (!$model->getIDbySID($sid)) {
                    Yii::$app->response->redirect('/');
                }
            }
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
}
