<?php
use yii\grid\GridView;
use yii\helpers\Html;
use yii\rbac\Item;

/**
 * @var \yii\web\View $this
 * @var \sb\modules\auth\components\ItemDataProvider $dataProvider
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

<?php /* if (Yii::$app->getController()->id == 'operation') { ?>


    <p class="alert alert-info">Если действия из списка ниже не будут добавлено в качестве операции, то они будут доступны только пользователю Admin.</p>

    <table class="table table-hover">
        <?php
        $classes = array_unique($this->module->authControllers);
        foreach($classes as $class) { ?>

            <tr>
                <th colspan="3">Операция на основе действий <b><?= $class ?></b></th>
            </tr>
            <tr>
                <td><b>Идентификатор действия</b></td>
                <td><b>Название (label)</b></td>

            </tr>

            <?php
            $obj = new $class(strtolower(str_replace('Controller', '', $class)));
            $data = $obj->actions();

            foreach ($data as $key => $value) {
                $operation_name = $obj->getRuleName($key);
                $label = isset($value['params']['label']) ? $value['params']['label'] : '';
                ?>
                <tr>
                    <td><?= $operation_name ?></td>
                    <!--td><?= $label ?></td-->
                    <td>
                        <?php
                        if(!array_key_exists($operation_name, Yii::$app->authManager->operations)) {
                            $form=$this->beginWidget('CActiveForm', array(
                                'enableAjaxValidation'=>false,
                                'htmlOptions'=>array(
                                    'class'=>'form-inline',
                                    'style'=>'margin: 0;'
                                ),
                                'action' => '/auth/operation/create/',
                            )); ?>
                            <?php echo $form->hiddenField($model, 'type'); ?>

                            <?php
                            echo $form->hiddenField(
                                $model,
                                'name',
                                array(
                                    'readonly' => true,
                                    'value' => $operation_name,
                                )
                            );
                            ?>

                            <?php
                            echo $form->textField(
                                $model,
                                'description',
                                array(
                                    'value' => $label,
                                )
                            );
                            ?>

                            <?php echo Html::submitButton('+', array('class' => 'btn btn-success')); ?>

                            </div>

                            <?php
                            $this->endWidget();
                        } else {
                            $name = Yii::$app->authManager->operations[$operation_name]->description;
                            echo "<span class='b-has-icon-checked'> {$name}</span>";
                        }
                        ?>
                    </td>
                </tr>

            <?php } ?>

        <?php } ?>
    </table>

<?php } */
?>
