<?php
/**
 * AuthItemController class file.
 * @author Christoffer Niska <ChristofferNiska@gmail.com>
 * @copyright Copyright &copy; Christoffer Niska 2012-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package auth.controllers
 */

namespace auth\controllers;

use auth\components\AuthController;
use auth\components\AuthItemDataProvider;
use auth\models\AuthItemForm;
use sbuilder\helpers\Dev;
use Yii;
use yii\rbac\PhpManager;
use yii\web\HttpException;

/**
 * Base controller for authorization item related actions.
 */
abstract class AuthItemController extends AuthController
{
    /**
     * @var integer the item type (0=operation, 1=task, 2=role).
     */
    public $type;

    /**
     * Displays a list of items of the given type.
     */
    public function actionIndex()
    {
        $dataProvider = new AuthItemDataProvider();
        $dataProvider->type = $this->type;

        return $this->render(
            'index',
            [
                'dataProvider' => $dataProvider,
            ]
        );
    }

    /**
     * Displays a form for creating a new item of the given type.
     */
    public function actionCreate()
    {
        $model = new AuthItemForm('create');

        if (isset($_POST['AuthItemForm'])) {
            $model->attributes = $_POST['AuthItemForm'];
            if ($model->validate()) {
                /* @var $am \yii\rbac\BaseManager|AuthBehavior */
                $am = Yii::$app->getAuthManager();

                if (($item = $am->getAuthItem($model->name)) === null) {
                    $item = $am->createAuthItem($model->name, $model->type, $model->description);
                    if ($am instanceof PhpManager) {
                        $am->save();
                    }
                }

                $this->redirect(['view', 'name' => $item->name]);
            }
        }

        $model->type = $this->type;

        return $this->render(
            'create',
            [
                'model' => $model,
            ]
        );
    }

    /**
     * Displays a form for updating the item with the given name.
     * @param string $name name of the item.
     * @throws HttpException if the authorization item is not found.
     */
    public function actionUpdate($name)
    {
        /* @var $am \yii\rbac\BaseManager|AuthBehavior */
        $am = Yii::$app->getAuthManager();

        $item = $am->getAuthItem($name);

        if ($item === null) {
            throw new HttpException(404, Yii::t('AuthModule.main', 'Page not found.'));
        }

        $model = new AuthItemForm('update');

        if (isset($_POST['AuthItemForm'])) {
            $model->attributes = $_POST['AuthItemForm'];
            if ($model->validate()) {
                $item->description = $model->description;

                $am->saveAuthItem($item);
                if ($am instanceof PhpManager) {
                    $am->save();
                }

                $this->redirect(['index']);
            }
        }

        $model->name = $name;
        $model->description = $item->description;
        $model->type = $item->type;

        return $this->render(
            'update',
            [
                'item' => $item,
                'model' => $model,
            ]
        );
    }

    /**
     * Displays the item with the given name.
     * @param string $name name of the item.
     */
    public function actionView($name)
    {
        $formModel = new AddAuthItemForm();

        /* @var $am \yii\rbac\BaseManager|AuthBehavior */
        $am = Yii::$app->getAuthManager();

        if (isset($_POST['AddAuthItemForm'])) {
            $formModel->attributes = $_POST['AddAuthItemForm'];
            if ($formModel->validate()) {
                if (!$am->hasItemChild($name, $formModel->items)) {
                    $am->addItemChild($name, $formModel->items);
                    if ($am instanceof PhpManager) {
                        $am->save();
                    }
                }
            }
        }

        $item = $am->getAuthItem($name);

        $dpConfig = [
            'pagination' => false,
            'sort' => ['defaultOrder' => 'depth asc'],
        ];

        $ancestors = $am->getAncestors($name);
        $ancestorDp = new PermissionDataProvider(array_values($ancestors), $dpConfig);

        $descendants = $am->getDescendants($name);
        $descendantDp = new PermissionDataProvider(array_values($descendants), $dpConfig);

        $childOptions = $this->getItemChildOptions($item->name);
        if (!empty($childOptions)) {
            $childOptions = array_merge(['' => Yii::t('AuthModule.main', 'Select item') . ' ...'], $childOptions);
        }

        return $this->render(
            'view',
            [
                'item' => $item,
                'ancestorDp' => $ancestorDp,
                'descendantDp' => $descendantDp,
                'formModel' => $formModel,
                'childOptions' => $childOptions,
            ]
        );
    }

