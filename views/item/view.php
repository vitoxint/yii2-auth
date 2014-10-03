<?php
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Button;
use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * @var \yii\web\View $this
 * @var \yii\rbac\Role|\yii\rbac\Permission $item
 * @var \auth\models\ChildForm $model
 * @var \yii\rbac\Role[]|\yii\rbac\Permission[] $children
 * @var \yii\rbac\Role[]|\yii\rbac\Permission[] $parents
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
<hr/>
<div class="row">
    <div class="col-md-6">
        <?= $this->render('_parents', ['data' => $parents, 'item' => $item]) ?>
    </div>
    <div class="col-md-6">
        <?= $this->render('_children', ['data' => $children, 'item' => $item]) ?>

        <?php if (!empty($childrenOptions)) { ?>
            <hr>
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
        <?php } // if ?>
    </div>
</div>
