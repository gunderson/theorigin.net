var breel = breel || {};
breel.components = breel.components || {};

// class that handles loading, processing, displaying and transitioning page templates

// required params:
// name : name used to identify children uniquely
// objectClassName : css class to assign all new children
// parentObject : stage object to hold new pages
breel.components.PageSwitcher = breel.components.PageSwitcher || function(parentObject, name, objectClassName){
	
	this.pageCache = [];
	this.name = name || "main";
	this.parentObject = parentObject;
	this.objectClassName = objectClassName || "breel.componenets.Page";
	this.transitionPageFromSide = 1;
	this.prevDivIndex = null;
	this.currentObject = null;
	this.prevObject = null;
	this.pageLoadAnimationComplete = function() {};
	this.pageReadyListenerId = null;
	this.pageLoadedListenerId = null;
	this.loadCounter = -1;
	this.objectClass = "breel.components.PageSwitcher";

	this.loadPage = function(newPageURL, transitionPageFromSide) {
			// set defaults
			this.transitionPageFromSide = transitionPageFromSide || 1;
			this.prevObject = this.currentObject;
			var objectClass = eval(this.objectClassName);
			this.currentObject = new objectClass();

			//create new div to put loaded contents into
			this.parentObject.addChild(this.currentObject);

			

			if (!this.pageCache[newPageURL]){
				trace(">>> - page not cached");
				breel.showLoader();

				// get html for new page
				$.ajax({
					url: "templates/" + newPageURL + ".html",
					dataType: 'text',
					complete: function(scope, pagename){
						return function(result) {
							scope.pageCache[pagename] = result.responseText;
							scope.onPageloadComplete.call(scope,result.responseText);
						};
					}(this, newPageURL)
				});
			} else {
				trace(">>> - getting page from cache");
				this.onPageloadComplete(this.pageCache[newPageURL]);
			}


		};

	this.onPageloadComplete = function(result) {
		// process template
		var html = result;
		// html = applyLocaleToTemplate(html);
		// html = applyLanguageToTemplate(html);
		// html = applyDyanamicContentToTemplate(html);

		//PAGE_READY fires when the page and its dependant pages are all on stage ready to animate in.
		this.pageReadyListenerId = breel.dispatcher.add(breel.PAGE_READY, this.pageIsReady, 0, this, true);

		//PAGE_LOADED fires when theere are depentant pages left to load
		this.pageLoadedListenerId = breel.dispatcher.add(breel.PAGE_LOADED, this.pageIsLoaded, 0, this, true);

		//render template
		$(this.currentObject.htmlElement).html(html);
	};

	this.setRootObject = function(rootObject){
		trace("setRootObject")
		this.currentObject.x = 0;
		this.currentObject.rootObject = rootObject;
	};

	this.pageIsLoaded = function() {
		//hide the new div and prep for transition in
		this.currentObject.visible = false;
		this.currentObject.x = this.transitionPageFromSide * breel.stage.stageWidth;
	};

	this.pageIsReady = function() {
		breel.hideLoader();
		var tweenTime;

		//transition out prev div
		if (this.prevObject){
			//prepare prev div to transition out
			this.prevObject.x = 0;
			this.prevObject.visible = true;

			tweenTime = breel.tweenTime;

			TweenMax.to(this.prevObject, tweenTime, {
				x: (-this.transitionPageFromSide * breel.stage.stageWidth),
				onUpdate: function() {}
			});
		} else {
			tweenTime = 0;
		}

		//prepare new div to transition in
		this.currentObject.x = this.transitionPageFromSide * breel.stage.stageWidth;
		this.currentObject.visible = true;

		tweenTime = breel.tweenTime;

		//transition in new div
		TweenMax.to(this.currentObject, tweenTime, {
			x: 0,
			onUpdate: function() {},
			onComplete: breel.delegate(this.resetPagePositions, this)
		});
	};

	this.resetPagePositions = function() {
		//$("#loadingCover").css("display", "none")
		if (this.prevObject){
			this.prevObject.destroy();
		}
		this.prevObject = null;
		breel.hideLoader();
		breel.dispatcher.dispatch("PageLoadTransitionComplete");
	};
};