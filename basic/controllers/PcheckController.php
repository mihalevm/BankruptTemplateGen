<?php
/**
 * Created by PhpStorm.
 * User: mmv
 * Date: 05.08.2019
 * Time: 11:10
 */

namespace app\controllers;

// upload unaviable passports
// http://guvm.mvd.ru/upload/expired-passports/list_of_expired_passports.csv.bz2

use Yii;
use yii\web\Controller;
use app\models\PcheckForm;

class PcheckController extends Controller {

    private function _sendJSONAnswer($res){
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $response->data = $res;

        return $response;
    }

    public function actionIndex(){
        $model  = new PcheckForm();
        $r = Yii::$app->request;
        $sid = $_REQUEST['sid'];
        $params = [
            'pnumber'  => '',
            'pserial' => '',
        ];

        if (null !== $r->get('sid')) {
            $sid = $r->get('sid');
        }

        if ( ! isset( $sid ) ){
            Yii::$app->response->redirect('/');
        } else {
            $params_value = $model->getSavedData($sid);
            if ($params_value['pserial'] && $params_value['pnumber']) {
                $params['pnumber'] = $params_value['pnumber'];
                $params['pserial'] = $params_value['pserial'];
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
        $model = new PcheckForm();

        if (   isset($_REQUEST['sid'])
            && null != $r->post('s')
            && null != $r->post('n')
            && null != $r->post('c')
            && null != $r->post('uid')
            && null != $r->post('jid')
        ){
            $res = $model->PassportValidate(
                $_REQUEST['sid'],
                $r->post('s'),
                $r->post('n'),
                $r->post('c'),
                $r->post('uid'),
                $r->post('jid')
            );
        }

        return $this->_sendJSONAnswer($res);
    }

    public function actionGetcaptcha (){
        $res   = null;
        $model = new PcheckForm();
        $res   = $model->getCaptcha();

        return $this->_sendJSONAnswer($res);
    }
}
