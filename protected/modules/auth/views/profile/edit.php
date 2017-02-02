<?php
$this->pageTitle=Yii::app()->name . ' - ' . 'User Profile';
?>

<?php if(isset($model)): ?>
<div class="col-12 margin-top">
    <div class="row-fluid">
        <div class="row-observation row-content archive" id="successProfileId">
            <?php if (isset($message)) {
                var_dump($message);
            } ?>
            <div class="col-12">
                <form class="form-horizontal" id="editProfileForm"  role="form" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label class="control-label col-sm-3">First Name:</label>
                        <div class="col-sm-9">
                            <input type="text" value="<?php echo $model['firstName']?>" class="form-control"
                                 name="firstName"  id="" placeholder="User Name">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-3">Last Name:</label>
                        <div class="col-sm-9">
                            <input type="text" value="<?php echo $model['lastName']?>" class="form-control"
                                   name="lastName" id="" placeholder="User Name">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-3">User Name:</label>
                        <div class="col-sm-9">
                            <input type="text" value="<?php echo $model['username']?>" class="form-control"
                               id="" placeholder="User Name" readonly>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-3">Email:</label>
                        <div class="col-sm-9">
                            <input type="email" value="<?php echo $model['email']?>" class="form-control"
                                   name="email"  id="" placeholder="Email" readonly>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-3">Avatar:</label>
                        <div class="col-sm-9">
                            <img src="<?php echo Yii::app()->getBaseUrl().Yii::app()->user->avatarPath;?>" id="tmpAvaImage"
                                 width="80px" class="img-thumbnail img-responsive" alt="Avatar Image">
                            <a id="clickUploadAva" class="btn-upload-avatar">Upload</a>
                            <input type="file" name="avatarPath" id="fileUploadAva" class="hide-space"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-3">Gender:</label>
                        <div class="col-sm-9">
                            <label class="radio-inline">
                                <input type="radio" class="pickGender" name="gender" value="0"
                                    <?php echo ($model['gender'] === "0") ? " checked" : "" ?>>Female
                            </label>
                            <label class="radio-inline">
                                <input type="radio" class="pickGender" name="gender" value="1"
                                    <?php echo ($model['gender'] === "1") ? " checked" : "" ?>>Male
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-3">Phone:</label>
                        <div class="col-sm-9">
                            <input type="text" value="<?php echo $model['phone']?>" class="form-control"
                                name="phone" placeholder="Phone">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-3" >Created Date:</label>
                        <div class="col-sm-9">
                            <input type="text" value="<?php echo date('d-m-Y H:i',STRTOTIME($model['dateCreate']) ) ?>"
                                   class="form-control" placeholder="NULL" readonly>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-3" >Last Seen:</label>
                        <div class="col-sm-9">
                            <input type="text" value="<?php echo date('d-m-Y H:i',STRTOTIME($model['lastSeen']) ) ?>"
                                   class="form-control" placeholder="NULL" readonly>
                        </div>
                    </div>
                    <div class="btn-archive">
                        <button type="button" name="editProfileSubmit" class="btn btn-primary" id="clickEditProfileSubmit">Change</button>
                        <button type="button"  onclick="location.href= '<?php echo Yii::app()->getBaseUrl() ?>/'"  name="editProfileSubmitCancel" class="btn btn-default">Cancel</button>
                    </div>
                </form>
            </div>
            <div class="clearfix"></div>
            <a id="clickEditPass">Change password</a>
        </div>
    </div>

    <div class="row-fluid">
        <div class="row-observation row-content archive" id="changePassWrapper" style="display: none">
            <div class="successProfile display-none" id="successPasswordId">
                <a href="javascript:void(0);" class="close" title="close">
                    <i class="fa fa-close"></i>
                </a>
                <strong>Success!</strong> Your password has been updated successfully.
            </div>
            <form class="form-horizontal" action="" id="changePassForm"
                method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label class="control-label col-sm-3">Current Password:</label>
                    <div class="col-sm-9">
                        <input type="password" class="form-control" name="currPass"  id="currPassId" >
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-3">New Password *:</label>
                    <div class="col-sm-9">
                        <input type="password" class="form-control required-entry" data-type="text"
                               name="newPass" id="firstPassword" placeholder="New password">
                        <div class="clear padTop15"></div>
                        <input type="password" class="form-control required-entry" data-type="text"
                               name="reNewPass" id="secondPassword" placeholder="Re-enter new password">
                    </div>
                </div>

                <div class="btn-archive">
                    <button type="button" name="editPassSubmit" id="editPassSubmitButtonId"
                        class="btn btn-primary">Change</button>
                    <button type="button" class="btn btn-default" id="cancelButtonEditPassProfile">Cancel</button>
                </div>
            </form>
        </div>
        <input type="hidden" id="dbPass" value="<?php echo $model["password"] ?>" />
    </div>
</div>

<!-- success modal -->

<!-- -->

<?php endif; ?>