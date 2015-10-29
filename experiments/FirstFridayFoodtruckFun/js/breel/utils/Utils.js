var breel = breel || {};
var $b = breel;
breel.utils = breel.utils || {};


////////////////////////////////////////////////////////////////////////////////
//Function Queue
////////////////////////////////////////////////////////////////////////////////
breel.FunctionQueue = function(runOnce) {
	var self = this;

	self.queue = [];
	self.enabled = true;
	self.runOnce = runOnce ? runOnce : false;
	self.runCount = 0;

	self.execute = function() {
		if (self.enabled) {
			var i = -1;
			var endi = self.queue.length;
			while (++i < endi) {
				self.queue[i]();
			}
		}
		self.runCount++;
		if (self.runOnce) self.enabled = false;
	};

	self.append = function(func, priority) {
		if ((self.runOnce && self.runCount === 0) || !self.runOnce) {
			if (!priority || priority === 0) {
				priority = 0;
				self.queue.push(func);
			} else {
				priority = Math.max(0, Math.min(self.queue.length, priority));
				self.queue.splice(self.queue.length - priority, 0, func);
			}
		} else {
			func();
		}

	};
};
////////////////////////////////////////////////////////////////////////////////
//Forms
////////////////////////////////////////////////////////////////////////////////
breel.validateForm = function(requiredFieldIds, invalidMsgId) {
	trace("breel.validateForm");
	var isValid = true;
	var validFields = [];
	var invalidFields = [];
	var emails = [];
	var emailsToMatch = 2;
	var val;
	var defaultValue;
	var field;
	for (var i = 0; i < requiredFieldIds.length; i++) {
		field = $(requiredFieldIds[i].id);
		if (requiredFieldIds[i].type == "checkbox"){
			val = field.is(':checked');
			defaultValue = false;
		} else if (requiredFieldIds[i].type == "select"){
			val = field.attr('value');
			defaultValue = field.attr("defaultValue");
		} else {
			field.val($.trim(field.val()));
			val = field.val();
			defaultValue = field.attr("defaultValue");
		}
		//is it filled-in?
		if (val == defaultValue || !val){
			invalidFields.push(requiredFieldIds[i]);
			isValid = false;
			//is it an email address?
		} else if (requiredFieldIds[i].email){
			emails.push(requiredFieldIds[i].email);
			//does it match?
			if (emails.length == emailsToMatch && emails[0] != emails[1]){
				//is it valid?
				if (!breel.validateEmailAddress(requiredFieldIds[i].email)){
					invalidFields.push(requiredFieldIds[i]);
					isValid = false;
				} else {
					validFields.push(requiredFieldIds[i]);
					isValid = false;
				}
			}
		} else {
			//it's valid
			validFields.push(requiredFieldIds[i]);
		}
	}

	trace(validFields);
	trace(invalidFields);
	
	breel.markInvalid(invalidFields, validFields);
	if (!isValid){
		$(invalidMsgId).show();
	} else {
		$(invalidMsgId).hide();
	}
	
	return isValid;
};

breel.validateEmailAddress = function(address) {
	trace("breel.validateEmailAddress");
	var isValid = true;
	var atpos = address.indexOf("@");
	var dotpos = address.lastIndexOf(".");
	if (atpos < 1 || dotpos < atpos + 2 || dotpos + 2 >= address.length) {
		isValid = false;
	}
	return isValid;
};

breel.markInvalid = function(invalidFields, validFields) {
	
/*
 * TODO: defect: ,invalidField is never removed
 */
	var i;
	if (validFields) {
		for (i in validFields) {
			$(validFields[i].id).removeClass('invalidField');
			$(validFields[i].label).removeClass('invalidField');
		}
	}
	
	for (i in invalidFields) {
		$(invalidFields[i].id).addClass('invalidField');
		$(invalidFields[i].label).addClass('invalidField');
	}
};


////////////////////////////////////////////////////////////////////////////////
// helper functions
////////////////////////////////////////////////////////////////////////////////

