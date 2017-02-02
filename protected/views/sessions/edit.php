<?php
    $this->pageTitle  = "Planning";
?>
<div class="col-12 max-height add-session">
    <div class="row-observation row-content-title">
        <form id="sessions-form" action="<?php echo Yii::app()->getBaseUrl() ?>/sessions/saveSession" method="post">
            <input type="hidden" value="<?php echo $id?>" name="id"/>
            <div class="col-12">
                <?php if (isset(Yii::app()->user->id)) {  ?>
                <a class="img-avartar">
                    <img alt="" src="<?php echo Yii::app()->getBaseUrl().Yii::app()->user->avatarPath?>"/>
                    <span><?php echo Yii::app()->user->firstName." ".Yii::app()->user->lastName?></span>
                </a>
                <?php }?>
            </div>

            <div class="col-12">
                <div class="row-fluid border-bottom padding-top" id="invitedTeam">
                    <select name="userInvited[]" placeholder="Please type name here" class="chosen span6" multiple="multiple" tabindex="6" size="5">
                        <?php
                            foreach ($dataUsers as $itemUser){
                                foreach($idUserInvited as $itemId){
                        ?>
                            <option value="<?php echo $itemUser['idUser'] ?>" 
                            <?php
                                if($itemUser['idUser'] == $itemId['idUserInvited']){
                                    echo "selected";
                                }
                            ?>>
                            <?php echo $itemUser['firstName'].' '.$itemUser['lastName'] ?>
                        </option>
                        <?php
                            }}
                        ?>  
                    </select>
                </div>

                <div class="row-fluid border-bottom">
                    <input class="txt-title createSessionTitle" type="text" value="<?php echo $dataDetailEdit['title'] ?>" name="title" placeholder="Title"/>
                </div>

                <div class="row-fluid">
                    <div class="padding-top">  
                        <div class="col-4">
                        <input class=" m-ctrl-medium date-picker" type="text" id="datepicker" value="<?php echo date('d-m-Y',strtotime($dataDetailEdit['datePost']))?>" name="date" placeholder="DATE PICKER"/>
                        </div>
                        <div class="col-8 select-option">
                            <select name="archive[]" multiple="multiple" placeholder="SELECT DOCUMENT" class="SlectBox">
                                <?php
                                    foreach ($dataArchives as $itemArchive){
                                        
                                ?>
                                <option value="<?php echo $itemArchive['idArchive'] ?>"
                                        <?php foreach ($idArchiveEdit as $itemArchiveId){
                                            if($itemArchive['idArchive'] == $itemArchiveId['idArchive']){
                                                echo "selected";
                                            }} 
                                        ?>><?php echo $itemArchive['name'];?>
                                </option>
                                <?php
                                                                       
                                }
                                ?>
                            </select>
                            <select name="topic" class="SlectBox" placeholder="SELECT TOPIC">
                                <?php
                                    foreach ($dataTopics as $itemTopic){
                                ?>
                                <option value="<?php echo $itemTopic['idTopic'] ?>" 
                                    <?php 
                                        if((int)$itemTopic['idTopic']===(int)$dataDetailEdit['idTopic']){ 
                                            echo "selected";
                                        }
                                    ?>><?php echo $itemTopic['name']?>
                                </option>
                                <?php
                                    }
                                ?>
                            </select>
                        </div>
                    </div>    
                    <textarea class="span12 padding-top createSessionTitle" rows="8" name="content"><?php echo $dataDetailEdit['description'] ?></textarea>
                </div>

                <div class="row-fluid border-top fixed-bottom">
                    <a class="btn" id="buttonCancel" >Cancel</a>
                    <a name="buttonOK" id="buttonOK" class="btn btn-primary">OK</a>
                </div>
            </div>
        </form>
        <div class="clearfix"></div>
    </div>
</div>