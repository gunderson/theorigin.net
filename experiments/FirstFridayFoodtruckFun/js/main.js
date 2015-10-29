TweenLite.defaultEase = Expo.easeInOut;

declare("breel.app", "js/main.js");
declare("breel.model.data", "js/main.js");

/****************************************************/

var pagewidth = $("body").width();
var pageheight = $("body").height();
breel.stage = new breel.display.Stage("#stageDiv", pagewidth, pageheight);

/****************************************************/

breel.model.CANVAS = {};
breel.model.CANVAS.HTMLElement = document.getElementById("mainCanvas");
breel.model.CANVAS.context = ctx = breel.model.CANVAS.HTMLElement.getContext("2d");

breel.model.CANVAS.width = 1024;
breel.model.CANVAS.height = 620;
breel.model.CANVAS.center = {
	x: breel.model.CANVAS.width >> 1,
	y: breel.model.CANVAS.height >> 1
};

breel.model.CANVAS.offset = {
	x: breel.model.CANVAS.width >> 1,
	y: breel.model.CANVAS.height >> 1
};

breel.model.ak = [
	new breel.app.display.MediaPoint({
		geo: {
			coordinates: [33.992443, -118.473784]
		}
	}), new breel.app.display.MediaPoint({
		geo: {
			coordinates: [33.991411, -118.46902]
		}
	}), new breel.app.display.MediaPoint({
		geo: {
			coordinates: [33.99013, -118.464128]
		}
	}), new breel.app.display.MediaPoint({
		geo: {
			coordinates: [33.989223, -118.462669]
		}
	}), new breel.app.display.MediaPoint({
		geo: {
			coordinates: [33.985042, -118.470072]
		}
	}), new breel.app.display.MediaPoint({
		geo: {
			coordinates: [33.991607, -118.475372]
		}
	})
];

/****************************************************/

breel.app.onResize = function() {
	breel.model.CANVAS.width = $(breel.model.CANVAS.HTMLElement).width();
	breel.model.CANVAS.height = $(breel.model.CANVAS.HTMLElement).height();
	breel.model.CANVAS.center = {
		x: breel.model.CANVAS.width >> 1,
		y: breel.model.CANVAS.height >> 1
	};
	breel.model.CANVAS.offset = {
		x: breel.model.CANVAS.width >> 1,
		y: breel.model.CANVAS.height >> 1
	};
};

/****************************************************/


breel.app.onDataReceived = function(siteData) {
	$(".main-container").show();
	breel.model.data.siteData = siteData;
	breel.model.parseData(siteData);

	breel.app.translateCoordinateArray(breel.model.ak);
	breel.app.translateCoordinateArray(breel.model.data.standard);
	breel.app.translateCoordinateArray(breel.model.data.trucks);

	breel.app.plot.plotField(true);

	$(window).bind('keyup', onKeyUp);


	//BEGIN ANIMATION
	if(!breel.model.started) {
		var kp;
		for(var i in breel.app.keypoints) {
			kp = breel.app.keypoints[i];
			getKeypointStepDelta(kp, breel.model.ak[kp.prevTargetPointId].geo.coordinates, breel.model.ak[kp.targetPointId].geo.coordinates);
		}
		breel.model.started = true;
		window.requestAnimFrame(rAF);
	}
};

breel.model.parseData = function(data){
	data.results = breel.app.removeNonGeocodedResponses( data.results.slice() );
	var trucks = [];
	var standard = [];
	var record;
	for (var i in data.results){
		record = data.results[i];
		if (record.type == "truck"){
			trucks.push( new breel.app.display.MediaPoint(record) );
		} else {
			standard.push( new breel.app.display.MediaPoint(record) );
		}
	}

	breel.model.data.trucks = trucks;
	breel.model.data.standard = standard;
};

breel.app.removeNonGeocodedResponses = function(oldArray) {
	var newArray = [];
	for(var i in oldArray) {
		if(oldArray[i].geo) {
			newArray.push(oldArray[i]);
		}
	}
	return newArray;
};