breel.utils.formatDate = function(dateString){
	if(breel.langId == "en"){
		return dateString;
	} else {
		var parts = dateString.split("/");
		var month = parts[0];
		var date = parts[1];
		var year = parts[2];
		return (date + "/" + month + "/" + year);
	}
	
};
breel.utils.getOrdinal = function(n) {
    var s=["th","st","nd","rd"],
    v=n%100;
    return n+(s[(v-20)%10]||s[v]||s[0]);
}

breel.utils.addLeadingZeros = function(number, length) {
    var str = '' + number;
    while (str.length < length) {
        str = '0' + str;
    }
    return str;

}
////////////////////////////////////////////////////////////////////////////////
// Use these because IE breaks when you use common, useful functions
////////////////////////////////////////////////////////////////////////////////
window.requestAnimFrame = (function(){
  return  window.requestAnimationFrame       || 
          window.webkitRequestAnimationFrame || 
          window.mozRequestAnimationFrame    || 
          window.oRequestAnimationFrame      || 
          window.msRequestAnimationFrame     || 
          function( callback ){
            window.setTimeout(callback, 1000 / 60);
          };
})();


if (!Array.prototype.indexOf) {
	Array.prototype.indexOf = function(f_val) {
		var i = this.length;
		var endi = -1;
		while (--i > endi) {
			if (this[i] == f_val) {
				break;
			}
		}
		return i;
	};
}

if(typeof String.prototype.trim !== 'function') {
  String.prototype.trim = function() {
    return this.replace(/^\s+|\s+$/g, '');
  };
}

function trace() {
	if ( typeof (console) != "undefined") {
		console.log.apply(console, arguments);
	}
}

function throwError( e ) {
	if ( typeof (console) != "undefined") {
		console.error( e );
	}
}


/* json2.js
 * 2008-01-17
 * Public Domain
 * No warranty expressed or implied. Use at your own risk.
 * See http://www.JSON.org/js.html
*/
if(!this.JSON){
	this.JSON = function(){
		function f(n){
			return n<10?'0'+n:n;
		}
Date.prototype.toJSON=function(){return this.getUTCFullYear()+'-'+
f(this.getUTCMonth()+1)+'-'+
f(this.getUTCDate())+'T'+
f(this.getUTCHours())+':'+
f(this.getUTCMinutes())+':'+
f(this.getUTCSeconds())+'Z';};var m={'\b':'\\b','\t':'\\t','\n':'\\n','\f':'\\f','\r':'\\r','"':'\\"','\\':'\\\\'};function stringify(value,whitelist){var a,i,k,l,r=/["\\\x00-\x1f\x7f-\x9f]/g,v;switch(typeof value){case'string':return r.test(value)?'"'+value.replace(r,function(a){var c=m[a];if(c){return c;}
c=a.charCodeAt();return'\\u00'+Math.floor(c/16).toString(16)+
(c%16).toString(16);})+'"':'"'+value+'"';case'number':return isFinite(value)?String(value):'null';case'boolean':case'null':return String(value);case'object':if(!value){return'null';}
if(typeof value.toJSON==='function'){return stringify(value.toJSON());}
a=[];if(typeof value.length==='number'&&!(value.propertyIsEnumerable('length'))){l=value.length;for(i=0;i<l;i+=1){a.push(stringify(value[i],whitelist)||'null');}
return'['+a.join(',')+']';}
if(whitelist){l=whitelist.length;for(i=0;i<l;i+=1){k=whitelist[i];if(typeof k==='string'){v=stringify(value[k],whitelist);if(v){a.push(stringify(k)+':'+v);}}}}else{for(k in value){if(typeof k==='string'){v=stringify(value[k],whitelist);if(v){a.push(stringify(k)+':'+v);}}}}
return'{'+a.join(',')+'}';}}
return{stringify:stringify,parse:function(text,filter){var j;function walk(k,v){var i,n;if(v&&typeof v==='object'){for(i in v){if(Object.prototype.hasOwnProperty.apply(v,[i])){n=walk(i,v[i]);if(n!==undefined){v[i]=n;}}}}
return filter(k,v);}
if(/^[\],:{}\s]*$/.test(text.replace(/\\./g,'@').replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g,']').replace(/(?:^|:|,)(?:\s*\[)+/g,''))){j=eval('('+text+')');return typeof filter==='function'?walk('',j):j;}
throw new SyntaxError('parseJSON');}};}();}