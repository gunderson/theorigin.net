<!DOCTYPE html>
<?
$cdnRoot = "";
$pageRoot = "";

$trackingString = '';

$tabsAPI = '';
$title = "Ablaze.js";
$desc = "Ablaze.js is a Javascript port of the original flash app. Ablaze is pretty fun to look at both while it's working and when it's finished. It uses an emergent algorithm so each image is unique and will never be repeated. Click it to start anew.";
$img = "http://theorigin.net/ablaze/thumb_small.png";
$_twMessages[] = "Ablaze uses an emergent algorithm to make art, so each image it makes is unique. http://theorigin.net/ablaze (via @gunderson)";
$defaultTitle = $title;

/*
 * The following arrays include all of the valid inputs expected from facebook.
 *  invalid inputs are discarded and defaults are used in their place.
 */

$_titles = array();
$_images = array();
$_descriptions = array();
$_sites = array();
$_twMessages = array();


//$contents = file_get_contents("json/results.json");
//$contents = utf8_encode($contents);

$result = json_decode($contents);

/*
 * Add elements to the $_sites array for each allowed site's tracking string.
 * Use this feature by adding the site var to the end of the url, like: 
 *
 * /index.php/?site=thismoment
 * 
 * would be valid usage for a site using thismoment as a tracking string.
 */
/* $_sites[] = "thismoment";
  $_sites[] = "crave";
  $_sites[] = "failblog";
  $_sites[] = "mog";
  $_sites[] = "hark";
  $_sites[] = "addictinggames";
  $_sites[] = "cracked";


  $_titles[] = $result->title;

  $_descriptions[] = $result->SocialMediaMessages->fail->fbDesc;
  $_descriptions[] = $result->SocialMediaMessages->success->fbDesc;

  foreach ($result->SocialMediaMessages->level as $d) {
  $_descriptions[] = $d->fbDesc;
  }
  $_descriptions[] = $result->SocialMediaMessages->promotion->fbDesc;


  $_images[] = $result->SocialMediaMessages->fail->fbImage;
  $_images[] = $result->SocialMediaMessages->success->fbImage;

  foreach ($result->SocialMediaMessages->level as $i) {
  $_images[] = $i->fbImage;
  }
  $_images[] = $result->SocialMediaMessages->promotion->fbImage;


  $_twMessages[] = $result->SocialMediaMessages->fail->twDesc;
  $_twMessages[] = $result->SocialMediaMessages->success->twDesc;

  foreach ($result->SocialMediaMessages->level as $t) {
  $_twMessages[] = $t->twDesc;
  }
  $_twMessages[] = $result->SocialMediaMessages->promotion->twDesc;


  if (isset($_REQUEST['t'])) {
  $_REQUEST['t'] = urldecode(stripslashes($_REQUEST['t']));
  if (isInList($_REQUEST['t'], $_titles)) {
  $title = $_REQUEST['t'];
  }
  } else {
  $title = $title;
  }
  if (isset($_REQUEST['d'])) {
  $_REQUEST['d'] = urldecode(stripslashes($_REQUEST['d']));
  if (isInList($_REQUEST['d'], $_descriptions)) {
  $desc = $_REQUEST['d'];
  }
  } else {
  $desc = $desc;
  }
  if (isset($_REQUEST['i'])) {
  $_REQUEST['i'] = urldecode(stripslashes($_REQUEST['i']));
  if (isInList($_REQUEST['i'], $_images)) {
  $img = $_REQUEST['i'];
  }
  } else {
  $img = $img;
  }
  if (isset($_REQUEST['site'])) {
  $_REQUEST['site'] = urldecode(stripslashes($_REQUEST['site']));
  if (isInList($_REQUEST['site'], $_sites)) {
  $trackingString = $_REQUEST['site'] . ":";
  }
  } else {
  $trackingString = "";
  }

  function isInList($f_str, $f_list) {
  $ret = false;
  $i = -1;
  $endi = count($f_list);
  while (++$i < $endi) {
  if ($f_list[$i] == $f_str) {
  $ret = true;
  }
  }
  $ret = true; // Accept all input
  return $ret;
  }
 */

