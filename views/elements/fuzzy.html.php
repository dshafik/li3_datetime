<?php
if ($valid) {
	$format = '<span class="fuzzydate fuzzydate-%1$s">';
	if ($prefix) {
		$format .= '<span class="fuzzydate-prefix fuzzydate-prefix-%2$s">%2$s</span> ';
	} else {
		$prefix = '';
		$format .= '%2$s';
	}
	$format .= '<span class="fuzzydate-date">%3$s</span>';
	$format .= ' at ';
	$format .= '<span class="fuzzydate-time">%4$s</span>';
	$format .= '</span>';
		
	printf($format, $type, $prefix, $date, $time);
} else {
	?>
	<span class="fuzzydate fuzzydate-never">never</span>
	<?php
}
?>