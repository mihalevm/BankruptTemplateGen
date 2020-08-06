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
use app\models\DoclistForm;
use yii\data\ArrayDataProvider;

class DoclistController extends Controller {

    private function _sendPDFDoc($res){
        $response = Yii::$app->response;
        $response->headers->add('appliction','octet-stream');
        $response->headers->add('Content-disposition','attachment; filename="doc01.pdf"');
        $response->format = \yii\web\Response::FORMAT_RAW;
        $response->data = $res;

        return $response;
    }

    public function actionIndex() {
        $model = new DoclistForm();

        $allDocs = new ArrayDataProvider([
            'allModels' => $model->getDocList(),
            'sort' => [
                'attributes' => ['rdate', 'email'],
            ],
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);


        return $this->render('index',[
            'model' => $model,
            'allDocs' => $allDocs,
        ]);
    }

    public function actionGetdoc() {
        $r = Yii::$app->request;
        $model = new DoclistForm();
        $uid = null;
        $pdf = null;
        $this->layout = 'doc';
        $mpdf = new \Mpdf\Mpdf(['tempDir' => '/tmp']);

        if (null !== $r->get('id')) {
            $uid = $r->get('id');
            $attrs = $model->getDocAttrs($uid);

            $html_template = $this->render('doc_template',[
                'rdate'     => $attrs['rdate'],
                'email'     => $attrs['email'],
                'pvalidate' => $attrs['pvalidate'],
                'egrul'     => $attrs['egrul'],
                'gibdd'     => $attrs['gibdd'],
                'fssp'      => $attrs['fssp'],
            ]);

            $mpdf->WriteHTML($html_template);
            $pdf = $mpdf->Output();
        }

        return $pdf ? $this->_sendPDFDoc($pdf) : '';
    }
}
