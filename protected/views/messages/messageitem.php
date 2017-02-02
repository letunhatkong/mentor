<?php if (is_array($message)) { ?>

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
                <span><?php echo date('d.m.Y', strtotime($message['dateCreate'])); ?></span></div>
        </div>
        <div class="col-12">
            <p class="nd-messages"><?php echo $message['content'] ?></p>

            <div class="row-fluid text-align-right">
                <a href="#CreateMessages" role="button" class="btn btn-primary replyMessage" data-toggle="modal"
                   data-id-reply="<?php echo $message['idMessage'] ?>">Reply</a>
                <?php if ((int)$message['countInvited'] > 1) { ?>
                    <a href="#CreateMessages" role="button" class="btn btn-primary replyAllMessage" data-toggle="modal"
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
                            <img src="<?php echo Yii::app()->getBaseUrl() . $avatarUserCreateMessageReplyItem ?>">
                            <span><?php echo $messageReplyItem['firstName'] . ' ' . $messageReplyItem['lastName'] ?></span>
                        </a>
                    </div>
                    <div class="col-6 text-align-right">
                        <span><?php echo date('d.m.Y', strtotime($messageReplyItem['dateCreate'])); ?></span></div>
                </div>
                <div class="col-12">
                    <p class="nd-messages"><?php echo $messageReplyItem['content'] ?></p>
                </div>
                <div class="clearfix"></div>
            <?php } ?>
        <?php } ?>
    </div>
<?php
}

