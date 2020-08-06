<?php
use yii\widgets\Pjax;

$this->title = \Yii::t('app','Documents list');
?>
<div>
    <?php
    Pjax::begin(['id' => 'doc_list', 'timeout' => false, 'enablePushState' => false, 'clientOptions' => ['method' => 'POST']]);
    echo \yii\grid\GridView::widget([
        'dataProvider' => $allDocs,
        'layout' => "{items}<div align='right'>{pager}</div>",
//        'rowOptions' => function ($model, $key, $index, $grid) {
//            return [
//                'class' => $index&1 ? 'bg-attr-item-one':'bg-attr-item-two',
//            ];
//        },
        'columns' => [
            [
                'format' => 'ntext',
                'attribute'=>'uid',
                'label'=>'№',
            ],
            [
                'format' => 'ntext',
                'attribute'=>'rdate',
                'label'=>\Yii::t('app','Data creation'),
                'headerOptions' => ['style' => 'width:40%'],
            ],
            [
                'format' => 'ntext',
                'attribute'=>'email',
                'label'=>\Yii::t('app','User email'),
            ],
            [
                'label' => \Yii::t('app','Action'),
                'format' => 'raw',
                'headerOptions' => ['style' => 'width:5%'],
                'value' => function($data){
                    return '<div class="doclist-tools" onclick="doclist.preview('.$data['uid'].')">Скачать</div>';
                }
            ],
        ],
    ]);
    Pjax::end();
    ?>
</div>
