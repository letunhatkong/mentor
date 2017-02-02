<?php
class HandlerFFMpeg{
    
    public $videoPath = null;
    public $method= null;
    public $commandCreater = null;
    public $commandMain = null;
    public $command = null;
    public $output = null;

    public function createCommand($videoPath){
        $this->videoPath = $videoPath;
        $this->commandCreater = "ffmpeg -i ".$videoPath." 2>&1"; 
        $this->commandMain = "";
        return $this;
    }
    
    public function split($outputPath,$startTime,$duration){
        $this->commandMain = " -ss ".$startTime." -t ".$duration." -c:v copy -c:a copy ".$outputPath;
        return $this;        
    }
    
    public function convert($outputPath){
        $this->commandMain = " ".$outputPath;
        return $this;        
    }
    
    public function getCommand(){
        if($this->commandCreater!=="" && $this->commandCreater!==null){
            $this->command = $this->commandCreater;
            if($this->commandMain!==null){
                $this->command .= $this->commandMain;
            }
        }else{
            $this->command = "";
        }
        return $this->command;
    }
    
    public function getDuration(){
        $output = $this->execFfmpeg();
        $search='/Duration: (.*?),/';
        preg_match($search, $output, $matches);
        $explode = explode(':', $matches[1]);
        
        $dataOutput = array(
            'durationString' => $matches[1],
            'durationInt' => $explode[0]*60*60 + $explode[1]*60 + $explode[2],
            'hour' => $explode[0],
            'minute' => $explode[1],
            'seconds' => $explode[2],
        );
        
        return $dataOutput;
    }
    
    public function execFfmpeg(){
        $command = $this ->getCommand();
        $this->output=  shell_exec($command);
        return $this->output;
    }

    public function execFfmpegCommand($command){
        $this->output=  shell_exec($command);
        return $this->output;
    }
}