function curPageURL() {
	$pageURL = 'http';
	if ($_SERVER["HTTPS"] == "on") {
		$pageURL .= "s";
	}
	$pageURL .= "://";
	if ($_SERVER["SERVER_PORT"] != "80") {
		$pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
	} else {
		$pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
	}
	return $pageURL;
}

function curPageBaseURL() {
	$urlcomponents = explode('?', curPageURL());
	$baseURL = $urlcomponents[0];
	return $baseURL;
}

function encodedCurPageURL() {
	return urlencode(curPageBaseURL());
}
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="user-scalable=yes, height=690"/>
		<meta name="apple-mobile-web-app-status-bar-style" content="black" />
		<meta name="apple-mobile-web-app-capable" content="yes" />

		<meta property="title" content="<?= $title ?>"/>
        <meta property="description" content="<?= $desc ?>"/>
        <meta name="keywords" content="Ablaze.js, creative js, creative, js, javascript, html5, flash, art" />
        <meta property="og:title" content="<?= $title ?>"/>
        <meta property="og:description" content="<?= $desc ?>"/>
        <meta property="og:type" content="game"/>
        <meta property="og:url" content="<?= curPageURL() ?>"/>
        <meta property="og:image" content="<?= $img ?>"/>
        <meta property="og:site_name" content="<?= $title ?>"/>
        <meta property="fb:app_id" content="216806998371194"/>

        <title><?= $title ?></title>

        <link rel="stylesheet" type="text/css" href="css/fullWeb.css" />
		<link type="text/css" href="css/ui-darkness/jquery-ui-1.8.14.custom.css" rel="stylesheet" />
		<link rel="stylesheet" type="text/css" href="css/fileinput.css" />		

		<script type="text/javascript" src="js/jquery-1.5.1.min.js"></script>
		<script type="text/javascript" src="js/jquery-ui-1.8.14.custom.min.js"></script>
		<script type="text/javascript" src="js/perlin-noise-simplex.js"></script>
		<script type="text/javascript" src="js/jquery.fileinput.min.js"></script>


		<script type="text/javascript">
            var trackingEnabled = true;
            window.onload = onLoadComplete;
			
			//resets page title for visitors clicking through FB shared links
            function rewriteTitle(){
                if (!/facebookexternalhit[\/\s](\d+\.\d+)/.test(navigator.userAgent)){
                    document.title = "<?= $defaultTitle ?>";
                }
            }
		
            function onLoadComplete(){
                rewriteTitle();
				init();
				console.log("loadComplete")
                $(window).bind("resize", onResize)
            }
			
			function onResize(){
				var c = document.getElementById("drawer");
				c.width = $(window).width();
				c.height = $(window).height();
				reset();
			}
			
            ////////////////////////////////////////////////
            // Sharing scripts
            ////////////////////////////////////////////////
            
		
            var fbshareTitle = "<?= $title ?>";
            var fbshareDesc = "<?= $desc ?>";
            var fbShareImg = "<?= $img ?>";
            var twMessage = "<?= $_twMessages[0] ?>";
		
            function fb_share() {
                    var u = "http://www.theorigin.net/ablazejs/";
					u += "?t=" + "Ablaze";
                    u += "&d=" + "Ablaze.js is a Javascript port of the original Ablaze flash app. Ablaze is pretty fun to look at both while it's working and when it's finished. It uses an emergent algorithm so each image is unique and will never be repeated. Click it to start anew.";
                    u += "&i=" + "http://theorigin.net/ablaze/thumb_small.png";
                    
                    var fbshareTitle = "Ablaze.js" + " by Patrick Gunderson";
                    
		    window.open('http://www.facebook.com/sharer.php?u=' + encodeURIComponent(u) + "&t=" + encodeURIComponent(fbshareTitle) ,'sharer','toolbar=0,status=0,width=800,height=450');
		}
		
		function tw_share(){
                    var twMessage = "Ablazejs uses an emergent process to make art, so each image it makes is unique. http://theorigin.net/ablazejs >@gunderson";
                    
		    window.open('http://twitter.com/intent/tweet?text=' + twMessage,'sharer','toolbar=0,status=0,width=800,height=450');
		}
			
			
			
			
			
	</script>

	<script>

	//////////////////////////////////////////////////
	// Ablaze.js
	//////////////////////////////////////////////////
	var particles;
	var lineColor;
	var spawnY;
	var maxDist = 64;
	var numParticles = 128;
	var canvas;
	var drawer;
	var bumpmap;
	var colormap;
	var ctx;

	var menu;
	var menuTweenX;

	var saveBtn;
	var generateBtn;
	var fullscreenBtn;
	var fbShareBtn;
	var twShareBtn;
	var speedRangeSlider;
	var directionRangeSlider;
	var numPointsSlider;
	var maxDistSlider;
	var bumpmapEffectSlider;

	var circleArrangementModeBtn
	var lineArrangementModeBtn;
	var randomArrangementModeBtn;

	var menuLabel;
	var newNumParticles;
	var newMaxDist;
	var bumpmapEffect;
	var ringAngle;
	var ringRadius = 200;
	var centerX;
	var centerY;

	var positionMethod = 0;
	var drawMethod = 0;

	var drawCommands;
	var drawPoints;

	var frameTimer;
	var framerate = 1000/72;

	var snapAngle = Math.PI / 2;

