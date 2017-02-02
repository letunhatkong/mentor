<?php
$this->pageTitle=Yii::app()->name . ' - Error';
?>
<div class="page-error">
        <?php 
        if((int)$code = 404){
        ?>
            <img src="<?php echo Yii::app()->getBaseUrl(); ?>/images/404.png">            
        <?php
        }else{
        ?>
            <h2>Error <?php echo $code; ?></h2>
        <?php
        }
        ?>
</div>