<?php
/**
 * AuthAssignmentItemsColumn class file.
 * @author Christoffer Niska <ChristofferNiska@gmail.com>
 * @copyright Copyright &copy; Christoffer Niska 2012-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package auth.widgets
 */

namespace auth\widgets;

use sbuilder\helpers\Dev;
use Yii;

/**
 * Grid column for displaying the authorization items for an assignment row.
 */
class AuthAssignmentItemsColumn extends AuthAssignmentColumn
{
    /**
     * Initializes the column.
     */
    public function init()
    {
        if (isset($this->options['class'])) {
            $this->options['class'] .= ' assignment-items-column';
        } else {
            $this->options['class'] = 'assignment-items-column';
        }
    }

    /**
     * @inheritdoc
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        /* @var $am \yii\rbac\BaseManager|\auth\components\AuthBehavior */
        $am = Yii::$app->getAuthManager();

        /* @var $controller \auth\controllers\AssignmentController */
        $controller = $this->grid->view->context;

        $assignments = $am->getAssignments($model->{$this->idColumn});
        $permissions = $am->getItemsPermissions(array_keys($assignments));
        foreach ($permissions as $itemPermission) {
            $html = $itemPermission['item']->description;
            $html .= ' <small>' . $controller->getItemTypeText($itemPermission['item']->type, false) . '</small><br />';

            return $html;
        }
    }
}
