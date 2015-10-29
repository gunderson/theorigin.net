TweenLite.defaultEase = Expo.easeInOut;
ctx = document.getElementById("mainCanvas").getContext("2d");

declare("breel.app", "js/main.js");
declare("breel.model", "js/main.js");

/****************************************************/

breel.model.CANVAS = {
	width: 1024,
	height: 512
};
breel.model.CANVAS.center = {
	x: breel.model.CANVAS.width >> 1,
	y: breel.model.CANVAS.height >> 1
};

breel.model.CANVAS.offset = {
	x: breel.model.CANVAS.width >> 1,
	y: breel.model.CANVAS.height >> 1
};

breel.model.ak = [{
		geo: {coordinates: [33.992443,-118.473784]}
	},
	{
		geo: {coordinates: [33.991411,-118.46902]}
	},
	{
		geo: {coordinates: [33.99013,-118.464128]}
	},
	{
		geo: {coordinates: [33.989223,-118.462669]}
	},
	{
		geo: {coordinates: [33.985042,-118.470072]}
	},
	{
		geo: {coordinates: [33.991607,-118.475372]}
	}
];

/****************************************************/

breel.app.removeNonGeocodedResponses = function(oldArray){
	var newArray = [];
	for (var i in oldArray){
		if (oldArray[i].geo){
			newArray.push(oldArray[i]);
		}
	}
	return newArray;
};

breel.app.onInitDataReceived = function(siteData) {
	siteData.results = breel.app.removeNonGeocodedResponses(siteData.results);
	$(".main-container").show();
	breel.model.siteData = siteData;

	breel.app.translateCoordinateArray(breel.model.ak);
	breel.app.translateCoordinateArray(breel.model.siteData.results);

	breel.app.plot.plotField();
	
	$(window).bind('keyup', onKeyUp);

	breel.app.keypoints = [
		breel.extend({}, breel.app.Keypoint),
		breel.extend({}, breel.app.Keypoint),
		breel.extend({}, breel.app.Keypoint),
		breel.extend({}, breel.app.Keypoint),
	];

	breel.app.keypoints[0].targetPointId = 1;
	breel.app.keypoints[1].prevTargetPointId = 1;
	breel.app.keypoints[1].targetPointId = 2;
	breel.app.keypoints[2].prevTargetPointId = 2;
	breel.app.keypoints[2].targetPointId = 3;
	breel.app.keypoints[3].prevTargetPointId = 3;
	breel.app.keypoints[3].targetPointId = 4;
	//BEGIN ANIMATION
	var kp;
	for (var i in breel.app.keypoints){
		kp = breel.app.keypoints[i];
		getKeypointStepDelta(kp, breel.model.ak[kp.prevTargetPointId].geo.coordinates, breel.model.ak[kp.targetPointId].geo.coordinates);
	}
	window.requestAnimFrame(rAF);
};

breel.app.plotGeo = function(arr){
	var lat, lon;
	for (var i in arr){
		ctx.beginPath();
		y = arr[i].geo.coordinates.y;
		x = arr[i].geo.coordinates.x;
		ctx.fillStyle = "rgba(255, 255, 255, 0.01)";
		ctx.arc(x, y, 2, 0, Math.TWO_PI);
		ctx.fill();
	}
}

breel.app.translateCoordinateArray = function(arr){
	for (var i in arr){
		arr[i].geo.coordinates = breel.app.translateCoordinates(arr[i].geo.coordinates);
	}
};

breel.app.plotAK = function(){
	var x, y, coordinates;
	ctx.beginPath();
	ctx.strokeStyle = "rgba(0,255,255,0.02)";  // Use strokeStyle to change the color.
	ctx.lineWidth = "1";
	coordinates = breel.model.ak[breel.model.ak.length - 1].geo.coordinates;
	ctx.moveTo(coordinates.x, coordinates.y);

	for (var i in breel.model.ak){
		y = breel.model.ak[i].geo.coordinates.y;
		x = breel.model.ak[i].geo.coordinates.x;
		ctx.lineTo(x, y);
	}
	ctx.stroke();
};

breel.app.translateCoordinates = function(coordinates){
	coordinates = coordinates.slice();
	coordinates.y = breel.model.CANVAS.center.y + (coordinates[0] - 33.988269) * -100 * 320;
	coordinates.x = breel.model.CANVAS.center.x + (coordinates[1] + 118.468355) * 100 * 512;
	return coordinates;
};

/****************************************************/

declare("breel.app.plot", "js/main.js");
breel.app.plot.plotField = function(){
	breel.app.plotAK();
	breel.app.plotGeo(breel.model.siteData.results);
};

