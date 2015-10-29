var breel = breel || {};
breel.physics = breel.physics || {};
breel.physics.Universe = breel.physics.Universe || function(settings){
	settings = settings || {};
	this.TWO_PI = Math.PI * 2;
	this.PI_OVER_2 = Math.PI / 2;
	this.timeScale = settings.timeScale || 1;
	this.friction = settings.friction || 0;
	this.particleId = 0;
	this.forces = [];
	this.particles = [];
	this.addForce = function(force){
		this.forces.push(force);
	};
	this.removeForce = function(force){
		var i = this.forces.indexOf(force);
		if (i > -1)
			this.forces.splice(i,1);
	};
	this.addParticle = function(particle){
		this.particles[++this.particleId] = (particle);
		particle.universe = this;
	};
	this.removeParticle = function(particle){
		var i = this.particles.indexOf(particle);
		if (i > -1){
			this.particles.splice(i,1);
			particle.universe = null;
			return particle;
		}
		return null;
	};
	this.drawForces = function(){
		var starts = [];
		var ends = [];
		var i = 0;
		var j = 0;
		var p0, p1;
		for (i in this.particles){
			p0 = breel.stage.getTranslatedPoint(this.particles[i].x,this.particles[i].y);
			for (j in this.particles[i].forces){
				p1 = breel.stage.getTranslatedPoint(this.particles[i].forces[j].x,this.particles[i].forces[j].y);
				starts.push(p0);
				ends.push(p1);
			}
		}
		var ctx = breel.stage.ctx;
		ctx.shadowColor="none";
		ctx.beginPath();
		ctx.strokeStyle = "rgba(128,128,128,0.2)";
		for (i in starts){
			ctx.moveTo(starts[i].x, starts[i].y);
			ctx.lineTo(ends[i].x, ends[i].y);
		}
		ctx.stroke();
	};

	this.getKineticEffectOn = function(particle){
		var fx = 0;
		var fy = 0;
		var forceEffect = {};
		var i = 0;
		if (particle.movable){
			for (i in particle.forces){
				forceEffect = this.getForceEffectBetween(particle, particle.forces[i]);
				// apply kinetic force
				if (forceEffect.dist <= particle.force.radius + particle.forces[i].radius){
					var angleOfIncidence = forceEffect.direction;
					var tanOfIncidence = angleOfIncidence + this.PI_OVER_2;
					var energyAngle = particle.force.direction;
					var transferRate = 1 - (Math.abs(tanOfIncidence - energyAngle) / this.PI_OVER_2); // may actually be a sin function instead of linear
					
					//add transferRate * p0.speed at angleOfIncidence to p1

					//add transferRate * -p0.speed at angleOfIncidence to p0
				}
				
			}
		}
		var total = {
			fx: fx,
			fy: fy,
			speed: Math.sqrt((fx*fx)+(fy*fy)),
			direction: Math.atan2(fy, fx),
			calculatedForces: calculatedForces
		};
		return total;
	};

	this.getKineticEffectBetween = function(p, f){
		if (f.calculatedForces[p.id]){
			var force = $b.extend({}, f.calculatedForces[p.id]);
			force.speed *= -1;
			force.fx *= -1;
			force.fy *= -1;
			return force;
		} else {
			var polarity = -1 *(p.force.polarity * f.polarity);
			var dx = f.x - p.x;
			var dy = f.y - p.y;
			var dist = Math.max(1, Math.sqrt((dx*dx)+(dy*dy)));
			var direction = Math.atan2(dy, dx);
			var power = (f.power * polarity) / Math.pow(dist, f.falloffRate);
			var fx = (Math.abs(power) > 0.1) ? Math.cos(direction) * power * this.timeScale : 0;
			var fy = (Math.abs(power) > 0.1) ? Math.sin(direction) * power * this.timeScale : 0;
			return {fx:fx, fy:fy, dist:dist, direction: direction};
		}
	};

	this.getMagneticEffectOn = function(particle){
		var fx = 0;
		var fy = 0;
		var forceEffect = {};
		var i = 0;
		var calculatedForces = [];
		if (particle.movable){
			for (i in particle.forces){
				forceEffect = this.getForceEffectBetween(particle, particle.forces[i]);
				calculatedForces[particle.forces[i].id] = forceEffect;
				fx += forceEffect.fx;
				fy += forceEffect.fy;
			}
			for (i in this.particles){
				if (this.particles[i] != particle){
					forceEffect = this.getForceEffectBetween(particle, this.particles[i].force);
					calculatedForces[this.particles[i].id] = forceEffect;
					fx += forceEffect.fx;
					fy += forceEffect.fy;
				}
			}
		}
			

		var total = {
			fx: fx,
			fy: fy,
			speed: Math.sqrt((fx*fx)+(fy*fy)),
			direction: Math.atan2(fy, fx),
			calculatedForces: calculatedForces
		};
		return total;
	};

	this.getForceEffectBetween = function(p, f){
		if (f.calculatedForces[p.id]){
			var force = $b.extend({}, f.calculatedForces[p.id]);
			force.speed *= -1;
			force.fx *= -1;
			force.fy *= -1;
			return force;
		} else {
			var polarity = -1 *(p.force.polarity * f.polarity);
			var dx = f.x - p.x;
			var dy = f.y - p.y;
			var dist = Math.max(1, Math.sqrt((dx*dx)+(dy*dy)));
			var direction = Math.atan2(dy, dx);
			var power = (f.power * polarity) / Math.pow(dist, f.falloffRate);
			var fx = (Math.abs(power) > 0.1) ? Math.cos(direction) * power * this.timeScale : 0;
			var fy = (Math.abs(power) > 0.1) ? Math.sin(direction) * power * this.timeScale : 0;
			return {fx:fx, fy:fy, dist:dist, direction: direction};
		}
	};

	this.getAngleOfIncidence = function(f0, f1){

	}

	this.init = function(){
		breel.dispatcher.add(breel.ENTER_FRAME, this.drawForces, 0, this);
	};
};