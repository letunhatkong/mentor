<!-- -->
<div class="col-12 margin-top">
    <div class="row-fluid" id="containerMessage">
        <?php if (isset($dataMessages)) { ?>
            <?php foreach ($dataMessages as $message) { ?>
                <div class="row-observation row-content" id="message_index_<?php echo $message["idMessage"] ?>">
                    <div class="col-12">
                        <div class="col-6">
                            <a class="img-avartar">
                                <?php if ($message['avatarPath'] != "" && $message['avatarPath'] != null) {
                                    $avatarUserCreateMessage = Yii::app()->params['avatarFolderPath'] . '/' . $message['avatarPath'];
                                } else {
                                    $avatarUserCreateMessage = Yii::app()->params['avatarDefault'];
                                } ?>
                                <img src="<?php echo Yii::app()->getBaseUrl() . $avatarUserCreateMessage ?>">
                                <span><?php echo $message['firstName'] . ' ' . $message['lastName'] ?></span>
                            </a>
                        </div>
                        <div class="col-6 text-align-right">
                            <span><?php echo date('d.m.Y', strtotime($message['dateCreate'])); ?></span>
                        </div>
                    </div>

                    <div class="col-12">
                        <p class="nd-messages"><?php echo $message['content'] ?></p>

                        <div class="row-fluid text-align-right">
                            <a href="#CreateMessages" role="button" class="btn btn-primary replyMessage"
                               data-toggle="modal" data-backdrop="static" data-keyboard="false" 
                               data-id-reply="<?php echo $message['idMessage'] ?>">Reply</a>
                            <?php if ((int)$message['countInvited'] > 1) { ?>
                                <a href="#CreateMessages" role="button" class="btn btn-primary replyAllMessage"
                                   data-toggle="modal" data-backdrop="static" data-keyboard="false" 
                                   data-id-reply="<?php echo $message['idMessage'] ?>">Reply all</a>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="clearfix"></div>

                    <?php if (count($message['messageReplyList']) > 0) { ?>
                        <div class="col-12 linepagebreak"></div>
                        <?php foreach ($message['messageReplyList'] as $messageReplyItem) { ?>
                            <div class="col-12">
                                <div class="col-6">
                                    <a class="img-avartar">
                                        <?php if ($messageReplyItem['avatarPath'] != "" && $messageReplyItem['avatarPath'] != null) {
                                            $avatarUserCreateMessageReplyItem = Yii::app()->params['avatarFolderPath'] . '/' . $messageReplyItem['avatarPath'];
                                        } else {
                                            $avatarUserCreateMessageReplyItem = Yii::app()->params['avatarDefault'];
                                        } ?>
                                        <img
                                            src="<?php echo Yii::app()->getBaseUrl() . $avatarUserCreateMessageReplyItem ?>">
                                        <span><?php echo $messageReplyItem['firstName'] . ' ' . $messageReplyItem['lastName'] ?></span>
                                    </a>
                                </div>
                                <div class="col-6 text-align-right">
                                    <span><?php echo date('d.m.Y', strtotime($messageReplyItem['dateCreate'])); ?></span>
                                </div>
                            </div>
                            <div class="col-12">
                                <p class="nd-messages"><?php echo $messageReplyItem['content'] ?></p>
                            </div>
                            <div class="clearfix"></div>
                        <?php } ?>
                    <?php } ?>

                </div>
            <?php } ?>
        <?php } ?>
    </div>
    <!-- # containerMessage -->
</div>
<!-- -->
<a href="#CreateMessages" role="button" class="btn-add new-comment" data-toggle="modal" data-backdrop="static" data-keyboard="false">
    <span><i class="fa fa-plus"></i></span>
</a>
<div id="CreateMessages" class="modal hide" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1"
     aria-hidden="true">
    <div class="modal-header">
        <a class="img-avartar">
            <img src="<?php echo Yii::app()->getBaseUrl() . Yii::app()->user->avatarPath ?>"/>
            <span><?php echo Yii::app()->user->firstName . " " . Yii::app()->user->lastName; ?></span>
        </a>
    </div>
    <div class="modal-body-reply">
        <form id="createMessagesForm" name="createMessagesForm" method="post" enctype="multipart/form-data"
              action="<?php echo Yii::app()->getBaseUrl() ?>/messages/create">
            <div class="row-fluid">
                <select name='usersInvited[]' id="usersInvited" data-placeholder="Your Favorite Teams"
                        class="chosen span6" multiple="multiple" tabindex="6" size="5">
                    <option value=""></option>
                    <?php if (isset($usersInviteMessages)) { ?>
                        <?php foreach ($usersInviteMessages as $user) { ?>
                            <option
                                value="<?php echo (int)$user['idUser']; ?>"><?php echo $user['firstName'] . ' ' . $user['lastName']; ?></option>
                        <?php } ?>
                    <?php } ?>
                </select>
            </div>
            <div class="row-fluid border-top">
                <textarea class="span12" rows="3" placeholder="Text input here..." name="messagesContent"
                          id="messagesContent"></textarea>
                <input type="hidden" id="idMessageReply" name="idMessageReply" value=""/>

                <div class="clearfix"></div>
            </div>
        </form>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true" id="btnCancelMessage">Cancel</button>
        <a class="btn btn-primary" id="createMessagesButton">OK</a>
    </div>
</div>
<script>
	$(document).ready(function() {
		$('#CreateMessages').on('shown.bs.modal', function () {
			$('#messagesContent').focus();
		});
		
		$("#btnCancelMessage").click(function() {
			// close modal popup
			$("#CreateMessages").modal('hide');
			
			// hide keyboard on iOS
			$("#messagesContent").focus().blur();
			
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