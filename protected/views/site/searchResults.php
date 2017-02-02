<!-- -->
<div class="col-12 margin-top">
    <div class="tabbable portlet-tabs">
        <ul class="nav-tabs">
            <li class="col-6 active"><a href="#portlet_tab1" data-toggle="tab">Planned</a></li>
            <li class="col-6"><a href="#portlet_tab2" data-toggle="tab">Past</a></li>
        </ul>
        <div class="tab-content">
            <!-- tab 1 -->
            <div class="tab-pane active" id="portlet_tab1">
                <div class="row-fluid">
                <?php if (isset($dataPlanned) && !is_null($dataPlanned)) { ?>
                    <?php  foreach($dataPlanned as $rowPlanned){
                        if((int)$rowPlanned['active'] == 1){  ?>
                            <div class="row-observation row-title">
                                <a style="width:100%; display:inline-block;"href="<?php echo Yii::app()->getBaseUrl();?>/observation/detail/id/<?php echo $rowPlanned['idSession'] ?>">
                                <div class="col-left">
                                    <span>
                                        <?php
                                        if($rowPlanned['avatarPath']!= "" && $rowPlanned['avatarPath']!= null){
                                            $avatarUserCreateSessionPlanned= Yii::app()->params['avatarFolderPath'].'/'.$rowPlanned['avatarPath'];
                                        }else{
                                            $avatarUserCreateSessionPlanned = Yii::app()->params['avatarDefault'];
                                        }
                                        ?>
                                        <img alt="" src="<?php echo Yii::app()->getBaseUrl().$avatarUserCreateSessionPlanned?>">
                                        <span>
                                            <?php echo $rowPlanned['firstName']." ".$rowPlanned['lastName'] ?>
                                        </span>
                                    </span>
                                    <span><?php echo CHtml::encode($rowPlanned['title'])?></span>
                                    <span><?php echo $rowPlanned['name'] ?></span>
                                </div>
                                <div class="col-right">
                                    <span>Active</span>
                                </div>
                                </a> 
                                <?php if(Yii::app()->user->idUser == $rowPlanned['idUserCreate'] || (int)$rowPlanned['numComment'] > 0){?>
                                <div class="col-right-dropdown">
                                    <span class="drop-ic">
                                        <i class="fa fa-angle-down"></i>
                                        <div class="op-action">
                                            <ul>
                                                <?php if(Yii::app()->user->idUser == $rowPlanned['idUserCreate']){?> 
                                                <li>
                                                    <a href="<?php echo Yii::app()->getBaseUrl();?>/sessions/edit/id/<?php echo $rowPlanned['idSession'] ?>">
                                                        <i class="fa fa-pencil-square-o"></i> Edit
                                                    </a>
                                                </li>
                                                <?php } 
                                                if((int)$rowPlanned['numComment'] > 0){ ?>
                                                <li>
                                                    <?php echo $rowPlanned['numComment']?> comments
                                                </li>  
                                                <?php } ?>
                                            </ul>
                                        </div>
                                    </span>
                                </div>
                                <?php } ?>  
                            </div> <!-- # row-observation -->
                        <?php  } else { ?>
                            <div class="row-observation row-content">
                                <?php if(Yii::app()->user->idUser == $rowPlanned['idUserCreate']){?> 
                                <div class="col-right-dropdown">
                                    <span class="drop-ic">
                                        <i class="fa fa-angle-down"></i>
                                        <div class="op-action">
                                            <ul>
                                                <li>
                                                    <a href="<?php echo Yii::app()->getBaseUrl();?>/sessions/edit/id/<?php echo $rowPlanned['idSession'] ?>">
                                                         <i class="fa fa-pencil-square-o"></i> Edit
                                                    </a>
                                                </li>  
                                            </ul>
                                        </div>
                                    </span>
                                </div>
                                <?php } ?>
                                <div class="col-12">
                                    <div class="col-8">
                                        <a class="img-avartar">
                                            <?php
                                            if($rowPlanned['avatarPath']!= "" && $rowPlanned['avatarPath']!= null){
                                                $avatarUserCreateSessionPlanned= Yii::app()->params['avatarFolderPath'].'/'.$rowPlanned['avatarPath'];
                                            }else{
                                                $avatarUserCreateSessionPlanned = Yii::app()->params['avatarDefault'];
                                            }
                                            ?>
                                            <img src="<?php echo Yii::app()->getBaseUrl().$avatarUserCreateSessionPlanned?>" alt="" >
                                            <span>
                                                <?php echo $rowPlanned['firstName'] ." ". $rowPlanned['lastName'] ?>
                                            </span>
                                        </a>
                                    </div>
                                    <div class="col-4 text-align-right"><span><?php echo $rowPlanned['timeElapse'];?></span></div>
                                </div>

                                <div class="col-12">
                                    <div class="col-8">
                                        <h1 class="txt-title">
                                            <a href="<?php echo Yii::app()->getBaseUrl();?>/observation/detail/id/<?php echo $rowPlanned['idSession'] ?>">
                                                <?php echo CHtml::encode($rowPlanned['title'])?>
                                            </a>
                                        </h1>
                                        <p><?php echo nl2br(CHtml::encode($rowPlanned['description']))?></p>
                                        <a href="<?php echo Yii::app()->getBaseUrl();?>/observation/detail/id/<?php echo $rowPlanned['idSession'] ?>">
                                            <span class="circle"></span>
                                            <span class="circle"></span>
                                            <span class="circle"></span>
                                        </a>
                                    </div>
                                    <div class="col-4 text-align-right">
                                        <span class="txt-topic"><?php echo CHtml::encode($rowPlanned['name'])?></span>
                                        <div class="row-fluid">
                                            <?php if((int)$rowPlanned['numArchive'] > 0){?>
                                                <a href="<?php echo Yii::app()->getBaseUrl();?>/observation/detail/id/<?php echo $rowPlanned['idSession'] ?>" class="btn-icon document-acitve">
                                                    <b><?php echo CHtml::encode($rowPlanned['numArchive']) ?></b>
                                                    <span>Documents</span>
                                                </a>
                                            <?php }else{?>
                                                <a class="btn-icon document-deacitve">
                                                    <b>0</b>
                                                    <span>Documents</span>
                                                </a>
                                            <?php }?>
                                        </div>
                                    </div>
                                </div>
                                <?php if((int)$rowPlanned['numComment'] > 0){ ?>
                                <div class="col-12 number-comment">
                                    <a href="<?php echo Yii::app()->getBaseUrl();?>/observation/detail/id/<?php echo $rowPlanned['idSession'] ?>">
                                        <?php echo $rowPlanned['numComment']?> comments
                                    </a>
                                </div>
                                <?php } ?>
                                <div class="clearfix"></div>
                            </div>
                        <?php } // end if ?>
                    <?php } // end foreach ?>
                <?php } // end if ?>
                </div>
            </div><!-- # tab 1 -->

            <!-- tab 2 -->
            <div class="tab-pane" id="portlet_tab2">
            <?php if (isset($dataPast) && !is_null($dataPast)) { ?>
                <?php  foreach($dataPast as $rowPast){?>                
                <div class="row-observation row-content">                    
                    <div class="col-12">
                        <div class="col-8">
                            <a class="img-avartar">
                                <?php
                                if($rowPast['avatarPath']!= "" && $rowPast['avatarPath']!= null){
                                    $avatarUserCreateSessionPast = Yii::app()->params['avatarFolderPath'].'/'.$rowPast['avatarPath'];
                                }else{
                                    $avatarUserCreateSessionPast = Yii::app()->params['avatarDefault'];
                                }
                                ?>
                                <img src="<?php echo Yii::app()->getBaseUrl().$avatarUserCreateSessionPast?>" alt="" >
                                <span>
                                    <?php echo $rowPast['firstName'] ." ". $rowPast['lastName'] ?>
                                </span>
                            </a>
                        </div>
                        <div class="col-4 text-align-right"><span><?php  echo $rowPast['timeElapse']?></span></div>
                    </div> <!-- # col-12 -->

                    <div class="col-12">
                        <div class="col-8">
                            <h1 class="txt-title">
                                <a href="<?php echo Yii::app()->getBaseUrl();?>/observation/pastDetail/id/<?php echo $rowPast['idSession'] ?>">
                                    <?php echo CHtml::encode($rowPast['title'])?>
                                </a>
                            </h1>
                            <p><?php echo nl2br(CHtml::encode($rowPast['description']))?></p>
                            <a href="<?php echo Yii::app()->getBaseUrl();?>/observation/pastDetail/id/<?php echo $rowPast['idSession'] ?>">
                                <span class="circle"></span>
                                <span class="circle"></span>
                                <span class="circle"></span>
                            </a>
                        </div> <!-- # col-8 -->
                        <div class="col-4 text-align-right">
                            <span class="txt-topic"><?php echo CHtml::encode($rowPast['name'])?></span>
                            <div class="row-fluid">
                            <?php if((int)$rowPast['numArchive'] > 0){?>
                                <a class="btn-icon document-acitve">
                                    <b><?php echo CHtml::encode($rowPast['numArchive']) ?></b>
                                    <span>Documents</span>
                                </a>
                            <?php }else{?>
                                <a class="btn-icon document-deacitve">
                                    <b>0</b>
                                    <span>Documents</span>
                                </a>
                            <?php }?>
                            </div>
                        </div> <!-- # col-8 -->
                    </div> <!-- # col-12 -->

                    <?php if((int)$rowPast['numComment'] > 0){ ?>
                    <div class="col-12 number-comment">
                        <a href="<?php echo Yii::app()->getBaseUrl();?>/observation/pastDetail/id/<?php echo $rowPast['idSession'] ?>">
                            <?php echo $rowPast['numComment']?> comments
                        </a>
                    </div>
                    <?php } ?>
                    <div class="clearfix"></div>

                </div>
                <?php } // End forEarch ?>
            <?php } // Endif ?>
            </div><!-- # tab 2 -->
        </div>
    </div>
</div>
<!-- -->
<!------------------ button add and dialog+ ------->
<a href="<?php echo Yii::app()->getBaseUrl();?>/sessions/create" role="button" class="btn-add new-comment">
    <span>+</span>
</a>