<?php
/**
 * AuthItemTypeColumn class file.
 * @author Christoffer Niska <ChristofferNiska@gmail.com>
 * @copyright Copyright &copy; Christoffer Niska 2012-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package auth.widgets
 */

namespace auth\widgets;

use Yii;

/**
 * Grid column for displaying the type for an authorization item row.
 */
class AuthItemTypeColumn extends AuthItemColumn
{
    /**
     * Initializes the column.
     */
    public function init()
    {
        if (isset($this->options['class'])) {
            $this->options['class'] .= ' item-type-column';
        } else {
            $this->options['class'] = 'item-type-column';
        }
    }

    /**
     * @inheritdoc
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        /* @var $am \yii\rbac\BaseManager|AuthBehavior */
        $am = Yii::$app->getAuthManager();

        $labelType = $this->active || $am->hasParent($this->itemName, $model['name']) || $am->hasChild(
            $this->itemName,
            $data['name']
        ) ? 'info' : '';

        /* @var $controller \auth\controllers\AuthItemController */
        $controller = $this->grid->view->context;

        echo TbHtml::labelTb(
            $controller->getItemTypeText($data['item']->type),
            array(
                'color' => $labelType,
            )
        );
    }
}
