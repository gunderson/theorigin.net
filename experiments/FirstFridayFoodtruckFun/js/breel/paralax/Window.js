var breel = breel || {};
breel.paralax = breel.paralax || {};

breel.paralax.Window = breel.paralax.Window || function(params) {

	var self = this;
	$b.extend(self, new breel.display.DisplayObjectContainer());
	$.extend(self, params);

	self.objectClass= "breel.paralax.Window";
	self.destroy = function(){

	};
	self.setX = function(val){
		this.superClass.setX.call(this, val);
		for (var i in self.displayList){
			self.displayList[i].x = self.x * 0.2 * i + (breel.paralaxOffset * 0.2 * i);
		}
	};
	self.setY = function(val){
		this.superClass.setY.call(this, val);
	};

	self.onMouseOver = function(){
		self.enterFrameListenerId = breel.dispatcher.add(breel.ENTER_FRAME, self.enterFrame);
	};
	self.onMouseOut = function(){
		breel.dispatcher.remove(self.enterFrameListenerId);
	};
	self.enterFrame = function(){
		self.displayList[0].y += 1;
		self.displayList[0].x += 1;
	}
	self.enterFrameListenerId = null;
	self.configGetters.call(self);

	self.onAdded = function(){
		$(self.htmlElement).mouseout(self.onMouseOut);
		$(self.htmlElement).mouseover(self.onMouseOver);
	};
};

