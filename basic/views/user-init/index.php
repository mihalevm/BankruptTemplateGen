<?php

$this->title = \Yii::t('app','Registration');

?>
<div class="site-index">
    <div class="row">
        <div class="col-md-3"><?=\Yii::t('app','Input email')?></div>
        <div class="col-md-4" id="container">
            <input autofocus onpaste="userinit.search()" onchange="userinit.search()" onkeyup="userinit.search()" name="email" type="text" class="form-control" placeholder="<?=\Yii::t('app','Email')?>" data-sid="<?=$sid?>"/>
        </div>
    </div>
    <div class="row">
        <div class="col-md-1 col-md-offset-6 captcha-refresh">
            <button type="button" class="btn btn-primary pull-right" onclick="userinit.start();"><?=\Yii::t('app','Start')?></button>
        </div>
    </div>
    <div class="row">
        <div class="col-md-7">
            <fieldset class="saved-session-holder">
                <legend>Для продолжения выберите сохраненную сессию</legend>
                <div id="saved_session"><div>
            </fieldset>
        </div>
    </div>
</div>
