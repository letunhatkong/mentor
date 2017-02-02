<?php

/**
 * Controller for Screen Planning
 * @author UTC.KongLTN
 * Last Update on Nov 23, 2015
 */

Class PlanningController extends Controller{
    
     /**
     * Filter and limit user if user does not login
     * @returns undefined
     */
    public function filters() {
        if (Yii::app()->user->isGuest) {
            $this->redirect(Yii::app()->getBaseUrl() . "/auth/user/login");
        }
    }

    
    /**
     * Limit description of session in observation screen
     * @param $string
     * @return string description after limit to show in observation screen
     */
    function limitShow($string){
        $string = strip_tags($string);
        if(strlen($string) > 120){
            $stringCut = substr($string, 0, 120);
            $string = substr($stringCut, 0, strrpos($stringCut,' '));
        }
        return $string;
    }
    
    /**
     * Filter and limit user if user don't create session or is not invited to a session 
     * @returns true if id current user is id user create current session or id user invited current session
     */
    function checkPermissionViewWithIdUser(){
        $idSession = Yii::app()->request->getParam('id');
        $idUser = Yii::app()->user->idUser;
        $dataIdUser = InvitedSession::model()->getIdUserCreateAndInvitedUser($idSession);
        $arrayUser =  array_column($dataIdUser, 'idUser');
        if(in_array($idUser, $arrayUser)){
            return 'true';
        }
        return 'false';
    }
    
    /**
     * Show all planned and past session
     * @returns undefined
     */
    public function actionIndex(){
        $this->titleInHeaderBar = "Planning";
        $dataPlannedPlanning = InvitedSession::model()->getSessionInPlannedTabPlanning();
        foreach($dataPlannedPlanning as $key=>$row){
            $dataPlannedPlanning[$key]['timeElapse']=$this->getTiming($row['dateCreate']);
            $dataPlannedPlanning[$key]['description'] = $this->limitShow($row['description']);
            $dataNumCommentInPlannedPlanning = Comments::model()->getNumComment((int)$row['idSession']);
            $dataPlannedPlanning[$key]['numComment'] = $dataNumCommentInPlannedPlanning;
            $dataPlannedPlanning[$key]['invitedUsers'] = InvitedSession::model()->getInfoOfInvitedUsers((int)$row['idSession']);
            $dataPlannedPlanning[$key]['archives'] = ArchiveSession::model()->getArchivesBySessionId((int)$row['idSession']);
        }
        $dataPastPlanning = InvitedSession::model()->getSessionInPastTabPlanning();
        foreach($dataPastPlanning as $key=>$row){
            $dataPastPlanning[$key]['timeElapse']=$this->getTiming($row['dateCreate']);
            $dataPastPlanning[$key]['description'] = $this->limitShow($row['description']);
            $dataNumCommentInPastPlanning = Comments::model()->getNumComment((int)$row['idSession']);
            $dataPastPlanning[$key]['numComment'] = $dataNumCommentInPastPlanning;
            $dataPastPlanning[$key]['invitedUsers'] = InvitedSession::model()->getInfoOfInvitedUsers((int)$row['idSession']);
            $dataPastPlanning[$key]['archives'] = ArchiveSession::model()->getArchivesBySessionId((int)$row['idSession']);
        }
        $this->render('index',array(
            'dataPlannedPlanning'=>$dataPlannedPlanning,
            'dataPastPlanning'=>$dataPastPlanning
        ));
    }
    
    /**
     * Change session into active or deactive
     * @returns undefined
     */
    public function actionSetActive() {
        $userId = Yii::app()->user->idUser;     
        $idSession = Yii::app()->request->getParam('idSession');
        try{
            if (isset($idSession) && !is_null($idSession) && (int)$idSession > 0) {
                $session = Sessions::Model()->findByPk((int) $idSession);                
                if ($session->idUserCreate == $userId) {
                    $session->active = ($session->active == 1) ? 0 : 1;
                    $session->activatedPoint = ($session->active == 1) ? time() : 0;
                    $session->save();         
                    echo $session->active;
                }
            } else {
                echo -1;
            }
        }catch(Exception $ex){
            echo -1;
        }
        exit;
    }
}