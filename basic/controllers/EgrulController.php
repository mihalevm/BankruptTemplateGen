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

        if (!isset($_REQUEST['sid'])){
            Yii::$app->response->redirect('/');
        }

        return $this->render('index');
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
