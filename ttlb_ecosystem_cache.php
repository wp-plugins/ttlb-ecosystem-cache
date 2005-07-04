<?php
/*
Plugin Name: TTLB Ecosystem Cache
Version: 1.6
Plugin URI: http://blog.slaven.net.au/archives/2005/02/24/ttlb-ecosystem-cache/
Description: This is to replace the javascript tag that the <a href="http://www.truthlaidbear.com/ecosystem.php">TTLB Ecosystem</a> currently provides to display your sites current status in the Ecosystem.  It caches the results every 24 hours so as to reduce load times.
Author: Glenn Slaven
Author URI: http://blog.slaven.net.au/

TLB Ecosystem Cache Plugin for Wordpress 2005-03-07
Copyright © 2005 Glenn Slaven http://blog.slaven.net.au/

This program is distributed in the hope that it will be useful, but
WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
General Public License for more details.
*/

DEFINE(TLB_URL, 'http://www.truthlaidbear.com/MyDetails.php?style=javascript&url=');
define(TTLB_CACHE_FOLDER, dirname(dirname(__FILE__)));
DEFINE(TTLB_CACHE_LOC, TTLB_CACHE_FOLDER.'/ttlbstatus.php');

function ttlb_ecosystem_details($my_blog = '') {
	if (file_exists(TLB_CACHE_LOC)) {
		$delta = time() - filemtime(TLB_CACHE_LOC);
		$update = ($delta >=  (24*60*60));
	} else {
		$update = true;
	}

	if ($update || $_GET['force_refresh_ttlb']) {
		if (! $my_blog && function_exists('get_settings')) {
			$my_blog = get_settings("siteurl");
		}

		//Use the Snoopy net client to pull the asin out of the allconsuming page
		$snoopy_url = TLB_URL.$my_blog;
		$client = new Snoopy();		
		$client->read_timeout = 3;
		$client->use_gzip = true;
		($_GET['dieloud'] ? $client->fetch($snoopy_url) : @$client->fetch($snoopy_url));		
		if ($client->results) {
			if (is_writable(TTLB_CACHE_LOC) || (!file_exists(TTLB_CACHE_LOC) && is_writable(TTLB_CACHE_FOLDER))) {			    
				$write_file = @fopen(TTLB_CACHE_LOC, "w");			
				$buffer = $client->results;
				fwrite($write_file, '<script language="javascript" type="text/javascript">'.$buffer.'</script>');
				fclose($write_file);		      
			} elseif ($_GET['dieloud']) {
				print "<span style=\"color:#FF0000;\"><strong>TTLB Ecosystem Plugin Error</strong><br />The cache file location either doesn't exist or the web server doesn't have permission to write to it.</span>\n";
			}
        }
	}
	include(TTLB_CACHE_LOC);
}
?>