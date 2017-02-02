<?php if (isset($itemComment)) { ?>
    <?php if ((int)$itemComment['idCommentParent'] === 0) { ?>
        <div class="row-fluid row-fluid-top" id="commentRow<?php echo $itemComment['idComment'] ?>">
            <div class="clearfix"></div>

            <div class="timeline-messages">
                <div class="col-12 msg-time-chat">
                    <div class="col-6">
                        <?php if ($itemComment['contentMediaType'] == "TEXT") { ?>
                            <i class="fa fa-comment fa-title float-left"></i>
                        <?php } elseif ($itemComment['contentMediaType'] == "VIDEO") { ?>
                            <i class="fa fa-video-camera fa-title float-left"></i>
                        <?php } else { ?>
                            <i class="fa fa-camera fa-title float-left"></i>
                        <?php } ?>
                        <a class="img-avartar">
                            <?php if ($itemComment['avatarPath'] != "" && $itemComment['avatarPath'] != null) {
                                $avatar = Yii::app()->params['avatarFolderPath'] . '/' . $itemComment['avatarPath'];
                            } else {
                                $avatar = Yii::app()->params['avatarDefault'];
                            } ?>
                            <img alt="" src="<?php echo Yii::app()->getBaseUrl() . $avatar ?>"/>
                            <span><?php echo $itemComment['firstName'] . " " . $itemComment['lastName'] ?></span>
                        </a>
                    </div>
                    <div class="col-6 text-align-right count-time">
                        <span><?php echo $itemComment['timeElapse'] ?></span>
                    </div>
                    <div class="message-body">
                        <p>
                            <?php if ($itemComment['contentMediaType'] == "TEXT") {
                                echo nl2br(CHtml::encode($itemComment['content']));
                            } elseif ($itemComment['contentMediaType'] == "VIDEO") { ?>
                                <output>
                                    <video class="" controls>
                                        <source
                                            src="<?php echo Yii::app()->getBaseUrl() . Yii::app()->params['fileVideoFolderPath'] . '/' . $itemComment['contentMediaPath'] ?>"
                                            type="video/mp4">
                                        Your browser does not support HTML5 video.
                                    </video>
                                </output>
                            <?php } else { ?>
                                <img
                                    src="<?php echo Yii::app()->getBaseUrl() . Yii::app()->params['fileImagesFolderPath'] . '/' . $itemComment['contentMediaPath'] ?>"
                                    alt="image comment" class="video"/>
                            <?php } ?>
                        </p>
                        <div class="clearfix"></div>
                        <div class="like-comment">
                            <?php if (isset($dataSetting)) { ?>
                                <?php if ($dataSetting->allowComment == 1) { ?>
                                    <a href="#commentText" data-toggle="modal" class="buttonCommentText"
                                       value-id="<?php echo $itemComment['idComment'] ?>">
                                        Comment
                                    </a>
                                <?php } ?>

                                <?php if ($dataSetting->allowLike == 1) { ?>
                                    <a cmtId="<?php echo $itemComment['idComment'] ?>"
                                       class="clickLike"><?php echo $this->getLikeTxt($itemComment['idComment']) ?></a>
                                <?php } ?>

                                <?php if ($dataSetting->allowLike == 1) { ?>
                                    <a class="listLiker">
                                        <div class="listLiker-div">
                                            <?php $list = explode(",", $itemComment['likedUserList']);
                                            if ($itemComment['likedUserList'] != "" && count($list) >= 1) { ?>
                                                <?php foreach ($list as $name) { ?>
                                                    <p><?php echo $name ?></p>
                                                <?php } ?>
                                            <?php } ?>
                                        </div>
                                        <i class="fa fa-thumbs-up"></i>
                                    <span id="countLike<?php echo $itemComment['idComment'] ?>">
                                        <?php echo $itemComment['countLikes'] ?>
                                    </span>
                                    </a>
                                <?php } ?>
                            <?php } ?>
                        </div>

                        <?php if (Yii::app()->user->idUser == $itemComment['idUserComment']) { ?>
                            <div class="clearfix"></div>
                            <div class="editDeleteCmtWrapper">
                                <div class="editCmt">
                                    <?php if ($itemComment['contentMediaType'] == "TEXT") { ?>
                                        <a href="#commentText" data-toggle="modal"
                                           class="clickEditComment"
                                           data-comment-id="<?php echo $itemComment['idComment'] ?>">
                                            <i class="fa fa-pencil"></i> Edit
                                        </a>
                                    <?php } elseif ($itemComment['contentMediaType'] == "PICTURE") { ?>
                                        <a href="#myCamera" data-toggle="modal"
                                           class="clickEditImageComment"
                                           data-image-comment-id="<?php echo $itemComment['idComment'] ?>">
                                            <i class="fa fa-pencil"></i> Edit
                                        </a>
                                    <?php } elseif ($itemComment['contentMediaType'] == "VIDEO") { ?>
                                        <a href="#myVideo" data-toggle="modal"
                                           class="clickEditVideoComment"
                                           data-comment-id="<?php echo $itemComment['idComment'] ?>">
                                            <i class="fa fa-pencil"></i> Edit
                                        </a>
                                    <?php } ?>
                                </div>
                                <div class="delCmt">
                                    <a class="clickDeleteComment"
                                       data-delete-id="<?php echo $itemComment['idComment'] ?>">
                                        <i class="fa fa-trash"></i> Delete
                                    </a>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
        </div>
    <?php } else { ?>
        <div class="row-fluid" id="commentRow<?php echo $itemComment['idComment'] ?>">
            <div class="clearfix"></div>
            <div class="messages-video">
                <div class="msg-time-chat">
                    <i class="fa fa-comment float-left"></i>
                    <a class="img-avartar">
                        <?php if ($itemComment['avatarPath'] != "" && $itemComment['avatarPath'] != null) {
                            $avatar = Yii::app()->params['avatarFolderPath'] . '/' . $itemComment['avatarPath'];
                        } else {
                            $avatar = Yii::app()->params['avatarDefault'];
                        } ?>
                        <img alt=""
                             src="<?php echo Yii::app()->getBaseUrl() . $avatar ?>"/>
                        <span><?php echo $itemComment['firstName'] . " " . $itemComment['lastName'] ?></span>
                    </a>

                    <div class="message-body msg-in">
                        <p><?php echo nl2br(CHtml::encode($itemComment['content'])) ?></p>
                        <?php if (Yii::app()->user->idUser == $itemComment['idUserComment']) { ?>
                            <div class="editDeleteCmtWrapper">
                                <div class="editCmt">
                                    <?php if ($itemComment['contentMediaType'] == "TEXT") { ?>
                                        <a href="#commentText" data-toggle="modal"
                                           class="clickEditComment"
                                           data-comment-id="<?php echo $itemComment['idComment'] ?>">
                                            <i class="fa fa-pencil"></i> Edit
                                        </a>
                                    <?php } elseif ($itemComment['contentMediaType'] == "PICTURE") { ?>
                                        <a href="#myCamera" data-toggle="modal"
                                           class="clickEditImageComment"
                                           data-image-comment-id="<?php echo $itemComment['idComment'] ?>">
                                            <i class="fa fa-pencil"></i> Edit
                                        </a>
                                    <?php } elseif ($itemComment['contentMediaType'] == "VIDEO") { ?>
                                        <a href="#myVideo" data-toggle="modal"
                                           class="clickEditVideoComment"
                                           data-comment-id="<?php echo $itemComment['idComment'] ?>">
                                            <i class="fa fa-pencil"></i> Edit
                                        </a>
                                    <?php } ?>
                                </div>
                                <div class="delCmt">
                                    <a class="clickDeleteComment"
                                       data-delete-id="<?php echo $itemComment['idComment'] ?>">
                                        <i class="fa fa-trash"></i> Delete
                                    </a>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>

    <?php } ?>
<?php } ?>
