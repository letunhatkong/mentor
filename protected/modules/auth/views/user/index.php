<?php
$this->pageTitle = Yii::app()->name . ' - Login';
?>
<div class="form-login text-center">
    <form id="login-auth-form" action="<?php echo Yii::app()->getBaseUrl() ?>/auth/user/login" method="post">

        <div class="row">
            <div class="col-md-12">
                <input type="text" class="input-block-level" value="" placeholder="Username" name="LoginAuth[username]"
                       id="LoginAuth_username">
                <input type="password" class="input-block-level" value="" placeholder="Password"
                       name="LoginAuth[password]" id="LoginAuth_password">
                <p style="color:red"><?php echo $txtErr ?></p>
                <button type="submit" class="btn-login btn-block">Log In</button>
            </div>
        </div>
    </form>
</div>
