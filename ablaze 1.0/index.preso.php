<!DOCTYPE html>

<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

        <title>Ablaze.js</title>

        <link rel="stylesheet" type="text/css" href="css/fullWeb.css" />
		<link type="text/css" href="css/ui-darkness/jquery-ui-1.8.14.custom.css" rel="stylesheet" />
		<link rel="stylesheet" type="text/css" href="css/fileinput.css" />		

		<script type="text/javascript" src="js/jquery-1.5.1.min.js"></script>
		<script type="text/javascript" src="js/perlin-noise-simplex.js"></script>


		<script type="text/javascript">
            var trackingEnabled = true;
            window.onload = onLoadComplete;
			
		
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
	var spinmap;
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
	var spinmapEffectSlider;

	var circleArrangementModeBtn
	var lineArrangementModeBtn;
	var randomArrangementModeBtn;

	var menuLabel;
	var newNumParticles;
	var newMaxDist;
	var spinmapEffect;
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
		spinmapEffect = 0.1;
		positionMethod = 0;
		colormap = $("#colormap");
		setupMenu()
		setupCanvas();
		reset();

		window.requestAnimFrame(oef);

		$("#drawer").bind("click", onClick);
		$(window).bind("keyup", onKeyUph);
		console.log("init()")
	}

	function setupCanvas(){
		var $canvas = $("<canvas id='drawer' />")
		$canvas[0].width = $(window).width();
		$canvas[0].height = $(window).height();
		$("#contentWrapper").append(html);
	}

	// function setInitialArrangementMode(e){
	// 	if (circleArrangementModeBtn.selected){
	// 		positionMethod = 0;
	// 	} else if (lineArrangementModeBtn.selected){
	// 		positionMethod = 1;
	// 	} else if (randomArrangementModeBtn.selected){
	// 		positionMethod = 2;
	// 	}
	// }

	// function showMenu(){
	// 	$("#menu").animate({left: 0}, 400);
	// 	menuShowing = true;
	// 	$("#showOptionsBtn").hide();
	// 	$("#hideOptionsBtn").show();
	// }

	// function hideMenu(){
	// 	$("#menu").animate({left: -360}, 400);
	// 	menuShowing = false;
	// 	$("#showOptionsBtn").show();
	// 	$("#hideOptionsBtn").hide();
	// }

	// function shareOnFB(e){
	// 	ExternalInterface.call("fb_share");
	// }

	// function shareOnTwitter(e){
	// 	ExternalInterface.call("tw_share");
	// }

	function reset(e){
		ctx = $("#drawer").getContext("2d");

		ctx.clearRect(0,0,$("#drawer").width(),$("#drawer").height())

		spinmap = new SimplexNoise();
		spinmap.offsetX = 0;
		spinmap.offsetY = 0;
		spinmap.scale = 200;

		centerX = $("#drawer").width() >> 1;
		centerY = $("#drawer").height() >> 1;

		lineColor = 0xffffff;
	
		//get settings from menu and set defaults
		positionMethod = Number($('input[name=alignmentRadio]:checked', '#alignmentRadio').val())
		drawMethod = Number($('input[name=drawMethodRadio]:checked', '#drawMethodRadio').val())
		numParticles = $( "#particleCountSlider" ).slider( "value" );
		maxDist = $( "#maxDistSlider" ).slider( "value" );
		spinmapEffect = $( "#spinmapEffectSlider" ).slider( "value" );
		makeParticles()
		ringRadius = Math.min($("#drawer").width(), $("#drawer").height()) >> 2;
	}

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
			p.color = 0xffffff;
			if (cmapCtx){
				propX = p.x / $("#drawer").width();
				propY = p.y / $("#drawer").height();
				pixeldata = cmapData.data
				col = (propX * cmapData.width) << 2;
				row = (propY * cmapData.height) >> 0;
				rowWidth = cmapData.width << 2
				p.color = (pixeldata[col + (row * rowWidth) + 0] << 16) | (data[col + (row * rowWidth) + 1] << 8) | data[col + (row * rowWidth) + 2];
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
			//var colorVal = 0x80; //spinmap.bitmapData.getPixel(propX * 100, propY * 100);
			//colorVal = (colorVal & 0xff) - 0x80;
			//var angleOffset = spinmapEffect * (colorVal / 0x80);
			colorVal = spinmap.noise((spinmap.offsetX + f_obj.x) / spinmap.scale, (spinmap.offsetY + f_obj.y) / spinmap.scale);
			var angleOffset = spinmapEffect * colorVal;
			f_obj.angle += angleOffset;
		}
		f_obj.x += Math.cos(f_obj.angle) * f_obj.speed;
		f_obj.y += Math.sin(f_obj.angle) * f_obj.speed;
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
		var img = $("#drawer").toDataURL("image/jpeg", 10);
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
			$( "#spinmapEffectSlider" ).slider({
				value:0.01,
				min: 0,
				max: .1,
				step: .001,
				slide: function( event, ui ) {
					$( "#spinmapEffectLabel" ).val(ui.value);
				}
			});
			$( "#spinmapEffectLabel" ).val( $( "#spinmapEffectSlider" ).slider( "value" ) );
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
					<label for="spinmapEffectLabel">spinmap Effect</label>
					<input type="text" id="spinmapEffectLabel"/>
				</p>
				<div id="spinmapEffectSlider"></div>
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
		<div id="spinmapHolder"></div>
		
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
