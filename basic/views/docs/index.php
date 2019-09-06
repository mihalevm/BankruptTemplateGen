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
        <div class="col-md-5" style="line-height: 1;">
            <? if ($upload_result['A']) {echo \Yii::t('app','File already uploaded: ').$upload_result['A'];}?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-1 col-md-offset-4 captcha-refresh">
            <button  class="btn btn-primary pull-right" ><?=\Yii::t('app','Upload')?></button>
        </div>
        <div class="col-md-1 captcha-refresh">
            <button type="button" name="next" class="btn btn-primary pull-right" onclick="webtools.nextStep('docs');"><?=\Yii::t('app','Next')?></button>
        </div>
    </div>
    <?php ActiveForm::end() ?>
</div>
