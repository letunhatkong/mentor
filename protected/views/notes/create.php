<?php
/* @var $this SiteController */
$this->pageTitle = Yii::app()->name . ' - ' .'My notes';
?>

<div class="margin-top"></div>

<div class="col-12 external-link margin-top">
    <div class="container">
        <h2>Create a new note</h2>
        <form id="create-notes-form" action="<?php echo Yii::app()->getBaseUrl() ?>/notes/create" method="post">
            <div class="row">
                <label class="required">Your Note <span class="required">*</span></label>
                <textarea class="span12 padding-top" rows="10" placeholder="Text input here ..." name="notesContent"></textarea>
                <div class="errorMessage"><div>
            </div>

            <div class="row buttons">
                <input type="submit" name="submit" value="Create">
            </div>

        </form>
    </div>
</div>