// shim layer with setTimeout fallback
    window.requestAnimFrame = (function(){
      return  window.requestAnimationFrame       || 
              window.webkitRequestAnimationFrame || 
              window.mozRequestAnimationFrame    || 
              window.oRequestAnimationFrame      || 
              window.msRequestAnimationFrame     || 
              function( callback ){
                window.setTimeout(callback, 1000 / 60);
              };
    })();

	function init(){
		availableShapeCount = 0;
		usedShapeCount = 0;

		lineColor = 0xffffff;
		numParticles = 128;
		spawnY = 360;
		maxDist = 80;
		bumpmapEffect = .1;
		positionMethod = 0;
		colormap = $("#colormap");

		//var bmd:BitmapData = new BitmapData(100, 100);
		//bmd.perlinNoise(100, 100, 3, Math.random(), false, true, 1, true);
		//bumpmap = new Bitmap(bmd);
		//addChild(bumpmap);

		setupMenu()
		setupCanvas();
		reset();

		window.requestAnimFrame(oef);
		//frameTimer = setInterval(oef, framerate)

		$("#drawer").bind("click", onClick);
		$("window").bind("keyup", onKeyUph);
		console.log("init()")
	}

	function setupCanvas(){
		console.log($(window).width() + "px", $(window).height() + "px");

		var html = "<canvas id='drawer' width='" + $(window).width() + "px' height='" + $(window).height() + "px'></canvas>";
		$("#contentWrapper").append(html);
	}

	/*function setupMenu(){
		posY += 25
		new Label(menu, 20, posY, "Initial Arrangement")
		posY += 20
		circleArrangementModeBtn = new RadioButton(menu, 20, posY, "Circle", true, setInitialArrangementMode);
		posY += 15
		lineArrangementModeBtn = new RadioButton(menu, 20, posY, "Line", false, setInitialArrangementMode);	
		posY += 15
		randomArrangementModeBtn = new RadioButton(menu, 20, posY, "Random", false, setInitialArrangementMode);

		posY += 40
		generateBtn = new PushButton(menu, 20, posY, "Restart", reset);
		generateBtn.width = 125

		//posY += 25
		//var loadColorMapBtn:PushButton = new PushButton(menu, 20, posY, "Load Color Map", loadColorMap);
		//loadColorMapBtn.width = 125

		posY += 25
		saveBtn = new PushButton(menu, 20, posY, "Save", saveFile);
		saveBtn.width = 125

		posY += 30
		fullscreenBtn = new PushButton(menu, 20, posY, "Toggle Full Screen", toggleFullScreen);
		fullscreenBtn.width = 125

		posY += 30
		fbShareBtn = new PushButton(menu, 20, posY, "Share On Facebook", shareOnFB);
		fbShareBtn.width = 125

		posY += 25
		twShareBtn = new PushButton(menu, 20, posY, "Share On Twitter", shareOnTwitter);
		twShareBtn.width = 125

		menuLabel = new Label(menu, 210, 10, "OPTIONS")
	}*/

	function setInitialArrangementMode(e){
		if (circleArrangementModeBtn.selected){
			positionMethod = 0;
		} else if (lineArrangementModeBtn.selected){
			positionMethod = 1;
		} else if (randomArrangementModeBtn.selected){
			positionMethod = 2;
		}
	}

	function showMenu(){
		$("#menu").animate({left: 0}, 400);
		menuShowing = true;
		$("#showOptionsBtn").hide();
		$("#hideOptionsBtn").show();
	}

	function hideMenu(){
		$("#menu").animate({left: -360}, 400);
		menuShowing = false;
		$("#showOptionsBtn").show();
		$("#hideOptionsBtn").hide();
	}

	function shareOnFB(e){
		ExternalInterface.call("fb_share");
	}

	function shareOnTwitter(e){
		ExternalInterface.call("tw_share");
	}

	function reset(e){
		//bumpmap.bitmapData.perlinNoise(100, 100, 3, Math.random() * 70000, false, true, 1, true);
		ctx = document.getElementById("drawer").getContext("2d");
		ctx.fillStyle = "rgba(0, 0, 0, 1)";
		ctx.fillRect(0,0,$("#drawer").width(),$("#drawer").height())


		bumpmap = new SimplexNoise();
		bumpmap.offsetX = 0;
		bumpmap.offsetY = 0;
		bumpmap.scale = 200;

		centerX = $("#drawer").width() >> 1;
		centerY = $("#drawer").height() >> 1;

		lineColor = 0xffffff;
		
	
		positionMethod = Number($('input[name=alignmentRadio]:checked', '#alignmentRadio').val())
		drawMethod = Number($('input[name=drawMethodRadio]:checked', '#drawMethodRadio').val())
		numParticles = $( "#particleCountSlider" ).slider( "value" );
		maxDist = $( "#maxDistSlider" ).slider( "value" );
		bumpmapEffect = $( "#bumpmapEffectSlider" ).slider( "value" );
		makeParticles()
		ringRadius = Math.min($("#drawer").width(), $("#drawer").height()) >> 2;

		//canvas.bitmapData = new BitmapData($("#drawer").width(), $("#drawer").height(), false, 0);
	}

	function onClick(e){
		reset();
		console.log("CLICKED")
	}

	function onKeyUph(e){
		/*trace("Key up");
		switch (e.keyCode) {
			case Keyboard.S:
				saveFile();
			case Keyboard.C:
				loadColorMap(e);
			break;
		}*/
	}

	/*function toggleFullScreen(e = null){
		if (stage.displayState == StageDisplayState.NORMAL){
			stage.displayState = StageDisplayState.FULL_SCREEN_INTERACTIVE;
		} else {
			stage.displayState = StageDisplayState.NORMAL;
		}
		reset();
	}*/

	function makeParticles(){
		particles = new Array(numParticles);
		var p;
		var i = -1;	
		var propX;
		var propY;
		var data;
		var col;
		var row;
		var rowWidth;
		while(++i < numParticles){
			p = {};
			particles[i] = p;
			p.angle = getRandomFromRange($( "#directionRangeSlider" ).slider( "values" )[0], $( "#directionRangeSlider" ).slider( "values" )[1]);
			p.speed = getRandomFromRange($( "#speedRangeSlider" ).slider( "values" )[0], $( "#speedRangeSlider" ).slider( "values" )[1]);
			setInitialPosition(p, positionMethod)
			p.distances = new Array(numParticles);
			p.px = p.x;
			p.py = p.y;
			p.color = 0xffffff;//colormap.bitmapData.getPixel(propX * colormap.width, propY * colormap.height);
			if (cmapCtx){
				propX = p.x / $("#drawer").width();
				propY = p.y / $("#drawer").height();
				data = cmapData.data//.getImageData(propX * cmapCtx.width, propY * cmapCtx.height,1, 1)
				col = (propX * cmapData.width) << 2;
				row = (propY * cmapData.height) >> 0;
				rowWidth = cmapData.width << 2
				p.color = (data[col + (row * rowWidth) + 0] << 16) | (data[col + (row * rowWidth) + 1] << 8) | data[col + (row * rowWidth) + 2];
				/*console.log(
					p.x, p.y,
					propX, propY,
					cmapData.width, cmapData.height,
					cmapData.data[0],
					col, row,
					data[col + (row * rowWidth) + 0],
					data[col + (row * rowWidth) + 1],
					data[col + (row * rowWidth) + 2]
				)*/
			}
		}
	}

	function setInitialPosition(f_obj, f_method){
		if (!f_method){
			f_method = 0;
		}

		switch (f_method){
			// circle
			case 0:
				ringAngle = getRandomFromRange(0, Math.PI * 2)
				f_obj.x = Math.cos(ringAngle) * ringRadius + centerX;
				f_obj.y = Math.sin(ringAngle) * ringRadius + centerY;
				break;

			//Horizontal Line
			case 1:
				f_obj.x = Math.random() * $("#drawer").width();
				f_obj.y = $("#drawer").height() >> 1;
				break;

			//Random
			case 2:
				f_obj.x = Math.random() * $("#drawer").width();
				f_obj.y = Math.random() * $("#drawer").height();
				break;
		}
	}

	function oef(e){
		//console.log("oef()")
		/*
				 if(stage.mouseX < 200){
					showMenu();
				} else{
					hideMenu();
				}*/
		particles.forEach(moveParticle);
		particles.forEach(resetDistances);
		particles.forEach(getDistances);
		particles.forEach(drawLines);
		window.requestAnimFrame(oef);
		
	}

	function checkReset(f_array){
		var done = 0;

		var i = -1;
		var endi = f_array.length;
		var p;
		while (++i < endi){
			p = f_array[i]
			if ((p.x < 0 || p.x > $("#drawer").width()) || (p.y < 0 || p.y > $("#drawer").height())){
				done++;
			}
		}
		if (done >= f_array.length){
			if (lineColor == 0){
				lineColor = 0xffffff;
			} else {
				lineColor = 0;
			}
			makeParticles()
		}
	}

	function moveParticle(f_obj, f_id, f_array){
		f_obj.px = f_obj.x;
		f_obj.py = f_obj.y;
		if (f_obj.x < 0 || f_obj.x > $("#drawer").width() || f_obj.y < 0 || f_obj.y > $("#drawer").height()){

		} else {
			var propX = f_obj.x / $("#drawer").width();
			var propY = f_obj.y / $("#drawer").height();
			//var colorVal = 0x80; //bumpmap.bitmapData.getPixel(propX * 100, propY * 100);
			//colorVal = (colorVal & 0xff) - 0x80;
			//var angleOffset = bumpmapEffect * (colorVal / 0x80);
			colorVal = bumpmap.noise((bumpmap.offsetX + f_obj.x) / bumpmap.scale, (bumpmap.offsetY + f_obj.y) / bumpmap.scale);
			var angleOffset = bumpmapEffect * colorVal;
			f_obj.angle += angleOffset;
		}
		var snappedAngle = snapToNearest(f_obj.angle, snapAngle);

		f_obj.x += Math.cos(snappedAngle) * f_obj.speed;
		f_obj.y += Math.sin(snappedAngle) * f_obj.speed;
	}

	function snapToNearest(num, base){
		return ((num / base) >> 0) * base;
	}

	function resetDistances(f_obj, f_id, f_array){
		f_obj.distances = new Array(numParticles);
	}

	function getDistances(f_obj, f_id, f_array){
		var dx;
		var dy;
		var i = -1;
		while(++i < numParticles){
			if (f_array[i].distances[f_id] == null){
				dx = f_array[i].x - f_obj.x;
				dy = f_array[i].y - f_obj.y;
				f_obj.distances[i] = Math.sqrt((dx * dx) + (dy * dy));
			}
		}
	}

	function drawLines(f_obj, f_id, f_array){
		var i = -1;
		var dx;
		var dy;
		var p0 = {};
		var radius;
		var r;
		var g;
		var b;
		while(++i < numParticles){
			if (f_obj.distances[i] != null){
				if (f_obj.distances[i] < maxDist && f_obj.distances[i] > 1){
					p0.x = f_obj.x + ((f_array[i].x - f_obj.x) * 0.5);
					p0.y = f_obj.y + ((f_array[i].y - f_obj.y) * 0.5);

					r = f_obj.color >> 16;
					g = (f_obj.color >> 8) & 0xff;
					b = f_obj.color & 0xff;
					ctx.beginPath();
					ctx.strokeStyle = "rgba("+ r +","+ g +","+ b +","+ (0.2 - (0.2 * (f_obj.distances[i] / maxDist))) +")";
					if (drawMethod == 0){
						ctx.moveTo(f_obj.x, f_obj.y);
						ctx.lineTo(f_array[i].x, f_array[i].y)
					} else if (drawMethod == 1){
						dx = f_obj.x - f_array[i].x;
						dy = f_obj.y - f_array[i].y;
						radius = Math.sqrt((dx*dx) + (dy*dy));
						ctx.moveTo(p0.x + radius, p0.y);
						ctx.arc(p0.x,p0.y,radius, 0, Math.PI * 2);
					}
					ctx.stroke();
				}
			}
		}
	}
	function getRandomFromRange(f_min, f_max){
		return (Math.random() * (f_max - f_min)) + f_min;
	}

	function saveFile(e){ 
		var reader = new FileReader();
		var now = new Date();
		var filename = "AblazeJS_" +now.fullYear + "-" + now.month + "-" + now.date + "_" + now.hours +"-"+ now.minutes +"-"+ now.seconds + ".png";
		var img = document.getElementById("drawer").toDataURL("image/jpeg", 10);
		window.open(img, "_blank")
	} 
                        
                        
	
    var oFReader = new FileReader();
	var cmapCtx;
	var cmapImg;
	var cmapData;
	function fileInputs(){
		oFReader = new FileReader();
		oFReader.onload = onFileLoaded;
		var $this = $(this),
		$val = $this.val(),
		valArray = $val.split('\\'),
		newVal = valArray[valArray.length-1]
		if(newVal !== '') {
			loadImageFile();
		}
	}

	function loadImageFile() {
		console.log("loadImageFile()")
		if (document.getElementById("colormapBtn").files.length === 0) { console.log("loadImageFile() no file") }
		var oFile = document.getElementById("colormapBtn").files[0];
		//if (!rFilter.test(oFile.type)) { alert("You must select a valid image file!"); return; }
		oFReader.readAsDataURL(oFile);
	}
	

	 function onFileLoaded(e) {
		
		console.log("loadImageFile()")
		var imgSrc = e.target.result;
		cmapImg = new Image();
		//cmapImg.onload = postCmapImgLoad;
		cmapImg.src = e.target.result
		setTimeout(postCmapImgLoad, 500)
	}
                        
	function postCmapImgLoad(){   
		var canvasHtml = "<canvas id='cmap' width='" + 100 + "px' height='" + 100 + "px'></canvas>";
		$("#colormapHolderr").html(canvasHtml);
		cmapCtx = document.getElementById("cmap").getContext("2d");
		cmapCtx.drawImage(cmapImg,0,0, 100, 100);
		cmapData = cmapCtx.getImageData(0,0,100,100)
		reset();
	}

			
			
	//////////////////////////////////////////////////
	// Menu
	////////////////////////////////////////////////
	function setupMenu(){
		$(function() {
			$( "#particleCountSlider" ).slider({
				value:100,
				min: 0,
				max: 512,
				step: 1,
				slide: function( event, ui ) {
					$( "#particleCountLabel" ).val(ui.value);
				}
			});
			$( "#particleCountLabel" ).val( $( "#particleCountSlider" ).slider( "value" ) );
		});
				
		$(function() { 
			$( "#maxDistSlider" ).slider({
				value:80,
				min: 0,
				max: 512,
				step: 1,
				slide: function( event, ui ) {
					$( "#maxDistLabel" ).val(ui.value);
				}
			});
			$( "#maxDistLabel" ).val( $( "#maxDistSlider" ).slider( "value" ) );
		});
				
		$(function() {
			$( "#bumpmapEffectSlider" ).slider({
				value:0.01,
				min: 0,
				max: .1,
				step: .001,
				slide: function( event, ui ) {
					$( "#bumpmapEffectLabel" ).val(ui.value);
				}
			});
			$( "#bumpmapEffectLabel" ).val( $( "#bumpmapEffectSlider" ).slider( "value" ) );
		});
				
		$(function() {
			$( "#directionRangeSlider" ).slider({
				range:true,
				min: 0,
				max: Math.PI * 2,
				step: .001,
				values: [0, Math.PI * 2],
				slide: function( event, ui ) {
					$( "#directionRangeLabel" ).val(ui.values[0] + " - " + ui.values[1]);
				}
			});
			$( "#directionRangeLabel" ).val( $( "#directionRangeSlider" ).slider("values")[0] + " - " + $( "#directionRangeSlider" ).slider("values")[1] );
		});
				
		$(function() {
			$( "#speedRangeSlider" ).slider({
				range:true,
				min: 0,
				max: 10,
				step: 0.1,
				values: [0.1, 3],
				slide: function( event, ui ) {
					$( "#speedRangeLabel" ).val(ui.values[0] + " - " + ui.values[1]);
				}
			});
			$( "#speedRangeLabel" ).val( $( "#speedRangeSlider" ).slider("values")[0] + " - " + $( "#speedRangeSlider" ).slider("values")[1] );
		});
                                
		$(function() {
			$( "button", "#menu" ).button();
			$( "#hideOptionsBtn").click(hideMenu);
			$( "#showOptionsBtn").click(showMenu);
			$( "#saveBtn" ).click(saveFile);
			$( "#resetBtn" ).click(reset);
			$( "#fbShareBtn" ).click(fb_share);
			$( "#twShareBtn" ).click(tw_share);
		});
                     
		//$("#colormapBtn").fileinput("option","buttonText","Button Changed");	
		$('#colormapBtn').bind('change focus click', fileInputs);
		
		$("#colormapBtn").fileinput();
		$("#colormapBtn").fileinput("option","inputText","Load Colormap");
		hideMenu();
		$("#alignmentRadio").buttonset();
		$("#drawMethodRadio").buttonset();
                                
                                
                                
                                
	}

			
	var menuShowing = true;
	var saveBtn;
	var generateBtn;
	var fullscreenBtn;
	var fbShareBtn;
	var twShareBtn;
			
	</script>
    </head>
    <body>
		<div id="contentWrapper">
			<div id="menu">
				<div id="alignmentRadio">
					<input value="0" type="radio" id="radio1" name="alignmentRadio" checked="checked" /><label for="radio1">Ring</label>
					<input value="1" type="radio" id="radio2" name="alignmentRadio" /><label for="radio2">H-Line</label>
					<input value="2" type="radio" id="radio3" name="alignmentRadio" /><label for="radio3">Random</label>
				</div>
				
				<div id="drawMethodRadio">
					<input value="0" type="radio" id="drawMethodRadio1" name="drawMethodRadio" checked="checked" /><label for="drawMethodRadio1">Lines</label>
					<input value="1" type="radio" id="drawMethodRadio2" name="drawMethodRadio" /><label for="drawMethodRadio2">Circles</label>
				</div>
				<p>
					<label for="particleCountLabel">Particle Count</label>
					<input type="text" id="particleCountLabel"/>
				</p>
				<div id="particleCountSlider"></div>
				<p>
					<label for="maxDistLabel">Max Connection Distance</label>
					<input type="text" id="maxDistLabel"/>
				</p>
				<div id="maxDistSlider"></div>
				<p>
					<label for="bumpmapEffectLabel">Bumpmap Effect</label>
					<input type="text" id="bumpmapEffectLabel"/>
				</p>
				<div id="bumpmapEffectSlider"></div>
				<p>
					<label for="directionRangeLabel">Direction Range</label>
					<input type="text" id="directionRangeLabel"/>
				</p>
				<div id="directionRangeSlider"></div>
				<p>
					<label for="speedRangeLabel">Speed Range</label>
					<input type="text" id="speedRangeLabel"/>
				</p>
				<div id="speedRangeSlider"></div>

				<div id="buttons">
					<div id="loadBtn">
						<input type="file" name="colormapBtn" id="colormapBtn" />
						<!-- <button>Load a Colormap</button> -->
					</div>
					<div id="resetBtn"><button style="width:240px;">Restart</button></div>
					<div id="saveBtn"><button style="width:240px;">Save</button></div>
					<div id="byline">by <a href="http://pat.theorigin.net">Patrick Gunderson</a></div>
					<div id="sharing">
						<div id="gPlusShareBtn"><g:plusone count="false" href="http://theorigin.net/ablazejs"></g:plusone></div>
						<div id="fbShareBtn"><iframe src="http://www.facebook.com/plugins/like.php?app_id=267067926643119&amp;href=http%3A%2F%2Ftheorigin.net%2Fablazejs&amp;send=false&amp;layout=box_count&amp;width=60&amp;show_faces=false&amp;action=like&amp;colorscheme=dark&amp;font=arial&amp;height=90" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:60px; height:90px;" allowTransparency="true"></iframe></div>
						<div id="twShareBtn"><a href="http://twitter.com/share" class="twitter-share-button" data-url="http://theorigin.net/ablazejs" data-text="Ablazejs uses an emergent process to make art, so each image it makes is unique." data-count="vertical" data-via="gunderson">Tweet</a></div>
						<div id="tumblerShareBtn"><a href="http://www.tumblr.com/share/link?url=<?php echo urlencode("http://theorigin.net/ablazejs") ?>&name=<?php echo urlencode($title) ?>&description=<?php echo urlencode($description) ?>" title="Share on Tumblr" style="display:inline-block; text-indent:-9999px; overflow:hidden; width:61px; height:20px; background:url('http://platform.tumblr.com/v1/share_2T.png') top left no-repeat transparent;">Share on Tumblr</a></div>
					
					</div>
					<div class="badge"><a href="http://www.chromeexperiments.com/detail/ablazejs/"><img src="http://www.chromeexperiments.com/img/badge-black_black.png" alt="See my Experiment on ChromeExperiments.com" /></a></div>
				</div>
				
				<div id="hideOptionsBtn"><button>Hide Options</button></div>
				<div id="showOptionsBtn"><button>Show Options</button></div>
			</div>
		</div>
		<div id="colormapHolderr"></div>
		<div id="bumpmapHolder"></div>
		
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
		
		<script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
		<script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>
		<script type="text/javascript" src="http://platform.tumblr.com/v1/share.js"></script>
    </body>
</html>
