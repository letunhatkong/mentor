<?php $currentUserId = Yii::app()->user->idUser;
$archivePath = Yii::app()->getBaseUrl(). Yii::app()->params['archiveFolderPath'] . '/';
?>

<div class="col-12 margin-top">
    <div class="tabbable portlet-tabs">
        <ul class="nav-tabs">
            <li class="col-6 active"><a href="#portlet_tab1" data-toggle="tab">Planned</a></li>
            <li class="col-6"><a href="#portlet_tab2" data-toggle="tab">Past</a></li>
        </ul>
        <div class="tab-content">
            <!-- tab 1 -->
            <div class="tab-pane active" id="portlet_tab1">
                <?php if (isset($dataPlannedPlanning)) { ?>
                    <?php if (count($dataPlannedPlanning) == 0) { ?>
                    <a href="<?php echo Yii::app()->getBaseUrl() . '/sessions/create' ?>" id="addAPre">Add a presentation</a>
                    <?php } ?>

                    <?php foreach ($dataPlannedPlanning as $rowPlanned) { ?>
                        <div class="row-observation row-content">
                            <div class="col-12 cus-option">
                                <div class="col-6">
                                    <a class="img-avartar">
                                        <?php if ($rowPlanned['avatarPath'] != "" && $rowPlanned['avatarPath'] != null) {
                                            $avatarUserCreateSessionPlanned = Yii::app()->params['avatarFolderPath'] . '/' . $rowPlanned['avatarPath'];
                                        } else {
                                            $avatarUserCreateSessionPlanned = Yii::app()->params['avatarDefault'];
                                        } ?>
                                        <img
                                            src="<?php echo Yii::app()->getBaseUrl() . $avatarUserCreateSessionPlanned ?>"/>
                                    <span>
                                        <?php echo $rowPlanned['firstName'] . " " . $rowPlanned['lastName'] ?>
                                    </span>
                                    </a>
                                </div>
                                <div class="col-6 text-align-right">
                                    <?php if (Yii::app()->user->idUser == $rowPlanned['idUserCreate']) { ?>
                                        <a href="<?php echo Yii::app()->getBaseUrl(); ?>/sessions/edit/id/<?php echo $rowPlanned['idSession'] ?>">
                                            <span class="editSessionIcon"><i class="fa fa-pencil"></i></span>
                                        </a>
                                        <a href="javascript:void(0);">
                                            <span class="delSessionIcon" data-id="<?php echo $rowPlanned['idSession'] ?>">
                                                <i class="fa fa-trash"></i>
                                            </span>
                                        </a>
									<?php } ?>
                                    <span class="count-time"><?php echo $rowPlanned['timeElapse']; ?></span>
									<span class="set-active">
										Set at active                                         
										<input type="checkbox" data-id="<?php echo $rowPlanned['idSession']; ?>"
											   name="onoffswitch<?php echo $rowPlanned['idSession'] ?>"
											   id="myonoffswitch<?php echo $rowPlanned['idSession'] ?>"
											   class="clickSetActive toogleswitch" <?php if ((int)$rowPlanned['active'] == 1) echo "checked" ?> >

									</span>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="col-6">
                                    <h1 class="txt-title">
                                        <a href="<?php echo Yii::app()->getBaseUrl(); ?>/observation/detail/id/<?php echo $rowPlanned['idSession'] ?>">
                                            <?php echo CHtml::encode($rowPlanned['title']) ?>
                                        </a>
                                    </h1>

                                    <p><?php echo $rowPlanned['description'] ?></p>
                                    <a href="<?php echo Yii::app()->getBaseUrl(); ?>/observation/detail/id/<?php echo $rowPlanned['idSession'] ?>">
                                        <span class="circle"></span>
                                        <span class="circle"></span>
                                        <span class="circle"></span>
                                    </a>
                                </div>
                                <div class="col-6 text-align-right">
                                    <span class="txt-topic"><?php echo CHtml::encode($rowPlanned['name']) ?></span>

                                    <div class="row-fluid">
                                        <div class="dropdown float-right">
                                            <?php if ((int)$rowPlanned['numArchive'] > 0) { ?>
                                                <a href="javascript:void(0);" class="btn-icon document-acitve"
                                                   id="docListOf_<?php echo $rowPlanned['idSession'] ?>" data-toggle="dropdown">
                                                    <b><?php echo $rowPlanned['numArchive'] ?></b>
                                                    <span>Documents</span>
                                                </a>
                                                <div class="showDropdownCSS usersAndDocBox" aria-labelledby="docListOf_<?php echo $rowPlanned['idSession'] ?>">
                                                    <?php foreach ($rowPlanned['archives'] as $docItem) { ?>
                                                        <a href="<?php echo $archivePath . $docItem['path'] ?>" target="_blank">
                                                            <p><?php echo $docItem['name'] ?></p>
                                                        </a>
                                                    <?php } ?>
                                                </div>
                                            <?php } else { ?>
                                                <a class="btn-icon document-deacitve">
                                                    <b>0</b>
                                                    <span>Document</span>
                                                </a>
                                            <?php } ?>
                                        </div>

                                        <div class="dropdown float-right">
                                            <?php if (count($rowPlanned['invitedUsers']) > 0) { ?>
                                                <a href="javascript:void(0);" class="btn-icon document-acitve"
                                                   id="userListOf_<?php echo $rowPlanned['idSession'] ?>" data-toggle="dropdown">
                                                    <b><?php echo count($rowPlanned['invitedUsers']) ?></b>
                                                    <span>Invited</span>
                                                </a>
                                                <div class="showDropdownCSS usersAndDocBox" aria-labelledby="userListOf_<?php echo $rowPlanned['idSession'] ?>">
                                                    <?php foreach ($rowPlanned['invitedUsers'] as $userItem) { ?>
                                                        <p><?php echo $userItem['firstName']. " ".$userItem['lastName'] ?></p>
                                                    <?php } ?>
                                                </div>
                                            <?php } else { ?>
                                                <a class="btn-icon document-deacitve">
                                                    <b>0</b>
                                                    <span>Invited</span>
                                                </a>
                                            <?php } ?>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                            </div>
                            <?php if ((int)$rowPlanned['numComment'] > 0) { ?>
                                <div class="col-12 number-comment">
                                    <a href="<?php echo Yii::app()->getBaseUrl(); ?>/observation/detail/id/<?php echo $rowPlanned['idSession'] ?>">
                                        <?php echo $rowPlanned['numComment'] ?> comments
                                    </a>
                                </div>
                            <?php } ?>
                            <div class="clearfix"></div>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>
            <!-- tab 2 -->
            <div class="tab-pane" id="portlet_tab2">
                <?php if (isset($dataPastPlanning)) { ?>
                    <?php foreach ($dataPastPlanning as $rowPast) { ?>
                        <div class="row-observation row-content">
                            <div class="col-12 cus-option">
                                <div class="col-6">
                                    <a class="img-avartar">
                                        <?php if ($rowPast['avatarPath'] != "" && $rowPast['avatarPath'] != null) {
                                            $avatarUserCreateSessionPast = Yii::app()->params['avatarFolderPath'] . '/' . $rowPast['avatarPath'];
                                        } else {
                                            $avatarUserCreateSessionPast = Yii::app()->params['avatarDefault'];
                                        } ?>
                                        <img
                                            src="<?php echo Yii::app()->getBaseUrl() . $avatarUserCreateSessionPast ?>"/>
                            <span>
                                <?php echo $rowPast['firstName'] . " " . $rowPast['lastName'] ?>
                            </span>
                                    </a>
                                </div>
                                <div class="col-6 text-align-right">
                                    <span><?php echo $rowPast['timeElapse']; ?></span>

                                </div>
                            </div>
                            <div class="col-12">
                                <div class="col-6">
                                    <h1 class="txt-title">
                                        <a href="<?php echo Yii::app()->getBaseUrl(); ?>/observation/pastDetail/id/<?php echo $rowPast['idSession'] ?>">
                                            <?php echo CHtml::encode($rowPast['title']) ?>
                                        </a>
                                    </h1>

                                    <p><?php echo nl2br(CHtml::encode($rowPast['description'])) ?></p>
                                    <a href="<?php echo Yii::app()->getBaseUrl(); ?>/observation/pastDetail/id/<?php echo $rowPast['idSession'] ?>">
                                        <span class="circle"></span>
                                        <span class="circle"></span>
                                        <span class="circle"></span>
                                    </a>
                                </div>
                                <div class="col-6 text-align-right">
                                    <span class="txt-topic"><?php echo CHtml::encode($rowPast['name']) ?></span>

                                    <div class="row-fluid">
                                        <div class="dropdown float-right">
                                            <?php if ((int)$rowPast['numArchive'] > 0) { ?>
                                                <a href="javascript:void(0);" class="btn-icon document-acitve"
                                                   id="docListOf_<?php echo $rowPast['idSession'] ?>" data-toggle="dropdown">
                                                    <b><?php echo $rowPast['numArchive'] ?></b>
                                                    <span>Documents</span>
                                                </a>
                                                <div class="showDropdownCSS usersAndDocBox" aria-labelledby="docListOf_<?php echo $rowPast['idSession'] ?>">
                                                    <?php foreach ($rowPast['archives'] as $docItem) { ?>
                                                        <a href="<?php echo $archivePath . $docItem['path'] ?>" target="_blank">
                                                            <p><?php echo $docItem['name'] ?></p>
                                                        </a>
                                                    <?php } ?>
                                                </div>
                                            <?php } else { ?>
                                                <a class="btn-icon document-deacitve">
                                                    <b>0</b>
                                                    <span>Document</span>
                                                </a>
                                            <?php } ?>
                                        </div>

                                        <div class="dropdown float-right">
                                            <?php if (count($rowPast['invitedUsers']) > 0) { ?>
                                                <a href="javascript:void(0);" class="btn-icon document-acitve"
                                                   id="userListOf_<?php echo $rowPast['idSession'] ?>" data-toggle="dropdown">
                                                    <b><?php echo count($rowPast['invitedUsers']) ?></b>
                                                    <span>Invited</span>
                                                </a>
                                                <div class="showDropdownCSS usersAndDocBox" aria-labelledby="userListOf_<?php echo $rowPast['idSession'] ?>">
                                                    <?php foreach ($rowPast['invitedUsers'] as $userItem) { ?>
                                                        <p><?php echo $userItem['firstName']. " ".$userItem['lastName'] ?></p>
                                                    <?php } ?>
                                                </div>
                                            <?php } else { ?>
                                                <a class="btn-icon document-deacitve">
                                                    <b>0</b>
                                                    <span>Invited</span>
                                                </a>
                                            <?php } ?>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                            </div>
                            <?php if ((int)$rowPast['numComment'] > 0) { ?>
                                <div class="col-12 number-comment">
                                    <a href="<?php echo Yii::app()->getBaseUrl(); ?>/observation/pastDetail/id/<?php echo $rowPast['idSession'] ?>">
                                        <?php echo $rowPast['numComment'] ?> comments
                                    </a>
                                </div>
                            <?php } ?>
                            <div class="clearfix"></div>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<!-- -->
<!------------------ button add and dialog+ ------->
<a href="<?php echo Yii::app()->getBaseUrl(); ?>/sessions/create" class="btn-add new-comment">
    <span><i class="fa fa-plus"></i></span>
</a>
