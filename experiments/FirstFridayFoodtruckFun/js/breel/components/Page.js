var breel = breel || {};
breel.componenets = breel.componenets || {};

breel.componenets.Page = breel.componenets.Page || function(params) {

	$b.extend(this, new breel.display.DisplayObjectContainer());
	$.extend(this, params);

	var self = this;
	self.objectClass = "breel.componenets.Page";
	this.rootObject = null;
	self.setX = function(val){
		if (this.rootObject){
			this.rootObject.x = val;
		} else {
			this.superClass.setX.call(self, val);
		}
	};
	self.setY = function(val){
		if (this.rootObject){
			this.rootObject.y = val;
		} else {
			this.superClass.setY.call(self, val);
		}
	};
	self.getX = function(){
		if (this.rootObject){
			return this.rootObject.x;
		} else {
			this.superClass.getX.call(self);
		}
	};
	self.getY = function(){
		if (this.rootObject){
			return this.rootObject.y;
		} else {
			this.superClass.getY.call(self);
		}
	};

	self.configGetters();
};

