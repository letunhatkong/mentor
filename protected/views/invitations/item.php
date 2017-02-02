<?php if (isset($item)): ?>
    <div class="row-observation row-content" id="notificationId_<?= $item['isGuestId'] . '_' . $item['notifyId'] ?>">
        <div class="img-notif">
            <img src="<?= $item['avaPath'] ?>">
            <span class="notif-message notif-user"><i class="fa fa-users"></i></span>
        </div>

        <div class="notifi-box">
            <h4><?= $item['fullNameOfCreator'] ?> invited you</h4>

            <p class="notif-time"><?= $item['notifyTime'] ?></p>
            <i class="fa fa-remove notif-remove clickDelNotification" data-id="<?= $item['notifyId'] ?>"></i>

            <p><?= $item['content'] ?></p>

            <div class="notif-join" id="dismissJoinOption_<?= $item["idSession"] ?>">
                <div class="dismiss clickDismiss" data-id="<?= $item["idSession"] ?>">
                    <span><i class="fa fa-remove"></i></span> DISMISS
                </div>
                <div class="join clickJoinTo" data-id="<?= $item["idSession"] ?>">
                    <span><i class="fa fa-users"></i></span> JOIN
                </div>
            </div>

        </div>
        <div class="clickToNotifyDetail" data-link="<?= $item['redirect'] ?>"></div>
    </div>
<?php endif;