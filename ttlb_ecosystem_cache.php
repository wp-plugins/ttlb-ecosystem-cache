<?php
/*
Plugin Name: TTLB Ecosystem Cache
Version: 1.4
Plugin URI: http://blog.slaven.net.au/archives/2005/02/24/ttlb-ecosystem-cache/
Description: This is to replace the javascript tag that the <a href="http://www.truthlaidbear.com/ecosystem.php">TTLB Ecosystem</a> currently provides to display your sites current status in the Ecosystem.  It caches the results every 24 hours so as to reduce load times.
Author: Glenn Slaven
Author URI: http://blog.slaven.net.au/

TLB Ecosystem Cache Plugin for Wordpress 2005-03-07
Copyright � 2005 Glenn Slaven http://blog.slaven.net.au/

This program is distributed in the hope that it will be useful, but
WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
General Public License for more details.
*/

DEFINE(TLB_URL, 'http://www.truthlaidbear.com/MyDetails.php?style=javascript&url=');
DEFINE(TLB_CACHE_LOC, dirname(dirname(__FILE__)).'/tlbstatus.php');

function ttlb_ecosystem_details($my_blog = '', $force_update = false) {
	if (file_exists(TLB_CACHE_LOC)) {
		$delta = time() - filemtime(TLB_CACHE_LOC);
		$update = ($delta >=  (24*60*60));
	} else {
		$update = true;
	}

	if ($update || $force_update) {
		if (! $my_blog && function_exists('get_settings')) {
			$my_blog = get_settings("siteurl");
		}
		if ($handle = @fopen(TLB_URL.$my_blog, "r")) {        
    		while (!feof($handle)) {
    		   $buffer .= fgets($handle, 4096);
    		}
    		fclose($handle);
        
    		//Check for no data
    		if (strlen($buffer) > 10) {
    		  $write_file = fopen(TLB_CACHE_LOC, "w");
    	   	  fwrite($write_file, '<script language="javascript" type="text/javascript">'.$buffer.'</script>');
    		  fclose($write_file);
            }
        }
	}
	require(TLB_CACHE_LOC);
}
?>