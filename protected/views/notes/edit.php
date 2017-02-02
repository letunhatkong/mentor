<?php
/* @var $this SiteController */
$this->pageTitle = Yii::app()->name . ' - ' .'My notes';
?>

<div class="margin-top"></div>

<?php if( isset($model)): ?>
<div class="col-12 external-link margin-top">
    <div class="container">
        <h2>Edit my note</h2>
        <p>Last update: <?php echo $model->lastUpdate ?></p>

        <form id="edit-notes-form" action="<?php echo Yii::app()->getBaseUrl() ?>/notes/edit" method="post">
            <div class="row">
                <textarea class="span12 padding-top" rows="10" placeholder="Text input here ..."
                    name="notesContent"><?php echo $model->content ?></textarea>
                <div class="errorMessage"><div>
            </div>

            <div class="row buttons">
                <input type="submit" name="submit" value="Save">
            </div>
        </form>
    </div>
</div>
<?php endif; ?>
