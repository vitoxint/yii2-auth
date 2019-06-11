<?php
/**
 * @var \yii\web\View $this
 * @var string $style
 */

use yii\bootstrap\Nav;
$this->title= "Autentificación"
?>

<?= Nav::widget([
    'items' => [
        ['label' => Yii::t('auth.main', 'Assignments'), 'url' => ['assignment/index']],
        ['label' => Yii::t('auth.main', 'Roles'), 'url' => ['role/index']],
        ['label' => Yii::t('auth.main', 'Permissions'), 'url' => ['permission/index']],
    ],
    'options' => [
        'class' => 'nav-tabs',
    ],
]) ?>
