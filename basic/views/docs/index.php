<?php
use yii\widgets\ActiveForm;

$this->title = \Yii::t('app','Upload documents');
?>
<div class="site-index">
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>
    <div class="row">
        <div class="col-md-3">
            <?=\Yii::t('app','File for upload')?>
        </div>
        <div class="col-md-4">
            <?=$form->field($model, 'pdfFile')->fileInput()->label(false)?>
            <?=$form->field($model, 'typeFile')->hiddenInput(['value' => 'A'])->label(false);?>
        </div>

    </div>
    <div class="row">
        <div class="col-md-3">
            <?=$upload_result?>
        </div>
        <div class="col-md-2 col-md-offset-1"><button><?=\Yii::t('app','Upload')?></button></div>
    </div>
    <?php ActiveForm::end() ?>
</div>
