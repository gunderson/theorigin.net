declare("breel.app.display");

breel.app.display.MediaPoint = breel.app.display.MediaPoint || function(params) {
	var geo = null;
	$b.extend(this, new breel.display.DisplayObjectContainer());
	$.extend(this, params);

	var self = this;
	self.objectClass = "breel.app.MediaPoint";
	self.isNew = true;
	self.animateIn = function(){

	};
	self.animateOut = function(){

	};
	self.setX = function(val){
		self.superClass.setX.call(self, val);
	};
	self.setY = function(val){
		this.superClass.setY.call(this, val);
	};

	self.configGetters();
};