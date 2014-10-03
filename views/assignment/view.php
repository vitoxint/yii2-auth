<?php
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Button;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\rbac\Item;

/**
 * @var \yii\db\ActiveRecord $user
 * @var auth\models\ChildForm $model
 * @var yii\rbac\Role[] $assignments
 * @var array $assignmentOptions
 */
?>

<div class="row">
    <div class="col-md-12">
        <h1><?= Yii::t('auth.main', 'Assignments') . ': ' . Html::encode($user->{$this->context->module->userNameColumn}) ?></h1>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <?= $this->render('../menu/default') ?>
    </div>
</div>

<div class="row">
    <div class="col-md-12">

        <?= GridView::widget([
            'dataProvider' => new ArrayDataProvider(['allModels' => $assignments]),
            'tableOptions' => ['class' => 'table table-hover'],
            'layout' => '{items}',
            'columns' => [
                [
                    'label' => Yii::t('auth.main', 'Description'),
                    'format' => 'raw',
                    'value' => function ($data) {
                        return Html::a($data->description, ['role/view', 'name' => $data->name, 'type' => $data->type]);
                    }
                ],
                [
                    'label' => Yii::t('auth.main', 'Type'),
                    'format' => 'raw',
                    'contentOptions' => ['class' => 'col-md-2'],
                    'value' => function ($data) {
                        return $data->type == Item::TYPE_ROLE
                            ? '<span class="label label-primary">' . Yii::t('auth.main', 'Role') . '</span>'
                            : '<span class="label label-default">' . Yii::t('auth.main', 'Permission') . '</span>';
                    }
                ],
                [
                    'format' => 'raw',
                    'contentOptions' => ['class' => 'col-md-1 text-right'],
                    'value' => function ($data) use ($user) {
                        if (Yii::$app->authManager->getAssignment($data->name, $user->{$user::primaryKey()[0]})) {
                            return Html::a('<span class="glyphicon glyphicon-remove"></span>', ['revoke', 'user' => $user->{$user::primaryKey()[0]}, 'name' => $data->name], [
                                'class' => 'btn btn-link btn-xs',
                                'title' => Yii::t('auth.main', 'Revoke'),
                            ]);
                        } else {
                            return false;
                        }
                    }
                ],
            ],
        ]) ?>

    </div>
</div>

<?php if (!empty($assignmentOptions)) { ?>

    <hr/>

    <div class="row">
        <div class="col-md-4 col-md-offset-4">

            <h3><?= Yii::t('auth.main', 'Assign permission') ?></h3>

            <?php $form = ActiveForm::begin() ?>

            <?= $form->field($model, 'items[]', ['enableLabel' => false])->dropDownList($assignmentOptions, [
                'multiple' => 'multiple',
                'size' => 10,
            ]) ?>

            <div class="form-group text-center">
                <?= Button::widget(['label' => Yii::t('auth.main', 'Assign'), 'options' => ['class' => 'btn-success']]) ?>
            </div>

            <?php ActiveForm::end() ?>

        </div>
    </div>

<?php } // if ?>
