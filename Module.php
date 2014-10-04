<?php
/**
 * AuthModule class file.
 * @author Christoffer Niska <ChristofferNiska@gmail.com>
 * @copyright Copyright &copy; Christoffer Niska 2012-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package auth
 * @version 2.0.0
 */

namespace auth;

use Yii;

/**
 * Web module for managing Yii's built-in authorization manager (\yii\rbac\DbeManager).
 */
class Module extends \yii\base\Module
{
    /**
     * @var string Name of the user model class. Change this if your user model name is different than the default value.
     */
    public $userClass;

    /**
     * @var string Name of the user id column. Change this if the id column in your user table is different than the default value.
     */
    public $userIdColumn = 'id';

    /**
     * @var string Name of the user name column. Change this if the name column in your user table is different than the default value.
     */
    public $userNameColumn = 'name';

    /**
     * @var array Map of flash message keys to use for the module.
     */
    public $flashKeys = array();

    /**
     * @var string String the id of the default controller for this module.
     */
    public $defaultRoute = 'assignment';

    /**
     * @var array Map of application's controllers. For example,
     *
     * ```php
     * 'applicationControllers' => [
     *      '\frontend\controllers' => Yii::getAlias('@frontend') . '/controllers',
     *      '\backend\controllers' => Yii::getAlias('@backend') . '/controllers',
     * ],
     * ```
     */
    public $applicationControllers = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->registerTranslations();

        Yii::$app->authManager->attachBehavior('auth', 'auth\components\AuthBehavior');

        if (empty($this->userClass)) {
            $this->userClass = Yii::$app->user->identityClass;
        }

        $this->flashKeys = array_merge($this->flashKeys, [
            'error' => 'error',
            'info' => 'info',
            'success' => 'success',
            'warning' => 'warning',
        ]);
    }

    /**
     * Register i18n messages.
     */
    public function registerTranslations()
    {
        Yii::$app->i18n->translations['auth.*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'basePath' => '@vendor/binn/yii2-auth/messages',
            'fileMap' => [
                'auth.main' => 'main.php'
            ],
        ];
    }

    /**
     * Returns the module version number.
     *
     * @return string Version number.
     */
    public function getVersion()
    {
        return '2.0.0';
    }
}