    /**
     * Deletes the item with the given name.
     * @throws HttpException if the item does not exist or if the request is invalid.
     */
    public function actionDelete()
    {
        if (isset($_GET['name'])) {
            $name = $_GET['name'];

            /* @var $am \yii\rbac\BaseManager|AuthBehavior */
            $am = Yii::$app->getAuthManager();

            $item = $am->getAuthItem($name);
            if ($item instanceof CAuthItem) {
                $am->removeAuthItem($name);
                if ($am instanceof PhpManager) {
                    $am->save();
                }

                if (!isset($_POST['ajax'])) {
                    $this->redirect(['index']);
                }
            } else {
                throw new HttpException(404, Yii::t('AuthModule.main', 'Item does not exist.'));
            }
        } else {
            throw new HttpException(400, Yii::t('AuthModule.main', 'Invalid request.'));
        }
    }

    /**
     * Removes the parent from the item with the given name.
     * @param string $itemName name of the item.
     * @param string $parentName name of the parent.
     */
    public function actionRemoveParent($itemName, $parentName)
    {
        /* @var $am \yii\rbac\BaseManager|AuthBehavior */
        $am = Yii::$app->getAuthManager();

        if ($am->hasItemChild($parentName, $itemName)) {
            $am->removeItemChild($parentName, $itemName);
            if ($am instanceof PhpManager) {
                $am->save();
            }
        }

        $this->redirect(['view', 'name' => $itemName]);
    }

    /**
     * Removes the child from the item with the given name.
     * @param string $itemName name of the item.
     * @param string $childName name of the child.
     */
    public function actionRemoveChild($itemName, $childName)
    {
        /* @var $am \yii\rbac\BaseManager|AuthBehavior */
        $am = Yii::$app->getAuthManager();

        if ($am->hasItemChild($itemName, $childName)) {
            $am->removeItemChild($itemName, $childName);
            if ($am instanceof PhpManager) {
                $am->save();
            }
        }

        $this->redirect(['view', 'name' => $itemName]);
    }

    /**
     * Returns a list of possible children for the item with the given name.
     * @param string $itemName name of the item.
     * @return array the child options.
     */
    protected function getItemChildOptions($itemName)
    {
        $options = [];

        /* @var $am \yii\rbac\BaseManager|AuthBehavior */
        $am = Yii::$app->getAuthManager();

        $item = $am->getAuthItem($itemName);
        if ($item instanceof CAuthItem) {
            $exclude = $am->getAncestors($itemName);
            $exclude[$itemName] = $item;
            $exclude = array_merge($exclude, $item->getChildren());
            $authItems = $am->getAuthItems();
            $validChildTypes = $this->getValidChildTypes();

            foreach ($authItems as $childName => $childItem) {
                if (in_array($childItem->type, $validChildTypes) && !isset($exclude[$childName])) {
                    $options[$this->capitalize(
                        $this->getItemTypeText($childItem->type, true)
                    )][$childName] = $childItem->description;
                }
            }
        }

        return $options;
    }

    /**
     * Returns a list of the valid child types for the given type.
     * @return array the valid types.
     */
    protected function getValidChildTypes()
    {
        $validTypes = [];

        switch ($this->type) {
            case CAuthItem::TYPE_OPERATION:
                break;

            case CAuthItem::TYPE_TASK:
                $validTypes[] = CAuthItem::TYPE_OPERATION;
                break;

            case CAuthItem::TYPE_ROLE:
                $validTypes[] = CAuthItem::TYPE_OPERATION;
                $validTypes[] = CAuthItem::TYPE_TASK;
                break;
        }

        if (!$this->module->strictMode) {
            $validTypes[] = $this->type;
        }

        return $validTypes;
    }

    /**
     * Returns the authorization item type as a string.
     * @param boolean $plural whether to return the name in plural.
     * @return string the text.
     */
    public function getTypeText($plural = false)
    {
        return parent::getItemTypeText($this->type, $plural);
    }

    /**
     * Returns the directory containing view files for this controller.
     * @return string the directory containing the view files for this controller.
     */
    public function getViewPath()
    {
        return $this->module->getViewPath() . DIRECTORY_SEPARATOR . 'authItem';
    }
}