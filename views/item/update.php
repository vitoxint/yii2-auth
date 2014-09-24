<?php
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Button;
use yii\helpers\Html;

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

<hr/>

<div class="row">
    <div class="col-md-4 col-md-offset-4">
        <?php $form = ActiveForm::begin() ?>

        <?= $form->field($model, 'name')->input('text', ['disabled' => 'disabled', 'title' => Yii::t('auth.main', 'System name cannot be changed after creation.')]) ?>
        <?= $form->field($model, 'description')->textarea() ?>

        <div class="form-group text-center">
            <?= Button::widget(['label' => Yii::t('auth.main', 'Save'), 'options' => ['class' => 'btn-primary']]) ?>
            <?= Html::a(Yii::t('auth.main', 'Cancel'), ['index'], ['class' => 'btn btn-default']) ?>
        </div>
    </div>

    <?php ActiveForm::end() ?>
</div>

