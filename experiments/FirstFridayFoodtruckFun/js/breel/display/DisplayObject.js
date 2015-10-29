declare("breel.display");

breel.display.DisplayObject = breel.display.DisplayObject || function (){
	$b.extend(this,{
		objectClass: "breel.display.DisplayObject",
		depth: 0,
		view: null,
		stage: null,
		_parent: null,
		htmlElement: null,
		lockDepthAt: null,
		visible: true,
		mouseEnabled: false,
		mouseIsOver: false,
		buttonMode: false,
		width: 0,
		
		_x: 0,
		_y: 0,
		zBuffer: false,
		id: 10,

		draw: function(){
			if (this.view && this.parent && this.visible){
				this.view.draw();
			}
		},
		getX: function(){
			return this._x;
		},
		getY: function(){
			return this._y;
		},
		setX: function(val){
			this._x = val;
			var s = this.getTransformString();
			if (this.htmlElement){
				this.htmlElement.style.webkitTransform = s;
				this.htmlElement.style.MozTransform = s;
			}
		},
		setY: function(val){
			this._y = val;
			var s = this.getTransformString();
			if (this.htmlElement){
				this.htmlElement.style.webkitTransform = s;
				this.htmlElement.style.MozTransform = s;
			}
		},
		setParent: function(val){
			if (val){
				this._parent = val;
				this.id = ++this._parent.totalChildren;
				if (!this.htmlElement){
					this.htmlElement = breel.createDiv("child"+this.id, this._parent.htmlElement);
				} else {
					this._parent.htmlElement.appendChild(this.htmlElement);
				}
				this.onAdded();
			} else {
				trace("parent set to null");
			}
		},
		getParent: function(){
			return this._parent;
		},
		getTransformString: function(){
			return "translate(" + Math.floor(this.x) + "px," + Math.floor(this.y) + "px)";
		},
		destroy: function(){
			trace("destroy")
			if (this.parent){
				this.parent.removeChild(this);
			}
			$(this.htmlElement).empty().remove();

		},
		onAdded: function(){
			$(this.htmlElement).mouseout(this.onMouseOut);
			$(this.htmlElement).mouseover(this.onMouseOver);
		},
		configGetters: function(){
			this.__defineGetter__("x", this.getX);
			this.__defineSetter__("x", this.setX);
			this.__defineGetter__("y", this.getY);
			this.__defineSetter__("y", this.setY);
			this.__defineGetter__("parent", this.getParent);
			this.__defineSetter__("parent", this.setParent);
		}

	});
}

 