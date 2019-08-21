<?php

use yii\widgets\MaskedInput;

/* @var $this yii\web\View */

$this->title = \Yii::t('app','Passport check');
?>
<div class="site-index">
    <div class="row">
        <div class="col-md-2"><?=\Yii::t('app','Passport serial')?></div>
        <div class="col-md-4">
            <?=MaskedInput::widget(['name' => 'pserial','mask' => '9999', 'options'=>['autofocus'=>'', 'class'=>'form-control', 'placeholder'=>\Yii::t('app','Serial')]]);?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-2"><?=\Yii::t('app','Passport number')?></div>
        <div class="col-md-4">
            <?=MaskedInput::widget(['name' => 'pnumber','mask' => '999999', 'options'=>['class'=>'form-control', 'placeholder'=>\Yii::t('app','Number')]]);?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-1 col-md-offset-4 captcha-refresh">
            <button type="button" name="request" class="btn btn-primary pull-right" onclick="pcheck.check();"><?=\Yii::t('app','Request')?></button>
        </div>
        <div class="col-md-1 captcha-refresh">
            <button type="button" name="next" class="btn btn-primary pull-right" onclick="webtools.nextStep('pcheck');"><?=\Yii::t('app','Next')?></button>
        </div>
    </div>
</div>
<div class="center-result"><span id="log_result"></span></div>
