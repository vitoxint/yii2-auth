<?php
/**
 * RoleController class file.
 * @author Christoffer Niska <ChristofferNiska@gmail.com>
 * @copyright Copyright &copy; Christoffer Niska 2012-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package auth.controllers
 */

namespace auth\controllers;

use ReflectionClass;
use auth\components\ItemController;
use Yii;
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
     *
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
                            $prefix = mb_strtolower(mb_substr(str_replace(['Controller'], [null], $class), 1, null, Yii::$app->charset), Yii::$app->charset);
                            foreach ($actions as $action) {
                                $this->actionsMap[$class][] = $prefix . '::' . mb_strtolower($action, Yii::$app->charset);
                            }
                        }
                    }
                }
            }
        }
    }
}
