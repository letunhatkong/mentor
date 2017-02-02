<!-- -->
<?php if(isset($dataPlannedDetail)) { ?>
<div class="col-12 margin-top">
    <div class="row-fluid">
        <div class="row-observation row-content">
            <div class="col-12">
                <div class="col-6">
                    <a class="img-avartar">
                        <?php if ($dataPlannedDetail['avatarPath'] != "" && $dataPlannedDetail['avatarPath'] != null) {
                            $avatar = Yii::app()->params['avatarFolderPath'] . '/' . $dataPlannedDetail['avatarPath'];
                        } else {
                            $avatar = Yii::app()->params['avatarDefault'];
                        } ?>
                        <img src="<?php echo Yii::app()->getBaseUrl() . $avatar ?>" alt="">
                            <span>
                                <?php echo $dataPlannedDetail['firstName'] ." ". $dataPlannedDetail['lastName'] ?>
                            </span>
                    </a>
                </div>
                <div class="col-6 text-align-right">
                    <span>
                        <?php echo date("d.m.Y", strtotime($dataPlannedDetail['datePost'])) ?><i
                            class="fa fa-calendar-o"></i>
                    </span>
                </div>
            </div>

            <!-- Document -->
            <div class="col-12">
                <div class="col-nd-topic text-align-right">
                    <span class="txt-topic"><?php echo $dataPlannedDetail['name'] ?></span>

                    <div class="row-fluid">
                        <?php if ((int)$dataPlannedDetail['numArchive'] > 0) { ?>
                            <a class="btn-icon document-acitve">
                                <b><?php echo $dataPlannedDetail['numArchive'] ?></b>
                                <span>Documents</span>
                            </a>
                        <?php } else { ?>
                            <a class="btn-icon document-deacitve">
                                <b>0</b>
                                <span>Documents</span>
                            </a>
                        <?php } ?>
                    </div>
                </div>
                <div class="col-nd-title">
                    <h1 class="txt-title"><?php echo CHtml::encode($dataPlannedDetail['title']) ?></h1>

                    <p class="txt-mg"><?php echo CHtml::encode($dataPlannedDetail['description']) ?></p>
                    <div class="dropdown">
                        <a href="javascript:void(0);" data-toggle="dropdown">
                            <span class="circle"></span>
                            <span class="circle"></span>
                            <span class="circle"></span>
                        </a>
                        <div class="showDropdownCSS">
                            <?php if (isset($documentNameDetail)) { ?>
                                <?php foreach ($documentNameDetail as $row) { ?>
                                    <div >
                                        <?php if ($row['typeArchive'] === 'link') {
                                            $hrefArchive = $row["link"];
                                            $icon = "fa-link";
                                        } else {
                                            $hrefArchive = Yii::app()->getBaseUrl() . '/archives/download/file/' . $row["path"];
                                            $icon = "fa-file";
                                        } ?>
                                        <a href="<?php echo $hrefArchive; ?>" class="file-dowload">
                                            <i class="fa <?php echo $icon ?>"></i>
                                            <span><?php echo $row['name']; ?></span>
                                        </a>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- # Document -->

            <div class="col-12 title-comment containerComment" id="containerComment<?php echo $dataPlannedDetail['idSession']; ?>">
                <div class="row-fluid">
                    <div class="timeline-messages "></div>
                </div>
                <?php if (isset($dataPlannedComment)) { ?>
                    <?php foreach ($dataPlannedComment as $itemComment) {
                        if ((int)$itemComment['idCommentParent'] == 0) { ?>
                            <div class="row-fluid row-fluid-top" id="commentRow<?php echo $itemComment['idComment'] ?>">
                                <div class="col-12">
                                    <div class="col-6">
                                        <?php if ($itemComment['contentMediaType'] == "TEXT") { ?>
                                            <i class="fa fa-comment fa-title"></i>
                                        <?php } elseif ($itemComment['contentMediaType'] == "VIDEO") { ?>
                                            <i class="fa fa-video-camera fa-title"></i>
                                        <?php } else { ?>
                                            <i class="fa fa-camera fa-title"></i>
                                        <?php } ?>
                                        <a class="img-avartar">
                                            <?php
                                            if ($itemComment['avatarPath'] != "" && $itemComment['avatarPath'] != null) {
                                                $avatar = Yii::app()->params['avatarFolderPath'] . '/' . $itemComment['avatarPath'];
                                            } else {
                                                $avatar = Yii::app()->params['avatarDefault'];
                                            }
                                            ?>
                                            <img alt="" src="<?php echo Yii::app()->getBaseUrl() . $avatar ?>"/>
                                            <span><?php echo $itemComment['firstName'] ." ". $itemComment['lastName'] ?></span>
                                        </a>

                                    </div>
                                    <div class="col-6 text-align-right  count-time">
                                <span>
                                    <?php echo $itemComment['timeElapse'] ?>
                                </span>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <div class=" timeline-messages">
                                    <div class="msg-time-chat">
                                        <div class="message-body">
                                            <p>
                                                <?php
                                                if ($itemComment['contentMediaType'] == "TEXT") {
                                                    echo nl2br(CHtml::encode($itemComment['content']));
                                                } elseif ($itemComment['contentMediaType'] == "VIDEO") {
                                                    ?>
                                                    <output>
                                                        <video class="" controls>
                                                            <source
                                                                src="<?php echo Yii::app()->getBaseUrl() . Yii::app()->params['fileVideoFolderPath'] . '/' . $itemComment['contentMediaPath'] ?>"
                                                                type="video/mp4">
                                                            Your browser does not support HTML5 video.
                                                        </video>
                                                    </output>
                                                <?php
                                                } else {
                                                    ?>
                                                    <img
                                                        src="<?php echo Yii::app()->getBaseUrl() . Yii::app()->params['fileImagesFolderPath'] . '/' . $itemComment['contentMediaPath'] ?>"
                                                        alt="image comment" class="video"/>
                                                <?php } ?>
                                            </p>
                                            <div class="clearfix"></div>
                                            <div class="like-comment">
                                            <?php if (isset($dataSetting)) { ?>
                                                <?php if ($dataSetting->allowLike == 1) { ?>
                                                    <a cmtId="<?php echo $itemComment['idComment'] ?>"
                                                       class="clickLike">Like</a>
                                                <?php } ?>

                                                <?php if ($dataSetting->allowComment == 1) { ?>
                                                    <a href="#commentText" data-toggle="modal" data-backdrop="static" data-keyboard="false" class="buttonCommentText"
                                                       value-id="<?php echo $itemComment['idComment'] ?>">
                                                        Comment
                                                    </a>
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
                                                            <a href="#commentText" data-toggle="modal" data-backdrop="static" data-keyboard="false"
                                                               class="clickEditComment"
                                                               data-comment-id="<?php echo $itemComment['idComment'] ?>">
                                                                <i class="fa fa-pencil"></i> Edit
                                                            </a>
                                                        <?php } elseif ($itemComment['contentMediaType'] == "PICTURE") { ?>
                                                            <a href="#myCamera" data-toggle="modal" data-backdrop="static" data-keyboard="false" 
                                                               class="clickEditImageComment"
                                                               data-image-comment-id="<?php echo $itemComment['idComment'] ?>">
                                                                <i class="fa fa-pencil"></i> Edit
                                                            </a>
                                                        <?php } elseif ($itemComment['contentMediaType'] == "VIDEO") { ?>
                                                            <a href="#myVideo" data-toggle="modal" data-backdrop="static" data-keyboard="false" 
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
                                            <div class="sub-cmt" id="containerCommentReply<?php echo $itemComment['idComment'] ?>">
                                                <?php foreach ($dataPlannedComment as $itemReplyComment) {
                                                    if ($itemReplyComment['idCommentParent'] == $itemComment['idComment']) {
                                                        ?>
                                                        <div class="row-fluid" id="commentRow<?php echo $itemReplyComment['idComment'] ?>">
                                                            <div class="col-12">
                                                                <i class="fa fa-comment float-left"></i>
                                                                <a class="img-avartar">
                                                                    <?php
                                                                    if ($itemReplyComment['avatarPath'] != "" && $itemReplyComment['avatarPath'] != null) {
                                                                        $avatar = Yii::app()->params['avatarFolderPath'] . '/' . $itemReplyComment['avatarPath'];
                                                                    } else {
                                                                        $avatar = Yii::app()->params['avatarDefault'];
                                                                    }
                                                                    ?>
                                                                    <img alt=""
                                                                         src="<?php echo Yii::app()->getBaseUrl() . $avatar ?>"/>
                                                                    <span><?php echo $itemReplyComment['firstName'] ." ". $itemReplyComment['lastName'] ?></span>
                                                                </a>
                                                            </div>
                                                            <div class="clearfix"></div>
                                                            <div class="messages-video">
                                                                <div class="msg-time-chat">
                                                                    <div class="message-body msg-in">
                                                                        <p><?php echo nl2br(CHtml::encode($itemReplyComment['content'])) ?></p>
                                                                        <?php if(Yii::app()->user->idUser == $itemReplyComment['idUserComment']) { ?>
                                                                            <div class="editDeleteCmtWrapper">
                                                                                <div class="editCmt">
                                                                                    <?php if ($itemReplyComment['contentMediaType'] == "TEXT") { ?>
                                                                                        <a href="#commentText" data-toggle="modal" data-backdrop="static" data-keyboard="false" 
                                                                                           class="clickEditComment"
                                                                                           data-comment-id="<?php echo $itemReplyComment['idComment'] ?>">
                                                                                            <i class="fa fa-pencil"></i> Edit
                                                                                        </a>
                                                                                    <?php } elseif ($itemReplyComment['contentMediaType'] == "PICTURE") { ?>
                                                                                        <a href="#myCamera" data-toggle="modal"
                                                                                           class="clickEditImageComment"
                                                                                           data-image-comment-id="<?php echo $itemReplyComment['idComment'] ?>">
                                                                                            <i class="fa fa-pencil"></i> Edit
                                                                                        </a>
                                                                                    <?php } elseif ($itemReplyComment['contentMediaType'] == "VIDEO") { ?>
                                                                                        <a href="#myVideo" data-toggle="modal"
                                                                                           class="clickEditVideoComment"
                                                                                           data-comment-id="<?php echo $itemReplyComment['idComment'] ?>">
                                                                                            <i class="fa fa-pencil"></i> Edit
                                                                                        </a>
                                                                                    <?php } ?>
                                                                                </div>
                                                                                <div class="delCmt">
                                                                                    <a class="clickDeleteComment"
                                                                                       data-delete-id="<?php echo $itemReplyComment['idComment'] ?>">
                                                                                        <i class="fa fa-trash"></i> Delete
                                                                                    </a>
                                                                                </div>
                                                                            </div>
                                                                        <?php } ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php }
                                                } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php }
                    }
                } ?>
            </div>
            <div class="clearfix"></div>
        </div>
        <!-- # row-observation row-content -->
    </div>
</div>

<!------ button add and dialog+ ------->
<?php if(isset($dataSetting)) { ?>
    <?php if ($dataSetting->allowComment == 1) { ?>
        <a class="btn-add btn-total">
            <span><i class="fa fa-plus"></i></span>
        </a>
        <a class="btn-add btn-close display-none">
            <span><i class="fa fa-remove"></i></span>
        </a>
        <a href="#myVideo" role="button" data-toggle="modal" data-backdrop="static" data-keyboard="false" class="btn-add btn-video display-none">
            <span><i class="fa fa-video-camera"></i></span>
        </a>
        <a href="javascript:void(0);" role="button" class="btn-add btn-video-app display-none" id="uploadVideoMobile">
            <span><i class="fa fa-video-camera"></i></span>
        </a>
        <a href="#commentText" role="button" data-toggle="modal" data-backdrop="static" data-keyboard="false" class="btn-add btn-comment display-none">
            <span><i class="fa fa-comment"></i></span>
        </a>
        <a href="#myCamera" role="button" data-toggle="modal" data-backdrop="static" data-keyboard="false" class="btn-add btn-camera display-none">
            <span><i class="fa fa-camera"></i></span>
        </a>
        <a href="javascript:void(0);" role="button" class="btn-add btn-camera-app display-none" id="uploadPictureMobile">
            <span><i class="fa fa-camera"></i></span>
        </a>


        <div id="commentText" class="modal hide" tabindex="-1" role="dialog" aria-labelledby="commentTextLabel"
             aria-hidden="true">
            <form id="commentTextForm">
                <input type="hidden" value="<?php echo $dataPlannedDetail['idSession'] ?>" id="idSessionCommentText"
                       name="idSessionCommentText"/>

                <div class="modal-header">
                    <a class="img-avartar">
                        <img src="<?php echo Yii::app()->getBaseUrl() . Yii::app()->user->avatarPath; ?>"/>
                        <span><?php echo Yii::app()->user->firstName . " " . Yii::app()->user->lastName ?></span>
                    </a>
                </div>
                <div class="modal-body">
                    <textarea id="contentCommentText" name="contentCommentText" class="span12" rows="3"
                              placeholder="Text input here..."></textarea>
                </div>
                <div class="modal-footer">
                    <button class="btn" data-dismiss="modal" aria-hidden="true" id="btnCancelCommentText">Cancel</button>
                    <a class="btn btn-primary" id="buttonSaveCommentText">OK</a>
                    <input type="hidden" id="parentIdCommentText" name="parentIdCommentText" value="0"/>
                    <input type="hidden" id="editCommentTextId" name="editCommentTextId" value="0"/>
                </div>
            </form>
        </div>

        <div id="myCamera" class="modal hide fade modal-video" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1"
             aria-hidden="true">
            <input type="hidden" value="<?php echo $dataPlannedDetail['idSession'] ?>" name="idSessionCommentImage"
                   id="idSessionCommentImage"/>

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <div class="modal-body">
                    <img src="<?php echo Yii::app()->getBaseUrl() ?>/images/choose_an_image.jpg" id="tmpImage" class="video"
                         alt="Choose an image">
                    <a id="buttonUploadImage" class="btn-upload-avatar">Browse</a>
                    <input type="file" name="imagePath" id="fileUploadImage" class="hide-space" accept="image/*"/>
                    <!--a href="myapp://selectImage" id="selectImageButtonApp" class="display-none"></a-->
                </div>
                <div class="modal-footer">
                    <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
                    <button class="btn btn-primary" id="buttonSaveCommentImage">OK</button>
                    <!--a href="myapp://uploadImage" class="btn btn-primary" id="buttonSaveCommentImageApp">OK</a-->
                    <input type="hidden" name="idCommentImage" id="idCommentImage" value=""/>
                </div>
            </div>
        </div>

        <div id="myVideo" class="modal hide fade modal-video" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1"
             aria-hidden="true">
            <input type="hidden" id="idSessionCommentVideo" name="idSessionCommentVideo"
                   value="<?php echo (int)$dataPlannedDetail['idSession']; ?>"/>

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <div class="modal-body">
                    <div class="row-fluid" id="containerVideoUploadForm">
                        <div class="row-observation row-content">
                            <div class="col-12" id="drop_zone_video_progress">
                                <div class="progress progress-success">
                                    <div style="width: 0;" class="bar"></div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-12">
                                <div class="col-12">
                                    <div class="col-12 drop_zone_video_area">
                                        <div class="col-12" id="drop_zone_video_upload">
                                            Drop video file or click here
                                        </div>
                                        <input type="file" class="default" id="videoBrowser" name="videoBrowser"
                                               accept="video/*"
                                               style="display:none"/>
                                        <a href="myapp://selectVideo" id="selectVideoButtonApp" class="display-none"></a>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-12">
                                        <label class="error" id="videoupload-error"><?php if (isset($errorString)) {
                                                echo $errorString;
                                            } ?></label>
                                    </div>
                                </div>
                                <div class="col-4 drop_zone_video_view" style="display:none">
                                    <output id="drop_zone_video_player">
                                        <video class="col-11" controls autoplay>
                                            <source src="" type="video/mp4">
                                            Your browser does not support HTML5 video.
                                        </video>
                                    </output>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
                    <button class="btn btn-primary" id="uploadVideoButton">Save</button>
                    <a href="myapp://uploadVideo" class="btn btn-primary" id="uploadVideoButtonApp">Save</a>
                    <input type="hidden" id="videoEditCommentId" name="videoEditCommentId" value=""/>
                    <input type="hidden" id="idUserComment" name="idUserComment"
                           value="<?php echo Yii::app()->user->idUser ?>"/>
    <!--                <input type="hidden" id="limitDurationVideo" name="limitDurationVideo"-->
    <!--                       value="--><?php //echo $dataSetting->allowLimitDuration == 1 ? $dataSetting->limitDurationValue : $dataSetting->limitDurationDefault; ?><!--"/>-->
                </div>
            </div>
        </div>
    <?php } ?>

<script type="text/javascript">
	var idSession = <?php echo $dataPlannedDetail['idSession']; ?>;
	var limitDur = <?php echo $dataSetting->allowLimitDuration ==1?$dataSetting->limitDurationValue:$dataSetting->limitDurationDefault; ?>;
	
	$(document).ready(function() {
		$('#commentText').on('shown.bs.modal', function () {
			$('#contentCommentText').focus();
		})
		
		$("#btnCancelCommentText").click(function() {
			// close modal popup
			$("#commentText").modal('hide');
			
			// hide keyboard on iOS
			$("#contentCommentText").focus().blur();
			
			// Scroll to top window when clicked cancel button
			$(window).scrollTop(0);
		});
		
		// iOS check...ugly but necessary
		if( navigator.userAgent.match(/iPad|iPod/i) ) {
			$('.modal').on('show.bs.modal', function() {
				// Position modal absolute and bump it down to the scrollPosition
				$(this)
					.css({
						position: 'absolute',
						//marginTop: $(window).scrollTop() + '50' + 'px',
						//bottom: 'auto'
						bottom:'0px'
					});
				// Position backdrop absolute and make it span the entire page
				// Also dirty, but we need to tap into the backdrop after Boostrap 
				// positions it but before transitions finish.
				setTimeout( function() {
					$('.modal-backdrop').css({
						position: 'absolute', 
						top: 0, 
						left: 0,
						width: '100%',
						height: Math.max(
							document.body.scrollHeight, document.documentElement.scrollHeight,
							document.body.offsetHeight, document.documentElement.offsetHeight,
							document.body.clientHeight, document.documentElement.clientHeight
						) + 'px'
					});
				}, 0);
			});
		}
		
		if( navigator.userAgent.match(/iPhone/i) ) {
			$('.modal').on('show.bs.modal', function() {
				// Position modal absolute and bump it down to the scrollPosition
				$(this)
					.css({
						position: 'absolute',
						marginTop: $(window).scrollTop() + 'px',
						bottom: 'auto'
					});
				// Position backdrop absolute and make it span the entire page
				// Also dirty, but we need to tap into the backdrop after Boostrap 
				// positions it but before transitions finish.
				setTimeout( function() {
					$('.modal-backdrop').css({
						position: 'absolute', 
						top: 0, 
						left: 0,
						width: '100%',
						height: Math.max(
							document.body.scrollHeight, document.documentElement.scrollHeight,
							document.body.offsetHeight, document.documentElement.offsetHeight,
							document.body.clientHeight, document.documentElement.clientHeight
						) + 'px'
					});
				}, 0);
			});
		}
	});
</script>
<?php } ?>

<?php } ?>