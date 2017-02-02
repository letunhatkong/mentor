<div class="row-observation row-content">          
    <div class="col-12" id="drop_zone_video_progress">
        <div class="progress progress-success">
            <div style="width: 0" class="bar"></div>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="col-12">
        <div class="col-12">                
            <div class="col-12 drop_zone_video_area">
                <div class="col-12" id="drop_zone_video_upload">
                Drop video file or click here
                </div>
                <input type="file" class="default" id="videoBrowser" name="videoBrowser" style="display:none"/>
                <a href="myapp://selectVideo" id="selectVideoButtonApp" class="display-none"></a>
            </div>                
            <div class="clearfix"></div>                
            <div class="col-12">
                <label class="error" id="videoupload-error"><?php if(isset($errorString)){echo $errorString;}?></label>
            </div>                
        </div>
        <div class="col-4 drop_zone_video_view" style="display:none">
           <output id="drop_zone_video_player">
               <video class="col-11" controls autoplay>
                    <source src="" type="video/mp4">
                    Your browser does not support HTML5 video.
                </video>                   
            </output>	
        </div>
    </div>
    <div class="clearfix"></div>        
</div>




