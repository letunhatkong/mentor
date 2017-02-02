<?php
$this->pageTitle = "Planning";
?>

<div class="col-12 max-height add-session">
    <div class="row-observation row-content-title">
        <form id="sessions-form" action="<?php echo Yii::app()->getBaseUrl() ?>/sessions/saveSession" method="post">
            <div class="col-12">
                <a class="img-avartar">
                    <img alt="" src="<?php echo Yii::app()->getBaseUrl() . Yii::app()->user->avatarPath ?>">
                    <span><?php echo Yii::app()->user->firstName . " " . Yii::app()->user->lastName; ?></span>
                </a>
            </div>

            <div class="col-12">
                <div class="row-fluid border-bottom padding-top" id="invitedTeam">
                    <select name="userInvited[]" class="chosen span6 atc" placeholder="Your Favorite Teams" id="userInvitedSelectSession"
                            multiple="multiple" tabindex="6" size="5" style="border: none; background:#fafafa">
                        <option></option>
                        <?php if (isset($dataUsers)) { ?>
                            <?php foreach ($dataUsers as $itemUser) { ?>
                                <option
                                    value="<?php echo $itemUser['idUser'] ?>"><?php echo $itemUser['firstName'] . ' ' . $itemUser['lastName'] ?></option>
                            <?php } ?>
                        <?php } ?>
                    </select>
                </div>

                <div class="row-fluid border-bottom">
                    <input class="txt-title createSessionTitle" type="text" value="" name="title" placeholder="Title"/>
                </div>

                <div class="row-fluid">
                    <div class="padding-top">
                        <div class="col-4">
                            <input class="m-ctrl-medium date-picker" type="text" id="datepicker" value="" name="date"
                                   placeholder="DATE PICKER"/>
                        </div>
                        <div class="col-8 select-option">
                            <select name="archive[]" multiple="multiple" class="SlectBox" placeholder="SELECT DOCUMENTS">
                                <?php if (isset($dataArchives)) { ?>
                                    <?php foreach ($dataArchives as $itemArchive) { ?>
                                        <option
                                            value="<?php echo $itemArchive['idArchive'] ?>"><?php echo $itemArchive['name'] ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                            <select id="topic" name="topic" class="SlectBox" placeholder="SELECT TOPIC">
                                <option value="" selected></option>
                                <?php if (isset($dataTopics)) { ?>
                                    <?php foreach ($dataTopics as $itemTopic) { ?>
                                        <?php if($itemTopic['active'] == "1") { ?>
                                        <option
                                            value="<?php echo $itemTopic['idTopic'] ?>"><?php echo $itemTopic['name'] ?></option>
                                        <?php } ?>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <textarea class="span12 padding-top createSessionTitle" rows="8" placeholder="Text input here..."
                              name="content"></textarea>
                </div>

                <div class="row-fluid border-top fixed-bottom">
                    <a class="btn" id="buttonCancel">Cancel</a>
                    <a name="buttonOK" id="buttonOK" class="btn btn-primary">OK</a>
                </div>
            </div>
        </form>
        <div class="clearfix"></div>
    </div>
</div>