breel.app.plotGeo = function(arr) {
	var lat, lon;
	var ctx = breel.app.plot.fieldCanvas.getContext("2d");
	for(var i in arr) {
		ctx.beginPath();
		x = arr[i].x;
		y = arr[i].y;
		ctx.fillStyle = "rgba(255, 255, 255, 0.01)";
		ctx.arc(x, y, 2, 0, Math.TWO_PI);
		ctx.fill();
	}
};

breel.app.translateCoordinateArray = function(arr) {
	for(var i in arr) {
		arr[i].geo.coordinates = breel.app.translateCoordinates(arr[i].geo.coordinates);
		arr[i].x = arr[i].geo.coordinates.x;
		arr[i].y = arr[i].geo.coordinates.y;
	}
};

breel.app.plotAK = function() {
	var x, y, obj;
	var ctx = breel.app.plot.fieldCanvas.getContext("2d");
	ctx.clearRect(0, 0, breel.model.CANVAS.width, breel.model.CANVAS.height);
	ctx.beginPath();
	ctx.strokeStyle = "rgba(0,255,255,0.02)"; // Use strokeStyle to change the color.
	ctx.lineWidth = "1.5";
	obj = breel.model.ak[breel.model.ak.length - 1];
	ctx.moveTo(obj.x, obj.y);

	for(var i in breel.model.ak) {
		y = breel.model.ak[i].y;
		x = breel.model.ak[i].x;
		ctx.lineTo(x, y);
	}
	ctx.stroke();
};

breel.app.translateCoordinates = function(coordinates) {
	//coordinates = mediaPoint.geo.coordinates;
	coordinates = $.extend({}, coordinates);
	coordinates.y = breel.model.CANVAS.center.y + (coordinates[0] - 33.988269) * -100 * 320;
	coordinates.x = breel.model.CANVAS.center.x + (coordinates[1] + 118.468355) * 100 * 512;
	return coordinates;
};
breel.app.getCoordinates = function(coordinates) {
	var mycoordinates = {};
	mycoordinates.y = ((((coordinates[0] - breel.model.CANVAS.center.y) / 320) / -100) + 33.988269);
	mycoordinates.x = ((((coordinates[1] - breel.model.CANVAS.center.x) / 512) / 100) - 118.468355);
	return mycoordinates;
};

/****************************************************/

declare("breel.app.plot", "js/main.js");
breel.app.plot.plotField = function(clear) {
	if(!breel.app.plot.fieldImage || clear) {
		breel.app.plotAK();
		breel.app.plotGeo(breel.model.data.standard);
		breel.app.plot.plotStreetNames();

		//cache the field
		breel.app.plot.fieldImage = new Image();
		breel.app.plot.fieldImage.src = breel.app.plot.fieldCanvas.toDataURL();
	} else {
		ctx.drawImage(breel.app.plot.fieldImage, 0, 0);
	}
};

breel.app.plot.plotStreetNames = function() {
	var ctx = breel.app.plot.fieldCanvas.getContext("2d");
	ctx.save();
	var coordinates = breel.app.translateCoordinates([33.991425250000006, -118.4693315625]);
	ctx.translate(coordinates.x, coordinates.y);
	ctx.rotate(0.15);
	ctx.textAlign = "center";
	ctx.fillStyle = "rgba(0,255,255, 0.02)";
	ctx.textBaseline = 'top';
	ctx.font = "22px Puritan";
	ctx.fillText("ABBOT KINNEY", 0, 0);
	ctx.restore();

	ctx.save();
	coordinates = breel.app.translateCoordinates([33.9870815, -118.466401875]);
	ctx.translate(coordinates.x, coordinates.y);
	ctx.rotate(-0.34);
	ctx.textAlign = "center";
	ctx.fillStyle = "rgba(0,255,255, 0.02)";
	ctx.textBaseline = 'top';
	ctx.font = "22px Puritan";
	ctx.fillText("VENICE", 0, 0);
	ctx.restore();

	ctx.save();
	coordinates = breel.app.translateCoordinates([33.988206500000004, -118.47274953125]);
	ctx.translate(coordinates.x, coordinates.y);
	ctx.rotate(0.64);
	ctx.textAlign = "center";
	ctx.fillStyle = "rgba(0,255,255, 0.02)";
	ctx.textBaseline = 'top';
	ctx.font = "22px Puritan";
	ctx.fillText("PACIFIC", 0, 0);
	ctx.restore();
};

