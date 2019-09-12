<?php
/**
 * Created by PhpStorm.
 * User: mmv
 * Date: 05.08.2019
 * Time: 11:15
 */

namespace app\models;

use Yii;

class DocsForm extends ToolsForm {
    protected $db_conn;
    public    $pdfFile;
    public    $fDesc;
    private   $base_dir = 'uploads/';

    public function rules() {
        return [
            [['pdfFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'pdf'],
        ];
    }

    function __construct () {
        $this->db_conn = Yii::$app->db;
    }

    private function deleteOldFile ($sid, $ftype) {
        $dsid = $this->getIDbySID($sid);

        $int_file_name = ($this->db_conn->createCommand("select int_name from bg_module_docs where sid=:sid and ftype=:ftype",[
            ':sid'   => null,
            ':ftype' => null,
        ])
        ->bindValue(':sid', $dsid)
        ->bindValue(':ftype', $ftype)
        ->queryAll())[0];

        if (file_exists($this->base_dir.$sid.'/'.$int_file_name['int_name'])){
            unlink($this->base_dir.$sid.'/'.$int_file_name['int_name']);
        }

        return true;
    }

    private function addDocument ($sid, $ftype, $fdesc, $ext_name) {
        $this->deleteOldFile($sid, $ftype);
        $sid = $this->getIDbySID($sid);
        $int_name = $this->generateRandomString(40);

        $this->db_conn->createCommand("delete from bg_module_docs where sid=:sid and ftype=:ftype",
            [
                ':sid'   => null,
                ':ftype' => null
            ])
            ->bindValue(':sid',   $sid )
            ->bindValue(':ftype', $ftype )
            ->execute();

        $this->db_conn->createCommand("insert into bg_module_docs (sid, ftype, ext_name, int_name, fdesc) values (:sid, :ftype, :ext_name, :int_name, :fdesc)",
            [
                ':sid'      => null,
                ':ftype'    => null,
                ':ext_name' => null,
                ':int_name' => null,
                ':fdesc'    => null,
            ])
            ->bindValue(':sid',      $sid      )
            ->bindValue(':ftype',    $ftype    )
            ->bindValue(':fdesc',    $fdesc    )
            ->bindValue(':ext_name', $ext_name )
            ->bindValue(':int_name', $int_name.'.'.$this->pdfFile->extension )
            ->execute();

        return $int_name;
    }

    public function upload($sid, $fileType, $fileDesc) {

        if ($this->validate()) {
            $internal_file_name = $this->addDocument($sid, $fileType, $fileDesc, $this->pdfFile->baseName.'.'.$this->pdfFile->extension);

            if (!is_dir($this->base_dir.$sid)){
                mkdir($this->base_dir.$sid);
            }

            if ($internal_file_name) {
                $this->pdfFile->saveAs($this->base_dir.$sid.'/'. $internal_file_name . '.' . $this->pdfFile->extension);
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    public function getSavedData ($sid) {
        $sid = $this->getIDbySID($sid);
        $result = [];

        $uploaded_file = $this->db_conn->createCommand("select ftype, ext_name, fdesc from bg_module_docs where sid=:sid",[
            ':sid' => null,
        ])
            ->bindValue(':sid', $sid)
            ->queryAll();

        if (count($uploaded_file) > 0) {
            foreach ($uploaded_file as $file_item) {
                $result[$file_item['ftype']]['ext_name'] = $file_item['ext_name'];
                $result[$file_item['ftype']]['fdesc'] = $file_item['fdesc'];
            }
        }

        return $result;

    }
}