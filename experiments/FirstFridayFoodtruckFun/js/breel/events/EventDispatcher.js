////////////////////////////////////////////////////////////////////////////////
//Event System
////////////////////////////////////////////////////////////////////////////////
var breel = breel || {};
breel.events = breel.events || {};

breel.PAGE_CHANGE = "PageChangeEvent";
breel.SUBPAGE_CHANGE = "SubPageChangeEvent";
breel.CONTENT_CHANGE = "ContentChangeEvent";
breel.HASH_CHANGE = "HashChangeEvent";
breel.PAGE_READY = "PageReadyEvent";
breel.PAGE_LOADED = "PageLoadedEvent";
breel.ENTER_FRAME = "EnterFrame";

breel.events.EventDispatcher = breel.events.EventDispatcher || function() {
	var self = this;

	self.currentEventId = 0;
	self.eventQueues = [];

	self.add = function(type, eventHandler, priority, scope, oneTimeUse) {
		var eventListener = new breel.events.EventListener(type, eventHandler, priority, scope,oneTimeUse);
		if (eventListener.type && eventListener.eventHandler) {
			if ( typeof (self.eventQueues[type]) == "undefined") {
				self.eventQueues[type] = [];
			}
			eventListener.id = self.currentEventId;
			self.eventQueues[type].push(eventListener);
			return self.currentEventId++;
		} else {
			throwError("breel.events.EventDispatcher.addEventListener() is missing required parameter 'type'(" + type + ") or 'eventHandler'");
			return null;
		}
	};

	self.addOnce = function(type, eventHandler, priority, scope){
		self.add(type, eventHandler, priority, scope, true);
	};

	self.remove = function(id, destroy) {
		destroy = destroy || false;
		for (var eventQueueName in self.eventQueues) {
			//trace("breel.events.EventDispatcher.removeEventListener() searching " + eventQueueName);
			var eventQueue = self.eventQueues[eventQueueName];
			for (var eventListenerIndex in eventQueue) {
				var eventListener = eventQueue[eventListenerIndex];
				if (eventListener.id === id) {
					//trace("breel.events.EventDispatcher.removeEventListener() found id: " + id);
					eventQueue.splice(eventQueue.indexOf(eventListener), 1);
					if (eventQueue.length === 0) {
						self.eventQueues.splice(self.eventQueues.indexOf(eventQueue), 1);
					}
					if (destroy !== false) {
						eventListener.destroy();
						return true;
					} else {
						return eventListener;
					}
				}
			}
		}

		throwError("breel.events.EventDispatcher.removeEventListener() listener " + id + " not found.");
		return null;
	};

	self.dispatch = function(event) {
		//convert simple string into event object
		if (typeof(event) == "string"){
			event = new breel.events.Event(event);
		}
		//trace("dispatching Event type: " + event.type);
		var eventQueue = self.eventQueues[event.type];
		if ( typeof (eventQueue) != "undefined") {
			//trace(" to " + eventQueue.length + " listeners")
			eventQueue.sort(self.sortOnPriority);
			var i = -1;
			var endi = eventQueue.length;
			while (++i < endi) {
				if (eventQueue[i]) eventQueue[i].eventHandler.call(eventQueue[i].scope, event);
			}
			//clean oneTimeUse listeners
			i = eventQueue.length;
			endi = 0;
			while (--i >= endi) {
				if  (eventQueue[i].oneTimeUse === true) {
					eventQueue.splice(i,1);
				}
			}
		}
	};

	self.sortOnPriority = function(a, b) {
		if (a.priority == b.priority) {
			return 0;
		} else if (a.priority > b.priority) {
			return 1;
		} else {
			return -1;
		}
	};
};

breel.events.Event = function(type, data) {
	var self = this;
	self.type = type ? type : "";
	self.data = data ? data : null;
};

breel.events.EventListener = function(type, eventHandler, priority, scope, oneTimeUse) {
	var self = this;
	self.type = type || null;
	self.priority = priority || 0;
	self.eventHandler = eventHandler || null;
	self.scope = scope || breel.root;
	self.id = null;
	self.oneTimeUse = oneTimeUse || false;

	self.destroy = function() {
		self.type = null;
		self.priority = null;
		self.eventHandler = null;
		self.scope = null;
		self.id = null;
		self.oneTimeUse = null;
	};
};

breel.delegate = function(f, scope){
	return function delegate(){
		f.call(scope);
	};
};