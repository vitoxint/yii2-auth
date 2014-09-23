<?php
/**
 * AssignmentController class file.
 * @author Christoffer Niska <ChristofferNiska@gmail.com>
 * @copyright Copyright &copy; Christoffer Niska 2012-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package auth.controllers
 */

namespace auth\controllers;

use auth\components\AuthBehavior;
use auth\components\AuthController;
use auth\components\AuthItemDataProvider;
use auth\models\AddAuthItemForm;
use sbuilder\helpers\Dev;
use yii\data\ActiveDataProvider;
use Yii;
use yii\db\ActiveRecord;
use yii\rbac\BaseManager;
use yii\rbac\Item;
use yii\rbac\PhpManager;
use yii\web\HttpException;

/**
 * Controller for assignment related actions.
 */
class AssignmentController extends AuthController
{
    /**
     * Displays the a list of all the assignments.
     */
    public function actionIndex()
    {
        $model = $this->module->userClass;
        $dataProvider = new ActiveDataProvider(['query' => $model::find()]);

        return $this->render(
            'index',
            [
                'dataProvider' => $dataProvider
            ]
        );
    }

    /**
     * Displays the assignments for the user with the given id.
     * @param string $id the user id.
     */
    public function actionView($id)
    {
        $formModel = new AddAuthItemForm();

        /* @var $am \yii\rbac\BaseManager|AuthBehavior */
        $am = Yii::$app->getAuthManager();

        if (isset($_POST['AddAuthItemForm'])) {
            $formModel->attributes = $_POST['AddAuthItemForm'];
            if ($formModel->validate()) {
                if (!$am->isAssigned($formModel->items, $id)) {
                    $am->assign($formModel->items, $id);
                    if ($am instanceof PhpManager) {
                        $am->save();
                    }

                    if ($am instanceof PhpManager) {
                        $am->flushAccess($formModel->items, $id);
                    }
                }
            }
        }

        /** @var ActiveRecord $model */
        $model = $this->module->userClass;
        $model = $model::findOne(['id' => $id]); // FIXME искать по первичному ключу

        $assignments = $am->getAssignments($id);
        $authItems = $am->getItemsPermissions(array_keys($assignments));
        $authItemDp = new AuthItemDataProvider();
        $authItemDp->setAuthItems($authItems);

        $assignmentOptions = $this->getAssignmentOptions($id);
        if (!empty($assignmentOptions)) {
            $assignmentOptions = array_merge(
                ['' => Yii::t('AuthModule.main', 'Select item') . ' ...'],
                $assignmentOptions
            );
        }

        return $this->render(
            'view',
            [
                'model' => $model,
                'authItemDp' => $authItemDp,
                'formModel' => $formModel,
                'assignmentOptions' => $assignmentOptions,
            ]
        );
    }

    /**
     * Revokes an assignment from the given user.
     * @throws HttpException if the request is invalid.
     */
    public function actionRevoke()
    {
        if (isset($_GET['itemName'], $_GET['userId'])) {
            $itemName = $_GET['itemName'];
            $userId = $_GET['userId'];

            /* @var $am BaseManager|AuthBehavior */
            $am = Yii::$app->getAuthManager();

            if ($am->isAssigned($itemName, $userId)) {
                $am->revoke($itemName, $userId);
                if ($am instanceof PhpManager) {
                    $am->save();
                }

                if ($am instanceof PhpManager) {
                    $am->flushAccess($itemName, $userId);
                }
            }

            if (!isset($_POST['ajax'])) {
                $this->redirect(['view', 'id' => $userId]);
            }
        } else {
            throw new HttpException(400, Yii::t('AuthModule.main', 'Invalid request.'));
        }
    }

    /**
     * Returns a list of possible assignments for the user with the given id.
     * @param string $userId the user id.
     * @return array the assignment options.
     */
    protected function getAssignmentOptions($userId)
    {
        $options = [];

        /* @var $am BaseManager|AuthBehavior */
        $am = Yii::$app->authManager;

        $assignments = $am->getAssignments($userId);
        $assignedItems = array_keys($assignments);

        /* @var $authItems Item[] */
        $authItems = $am->getItems();
        foreach ($authItems as $itemName => $item) {
            if (!in_array($itemName, $assignedItems)) {
                $options[$this->capitalize($this->getItemTypeText($item->type, true))][$itemName] = $item->description;
            }
        }

        return $options;
    }
}