<?php
/* @var $this SiteController */
$this->pageTitle = Yii::app()->name . ' - ' .
    Yii::app()->controller->action->id . " - " .
    Yii::app()->controller->id;
?>

    <div class="margin-top"></div>

<?php if (isset($model) && !is_null($model)): ?>

    <div class="col-12 external-link margin-top">
        <div class="container create-archive">
            <h2>Edit document</h2>

            <form id="edit-archives-form" method="post" enctype="multipart/form-data"
                  action="<?php echo Yii::app()->getBaseUrl() ?>/archives/edit/id/<?php echo $model->idArchive ?>">
                <div class="">
                    <label class="required">File Name<span class="required">*</span></label>
                    <input name="archiveName" class="form-control" id="Archives_name" type="text"
                           value="<?php echo $model->name ?>">
                </div>
                <div class="clearfix padTop10"></div>
                <?php if ($model->typeArchive === "file"): ?>
                <div class="">
                    <label class="required">Path Name</label>
                    <span><?php echo $model->fileName ?></span>
                </div>
                <div class="">
                    <label class="required">&nbsp;</label>
                    <input name="archivePath" type="file" class="upload-file" style="border:none">
                </div>
                <?php else: ?>
                <div class="">
                    <label class="required">&nbsp;</label>
                    <input name="archiveLink" type="text" class="" placeholder="Set file link here ..."
                       value="<?php echo $model->link ?>">
                </div>
                <?php endif; ?>

                <div class="">
                    <input type="submit" class="btn btn-primary" name="submit" value="SAVE" id="submitEditArchiveForm">
                    <a class="btn btn-primary" id="clickDeleteEditArchiveForm">DELETE</a>

                </div>
            </form>
        </div>
    </div>
<?php endif; ?>