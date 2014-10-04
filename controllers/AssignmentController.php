<?php
/**
 * AssignmentController class file.
 * @author Artur Fursa <art@binn.ru>
 * @author Yevhen Servetnyk <evgeniy.servetnik@binn.ru>
 * @copyright Copyright &copy; Binn Ltd. 2014-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */

namespace auth\controllers;

use auth\components\AuthController;
use auth\models\ChildForm;
use yii\data\ActiveDataProvider;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
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

        return $this->render('index', [
            'dataProvider' => new ActiveDataProvider([
                'query' => $model::find()
            ]),
        ]);
    }

    /**
     * Displays the assignments for the user with the given id.
     *
     * @param string $id the user id.
     *
     * @return string
     */
    public function actionView($id)
    {
        $model = new ChildForm;

        // set assignment
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            foreach ($model->items as $name) {
                if ($role = Yii::$app->authManager->getRole($name)) {
                    Yii::$app->authManager->assign($role, $id);
                }
            }
        }

        /** @var ActiveRecord $model */
        $modelUser = $this->module->userClass;
        $user = $modelUser::findOne($id);

        // assigned items
        $roles = Yii::$app->authManager->getRolesByUser($id);
        usort($roles, function ($a, $b) {
            return $a->description - $b->description;
        });
        $permissions = Yii::$app->authManager->getPermissionsByUser($id);
        usort($permissions, function ($a, $b) {
            return $a->description - $b->description;
        });
        $assignments = ArrayHelper::merge($roles, $permissions);

        $assignmentOptions = $this->getAssignmentOptions($id);

        return $this->render('view', [
            'user' => $user,
            'model' => $model,
            'assignments' => $assignments,
            'assignmentOptions' => $assignmentOptions,
        ]);
    }

    /**
     * Revokes an assignment from the given user.
     *
     * @param string $name Name of permission.
     * @param integer $user User id.
     *
     * @throws HttpException
     */
    public function actionRevoke($name, $user)
    {
        if (Yii::$app->authManager->getAssignment($name, $user)) {
            if ($item = Yii::$app->authManager->getRole($name)) {
                Yii::$app->authManager->revoke($item, $user);
                $this->redirect(['view', 'id' => $user]);
            } else {
                throw new HttpException(404, Yii::t('auth.main', 'Role does not exist.'));
            }
        } else {
            throw new HttpException(404, Yii::t('auth.main', 'Assignment does not exist.'));
        }
    }

    /**
     * Returns a list of possible assignments for the user with the given id.
     *
     * @param integer $userId User id.
     *
     * @return array Assignment options list.
     */
    protected function getAssignmentOptions($userId)
    {
        $options = [];

        $assignments = Yii::$app->authManager->getRolesByUser($userId);
        $roles = Yii::$app->authManager->getRoles();

        foreach ($roles as $name => $role) {
            if (!isset($assignments[$name])) {
                $options[$name] = $role->description;
            }
        }

        return $options;
    }
}
