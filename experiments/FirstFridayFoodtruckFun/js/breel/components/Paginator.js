if (typeof(breel) == "undefined") {
	trace("breel object created by Paginator");
	var breel = {};
} 

/*--------------------------------------------------*/	

if (typeof(breel.Paginator) != "function") {
	breel.Paginator = function(numPages, objectName, containerId, gotoPageFunction) {
		var self = this;
		this.numPages = numPages
		this.gotoPageFunction = gotoPageFunction;
		this.activeIndex = 0;
		this.transitionFrom = 1;
		this.containerId = containerId;
		this.objectName = objectName;
		this.defaultSettings = {
			visiblePrev: 3,
			visibleNext: 3,
			showPrev: true,
			showNext: true,
			showFirst: true,
			showLast: true
		}

		/*--------------------------------------------------*/

		this.setPage = function(f_id) {
			this.rebuild(f_id)
			if (gotoPageFunction != null) {
				gotoPageFunction(f_id);
			}
		} /*--------------------------------------------------*/

		this.rebuild = function(activeIndex, visiblePrev, visibleNext, showPrev, showNext, showFirst, showLast) {
			this.activeIndex = activeIndex = (typeof(activeIndex) != "undefined") ? activeIndex : 0;
			visiblePrev = (typeof(visiblePrev) != "undefined") ? visiblePrev : this.defaultSettings.visiblePrev;
			visibleNext = (typeof(visibleNext) != "undefined") ? visibleNext : this.defaultSettings.visibleNext;
			showPrev = (typeof(showPrev) != "undefined") ? showPrev : this.defaultSettings.showPrev;
			showNext = (typeof(showNext) != "undefined") ? showNext : this.defaultSettings.showNext;
			showFirst = (typeof(showFirst) != "undefined") ? showFirst : this.defaultSettings.showFirst;
			showLast = (typeof(showLast) != "undefined") ? showLast : this.defaultSettings.showLast;

			var startNumber = activeIndex - visiblePrev;
			if (startNumber <= 0) {
				visibleNext -= startNumber;
				startNumber = 0;
			}

			var endNumber = activeIndex + visibleNext;
			if (endNumber >= this.numPages) {
				endNumber = this.numPages;
			}

			var html = "";
			if (this.numPages > 0) {
				for (var i = startNumber; i < endNumber; i++) {
					html += this.makeNumberDisplay(i, activeIndex);
				}

				if (showFirst && activeIndex - visiblePrev > 0) {
					html = this.makeNumberDisplay(0, activeIndex) + " ... " + html;
				}

				if (showLast && activeIndex + visibleNext < this.numPages) {
					html = html + " ... " + this.makeNumberDisplay(this.numPages, activeIndex);
				}
			}
			$(this.containerId).html(html);
		} /*--------------------------------------------------*/

		this.makeNumberDisplay = function(index, activeIndex) {
			var classNames = 'pageNumber' + ((index == activeIndex) ? " actv" : "");
			var onclick = (index == activeIndex) ? "" : 'onmousedown="' + this.objectName + '.setPage(' + index + ')"';
			var displayText = (index + 1)
			var html = '<div class="' + classNames + '"' + onclick + '>' + displayText + '</div>';
			return html;
		} /*--------------------------------------------------*/

		this.nextPage = function() {
			if (this.activeIndex + 1 < this.numPages) {
				this.setPage(this.activeIndex + 1);
			}
		} /*--------------------------------------------------*/

		this.prevPage = function() {
			if (this.activeIndex - 1 >= 0) {
				this.setPage(this.activeIndex - 1);
			}
		} /*--------------------------------------------------*/

	}
}