<?php
/**
 * ItemForm class file.
 * @author Artur Fursa <art@binn.ru>
 * @author Yevhen Servetnyk <evgeniy.servetnik@binn.ru>
 * @copyright Copyright &copy; Binn Ltd. 2014-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */

namespace auth\models;

use Yii;
use yii\base\Model;

/**
 * Form model for updating an authorization item.
 */
class ItemForm extends Model
{
    /**
     * @var string Item name.
     */
    public $name;

    /**
     * @var string Item description.
     */
    public $description;

    /**
     * @var string Business rule associated with the item.
     */
    public $rule_name;

    /**
     * @var string Additional data for the item.
     */
    public $data;

    /**
     * @var string Item type.
     */
    public $type;

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('auth.main', 'System name'),
            'description' => Yii::t('auth.main', 'Description'),
            'rule_name' => Yii::t('auth.main', 'Business rule'),
            'data' => Yii::t('auth.main', 'Data'),
            'type' => Yii::t('auth.main', 'Type'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['description'], 'required'],
            [['name'], 'required', 'on' => 'create'],
            [['name'], 'string', 'max' => 64],
        ];
    }
}
