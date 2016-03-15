<?php
/**
 * AuthBehavior class file.
 * @author Artur Fursa <art@binn.ru>
 * @author Yevhen Servetnyk <evgeniy.servetnik@binn.ru>
 * @copyright Copyright &copy; Binn Ltd. 2014-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */

namespace auth\components;

use yii\base\Behavior;
use yii\rbac\Item;
use Yii;

/**
 * Auth module behavior for the authorization manager.
 *
 * @property Yii\rbac\DbManager $owner The authorization manager.
 */
class AuthBehavior extends Behavior
{
    /**
     * @var array cached relations between the auth items.
     */
    private $_items = array();

    private $_isAdmin = null;

    /**
     * Return thee parents and children of specific item or all items
     * @param string $itemName name of the item.
     * @return array
     */
    public function getItems($itemName = null)
    {
        if ($itemName && isset($this->_items[$itemName])) {
            return $this->_items[$itemName];
        }

        return $this->_items;
    }

    /**
     * Sets the parents of specific item
     * @param string $itemName name of the item.
     * @param array $parents
     */
    public function setItemParents($itemName, $parents)
    {
        $this->_items[$itemName]['parents'] = $parents;
    }

    /**
     * Sets the children of specific item
     * @param string $itemName name of the item.
     * @param array $children
     */
    public function setItemChildren($itemName, $children)
    {
        $this->_items[$itemName]['children'] = $children;
    }

    /**
     * Gets the parents of specific item if exists
     * @param string $itemName name of the item.
     * @return array
     */
    public function getParents($itemName)
    {
        $items = $this->getItems($itemName);
        if (isset($items['parents'])) {
            return $items['parents'];
        }

        return array();
    }

    /**
     * Gets the children of specific item if exists
     * @param string $itemName name of the item.
     * @return array
     */
    public function getChildren($itemName)
    {
        $items = $this->getItems($itemName);
        if (isset($items['children'])) {
            return $items['children'];
        }

        return array();
    }

    /**
     * Returns whether the given item has a specific parent.
     * @param string $itemName name of the item.
     * @param string $parentName name of the parent.
     * @return boolean the result.
     */
    public function hasParent($itemName, $parentName)
    {
        $parents = $this->getParents($itemName);
        if (in_array($parentName, $parents)) {
            return true;
        }
        return false;
    }

    /**
     * Returns whether the given item has a specific child.
     * @param string $itemName name of the item.
     * @param string $childName name of the child.
     * @return boolean the result.
     */
    public function hasChild($itemName, $childName)
    {
        $children = $this->getChildren($itemName);
        if (in_array($childName, $children)) {
            return true;
        }
        return false;
    }

    /**
     * Returns whether the given item has a specific ancestor.
     * @param string $itemName name of the item.
     * @param string $ancestorName name of the ancestor.
     * @return boolean the result.
     */
    public function hasAncestor($itemName, $ancestorName)
    {
        $ancestors = $this->getAncestors($itemName);
        return isset($ancestors[$ancestorName]);
    }

    /**
     * Returns whether the given item has a specific descendant.
     * @param string $itemName name of the item.
     * @param string $descendantName name of the descendant.
     * @return boolean the result.
     */
    public function hasDescendant($itemName, $descendantName)
    {
        $descendants = $this->getDescendants($itemName);
        return isset($descendants[$descendantName]);
    }

    /**
     * Returns flat array of all ancestors.
     * @param string $itemName name of the item.
     * @return array the ancestors.
     */
    public function getAncestors($itemName)
    {
        $ancestors = $this->getAncestor($itemName);
        return $this->flattenPermissions($ancestors);
    }

    /**
     * Returns all ancestors for the given item recursively.
     * @param string $itemName name of the item.
     * @param integer $depth current depth.
     * @return array the ancestors.
     */
    public function getAncestor($itemName, $depth = 0)
    {
        $ancestors = array();
        $parents = $this->getParents($itemName);
        if (empty($parents)) {
            $parents = $this->owner->db->createCommand()
                ->select('parent')
                ->from($this->owner->itemChildTable)
                ->where('child=:child', array(':child' => $itemName))
                ->queryColumn();
            $this->setItemParents($itemName, $parents);
        }

        foreach ($parents as $parent) {
            $ancestors[] = array(
                'name' => $parent,
                'item' => $this->owner->getAuthItem($parent),
                'parents' => $this->getAncestor($parent, $depth + 1),
                'depth' => $depth
            );
        }
        return $ancestors;
    }

    /**
     * Returns flat array of all the descendants.
     * @param string $itemName name of the item.
     * @return array the descendants.
     */
    public function getDescendants($itemName)
    {
        $descendants = $this->getDescendant($itemName);
        return $this->flattenPermissions($descendants);
    }

