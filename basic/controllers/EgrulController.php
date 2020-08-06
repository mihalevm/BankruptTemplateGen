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
use app\models\EgrulForm;

class EgrulController extends Controller {

    private function _sendJSONAnswer($res){
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $response->data = $res;

        return $response;
    }

    public function actionIndex(){
        $model  = new EgrulForm();
        $r = Yii::$app->request;
        $sid = $_COOKIE['sid'];
        $params = [
            'inn'  => '',
            'name' => '',
            'attr' => ''
        ];

        if (null !== $r->get('sid')) {
            $sid = $r->get('sid');
        }

        if ( ! isset( $sid ) ){
            Yii::$app->response->redirect('/');
        } else {
            $json_obj = json_decode($model->getSavedData($sid));

            if (null !== $json_obj) {
                $json_obj = $json_obj->rows[0];
                $params['inn'] = $json_obj->i;
                $params['name'] = $json_obj->n;
                $params['attr'] = \Yii::t('app','OGRNIP').": " . $json_obj->o . " , ".\Yii::t('app','INN').": " . $json_obj->i . " , ".\Yii::t('app','Date OGRNIP').": " . $json_obj->r;
                if (property_exists($json_obj, 'e')) {
                    $params['attr'] = $params['attr'].' '.\Yii::t('app','Date close').": " . $json_obj->e;
                }
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
        $model  = new EgrulForm();
        $r      = Yii::$app->request;
        $result = [];

        if (null !== $r->post('inn')) {
            $result = $model->EgrulRequest($r->post('inn'));
        }

        return $this->_sendJSONAnswer($result);
    }
}
