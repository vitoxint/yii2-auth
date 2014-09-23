<?php
/**
 * AuthItemColumn class file.
 * @author Christoffer Niska <ChristofferNiska@gmail.com>
 * @copyright Copyright &copy; Christoffer Niska 2012-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package auth.widgets
 */

namespace auth\widgets;

use yii\helpers\Html;
use Yii;

/**
 * Grid column for displaying the description for an authorization item row.
 */
class AuthItemDescriptionColumn extends AuthItemColumn
{
    /**
     * Initializes the column.
     */
    public function init()
    {
        if (isset($this->options['class'])) {
            $this->options['class'] .= ' item-description-column';
        } else {
            $this->options['class'] = 'item-description-column';
        }
    }

    /**
     * @inheritdoc
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        /* @var $am \yii\rbac\BaseManager|AuthBehavior */
        $am = Yii::$app->getAuthManager();

        $linkCssClass = $this->active || $am->hasParent($this->itemName, $model['name']) || $am->hasChild(
            $this->itemName,
            $model['name']
        ) ? 'active' : 'disabled';

        /* @var $controller AuthItemController */
        $controller = $this->grid->view->context;

        return Html::a(
            $data['item']->description,
            array('/auth/' . $controller->getItemControllerId($data['item']->type) . '/view', 'name' => $data['name']),
            array('class' => $linkCssClass)
        );
    }
}
