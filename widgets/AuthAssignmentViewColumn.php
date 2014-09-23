<?php
/**
 * AuthAssignmentViewColumn class file.
 * @author Christoffer Niska <ChristofferNiska@gmail.com>
 * @copyright Copyright &copy; Christoffer Niska 2012-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package auth.widgets
 */

namespace auth\widgets;

use Yii;
use yii\bootstrap\Button;

/**
 * Grid column for displaying the view link for an assignment row.
 */
class AuthAssignmentViewColumn extends AuthAssignmentColumn
{
    /**
     * Initializes the column.
     */
    public function init()
    {
        if (isset($this->options['class'])) {
            $this->options['class'] .= ' actions-column';
        } else {
            $this->options['class'] = 'actions-column';
        }
    }

    /**
     * @inheritdoc
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        //if (!Yii::$app->user->isAdmin) {
            return Button::widget([
                'label' => 's',//TbHtml::icon(TbHtml::ICON_EYE_OPEN)
                /*[
                    //'color' => TbHtml::BUTTON_COLOR_LINK,
                    //'size' => TbHtml::BUTTON_SIZE_MINI,
                    'url' => ['view', 'id' => $model->{$this->idColumn}],
                    'htmlOptions' => ['rel' => 'tooltip', 'title' => Yii::t('AuthModule.main', 'View')],
                ]*/
            ]);
        //}
    }
}
