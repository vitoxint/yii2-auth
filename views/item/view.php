<?php
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Button;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\rbac\Item;
use yii\widgets\DetailView;

/**
 * @var \yii\web\View $this
 * @var \yii\rbac\Role|\yii\rbac\Permission $item
 * @var \auth\models\ChildForm $model
 * @var \yii\rbac\Role[]|\yii\rbac\Permission[] $children
 */
?>

<div class="row">
    <div class="col-md-12">
        <h1><?= Html::encode($item->description) ?></h1>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <?= $this->render('../menu/default') ?>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <?= DetailView::widget([
            'model' => $item,
            'options' => ['class' => 'table table-hover'],
            'template' => '<tr><th class="col-md-2">{label}</th><td>{value}</td></tr>',
            'attributes' => [
                ['attribute' => 'name', 'label' => Yii::t('auth.main', 'System name')],
                ['attribute' => 'description', 'label' => Yii::t('auth.main', 'Description')],
            ],
        ]) ?>
    </div>
</div>

<div class="row text-center">
    <div class="col-md-12">
        <?= Html::a(Yii::t('auth.main', 'Edit'), ['update', 'name' => $item->name, 'type' => $item->type], ['class' => 'btn btn-info']) ?>
        <?= Html::a(Yii::t('auth.main', 'Delete'), ['delete', 'name' => $item->name], ['class' => 'btn btn-danger']) ?>
    </div>
</div>

<?php if (!empty($children)) { ?>

    <hr/>

    <h3><?= Yii::t('auth.main', 'Descendants') ?></h3>

    <div class="row">
        <div class="col-md-12">
            <?= GridView::widget([
                'dataProvider' => new ArrayDataProvider(['allModels' => $children]),
                'tableOptions' => ['class' => 'table table-hover'],
                'layout' => '{items}',
                'columns' => [
                    [
                        'label' => Yii::t('auth.main', 'Description'),
                        'format' => 'raw',
                        'contentOptions' => ['class' => 'col-md-2'],
                        'value' => function ($data) {
                            return Html::a($data->description, ['view', 'name' => $data->name, 'type' => $data->type]);
                        }
                    ],
                    [
                        'label' => Yii::t('auth.main', 'Type'),
                        'format' => 'raw',
                        'value' => function ($data) {
                            return $data->type == Item::TYPE_ROLE
                                ? '<span class="label label-primary">' . Yii::t('auth.main', 'Role') . '</span>'
                                : '<span class="label label-default">' . Yii::t('auth.main', 'Permission') . '</span>';
                        }
                    ],
                    [
                        'format' => 'raw',
                        'contentOptions' => ['class' => 'col-md-1 text-right'],
                        'value' => function ($data) use ($item) {
                            if (Yii::$app->authManager->hasChild($item, $data)) {
                                return Html::a('<span class="glyphicon glyphicon-trash"></span>', ['remove-child', 'parentName' => $item->name, 'childName' => $data->name], [
                                    'class' => 'btn btn-link btn-xs',
                                    'title' => Yii::t('auth.main', 'Remove'),
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

<?php } // if ?>

<?php if (!empty($childrenOptions)) { ?>

    <hr/>

    <div class="row">
        <div class="col-md-4 col-md-offset-4">

            <h3><?= Yii::t('auth.main', 'Add child') ?></h3>

            <?php $form = ActiveForm::begin() ?>

            <?= $form->field($model, 'items[]', ['enableLabel' => false])->dropDownList($childrenOptions, [
                'multiple' => 'multiple',
                'size' => 10,
            ]) ?>

            <div class="form-group text-center">
                <?= Button::widget(['label' => Yii::t('auth.main', 'Add'), 'options' => ['class' => 'btn-success']]) ?>
            </div>

            <?php ActiveForm::end() ?>
        </div>
    </div>

<?php } // if ?>
