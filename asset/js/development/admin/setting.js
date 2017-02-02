/**
 * Javascript for user stories in setting section of admin page
 * @author UTC.HuyTD
 * Last Update: 5/11/2015
 */

/**
 *
 * @type {{settingCommentAllow: string, settingLikeAllow: string, settingDurationLimitAllow: string, settingDurationLimitValue: string}}
 * @private
 */
var _idsSetting = {
    settingCommentAllow:'#settingCommentAllow',
    settingLikeAllow:'#settingLikeAllow',
    settingDurationLimitAllow:'#settingDurationLimitAllow',
    settingDurationLimitValue:'#settingDurationLimitValue'
    
};

/**
 *
 * @type {{
 *      init: Function,
 *      changeSetting: Function,
 *      handlerChangeSetting: Function}}
 */
var infoFormSettingEvent = {
    init: function () {
        this.handlerChangeSetting();
    },
    
    /**
     * Function save change setting and load setting after change
     * @returns {undefined}
     */
    changeSetting: function(){        
        var settingCommentAllow = $(_idsSetting.settingCommentAllow).prop('checked')? 1 : 0;
        var settingLikeAllow = $(_idsSetting.settingLikeAllow).prop('checked')? 1 : 0;
        var settingDurationLimitAllow = $(_idsSetting.settingDurationLimitAllow).prop('checked')? 1 : 0;
        var settingDurationLimitValue = $(_idsSetting.settingDurationLimitValue).val();
        
        $.ajax({
            type: "post",
            url: getBaseUrl() +'/admin/setting/updateSetting',
            data: { 
                'settingCommentAllow':settingCommentAllow,
                'settingLikeAllow':settingLikeAllow,
                'settingDurationLimitAllow':settingDurationLimitAllow,
                'settingDurationLimitValue':settingDurationLimitValue
            },
            cache: false,
            success: function(data){
                var dataObject= JSON.parse(data);
                if(dataObject.allowComment==1){
                    $(_idsSetting.settingCommentAllow).prop("checked", true);
                }else{
                    $(_idsSetting.settingCommentAllow).prop("checked", false);
                }
                if(dataObject.allowLike==1){
                    $(_idsSetting.settingLikeAllow).prop("checked", true);
                }else{
                    $(_idsSetting.settingLikeAllow).prop("checked", false);
                }
                if(dataObject.allowLimitDuration==1){
                    $(_idsSetting.settingDurationLimitAllow).prop("checked", true);
                }else{
                    $(_idsSetting.settingDurationLimitAllow).prop("checked", false);
                }                
                $(_idsSetting.settingDurationLimitValue).val(dataObject.limitDurationValue);
            }
        });        
    },
    
    /**
     * Handle change setting in admin page: change
     * @returns {undefined}
     */
    handlerChangeSetting: function(){
        $(_idsSetting.settingCommentAllow).on('change',function(){
            infoFormSettingEvent.changeSetting();
        });
        $(_idsSetting.settingLikeAllow).on('change',function(){
            infoFormSettingEvent.changeSetting();
        });
        $(_idsSetting.settingDurationLimitAllow).on('change',function(){
            infoFormSettingEvent.changeSetting();
        });
        $(_idsSetting.settingDurationLimitValue).on('change',function(){
            infoFormSettingEvent.changeSetting();
        });
    }
};

$(document).ready(function () {
    "use strict";
    infoFormSettingEvent.init();
});