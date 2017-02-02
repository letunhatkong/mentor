<?php
/* @var $this SiteController */
$this->pageTitle = Yii::app()->name . ' - ' .
    Yii::app()->controller->action->id . " - " .
    Yii::app()->controller->id;
?>

<div class="margin-top"></div>

<div class="col-12 external-link margin-top">
    <div class="container create-archive">
        <h2>Add a new document</h2>
        <form id="create-archives-form" action="<?php echo Yii::app()->getBaseUrl() ?>/archives/create"
              method="post" enctype="multipart/form-data">
            <div class="">
                <label class="required">File Name<span class="required">*</span></label>
                <input name="archiveName" class="form-control" id="Archives_name" type="text">
            </div>
            <div class="clearfix padTop10"></div>
            <div id="fileArchiveWrap" class="">
                <label class="required">&nbsp;</label>
                <input name="archivePath" type="file" class="upload-file">
            </div>
            <div id="linkArchiveWrap" class=""></div>

            <div class="">
                <input type="submit" class="btn btn-primary" name="submit" value="UPLOAD" id="submitAddArchiveForm">
                <a class="btn btn-primary" id="clickChosenFile">File</a>
                <a class="btn btn-primary" id="clickChosenLink">Link</a>
            </div>
            <input type="hidden" name="typeFile" value="file" class="hide-space">

        </form>
    </div>
</div>