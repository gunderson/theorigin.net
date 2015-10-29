var breel = breel || {};
breel.physics = breel.physics || {};

breel.physics.SimpleParticle = breel.physics.SimpleParticle || function(params) {

	$b.extend(this, breel.display.DisplayObject);
	var self = this;
	this._dx = 0;
	this._dy = 0;
	this.force = params.force || new breel.physics.Force();
	this.speed = params.speed || 0;
	this.direction = params.direction || 0;
	this.bounds =  null;
	this.mass = 1; //balance external forces with inertia.
	this.charge = 0;
	this.movable = true;
	this.universe = null;
	this.id = null;
	this.primaryColor = "#F7B16D";
	this.activeColor = "#B86F45";

	$.extend(this, params);

	this.move = function(){
		if (this.speed !== 0 && this.movable){
			if (!this.bounds){
				this.x += this._dx;
				this.y += this._dy;
			} else {
				//check bounds
				var b = this.bounds;
				if (this.x < 0){
					//out of bounds left edge
				} else if (this.x + this.width > b.x + b.width){
					//out of bounds right edge
				}
				if (this.y < 0){
					//out of bounds top edge
				} else if (this.y + this.height > b.y + b.height){
					//out of bounds bottom edge
				}
			}
		}
		this.force.calculatedForces = [];
	};
	this.findIntersection = function(){

	};

	this.applyForces = function(){
		var forces = null;
		var friction = 0;
		var fx = 0;
		var fy = 0;
		// get universe forces
		if (this.universe){
			friction = this.universe.friction;
			//magnetic force
			forces = this.universe.getMagneticEffectOn(this);
			this.force.calculatedForces = forces.calculatedForces;
			fx = forces.fx;
			fy = forces.fy;
			//kinetic force
			// forces = this.universe.getKineticEffectOn(this);
			// fx += forces.fx;
			// fy += forces.fy;

		}
		//add inertia vector
		this._dx = fx + Math.cos(this.direction) * this.speed * (1 - friction * this.universe.timeScale);
		this._dy = fy + Math.sin(this.direction) * this.speed * (1 - friction * this.universe.timeScale);
		//new dynamics
		this.speed = this.getSpeed(this._dx, this._dy);
		this.direction = Math.atan2(this._dy, this._dx);

	};
	this.getSpeed = function(x,y){
		return Math.sqrt((x*x) + (y*y));
	};
	this.drawForceVector = function(){
		var ctx = this.stage.ctx;
		var radius = 5;
		var point0 = this.stage.getTranslatedPoint(this.x, this.y);
		ctx.beginPath();
		ctx.strokeStyle = "rgba(200,200,200,0.5";
		ctx.moveTo(point0.x, point0.y);
		ctx.lineTo(point0.x + this._dx, point0.y + this._dy);
		ctx.stroke();
	};
	this.destroy = function(){

	};
	this.setX = function(val){
		this.superClass.setX.call(this, val);
		this.force.x = val;
	};
	this.setY = function(val){
		this.superClass.setY.call(this, val);
		this.force.y = val;
	};

	this.configGetters();
};

