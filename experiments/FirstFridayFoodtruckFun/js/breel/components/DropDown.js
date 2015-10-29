if (typeof (breel) == "undefined") {
	trace("breel object created by DropDown");
	var breel = {};
}

breel.DropDown = function(rootNode) {
	var self = this;
	self.rootNode = rootNode;
	self.$holder = $(self.rootNode + " #optionHolder");
	self.$mask = $(self.rootNode + " #optionMask");
	self.OPEN = "OPEN";
	self.CLOSED = "CLOSED";
	self.state = self.CLOSED;
	self.activeClass = "actv";
	self.inactiveClass = "inactv";
	self.clickCounter = 0;
	self.radioButtons = new Array();
	self.activateHandlers = new Array();
	self.deactivateHandlers = new Array();
	self.outHandlers = new Array();
	self.activeId = "";
	self.activeIndex = 0;
	self.animationTime = 0.025;
	self.scrollButtonTop = null;
	self.scrollButtonBottom = null;
	self.maxHeight = 400;
	self.maxWidth = 0;
	self.listHeight = 0;
	self.scrollEnabled = false;

	self.frameInterval = 50;
	self.frameDelta = 9;
	self.scrollDownTimer = null;
	self.scrollUpTimer = null;

	$(rootNode).mouseleave(function() {self.collapse()});
	$(rootNode).mouseenter(function() {self.expand()});

	self.addButton = function(f_buttonId, activateHandler, deactivateHandler) {
		self.radioButtons.push(f_buttonId);
		self.activateHandlers[f_buttonId] = activateHandler;
		self.deactivateHandlers[f_buttonId] = deactivateHandler;
		$(f_buttonId).attr('closedposition', "{top:-" + ((self.radioButtons.length - 1) * $(f_buttonId).height()) + ",left:0}");
		$(f_buttonId).bind("click", function() {self.selectButton(f_buttonId)});
		self.gotoClosedPosition(f_buttonId, 0);
		
		self.listHeight = (self.radioButtons.length - 1) * $(f_buttonId).height();
		if (self.radioButtons.length == 1) {
				self.activeId = f_buttonId;
		} else {
			$(f_buttonId).siblings().removeClass("last");
		}
		$(f_buttonId).addClass("last");
		self.listHeight += $(f_buttonId).outerHeight();

		self.maxWidth = Math.max(self.maxWidth, $(f_buttonId).outerWidth());
		self.$mask.css("width", self.maxWidth + "px");
	}

	self.selectButton = function(f_button, externalTrigger) {
		switch (self.state) {
			case self.OPEN :
				if (!externalTrigger) {
					externalTrigger = false;
				}
				self.deselectAll();

				// deactivate currently active button
				if (self.deactivateHandlers[self.activeId] && typeof (self.deactivateHandlers[self.activeId]) == "function") {
					self.deactivateHandlers[self.activeId](externalTrigger);
				}
				// activate
				self.activeId = f_button;
				$(f_button).removeClass(self.inactiveClass);
				$(f_button).addClass(self.activeClass);
				if (self.activateHandlers[f_button] && typeof (self.activateHandlers[f_button]) == "function") {
					self.activateHandlers[f_button](externalTrigger);
				}
				self.collapse();
				self.activeIndex = this.radioButtons.indexOf(f_button);
				break;
			case self.CLOSED :
				self.expand();
				break;
		}
	}

	self.deselectAll = function() {
		var i = -1;
		var endi = self.radioButtons.length;
		while (++i < endi) {
			self.deselect(self.radioButtons[i], i, self.radioButtons);
		}
	}

	self.deselect = function(f_button, f_i, f_array) {
		$(f_button).removeClass(self.activeClass);
		$(f_button).addClass(self.inactiveClass);
	}

	self.expand = function() {
		var i = -1;
		var endi = self.radioButtons.length;
		while (++i < endi) {
			self.gotoOpenPosition(self.radioButtons[i], i);
		}
		self.state = self.OPEN;

		var newHeight = Math.min(self.maxHeight, self.listHeight);
		self.scrollEnabled = (self.listHeight > self.maxHeight);
		self.$mask.css({
			height: (newHeight + 60)+"px",
			top: "-30px",
			width: self.maxWidth,
			overflow: "hidden"
		})
		self.$holder.css({
			top: "30px"
		});

		if (self.scrollEnabled){
			self.maxOffset = self.maxHeight - self.listHeight; //intentionally negative
			//show down arrow
			//$(self.rootNode + " #downScroller").show();
			//set mouseenter listener
			$(self.rootNode + " #downScroller").mouseenter(self.scrollDownStart);


			//show up arrow
			$(self.rootNode + " #upScroller").show();
			//set mouseenter listener
			$(self.rootNode + " #upScroller").mouseenter(self.scrollUpStart);
		}
	}
	
	self.gotoOpenPosition = function(f_button, i) {
//		var positionStr = $(self.activeId).attr('closedposition')
//		var positionObj = eval("(" + positionStr + ")")
		positionObj = {
			//top : positionObj.top + "px",
			top : "0px",
			left : "0px"
		};
		
		var delayModifier = Math.abs(i - self.activeIndex)
		//TweenMax.to($(f_button), self.animationTime * (delayModifier), {
		TweenMax.to($(f_button), self.animationTime * (i), {
			css:positionObj,
			ease: Linear.easeNone
		})
		
		
//		$(f_button).animate(positionObj, {
//			duration : self.animationTime * 1000  * (i + 1),
//			easing: "linear"
//		})
	}

	self.collapse = function() {
		$(self.rootNode + " #downScroller").unbind("mouseenter",self.scrollDownStart).unbind("mouseleave",self.scrollDownEnd);
		$(self.rootNode + " #upScroller").unbind("mouseenter",self.scrollUpStart);
		self.scrollUpEnd();
		self.scrollDownEnd();
		$(self.rootNode + " #downScroller").hide();
		$(self.rootNode + " #upScroller").hide();
		self.$mask.css({
			height: $(self.radioButtons[0]).height()+"px",
			top: "0px",
			overflow: "visible"
		})

		self.$holder.css({
			top: "0px",
			overflow: "visible",
			height: $(self.radioButtons[0]).height()+"px"
		});
		self.$holder.css("top", "0px");
		var i = -1;
		var endi = self.radioButtons.length;
		while (++i < endi) {
			self.gotoClosedPosition(self.radioButtons[i], i);
		}
		self.state = self.CLOSED;
	}

	self.gotoClosedPosition = function(f_button, i) {
		//trace("breel.DropDown :: gotoClosedPosition(" + f_button + ", " + i + ") :: $(f_buttonId).height() = " + $(f_button).height())
		var positionStr = $(f_button).attr('closedposition')
		var positionObj = eval("(" + positionStr + ")")
//		$(f_button).animate(positionObj, {
//			duration: self.animationTime * 1000,
//			easing: "linear"
//		})
		positionObj.top = positionObj.top + "px";
		positionObj.left = positionObj.left + "px";
		var delayModifier = Math.abs(i - self.activeIndex)
		//TweenMax.to($(f_button), self.animationTime * (delayModifier), {
		TweenMax.to($(f_button), self.animationTime * 0 * (i), {
			css:positionObj,
			ease: Linear.easeNone
		})
	}
	
	self.destroy = function(){
		self.reset();
	}

	self.getHeight = function(){
		return (self.radioButtons.length - 1) * $(self.radioButtons[0]).height();
	}

	self.reset = function(){
		self.maxWidth = 0;
		$(rootNode).mouseleave(false);
		$(rootNode).mouseenter(false);
		self.radioButtons = new Array();
		self.activateHandlers = new Array();
		self.deactivateHandlers = new Array();
	}

	self.stepUp = function(){
		var currentOffset = self.$holder.position().top;
		if (currentOffset - self.frameDelta < self.maxOffset+30){
			self.$holder.css("top", (self.maxOffset+30) + "px");
			$(self.rootNode + " #upScroller").hide();
			self.scrollUpEnd();
		} else {
			self.$holder.css("top", (currentOffset - self.frameDelta) + "px");
		}
		trace("stepUp " + currentOffset ) ;
	}

	self.stepDown = function(){
		var currentOffset = self.$holder.position().top;
		if (currentOffset + self.frameDelta > 30){
			self.$holder.css("top", "30px");
			$(self.rootNode + " #downScroller").hide();
			self.scrollDownEnd();
		} else {
			self.$holder.css("top", (currentOffset + self.frameDelta) + "px");
		}
		trace("stepDown " + currentOffset ) ;
	}

	self.scrollUpStart = function(){
		$(self.rootNode + "  #downScroller").show();
		self.scrollUpTimer = setInterval( self.stepUp, self.frameInterval);
		//set mouseleave listener
		$(self.rootNode).mouseleave(self.scrollUpEnd);
		$(self.rootNode + "  #upScroller").mouseleave(self.scrollUpEnd);
	}

	self.scrollDownStart = function(){
		$(self.rootNode + "  #upScroller").show();
		self.scrollDownTimer = setInterval(self.stepDown, self.frameInterval);
		$(self.rootNode).mouseleave(self.scrollDownEnd)
		$(self.rootNode + "  #downScroller").mouseleave(self.scrollDownEnd);
	}

	self.scrollUpEnd = function(){
		$(self.rootNode).unbind('mouseleave', self.scrollUpEnd);
		$(self.rootNode + "  #downScroller").unbind("mouseleave",self.scrollUpEnd);
		if (self.scrollUpTimer){
			clearInterval(self.scrollUpTimer)
			self.scrollUpTimer = null;
		}
	}

	self.scrollDownEnd = function(){
		$(self.rootNode).unbind('mouseleave', self.scrollDownEnd);
		$(self.rootNode + "  #upScroller").unbind("mouseleave",self.scrollDownEnd);
		if (self.scrollDownTimer){
			clearInterval(self.scrollDownTimer);
			self.scrollDownTimer = null;
		}	
	}
}