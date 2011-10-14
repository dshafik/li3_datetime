<?php
/**
 * li3_datetime plugin for Lithium: the most rad php framework.
 *
 * @author		Davey Shafik <dshafik@engineyard.com>
 * @license		http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_datetime\extensions\helper;

class DateTime extends \lithium\template\Helper {

	protected $_classes = array();

	/**
	 * Output a friendly date
	 * 
	 * @param string [$date] A date/time string
	 * @param array [$options] Optional options.
	 *              - inputFormat: Date Format (default: DateTime::ISO8601)
	 *				- outputDateFormat: Format for output dates (that can't be friendly) (default: "l, F dS, Y")
	 *				- ouputTimeForm: Format for output times (that can't be friendly) (default: "g:ia")
	 *              - template: Name of the template that will be rendered.
	 *              - data: Additional data for the template.
	 *              - options: Additional options that will be passed to the renderer.
	 * @return string Returns the rendered template.
	 */
	public function fuzzy($date = null, array $options = array()) {
		$defaults = array(
			'type' => 'element',
			'inputFormat' => \DateTime::ISO8601,
			'outputDateFormat' => 'l, F dS, Y',
			'outputTimeFormat' => 'g:ia',
			'template' => 'fuzzy',
			'data' => array(),
			'options' => array()
		);
		$options += $defaults;
		
		$view = $this->_context->view();
		
		if (is_null($date)) {
			// now
			$date = new \DateTime();
		}
		
		// $date might be a DateTime
		$dateTime = $date;
		if (!($date instanceof DateTime)) {
			// Parse it
			$dateTime = \DateTime::createFromFormat($options['inputFormat'], $date);
		}
		
		if (!$dateTime) {
			$data = $options['data'] + array('valid' => false);
		} else {
			$timezone = $dateTime->getTimezone();

			// Fuzzy Date ranges
			$lastWeekStart = new \DateTime("2 weeks ago sunday 11:59:59", $timezone);

			$yesterdayStart = new \DateTime("yesterday midnight");

			$todayStart = new \DateTime("today midnight", $timezone);
			$todayEnd = new \DateTime("today 23:59:59", $timezone);

			$tomorrowStart = new \DateTime("tomorrow midnight", $timezone);
			$tomorrowEnd = new \DateTime("tomorrow 23:59:59", $timezone);

			$thisWeekStart = new \DateTime("1 week ago sunday 11:59:59", $timezone);

			$thisWeekEnd = new \DateTime("sunday 11:59:59", $timezone);
			$nextWeekEnd = new \DateTime("1 week sunday midnight", $timezone);

			$timestamp = $dateTime->getTimestamp();

			// We have to start with the oldest onces first
			$prefix = false;
			if ($timestamp < $lastWeekStart->getTimestamp()) {
				$type = "past";
				$prefix = "on";
				$fuzzyDate = ucwords($dateTime->format($options['outputDateFormat']));
			} elseif ($timestamp > $lastWeekStart->getTimestamp() && $timestamp < $thisWeekStart->getTimestamp()) {
				$type = "past";
				$prefix = "last";
				$fuzzyDate = ucwords($dateTime->format("l"));
			} elseif ($timestamp > $yesterdayStart->getTimestamp() && $timestamp < $todayStart->getTimestamp()) {
				$type = "past";
				$fuzzyDate = "yesterday";
			} elseif ($timestamp < $todayEnd->getTimestamp()) {
				$type = "present";
				$fuzzyDate = "today";
			} elseif ($timestamp < $tomorrowEnd->getTimestamp()) {
				$type = "future";
				$fuzzyDate = "tomorrow";
			} elseif ($timestamp < $thisWeekEnd->getTimestamp()) {
				$type = "future";
				$prefix = "this";
				$fuzzyDate = ucwords($dateTime->format("l"));
			} elseif ($timestamp < $nextWeekEnd->getTimestamp()) {
				$type = "future";
				$prefix = "next";
				$fuzzyDate = ucwords($dateTime->format("l"));
			} else {
				$type = "future";
				$prefix = "on";
				$fuzzyDate = ucwords($dateTime->format($options['outputDateFormat']));
			}

			if ($dateTime->format("Hi") != "0000") {
				$fuzzyTime = $dateTime->format($options['outputTimeFormat']);
			} else {
				$fuzzyTime = "midnight";
			}

			$data = $options['data'] + array('valid' => true, 'type' => $type, 'prefix' => $prefix, 'date' => $fuzzyDate, 'time' => $fuzzyTime);
		}
		
		$type = array($options['type'] => $options['template']);
		try {
			$output = $view->render($type, $data, $options['options']);
		} catch (\Exception $e) {
			$output = $view->render($type, $data, array('library' => 'li3_datetime'));
		}
		return $output;
	}

}

?>


<?php
class BarBanners_DateTime extends DateTime {
	public function __toString()
	{
		return $this->format("Y-m-d") .'T'. $this->format("H:i:sP");
	}
	
	static public function fuzzyDate($date = null, $inputFormat = "Y-m-d?H:i:sP", $outputDateFormat = "l, F dS, Y", $outputTimeFormat = "H:ia")
	{
		
	}
}