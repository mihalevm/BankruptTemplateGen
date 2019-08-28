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
        <div class="col-md-2"><?=\Yii::t('app','Captcha')?></div>
        <div class="col-md-4">
            <?=MaskedInput::widget(['name' => 'captcha_str','mask' => '999999', 'options'=>['class'=>'form-control', 'placeholder'=>\Yii::t('app','Captcha')]]);?>
        </div>
    </div>

    <div class="row captcha-size">
        <div class="col-md-3 col-md-offset-2">
            <img id="captcha" alt="<?=\Yii::t('app','Captcha request')?>" src="" data-uid="" data-jid=""/>
        </div>
        <div class="col-md-1 captcha-refresh">
            <button type="button" class="btn btn-primary glyphicon glyphicon-refresh pull-right" onclick="pcheck.getcaptcha();" title="<?=\Yii::t('app','Refresh captcha')?>"></button>
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
