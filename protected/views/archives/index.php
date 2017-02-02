<?php
$this->pageTitle = Yii::app()->name . ' - Archives';
?>
<!-- -->
<div class="col-12 margin-top">
    <div class="row-fluid">
        <div class="row-observation row-content archive">
        <?php if (isset($archives)): ?>
            <?php foreach ($archives as $archive): ?>
                <div class="col-12">
                    <div class="col-6">
                        <a class="img-avartar">
                            <img src="<?= Yii::app()->getBaseUrl() . Yii::app()->user->avatarPath ?>"/>
                            <span><?= Yii::app()->user->firstName . ' ' . Yii::app()->user->lastName ?></span>
                        </a>
                    </div>
                    <div class="col-6 text-align-right">
                        <span class="getArchiveClientTime"><?= $archive['dateCreate']; ?>
                        </span>
                    </div>
                    <div class="title-document pull-left">
                        <?php $faFile = ($archive['typeArchive'] == 'link') ? 'fa-link' : 'fa-file'; ?>
                        <i class="fa <?= $faFile ?>"></i>

                        <?php $link = '#';
                        $target = "";
                        if (!is_null($archive['path']) && $archive['path'] != "") {
                            $link = Yii::app()->getBaseUrl() . '/archives/download/file/' . $archive['path'];
                        } else if (!is_null($archive['link']) && $archive['link'] != "") {
                            $link = $archive['link'];
                            $target = 'target="_blank"';
                        } ?>
                        <a href="<?= $link ?>" <?= $target ?>>
                            <?= $archive['name'] ?>
                        </a>
                    </div>
                    <span class="clickDelArchive pull-right" data-id="<?= $archive["idArchive"] ?>">
                        <i class="fa fa-trash"></i>
                    </span>
                    <span class="clickEditArchive pull-right" data-id="<?= $archive["idArchive"]?>"
                        data-type="<?= $archive["typeArchive"]?>" data-link="<?= $archive["link"]?>"
                        data-name="<?= $archive["name"]?>">
                        <i class="fa fa-pencil"></i>
                    </span>

                    <div class="clearfix"></div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        </div>
        <div class="clearfix"></div>
    </div>
</div>
<!-- -->

<a class="btn-add btn-total-archive" id="plusButtonInArchive">
    <span><i class="fa fa-plus"></i></span>
</a>
<a class="btn-add btn-close-archive display-none" id="xButtonInArchive">
    <span><i class="fa fa-remove"></i></span>
</a>
<a href="#newFileArchiveModal" role="button" class="btn-add btn-video btn-link display-none"
   data-toggle="modal" data-backdrop="static" data-keyboard="false">
    <span><i class="fa fa-link"></i></span>
</a>

<a href="#newFileArchiveModal" role="button" class="btn-add btn-video btn-file display-none"
   data-toggle="modal" data-backdrop="static" data-keyboard="false">
    <span><i class="fa fa-file"></i></span>
</a>

<div id="newFileArchiveModal" class="modal hide" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="col-12">
        <div class="progress progress-success">
            <div style="width: 0" class="bar" id="progressArchiveFile"></div>
        </div>
    </div>
    <form id="newFileArchiveForm" action="<?php echo Yii::app()->getBaseUrl() ?>/archives/create"
          method="post" enctype="multipart/form-data">
        <div class="modal-header">
            <p class="titModal" id="tit-modal-image">New File Archive</p>
        </div>
        <div class="modal-body archiveForm">
            <div class="form-group">
                <label>File name *:</label>
                <input type="text" name="archiveName" class="archiveName">
            </div>
            <div class="form-group" id="archiveFileGroup">
                <label>Browse *:</label>
                <input name="archivePath" type="file" class="upload-file" id="uploadArchiveInput">
                <a href="myapp://fileArchives" role="button" id="chosenArchiveFileApp" class="display-none">
                    <span>Choose File</span>
                </a>
            </div>
            <div class="form-group display-none" id="archiveLinkGroup">
                <label>Link path/URL *:</label>
                <input name="archiveLink" type="url" id="archiveLinkId">
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
            <a class="btn btn-primary" id="buttonOkNewFileArchive">OK</a>
        </div>
        <input type="hidden" value="0" name="archiveId" id="inputArchiveId" />
        <input type="hidden" name="archiveType" id="typeOfArchive" value="file">
        <input type="hidden" name="actionType" id="typeOfAction" value="add">

    </form>
</div>

