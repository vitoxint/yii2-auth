<?php
/**
 * AuthAssignmentNameColumn class file.
 * @author Christoffer Niska <ChristofferNiska@gmail.com>
 * @copyright Copyright &copy; Christoffer Niska 2012-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package auth.widgets
 */

namespace auth\widgets;

use yii\helpers\Html;

/**
 * Grid column for displaying the name of the user for an assignment row.
 */
class AuthAssignmentNameColumn extends AuthAssignmentColumn
{
    /**
     * Initializes the column.
     */
    public function init()
    {
        if (isset($this->options['class'])) {
            $this->options['class'] .= ' name-column';
        } else {
            $this->options['class'] = 'name-column';
        }
    }

    /**
     * @inheritdoc
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        return Html::a($model->{$this->nameColumn}, ['view', 'id' => $model->{$this->idColumn}]);
    }
}
