<?
if (!get_magic_quotes_gpc()){
    foreach ($_GET as $key => &$val) $val = filter_input(INPUT_GET, $key);   
}
    ////////////////////////////////////////////////
    // Sharing scripts
    // This block is necessary for FB sharing and tracking.
    // It dynamically changes key variables so facebook will use the correct assets and copy when it visits this page to scrape OG details.
    ////////////////////////////////////////////////

    $flashdotRoot = "";
    $pageRoot = "http://theorigin.net/ablaze/";

  /*
      * The following arrays include all of the valid inputs expected from facebook.
      *  invalid inputs are discarded and defaults are used in their place.
      */
    
    $_titles = array();
    $_images = array();
    $_descriptions = array();
    $_sites = array();
    $_twMessages = array();
    
    $_titles[] = "Ablaze";
    $_descriptions[] = "Ablaze is pretty fun to lok at. Each image is unique. Click it to start anew.";
    $_images[] = "http://theorigin.net/ablaze/thumb_small.png";
    $_twMessages[] = "Ablaze is pretty fun to lok at. Each image is unique. Click it to start anew. http://theorigin.net/ablaze (via @gunderson)";
    
    
    $title = $_titles[0];
    $desc = $_descriptions[0];
    $img = $_images[0];
    $defaultTitle = $_titles[0];
    $twShareMsg = $_twMessages[0];
	/*
	  * Add elements to the $_sites array for each allowed site's tracking string.
	  * Use this feature by adding the site var to the end of the url, like: 
	 *
	  * /index.php/?site=thismoment
	  * 
	  * would be valid usage for a site using thismoment as a tracking string.
	  */

    if(isset($_REQUEST['t'])){
	    $_REQUEST['t'] = urldecode(stripslashes($_REQUEST['t']));
	    if (isInList($_REQUEST['t'] , $_titles)){
		    $title = $_REQUEST['t'];
	    }
    }else{
	    $title = $title;
    }
    if(isset($_REQUEST['d'])){
	    $_REQUEST['d'] = urldecode(stripslashes($_REQUEST['d']));
	    if (isInList($_REQUEST['d'] , $_descriptions)){
		    $desc = $_REQUEST['d'];
	    }
    }else{
	    $desc = $desc;
    }
    if(isset($_REQUEST['i'])){
	    $_REQUEST['i'] = urldecode(stripslashes($_REQUEST['i']));
	    if (isInList($_REQUEST['i'] , $_images)){
		    $img = $_REQUEST['i'];
	    }
    }else{
	    $img = $img;
    }
    if(isset($_REQUEST['site'])){
	    $_REQUEST['site'] = urldecode(stripslashes($_REQUEST['site']));
	    if (isInList($_REQUEST['site'] , $_sites)){
		    $trackingString = $_REQUEST['site'] . ":";
	    }
    }else{
	    $trackingString = "";
    }


    function isInList($f_str, $f_list){
	    $ret = false;
	    $i = -1;
	    $endi = count($f_list);
	    while (++$i < $endi){
		    if ($f_list[$i] == $f_str){
			    $ret = true;
		    }
	    }
	    return $ret;
    }

    function curPageURL() {
	     $pageURL = 'http';
	     if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
	     $pageURL .= "://";
	     if ($_SERVER["SERVER_PORT"] != "80") {
	      $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	     } else {
	      $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	     }
	     return $pageURL;
    }

    function curPageBaseURL(){
	    $urlcomponents = explode('?', curPageURL());
	    $baseURL = $urlcomponents[0];
	    return $baseURL;
    }

    function encodedCurPageURL() {
	    return urlencode(curPageBaseURL());
    }
    ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
	<head>
		<title>Ablaze</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <meta name="keywords" content="" />
                <meta property="og:title" content="<?=$title?>"/>
                <meta property="og:description" content="<?=$desc?>"/>
                <meta property="og:type" content="game"/>
                <meta property="og:url" content="<?=curPageURL()?>"/>
                <meta property="og:image" content="<?=$img?>"/>
                <meta property="og:site_name" content="<?=$title?>"/>
                <meta property="fb:app_id" content="107221462696230"/>
		<style type="text/css" media="screen">
		html, body { height:100%; background-color: #000000;}
		body { margin:0; padding:0; width:100%; height:100%}
		#wrapper {width:100%; margin: auto}
		#flashContent { width:100%; height:100%; margin: auto }
                #instructions {
                        font-family:Arial, Helvetica, sans-serif;
                        font-size: 0.7em;
                        color:#ccc;
                        margin: 0px;
                        text-align:center;
                }
		</style>
                <script type="text/javascript">
                function fb_share() {
                    var u = "<?=curPageBaseURL()?>";
		    u += "?t=" + "<?=$title?>";
                    u += "&d=" + "<?=$desc?>";
                    u += "&i=" + "<?=$img?>";
                    
                    var fbshareTitle = "<?=$title?>" + " by Patrick Gunderson";
                    
		    window.open('http://www.facebook.com/sharer.php?u=' + encodeURIComponent(u) + "&t=" + encodeURIComponent(fbshareTitle) ,'sharer','toolbar=0,status=0,width=800,height=450');
		}
		
		function tw_share(){
                    var twMessage = "<?=$twShareMsg?>";
                    
		    window.open('http://twitter.com/intent/tweet?text=' + twMessage,'sharer','toolbar=0,status=0,width=800,height=450');
		}
                    
                </script><script type="text/javascript" src="swfobject.js"></script>
		<script type="text/javascript">
			<!-- Adobe recommends that developers use SWFObject2 for Flash Player detection. -->
			<!-- For more information see the SWFObject page at Google code (http://code.google.com/p/swfobject/). -->
			<!-- Information is also available on the Adobe Developer Connection Under Detecting Flash Player versions and embedding SWF files with SWFObject 2" -->
			<!-- Set to minimum required Flash Player version or 0 for no version detection -->
			var swfVersionStr = "10.1.52";
			<!-- xiSwfUrlStr can be used to define an express installer SWF. -->
			var xiSwfUrlStr = "";
			var flashvars = {};
			var params = {};
			params.quality = "high";
			params.bgcolor = "#000000";
			params.play = "true";
			params.loop = "true";
			params.wmode = "window";
			params.scale = "noscale";
			params.menu = "true";
			params.devicefont = "false";
			params.salign = "lt";
			params.allowscriptaccess = "sameDomain";
			params.allowFullScreen = "true";
			var attributes = {};
			attributes.id = "fractal2";
			attributes.name = "fractal2";
			attributes.align = "left";
			swfobject.embedSWF(
				"fractal2.swf", "flashContent",
				"100%", window.innerHeight,
				swfVersionStr, xiSwfUrlStr,
				flashvars, params, attributes);
		</script>
	</head>
	<body>
		<div id="wrapper">
        <div style="display:none"><img src="thumb_small.png" /></div>
		<div id="flashContent">
			<a href="http://www.adobe.com/go/getflash">
				<img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" />
			</a>
			<p>This page requires Flash Player version 10.1.52 or higher.</p></div>
        <!-- Start of Flickr Badge -->
        <div style="clear:both">
        <style type="text/css">
        #flickr_badge_source_txt {padding:0; font: 9px Arial, Helvetica, Sans serif; color:#666666;}
        #flickr_badge_icon {display:block !important; margin:0 !important; border: 1px solid rgb(0, 0, 0) !important;}
        #flickr_icon_td {padding:0 5px 0 0 !important;}
        .flickr_badge_image {text-align:center !important;}
        .flickr_badge_image img {border: 1px solid black !important;}
        #flickr_www {display:block; padding:0 10px 0 10px !important; font: 9px Arial, Helvetica, Sans serif !important; color:#3993ff !important;}
        #flickr_badge_uber_wrapper {width:900px; margin:auto}
        #flickr_badge_uber_wrapper a:hover,
        #flickr_badge_uber_wrapper a:link,
        #flickr_badge_uber_wrapper a:active,
        #flickr_badge_uber_wrapper a:visited {text-decoration:none !important; background:inherit !important;color:#3993ff;}
        #flickr_badge_wrapper {background-color:#000000;border: solid 1px #000000}
        #flickr_badge_source {padding:0 !important; font: 9px Arial, Helvetica, Sans serif !important; color:#666666 !important;}
        </style>
        <table id="flickr_badge_uber_wrapper" cellpadding="0" cellspacing="10" border="0"><tr><td><a href="http://www.flickr.com" id="flickr_www">www.<strong style="color:#3993ff">flick<span style="color:#ff1c92">r</span></strong>.com</a><table cellpadding="0" cellspacing="10" border="0" id="flickr_badge_wrapper">
        <tr>
        <script type="text/javascript" src="http://www.flickr.com/badge_code_v2.gne?count=10&display=random&size=s&layout=h&source=user&user=56404767%40N00"></script>
        </tr>
        </table>
        </td></tr></table>
        </div>
        <!-- End of Flickr Badge -->

		</div>
            <script type="text/javascript">
		
		  var _gaq = _gaq || [];
		  _gaq.push(['_setAccount', 'UA-1087356-1']);
		  _gaq.push(['_trackPageview']);
		
		  (function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		  })();
		
		</script>
	</body>
</html>
