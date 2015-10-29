<!DOCTYPE html>
<?
$cdnRoot = "";
$pageRoot = "";

$trackingString = '';

$tabsAPI = '';
$title = "partciplejs";
$desc = "Partciplejs is an experiment in digital painting. It takes photos and helps you extract the instrumental track.";
$img = "http://theorigin.net/partciplejs/img/partciplejs_small.jpg";
$_twMessages[] = "Turn your images into abstract paintings with partciplejs, an experiment in digital painting. http://theorigin.net/partciplejs (via @gunderson)";
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
        <meta name="keywords" content="partciplejs, creative js, creative, js, javascript, html5, flash, art" />
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
                    var twMessage = "Partciplejs is an experiment in digital painting. It takes photos and helps you extract the instrumental track. http://theorigin.net/partciplejs (via @gunderson)";
                    
		    window.open('http://twitter.com/intent/tweet?text=' + twMessage,'sharer','toolbar=0,status=0,width=800,height=450');
		}
			
			
			
			
			
	</script>

	<script>

	//////////////////////////////////////////////////
	// Participle.js
	//////////////////////////////////////////////////
	Math.TWO_PI = Math.PI * 2;

	var participle = {};

	var particles;
	var shdowParticles;
	var lineColor;
	var canvas;
	var drawerWidth;
	var drawerHeight;
	var drawer;
	var bumpmap;
	var colormap;
	var ctx;
	participle.throttle = .5;
	participle.lifespan = 5;
	var mouseIsDown = false;

	var positionMethod = 0;
	var drawMethod = 0;

	var drawCommands;
	var drawPoints;

	
	participle.opacity = 0.5
	participle.density = 1
	participle.spread = 8
	participle.bumpmapScale = 100;
	participle.bumpmapEffect = .4;
	participle.scatter = 0

	var emitters = [];
	participle.numEmitters = 8;
	participle.emitterSpinRate = 0.02;
	participle.emitterRadius = 180;
	participle.emitterBaseAngle = 0;

	//------------------------------------
	// particle groups

	var particleGroups = [];
	participle.pulseSize = 32; // must be >= 3; //angle of each particle will be evenly spread around a circle, with variation in group[0] angle and pulseAngleVariation
	participle.pulseSpeedVariation = 0.1; //max variation from pulse base speed 
	participle.pulseAngleVariation = 0.3; //max variation from base angle

	//------------------------------------
	
	participle.useShadows = false;
	participle.shadowFillBehind = true;
	participle.shadowOpacity = 0.3
	participle.shadowDepth = 0.4;
	participle.shadowLayers = 3;
	
	participle.useMouse = false;
	var inc = 0;
	participle.rateX0 = .0335
	participle.rateX1 = .0212
	participle.rateX2 = .08223
	participle.rateX3 = .2
	participle.lengthX0 = .35
	participle.lengthX1 = .35
	participle.lengthX2 = .40
	participle.lengthX3 = .216
	participle.rateY0 = .05235
	participle.rateY1 = .022
	participle.rateY2 = .043223
	participle.rateY3 = .6
	participle.lengthY0 = .5
	participle.lengthY1 = .25
	participle.lengthY2 = .35
	participle.lengthY3 = .057
	
	var rateX = [participle.rateX0, participle.rateX1, participle.rateX2, participle.rateX3]
	var lengthX = [participle.lengthX0, participle.lengthX1, participle.lengthX2, participle.lengthX3]
	var rateY = [participle.rateY0, participle.rateY1, participle.rateY2, participle.rateY3]
	var lengthY = [participle.lengthY0, participle.lengthY1, participle.lengthY2, participle.lengthY3]

	var mouseEvent;

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
		colormap = $("#colormap");
		setupCanvas();
		reset();
		//frameTimer = setInterval(oef, framerate)
		window.requestAnimFrame(oef);
		$("#drawer").bind("dblclick", onClick);
		$("#drawer").bind("mousedown", onMouseDown);
		$("#drawer").bind("mouseup", onMouseUp);
		$(window).bind("keyup", onKeyUph);
		console.log("init()")
	}

	function Emitter(){
		var emitter = {
			groups: []
		}
		return emitter;
	}

	function rebuildEmitters(numEmitters){
		var emitters = [];
		while (numEmitters--){
			emitters.push(new Emitter());
		}
		participle.emitterBaseAngle = Math.TWO_PI / emitters.length;
		return emitters
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
		bumpmap.scale = participle.bumpmapScale;

		centerX = $("#drawer").width() >> 1;
		centerY = $("#drawer").height() >> 1;

		lineColor = 0xffffff;
		
		emitters = rebuildEmitters(participle.numEmitters)
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



	function oef(e){
		rateX = [participle.rateX0, participle.rateX1, participle.rateX2, participle.rateX3]
		lengthX = [participle.lengthX0, participle.lengthX1, participle.lengthX2, participle.lengthX3]
		rateY = [participle.rateY0, participle.rateY1, participle.rateY2, participle.rateY3]
		lengthY = [participle.lengthY0, participle.lengthY1, participle.lengthY2, participle.lengthY3]
		
		inc += participle.throttle * 2;
		
		if (participle.useMouse){
			analyzeMouse(mouseEvent);
		} else {
			analyzeMouse();
		}

		drawerWidth = $("#drawer").width();
		drawerHeight= $("#drawer").height();

		moveEmitters();
		var emitter;
		var numGroups;
		for (var emitterIndex in emitters){
			emitter = emitters[emitterIndex];
			if (participle.useShadows){
				groupIndex = emitter.groups.length;
				while(--groupIndex + 1){
					makeShadowGroups(emitter.groups[groupIndex], participle.shadowDepth, participle.shadowLayers, participle.shadowFillBehind)
					emitter.groups[groupIndex].shadowGroups.forEach(drawShadowRing);
				}
			}
			spawnParticles(emitter);
			emitter.groups.forEach(drawRing);
			emitter.groups.forEach(moveParticles);
			groupIndex = emitter.groups.length;
			while(groupIndex--){
				if (!emitter.groups[groupIndex] || ++emitter.groups[groupIndex].age > participle.lifespan){
					emitter.groups.splice(groupIndex, 1).particles = null;
				}
			}
		}

		window.requestAnimFrame(oef)
	}
	
	function makeParticle(f_x, f_y, f_angle, f_speed){
		var p = {};
		p.angle = f_angle
		p.speed = f_speed
		p.ox = mouseData.px1;
		p.oy = mouseData.py1;
		p.x = f_x;
		p.y = f_y;
		p.px = p.x;
		p.py = p.y;
		p.dx = 0;
		p.dy = 0;
		p.age = 0;
		p.distances = new Array();
		p.color = 0xffffff;//colormap.bitmapData.getPixel(propX * colormap.width, propY * colormap.height);
		if (cmapCtx){
			propX = p.x / drawerWidth;
			propY = p.y / drawerHeight;
			data = cmapData.data//.getImageData(propX * cmapCtx.width, propY * cmapCtx.height,1, 1)
			col = (propX * cmapData.width) << 2;
			row = (propY * cmapData.height) >> 0;
			rowWidth = cmapData.width << 2
			p.color = (data[col + (row * rowWidth) + 0] << 16) | (data[col + (row * rowWidth) + 1] << 8) | data[col + (row * rowWidth) + 2];
			
		}
		return p;
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
			//use epicycles!
			var stageWidth = drawerWidth
			var stageHeight = drawerHeight
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
	
	function spawnParticles(emitter){
		if (Math.random() < participle.density){
			var baseAngleDelta = Math.TWO_PI / participle.pulseSize;
			var speed = (1/Math.max(mouseData.speed, 0.01) * participle.spread)//inversly proportionate to mouse speed
			if (mouseData.speed > 0){
				var group = {
					particles:[],
					color: 0xffffff,
					age: 0,
					speed: speed,
					emitter: emitter,
					shadowGroups: []
				};
				var i = -1;
				while (++i < participle.pulseSize){
					speed = (1/Math.max(mouseData.speed, 0.01) * participle.spread)//inversly proportionate to mouse speed
					speed += ((Math.random()) * participle.pulseSpeedVariation * speed)
					group.particles.push(
						makeParticle(
							emitter.x, 
							emitter.y, 
							mouseData.angle + (baseAngleDelta * (i)) + ((Math.random() - 0.5) * participle.pulseAngleVariation * baseAngleDelta), //random variation within range of pulseAngleVariation
							//spread - ((Math.random()) * pulseSpeedVariation * spread) + (1/ mouseData.speed * spread) //random variation within range of pulseAngleVariation
							speed 
						)
					)
				}
			}
			emitter.groups.push(group);
		}
	}

	function moveEmitters(){
		var i = emitters.length;
		while(i--){
			emitters[i].x = mouseData.x + (participle.emitterRadius * Math.cos((participle.emitterBaseAngle * i) + (inc * participle.emitterSpinRate)));
			emitters[i].y = mouseData.y + (participle.emitterRadius * Math.sin((participle.emitterBaseAngle * i) + (inc * participle.emitterSpinRate)));
		}
			console.log([participle.emitterRadius, participle.emitterBaseAngle, i, inc, participle.emitterSpinRate])
	}

	function moveParticles(f_obj, f_id, f_array){
		var p;
		var i = -1;

		if (!f_obj){
			return
		}

		if (cmapCtx){
			//use particl's postion
			// propX = p.x / drawerWidth;
			// propY = p.y / drawerHeight;

			//use emitter's position
			propX = f_obj.emitter.x / drawerWidth;
			propY = f_obj.emitter.y / drawerHeight;

			data = cmapData.data//.getImageData(propX * cmapCtx.width, propY * cmapCtx.height,1, 1)
			col = (propX * cmapData.width) << 2;
			row = (propY * cmapData.height) >> 0;
			rowWidth = cmapData.width << 2
			f_obj.color = (data[col + (row * rowWidth) + 0] << 16) | (data[col + (row * rowWidth) + 1] << 8) | data[col + (row * rowWidth) + 2];
		} 

		while (++i < f_obj.particles.length){
			p = f_obj.particles[i];
			p.px = p.x;
			p.py = p.y;
			if (p.x < 0 || p.x > drawerWidth || p.y < 0 || p.y > drawerHeight){
				//off stage, ignore that bitch
			} else {
				var propX = p.x / drawerWidth;
				var propY = p.y / drawerHeight;
				//var colorVal = 0x80; //bumpmap.bitmapData.getPixel(propX * 100, propY * 100);
				//colorVal = (colorVal & 0xff) - 0x80;
				//var angleOffset = bumpmapEffect * (colorVal / 0x80);
				colorVal = bumpmap.noise((bumpmap.offsetX + p.x) / bumpmap.scale, (bumpmap.offsetY + p.y) / bumpmap.scale);
				var angleOffset = participle.bumpmapEffect * colorVal;
				p.angle += angleOffset;
				
			}
			p.dx = Math.cos(p.angle) * p.speed;
			p.dy = Math.sin(p.angle) * p.speed;
			p.x += p.dx;
			p.y += p.dy;
		}
		
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

	function drawRing(f_obj, f_id, f_array){
		var i = -1;
		var p0;
		var p1;
		var radius;
		var r;
		var g;
		var b;
		var offsetX = 0;
		var offsetY = 0;
		if (!f_obj){
			return
		}

		r = f_obj.color >> 16;
		g = (f_obj.color >> 8) & 0xff;
		b = f_obj.color & 0xff;
		ctx.beginPath();
		f_obj.opacity = Math.max(0, (participle.opacity - (f_obj.speed / 100)))
		ctx.fillStyle = "rgba("+ r +","+ g +","+ b +","+ f_obj.opacity +")";

		while(++i < f_obj.particles.length){
			p0 = f_obj.particles[i];
			p1 = (i === f_obj.particles.length - 1) ? f_obj.particles[0] : f_obj.particles[i+1];

			if (participle.scatter > 0){
				offsetX = participle.scatter*Math.random()-participle.scatter/2
				offsetY = participle.scatter*Math.random()-participle.scatter/2
			}
			

			ctx.moveTo(p0.x + offsetX, p0.y + offsetY);
			ctx.lineTo(p1.x + offsetX, p1.y+ offsetY)
			ctx.lineTo(p1.px + offsetX, p1.py+ offsetY)
			ctx.lineTo(p0.px + offsetX, p0.py + offsetY)
				
		}
		ctx.closePath()
		ctx.fill();
	}

	function drawShadowRing(f_obj, f_id, f_array){
		var i = -1;
		var p0;
		var p1;
		var radius;
		var offsetX = 0;
		var offsetY = 0;
		if (!f_obj){
			return
		}
		ctx.beginPath();
		ctx.fillStyle = "rgba(0,0,0,"+ (f_obj.opacity * participle.shadowOpacity / participle.shadowLayers) + ")";

		while(++i < f_obj.particles.length){
			p0 = f_obj.particles[i];
			p1 = (i === f_obj.particles.length - 1) ? f_obj.particles[0] : f_obj.particles[i+1];

			if (participle.scatter > 0){
				offsetX = participle.scatter*Math.random()-participle.scatter/2
				offsetY = participle.scatter*Math.random()-participle.scatter/2
			}
			

			ctx.moveTo(p0.x + offsetX, p0.y + offsetY);
			ctx.lineTo(p1.x + offsetX, p1.y+ offsetY)
			ctx.lineTo(p1.px + offsetX, p1.py+ offsetY)
			ctx.lineTo(p0.px + offsetX, p0.py + offsetY)
				
		}
		ctx.closePath()
		ctx.fill();
	}

	function makeShadowGroups(group, shadowDepth, levels, fillBehind){
		group.shadowGroups = [];
		var i = levels;
		var levelRange;
		while (i--){ // for each level
			levelRange = i * shadowDepth / levels 
			if (fillBehind){
				group.shadowGroups.push(makeFullShadowGroup(group, levelRange));
			} else {
				group.shadowGroups.push(makeInnerShadowGroup(group, levelRange));
				group.shadowGroups.push(makeOuterShadowGroup(group, levelRange));
			}
		}
	}

	function makeFullShadowGroup(group, shadowDepth){
		var shadowGroup = {
			particles:[],
			color: 0,
			opacity: group.opacity
		};
		var sp;
		var p;
		var i = group.particles.length;
		while (--i + 1){
			p = group.particles[i]
			sp = {};
			$.extend(sp, p);
			sp.x = p.x + (p.dx * shadowDepth);
			sp.y = p.y + (p.dy * shadowDepth);
			sp.px = p.px - (p.dx * shadowDepth);
			sp.py = p.py - (p.dy * shadowDepth);
			shadowGroup.particles.push(sp);
		}
		return shadowGroup;
	}
	
	function makeInnerShadowGroup(group, shadowDepth){
		var shadowGroup = {
			particles:[],
			color: 0,
			opacity: group.opacity
		};
		var sp;
		var p;
		var i = group.particles.length;
		while (i--){
			p = group.particles[i]
			sp = {};
			$.extend(sp, p);
			sp.x = p.px - (p.speed * shadowDepth * Math.cos(p.angle));
			sp.y = p.py - (p.speed * shadowDepth * Math.sin(p.angle));
			sp.px = p.px;
			sp.py = p.py;
			shadowGroup.particles.push(sp);
		}
		return shadowGroup;
	}
	
	function makeOuterShadowGroup(group, shadowDepth){
		var shadowGroup = {
			particles:[],
			color: 0,
			opacity: group.opacity
		};
		var sp;
		var p;
		var i = group.particles.length;
		while (i--){
			p = group.particles[i]
			sp = {};
			$.extend(sp, p);
			sp.x = p.x + (p.speed * shadowDepth * Math.cos(p.angle));
			sp.y = p.y + (p.speed * shadowDepth * Math.sin(p.angle));
			sp.px = p.x;
			sp.py = p.y;
			shadowGroup.particles.push(sp);
		}
		return shadowGroup;
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
		var filename = "partciplejs_" +now.fullYear + "-" + now.month + "-" + now.date + "_" + now.hours +"-"+ now.minutes +"-"+ now.seconds + ".png";
		var img = document.getElementById("drawer").toDataURL("image/jpeg", 10);
		//window.open(img, "_blank")
		saveToDisk(img, "partciplejs.jpg");
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
		gui.remember(participle);
		var f1 = gui.addFolder('Movement');
		  f1.add(participle, 'throttle', -1, 1);
		  f1.add(participle, 'rateX0', 0, 1);
		  f1.add(participle, 'rateX1', 0, 1);
		  f1.add(participle, 'rateX2', 0, 1);
		  f1.add(participle, 'rateX3', 0, 3);
		  
		  f1.add(participle, 'lengthX0', 0, 1);
		  f1.add(participle, 'lengthX1', 0, 1);
		  f1.add(participle, 'lengthX2', 0, 1);
		  f1.add(participle, 'lengthX3', 0, 1);
		  
		  f1.add(participle, 'rateY0', 0, 1);
		  f1.add(participle, 'rateY1', 0, 1);
		  f1.add(participle, 'rateY2', 0, 1);
		  f1.add(participle, 'rateY3', 0, 3);
		  
		  f1.add(participle, 'lengthY0', 0, 1);
		  f1.add(participle, 'lengthY1', 0, 1);
		  f1.add(participle, 'lengthY2', 0, 1);
		  f1.add(participle, 'lengthY3', 0, 1);

		var emitterFolder = gui.addFolder('Emitters');
		  var updateEmittersController = emitterFolder.add(participle, 'numEmitters', 0, 16).step(1);
		  updateEmittersController.onFinishChange(function(value) {reset()});
		  emitterFolder.add(participle, 'emitterRadius', 0, 256);
		  emitterFolder.add(participle, 'emitterSpinRate', 0, 0.5);

		var f2 = gui.addFolder('Brush');
		  f2.add(participle, 'bumpmapEffect', 0, 1);
		  f2.add(participle, 'bumpmapScale', 0, 256);
		  f2.add(participle, 'lifespan', 0, 20).step(1);
		  f2.add(participle, 'density', 0, 1);
		  f2.add(participle, 'opacity', 0, 1);
		  f2.add(participle, 'scatter', 0, 100);
		  f2.add(participle, 'spread', 0, 100);
		  f2.add(participle, 'pulseSize', 3, 128);
		  f2.add(participle, 'pulseSpeedVariation', 0, 1);
		  f2.add(participle, 'pulseAngleVariation', 0, 1);
		  
		  var f3 = gui.addFolder('Shadow');
		  f3.add(participle, 'useShadows');
		  f3.add(participle, 'shadowFillBehind');
		  f3.add(participle, 'shadowOpacity', 0, 1);
		  f3.add(participle, 'shadowDepth', 0, 1);
		  f3.add(participle, 'shadowLayers', 0, 10).step(1);
		  
		  gui.add(participle, 'useMouse');
		  gui.add(self, "reset");
		  gui.add(self, "save");
		  gui.add(self, "pickColorMap");
		
          
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
	<div id="header"><span id="title">partciplejs</span> by <a href="http://pat.theorigin.net">Patrick Gunderson</a></div>
		<div id="colormapPicker">
			<p>Pick an image</p>
			<input type="file" name="colormapBtn" id="colormapBtn" /><br/><br/>
			<p> or click one of these<p>
			<img id="favoriteImg0" src="img/colormap0.jpg" height="85" width="85" class="favoriteImg" onclick="selectImg($('#favoriteImg0'))"/>
			<img id="favoriteImg1" src="img/colormap1.jpg" height="85" width="85" class="favoriteImg" onclick="selectImg($('#favoriteImg1'))"/>
			<img id="favoriteImg2" src="img/colormap2.jpg" height="85" width="85" class="favoriteImg" onclick="selectImg($('#favoriteImg2'))"/><br/><br/>
			<p><button id="closeBtn" onclick="$('#colormapPicker').hide()">close</button></p>
		</div>
		<div id="contentWrapper">

		</div>
		<div id="shareblock">
			<div class="share"><fb:like href="http://theorigin.net/partciplejs" send="false" layout="button_count" width="450" show_faces="true" colorscheme="dark" font="arial"></fb:like></div>
			<div class="share"><a href="http://twitter.com/share" class="twitter-share-button" data-url="http://theorigin.net/partciplejs" data-text="<?=$_twMessages[0]?>" data-via="gunderson">Tweet</a></div>
			<div class="share"><g:plusone count="true" href="http://theorigin.net/partciplejs"></div>
			<div class="share"><a href="http://www.tumblr.com/share/link?url=<?php echo urlencode("http://theorigin.net/partciplejs") ?>&name=<?php echo urlencode($title) ?>&description=<?php echo urlencode($description) ?>" title="Share on Tumblr" style="display:inline-block; text-indent:-9999px; overflow:hidden; width:61px; height:20px; background:url('http://platform.tumblr.com/v1/share_2T.png') top left no-repeat transparent;">Share on Tumblr</a></div>
			<div class="share"><a href="http://pinterest.com/pin/create/button/?url=http%3A%2F%2Ftheorigin.net%2Fpartciplejs&media=http%3A%2F%2Ftheorigin.net%2Fpartciplejs%2Fimg%2Fpartciplejs.png&description=My%20Description" class="pin-it-button" count-layout="horizontal"><img border="0" src="//assets.pinterest.com/images/PinExt.png" title="Pin It" /></a></div>
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
