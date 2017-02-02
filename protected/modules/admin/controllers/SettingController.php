<?php
/**
 * Class SettingController
 * This is setting controller of admin module.
 * @author UTC.HuyTD
 */
class SettingController extends Controller {
    /**
     * Ajax Function show view edit video
     * @return undefined
     */
    public function actionUpdateSetting(){        
        if (Yii::app()->request->isAjaxRequest) {
            $dataSetting = Setting::model()->find();
            $dataSetting->allowComment=Yii::app()->request->getParam('settingCommentAllow');
            $dataSetting->allowLike=Yii::app()->request->getParam('settingLikeAllow');
            $dataSetting->allowLimitDuration=Yii::app()->request->getParam('settingDurationLimitAllow');
            $dataSetting->limitDurationValue=Yii::app()->request->getParam('settingDurationLimitValue');
            $dataSetting->save();
            echo json_encode(array(                
                'allowComment'=>$dataSetting->allowComment,
                'allowLike'=>$dataSetting->allowLike,
                'allowLimitDuration'=>$dataSetting->allowLimitDuration,
                'limitDurationValue'=>$dataSetting->limitDurationValue
            ));exit;
        }else{
            $this->forward('/site/error');
        }
    }
}