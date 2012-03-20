<?php
 
class Colorize {
	
	private static $foreground_colors = array(
		"black" => "1;30",
		"dark_gray" => "1;30",
		"blue" => "0;34",
		"light_blue" => "1;34",
		"green" => "0;32",
		"light_green" => "1;32",
		"cyan" => "0;36",
		"light_cyan" => "1;36",
		"red" => "0;31",
		"light_red" => "1;31",
		"purple" => "0;35",
		"light_purple" => "1;35",
		"brown" => "0;33",
		"yellow" => "1;33",
		"light_gray" => "0;37",
		"white" => "1;37"
	);
	
	private static $background_colors = array(
		"black" => "40",
		"red" => "41",
		"green" => "42",
		"yellow" => "43",
		"blue" => "44",
		"magenta" => "45",
		"cyan" => "46",
		"light_gray" => "47"
	);

	// Returns colored string
	public function getColoredString($string, $foreground_color = null, $background_color = null) {
		$colored_string = "";

		// Check if given foreground color found
		if (isset(self::$foreground_colors[$foreground_color])) {
			$colored_string .= "\033[" . self::$foreground_colors[$foreground_color] . "m";
		}
		// Check if given background color found
		if (isset(self::$background_colors[$background_color])) {
			$colored_string .= "\033[" . self::$background_colors[$background_color] . "m";
		}

		// Add string and end coloring
		$colored_string .=  $string . "\033[0m";

		return $colored_string;
	}

	// Returns all foreground color names
	public function getForegroundColors() {
		return array_keys(self::$foreground_colors);
	}

	// Returns all background color names
	public function getBackgroundColors() {
		return array_keys(self::$background_colors);
	}
}