<?php
/**
 * ItemDataProvider class file.
 * @author Artur Fursa <art@binn.ru>
 * @author Yevhen Servetnyk <evgeniy.servetnik@binn.ru>
 * @copyright Copyright &copy; Binn Ltd. 2014-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */

namespace auth\components;

use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\rbac\Item;
use Yii;

/**
 * Data provider for listing authorization items.
 *
 * @package auth\components
 */
class ItemDataProvider extends ActiveDataProvider
{
    /**
     * @var string The item type.
     */
    public $type;

    /**
     * @var array List of data items.
     */
    private $_items = [];

    /**
     * Constructor.
     *
     * @param string $type Authorization item type.
     * @param array $config Name-value pairs that will be used to initialize the object properties.
     */
    public function __construct($type = null, $config = []) {
        parent::__construct($config);

        $this->type = $type;
        $this->pagination = false;
    }

    /**
     * Sets the authorization items.
     *
     * @param Item[] $items authorization items.
     */
    public function setItems($items)
    {
        $this->_items = array_values($items);
    }

    /**
     * @inheritdoc
     */
    public function getModels()
    {
        if (empty($this->query) && Yii::$app->authManager instanceof DbManager) {
            $this->query = (new Query)
                ->from(Yii::$app->authManager->itemTable)
                ->where(['type' => $this->type]);
        }

        if (empty($this->_items)) {
            switch ($this->type) {
                case Item::TYPE_ROLE:
                    $this->setItems(Yii::$app->authManager->getRoles());
                    break;
                case Item::TYPE_PERMISSION:
                    $this->setItems(Yii::$app->authManager->getPermissions());
                    break;
            }
        }

        return $this->_items;
    }
}
