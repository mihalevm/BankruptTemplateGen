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
                'value'   => $dcard,
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
                                    'value'       => date($rdate ? 'd.m.Y' : '01.m.Y', strtotime($rdate ? $rdate : date('Y-m-d'))),
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
    <br/>
    <?php
    if ( property_exists($rdata, 'doc') ) {
        echo '<div class="row"><div class="col-md-3 text-right result-item-name">'.\Yii::t('app','Category').':</div><div class="col-md-4 result-item-value">'.$rdata->doc->cat.'</div></div>';
        echo '<div class="row"><div class="col-md-3 text-right result-item-name">'.\Yii::t('app','Birthday').':</div><div class="col-md-4 result-item-value">'.$rdata->doc->bdate.'</div></div>';
        echo '<div class="row"><div class="col-md-3 text-right result-item-name">'.\Yii::t('app','Change date').':</div><div class="col-md-4 result-item-value">'.$rdata->doc->srok.'</div></div>';
        if (strlen($rdata->doc->wanted)) {
            echo '<div class="row"><div class="col-md-3 text-right result-item-name">' . \Yii::t('app', 'Disabled date') . ':</div><div class="col-md-4 result-item-value">' . $rdata->doc->wanted . '</div></div>';
        }
    }
    ?>
</div>
