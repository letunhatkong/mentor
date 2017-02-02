<div class="row-observation row-content">          
    <div class="col-12 video_player_area textcenter">        
        <output id="video_edit_player">
                <video class="col-4" controls autoplay>
                    <source src="<?php if(isset($pathURL)){echo $pathURL;} ?>" type="<?php if(isset($type)){echo $type;} ?>">
                    Your browser does not support HTML5 video.
                </video>                   
        </output>
    </div>
     <div class="clearfix"></div>
    <div class="col-12 video_player_area">
        <div class="col-12" id="errorSplitVideo"><?php if(isset($errorString)){echo $errorString;} ?></div>
    </div>
    <div class="clearfix"></div>
    <div class="col-12 video_player_area">
        <div class="col-6">
            Selected video range: <label id="minRangeSelect">1</label>s - <label id="maxRangeSelect">10<label>s
        </div>
        <div class="col-6 textright">
            Duration of video splip: <label id="durationRangeSelect">10</label>s
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="col-12">
        <div id="ranger_noui">
        </div>
    </div>
    <div class="clearfix"></div> 
    <input type="hidden" id="idCommentTemp" name="idCommentTemp" value="<?php if(isset($idComment)){echo (int)$idComment;} ?>"/>
    <input type="hidden" id="formEdit" name="formEdit" value="1"/>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        var limitSlider = document.getElementById('ranger_noui');
        var marginDur = 10;
        var limitDur = 60;
        var startValue = 0;
        var endValue = <?php echo (int) $duration ?>;
        var step = 1;
        
        noUiSlider.create(limitSlider, {
            start: [ startValue, endValue ],
            limit: limitDur,
            margin: marginDur,
            step: step,
            behaviour: 'drag',
            connect: true,
            range: {
                'min': startValue,
                'max': endValue
            }
        });

        var limitFieldMin = document.getElementById('minRangeSelect');
        var limitFieldMax = document.getElementById('maxRangeSelect');
        var limitDuration = document.getElementById('durationRangeSelect');

        limitSlider.noUiSlider.on('update', function( values, handle ){
                (handle ? limitFieldMax : limitFieldMin).innerHTML = values[handle];
                limitDuration.innerHTML=(limitFieldMax.textContent - limitFieldMin.textContent);
        });
    });
</script>