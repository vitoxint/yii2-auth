<?php
/**
 * Ancestors list.
 * 
 * @var \yii\rbac\Role|\yii\rbac\Permission $item
 * @var \yii\rbac\Role[] $data
 */
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\rbac\Item;

?>
<h3><?= Yii::t('auth.main', 'Roles') ?></h3>
<?= GridView::widget([
    'dataProvider' => new ArrayDataProvider(['allModels' => $data]),
    'tableOptions' => ['class' => 'table table-hover'],
    'layout' => '{items}',
    'columns' => [
        [
            'label' => Yii::t('auth.main', 'Description'),
            'format' => 'raw',
            'contentOptions' => ['class' => 'col-md-2'],
            'value' => function ($data) {
                    return Html::a($data->description, ['role/view', 'name' => $data->name, 'type' => $data->type]);
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
                    if (Yii::$app->authManager->hasChild($data, $item)) {
                        return Html::a('<span class="glyphicon glyphicon-remove"></span>', ['remove-child', 'parentName' => $data->name, 'childName' => $item->name], [
                                'class' => 'btn btn-link btn-xs',
                                'title' => Yii::t('auth.main', 'Revoke'),
                            ]);
                    } else {
                        return false;
                    }
                }
        ],
    ],
]);
