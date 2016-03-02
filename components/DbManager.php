<?php
/**
 * DbManager class file.
 * @author Artur Fursa <art@binn.ru>
 * @copyright Copyright &copy; Binn Ltd 2014-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */

namespace auth\components;

/**
 * This class needed because `\yii\rbac\DbManager` does not allow to assign behaviors directly via configuration.
 */
class DbManager extends \yii\rbac\DbManager
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
