<?php
use Illuminate\Support\Facades\DB;

if (!function_exists('mpr')) {
	function mpr($d, $echo = TRUE)
	{
		if ($echo) {
			echo '<pre>' . print_r($d, true) . '</pre>';
		} else {
			return '<pre>' . print_r($d, true) . '</pre>';
		}
	}
}

if (!function_exists('pr')) {
	function pr($d)
	{
		$last = debug_backtrace()[0];
		echo "<small><sub>".$last['file'].":".$last['line']."</sub></small>";
		echo "<br>";
		mpr($d);
		die;
	}
}

if (!function_exists('GetUploadStatus')) {
    function GetUploadStatus($lus_id)
    {
        $statusRow = DB::table('lkp_upload_status')->where('lus_id', $lus_id)->first();

        if ($statusRow) {
            $status = $statusRow->lus_description;
            return $status;
        } else {
            return 'false';
        }
    }
}

?>