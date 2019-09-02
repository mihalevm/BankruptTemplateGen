<?php
use yii\widgets\MaskedInput;

$this->title = \Yii::t('app','EGRUL check');
?>
<div class="site-index">
    <div class="row">
        <div class="col-md-2"><?=\Yii::t('app','INN')?></div>
        <div class="col-md-4" id="container">
            <?=MaskedInput::widget(['value' => $inn, 'name' => 'inn','mask' => '99999999999999', 'options'=>['autofocus'=>'', 'class'=>'form-control', 'placeholder'=>\Yii::t('app','INN')]]);?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-1 col-md-offset-4 captcha-refresh">
            <button type="button" class="btn btn-primary pull-right" onclick="egrul.check()"><?=\Yii::t('app','Request')?></button>
        </div>
        <div class="col-md-1 captcha-refresh">
            <button type="button" name="next" class="btn btn-primary pull-right" onclick="webtools.nextStep('egrul')"><?=\Yii::t('app','Next')?></button>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <label id="egrul_name"><?=$name?></label>
        </div>
    </div>
    <div class="row">
        <div class="col-md-10">
            <label id="egrul_attr"><?=$attr?></label>
        </div>
    </div>
</div>