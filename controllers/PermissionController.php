<?php
/**
 * RoleController class file.
 * @author Artur Fursa <art@binn.ru>
 * @author Yevhen Servetnyk <evgeniy.servetnik@binn.ru>
 * @copyright Copyright &copy; Binn Ltd. 2014-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */

namespace auth\controllers;

use ReflectionClass;
use auth\components\ItemController;
use Yii;
use yii\helpers\Inflector;
use yii\rbac\Item;

/**
 * Controller for role related actions.
 */
class PermissionController extends ItemController
{
    /**
     * @var string
     */
    const ACTION_PREFIX = 'action';

    /**
     * @var integer Item type.
     */
    public $type = Item::TYPE_PERMISSION;

    /**
     * @var array
     */
    public $actionsMap = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->initMap();
    }

    /**
     * Generate actions map
     *
     * @return void
     */
    private function initMap()
    {
        $offset = mb_strlen(self::ACTION_PREFIX, Yii::$app->charset);

        foreach ($this->module->applicationControllers as $namespace => $path) {
            if (is_dir($path) && is_readable($path)) {
                foreach (scandir($path) as $filename) {
                    if (is_file($path . '/' . $filename)) {
                        $class = $namespace . '\\' . pathinfo($filename, PATHINFO_FILENAME);

                        $reflection = new ReflectionClass($class);

                        // get array-action (from class::actions())
                        $actions = array_keys($reflection->newInstanceWithoutConstructor()->actions());

                        // get method-action (like class::actionIndex())
                        foreach ($reflection->getMethods() as $method) {
                            if (mb_strlen($method->name, Yii::$app->charset) > $offset + 1 && mb_substr($method->name, 0, $offset, Yii::$app->charset) === self::ACTION_PREFIX) {
                                $actions[] = mb_substr($method->name, $offset, null, Yii::$app->charset);
                            }
                        }

                        // create map
                        if (!empty($actions)) {
                            $prefix = Inflector::camel2id(str_replace('Controller', '', $reflection->getShortName()), '');
                            foreach ($actions as $action) {
                                $actionId = $prefix . '.' . mb_strtolower($action, Yii::$app->charset);
                                $actionId = \Yii::$app->getModule('auth')->useApplicationPrefix ? Yii::$app->id . '.' . $actionId : $actionId;
                                $this->actionsMap[$class][$actionId] = Inflector::camel2words($actionId);
                            }
                        }
                    }
                }
            }
        }
    }
}
