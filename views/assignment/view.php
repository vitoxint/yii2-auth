<?php
/* @var $this AssignmentController */
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $model User */
/* @var $authItemDp AuthItemDataProvider */
/* @var $formModel AddAuthItemForm */
/* @var $form TbActiveForm */
/* @var $assignmentOptions array */

/*$this->breadcrumbs = array(
    Yii::t('AuthModule.main', 'Assignments') => array('index'),
    CHtml::value($model, $this->module->userNameColumn),
);*/
?>

<h1><?php echo Html::encode($model->{$this->context->module->userNameColumn}); ?>
    <small><?php echo Yii::t('AuthModule.main', 'Assignments'); ?></small>
</h1>

<div class="row">

    <div class="span6">

        <h3>
            <?php echo Yii::t('AuthModule.main', 'Permissions'); ?>
            <small><?php echo Yii::t('AuthModule.main', 'Items assigned to this user'); ?></small>
        </h3>

        <?= GridView::widget(
            [
                //'type' => 'striped condensed hover',
                'dataProvider' => $authItemDp,
                'emptyText' => Yii::t('AuthModule.main', 'This user does not have any assignments.'),
                //'hideHeader' => true,
                'layout' => "{items}",
                'columns' => [
                    [
                        'class' => 'auth\widgets\AuthItemDescriptionColumn',
                        'active' => true,
                    ],
                    [
                        'class' => 'auth\widgets\AuthItemTypeColumn',
                        'active' => true,
                    ],
                    [
                        'class' => 'auth\widgets\AuthAssignmentRevokeColumn',
                        'userId' => $model->{$this->context->module->userIdColumn},
                    ],
                ],
            ]
        ); ?>

        <?php if (!empty($assignmentOptions)): ?>

            <h4><?php echo Yii::t('AuthModule.main', 'Assign permission'); ?></h4>

            <?php $form = $this->beginWidget(
                'bootstrap.widgets.TbActiveForm',
                [
                    'layout' => TbHtml::FORM_LAYOUT_INLINE,
                ]
            ); ?>

            <?php echo $form->dropDownList($formModel, 'items', $assignmentOptions, ['label' => false]); ?>

            <?php echo TbHtml::submitButton(
                Yii::t('AuthModule.main', 'Assign'),
                [
                    'color' => TbHtml::BUTTON_COLOR_PRIMARY,
                ]
            ); ?>

            <?php $this->endWidget(); ?>

        <?php endif; ?>

    </div>

</div>