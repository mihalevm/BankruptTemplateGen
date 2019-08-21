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
use app\models\PcheckForm;

class PcheckController extends Controller {

    private function _sendJSONAnswer($res){
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $response->data = $res;

        return $response;
    }

    public function actionIndex(){
        $model = new PcheckForm();
        return $this->render('index',[
            'model' => $model,
            ]
        );
    }

    public function actionCheck(){
        $r     = Yii::$app->request;
        $res   = null;
        $model = new PcheckForm();

        if (   null != $r->post('sid')
            && null != $r->post('s')
            && null != $r->post('n')
        ){
            $res = $model->PassportValidate(
                $r->post('sid'),
                $r->post('s'),
                $r->post('n')
            );
        }

        return $this->_sendJSONAnswer($res);
    }
}
