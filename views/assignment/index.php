<?php
/* @var $this AssignmentController */

use yii\data\ActiveDataProvider;
use yii\grid\GridView;

/* @var $dataProvider ActiveDataProvider */

/*
$this->breadcrumbs = array(
    Yii::t('AuthModule.main', 'Assignments'),
);
 */
?>

<h1><?php echo Yii::t('AuthModule.main', 'Assignments'); ?></h1>

<?= GridView::widget(
    [
        'dataProvider' => $dataProvider,
        'emptyText' => Yii::t('AuthModule.main', 'No assignments found.'),
        'layout' => "{items}\n{pager}",
        'columns' => [
            [
                'header' => Yii::t('AuthModule.main', 'User'),
                'class' => 'auth\widgets\AuthAssignmentNameColumn',
            ],
            [
                'header' => Yii::t('AuthModule.main', 'Assigned items'),
                'class' => 'auth\widgets\AuthAssignmentItemsColumn',
            ],
            [
                'class' => 'auth\widgets\AuthAssignmentViewColumn',
            ],
        ],
    ]
); ?>
