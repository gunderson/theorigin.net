var breel = breel || {};
breel.physics = breel.physics || {};
breel.physics.Force = breel.physics.Force || function(params){
	$b.extend(this, breel.display.DisplayObject);
	
	var self = this;
	self.movable = false;
	self._x = 0;
	self._y = 0;
	self.power = 10;
	self.polarity = 0;
	self.radius = 10;
	self.falloffRate = 1;
	$b.extend(this, params);
	this.calculatedForces = [];
	this.view = null;
	this.visible = true;
	this.draw = function(){
		if (this.view && this.stage && this.visible){
			this.view.draw();
		}
	};
	this.move = function(){

	};
	this.applyForces = function(){

	};

	this.configGetters();
};

breel.physics.Force.POSITIVE = 1;
breel.physics.Force.NEGATIVE = -1;
breel.physics.Force.NEUTRAL = 0;