breel.app.plot.plotConnectors = function(keypoint) {
	var level;
	for(var i in keypoint.closePoints) {
		ctx.beginPath();
		level = Math.floor(255 * ((keypoint.pointRange - keypoint.closePoints[i].dist) / keypoint.pointRange));
		ctx.strokeStyle = "rgba(" + level + ", " + (255 - level) + ", 255, 0.1)";
		ctx.lineWidth = "1";
		ctx.moveTo(keypoint.x, keypoint.y);
		ctx.lineTo(keypoint.closePoints[i].x, keypoint.closePoints[i].y);
		// ctx.moveTo(keypoint.closePoints[i].x + keypoint.closePoints[i].dist, keypoint.closePoints[i].y);
		// ctx.arc(keypoint.closePoints[i].x, keypoint.closePoints[i].y, keypoint.closePoints[i].dist, 0, Math.TWO_PI);
		ctx.stroke();
	}
};

breel.app.Keypoint = {
	x: 0,
	y: 0,
	dx: 0,
	dy: 0,
	px: 0,
	py: 0,
	startX: 0,
	startY: 0,
	endX: 0,
	endY: 0,
	stepDelta: 5,
	stepDeltaTarget: 2,
	pointRange: 150,
	stepsRemaining: 0,
	targetPointId: 1,
	prevTargetPointId: 0,
	points: []
};



moveKeypoint = function(keypoint) {
	keypoint.x += keypoint.dx;
	keypoint.y += keypoint.dy;
	if(--keypoint.stepsRemaining === 0) {
		keypoint.prevTargetPointId = keypoint.targetPointId;
		if(++keypoint.targetPointId >= breel.model.ak.length) {
			keypoint.targetPointId = 0;
		}
		getKeypointStepDelta(keypoint, breel.model.ak[keypoint.prevTargetPointId].geo.coordinates, breel.model.ak[keypoint.targetPointId].geo.coordinates);
	}
};

moveRadarpoint = function(radarpoint) {
	radarpoint.x = breel.model.CANVAS.center.x + (radarpoint.radius * Math.cos(radarpoint.angleOffset + (breel.model.radarInc * radarpoint.rotationRate)));
	radarpoint.y = breel.model.CANVAS.center.y + (radarpoint.radius * Math.sin(radarpoint.angleOffset + (breel.model.radarInc * radarpoint.rotationRate)));
};

getKeypointStepDelta = function(keypoint, startPoint, endPoint) {
	var dx = endPoint.x - startPoint.x;
	var dy = endPoint.y - startPoint.y;
	var dist = Math.sqrt((dx * dx) + (dy * dy));
	var numSteps = Math.ceil(dist / keypoint.stepDeltaTarget);
	keypoint.x = startPoint.x;
	keypoint.y = startPoint.y;
	keypoint.dx = dx / numSteps;
	keypoint.dy = dy / numSteps;
	keypoint.stepsRemaining = numSteps;
};

getPointsWithinRange = function(keypoint, points) {
	var dx, dy, dist;
	var closePoints = [];
	for(var i in points) {
		dx = keypoint.x - points[i].geo.coordinates.x;
		dy = keypoint.y - points[i].geo.coordinates.y;
		dist = Math.sqrt((dx * dx) + (dy * dy));
		if(dist < keypoint.pointRange) {
			closePoints.push({
				x: points[i].geo.coordinates.x,
				y: points[i].geo.coordinates.y,
				dist: dist
			});
		}
	}
	keypoint.closePoints = closePoints;
};

