<?php $currentUserId = Yii::app()->user->idUser;
    $archivePath = Yii::app()->getBaseUrl(). Yii::app()->params['archiveFolderPath'] . '/';
?>
<div class="col-12 margin-top">
    <div class="tabbable portlet-tabs">
        <ul class="nav-tabs">
            <li class="col-4 active"><a href="#portlet_tab1" data-toggle="tab">Planned</a></li>
            <li class="col-4">
                <a href="#portlet_tab2" data-toggle="tab">
                    <span>Active </span>
                    <?php if (isset($dataActive) && count($dataActive) > 0) { ?>
                        <span class="numActivatedSession"><?php echo count($dataActive); ?></span>
                    <?php } ?>
                </a>
            </li>
            <li class="col-4"><a href="#portlet_tab3" data-toggle="tab">Past</a></li>
        </ul>

        <div class="tab-content">
            <!-- tab 1 - Planned -->
            <div class="tab-pane active" id="portlet_tab1">
                <div class="row-fluid">
                    <?php if (isset($dataPlanned)) { ?>
                        <?php foreach ($dataPlanned as $rowPlanned) { ?>
                            <div class="row-observation row-content">                                
                                <div class="col-12 cus-option">
                                    <div class="col-6">
                                        <a class="img-avartar">
                                            <?php
                                            if ($rowPlanned['avatarPath'] != "" && $rowPlanned['avatarPath'] != null) {
                                                $avatarUserCreateSessionPlanned = Yii::app()->params['avatarFolderPath'] . '/' . $rowPlanned['avatarPath'];
                                            } else {
                                                $avatarUserCreateSessionPlanned = Yii::app()->params['avatarDefault'];
                                            }
                                            ?>
                                            <img
                                                src="<?php echo Yii::app()->getBaseUrl() . $avatarUserCreateSessionPlanned ?>"
                                                alt="">
                                        <span>
                                            <?php echo $rowPlanned['firstName'] . " " . $rowPlanned['lastName'] ?>
                                        </span>
                                        </a>
                                    </div>
                                    <div class="col-6 text-align-right">
										<?php if ($currentUserId == $rowPlanned['idUserCreate']) { ?>
											<a href="<?php echo Yii::app()->getBaseUrl(); ?>/sessions/edit/id/<?php echo $rowPlanned['idSession'] ?>">
												<span class="editSessionIcon"><i class="fa fa-pencil"></i></span>
											</a>
										<?php } ?>
                                        <span class="count-tim "><?php echo $rowPlanned['timeElapse']; ?></span>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="col-8">
                                        <h1 class="txt-title">
                                            <?php $detailLink = 'javascript:void(0);';
                                                $showPleaseJoin = ' data-toggle="modal" data-target="#pleaseJoinModal" ';
                                                $checkIsInvited = false;
                                                foreach($rowPlanned['invitedUsers'] as $inviteUser) {
                                                    if ($inviteUser['idUserInvited'] == $currentUserId) {
                                                        $checkIsInvited = true;
                                                        break;
                                                    }
                                                }
                                                if($currentUserId == $rowPlanned['idUserCreate'] || $checkIsInvited) {
                                                    $detailLink = Yii::app()->getBaseUrl() . '/observation/plannedDetail/id/' . $rowPlanned['idSession'];
                                                    $showPleaseJoin = '';
                                                }
                                            ?>
                                            <a href="<?php echo $detailLink ?>" <?php echo $showPleaseJoin ?>>
                                                <?php echo $rowPlanned['title'] ?>
                                            </a>
                                        </h1>

                                        <p><?php echo nl2br(CHtml::encode($rowPlanned['description'])) ?></p>
                                        <a href="<?php echo $detailLink ?>" <?php echo $showPleaseJoin ?>>
                                            <span class="circle"></span>
                                            <span class="circle"></span>
                                            <span class="circle"></span>
                                        </a>
                                    </div>
                                    <div class="col-4 text-align-right">
                                        <span class="txt-topic"><?php echo $rowPlanned['name'] ?></span>

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
                                        <a href="<?= $detailLink ?>" <?= $showPleaseJoin ?>>
                                            <?php echo $rowPlanned['numComment'] ?> comments
                                        </a>
                                    </div>
                                <?php } ?>
                                <div class="clearfix"></div>
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
            <!-- # tab 1 - Planned-->

            <!-- tab 2 - Active Detail -->
            <div class="tab-pane" id="portlet_tab2">
                <div class="row-fluid">
                    <?php if (isset($dataActive)) { ?>
                        <?php foreach ($dataActive as $activeItem) { ?>
                            <div class="row-observation row-content">                               
                                <div class="col-12  cus-option">
                                    <div class="col-6">
                                        <a class="img-avartar">
                                            <?php
                                            if ($activeItem['avatarPath'] != "" && $activeItem['avatarPath'] != null) {
                                                $avatarUserCreateSessionPlanned = Yii::app()->params['avatarFolderPath'] . '/' . $activeItem['avatarPath'];
                                            } else {
                                                $avatarUserCreateSessionPlanned = Yii::app()->params['avatarDefault'];
                                            }
                                            ?>
                                            <img
                                                src="<?php echo Yii::app()->getBaseUrl() . $avatarUserCreateSessionPlanned ?>"
                                                alt="">
                                        <span>
                                            <?php echo $activeItem['firstName'] . " " . $activeItem['lastName'] ?>
                                        </span>
                                        </a>
                                    </div>
                                    <div class="col-6 text-align-right">
										 <?php if ($currentUserId == $activeItem['idUserCreate']) { ?>
											<a href="<?php echo Yii::app()->getBaseUrl(); ?>/sessions/edit/id/<?php echo $activeItem['idSession'] ?>">
												<span class="editSessionIcon"><i class="fa fa-pencil"></i></span>
											</a>
										<?php } ?>
                                        <span class="count-time "><?php echo $activeItem['timeElapse']; ?></span>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="col-8">

                                        <h1 class="txt-title">
                                            <?php $detailLink = 'javascript:void(0);';
                                            $showPleaseJoin = ' data-toggle="modal" data-target="#pleaseJoinModal" ';
                                            $checkIsInvited = false;
                                            foreach($activeItem['invitedUsers'] as $inviteUser) {
                                                if ($inviteUser['idUserInvited'] == $currentUserId) {
                                                    $checkIsInvited = true;
                                                    break;
                                                }
                                            }
                                            if($currentUserId == $activeItem['idUserCreate'] || $checkIsInvited) {
                                                $detailLink = Yii::app()->getBaseUrl() . '/observation/detail/id/' . $activeItem['idSession'];
                                                $showPleaseJoin = '';
                                            }
                                            ?>
                                            <a href="<?= $detailLink ?>" <?= $showPleaseJoin ?>>
                                                <?= $activeItem['title'] ?>
                                            </a>
                                        </h1>

                                        <p><?php echo nl2br(CHtml::encode($activeItem['description'])) ?></p>
                                        <a href="<?php echo Yii::app()->getBaseUrl(); ?>/observation/detail/id/<?php echo $activeItem['idSession'] ?>">
                                            <span class="circle"></span>
                                            <span class="circle"></span>
                                            <span class="circle"></span>
                                        </a>
                                    </div>
                                    <div class="col-4 text-align-right">
                                        <span class="txt-topic"><?php echo $activeItem['name'] ?></span>

                                        <div class="row-fluid">
                                            <div class="dropdown float-right">
                                            <?php if ((int)$activeItem['numArchive'] > 0) { ?>
                                                <a href="javascript:void(0);" class="btn-icon document-acitve"
                                                   id="docListOf_<?php echo $activeItem['idSession'] ?>" data-toggle="dropdown">
                                                    <b><?php echo $activeItem['numArchive'] ?></b>
                                                    <span>Documents</span>
                                                </a>
                                                <div class="showDropdownCSS usersAndDocBox" aria-labelledby="docListOf_<?php echo $activeItem['idSession'] ?>">
                                                    <?php foreach ($activeItem['archives'] as $docItem) { ?>
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
                                            <?php if (count($activeItem['invitedUsers']) > 0) { ?>
                                                <a href="javascript:void(0);" class="btn-icon document-acitve"
                                                   id="userListOf_<?php echo $activeItem['idSession'] ?>" data-toggle="dropdown">
                                                    <b><?php echo count($activeItem['invitedUsers']) ?></b>
                                                    <span>Invited</span>
                                                </a>
                                                <div class="showDropdownCSS usersAndDocBox" aria-labelledby="userListOf_<?php echo $activeItem['idSession'] ?>">
                                                    <?php foreach ($activeItem['invitedUsers'] as $userItem) { ?>
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
                                <?php if ((int)$activeItem['numComment'] > 0) { ?>
                                    <div class="col-12 number-comment">
                                        <a href="<?= $detailLink ?>" <?= $showPleaseJoin ?>>
                                            <?= $activeItem['numComment'] ?> comments
                                        </a>
                                    </div>
                                <?php } ?>
                                <div class="clearfix"></div>
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
            <!-- # tab 2 - Active Detail -->

            <!-- tab 3 - Past Detail -->
            <div class="tab-pane" id="portlet_tab3">
            <?php if (isset($dataPast)) { ?>
                <?php foreach ($dataPast as $rowPast) { ?>
                    <div class="row-observation row-content">
                        <div class="col-12">
                            <div class="col-8">
                                <a class="img-avartar">
                                    <?php
                                    if ($rowPast['avatarPath'] != "" && $rowPast['avatarPath'] != null) {
                                        $avatarUserCreateSessionPast = Yii::app()->params['avatarFolderPath'] . '/' . $rowPast['avatarPath'];
                                    } else {
                                        $avatarUserCreateSessionPast = Yii::app()->params['avatarDefault'];
                                    }
                                    ?>
                                    <img src="<?php echo Yii::app()->getBaseUrl() . $avatarUserCreateSessionPast ?>"
                                         alt="">
                                    <span>
                                        <?php echo $rowPast['firstName'] . " " . $rowPast['lastName'] ?>
                                    </span>
                                </a>
                            </div>
                            <div class="col-4 text-align-right "><span><?php echo $rowPast['timeElapse'] ?></span></div>
                        </div>
                        <div class="col-12">
                            <div class="col-8">
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
                            <div class="col-4 text-align-right">
                                <span class="txt-topic"><?php echo $rowPast['name'] ?></span>

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
            <!-- # tab 3 - Past Detail -->

        </div>
        <!-- # tab-content -->
    </div>
    <!-- # portlet tab -->
</div>

<div id="pleaseJoinModal" class="modal fade">
    <p>Please join to this session!</p>
</div>