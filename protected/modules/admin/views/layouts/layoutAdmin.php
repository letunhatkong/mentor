<?php /* @var $this Controller */ ?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="language" content="en">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=1"/>
    <title><?php echo CHtml::encode($this->pageTitle); ?></title>
    <link rel="shortcut icon" title="Favicon"/>

    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->getBaseUrl(); ?>/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->getBaseUrl(); ?>/asset/css/font-awesome.css">
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->getBaseUrl(); ?>/css/font-open_sans.css">
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->getBaseUrl(); ?>/css/form.css">
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->getBaseUrl(); ?>/css/jquery.datetimepicker.css">
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->getBaseUrl(); ?>/css/chosen.css"/>
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->getBaseUrl(); ?>/css/sumoselect.css"/>
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->getBaseUrl(); ?>/css/style_mentor_admin.css">
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->getBaseUrl(); ?>/css/reponsive.css">

    <!-- Socket IO   -->
    <script src='<?php echo Yii::app()->getBaseUrl(); ?>/asset/js/socket/socket.io-1.3.7.js'></script>
    <script>
        var curUrlHref = window.location.href;
        if (!sessionStorage.backURL) sessionStorage.backURL = curUrlHref;
        if (sessionStorage.currentURL && curUrlHref !== sessionStorage.currentURL) {
            sessionStorage.backURL = sessionStorage.currentURL;
        }
        sessionStorage.currentURL = curUrlHref;
    </script>
    <!--  JQuery  -->
    <script type="text/javascript" src="<?php echo Yii::app()->getBaseUrl(); ?>/asset/js/jquery-1.11.1.min.js"></script>
    <script type="text/javascript"
            src="<?php echo Yii::app()->getBaseUrl(); ?>/asset/js/socket/init_socket_client.js"></script>

</head>

<?php
$searchData = $this->getSearchData();
$notifies = array();
if ($this->checkIsGuest() != 0) {
    $notifies = $this->getAllNotify($this->checkIsGuest());
    $countNotify = $this->countNotifications($this->checkIsGuest());
}
?>

<body>

