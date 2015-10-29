declare("breel.display");

breel.display.Div = breel.display.Div || function(params) {

	$b.extend(this, new breel.display.DisplayObjectContainer());
	$.extend(this, params);

	var self = this;
	self.objectClass = "breel.display.Div";
	self.setX = function(val){
		self.superClass.setX.call(self, val);
	};
	self.setY = function(val){
		this.superClass.setY.call(this, val);
	};

	self.configGetters();
};

