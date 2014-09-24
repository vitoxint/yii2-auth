<?php
/**
 * RoleController class file.
 * @author Christoffer Niska <ChristofferNiska@gmail.com>
 * @copyright Copyright &copy; Christoffer Niska 2012-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package auth.controllers
 */

namespace sb\modules\auth\controllers;

use sb\modules\auth\components\ItemController;
use yii\rbac\Item;

/**
 * Controller for role related actions.
 */
class PermissionController extends ItemController
{
    /**
     * @var integer Item type.
     */
    public $type = Item::TYPE_PERMISSION;
}
