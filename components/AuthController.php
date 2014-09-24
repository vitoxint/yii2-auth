<?php
/**
 * AuthController class file.
 * @author Christoffer Niska <ChristofferNiska@gmail.com>
 * @copyright Copyright &copy; Christoffer Niska 2012-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package auth.components
 */

namespace sb\modules\auth\components;

use sb\modules\auth\controllers\AssignmentController;
use sb\modules\auth\controllers\OperationController;
use sb\modules\auth\controllers\RoleController;
use sb\modules\auth\controllers\TaskController;
use yii\base\Exception;
use yii\rbac\Item;
use yii\web\Controller;
use Yii;

/**
 * Base controller for the module.
 * Note: Do NOT extend your controllers from this class!
 */
abstract class AuthController extends Controller
{
    /**
     * Returns the controllerId for the given authorization item.
     * @param string $type the item type (0=operation, 1=task, 2=role).
     * @return string the controllerId.
     * @throws Exception if the item type is invalid.
     */
    public function getItemControllerId($type)
    {
        $controllerId = null;
        switch ($type) {
            /*case Item::TYPE_OPERATION:
                $controllerId = 'operation';
                break;

            case Item::TYPE_TASK:
                $controllerId = 'task';
                break;*/

            case Item::TYPE_ROLE:
                $controllerId = 'role';
                break;

            default:
                throw new Exception('Auth item type "' . $type . '" is invalid.');
        }
        return $controllerId;
    }

    /**
     * Capitalizes the first word in the given string.
     * @param string $string the string to capitalize.
     * @return string the capitalized string.
     * @see http://stackoverflow.com/questions/2517947/ucfirst-function-for-multibyte-character-encodings
     */
    public function capitalize($string)
    {
        if (!extension_loaded('mbstring')) {
            return ucfirst($string);
        }

        $encoding = Yii::$app->charset;
        $firstChar = mb_strtoupper(mb_substr($string, 0, 1, $encoding), $encoding);
        return $firstChar . mb_substr($string, 1, mb_strlen($string, $encoding) - 1, $encoding);
    }
}
