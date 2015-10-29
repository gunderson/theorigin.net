if (typeof(breel) == "undefined") {
	trace("breel object created by PageSwitcher");
	var breel = {};
}

// ------------------------------------------------
if (typeof(breel.PageSwitcher) != "function") {
	breel.PageSwitcher = function(baseName, baseClass, parentDiv){
		this.pageCache = [];
		this.baseName = baseName || "main";
		this.parentDiv = parentDiv || "#mainHolder";
		this.baseClass = baseClass || "mainContent";
		this.transitionPageFromSide = 1;
		this.prevDivIndex;
		this.newDiv;
		this.prevDiv;
		this.parentDiv;
		this.pageLoadAnimationComplete = function() {};
		this.pageReadyListenerId;
		this.pageLoadedListenerId;
		this.parentDiv;
		this.clickCounter = -1;

		this.loadNewPage = function(newPageURL, transitionPageFromSide) {
				// set defaults
				this.transitionPageFromSide = transitionPageFromSide || 1;
				this.prevDiv = "#" + baseName + (this.clickCounter);
				this.newDiv = "#" + baseName + (++this.clickCounter);

				//create new div to put loaded contents into
				$(parentDiv).append('<div class="' + baseClass + '" id="' + (this.newDiv.substr(1)) + '"></div>');
				//hide the new div
				$(this.newDiv).css({
					left: (this.transitionPageFromSide * stageWidth) + "px",
					display: "block"
				})

				if (!this.pageCache[newPageURL]){
					trace(">>> - page not cached")
					breel.showLoader();

					// get html for new page
					$.ajax({
						url: "modules/" + newPageURL + ".php",
						dataType: 'text',
						complete: function(scope, pagename){
							return function(result) {
								scope.pageCache[pagename] = result.responseText;
								scope.onPageloadComplete.call(scope,result.responseText);
							}
						}(this, newPageURL)
					});
				} else {
					trace(">>> - getting page from cache")
					this.onPageloadComplete(this.pageCache[newPageURL]);
				}


			}

		this.onPageloadComplete = function(result) {
			// put new page html into #mainNew
			var html = applyLocaleToString(result);
			html = applyLanguageToString(html)
			html = applyDyanamicContentToString(html);

			//PAGE_READY fires when the page and its dependant pages are all on stage ready to animate in.
			this.pageReadyListenerId = breel.dispatcher.addEventListener(breel.PAGE_READY, this.pageIsReady, 0, this, true);

			//PAGE_LOADED fires when theere are depentant pages left to load
			this.pageLoadedListenerId = breel.dispatcher.addEventListener(breel.PAGE_LOADED, this.pageIsLoaded, 0, this, true);
			$(this.newDiv).html(html);
		}

		this.pageIsLoaded = function() {
			this.pageLoadAnimationComplete();
		}

		this.pageIsReady = function() {
			breel.hideLoader();

			//prepare prev div to transition out
			$(this.prevDiv).css({
				left: "0px",
				//top : "40px",
				//opacity: 1,
				display: "block"
			})
			//transition out prev div
			if ($(this.prevDiv).length > 0) {
				var tweenTime = breel.tweenTime * 2;

				TweenMax.to($(this.prevDiv), tweenTime, {
					css: {
						left: (-this.transitionPageFromSide * stageWidth) + "px"
					},
					ease: breel.ease,
					onUpdate: function() {}
				})
				// $(this.prevDiv).animate({
				// 	left: (-this.transitionPageFromSide * stageWidth) + "px"
				// },{
				// 	duration: tweenTime *1000,
				// 	easing: "easeInOutQuint"
				// })


			} else {
				var tweenTime = 0;
			}



			//prepare new div to transition in
			$(this.newDiv).css({
				left: (this.transitionPageFromSide * stageWidth) + "px",
				//top : "40px",
				//opacity: 0,
				display: "block"
			})
			//transition in new div
			TweenMax.to($(this.newDiv), tweenTime, {
				css: {
					left: "0px",
					//top : "40px"
					//opacity: 1
				},
				//delay: breel.tweenTime,
				ease: breel.ease,
				onComplete: function(scope){
						return function() {
							scope.resetPagePositions.call(scope);
						}
					}(this),
			})

			// $(this.newDiv).animate({
			// 	left: "0px"
			// },{
			// 	duration: tweenTime * 1000,
			// 	complete: function(scope){
			// 			return function() {
			// 				scope.resetPagePositions.call(scope);
			// 			}
			// 		}(this),
			// 	easing: "easeInOutQuint"
			// })


		}

		this.resetPagePositions = function() {
			//$("#loadingCover").css("display", "none")
			$(this.prevDiv).remove();
			breel.hideLoader()
			this.pageLoadAnimationComplete();
		}
	}
}