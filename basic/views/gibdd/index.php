<?php

use kartik\date\DatePicker;
use yii\widgets\MaskedInput;

$this->title = \Yii::t('app','Check Gibdd');
?>
<div class="site-index">
    <div class="row">
        <div class="col-md-3"><?=\Yii::t('app','Driver card')?></div>
        <div class="col-md-4">
            <?=MaskedInput::widget([
                'value'   => '',
                'name'    => 'dcard',
                'mask'    => '9999-999999',
                'options' => [
                    'autofocus'   => 'autofocus',
                    'class'       => 'form-control',
                    'placeholder' => \Yii::t('app','Serial-Number')
                ]
            ])
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3"><?=\Yii::t('app','Register date')?></div>
        <div class="col-md-4">
        <?=DatePicker::widget([
                'model'         => $model,
                'attribute'     => 'date',
                'options'       => [
                                    'name'        => 'rdate',
                                    'placeholder' => \Yii::t('app','Register date'),
                                    'value'       => date( '01.m.Y', strtotime(date('Y-m-d'))),
                ],
                'type'          => DatePicker::TYPE_COMPONENT_APPEND,
                'removeButton'  => false,
                'pluginOptions' => [
                    'format'         => 'dd.mm.yyyy',
                    'orientation'    => 'bottom left',
                    'autoclose'      => true,
                    'todayHighlight' => true,
                ]
            ])
        ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-1 col-md-offset-5 captcha-refresh">
            <button type="button" name="request" class="btn btn-primary pull-right" onclick="gibdd.check();"><?=\Yii::t('app','Request')?></button>
        </div>
        <div class="col-md-1 captcha-refresh">
            <button type="button" name="next" class="btn btn-primary pull-right" onclick="webtools.nextStep('gibdd')"><?=\Yii::t('app','Next')?></button>
        </div>
    </div>
    <div class="row" id="progress">
        <div class="col-md-2"></div>
        <div class="col-md-2"><img src="img/progress.gif" width="150px"/></div>
        <div class="col-md-3"></div>
    </div>
    <br/>
    <div class="row" id="sn"><div class="col-md-3 text-right result-item-name"><?=\Yii::t('app','Serial-Number')?>:</div><div class="col-md-4 result-item-value"></div></div>
    <div class="row" id="rdate"><div class="col-md-3 text-right result-item-name"><?=\Yii::t('app','Register date')?>:</div><div class="col-md-4 result-item-value"></div></div>
    <div class="row" id="cat"><div class="col-md-3 text-right result-item-name"><?=\Yii::t('app','Category')?>:</div><div class="col-md-4 result-item-value"></div></div>
    <div class="row" id="bdate"><div class="col-md-3 text-right result-item-name"><?=\Yii::t('app','Birthday')?>:</div><div class="col-md-4 result-item-value"></div></div>
    <div class="row" id="cdate"><div class="col-md-3 text-right result-item-name"><?=\Yii::t('app','Change date')?>:</div><div class="col-md-4 result-item-value"></div></div>
    <div class="row" id="ddate"><div class="col-md-3 text-right result-item-name"><?=\Yii::t('app', 'Disabled date')?>:</div><div class="col-md-4 result-item-value"></div></div>
    <div class="row" id="status"><div class="col-md-3 text-right result-item-name"><?=\Yii::t('app', 'Request status')?>:</div><div class="col-md-4 result-item-value"></div></div>
</div>
