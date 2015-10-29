var trace = function() {
    if ( typeof (console) != "undefined") {
        console.log.apply(console, arguments);
    }
};

var throwError = function( e ) {
    if ( typeof (console) != "undefined") {
        console.error( e );
    }
};

Math.TWO_PI = Math.PI*2;

function declare(namespace, creator){
    creator = creator || "unknown";
	var parts = namespace.split(".");
	var obj = this;
    var parent = this;
    var moduleName = "";
	for (var i in parts){
        obj = parent[parts[i]];
        moduleName += (i === "0") ? parts[i] : "." + parts[i] ;
		if (!obj){
			obj = parent[parts[i]] = {definedBy: creator};
			trace("Module " + moduleName + " defined by " + creator);
		} else {
            trace("Module " + moduleName + " already exists. Defined by " + parent[parts[i]].definedBy);
        }
        parent = obj;
	}
}

declare("breel.app", "js/breel/Bootstrap.js");
$b = breel;

// Helper method for extending one object with another
breel.extend = function(a,b, verbose) {
    for ( var i in b ) {
        var g = b.__lookupGetter__(i), s = b.__lookupSetter__(i);
       
        if ( g || s ) {
            if ( g )
                a.__defineGetter__(i, g);
            if ( s )
                a.__defineSetter__(i, s);
         } else {
             a[i] = b[i];
             if (verbose) trace(i);
         }
    }
    a.superClass = $.extend({},b);
    return a;
};

breel.createDiv = function(id, parent, className){
    var newDiv = document.createElement('div');
    newDiv.id = id || "";
    newDiv.className = className || "";
    parent.appendChild(newDiv);
    return newDiv;
};