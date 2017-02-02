<?php $this->pageTitle=Yii::app()->name . ' - Admin'?>
<div class="external-link">
    <div class="clearfix" style="margin-bottom: 50px"></div>
    <div class="container container-admin">
        <div class="col-xs-12 col-sm-12 col-md-8">
            <!-- Get data Users -->
            <div class="section">
                <h4 class="title">User</h4>
                <div class="number-user">
                    Number of users <span><?php if(isset($data)) echo count($data['users']); ?></span>
                </div>
                <div class="table-responsive tbl-admin">
                    <table class="table">
                        <tr>
                            <td colspan="6"><div class="tbl-title">User List</div></td>
                        </tr>
                        <tr>
                            <td>Username</td>
                            <td>E-mail</td>
                            <td>Joined</td>
                            <td>Last seen</td>
                            <td>Sessions</td>
                            <td class="text-align">Actions</td>
                        </tr>
                        <?php if(isset($data['users'])): ?>
                        <?php foreach($data['users'] as $user): ?>
                        <tr>
                            <td>
                                <a id="editUser<?php echo $user['idUser']; ?>" class="clickUserName"
                                   data-user-id="<?php echo $user['idUser']; ?>" data-toggle="modal" data-target="#editUserContainer">
                                    <?php echo $user['username']; ?>
                                </a>
                            </td>
                            <td><?php echo $user['email']; ?></td>
                            <td><?php echo date('y.m.d',STRTOTIME($user['dateCreate']) ) ?></td>
                            <td><?php echo date('y.m.d',STRTOTIME($user['lastSeen']) ) ?></td>
                            <td>
                            <?php if( !is_null($user['sessions']) && count($user['sessions']) > 0 ): ?>
                                <select class="selectboxlimit">
                                <?php foreach($user['sessions'] as $subSes): ?>
                                    <option><?php echo $subSes['title'] ?></option>
                                <?php endforeach; ?>
                                </select>
                            <?php endif; ?>
                            </td>
                            <td class="text-align">
                                <?php if($user['role'] !== 'ADMIN'): ?>
                                <a href="javascript:void(0);" data-id="<?php echo $user['idUser'] ?>"
                                   class="clickDelUser btn-action"><i class="fa fa-remove"></i>
                                </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </table>
                </div>
                <a class="add-user" id="clickAddUserButton" data-toggle="modal" data-target="#addUserContainer">Add User</a>
            </div>
            <!-- # Get data Users -->

            <!-- Get data Sessions -->
            <div class="section">
                <div class="col-md-12 options">
                    <span class="pull-left">Sessions</span>
                    <div class="pull-right">
                        <?php if(isset($dataSetting)) { ?>
                        <label class="checkbox checkboxid pull-left"><input type="checkbox" value="1" name="settingCommentAllow" id="settingCommentAllow" <?php echo $dataSetting->allowComment == 1?'checked':'' ?>>Comments</label>
                        <label class="checkbox checkboxid pull-left"><input type="checkbox" value="1" name="settingLikeAllow" id="settingLikeAllow" <?php echo $dataSetting->allowLike == 1?'checked':'' ?>>Likes</label>
                        <label class="checkbox checkboxid pull-left"><input type="checkbox" value="1" name="settingDurationLimitAllow" id="settingDurationLimitAllow" <?php echo $dataSetting->allowLimitDuration == 1?'checked':'' ?>> Video length restriction</label>
                        <select class="pull-left  op-select" name="settingDurationLimitValue" id="settingDurationLimitValue">
                            <option value="60" <?php echo $dataSetting->limitDurationValue == 60?'selected':'' ?>>60 seconds</option>
                            <option value="70" <?php echo $dataSetting->limitDurationValue == 70?'selected':'' ?>>70 seconds</option>
                            <option value="90" <?php echo $dataSetting->limitDurationValue == 90?'selected':'' ?>>90 seconds</option>
                         </select>
                        <?php } ?>
                    </div>
                </div>
                <div class="table-responsive tbl-admin tbl-list">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Created by</th>
                                <th>Created date</th>
                                <th>Last edited</th>
                                <th>Invited users</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody class="tBodyGetCmt">
                            <!-- Filter Active Status -->
                            <tr>
                                <td colspan="6"><div class="tbl-title">Active</div></td>
                            </tr>
                            <?php if(isset($data['activeSession'])) { ?>
                            <?php foreach($data['activeSession'] as $session) { ?>
                                <!-- Get Session -->
                                <?php if($session['active'] === "1") { ?>
                                    <!-- info of session -->
                                    <tr>
                                        <td>
                                            <a class="btn-session adminClickShowComment" data-id="<?php echo $session["idSession"] ?>" >
                                                <i class="fa fa-plus-circle"></i> <?php echo $session['title'] ?></a>
                                        </td>
                                        <td><?php echo $session['username']; ?></td>
                                        <td><?php echo date('y.m.d',STRTOTIME($session['dateCreate']) ) ?></td>
                                        <td><?php echo date('y.m.d',STRTOTIME($session['lastUpdate']) ) ?></td>
                                        <td><?php echo $session['countUser'];?></td>
                                        <td>
                                            <a href="javascript:void(0);" data-id="<?php echo $session['idSession'] ?>"
                                               class="btn-action clickRemoveSession"><i class="fa fa-remove"></i></a>
                                        </td>
                                    </tr>
                                    <!-- # info of session -->

                                    <!-- Get Comments -->
                                    <?php if(isset($session["comments"]) && count($session["comments"]) > 0) { ?>
                                        <tr class="hidden cmt_session_<?php echo $session["idSession"]?>">
                                            <td colspan="5">
                                                <p>Comments:</p>
                                            </td>
                                        </tr>
                                        <?php foreach($session["comments"] as $cmt) { ?>
                                            <tr class="hidden cmt_session_<?php echo $session["idSession"]?>" id="commentIdAdmin_<?php echo $cmt["idComment"] ?>">
                                                <td colspan="5">
                                                    <p><?php echo $cmt["content"] ?> <span class="txtCommentBy">by <?php echo $cmt["firstName"]. " ".$cmt["lastName"]." ".$cmt["lastUpdate"]?></span></p>
                                                </td>
                                                <td><a class="btn-action clickRemoveCommentAdmin" data-id="<?php echo $cmt["idComment"] ?>"><i class="fa fa-remove"></i></a></td>
                                            </tr>
                                            <?php if (isset($cmt["childComments"])) { ?>
                                                <?php foreach($cmt["childComments"] as $childCmt) { ?>
                                                    <tr class="hidden cmt_session_<?php echo $session["idSession"]?>" id="commentIdAdmin_<?php echo $childCmt["idComment"] ?>"
                                                        data-parentid="<?php echo $childCmt["idCommentParent"] ?>">
                                                        <td colspan="5">
                                                            <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $childCmt["content"] ?> <span class="txtCommentBy">by <?php echo $childCmt["firstName"]. " ".$childCmt["lastName"]." ".$childCmt["lastUpdate"]?></span></p>
                                                        </td>
                                                        <td><a class="btn-action clickRemoveCommentAdmin" data-id="<?php echo $childCmt["idComment"] ?>"><i class="fa fa-remove"></i></a></td>
                                                    </tr>
                                                <?php } ?>
                                            <?php } ?>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <tr class="hidden cmt_session_<?php echo $session["idSession"]?>">
                                            <td colspan="5">
                                                <p>0 Comment:</p>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    <!-- # Get Comments -->
                            <?php } ?>
                            <!-- # Get Session -->
                        <?php } ?>
                        <?php } ?>
                        <!-- # Filter Active Status -->


                        <!-- Filter Planned Status -->
                        <tr>
                            <td colspan="6"><div class="tbl-title">Planned</div></td>
                        </tr>
                        <?php if(isset($data['plannedSession'])) { ?>
                        <?php foreach($data['plannedSession'] as $session) { ?>
                            <?php if($session['active'] === "0") { ?>
                                <!-- info of session -->
                                <tr>
                                    <td>
                                        <a class="btn-session adminClickShowComment" data-id="<?php echo $session["idSession"] ?>" >
                                            <i class="fa fa-plus-circle"></i> <?php echo $session['title'] ?></a>
                                    </td>
                                    <td><?php echo $session['username']; ?></td>
                                    <td><?php echo date('y.m.d',STRTOTIME($session['dateCreate']) ) ?></td>
                                    <td><?php echo date('y.m.d',STRTOTIME($session['lastUpdate']) ) ?></td>
                                    <td><?php echo $session['countUser'];?></td>
                                    <td>
                                        <a href="javascript:void(0);" data-id="<?php echo $session['idSession'] ?>"
                                           class="btn-action clickRemoveSession"><i class="fa fa-remove"></i></a>
                                    </td>
                                </tr>
                                <!-- # info of session -->

                                <!-- Get Comments -->
                                <?php if(isset($session["comments"]) && count($session["comments"]) > 0) { ?>
                                    <tr class="hidden cmt_session_<?php echo $session["idSession"]?>">
                                        <td colspan="5">
                                            <p>Comments:</p>
                                        </td>
                                    </tr>
                                    <?php foreach($session["comments"] as $cmt) { ?>
                                        <tr class="hidden cmt_session_<?php echo $session["idSession"]?>" id="commentIdAdmin_<?php echo $cmt["idComment"] ?>">
                                            <td colspan="5">
                                                <p><?php echo $cmt["content"] ?> <span class="txtCommentBy">by <?php echo $cmt["firstName"]. " ".$cmt["lastName"]." ".$cmt["lastUpdate"]?></span></p>
                                            </td>
                                            <td><a class="btn-action clickRemoveCommentAdmin" data-id="<?php echo $cmt["idComment"] ?>"><i class="fa fa-remove"></i></a></td>
                                        </tr>
                                        <?php if (isset($cmt["childComments"])) { ?>
                                            <?php foreach($cmt["childComments"] as $childCmt) { ?>
                                                <tr class="hidden cmt_session_<?php echo $session["idSession"]?>" id="commentIdAdmin_<?php echo $childCmt["idComment"] ?>"
                                                    data-parentid="<?php echo $childCmt["idCommentParent"] ?>">
                                                    <td colspan="5">
                                                        <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $childCmt["content"] ?> <span class="txtCommentBy">by <?php echo $childCmt["firstName"]. " ".$childCmt["lastName"]." ".$childCmt["lastUpdate"]?></span></p>
                                                    </td>
                                                    <td><a class="btn-action clickRemoveCommentAdmin" data-id="<?php echo $childCmt["idComment"] ?>"><i class="fa fa-remove"></i></a></td>
                                                </tr>
                                            <?php } ?>
                                        <?php } ?>
                                    <?php } ?>
                                <?php } else { ?>
                                    <tr class="hidden cmt_session_<?php echo $session["idSession"]?>">
                                        <td colspan="5">
                                            <p>0 Comment:</p>
                                        </td>
                                    </tr>
                                <?php } ?>
                                <!-- # Get Comments -->

                            <?php } ?>
                        <?php } ?>
                        <?php } ?>
                        <!-- # Filter Planned Status -->


                        <!-- Filter Past Status -->
                        <tr>
                            <td colspan="6"><div class="tbl-title">Past</div></td>
                        </tr>
                        <?php if(isset($data['pastSession'])) { ?>
                        <?php foreach($data['pastSession'] as $session) { ?>
                            <!-- info of session -->
                            <tr>
                                <td>
                                    <a class="btn-session adminClickShowComment" data-id="<?php echo $session["idSession"] ?>" >
                                        <i class="fa fa-plus-circle"></i> <?php echo $session['title'] ?></a>
                                </td>
                                <td><?php echo $session['username']; ?></td>
                                <td><?php echo date('y.m.d',STRTOTIME($session['dateCreate']) ) ?></td>
                                <td><?php echo date('y.m.d',STRTOTIME($session['lastUpdate']) ) ?></td>
                                <td><?php echo $session['countUser'];?></td>
                                <td>
                                    <a href="javascript:void(0);" data-id="<?php echo $session['idSession'] ?>"
                                       class="btn-action clickRemoveSession"><i class="fa fa-remove"></i></a>
                                </td>
                            </tr>
                            <!-- # info of session -->


                                <!-- Get Comments -->
                            <?php if(isset($session["comments"]) && count($session["comments"]) > 0) { ?>
                                <tr class="hidden cmt_session_<?php echo $session["idSession"]?>">
                                    <td colspan="5">
                                        <p>Comments:</p>
                                    </td>
                                </tr>
                                <?php foreach($session["comments"] as $cmt) { ?>
                                    <tr class="hidden cmt_session_<?php echo $session["idSession"]?>" id="commentIdAdmin_<?php echo $cmt["idComment"] ?>">
                                        <td colspan="5">
                                            <p><?php echo $cmt["content"] ?> <span class="txtCommentBy">by <?php echo $cmt["firstName"]. " ".$cmt["lastName"]." ".$cmt["lastUpdate"]?></span></p>
                                        </td>
                                        <td><a class="btn-action clickRemoveCommentAdmin" data-id="<?php echo $cmt["idComment"] ?>"><i class="fa fa-remove"></i></a></td>
                                    </tr>
                                    <?php if (isset($cmt["childComments"])) { ?>
                                        <?php foreach($cmt["childComments"] as $childCmt) { ?>
                                            <tr class="hidden cmt_session_<?php echo $session["idSession"]?>" id="commentIdAdmin_<?php echo $childCmt["idComment"] ?>"
                                                data-parentid="<?php echo $childCmt["idCommentParent"] ?>">
                                                <td colspan="5">
                                                    <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $childCmt["content"] ?> <span class="txtCommentBy">by <?php echo $childCmt["firstName"]. " ".$childCmt["lastName"]." ".$childCmt["lastUpdate"]?></span></p>
                                                </td>
                                                <td><a class="btn-action clickRemoveCommentAdmin" data-id="<?php echo $childCmt["idComment"] ?>"><i class="fa fa-remove"></i></a></td>
                                            </tr>
                                        <?php } ?>
                                    <?php } ?>
                                <?php } ?>
                            <?php } else { ?>
                                <tr class="hidden cmt_session_<?php echo $session["idSession"]?>">
                                    <td colspan="5">
                                        <p>0 Comment:</p>
                                    </td>
                                </tr>
                            <?php } ?>
                            <!-- # Get Comments -->

                        <?php } ?>
                        <?php } ?>
                        <!-- # Filter Past Status -->
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Get data Sessions -->

            <!-- Get topic -->
            <div class="section">
                <div class="col-xs-12">
                    <span class="title-admin">Topic</span>
                    <div class="add-user" id="clickNewTopic" data-toggle="modal" data-target="#newTopicModal">New a topic</div>
                    <div class="clearfix"></div>

                    <div class="table-responsive">
                        <table class="table" id="getAllTopicTable">
                            <thead>
                                <tr class="theadTopicTable">
                                    <th>Name</th>
                                    <th class="text-align">Active</th>
                                    <th class="text-align">Edit</th>
                                    <th class="text-align">Delete</th>
                                </tr>
                            </thead>

                            <tbody>
                            <?php if(isset($topics)) { ?>
                                <?php foreach($topics as $topic) { ?>
                                    <tr class="" data-id="<?php echo $topic->idTopic ?>">
                                        <td class="topicName">
                                            <?php echo $topic->name ?>
                                        </td>
                                        <td class="clickTopicStatus text-align">
                                            <?php echo ($topic->active) ? 'Activated' : 'Pending'; ?>
                                        </td>
                                        <td class="clickEditTopic text-align" data-toggle="modal" data-target="#editTopicModal">
                                            <i class="fa fa-pencil"></i>
                                        </td>
                                        <td class="clickDelTopic text-align">
                                            <i class="fa fa-trash"></i>
                                        </td>
                                    </tr>
                                <?php } ?>
                            <?php } ?>

                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
            <!-- # Get Topic -->
        </div>
        <!-- # col-md-8  -->

        <!-- col-md-4 -->
        <div class="col-xs-12 col-sm-12 col-md-4">
            <!-- Get Documents -->
            <div class="documents">
                <h4>Documents</h4>
                <?php if(isset($data['archives'])): ?>
                <?php foreach($data['archives'] as $archive):?>
                <div class="documents-items">
                    <div class="pull-left">
                        <?php $d  = date('y.m.d',STRTOTIME($archive['dateCreate'])); ?>
                        <span class="document-area"><?php echo $archive['name'];?> - Upload <?php echo $d;?></span>
                        <div class="clearfix"></div>
                        <?php if(isset($archive['sessions']) && count($archive['sessions']) > 0 ): ?>
                            <select class="selectboxlimit">
                            <?php foreach($archive['sessions'] as $ses):?>
                                <option><?php echo $ses['title'];?></option>
                            <?php endforeach; ?>
                            </select>
                        <?php endif; ?>

                    </div>
                    <a href="javascript:void(0);" data-id="<?php echo $archive['idArchive'] ?>"
                       class="clickDelDocument btn-action pull-right">
                        <i class="fa fa-remove"></i>
                    </a>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>

                <a href="<?php echo Yii::app()->getBaseUrl() ?>/archives/create" class="add-upload">Upload</a>
            </div>
            <!-- # Get Documents -->
        </div>
        <!-- # col-md-4 -->

        <div class="clearfix"></div>

        <!-- Add new user -->
        <div class="modal fade" id="addUserContainer" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Add User</h4>
                    </div>
                    <div class="modal-body">
                        <form class="form-horizontal" role="form" id="add_user_admin_form" method="post"
                              action="/admin/default/addUser" enctype="multipart/form-data">
                            <div class="form-group">
                                <label class="control-label col-sm-4" for="firstName">First Name *:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control"  placeholder="First Name"
                                        name="firstName" autofocus>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-sm-4">Last Name *:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" placeholder="Last Name"
                                        name="lastName">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-sm-4">User Name *:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" placeholder="User Name"
                                        name="username">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-sm-4">Password *:</label>
                                <div class="col-sm-8">
                                    <input type="password" class="form-control " data-type="text"
                                       name="password" id="firstPassword" placeholder="Password">
                                    <div class="clear padTop10"></div>
                                    <input type="password" class="form-control " data-type="text" placeholder="Re-enter Password"
                                        name="re_password" id="secondPassword" >
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-sm-4">Email *:</label>
                                <div class="col-sm-8">
                                    <input type="email" class="form-control validate-email "
                                        name="email" placeholder="Email">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-sm-4">Phone *:</label>
                                <div class="col-sm-8">
                                    <input type="number" class="form-control" placeholder="Phone"
                                        name="phone">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-sm-4">Gender:</label>
                                <div class="col-sm-8">
                                    <label class="radio-inline">
                                        <input type="radio" class="pickGender" name="gender" data-gender="0" value="0" checked>Female
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" class="pickGender" name="gender" data-gender="1" value="1">Male
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-sm-4">Avatar *:</label>
                                <div class="col-sm-8">
                                    <img src="<?php echo Yii::app()->getBaseUrl().Yii::app()->params['avatarDefault']; ?>"
                                         width="80px" class="img-thumbnail img-responsive" alt="Avatar image" id="tmpAvaImage">
                                    <a id="clickUploadAvaAddUserForm" class="btn-upload-avatar">Upload</a>
                                    <label id="errUploadAvaAddUserForm" class="error">image</label>
                                    <input type="file" name="uploadAvatar" id="uploadAvatar" class="hide-space"/>

                                    <input type="text" name="existsUser" id="checkExistsUser" value="false" class="hide-space">
                                    <input type="hidden" id="valueExistsUser" value="false" class="hide-space">
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary" id="addUserSubmitButton" name="addUserSubmit">Ok</button>
                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div> <!-- # modal-content -->
            </div> <!-- # modal-dialog -->
        </div> <!-- # addUserContainer -->


        <!-- Edit User -->
        <div class="modal fade" id="editUserContainer" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Edit User</h4>
                    </div>
                    <div class="modal-body">
                        <form class="form-horizontal" role="form" id="edit_user_admin_form" method="post"
                              action="/admin/default/editUser" enctype="multipart/form-data">
                            <div class="form-group">
                                <label class="control-label col-sm-4" for="firstName">First Name *:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control"  placeholder="First Name"
                                        name="firstNameEdit" autofocus>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-sm-4">Last Name *:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control"  placeholder="Last Name"
                                        name="lastNameEdit">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-sm-4">User Name *:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" placeholder="User Name"
                                        name="usernameEdit" readonly>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-sm-4" >Password :</label>
                                <div class="col-sm-8">
                                    <input type="password" class="form-control" data-type="text" placeholder="Password"
                                        name="passwordEdit" id="firstPasswordEdit">
                                    <div class="clear padTop10"></div>
                                    <input type="password" class="form-control" data-type="text" placeholder="Re-enter Password"
                                        name="re_passwordEdit" id="secondPasswordEdit">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-sm-4">Email *:</label>
                                <div class="col-sm-8">
                                    <input type="email" class="form-control" placeholder="Email"
                                        name="emailEdit">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-sm-4">Phone *:</label>
                                <div class="col-sm-8">
                                    <input type="number" class="form-control" placeholder="Phone"
                                        name="phoneEdit">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-sm-4">Gender:</label>
                                <div class="col-sm-8">
                                    <label class="radio-inline">
                                        <input type="radio" class="" name="genderEdit" data-gender="0" value="0" checked>Female
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" class="" name="genderEdit" data-gender="1" value="1">Male
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-sm-4">Avatar:</label>
                                <div class="col-sm-8">
                                    <img src="<?php echo Yii::app()->getBaseUrl().Yii::app()->params['avatarDefault']; ?>"
                                         width="80px" class="img-thumbnail img-responsive" alt="Avatar image" id="tmpAvaImageEdit">
                                    <a id="clickUploadAvaEditForm" class="btn-upload-avatar">Upload</a>
                                    <input type="file" name="uploadAvatarEdit" id="uploadAvatarEdit" class="hide-space"/>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary" id="editUserSubmitButton" name="editUserSubmit">Ok</button>
                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div> <!-- # modal-content -->
            </div> <!-- # modal-dialog -->
        </div> <!-- # edit user -->

        <!-- Add topic -->
        <div class="modal fade" id="newTopicModal" role="dialog">
            <div class="">
                <div class="modal-header">
                    <h4 class="modal-title">New a topic</h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal" role="form" id="add_topic_admin_form" method="post"
                          action="/admin/default/addTopic" enctype="multipart/form-data">
                        <div class="form-group">
                            <label class="control-label col-sm-4" for="name">Name *:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control"  placeholder="Topic Name"
                                       name="name" autofocus>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-4">Status *:</label>
                            <div class="col-sm-8">
                                <label class="radio-inline">
                                    <input type="radio" name="status" value="0">Pending
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="status" value="1" checked>Activated
                                </label>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="newTopicButtonOK" name="submitAddTopic">OK</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
        <!-- # Add topic -->

        <!-- Edit topic -->
        <div class="modal fade" id="editTopicModal" role="dialog">
            <div class="">
                <div class="modal-header">
                    <h4 class="modal-title">Edit topic</h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal" role="form" id="edit_topic_admin_form" method="post"
                          action="/admin/default/editTopic" enctype="multipart/form-data">
                        <div class="form-group">
                            <label class="control-label col-sm-4" for="name">Name *:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control"  placeholder="Topic Name"
                                       name="name" autofocus>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-4">Status *:</label>
                            <div class="col-sm-8">
                                <label class="radio-inline">
                                    <input type="radio" name="status" value="0">Pending
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="status" value="1" checked>Activated
                                </label>
                            </div>
                        </div>
                        <input type="hidden" value="0" name="id">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="editTopicButtonOK" name="submitEditTopic">OK</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
        <!-- # Edit topic -->


    </div>
</div>


