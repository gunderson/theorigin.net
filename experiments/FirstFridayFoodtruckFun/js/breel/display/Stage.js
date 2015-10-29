declare("breel.display");

breel.display.Stage = breel.display.Stage || function(stageElementName, stageWidth, stageHeight){
	
	var self = this;
	$b.extend(self, new breel.display.DisplayObjectContainer());
	this.objectClass = "breel.display.Stage";


	this.stage = this;
	this.stageElement = $(stageElementName);
	this.htmlElement = $(stageElementName)[0];
	this.pagePosition = {
		x: this.stageElement.offset().top,
		y: this.stageElement.offset().left
	};
	this.horizon = -3000;
	this.stageWidth = stageWidth;
	this.stageHeight = stageHeight;
	this.origin = {
		x: stageWidth >> 1,
		y: stageHeight >> 1
	};
	this.mouse = {
		x: 0,
		y: 0
	};
	this.frameRate = 60;
	this.enterFrameBeacon = null;
	this.enterFrameEvent = null;
	self.playing = false;
	
	self.dispatchEnterFrame = function(){
		if (self.playing){
			window.requestAnimFrame(breel.delegate(self.dispatchEnterFrame, self));
			breel.dispatcher.dispatch(self.enterFrameEvent);
		}
		//this.ctx.clearRect(0, 0, this.stageWidth, this.stageHeight);
		//this.positionMap.clearRect(0, 0, this.stageWidth, this.stageHeight);
		//this.yBufferDisplayList();
		//this.setChildIndex(breel.bg, 0);
		//this.redistributeDisplaylistIndicies();
		
	};

	this.draw = function(){
		var displayObject;
		// for (var i in this.displayList){
		// 	if (this.displayList[i].movable){
		// 		this.displayList[i].applyForces();
		// 	}
		// }
		for (var i in self.displayList){
			if (self.displayList[i].visible){
				self.displayList[i].draw();
			}
			// if (this.displayList[i].movable){
			// 	this.displayList[i].move();
			// }
		}
	};

	self.onMouseMove = function(e){
		var prevMouse = self.mouse;
		self.mouse = {
			x: e.pageX - self.pagePosition.x,
			y: e.pageY - self.pagePosition.y
		};
		self.mouse.dx = self.mouse.x - prevMouse.x;
		self.mouse.dy = self.mouse.y - prevMouse.y;

		//var data = self.positionMap.getImageData(self.mouse.x, self.mouse.y, 1, 1).data;
		var data = self.positionMap.getImageData(e.pageX, e.pageY, 1, 1).data;
		var mouseOverColor = data[2];
		for (var i in self.displayList){
			if (this.displayList[i].id == mouseOverColor){
				this.displayList[i].mouseIsOver = true;
			} else {
				this.displayList[i].mouseIsOver = false;
			}
		}
	};
	self.onMouseDown = function(e){
		var mouseOverColor = data[2];
		for (var i in this.displayList){
			if (this.displayList[i].id == mouseOverColor){
				this.displayList[i].mouseIsOver = true;
			} else {
				this.displayList[i].mouseIsOver = false;
			}
		}
	};
	self.onMouseUp = function(e){
		var data = self.positionMap.getImageData(e.pageX, e.pageY, 1, 1).data;
		var mouseOverColor = data[2];
		for (var i in this.displayList){
			if (this.displayList[i].id == mouseOverColor){
				this.displayList[i].mouseIsOver = true;
			} else {
				this.displayList[i].mouseIsOver = false;
			}
		}
	};

	self.play = function(){
		//this.enterFrameBeacon = this.enterFrameBeacon || setInterval(breel.delegate(this.dispatchEnterFrame, this), 1000 / this.frameRate);
		trace("play");
		if (!self.playing){
			self.playing = true;
			window.requestAnimFrame(self.dispatchEnterFrame);
		}
	};
	self.stop = function  () {
		//clearInterval(this.enterFrameBeacon);
		if (self.playing){
			self.playing = false;
			this.enterFrameBeacon = null;
		}
	};

	self.getParent= function(){
		return null;
	};
	self.construct = (function(){
		trace("construct Stage");
		self.configGetters();
		breel.dispatcher = new breel.events.EventDispatcher();
		self.enterFrameEvent = new breel.events.Event(breel.ENTER_FRAME);
		breel.dispatcher.add(breel.ENTER_FRAME, self.draw);
		$(window).resize(function onResize(){
			breel.dispatcher.dispatch(new breel.events.Event(breel.RESIZE));
		});
		$("#stage").mousemove(self.onMouseMove);
		return true;
	})();

};