if (typeof(breel) == "undefined") {
	trace("breel object created by FunctionalRadioGroup");
	var breel = {};
}


breel.FunctionalRadioGroup = function(){
	var self = this;
	self.clickCounter = 0;
	self.radioButtons = new Array();
	self.activateHandlers = new Array();
	self.deactivateHandlers = new Array();
	self.outHandlers = new Array();
	self.activeId = "";
	
	self.addButton = function (f_buttonId, activateHandler, deactivateHandler){
	    self.radioButtons.push(f_buttonId);
	    self.activateHandlers[f_buttonId] = activateHandler;
	    self.deactivateHandlers[f_buttonId] = deactivateHandler;
	}
	
	self.selectButton = function (f_button, externalTrigger){
		if (!externalTrigger){
			externalTrigger = false;
		}
	    self.deselectAll();
	    
	    //deactivate
	    if (self.deactivateHandlers[self.activeId] && typeof(self.deactivateHandlers[self.activeId]) == "function"){
			self.deactivateHandlers[self.activeId](externalTrigger);
		}
	    //activate
	    self.activeId = f_button;
	    $(f_button).removeClass("inactv");
		$(f_button).addClass("actv");
		if (self.activateHandlers[f_button] && typeof(self.activateHandlers[f_button]) == "function"){
			self.activateHandlers[f_button](externalTrigger);
		}
	}
		
	self.deselectAll = function(){
		var i = -1;
		var endi = self.radioButtons.length;
		while(++i < endi){
			self.deselect(self.radioButtons[i], i, self.radioButtons);
		}
	}
	
	self.deselect = function(f_button, f_i, f_array){
		$(f_button).removeClass("actv");
		$(f_button).addClass("inactv");
	}
}