    /**
     * Returns all the descendants for the given item recursively.
     * @param string $itemName name of the item.
     * @param integer $depth current depth.
     * @return array the descendants.
     */
    public function getDescendant($itemName, $depth = 0)
    {
        $descendants = array();
        $children = $this->getChildren($itemName);
        if (empty($children)) {
            $children = $this->owner->db->createCommand()
                ->select('child')
                ->from($this->owner->itemChildTable)
                ->where('parent=:parent', array(':parent' => $itemName))
                ->queryColumn();
            $this->setItemChildren($itemName, $children);
        }

        foreach ($children as $child) {
            $descendants[$child] = array(
                'name' => $child,
                'item' => $this->owner->getAuthItem($child),
                'children' => $this->getDescendant($child, $depth + 1),
                'depth' => $depth,
            );
        }
        return $descendants;
    }

    /**
     * Returns the permission tree for the given items.
     *
     * @param Item[] $items Items to process. If omitted the complete tree will be returned.
     *
     * @return array Permissions.
     */
    private function getPermissions($items = null)
    {
        if ($items === null) {
            $items = [
                Item::TYPE_ROLE => $this->owner->getRoles(),
                Item::TYPE_PERMISSION => $this->owner->getPermissions(),
            ];
        }

        return $items;
    }

    /**
     * Returns the permissions for the items with the given names.
     *
     * @param string[] $names List of item names.
     *
     * @return array Permissions.
     */
    public function getItemsPermissions($names)
    {
        $permissions = [];

        $items = $this->getPermissions();


        foreach ($items as $name => $item) {
            if (in_array($name, $names)) {
                $permissions[$name] = $item;
            }
        }

        return $permissions;
    }

    /**
     * Flattens the given permission tree.
     *
     * @param array $permissions Permissions tree.
     *
     * @return array Permissions.
     */
    public function flattenPermissions($permissions)
    {
        $flattened = [];
        foreach ($permissions as $itemName => $itemPermissions) {
            $flattened[$itemName] = $itemPermissions;

            if (isset($itemPermissions['children'])) {
                $children = $itemPermissions['children'];
                unset($itemPermissions['children']); // not needed in a flat tree
                $flattened = array_merge($flattened, $this->flattenPermissions($children));
            }

            if (isset($itemPermissions['parents'])) {
                $parents = $itemPermissions['parents'];
                unset($itemPermissions['parents']);
                $flattened = array_merge($flattened, $this->flattenPermissions($parents));
            }
        }
        return $flattened;
    }

    /**
     * Check that user is admin.
     * 
     * @return boolean
     */
    public function isAdmin()
    {
        if (Yii::$app->user->isGuest) {
            return false;
        }

        if ($this->_isAdmin === null) {
            $module = Yii::$app->getModule('auth');
            $userRoles = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id));
            $this->_isAdmin = array_intersect($userRoles, $module->admins);
        }
        
        return $this->_isAdmin;
    }

    /**
     * Check access for specified route.
     *
     * @param string $route Route to be checked like `module/controller/action`, default to `Yii::$app->controller->getRoute()`
     * @param array $params Additional params that can be used in business rules.
     *
     * @return boolean
     */
    public function checkRoute($route = null, array $params = [])
    {
        if (is_null($route)) {
            $route = Yii::$app->controller->getRoute();
        } elseif (is_array($route)) {
            $route = $route[0];
        }

        $route = str_replace('/', '.', trim($route, '/'));
        if (substr_count($route, '.') > 0) {
            $controllerRoute = $route;
            $controllerRoute = Yii::$app->getModule('auth')->useApplicationPrefix ? Yii::$app->id . '.' . $controllerRoute : $controllerRoute;
        } else {
            $controllerRoute = $this->getRuleName();
        }

        return $this->isAdmin() || Yii::$app->user->can($controllerRoute, $params) || Yii::$app->user->can($route, $params);
    }

    /**
     * RBAC permission name for specified action.
     *
     * @param string $action Action id.
     *
     * @return string
     */
    public function getRuleName($action = null)
    {
        if (is_null($action)) {
            $ruleName = str_replace('/', '.', Yii::$app->controller->getRoute());
        } else {
            $module = Yii::$app->controller->module;

            $ruleName = $module->id == Yii::$app->id ? Yii::$app->controller->id . '.' . $action : $module->id . '.' . Yii::$app->controller->id . '.' . $action;
            $ruleName = Yii::$app->getModule('auth')->useApplicationPrefix ? Yii::$app->id . '.' . $ruleName : $ruleName;
        }

        return $ruleName;
    }
}
