<?php
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Button;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\rbac\Item;

/**
 * @var \yii\web\View $this
 * @var \auth\components\ItemDataProvider $dataProvider
 */
?>
<div class="row">
    <div class="col-md-12">
        <h1><?= Yii::t('auth.main', $dataProvider->type == Item::TYPE_ROLE ? 'Roles' : 'Permissions') ?></h1>
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
                    'attribute' => 'description',
                    'format' => 'raw',
                    'header' => Yii::t('auth.main', 'Description'),
                    'value' => function ($data) {
                        return Html::a($data->description, ['view', 'name' => $data->name, 'type' => $data->type]);
                    },
                ],
                [
                    'attribute' => 'name',
                    'header' => Yii::t('auth.main', 'System name'),
                    'contentOptions' => ['class' => 'col-md-2'],
                ],
                [
                    'format' => 'raw',
                    'contentOptions' => ['class' => 'col-md-1 text-right'],
                    'value' => function ($data) {
                        return implode(' ', [
                            Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['view', 'name' => $data->name, 'type' => $data->type], [
                                'class' => 'btn btn-link btn-xs',
                                'title' => Yii::t('auth.main', 'View'),
                            ]),
                            Html::a('<span class="glyphicon glyphicon-edit"></span>', ['update', 'name' => $data->name, 'type' => $data->type], [
                                'class' => 'btn btn-link btn-xs',
                                'title' => Yii::t('auth.main', 'Edit'),
                            ]),
                            Html::a('<span class="glyphicon glyphicon-remove"></span>', ['delete', 'name' => $data->name, 'type' => $data->type], [
                                'class' => 'btn btn-link btn-xs',
                                'title' => Yii::t('auth.main', 'Delete'),
                            ])
                        ]);
                    },
                ],
            ],
        ]) ?>
    </div>
</div>

<div class="row">
    <div class="col-md-12 text-center">
        <?= Html::a(Yii::t('auth.main', 'Add'), ['create'], [
            'class' => 'btn btn-success',
        ]) ?>
    </div>
</div>

<?php if (!empty($this->context->actionsMap)) { ?>
    <hr/>
    <?php foreach ($this->context->actionsMap as $controller => $actions) { ?>
        <table class="table table-hover">
            <thead>
            <tr>
                <th colspan="2">
                    <?= Yii::t('auth.main', 'Rights based on {controller} actions', ['controller' => $controller]) ?>
                </th>
            </tr>
            <tr>
                <th class="col-md-5"><?= Yii::t('auth.main', 'System name') ?></th>
                <th class="col-md-7"><?= Yii::t('auth.main', 'Description') ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($actions as $name => $description) { ?>
                <tr>
                    <td><?= $name ?></td>
                    <td>
                        <?php if ($permission = Yii::$app->authManager->getPermission($name)) { ?>
                            <?= $permission->description ?>
                        <?php } else { ?>
                            <?php $form = ActiveForm::begin(['layout' => 'inline']) ?>

                            <?= $form->field($model, 'name', ['enableLabel' => false])->hiddenInput(['value' => $name]) ?>
                            <?= $form->field($model, 'description', ['enableLabel' => false, 'options' => ['class' => 'form-group']])->input('text', ['class' => 'form-control col-md-8 input-sm', 'value' => $description]) ?>
                            <?= Button::widget(['label' => Yii::t('auth.main', 'Create'), 'options' => ['class' => 'btn-primary btn-sm']]) ?>

                            <?php ActiveForm::end() ?>
                        <?php } // if ?>
                    </td>
                </tr>
            <?php } // if ?>
            </tbody>
        </table>
    <?php } // if ?>
<?php } // ?>
