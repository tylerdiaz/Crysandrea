<?PHP
function merge_layers($layer1,$layer2,$config){
	$layer2 	= imagecreatefrompng($layer2);
	$transcol	= imagecolorallocatealpha($layer2, 255, 0, 255, 127);
	$trans		= imagecolortransparent($layer2,$transcol);
	imagealphablending($layer2, true);
	imagesavealpha($layer2, true);
	imagecopy($layer1,$layer2,0,0,0,0,$config['width'],$config['height']);
	$transcol	= imagecolorallocatealpha($layer1, 255, 0, 255, 127);
	$trans		= imagecolortransparent($layer1,$transcol);
	
	return $layer1;
}
function image_flip_horizontal($im){
	$x_i = imagesx($im);
	$y_i = imagesy($im);
	$im_ = imagecreatetruecolor($x_i ,$y_i);
	imagealphablending($im_, false);
	imagesavealpha($im_, true);
	for ($x = 0; $x < $x_i; $x++){
		for ($y = 0; $y < $y_i; $y++){
			imagecopy($im_, $im, $x_i - $x - 1, $y, $x, $y, 1, 1);
		}
	}
	return $im_;
}
/****************************************************************/
/*  Copied Function, modified to fit only needs of Crysandria	*/
/****************************************************************/
function resizeThumbnailImage($thumb_image_name, $image, $width, $height, $start_width, $start_height, $scale){
	list($imagewidth, $imageheight, $imageType) = getimagesize($image);
	$imageType = image_type_to_mime_type($imageType);
	
	$newImageWidth	= ceil($width * $scale);
	$newImageHeight	= ceil($height * $scale);
	$newImage = imagecreatetruecolor($newImageWidth, $newImageHeight);
	switch($imageType){
	    case "image/png":
		case "image/x-png":
			$source=imagecreatefrompng($image); 
			break;
  	}
	imagecopyresampled($newImage,$source,0,0,$start_width,$start_height,$newImageWidth,$newImageHeight,$width,$height);
	switch($imageType){
		case "image/png":
		case "image/x-png":
			$transcol	= imagecolorallocatealpha($newImage, 255, 0, 255, 127);
			$trans		= imagecolortransparent($newImage,$transcol);
			imagefill($newImage, 0, 0, $transcol);
			imagesavealpha($newImage, true);
			imagealphablending($newImage, true);
			imagepng($newImage,$thumb_image_name);  
			break;
    }

		chmod($thumb_image_name, 0777);
}

?>