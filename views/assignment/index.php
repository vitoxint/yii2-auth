<?php
use yii\grid\GridView;
use yii\helpers\Html;

/**
 * @var \yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 */
?>
<div class="row">
    <div class="col-md-12">
        <h1><?= Yii::t('auth.main', 'Assignments') ?></h1>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <?= $this->render('../menu/default') ?>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'layout' => "{items}\n{pager}",
            'tableOptions' => ['class' => 'table table-hover'],
            'columns' => [
                [
                    'header' => Yii::t('auth.main', 'User'),
                    'format' => 'raw',
                    'contentOptions' => ['class' => 'col-md-2'],
                    'value' => function ($data) {
                        return Html::a($data->{$this->context->module->userNameColumn}, ['view', 'id' => $data->{$this->context->module->userIdColumn}]);
                    },
                ],
                [
                    'header' => Yii::t('auth.main', 'Assigned items'),
                    'format' => 'raw',
                    'value' => function ($data) {
                        $list = [];
                        foreach (Yii::$app->authManager->getRolesByUser($data->{$this->context->module->userIdColumn}) as $role) {
                            $list[] = '<span class="label label-primary">' . $role->description . '</span>';
                        }
                        foreach (Yii::$app->authManager->getPermissionsByUser($data->{$this->context->module->userIdColumn}) as $permission) {
                            $list[] = '<span class="label label-default">' . $permission->description . '</span>';
                        }
                        return implode(' ', $list);
                    },
                ],
                [
                    'format' => 'raw',
                    'contentOptions' => ['class' => 'col-md-1 text-right'],
                    'value' => function ($data) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['view', 'id' => $data->{$this->context->module->userIdColumn}], [
                            'class' => 'btn btn-link btn-xs',
                            'title' => Yii::t('auth.main', 'View'),
                        ]);
                    },
                ],
            ],
        ]) ?>
    </div>
</div>
