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
use app\models\DocsForm;
use yii\web\UploadedFile;

class DocsController extends Controller {

    private function _sendJSONAnswer($res){
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $response->data = $res;

        return $response;
    }

    public function actionIndex(){
        $model = new DocsForm();
        $r = Yii::$app->request;
        $sid = $_REQUEST['sid'];

        $params = [
            'model'  => $model,
            'upload_result' => '',
            'file_exist' => [],
            'fileDesc' => '',
        ];

        if (null !== $r->get('sid')) {
            $sid = $r->get('sid');
        }

        if ( ! isset( $sid ) ){
            Yii::$app->response->redirect('/');
        } else {
            $uploaded_files = $model->getSavedData($sid);
            $params['file_exist'] = $uploaded_files;

            if (Yii::$app->request->isPost) {
                $model->pdfFile = UploadedFile::getInstance($model, 'pdfFile');
                $p = Yii::$app->request->post('DocsForm');

                if ($model->upload($_REQUEST['sid'],$p['typeFile'], $p['fDesc'])) {
                    $params['upload_result'] = \Yii::t('app','File successfully uploaded');
                }
            }
        }

        return $this->render('index', $params);
    }
}
