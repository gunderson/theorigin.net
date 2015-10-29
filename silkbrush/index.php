<!DOCTYPE html>
<?
$cdnRoot = "";
$pageRoot = "";

$trackingString = '';

$tabsAPI = '';
$title = "Silkbrush";
$desc = "Turn your images into abstract paintings with Silkbrush, an experiment in digital painting.";
$img = "http://theorigin.net/silkbrush/img/silkbrush_small.jpg";
$_twMessages[] = "Turn your images into abstract paintings with Silkbrush, an experiment in digital painting. http://theorigin.net/silkbrush (via @gunderson)";
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
<html xmlns:fb="http://ogp.me/ns/fb#">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="user-scalable=yes, height=690"/>
		<meta name="apple-mobile-web-app-status-bar-style" content="black" />
		<meta name="apple-mobile-web-app-capable" content="yes" />

		<meta property="title" content="<?= $title ?>"/>
        <meta property="description" content="<?= $desc ?>"/>
        <meta name="keywords" content="silkbrush, creative js, creative, js, javascript, html5, flash, art" />
        <meta property="og:title" content="<?= $title ?>"/>
        <meta property="og:description" content="<?= $desc ?>"/>
        <meta property="og:type" content="game"/>
        <meta property="og:url" content="<?= curPageURL() ?>"/>
        <meta property="og:image" content="<?= $img ?>"/>
        <meta property="og:site_name" content="<?= $title ?>"/>
        <meta property="fb:app_id" content="107221462696230"/>

        <title><?= $title ?></title>

        <link rel="stylesheet" type="text/css" href="css/fullWeb.css" />	

		<script type="text/javascript" src="js/jquery-1.5.1.min.js"></script>
		<script type="text/javascript" src="js/perlin-noise-simplex.js"></script>
		<script type="text/javascript" src="js/dat.gui.min.js"></script>


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
                    
		}
		
		function tw_share(){
                    var twMessage = "Turn your images into abstract paintings with Silkbrush, an experiment in digital painting. http://theorigin.net/silkbrush (via @gunderson)";
                    
		    window.open('http://twitter.com/intent/tweet?text=' + twMessage,'sharer','toolbar=0,status=0,width=800,height=450');
		}
			
			
			
			
			
	</script>

	<script>

	//////////////////////////////////////////////////
	// Ablaze.js
	//////////////////////////////////////////////////
	var particles;
	var shdowParticles;
	var lineColor;
	var canvas;
	var drawer;
	var bumpmap;
	var colormap;
	var ctx;
	var throttle = .5;
	var lifespan = 10;
	var mouseIsDown = false;

	var positionMethod = 0;
	var drawMethod = 0;

	var drawCommands;
	var drawPoints;

	var frameTimer;
	var framerate = 1000/72;
	
	var maxDist = 100;
	var opacity = 0.3
	var density = 1
	var spread = 90
	var bumpmapScale = 100;
	var bumpmapEffect = .4;
	var scatter = 0
	
	var shadowMaxDist = 120
	var shadowOpacity = 0.075
	var shadowDensity = 0.8
	var shadowSpread = 433
	var shadowScatter = 0
	var shadowLifespan = 4;
	var useShadows = false;
	
	var useMouse = false;
	var inc = 0;
	var rateX0 = .0335
	var rateX1 = .0212
	var rateX2 = .08223
	var rateX3 = .2
	var lengthX0 = .35
	var lengthX1 = .35
	var lengthX2 = .40
	var lengthX3 = .216
	var rateY0 = .05235
	var rateY1 = .022
	var rateY2 = .043223
	var rateY3 = .6
	var lengthY0 = .5
	var lengthY1 = .25
	var lengthY2 = .35
	var lengthY3 = .057
	
	var rateX = [rateX0, rateX1, rateX2, rateX3]
	var lengthX = [lengthX0, lengthX1, lengthX2, lengthX3]
	var rateY = [rateY0, rateY1, rateY2, rateY3]
	var lengthY = [lengthY0, lengthY1, lengthY2, lengthY3]

	var mouseEvent;

	function init(){
		colormap = $("#colormap");
		setupCanvas();
		reset();
		frameTimer = setInterval(oef, framerate)
		$("#drawer").bind("dblclick", onClick);
		$("#drawer").bind("mousedown", onMouseDown);
		$("#drawer").bind("mouseup", onMouseUp);
		$(window).bind("keyup", onKeyUph);
		console.log("init()")
	}

	function setupCanvas(){
		console.log($(window).width() + "px", $(window).height() + "px");

		var html = "<canvas id='drawer' width='" + $(window).width() + "px' height='" + $(window).height() + "px'></canvas>";
		$("#contentWrapper").prepend(html);
	}

	function reset(e){
		//bumpmap.bitmapData.perlinNoise(100, 100, 3, Math.random() * 70000, false, true, 1, true);
		ctx = document.getElementById("drawer").getContext("2d");
		ctx.fillStyle = "rgba(0, 0, 0, 1)";
		ctx.fillRect(0,0,$("#drawer").width(),$("#drawer").height())


		bumpmap = new SimplexNoise();
		bumpmap.offsetX = 0;
		bumpmap.offsetY = 0;
		bumpmap.scale = this.bumpmapScale;

		centerX = $("#drawer").width() >> 1;
		centerY = $("#drawer").height() >> 1;

		lineColor = 0xffffff;
		
		particles = new Array();
		shadowParticles = new Array();
	}
	
	$(document).bind('touchmove',function(e){
      e.preventDefault();
      onMouseMove(e);
  });
	
	$(document).mousemove(onMouseMove); 

	function onClick(e){
		reset();
		console.log("CLICKED")
	}
	
	function onMouseMove(e){
		mouseEvent = e;
	}
	
	function onMouseDown(e){
		mouseIsDown = true;
	}
	
	function onMouseUp(e){
		mouseIsDown = false;
	}
	
	function onKeyUph(e){
		switch (e.keyCode) {
			case 83:
				save();
				break;
			case 67:
				pickColorMap();
			break;
		}
	}
	
	function makeParticle(f_x, f_y, f_angle, f_speed, group){
		var p = {};
		group.push(p);
		p.angle = f_angle
		p.speed = f_speed
		p.ox = mouseData.px1;
		p.oy = mouseData.py1;
		p.x = f_x;
		p.y = f_y;
		p.px = p.x;
		p.py = p.y;
		p.age = 0;
		p.distances = new Array();
		p.color = 0xffffff;//colormap.bitmapData.getPixel(propX * colormap.width, propY * colormap.height);
		if (cmapCtx){
			propX = p.x / $("#drawer").width();
			propY = p.y / $("#drawer").height();
			data = cmapData.data//.getImageData(propX * cmapCtx.width, propY * cmapCtx.height,1, 1)
			col = (propX * cmapData.width) << 2;
			row = (propY * cmapData.height) >> 0;
			rowWidth = cmapData.width << 2
			p.color = (data[col + (row * rowWidth) + 0] << 16) | (data[col + (row * rowWidth) + 1] << 8) | data[col + (row * rowWidth) + 2];
			
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
		rateX = [rateX0, rateX1, rateX2, rateX3]
		lengthX = [lengthX0, lengthX1, lengthX2, lengthX3]
		rateY = [rateY0, rateY1, rateY2, rateY3]
		lengthY = [lengthY0, lengthY1, lengthY2, lengthY3]
		
		if (useMouse){
			analyzeMouse(mouseEvent);
		} else {
			analyzeMouse();
		}
		if (useShadows){
			spawnShadowParticles();
			shadowParticles.forEach(resetDistances);
			shadowParticles.forEach(getDistances);
			shadowParticles.forEach(drawShadows);
			shadowParticles.forEach(moveParticle);

			var i = shadowParticles.length;
			var endi = -1;
			while(--i > endi){
				if (++shadowParticles[i].age > shadowLifespan){
					shadowParticles.splice(i, 1);
				}
			}
		}
		spawnParticles();
		particles.forEach(resetDistances);
		particles.forEach(getDistances);
		particles.forEach(drawLines);
		particles.forEach(moveParticle);
		
		var i = particles.length;
		var endi = -1;
		while(--i > endi){
			if (++particles[i].age > lifespan){
				particles.splice(i, 1);
			}
		}
	}
	
	var mouseData = {};
	mouseData.x;
	mouseData.y;
	mouseData.dx = 0;
	mouseData.dy = 0;
	mouseData.angle = 0;
	mouseData.speed = 0;
	mouseData.px = null;
	mouseData.py = null;
	
	function analyzeMouse(e){ 
		if (typeof(e) != "undefined"){
			mouseData.x = e.pageX;
			mouseData.y = e.pageY;
		} else {
			inc += throttle * 2;
			var stageWidth = $("#drawer").width()
			var stageHeight = $("#drawer").height()
			mouseData.x = stageWidth >> 1;
			var i = -1;
			var endi = rateX.length;
			while (++i < endi){
				mouseData.x += stageWidth * lengthX[i] * ((Math.cos(inc * rateX[i]) / 2));
			}
			
			mouseData.y = stageHeight >> 1;
			i = -1;
			endi = rateY.length;
			while (++i < endi){
				mouseData.y += stageHeight * lengthY[i] * ((Math.sin(inc * rateY[i]) / 2));
			}
		}
		
		if (mouseData.px === null){
			mouseData.px = mouseData.x;
			mouseData.py = mouseData.y;
		}
		
		mouseData.dx = mouseData.x - mouseData.px;
		mouseData.dy = mouseData.y - mouseData.py;
		
		mouseData.angle = Math.atan2(mouseData.dy, mouseData.dx);
		mouseData.speed = Math.sqrt((mouseData.dx*mouseData.dx) + (mouseData.dy*mouseData.dy));
		
		mouseData.px1 = mouseData.px;
		mouseData.py1 = mouseData.py;
		
		mouseData.px = mouseData.x;
		mouseData.py = mouseData.y;
	}
	
	function spawnParticles(){
		if (mouseData.speed > 0){
			makeParticle(mouseData.x, mouseData.y, mouseData.angle + (Math.PI * 0.5), 1/mouseData.speed * spread, particles)
			makeParticle(mouseData.x, mouseData.y, mouseData.angle - (Math.PI * 0.5), 1/mouseData.speed * spread, particles)
		}
	}
	
	function spawnShadowParticles(){
		if (mouseData.speed > 0){
			makeParticle(mouseData.x, mouseData.y, mouseData.angle + (Math.PI * 0.5), 1/mouseData.speed * shadowSpread, shadowParticles)
			makeParticle(mouseData.x, mouseData.y, mouseData.angle - (Math.PI * 0.5), 1/mouseData.speed * shadowSpread, shadowParticles)
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
			
			
			if (cmapCtx && f_array == particles){
				propX = f_obj.x / $("#drawer").width();
				propY = f_obj.y / $("#drawer").height();
				data = cmapData.data//.getImageData(propX * cmapCtx.width, propY * cmapCtx.height,1, 1)
				col = (propX * cmapData.width) << 2;
				row = (propY * cmapData.height) >> 0;
				rowWidth = cmapData.width << 2
				f_obj.color = (data[col + (row * rowWidth) + 0] << 16) | (data[col + (row * rowWidth) + 1] << 8) | data[col + (row * rowWidth) + 2];

			} else if (cmapCtx && f_array == shadowParticles){
				f_obj.color = 0
			}
			
			
		}
		f_obj.x += Math.cos(f_obj.angle) * f_obj.speed;
		f_obj.y += Math.sin(f_obj.angle) * f_obj.speed;
		
	}

	function resetDistances(f_obj, f_id, f_array){
		f_obj.distances = new Array(f_array.length);
	}

	function getDistances(f_obj, f_id, f_array){
		f_obj.disances = new Array(f_array.length);
		var dx;
		var dy;
		var i = -1;
		while(++i < f_array.length){
			if (!f_array[i].distances[f_id]){
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
		var offsetX;
		var offsetY;
                //i = f_id - 2;
                //if (f_id > 1){
		while(++i < f_obj.distances.length){
			if (f_obj.distances[i] != null){
				if (f_obj.distances[i] < maxDist && f_obj.distances[i] > 1 && Math.random() < density){

					r = f_obj.color >> 16;
					g = (f_obj.color >> 8) & 0xff;
					b = f_obj.color & 0xff;
					ctx.beginPath();
					ctx.fillStyle = "rgba("+ r +","+ g +","+ b +","+ opacity+")";
				
					
					offsetX = scatter*Math.random()-scatter/2
					offsetY = scatter*Math.random()-scatter/2
					
					ctx.moveTo(f_obj.x + offsetX, f_obj.y + offsetY);
					ctx.lineTo(f_array[i].x+ offsetX, f_array[i].y+ offsetY)
					ctx.lineTo(f_array[i].px+ offsetX, f_array[i].py+ offsetY)
					ctx.lineTo(f_obj.px + offsetX, f_obj.py + offsetY)
					//ctx.stroke();
					ctx.fill();
				}
			}
		}
	}
	
	function drawShadows(f_obj, f_id, f_array){
		var i = -1;
		var dx;
		var dy;
		var p0 = {};
		var radius;
		var r;
		var g;
		var b;
		var offsetX;
		var offsetY;
		while(++i < f_obj.distances.length){
			if (f_obj.distances[i] != null){
				if (f_obj.distances[i] < shadowMaxDist && f_obj.distances[i] > 1 && Math.random() < shadowDensity){

					r = f_obj.color >> 16;
					g = (f_obj.color >> 8) & 0xff;
					b = f_obj.color & 0xff;
					ctx.beginPath();
					ctx.fillStyle = "rgba("+ r +","+ g +","+ b +","+ shadowOpacity+")";
				
					
					offsetX = scatter*Math.random()-scatter/2
					offsetY = scatter*Math.random()-scatter/2
					
					ctx.moveTo(f_obj.x + offsetX, f_obj.y + offsetY);
					ctx.lineTo(f_array[i].x+ offsetX, f_array[i].y+ offsetY)
					ctx.lineTo(f_array[i].px+ offsetX, f_array[i].py+ offsetY)
					ctx.lineTo(f_obj.px + offsetX, f_obj.py + offsetY)
					//ctx.stroke();
					ctx.fill();
				}
			}
		}
	}
	
	function getRandomFromRange(f_min, f_max){
		return (Math.random() * (f_max - f_min)) + f_min;
	}
	
	var cmapCtx;
	var cmapImg;
	var cmapData;

	var save = function(){ 
		var reader = new FileReader();
		var now = new Date();
		var filename = "Silkbrush_" +now.fullYear + "-" + now.month + "-" + now.date + "_" + now.hours +"-"+ now.minutes +"-"+ now.seconds + ".png";
		var img = document.getElementById("drawer").toDataURL("image/jpeg", 10);
		//window.open(img, "_blank")
		saveToDisk(img, "silkbrush.jpg");
	}

	function saveToDisk(fileUrl, fileName) {
        // Auto-save file to disk
        var save = document.createElement("a");
        save.href = fileUrl;
        save.target = "_blank";
        save.download = fileName || fileUrl;

        var evt = document.createEvent('MouseEvents');
        evt.initMouseEvent('click', true, true, window, 1, 0, 0, 0, 0, false, false, false, false, 0, null);

        save.dispatchEvent(evt);

        //window.URL.revokeObjectURL(save.href)
    }
                        
                        
	if (typeof(FileReader) != "undefined"){
    var oFReader = new FileReader();

		var fileInputs = function(){
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
			deselectAllImg()
			console.log("loadImageFile()")
			if (document.getElementById("colormapBtn").files.length === 0) { console.log("loadImageFile() no file") }
			var oFile = document.getElementById("colormapBtn").files[0];
			//if (!rFilter.test(oFile.type)) { alert("You must select a valid image file!"); return; }
			oFReader.readAsDataURL(oFile);
		}
	}
	

	 function onFileLoaded(e) {
		
		console.log("loadImageFile()")
		var imgSrc = e.target.result;
		cmapImg = new Image();
		//cmapImg.onload = postCmapImgLoad;
		cmapImg.src = imgSrc
		
		cmapImg.onload = postCmapImgLoad;
		//setTimeout(postCmapImgLoad, 500)
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
	var self = this;
	$(document).ready(setupMenu)
	
	function setupMenu(){
		var gui = new dat.GUI();
		var text = self
		var f1 = gui.addFolder('Movement');
		  f1.add(text, 'throttle', -1, 1);
		  f1.add(text, 'rateX0', 0, 1);
		  f1.add(text, 'rateX1', 0, 1);
		  f1.add(text, 'rateX2', 0, 1);
		  f1.add(text, 'rateX3', 0, 3);
		  
		  f1.add(text, 'lengthX0', 0, 1);
		  f1.add(text, 'lengthX1', 0, 1);
		  f1.add(text, 'lengthX2', 0, 1);
		  f1.add(text, 'lengthX3', 0, 1);
		  
		  f1.add(text, 'rateY0', 0, 1);
		  f1.add(text, 'rateY1', 0, 1);
		  f1.add(text, 'rateY2', 0, 1);
		  f1.add(text, 'rateY3', 0, 3);
		  
		  f1.add(text, 'lengthY0', 0, 1);
		  f1.add(text, 'lengthY1', 0, 1);
		  f1.add(text, 'lengthY2', 0, 1);
		  f1.add(text, 'lengthY3', 0, 1);

		var f2 = gui.addFolder('Brush');
		  f2.add(text, 'bumpmapEffect', 0, 1);
		  f2.add(text, 'bumpmapScale', 0, 256);
		  f2.add(text, 'lifespan', 0, 20).step(1);
		  f2.add(text, 'density', 0, 1);
		  f2.add(text, 'opacity', 0, 1);
		  f2.add(text, 'scatter', 0, 100);
		  f2.add(text, 'spread', 0, 400);
		  f2.add(text, 'maxDist', 0, 512);
		  
		  var f3 = gui.addFolder('Shadow');
		  f3.add(text, 'useShadows');
		  f3.add(text, 'shadowLifespan', 0, 20).step(1);
		  f3.add(text, 'shadowDensity', 0, 1);
		  f3.add(text, 'shadowOpacity', 0, 1);
		  f3.add(text, 'shadowScatter', 0, 100);
		  f3.add(text, 'shadowSpread', 0, 1000);
		  f3.add(text, 'shadowMaxDist', 0, 512);
		  
		  gui.add(text, 'useMouse');
		  gui.add(text, "reset");
		  gui.add(text, "save");
		  gui.add(text, "pickColorMap");
		
          
        $('#colormapBtn').bind('change focus click', fileInputs);
		imgGroup = [$('#favoriteImg0'), $('#favoriteImg1'), $('#favoriteImg2')];
	}
	
	var by = "Patrick Gunderson"
	
	function pickColorMap(){
		$("#colormapPicker").show();
	}
	
	var imgGroup = [$('#favoriteImg0'), $('#favoriteImg1'), $('#favoriteImg2')];
	var selectedImage;
	var selectImg = function(f_img){
		deselectAllImg()
		f_img.css("border-color", "#fff")
		selectedImage = f_img;
		useRemoteColormap(f_img.attr("src"))
	}
	var deselectAllImg = function(){
		for (var i = 0; i < imgGroup.length; i++){
			console.log(i);
			imgGroup[i].css("border-color", "#333")
		}
	}
	function useRemoteColormap(f_src) {
		cmapImg = new Image();
		cmapImg.src = f_src
		cmapImg.onload = postCmapImgLoad;
	}
			
	var menuShowing = true;
	var saveBtn;
	var generateBtn;
	var fullscreenBtn;
	var fbShareBtn;
	var twShareBtn;
			
	$("#favoriteImg0").bind("touchStart", function(){
		selectImg($('#favoriteImg0'))
	})
	$("#favoriteImg1").bind("touchStart", function(){
		selectImg($('#favoriteImg1'))
	})
	$("#favoriteImg2").bind("touchStart", function(){
		selectImg($('#favoriteImg2'))
	})
	</script>
    </head>
    <body><div id="fb-root"></div>
	<div id="header"><span id="title">Silkbrush</span> by <a href="http://pat.theorigin.net">Patrick Gunderson</a></div>
		<div id="colormapPicker">
			<p>Pick one of your images to act as a colormap, or click to use one of my favorites below.</p>
			<input type="file" name="colormapBtn" id="colormapBtn" /><br/><br/>
			<img id="favoriteImg0" src="img/colormap.png" height="50" width="85" class="favoriteImg" onclick="selectImg($('#favoriteImg0'))"/>
			<img id="favoriteImg1" src="img/redgreen.jpg" height="50" width="85" class="favoriteImg" onclick="selectImg($('#favoriteImg1'))"/>
			<img id="favoriteImg2" src="img/comp54.jpg" height="50" width="85" class="favoriteImg" onclick="selectImg($('#favoriteImg2'))"/><br/><br/>
			<button id="closeBtn" onclick="$('#colormapPicker').hide()">close</button>
		</div>
		<div id="contentWrapper">

		</div>
		<div id="shareblock">
			<div class="share"><fb:like href="http://theorigin.net/silkbrush" send="false" layout="button_count" width="450" show_faces="true" colorscheme="dark" font="arial"></fb:like></div>
			<div class="share"><a href="http://twitter.com/share" class="twitter-share-button" data-url="http://theorigin.net/silkbrush" data-text="<?=$_twMessages[0]?>" data-via="gunderson">Tweet</a></div>
			<div class="share"><g:plusone count="true" href="http://theorigin.net/silkbrush"></div>
			<div class="share"><a href="http://www.tumblr.com/share/link?url=<?php echo urlencode("http://theorigin.net/silkbrush") ?>&name=<?php echo urlencode($title) ?>&description=<?php echo urlencode($description) ?>" title="Share on Tumblr" style="display:inline-block; text-indent:-9999px; overflow:hidden; width:61px; height:20px; background:url('http://platform.tumblr.com/v1/share_2T.png') top left no-repeat transparent;">Share on Tumblr</a></div>
			<div class="share"><a href="http://pinterest.com/pin/create/button/?url=http%3A%2F%2Ftheorigin.net%2Fsilkbrush&media=http%3A%2F%2Ftheorigin.net%2Fsilkbrush%2Fimg%2Fsilkbrush.png&description=My%20Description" class="pin-it-button" count-layout="horizontal"><img border="0" src="//assets.pinterest.com/images/PinExt.png" title="Pin It" /></a></div>
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

		  window.fbAsyncInit = function() {
				FB.init({
					appId : '267067926643119', // App ID
					status : false, // check login status
					cookie : true, // enable cookies to allow the server to access the session
					xfbml : true // parse XFBML
				});

				// Additional initialization code here
			}; 
			( function(d) {
					var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
					if (d.getElementById(id)) {
						return;
					}
					js = d.createElement('script');
					js.id = id;
					js.async = true;
					js.src = "//connect.facebook.net/en_US/all.js";
					ref.parentNode.insertBefore(js, ref);
				}(document));
		
		</script>
		
		<script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
		<script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>
		<script type="text/javascript" src="http://platform.tumblr.com/v1/share.js"></script>
		<script type="text/javascript" src="//assets.pinterest.com/js/pinit.js"></script>
    </body>
</html>
