<?PHP

/**
 * Spintax - A helper class to process Spintax strings.
 *
 * @author Jason Davis - https://www.codedevelopr.com/
 *
 * Tutorial: https://www.codedevelopr.com/articles/php-spintax-class/
 *
 * Updated with suggested performance improvement by @PhiSYS.
 */
class Spintax
{
	public function process($text, $refresh)
	{
		if(isset($refresh) && !empty($refresh) && $refresh == 'on' ){
			return preg_replace_callback(
			'/\{(((?>[^\{\}]+)|(?R))*?)\}/x',
			function($text){

				$text = $text[1];
				$parts = explode('|', $text);
				return $parts[array_rand($parts)];
			},
			$text
		);
		}else{
			return preg_replace_callback(
			'/\{(((?>[^\{\}]+)|(?R))*?)\}/x',
			function($text){
				$text = $text[1];
				$parts = explode('|', $text);
				return $parts[0];
			},
			$text
		);
		}
		
	}

// 	public function replace($text, $refresh)
// 	{
// 		print_r($refresh);
// 		$text = $this->process($text[1]);
// 		$parts = explode('|', $text);
// 		//         return $parts[array_rand($parts)];
// 		return $parts[0];
// 	}
}
