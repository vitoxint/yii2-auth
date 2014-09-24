<?php
/**
 * AuthItemController class file.
 * @author Christoffer Niska <ChristofferNiska@gmail.com>
 * @copyright Copyright &copy; Christoffer Niska 2012-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package auth.controllers
 */

namespace sb\modules\auth\components;

use sb\modules\auth\models\ChildForm;
use sb\modules\auth\models\ItemForm;
use Yii;
use yii\helpers\ArrayHelper;
use yii\rbac\Item;
use yii\rbac\Permission;
use yii\rbac\PhpManager;
use yii\rbac\Role;
use yii\web\HttpException;

/**
 * Base controller for authorization item related actions.
 *
 * @package sb\modules\auth\controllers
 */
abstract class ItemController extends AuthController
{
    /**
     * @var integer the item type.
     */
    public $type;

    /**
     * @inheritdoc
     */
    public function getViewPath()
    {
        return $this->module->viewPath . '/item';
    }

    /**
     * Displays a list of items of the given type.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index', [
            'dataProvider' => new ItemDataProvider($this->type),
        ]);
    }

    /**
     * Displays a form for creating a new item of the given type.
     *
     * @return string
     */
    public function actionCreate()
    {
        $model = new ItemForm;
        $model->scenario = 'create';
        $model->type = $this->type;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if (is_null($item = Yii::$app->authManager->{'get' . $this->typeName($model->type)}($model->name))) {
                /** @var Role|Permission $item */
                $item = Yii::$app->authManager->{'create' . $this->typeName($model->type)}($model->name);
                $item->description = $model->description;
                Yii::$app->authManager->add($item);
            }

            $this->redirect(['view', 'name' => $item->name, 'type' => $item->type]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Displays a form for updating the item with the given name.
     *
     * @param string $name Authorization item name.
     * @param integer $type Authorization item type.
     *
     * @return string
     *
     * @throws HttpException if the authorization item is not found.
     */
    public function actionUpdate($name, $type)
    {
        /** @var Role|Permission $item */
        $item = Yii::$app->authManager->{'get' . $this->typeName($type)}($name);

        if (is_null($item)) {
            throw new HttpException(404, Yii::t('auth.main', 'Page not found.'));
        }

        $model = new ItemForm;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $item->description = $model->description;
            Yii::$app->authManager->update($name, $item);
            $this->redirect(['view', 'name' => $item->name, 'type' => $item->type]);
        }

        $model->name = $name;
        $model->description = $item->description;

        return $this->render('update', [
            'item' => $item,
            'model' => $model,
        ]);
    }

    /**
     * Deletes the item with the given name.
     *
     * @param string $name Authorization item name.
     * @param integer $type Authorization item type.
     *
     * @throws HttpException If the item does not exist or if the request is invalid.
     */
    public function actionDelete($name, $type)
    {
        /** @var Role|Permission $item */
        if (!is_null($item = Yii::$app->authManager->{'get' . $this->typeName($type)}($name))) {
            Yii::$app->authManager->remove($item);
            $this->redirect(['index']);
        } else {
            throw new HttpException(404, Yii::t('auth.main', 'Item does not exist.'));
        }
    }

    /**
     * Displays the item with the given name.
     *
     * @param string $name Name of the item.
     * @param integer $type Type of the item.
     *
     * @return string
     */
    public function actionView($name, $type)
    {
        $model = new ChildForm;
        /** @var Role|Permission $item */
        $item = Yii::$app->authManager->{'get' . $this->typeName($type)}($name);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            foreach ($model->items as $childName) {
                // add selected child
                $child = Yii::$app->authManager->getPermission($childName) ?: Yii::$app->authManager->getRole($childName);
                if (!Yii::$app->authManager->hasChild($item, $child)) {
                    Yii::$app->authManager->addChild($item, $child);
                }
            }
        }

        return $this->render('view', [
            'model' => $model,
            'item' => $item,
            'children' => $this->getChildren($name),
            'childrenOptions' => $this->getChildrenOptions($item->name),
        ]);
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
     *
     * @param string $parentName Parent name of authorization item.
     * @param string $childName Child name of authorization item.
     *
     * @throws HttpException
     */
    public function actionRemoveChild($parentName, $childName)
    {
        $parent = Yii::$app->authManager->getRole($parentName) ?: Yii::$app->authManager->getPermission($parentName);
        $child = Yii::$app->authManager->getRole($childName) ?: Yii::$app->authManager->getPermission($childName);

        if (is_null($parent) || is_null($child)) {
            throw new HttpException(404, Yii::t('auth.main', 'Item does not exist.'));
        }

        if (Yii::$app->authManager->hasChild($parent, $child)) {
            Yii::$app->authManager->removeChild($parent, $child);
        }

        $this->redirect(['view', 'name' => $parent->name, 'type' => $parent->type]);
    }

    /**
     *
     *
     * @param $name
     *
     * @return array[]
     */
    protected function getChildren($name)
    {
        $direct = Yii::$app->authManager->getChildren($name);
        $indirect = [];

        foreach ($direct as $item) {
            if ($children = Yii::$app->authManager->getChildren($item->name)) {
                foreach ($children as $child) {
                    array_push($indirect, $this->getIndirectChildren($child));
                }
            }
        }

        usort($direct, function ($a, $b) {
            return $a->description - $b->description;
        });

        usort($indirect, function ($a, $b) {
            return $a->description - $b->description;
        });

        return array_merge($direct, $indirect);
    }

    /**
     *
     *
     * @param Item $item
     *
     * @return Item
     */
    protected function getIndirectChildren(Item $item)
    {
        if ($children = Yii::$app->authManager->getChildren($item->name)) {
            foreach ($children as $child) {
                $this->getIndirectChildren($child);
            }
        }

        return $item;
    }

    /**
     * Returns a list of possible children for the item with the given name.
     *
     * @param string $name Name of the item.
     *
     * @return array Child options.
     */
    protected function getChildrenOptions($name)
    {
        $options = [];

        $roles = $this->cleanChildren($name, ArrayHelper::map(Yii::$app->authManager->getRoles(), 'name', 'description'));
        $permissions = $this->cleanChildren($name, ArrayHelper::map(Yii::$app->authManager->getPermissions(), 'name', 'description'));

        if (!empty($roles)) {
            $options[Yii::t('auth.main', 'Roles')] = $roles;
        }

        if (!empty($permissions)) {
            $options[Yii::t('auth.main', 'Permission')] = $permissions;
        }

        return $options;
    }

    /**
     *
     *
     * @param $name
     * @param $list
     *
     * @return mixed
     */
    protected function cleanChildren($name, $list)
    {
        if (isset($list[$name])) {
            unset($list[$name]);
        }

        foreach (Yii::$app->authManager->getChildren($name) as $children) {
            if (isset($list[$children->name])) {
                unset($list[$children->name]);
            }
        }

        asort($list);

        return $list;
    }

    /**
     * Get name of authorization item type.
     *
     * @param integer $typeId Item type ident.
     *
     * @return string
     */
    protected function typeName($typeId)
    {
        return $typeId == Item::TYPE_ROLE ? 'Role' : 'Permission';
    }
}
