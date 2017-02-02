<?php
$this->pageTitle = Yii::app()->name . ' - ' . 'My notes';
?>

<?php if (isset($data) && !is_null($data)): ?>
    <div class="col-12 margin-top">
        <div class="row-fluid">
            <div class="row-observation row-content">
                <form id="editNoteForm" enctype="multipart/form-data">
                    <div class="col-12">
                        <div class="col-6">
                            <a class="img-avartar">
                                <img src="<?php echo Yii::app()->getBaseUrl() . Yii::app()->user->avatarPath; ?>"/>
                                <span><?php echo Yii::app()->user->firstName . " " . Yii::app()->user->lastName ?></span>
                            </a>
                        </div>
                    </div>

                    <div class="clearfix padTop10"></div>
                    <div class="col-12">
                    <textarea class="span12 padding-top createSessionTitle" rows="8" placeholder="Text input here..."
                        id="textAreaNote" name="content" readonly><?php echo $data->content ?></textarea>

                        <div class="row-fluid border-top fixed-bottom" id="bottomBarNotes" style="display: none">
                            <a class="btn" id="clickCancelNotesForm">Cancel</a>
                            <button type="submit" name="submitNote" id="submitNote" class="btn btn-primary">OK</button>
                        </div>
                    </div>

                    <input type="hidden" id="tmpNoteContent" value="<?php echo $data->content ?>" />
                    <div class="clearfix"></div>
                </form>
            </div>
        </div>
    </div>

    <a class="btn-add new-comment" id="clickEditNotes">
        <span><i class="fa fa-pencil"></i></span>
    </a>
<?php endif; ?>