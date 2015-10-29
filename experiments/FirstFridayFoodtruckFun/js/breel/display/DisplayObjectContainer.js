/*	Extends breel.display.DisplayObject
*
*/

declare("breel.display");

breel.display.DisplayObjectContainer = breel.display.DisplayObjectContainer || function() {
	$b.extend(this, new breel.display.DisplayObject());
	$.extend(this, {
		
		objectClass: "breel.display.DisplayObjectContainer", 
		origin: {
			x: 0,
			y: 0,
			scale: 1
		},
		totalChildren: 0,
		displayList: [],
		horizon: 0,
		getTranslatedPoint: function(x, y){
			// var _x = x * (1 + ((y / this.stageHeight) - 0.1) * 0.9);
			// var _y = y * (1 + ((y / this.stageHeight) - 0.1) * 0.9);
			// var _scale = 0.5 + Math.min(Math.max(1, ((this.origin.y + y) / this.stageHeight) * 4), 3);
			return {
				x:x,
				y:y,
				scale: 1
				// x:this.origin.x + _x,
				// y:this.origin.y + _y,
				// scale: _scale
			};
		},

		localToGlobal: function(x, y){

		},
		globalToLocal: function(x, y){

		},
		addChild: function(child){
			var childIndex = this.displayList.indexOf(child);
			if (child.parent){
				child.parent.removeChild(child);
			}
			this.displayList.push(child);
			//this.redistributeDisplaylistIndicies();
			child.parent = this;
			child.stage = this.stage;
			return (this.displayList.length);
		},
		setChildIndex: function(child, index){
			var currentIndex = this.displayList.indexOf(child);
			if (currentIndex > -1){
				this.displayList.splice(currentIndex, 1);
				this.displayList.splice(index, 0, child);
			} else {
				return;
			}
		},
		redistributeDisplaylistIndicies: function(){
			var i = -1;
			var endi = this.displayList.length;
			while(++i < endi){
				//this.displayList[i].id = i + 1;
			}
		},
		removeChild: function(child){
			var childIndex = this.displayList.indexOf(child);
			if (childIndex > -1){
				this.displayList.splice(childIndex, 1);
				//this.redistributeDisplaylistIndicies();
			} else {
				throwError(child + " is not a child of " + this);
			}
			child.parent = null;
			child.stage = null;
		},
		yBufferDisplayList: function(){
			this.displayList.sort(this.ybufferSort);
		},
		ybufferSort: function (a,b){
			if (a.y > b.y) {
				return 1;
			} else if (a.y < b.y){
				return -1;
			} else if (a.y == b.y){
				return 0;
			}
		},
		draw: function(){
			if (this.view && this.parent && this.visible){
				this.view.draw();
			}
			if (this.parent && this.parent.stage && this.visible){
				for (var i in this.displayList){
					this.displayList[i].draw();
				}
			}
		}
	});
}