rAF = function() {
	breel.app.plot.plotField();
	//fade stage
	ctx.fillStyle = "rgba(0,0,0,0.02)";
	ctx.fillRect(0, 0, breel.model.CANVAS.width, breel.model.CANVAS.height);

	for(var i in breel.app.keypoints) {
		kp = breel.app.keypoints[i];
		moveKeypoint(kp);
		getPointsWithinRange(kp, breel.model.data.standard);
		breel.app.plot.plotConnectors(kp);
	}
	// breel.model.radarInc++;
	// for (i in breel.app.radarPoints){
	// 	rp = breel.app.radarPoints[i];
	// 	moveRadarpoint(rp);
	// 	getPointsWithinRange(rp, breel.model.siteData.results);
	// 	breel.app.plot.plotConnectors(rp);
	// }
	window.requestAnimFrame(rAF);
};

/****************************************************/

function onKeyUp(event) {
	switch(event.which) {
	case 13:
		//enter
		//intentional fallthrough
	case 32:
		//spacebar
		breel.app.triggerSnapshot();
		event.preventDefault();
	}
	//console.log(event.which);
}

/****************************************************/


breel.model.pollForData = function() {
	$.ajax({
		type: "GET",
		dataType: "jsonp",
		url: "http://search.twitter.com/search.json",
		data: {
			geocode: "\"33.991269,-118.468355,0.75km\"",
			rpp: "100"
		},
		success: breel.app.onDataReceived
	});

	// https://api.instagram.com/v1/media/search?lat=33.994&lng=-118.46902&distance=750
};

function init() {

	breel.model.dataPoller = setInterval(breel.model.pollForData, 5000);
	breel.model.pollForData(); // force the first poll
	breel.app.plot.fieldCanvas = document.createElement("canvas");
	breel.app.plot.fieldCanvas.width = breel.model.CANVAS.width;
	breel.app.plot.fieldCanvas.height = breel.model.CANVAS.height;

	//set up radar beacons
	//keypoints follow abbot kinney
	breel.app.keypoints = [
	breel.extend({}, breel.app.Keypoint), breel.extend({}, breel.app.Keypoint), breel.extend({}, breel.app.Keypoint), breel.extend({}, breel.app.Keypoint), breel.extend({}, breel.app.Keypoint), breel.extend({}, breel.app.Keypoint)];
	breel.app.keypoints[0].targetPointId = 1;
	breel.app.keypoints[1].prevTargetPointId = 1;
	breel.app.keypoints[1].targetPointId = 2;
	breel.app.keypoints[2].prevTargetPointId = 2;
	breel.app.keypoints[2].targetPointId = 3;
	breel.app.keypoints[3].prevTargetPointId = 3;
	breel.app.keypoints[3].targetPointId = 4;
	breel.app.keypoints[4].prevTargetPointId = 4;
	breel.app.keypoints[4].targetPointId = 5;
	breel.app.keypoints[5].prevTargetPointId = 5;
	breel.app.keypoints[5].targetPointId = 0;


	//radarpoints rotate around an abstract circle
	// breel.model.radarInc = 0;
	// breel.app.radarPoints = [
	// 	// breel.extend({}, breel.app.Keypoint),
	// 	// breel.extend({}, breel.app.Keypoint),
	// 	breel.extend({}, breel.app.Keypoint),
	// 	breel.extend({}, breel.app.Keypoint)
	// ];
	// breel.app.radarPoints[0].radius = 20;
	// breel.app.radarPoints[0].angleOffset = 0;
	// breel.app.radarPoints[0].rotationRate = 0.02;
	// breel.app.radarPoints[0].pointRange = 200;
	// breel.app.radarPoints[1].radius = 40;
	// breel.app.radarPoints[1].angleOffset = Math.TWO_PI * 0.5;
	// breel.app.radarPoints[1].rotationRate = 0.01;
	// breel.app.radarPoints[1].pointRange = 200;
	// breel.app.radarPoints[2].radius = 300;
	// breel.app.radarPoints[2].angleOffset = Math.PI;
	// breel.app.radarPoints[2].rotationRate = 0.01;
	// breel.app.radarPoints[2].pointRange = 200;
	// breel.app.radarPoints[3].radius = 150;
	// breel.app.radarPoints[3].angleOffset = Math.TWO_PI * 0.75;
	// breel.app.radarPoints[3].rotationRate = -0.015;
	// breel.app.radarPoints[3].pointRange = 200;
}

$(document).ready(init);