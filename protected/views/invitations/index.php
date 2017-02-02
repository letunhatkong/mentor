<?php if (isset($invitations)): ?>
    <div class="tab-content" id="invitationWrapper">
        <?php foreach ($invitations as $item): ?>
            <div class="row-observation row-content"
                 id="notificationId_<?= $this->checkIsGuest() . '_' . $item['notifyId'] ?>">
                <div class="img-notif">
                    <img src="/upload/avatars/<?= $item['avatarPath'] ?>">
                    <span class="notif-message notif-user"><i class="fa fa-users"></i></span>
                </div>

                <div class="notifi-box">
                    <h4><?= $item['firstName'] . ' ' . $item['lastName'] ?> invited you</h4>

                    <p class="notif-time"><?= $item['seconds'] ?></p>
                    <i class="fa fa-remove notif-remove clickDelNotification" data-id="<?= $item['notifyId'] ?>"></i>

                    <p><?= $item['content'] ?></p>

                    <p>
                        <?php if ($item["isJoined"] == "1") {
                            echo "You joined to this session.";
                        } else if (is_null($item["isJoined"])) {
                            echo "You dismissed this session.";
                        } ?>
                    </p>
                    <?php if ($item["isJoined"] === "0"): ?>
                        <div class="notif-join" id="dismissJoinOption_<?= $item["link"] ?>">
                            <div class="dismiss clickDismiss" data-id="<?= $item["link"] ?>">
                                <span><i class="fa fa-remove"></i></span>
                                DISMISS
                            </div>
                            <div class="join clickJoinTo" data-id="<?= $item["link"] ?>"><span><i
                                        class="fa fa-users"></i></span> JOIN
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="clickToNotifyDetail" data-link="<?= $item['redirect'] ?>"></div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>