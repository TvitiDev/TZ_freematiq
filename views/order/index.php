<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $searchModel app\models\Order */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Счета';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="orders-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

<?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
        'columns' => [

            'id',
            // 'created',
            // 'updated',
            [
                'class' => 'yii\grid\DataColumn',
                'attribute' => 'created',
                'format' => 'text',
                'label' => 'Создан',
            ],
            [
                'class' => 'yii\grid\DataColumn',
                'attribute' => 'updated',
                'format' => 'text',
                'label' => 'Обновлен',
            ],
            [
                'class' => 'yii\grid\DataColumn',
                'attribute' => 'balance',
                'format' => 'text',
                'label' => 'Баланс',
            ],

            // ['class' => 'yii\grid\ActionColumn'],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'buttons' => [
                    'view' => function ($url, $model, $key) {
                        return Html::a('<span title="Просмотр" class="glyphicon glyphicon-eye-open"></span>',  Url::to(['order/view', 'id' => $model['id']]), ['data-pjax' => 0]);
                    },
                ],
            ]
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
