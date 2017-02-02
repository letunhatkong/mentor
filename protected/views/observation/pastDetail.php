<?php if (isset($dataPastDetail)) { ?>
<div class="col-12 margin-top">
    <div class="row-fluid">
        <div class="row-observation row-content">
            <div class="col-12">
                <div class="col-6">
                    <a class="img-avartar">
                        <?php if ($dataPastDetail['avatarPath'] != "" && $dataPastDetail['avatarPath'] != null) {
                            $avatar = Yii::app()->params['avatarFolderPath'] . '/' . $dataPastDetail['avatarPath'];
                        } else {
                            $avatar = Yii::app()->params['avatarDefault'];
                        }
                        ?>
                        <img src="<?php echo Yii::app()->getBaseUrl() . $avatar ?>" alt="">
                        <span>
                            <?php echo $dataPastDetail['firstName'] . " " . $dataPastDetail['lastName'] ?>
                        </span>
                    </a>
                </div>
                <div class="col-6 text-align-right">
                    <span>
                        <?php echo date("d.m.Y", strtotime($dataPastDetail['datePost'])) ?><i
                            class="fa fa-calendar-o"></i>
                    </span>
                </div>
            </div>

            <!-- Document -->
            <div class="col-12">
                <div class="col-nd-topic text-align-right">
                    <span class="txt-topic"><?php echo $dataPastDetail['name'] ?></span>

                    <div class="row-fluid">
                        <?php if ((int)$dataPastDetail['numArchive'] > 0) { ?>
                            <a class="btn-icon document-acitve">
                                <b><?php echo $dataPastDetail['numArchive'] ?></b>
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
                    <h1 class="txt-title"><?php echo CHtml::encode($dataPastDetail['title']) ?></h1>

                    <p class="txt-mg"><?php echo CHtml::encode($dataPastDetail['description']) ?></p>
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


            <div class="col-12 title-comment">
                <?php if (isset($dataPastComment)) { ?>
                    <?php foreach ($dataPastComment as $itemComment) {
                        if ((int)$itemComment['idCommentParent'] == 0) {
                            ?>
                            <div class="row-fluid">
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
                                            <span><?php echo $itemComment['firstName'] . " " . $itemComment['lastName'] ?></span>
                                        </a>
                                    </div>
                                    <div class="col-6 text-align-right count-time">
                                        <!--<span class="observationToClientTime">-->
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
                                                        <video class="col-7" controls>
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



                                            <?php foreach ($dataPastComment as $itemReplyComment) {
                                                if ($itemReplyComment['idCommentParent'] == $itemComment['idComment']) {
                                                    ?>
                                                    <div class="row-fluid">
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
                                                                <span><?php echo $itemReplyComment['firstName'] . " " . $itemReplyComment['lastName'] ?></span>
                                                            </a>
                                                        </div>
                                                        <div class="clearfix"></div>
                                                        <div class="messages-video">
                                                            <div class="msg-time-chat">
                                                                <div class="message-body msg-in">
                                                                    <p><?php echo nl2br(CHtml::encode($itemReplyComment['content'])) ?></p>
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
                        <?php }
                    }
                } ?>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
<?php } ?>
 