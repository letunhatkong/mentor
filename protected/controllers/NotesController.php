<?php
/**
 * Controller for Note page
 * @author      UTC.KongLtn
 * @date        2015/11/03
 */
class NotesController extends Controller {

    /**
     * Redirect to auth page if user is guest (not login)
     * @return undefined
     */
    public function filters() {
        if (Yii::app()->user->isGuest) {
            $this->redirect(Yii::app()->getBaseUrl() . "/auth/user/login");
        }
    }


    /**
     * Display note of current user, create a new note or edit current note when user click edit button
     * @return undefined
     */
    public function actionIndex() {
        $this->titleInHeaderBar = "My notes";
        $userId = Yii::app()->user->idUser;
        $model = new Notes;
        $latest = $this->getLatestNoteOfUser($userId);
        $isNull = is_null($latest) ? true : false;
        $latest = is_null($latest) ? $model : $latest;

        $submit = Yii::app()->request->getParam("submitNote");
        if (isset($submit)) {
            try {
                // Create a new Note
                if ($isNull) {
                    $latest = new Notes;
                    $latest->dateCreate = date("Y-m-d H:i:s");
                }
                $latest->idUserCreate = $userId;
                $latest->content = Yii::app()->request->getParam('content');
                $latest->lastUpdate = date("Y-m-d H:i:s");
                $latest->save();
                $this->redirect(Yii::app()->getBaseUrl() . "/notes");

            } catch (Exception $e) {
                echo $e->getMessage();
                exit;
            }
        }
        $this->render("index", array("data" => $latest, "isNull" => $isNull));
    }


    /**
     * Get Latest Note of Current User
     * @param $userId Id of current user
     * @return CActiveRecord|null|static object of Notes model
     */
    public function getLatestNoteOfUser($userId)  {
        $sql = 'select * from notes n where n.idUserCreate = :userId order by n.lastUpdate desc limit 1';
        $dataObj = Notes::model()->findBySql($sql, array(":userId" => $userId));
        return $dataObj;
    }

}
