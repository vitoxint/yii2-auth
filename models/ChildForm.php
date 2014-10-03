<?php
/**
 * ChildForm class file.
 * @author Christoffer Niska <ChristofferNiska@gmail.com>
 * @copyright Copyright &copy; Christoffer Niska 2012-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package auth.models
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