<div id="wrapper">

    <!-- header -->
    <div id="header">
        <div class="container">
            <div class="col-menu">
                <?php
                if ($this->showBackButtonInHeaderBar == true) {
                    ?>
                    <a id="backButtonInheaderBar">
                        <img src="<?php echo Yii::app()->getBaseUrl(); ?>/images/left-arrow.png"
                             class="back-button-header">
                    </a>
                <?php
                } else {
                ?>
                <button class="ou collapsed btn-nav" data-target="#navbar-collapse-main" data-toggle="collapse"
                        type="button">
                    <?php
                    }
                    ?>
                    <span class="cv">Toggle navigation</span>
                    <span class="ov"></span>
                    <span class="ov"></span>
                    <span class="ov"></span>
                </button>
                <?php if (isset($this->titleInHeaderBar)) { ?>
                    <span class="text-menu title-detail" id="titleInHeaderBar"><?php echo $this->titleInHeaderBar; ?></span>
                <?php } ?>
                <!--span class="menu-name">Name of current slide</span-->
            </div>

            <!-- search and ggi -->
            <div class="top-nav ">
                <ul class="nav pull-right top-menu">
                    <li class="dropdown mtop5">
                        <a class="dropdown-search1">
                            <i class="fa fa-search"></i>
                        </a>
                    </li>

                    <!-- Notification -->
                    <li class="relativePos">
                        <a class="notification" id="showNumberNotify">
                            <!--<img src="<?php echo Yii::app()->getBaseUrl(); ?>/images/notif.png">-->
                            <i class="fa fa-bell"></i>
                            <?php if (isset($countNotify) && $countNotify > 0) { ?>
                                <span id="countAllNotify"><?php echo $countNotify; ?></span>
                            <?php } ?>
                        </a>
                        <ul class="notificationDrop" id="allNotify_UL">
                            <li>
                                <h4>Notifications
                                    <span class="ic-solid">
                                        <img src="<?php echo Yii::app()->getBaseUrl(); ?>/images/ic-solid.jpg">
                                    </span>
                                </h4>
                            </li>

                            <?php if (isset($notifies) && !is_null($notifies)) { ?>
                                <?php foreach ($notifies as $notify) { ?>
                                    <li class="notification-li"
                                        id="notificationId_<?php echo $this->checkIsGuest() . "_" . $notify["notifyId"] ?>">
                                        <div class="img-notif">
                                            <img src="/upload/avatars/<?php echo $notify["avatarPath"] ?>">
                                            <?php if ($notify["typeNotify"] === "MESS") { ?>
                                                <span class="notif-message"><img src="/images/notif.png"></span>
                                            <?php } else if ($notify["typeNotify"] === "SESS") { ?>
                                                <span class="notif-message notif-user"><i
                                                        class="fa fa-users"></i></span>
                                            <?php } ?>
                                        </div>

                                        <div class="notifi-box">
                                            <h4>
                                                <?php echo $notify["firstName"] . " " . $notify["lastName"] ?>
                                            </h4>

                                            <p class="notif-time">
                                                <?php echo $this->toTimeNotify($notify["seconds"]) ?>
                                            </p>
                                            <i class="fa fa-remove notif-remove clickDelNotification"
                                                  data-id="<?php echo $notify["notifyId"] ?>"></i>

                                            <p><?php echo $notify["content"] ?></p>
                                            <?php if ($notify["typeNotify"] === "SESS") { ?>
                                                <p>
                                                    <?php if ($notify["isJoined"] === "1") {
                                                        echo "You joined to this session.";
                                                    } else if (is_null($notify["isJoined"])) {
                                                        echo "You dismissed this session.";
                                                    } ?>
                                                </p>
                                                <?php if ($notify["isJoined"] === "0") { ?>
                                                    <div class="notif-join"
                                                         id="dismissJoinOption_<?php echo $notify["link"] ?>">
                                                        <div class="dismiss clickDismiss"
                                                             data-id="<?php echo $notify["link"] ?>">
                                                            <span><i class="fa fa-remove"></i></span>
                                                            DISMISS
                                                        </div>
                                                        <div class="join clickJoinTo"
                                                             data-id="<?php echo $notify["link"] ?>"><span><i
                                                                    class="fa fa-users"></i></span> Join
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            <?php } ?>
                                        </div>
                                        <div class="clickToNotifyDetail"
                                             data-link="<?php echo $notify["redirect"] ?>"></div>
                                    </li>
                                <?php } ?>
                            <?php } ?>
                        </ul>
                    </li>
                    <!-- #Notification -->

                    <li class="dropdown">
                        <?php if (isset(Yii::app()->user->id)) { ?>
                            <a class="dropdown-toggle">
                                <img alt=""
                                     src="<?php echo Yii::app()->getBaseUrl() . Yii::app()->user->avatarPath; ?>">
                            </a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo Yii::app()->getBaseUrl() ?>/auth/profile/edit">
                                        <i class="fa fa-user"></i> <?php echo Yii::app()->user->firstName . " " . Yii::app()->user->lastName ?>
                                    </a></li>
                                <li>
                                    <a href="<?php echo Yii::app()->getBaseUrl() ?>/auth/user/logout">
                                        <i class="fa fa-sign-out"></i> Log Out
                                    </a>
                                </li>
                            </ul>
                        <?php } else { ?>
                            <a href="<?php echo Yii::app()->getBaseUrl() ?>/auth/user/login" style="color: #fff">
                                <i class="icon-key"></i> Log In
                            </a>
                        <?php } ?>
                    </li>

                </ul>
            </div>
            <!-- # search and ggi -->
        </div>
    </div>
    <!-- # header -->

    <!-- menu -->
    <div id="navbar-collapse-main">
        <ul>
            <li><a href="<?php echo Yii::app()->getBaseUrl() ?>/">Home</a></li>
            <li>
                <a href="<?php echo Yii::app()->getBaseUrl() ?>/observation" id="observationMenu">Observation </a>
            </li>
            <li><a href="<?php echo Yii::app()->getBaseUrl() ?>/messages" id="messageMenu">Messages </a></li>
            <li><a href="<?php echo Yii::app()->getBaseUrl() ?>/invitations">Invitations </a></li>
            <li><a href="<?php echo Yii::app()->getBaseUrl() ?>/planning">Planning</a></li>
            <li><a href="<?php echo Yii::app()->getBaseUrl() ?>/notes">My notes</a></li>
            <li><a href="<?php echo Yii::app()->getBaseUrl() ?>/archives">Archives</a></li>
            <!-- <li><a>Settings</a></li>-->
        </ul>
    </div>
    <!-- # menu -->

    <!-- search toggle  -->
    <div class="col-12 search-toggle">
        <div class="container position">
            <div class="searech-content"></div>
            <form class="search-form" action="/admin/default/searchUser" method="post">
                <div class="search-item">
                    <input type="text" placeholder="Search user" name="searchUser">
                    <i class="fa fa-arrows-v"></i>
                </div>

                <div style="overflow: hidden; width: 100%">
                    <button type="submit" class="pull-right btn-search btn-search-submit btn"
                            name="searchSubmitButton">Ok
                    </button>
                    <button type="button" class="pull-right btn-search btn" id="cancelSearchBar">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    <!-- # search toggle -->

    <div class="viewContent">
        <?php echo $content; ?>
    </div>

    <div class="clear"></div>

    <!-- footer -->
    <div id="footer">

    </div>
    <!-- # footer -->
    <input type="hidden" class="hide-space" value="<?php echo $this->checkIsGuest() ?>" id="isGuest">

</div>
<!-- end wrapper -->
<div class="background-hiddenmenu"></div>
<div class="background-hiddensearch"></div>
<div class="background-hiddenuser"></div>
<!-- JavaScript -->
<script type="text/javascript" src="<?php echo Yii::app()->getBaseUrl(); ?>/asset/js/back.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->getBaseUrl(); ?>/asset/js/jquery-1.8.2.min.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->getBaseUrl(); ?>/asset/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->getBaseUrl(); ?>/asset/js/custom.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->getBaseUrl(); ?>/asset/js/jquery.sumoselect.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->getBaseUrl(); ?>/asset/js/chosen.jquery.min.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->getBaseUrl(); ?>/asset/js/jquery.datetimepicker.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->getBaseUrl(); ?>/asset/js/jquery.validate.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->getBaseUrl(); ?>/asset/js/scripts.js"></script>
<script type="text/javascript">
    function getBaseUrl() {
        return "<?php echo Yii::app()->getBaseUrl(); ?>";
    }
</script>
<script type="text/javascript"
        src="<?php echo Yii::app()->getBaseUrl(); ?>/asset/js/development/site/common.js"></script>
<script type="text/javascript"
        src="<?php echo Yii::app()->getBaseUrl(); ?>/asset/js/development/admin/users.js"></script>
<script type="text/javascript"
        src="<?php echo Yii::app()->getBaseUrl(); ?>/asset/js/development/admin/setting.js"></script>
<script type="text/javascript"
        src="<?php echo Yii::app()->getBaseUrl(); ?>/asset/js/development/admin/adminSessions.js"></script>

<script type="text/javascript"
        src="<?php echo Yii::app()->getBaseUrl(); ?>/asset/js/development/site/messages.js"></script>
<script type="text/javascript"
        src="<?php echo Yii::app()->getBaseUrl(); ?>/asset/js/development/site/commenttext.js"></script>
<script type="text/javascript"
        src="<?php echo Yii::app()->getBaseUrl(); ?>/asset/js/development/site/commentpicture.js"></script>
<script type="text/javascript"
        src="<?php echo Yii::app()->getBaseUrl(); ?>/asset/js/development/admin/topic.js"></script>
</body>
</html>