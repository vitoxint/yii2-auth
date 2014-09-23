<?php
use yii\bootstrap\Button;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this OperationController|TaskController|RoleController */
/* @var $dataProvider AuthItemDataProvider */

/*$this->breadcrumbs = array(
    $this->capitalize($this->getTypeText(true)),
);*/
?>

<h1><?= $this->context->getTypeText(true) ?></h1>

<?= Button::widget([
        'label' => Yii::t('AuthModule.main', 'Add {type}', ['{type}' => $this->context->getTypeText()]),
        'options' => [
            //'color' => TbHtml::BUTTON_COLOR_PRIMARY,
            'url' => 'create',
        ]
    ]);
?>

<?= GridView::widget(
    [
        //'type' => 'striped hover',
        'dataProvider' => $dataProvider,
        'emptyText' => Yii::t('AuthModule.main', 'No {type} found.', ['{type}' => $this->context->getTypeText(true)]),
        'layout' => "{items}\n{pager}",
        'columns' => [
            [
                'attribute' => 'name',
                'format' => 'raw',
                'header' => Yii::t('AuthModule.main', 'System name'),
                'options' => ['class' => 'item-name-column'],
                'value' => function ($model) {
                        return Html::a($model->name, array('view', 'name' => $model->name));
                    },
            ],
            [
                'attribute' => 'description',
                'header' => Yii::t('AuthModule.main', 'Description'),
                'options' => ['class' => 'item-description-column'],
            ],
            /*[
                'class' => 'bootstrap.widgets.TbButtonColumn',
                'viewButtonLabel' => Yii::t('AuthModule.main', 'View'),
                'viewButtonUrl' => "Yii::$app->controller->createUrl('view', array('name'=>\$data->name))",
                'updateButtonLabel' => Yii::t('AuthModule.main', 'Edit'),
                'updateButtonUrl' => "Yii::$app->controller->createUrl('update', array('name'=>\$data->name))",
                'deleteButtonLabel' => Yii::t('AuthModule.main', 'Delete'),
                'deleteButtonUrl' => "Yii::$app->controller->createUrl('delete', array('name'=>\$data->name))",
                'deleteConfirmation' => Yii::t('AuthModule.main', 'Are you sure you want to delete this item?'),
            ],*/
        ],
    ]
); ?>
