<?php
/**
 * PhpManager class file.
 * @author Artur Fursa <art@binn.ru>
 * @copyright Copyright &copy; Binn Ltd 2014-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */

namespace auth\components;

/**
 * This class needed because `\yii\rbac\PhpManager` does not allow to assign behaviors directly via configuration. 
 */
class PhpManager extends \yii\rbac\PhpManager
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->attachBehavior('auth', ['class' => \Yii::$app->getModule('auth')->authBehavior]);
    }
}