breel.app.plot.plotStreetNames = function(){
	ctx.save();
	var newx = -50 + breel.model.CANVAS.center.x;
	var newy = -101 + breel.model.CANVAS.center.y;
	ctx.translate(newx, newy);
	ctx.rotate(.15);
	ctx.textAlign = "center";
	ctx.fillStyle = "rgba(0,255,255, 0.02)";
	ctx.textBaseline = 'top';
	ctx.font = "22px Puritan";
	ctx.fillText("ABBOT KINNEY", 0, 0);
	ctx.restore();
	
	ctx.save();
	newx = 100 + breel.model.CANVAS.center.x;
	newy = 38 + breel.model.CANVAS.center.y;
	ctx.translate(newx, newy);
	ctx.rotate(-0.34);
	ctx.textAlign = "center";
	ctx.fillStyle = "rgba(0,255,255, 0.02)";
	ctx.textBaseline = 'top';
	ctx.font = "22px Puritan";
	ctx.fillText("VENICE", 0, 0);
	ctx.restore();
	
	ctx.save();
	newx = -225 + breel.model.CANVAS.center.x;
	newy = 2 + breel.model.CANVAS.center.y;
	ctx.translate(newx, newy);
	ctx.rotate(0.64);
	ctx.textAlign = "center";
	ctx.fillStyle = "rgba(0,255,255, 0.02)";
	ctx.textBaseline = 'top';
	ctx.font = "22px Puritan";
	ctx.fillText("PACIFIC", 0, 0);
	ctx.restore();
}

breel.app.plot.plotConnectors = function(keypoint){
		ctx.beginPath();
	for (var i in keypoint.closePoints){
		ctx.strokeStyle = "rgba(255, 255, 255, 0.1)";
		ctx.lineWidth = "1";
		ctx.moveTo(keypoint.x, keypoint.y);
		ctx.lineTo(keypoint.closePoints[i].x, keypoint.closePoints[i].y);
		
	}
ctx.stroke();
	// ctx.beginPath();
	// ctx.fillStyle = "white";
	// ctx.arc(keypoint.x, keypoint.y, 5, 0, Math.TWO_PI);
	// ctx.fill();
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



moveKeypoint = function(keypoint){
	keypoint.x += keypoint.dx;
	keypoint.y += keypoint.dy;
	if (--keypoint.stepsRemaining === 0){
		keypoint.prevTargetPointId = keypoint.targetPointId;
		if (++keypoint.targetPointId >= breel.model.ak.length){
			keypoint.targetPointId = 0;
		}
		getKeypointStepDelta(keypoint, breel.model.ak[keypoint.prevTargetPointId].geo.coordinates, breel.model.ak[keypoint.targetPointId].geo.coordinates);
	}
};

getKeypointStepDelta = function(keypoint, startPoint, endPoint){
	var dx = endPoint.x - startPoint.x;
	var dy = endPoint.y - startPoint.y;
	var dist = Math.sqrt((dx*dx) + (dy*dy));
	var numSteps = Math.ceil(dist / keypoint.stepDeltaTarget);
	keypoint.x = startPoint.x;
	keypoint.y = startPoint.y;
	keypoint.dx = dx / numSteps;
	keypoint.dy = dy / numSteps;
	keypoint.stepsRemaining = numSteps;
};

onSegmentComplete = function(keypoint){

};

getPointsWithinRange = function(keypoint, points){
	var dx, dy, dist;
	var closePoints = [];
	for (var i in points){
		dx = keypoint.x - points[i].geo.coordinates.x;
		dy = keypoint.y - points[i].geo.coordinates.y;
		dist = Math.sqrt((dx*dx) + (dy*dy));
		if (dist < keypoint.pointRange){
			closePoints.push(points[i].geo.coordinates);
		}
	}
	keypoint.closePoints = closePoints;
};

rAF = function(){
	//ctx.clearRect(0,0,breel.model.CANVAS.width, breel.model.CANVAS.height);
	breel.app.plot.plotField();

	ctx.fillStyle = "rgba(0,0,0,0.02)";
	ctx.fillRect(0,0,breel.model.CANVAS.width, breel.model.CANVAS.height);

	for (var i in breel.app.keypoints){
		kp = breel.app.keypoints[i];
		moveKeypoint(kp);
		getPointsWithinRange(kp, breel.model.siteData.results);
		breel.app.plot.plotConnectors(kp);
	}
	breel.app.plot.plotStreetNames();
	
	window.requestAnimFrame(rAF);
};

/****************************************************/

function onKeyUp(event) {
	switch(event.which) {
	case 13://enter
		//intentional fallthrough
	case 32://spacebar
		breel.app.triggerSnapshot();
		event.preventDefault();
	}
	//console.log(event.which);
}

/****************************************************/


function init() {

	$.ajax({
		type: "GET",
		dataType: "jsonp",
		url: "http://search.twitter.com/search.json",
		data: {
			geocode: "\"33.991269,-118.468355,0.75km\"",
			rpp: "100"
		}, 
		success: breel.app.onInitDataReceived
	});

	// https://api.instagram.com/v1/media/search?lat=33.994&lng=-118.46902&distance=750
	
}
init();