var breel = breel || {};
breel.utils = breel.utils || {};

breel.utils.AssetLoader = breel.utils.AssetLoader || function (settings){
	var self = this;
	self.toLoad = [];
	self.loadIndex = -1;
	self.assets = [];
	$b.extend(this, settings);
	self.add = function(obj){
		self.toLoad.push(obj);
	};
	self.load = function(i){
		if (self.toLoad[i]){
			switch (self.toLoad[i].type){
				case "image":
					loadImage(self.toLoad[i]);
					break;
				case "sound":
					break;
			}
		}
	};
	self.get = function(name){
		return self.assets[name];
	};
	function loadImage(obj){
		var img = new Image();
		img.onload = function(e){
			obj.object = e.target;
			self.onProgress(obj);
		};
		img.src = obj.src;
	}

	self.start = function(){
		self.load(++self.loadIndex);
	};
	self.onProgress = function(loadedAsset){
		breel.dispatcher.dispatch(
			new breel.events.Event(
				breel.utils.AssetLoader.PROGRESS,
				{
					asset: loadedAsset,
					loaded: self.loadIndex + 1,
					total: self.toLoad.length
				}
			)
		);
		self.assets[loadedAsset.name] = loadedAsset;
		if (self.toLoad.length > self.loadIndex + 1) {
			self.load(++self.loadIndex);
		} else {
			self.onComplete();
		}
	};
	self.onComplete = function(){
		breel.dispatcher.dispatch(new breel.events.Event(breel.utils.AssetLoader.COMPLETE));
	};
	self.reset = function(){
		self.toLoad = [];
		self.loadIndex = -1;
	};
};
breel.utils.AssetLoader.COMPLETE = "loadQueueCompleteEvent";
breel.utils.AssetLoader.PROGRESS = "loadProgressEvent";

breel.utils.Asset = {
	name: "",
	type: "",
	src: "",
	object: null
};