<?php

use kartik\date\DatePicker;

/* @var $this yii\web\View */
//Федосенко Виктор Анатольевич 15.07.1977

$this->title = \Yii::t('app','Check FSSP');
?>
<div class="site-index">
    <div class="row">
        <div class="col-md-2">Фамилия</div>
        <div class="col-md-4">
            <input name="last_name" value="<?=$sureName?>" type="text" class="form-control" placeholder="<?=\Yii::t('app','Surname')?>" autofocus />
        </div>
    </div>
    <div class="row">
        <div class="col-md-2">Имя</div>
        <div class="col-md-4">
            <input name="first_name" value="<?=$firstName?>" class="form-control" type="text" placeholder="<?=\Yii::t('app','Name')?>"/>
        </div>
    </div>
    <div class="row">
        <div class="col-md-2">Отчество</div>
        <div class="col-md-4">
            <input class="form-control" value="<?=$secondName?>" name="patronymic" type="text" placeholder="<?=\Yii::t('app','Patronymic')?>" data-bdate="<?=$birthDate?>" data-summ="<?=$summ?>"/>
        </div>
    </div>
    <div class="row">
        <div class="col-md-2">Дата рождения</div>
        <div class="col-md-4">
<?php
            echo DatePicker::widget([
                'model' => $model,
                'attribute' => 'date',
                'options' => ['name' => 'date', 'placeholder' => 'Дата рождения','value' => date('01.m.Y', strtotime(date('Y-m-d'))),],
                'type' => DatePicker::TYPE_COMPONENT_APPEND,
                'removeButton' => false,
                'pluginOptions' => [
                    'format' => 'dd.mm.yyyy',
                    'orientation' => 'bottom left',
                    'autoclose'=>true,
                    'todayHighlight' => true,
                ]
            ]);
?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-2">Код с картинки</div>
        <div class="col-md-4">
            <input class="form-control" name="captcha_str" placeholder="<?=\Yii::t('app','Captcha')?>" type="text" data-sid="">
        </div>
    </div>
    <div class="row captcha-size">
        <div class="col-md-3 col-md-offset-2"><img id="captcha" alt="<?=\Yii::t('app','Captcha request')?>" src=""/></div>
        <div class="col-md-1 captcha-refresh">
            <button type="button" class="btn btn-primary glyphicon glyphicon-refresh pull-right" onclick="grab.getcapcha();" title="<?=\Yii::t('app','Refresh captcha')?>"></button>
        </div>
    </div>
    <div class="row">
        <div class="col-md-1 col-md-offset-4 captcha-refresh">
            <button type="button" name="request" class="btn btn-primary pull-right disabled" disabled onclick="grab.send_grab();"><?=\Yii::t('app','Request')?></button>
        </div>
        <div class="col-md-1 captcha-refresh">
            <button type="button" name="next" class="btn btn-primary pull-right" onclick="webtools.nextStep('grab')"><?=\Yii::t('app','Next')?></button>
        </div>

    </div>
</div>
<div class="center-result"><span id="log_result"></span></div>
