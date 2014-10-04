<?php
/**
 * RoleController class file.
 * @author Artur Fursa <art@binn.ru>
 * @author Yevhen Servetnyk <evgeniy.servetnik@binn.ru>
 * @copyright Copyright &copy; Binn Ltd. 2014-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */

namespace auth\controllers;

use auth\components\ItemController;
use yii\rbac\Item;

/**
 * Controller for role related actions.
 */
class RoleController extends ItemController
{
    /**
     * @var integer Item type.
     */
    public $type = Item::TYPE_ROLE;
}
