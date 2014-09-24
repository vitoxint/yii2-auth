<?php
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Button;
use yii\helpers\Html;

/**
 * @var \yii\web\View $this
 */
?>
<div class="row">
    <div class="col-md-12">
        <h1><?= Yii::t('auth.main', 'Create') ?></h1>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <?= $this->render('../menu/default') ?>
    </div>
</div>

<hr/>

<div class="row">
    <div class="col-md-4 col-md-offset-4">
        <?php $form = ActiveForm::begin() ?>

        <?= $form->field($model, 'name') ?>
        <?= $form->field($model, 'description')->textarea() ?>

        <div class="form-group text-center">
            <?= Button::widget(['label' => Yii::t('auth.main', 'Create'), 'options' => ['class' => 'btn-primary']]) ?>
            <?= Html::a(Yii::t('auth.main', 'Cancel'), ['index'], ['class' => 'btn btn-default']) ?>
        </div>

        <?php ActiveForm::end() ?>
    </div>
</div>
