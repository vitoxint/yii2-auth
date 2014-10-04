<?php
/**
 * ChildForm class file.
 * @author Artur Fursa <art@binn.ru>
 * @author Yevhen Servetnyk <evgeniy.servetnik@binn.ru>
 * @copyright Copyright &copy; Binn Ltd. 2014-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */

namespace auth\models;

use Yii;
use yii\base\Model;
use yii\rbac\Permission;
use yii\rbac\Role;

/**
 * Form model for displaying a list of authorization items.
 */
class ChildForm extends Model
{
    /**
     * @var Role|Permission List of authorization items.
     */
    public $items;

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'items' => Yii::t('auth.main', 'Items'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['items'], 'required'],
        ];
    }
}
