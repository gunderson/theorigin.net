if (typeof(breel) == "undefined") {
	trace("breel object created by Carousel");
	var breel = {};
}

// ------------------------------------------------
if (typeof (breel.Carousel) != "function") {
	breel.Carousel = function() {
		this.originalData;
		this.data = {};
		this.randomData;
		this.recendData;
		var self = this;
		this.carouselId;
		this.carouselVarName = "";
		this.activeIndex = 0;
		var onStageItems = [];
		var queuedItems = [];
		this.page = 0;
		this.prevpage = -1;
		this.totalPages = 1;
		this.carouselLength = 5;
		this.itemAnimationDelay = 0.06;
		this.isAnimating = false;
		this.sort = "randomized";
		this.flag = "";
		this.jsonUrl;
		var carouselData;
		var pages = [];
		this.lastButton = "";
		this.animationTime = 1200;
		this.sortGroup;
		this.clickCounter = 0;
		this.rowLength = 5;
		this.mediaLinkData = [];

		var animationIndex = 0;
		var animationTimer;

		// ------------------------------------------------

		this.init = function(f_carouselId, f_carouselVarName) {
			this.carouselId = f_carouselId;
			this.carouselVarName = f_carouselVarName;

			// setup search
			$(this.carouselId + " input.carouselSearchForm").click(
					this.onSearchFocus);
			$(this.carouselId + " input.carouselSearchForm").blur(
					this.onSearchBlur);

			trace("$(this.carouselId + \" input.carouselSearchForm\") = "
					+ $(this.carouselId + " input.carouselSearchForm").val())
		}

		// ------------------------------------------------

		this.showCarousel = function() {
			this.showNav();
			$(this.carouselId + " .carouselPageHolder").show();
			$(this.carouselId + " .carouselSearchResults").hide();
			this.sortGroup.selectButton(this.lastButton, true);

		}

		// ------------------------------------------------

		this.showSearch = function() {
			this.hideNav();
			$(this.carouselId + " .carouselPageHolder").hide();
			$(this.carouselId + " .carouselSearchResults").show();
			this.sortGroup.deselectAll();
		}

		// ------------------------------------------------

		this.showNav = function() {
			$(this.carouselId + " .carouselPageNumbers").css("opacity", "1");
			$(this.carouselId + " .carouselNext").css("opacity", "1");
			$(this.carouselId + " .carouselPrev").css("opacity", "1");
		}

		// ------------------------------------------------

		this.hideNav = function() {
			$(this.carouselId + " .carouselPageNumbers").css("opacity", "0");
			$(this.carouselId + " .carouselNext").css("opacity", "0");
			$(this.carouselId + " .carouselPrev").css("opacity", "0");
		}

		// ------------------------------------------------

		this.getCarouselPage = function(f_page) {
			this.prevpage = this.page;
			this.page = f_page;
			// this.makeCarouselPage();
			this.showCarouselPage()
		}

		// ------------------------------------------------

		this.nextPage = function() {
			if (this.page < this.totalPages - 1) {
				this.prevpage = this.page;
				this.page++;
				// this.makeCarouselPage();
				this.showCarouselPage();
			}
		}

		// ------------------------------------------------

		this.prevPage = function() {
			if (this.page > 0) {
				this.prevpage = this.page;
				this.page--;
				// this.makeCarouselPage();
				this.showCarouselPage()
			}
		}

		// ------------------------------------------------

		this.setData = function(e) {
			this.data = e;
			this.page = 0;
			pages = [];

			for(var i=0; i < this.mediaLinkData.length; i++){
				this.unlinkifyCarouselItem(this.mediaLinkData[0]);
				this.mediaLinkData.shift();
			}

			queuedItems = [];
			this.totalPages = Math.max(1, Math.ceil(Number(e.results.content.length) / this.carouselLength));
			this.setupCarouselPageNumbers();
			$(this.carouselId + " .carouselPageHolder").html("");
			$(this.carouselId + " .carouselPageHolder").css("left", "0px");

			var i = -1;
			var endi = this.totalPages;
			while (++i < endi) {
				this.page = i;
				this.makeCarouselPage();
			}


			for(var i=0; i < this.mediaLinkData.length; i++){
				this.linkifyCarouselItem(this.mediaLinkData[i]);
			}

			this.makeSearchBox();
			this.page = 0;
			this.prevpage = -1;
			this.showCarouselPage();
		}

		// ------------------------------------------------

		this.makeCarouselPage = function(f_position) {
			if (pages[this.page] == undefined) {
				var i = -1 + this.page * this.carouselLength;
				var endi = ((this.page + 1) * this.carouselLength);
				var mediaInfo;
//				var html = ("<div class='carouselPage' style='left: "
//						+ (this.page * $(this.carouselId + " .carouselItems")
//								.width()) + "px' page='" + this.page + "'>");
				
				var html = ("<div class='carouselPage' style='display:none; position:absolute' page='" + this.page + "'>");
				
				while (++i < endi) {
					if (typeof (this.data.results.content[i]) == 'object') {
						mediaInfo = this.data.results.content[i];
						html += (this.makeCarouselItem(
								this.data.results.content[i].content_id,
								mediaInfo.base + mediaInfo.site_media_id + ((mediaInfo.media_type == "1") ? this.data.results.suffix.image_thumb : this.data.results.suffix.thumb), 
								this.data.results.content[i].title,
								this.data.results.content[i].user_id,
								mediaInfo.base + mediaInfo.site_media_id + ((mediaInfo.media_type == "1") ? this.data.results.suffix.image_large : this.data.results.suffix.mp4), 
								this.carouselVarName,
								i % this.carouselLength,
								i,
								mediaInfo.media_type,
								mediaInfo.media_id,
								mediaInfo.views,
								mediaInfo.title,
								mediaInfo.city,
								mediaInfo.country,
								((mediaInfo.category_name) ?  escape(mediaInfo.category_name) : "video Uncategorized")
								));
					}
				}
				html += ("</div>");
				pages[this.page] = html;
				$(this.carouselId + " .carouselPageHolder").append(html);
				$(this.carouselId + " .carouselPage[page='"+ this.page +"'] .videoLink").hover(
					this.thumbMouseEnter,
					this.thumbMouseLeave
				)
			}
		}

		// ------------------------------------------------

		this.makeSearchBox = function() {
//			var i = -1;
//			var endi = data.results.content.length;
//			var mediaInfo;
//			var html = ""; // = ("<div class='carouselPage searchResults'
//							// style='left: "+ (this.page * $(this.carouselId +
//							// " .carouselItems").width()) +"px' page='"+
//							// this.page +"'>");
//			while (++i < endi) {
//				if (typeof (data.results.content[i]) == 'object') {
//					mediaInfo = data.results.content[i];
//						html += (this.makeCarouselItem(
//								data.results.content[i].content_id,
//								mediaInfo.base + mediaInfo.media_id + data.results.suffix.thumb, 
//								data.results.content[i].title,
//								data.results.content[i].user_id,
//								mediaInfo.media_id, 
//								this.carouselVarName,
//								i % this.carouselLength,
//								mediaInfo.media_type));
//				}
//			}
//			// html += ("</div>");
//			pages[this.page] = html;
//			$(this.carouselId + " .carouselSearchResults").html(html);
//			$(this.carouselId + " .carouselSearchResults").hide();
		}

		// ------------------------------------------------

		this.makeCarouselItem = function(vidId, vidThumbURL, vidName, contestantName, videoUrl, carouselVarName, index, mediaType) {
			//override this function
			var html = '<div class="videoLink" index="'+ index +'" style="left:'+ ((index % this.rowLength) * 130) +'px; top:'+ (Math.floor(index / watchVideosCarousel.rowLength) * 120) +'px" '
				+ 'baseLeft="'+ ((index % watchVideosCarousel.rowLength) * 130) +'">' 
				+ '<p class="contestantName">' + contestantName + '</p>' 
				+ '<div searchterms="' + escape(contestantName + vidId + vidName)+ '"class="videoThumb" bg="url(' + vidThumbURL
				+ ')" onmousedown="clickVideoThumb(\'' + vidId + '\',\'' + videoUrl +'\',\''+ vidThumbURL +'\',\''+ mediaType
				+ '\'); _gaq.push([\'_trackEvent\', \'Click\', \''
				+ carouselVarName + '\', \'Video\']);"></div>'
				+ '<p class="videoName">' + vidName + '</p>' + '</div>';
			return html;
		}

		this.linkifyCarouselItem = function(data){
			//override me
		}

		this.unlinkifyCarouselItem = function(data){
			//override me
		}

		// ------------------------------------------------

		this.showCarouselPage = function() {
			var fromDirection = (this.page < this.prevpage) ? -1 : 1;
			var carouselWidth = fromDirection * ($(this.carouselId + " .carouselItems").width())
			var itemAnimationDelay = this.itemAnimationDelay;
			var rowLength = this.rowLength;
			
			$(this.carouselId + " .carouselPage[page = '" + this.page + "'] .videoThumb").each(function() {
				$(this).css("background-image", $(this).attr("bg"));
			});
			// $(this.carouselId + " .carouselPage[page = '" + this.page + "'] .videoLink").each(function() {
			// 	$(this).css("left", (Number($(this).attr("baseLeft")) + carouselWidth) + "px");
			// });
			
			

			$(this.carouselId + " .carouselPage[page = '" + this.page + "']").css({
				display: "block",
				left: (fromDirection * $(this.carouselId + " .carouselItems").width())
			});
			this.setupCarouselPageNumbers();
			
			/*
			 *  INSTEAD OF MOVING THE WHOLE HOLDER, SHOW/HIDE .carouselPage, AND MOVE INDIVIDUAL .videoThumb s
			 * 
			 */

			 $(this.carouselId + " .carouselPage[page = '" + this.prevpage + "']").animate({
				left: -(fromDirection * $(this.carouselId + " .carouselItems").width())
			}, {
				duration: this.animationTime,
				queue: false,
				easing: "swing"
			});

			 $(this.carouselId + " .carouselPage[page = '" + this.page + "']").animate({
				left: "0px"
			}, {
				duration: this.animationTime,
				queue: false,
				easing: "swing"
			});

			
			// TweenMax.to( $(this.carouselId + " .carouselPageHolder"), this.animationTime / 1000, {css:
			// 		{
			// 			left : -(this.page * $(this.carouselId + " .carouselItems").width())
			// 		}, ease: Expo.easeInOut
			// });
			
			// var numThumbs = $(this.carouselId + " .carouselPage[page = '" + this.page + "'] .videoThumb").length;
			
			// var animationTime = this.animationTime / 1000;
			
			// $(this.carouselId + " .carouselPage[page = '" + this.page + "'] .videoLink").each(function(i){
				
			// 	TweenMax.to( $(this), animationTime, {
			// 		css:{
			// 				left : $(this).attr("baseLeft") + "px"
			// 			}
			// 		, ease: Quint.easeInOut
			// 		//, delay: (animationTime * rowLength / 14) + ((fromDirection == 1) ? (itemAnimationDelay * (i % rowLength)) : (itemAnimationDelay * (rowLength)) - (itemAnimationDelay * (i % rowLength)))
			// 	});
			// });
			// $(this.carouselId + " .carouselPage[page = '" + this.prevpage + "'] .videoLink").each(function(i){
				
			// 	TweenMax.to( $(this), animationTime, {
			// 		css:{
			// 				left : (Number($(this).attr("baseLeft")) - carouselWidth) + "px"
			// 			}
			// 		, ease: Quint.easeInOut
			// 		//, delay: ((fromDirection == 1) ? (itemAnimationDelay * (i % rowLength)) : (itemAnimationDelay * (rowLength)) - (itemAnimationDelay * (i % rowLength)))
			// 	});
			// });
			
			// TweenMax.delayedCall(animationTime + (itemAnimationDelay * numThumbs), function(){
			// 	$(this.carouselId + " .carouselPage[page = '" + this.prevpage + "']").css("display", "none");
			// })
		}

		// ------------------------------------------------

		this.thumbMouseEnter = function(){
			//override me!
		}

		// ------------------------------------------------

		this.thumbMouseLeave = function(){
			//override me!
		}

		// ------------------------------------------------

		this.setupCarouselPageNumbers = function(f_totalPages) {
			if (this.f_totalPages) {
				this.totalPages = f_totalPages;
			}

			var pageNumbersShown = 5;
			var selectedIndex = this.page;
			var startIndex = this.page - 2;
			var endIndex = this.page + 3;

			// trace("setupCarouselPageNumbers", startIndex, endIndex,
			// this.totalPages,this.page)

			if (startIndex < 0) {
				endIndex -= startIndex;
				startIndex = 0;
			}

			if (endIndex > this.totalPages - 3) {
				// startIndex -= endIndex - (totalPages - 3)
			}

			startIndex = Math.min(startIndex, this.totalPages - pageNumbersShown);
			endIndex = Math.min(endIndex, this.totalPages);
			startIndex = Math.max(startIndex, 0);
			// trace("setupCarouselPageNumbers", startIndex, endIndex)

			var i = -1;
			var endi = Math.min(endIndex - startIndex, this.totalPages);
			var html = "";
			var dir;

			// if (this.page != 0) {
			// 	html = '<a class="pageNumber" href="javascript:void(0);" onmousedown="'
			// 			+ this.carouselVarName
			// 			+ '.prevPage(); _gaq.push([\'_trackEvent\', \'Click\', \''
			// 			+ this.carouselVarName + '\', \'Prev\']);">Prev</a>  ';
			// } else {
			// 	html = "<span class='pageNumber'>Prev</span>"
			// }
			while (++i < endi) {
				if (startIndex + i == this.page) {
					html += "<span class='pageNumber selected' selected>" + (startIndex + i + 1) + "</span> ";
				} else {
					if (startIndex + i < this.page) {
						dir = "1";
					} else {
						dir = 1;
					}

					html += '<a class="pageNumber" href="javascript:void(0);" onmousedown="'
							+ this.carouselVarName + '.getCarouselPage(' + (startIndex + i)
							+ ',' + dir + ');_gaq.push([\'_trackEvent\', \'Click\', \''
							+ this.carouselVarName + '\', \'Page_' + (startIndex + i) + '\']);">' + (startIndex + i + 1) + '</a> ';
				}

			}
			// if (this.page < this.totalPages - 1) {
			// 	html += '<a class="pageNumber" href="javascript:void(0);" onmousedown="'
			// 			+ this.carouselVarName
			// 			+ '.nextPage(); _gaq.push([\'_trackEvent\', \'Click\', \''
			// 			+ this.carouselVarName + '\', \'Next\']);">Next</a>';
			// } else {
			// 	html += "<span class='pageNumber'>Next</span>";
			// }
			$(this.carouselId + " .carouselPageNumbers").html(html);
		}

		// //////////////////////////////////////////////////////////////////////////////
		// Search
		// //////////////////////////////////////////////////////////////////////////////

		this.onSearchFocus = function() {
			$(document).bind('keyup', onCarouselSearchKeyUp);
			breel.activeCarousel = self;
			// trace("focus triggered")
		}

		this.onSearchBlur = function() {
			$(document).unbind('keyup', onCarouselSearchKeyUp);
			// trace("blur triggered")

		}

		this.onSearchClick = function() {
			breel.activeCarousel = self;
			onCarouselSearchKeyUp();
		}
	}

	var onCarouselSearchKeyUp = function() {
		trace("Searching " + breel.activeCarousel.carouselId);
		this.scope = breel.activeCarousel;
		var searchTerm = $(this.scope.carouselId + " input.carouselSearchForm").val();
		searchTerm = searchTerm.toLowerCase();

		var searchterms;
		if (searchTerm != "" && searchTerm.length > 0
				&& searchTerm != "Search query...") {

			this.scope.showSearch();
			$(this.scope.carouselId + " .carouselSearchResults .videoThumb")
					.each(
							function() {
								searchterms = $(this).attr("searchterms");

								searchterms = searchterms.toLowerCase();
								if (searchterms.indexOf(searchTerm) == -1) {
									$(this).parent().css("display", "none");
								} else {
									$(this).parent().css("display",
											"inline-block");
									$(this).css("background-image",
											$(this).attr("bg"));
								}
							})
		} else {
			this.scope.showCarousel();
		}
	}
}