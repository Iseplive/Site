<?php
/*
 * ffprobe class helper for ffmpeg 0.9+ (JSON support)
 * Written by Paulo Freitas <me@paulofreitas.me> under CC BY-SA 3.0 license
 */
 
 /**
 * Set the ffprobe binary path
 */
if(!defined('PHPVIDEOTOOLKIT_FFPROBE_BINARY')){
	define('PHPVIDEOTOOLKIT_FFPROBE_BINARY', '/usr/local/bin/ffprobe');
}
echo "ljknl";
class ffprobe{
    public function __construct($filename){
        if (!file_exists($filename)) {
            throw new Exception(sprintf('File not exists: %s', $filename));
        }

        $this->__metadata = $this->__probe($filename);
    }

    private function __probe($filename){
		// Avoid escapeshellarg() issues with UTF-8 filenames
		setlocale(LC_CTYPE, 'en_US.UTF-8');

		// Run the ffprobe, save the JSON output then decode
		
		exec(PHPVIDEOTOOLKIT_FFPROBE_BINARY.' -v quiet -print_format json -show_format -count_frames -show_streams '.escapeshellarg($filename),$buffer);
		$json = json_decode(implode ($buffer));
		if (!isset($json->format)) {
			throw new Exception('Unsupported file type');
		}

        return $json;
    }

    public function __get($key){
        if (isset($this->__metadata->$key)) {
            return $this->__metadata->$key;
        }

        throw new Exception(sprintf('Undefined property: %s', $key));
    }
	
	public function getFrame($frame_number=false,$frame_rate=false){
		try{
		$toolkit= new PHPVideoToolkit(PHPVIDEOTOOLKIT_TEMP_DIRECTORY);
		$tmp_name	= $toolkit->unique().'-%index.jpg';
// 		extract the frame
		$toolkit->extractFrame($frame_number, $frame_rate, '%ft');
		$toolkit->setOutput(PHPVIDEOTOOLKIT_TEMP_DIRECTORY, $tmp_name, PHPVideoToolkit::OVERWRITE_EXISTING);
		$result = $this->_toolkit->execute(false, true);
// 		check the image has been outputted
		if($result !== PHPVideoToolkit::RESULT_OK){
			return false;
		}
		$temp_output = array_shift(array_flip($toolkit->getLastOutput()));
		print_r($temp_output);
		$gd_img = imagecreatefromjpeg($temp_output);		
		$ffmpeg_frame_time = $toolkit->formatTimecode($frame_number, '%ft', '%hh:%mm:%ss.%ms', $frame_rate);
		}
		catch(Exception $e){
			echo $e;
		}
		return new ffmpeg_frame($gd_img, $ffmpeg_frame_time);
	}
}
?>