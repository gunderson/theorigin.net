<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta name="viewport" content="width=1024, user-scalable=no ,initial-scale=1.0, maximum-scale=1.0"/>
		<meta name="apple-mobile-web-app-capable" content="yes">
        <title>Combat!</title>
		<link type="text/css" href="css/web.css" rel="stylesheet" />
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.3/jquery.min.js"></script>
		<scrpt src="js/jquery.animate-enhanced.min.js"></scrpt>
		<script>
		</script>
</head>
<body>
	<div id="container">
		<div id="matchScore">
			<div id="status">
				<div id="host">Robot BJJ</div>
				<div id="time">
					<div id="mins" class="numberContainer"><div class="display next">00</div><div class="display current">00</div>
						<div class="plus btn" onclick="adjustMins(1)">+</div>
						<div class="minus btn" onclick="adjustMins(-1)">-</div>
					</div>
					<div id="sep">:</div>
					<div id="secs"><div class="display next">00</div><div class="display current">00</div>
						<div class="plus btn" onclick="adjustSecs(1)">+</div>
						<div class="minus btn" onclick="adjustSecs(-1)">-</div>
					</div>
				</div>
				<div id="pause" class="btn wide" onclick="pauseTimer()">Pause</div>
				<div id="start" class="btn wide" onclick="startTimer()">Start</div>
				<div id="submit" class="btn wide" onclick="confirmSubmit()">End Match and Submit</div>
				<div id="reset" class="btn wide" onclick="confirmReset()">Reset</div>
				<div id="division">Male :: Blue Belt :: -195lb</div>

			</div>
			<div id="player0" class="player">
				<div id="name">Xande Ribero</div>
				<div id="points" class="numberContainer"><div class="display next">0</div><div class="display current">0</div>
					<div class="plus btn left" onclick="adjustScore(match.players[0], 'points',4, '#player0 #points') ">+4</div>
					<div class="plus btn" onclick="adjustScore(match.players[0], 'points', 3, '#player0 #points') ">+3</div>
					<div class="plus btn right" onclick="adjustScore(match.players[0], 'points', 2, '#player0 #points') ">+2</div>
					<div class="minus btn left" onclick="adjustScore(match.players[0], 'points', -4, '#player0 #points') ">-4</div>
					<div class="minus btn" onclick="adjustScore(match.players[0], 'points', -3, '#player0 #points') ">-3</div>
					<div class="minus btn right" onclick="adjustScore(match.players[0], 'points', -2, '#player0 #points') ">-2</div>
				</div>
				<div id="deductions" class="numberContainer"><div class="display next">0</div><div class="display current">0</div>
					<div class="plus btn" onclick="adjustScore(match.players[0], 'deductions', 1, '#player0 #deductions')">+</div>
					<div class="minus btn" onclick="adjustScore(match.players[0], 'deductions', -1, '#player0 #deductions')">-</div>
				</div>
				<div id="advantages" class="numberContainer"><div class="display next">0</div><div class="display current">0</div>
					<div class="plus btn" onclick="adjustScore(match.players[0], 'advantages', 1, '#player0 #advantages')">+</div>
					<div class="minus btn" onclick="adjustScore(match.players[0], 'advantages', -1, '#player0 #advantages')">-</div>
				</div>
			</div>
			<div id="player1" class="player">
				<div id="name">Roger Gracie</div>
				<div id="points" class="numberContainer"><div class="display next">0</div><div class="display current">0</div>
					<div class="plus btn left" onclick="adjustScore(match.players[1], 'points',4, '#player1 #points') ">+4</div>
					<div class="plus btn" onclick="adjustScore(match.players[1], 'points', 3, '#player1 #points') ">+3</div>
					<div class="plus btn right" onclick="adjustScore(match.players[1], 'points', 2, '#player1 #points') ">+2</div>
					<div class="minus btn left" onclick="adjustScore(match.players[1], 'points', -4, '#player1 #points') ">-4</div>
					<div class="minus btn" onclick="adjustScore(match.players[1], 'points', -3, '#player1 #points') ">-3</div>
					<div class="minus btn right" onclick="adjustScore(match.players[1], 'points', -2, '#player1 #points') ">-2</div>
				</div>
				<div id="deductions" class="numberContainer"><div class="display next">0</div><div class="display current">0</div>
					<div class="plus btn" onclick="adjustScore(match.players[1], 'deductions', 1, '#player1 #deductions')">+</div>
					<div class="minus btn" onclick="adjustScore(match.players[1], 'deductions', -1, '#player1 #deductions')">-</div>
				</div>
				<div id="advantages" class="numberContainer"><div class="display next">0</div><div class="display current">0</div>
					<div class="plus btn" onclick="adjustScore(match.players[1], 'advantages', 1, '#player1 #advantages')">+</div>
					<div class="minus btn" onclick="adjustScore(match.players[1], 'advantages', -1, '#player1 #advantages')">-</div>
				</div>
			</div>
			<div class="overlay"></div>
		</div>
		
		
		<div id="bracketTransitioner" class="bracket">
			<div id="title"></div>
			<div id="contents"></div>
		</div>
		<div id="bracket" class="bracket">
			<div id="title"></div>
			<div id="contents"></div>
		</div>

		<div id="adminMenu">
			<div id="nib"></div>
			<div id="verticalMenu">
				<div class="viewport">
					<div class="menuTitle">Tournament</div>
					<div class="menuItem">
						<div class="menuItemButton">Settings
							<div class="menuBullet"></div></div>
					</div>
					<div class="menuItem subHead">
						<div class="menuTitle">Black Belt</div>
					</div>
					<div class="menuItem" onclick="showBracket(bracketData0)">
						<div class="menuItemButton">Absolute
							<div class="menuBullet"></div></div>
					</div>
					<div class="menuItem" onclick="showBracket(bracketData1)">
						<div class="menuItemButton">Ultra Heavy +220.5
							<div class="menuBullet"></div></div>
					</div>
					<div class="menuItem" onclick="showBracket(bracketData0)">
						<div class="menuItemButton">Super Heavy -220.5
							<div class="menuBullet"></div></div>
					</div>
					<div class="menuItem" onclick="showBracket(bracketData1)">
						<div class="menuItemButton">Heavy -204.5
							<div class="menuBullet"></div></div>
					</div>
					<div class="menuItem" onclick="showBracket(bracketData0)">
						<div class="menuItemButton">Medium Heavy -194.5
							<div class="menuBullet"></div></div>
					</div>
					<div class="menuItem" onclick="showBracket(bracketData1)">
						<div class="menuItemButton">Middle -188.0
							<div class="menuBullet"></div></div>
					</div>
				</div>
			</div>

		</div>

	</div>

	<audio id="endSound" controls="false" preload="auto">
		<source src="snd/whistle.wav" type="audio/wav" />
	</audio> 

	<div id="templates">
		<div id="bracketRoundTemplate">
			<div class="round" num=""></div>
		</div>
		<div id="bracketMatchTemplate">
			<div class="matchup">
				<div class="connector"><div id="showMatchBtn">Show Match</div></div>
				<div class="player0 bracketPosition">
					<div class="competitorSeed"></div>
					<div class="competitorName"></div>
					<div class="competitorAcademy"></div>
				</div>
				<div class="player1 bracketPosition">
					<div class="competitorSeed"></div>
					<div class="competitorName"></div>
					<div class="competitorAcademy"></div>
					
				</div>
				<div class="winner bracketPosition"></div>
			</div>
		</div>
	</div>
	<script>
		
		
		$(document).ready( function(){  
			setTimeout(scrollTo, 0, 0, 1); // Kill Safari Chrome
			$("#container").css("top", (($(window).height() - $("#container").height()) / 2) + "px");
			
			showMatch(match0);
			$("#adminMenu").hide();
			$(".bracket").hide();
		}, false);
		
		$(window).resize(function(){
			$("#container").css("top", (($(window).height() - $("#container").height()) / 2) + "px")
		})
		
		var TWEEN_TIME = 350;
		
		var state = {
			activePane: "#bracket"
		}
		
		
		//---------------------------------------------
		// Match App
		var match;
		var match0 = {
			winner: -1,
			roundLength: 600,
			overtimeLength: 0,
			timeRemaining: 600,
			timeElapsed: 0,
			division: "",
			players: [
				{
					name: "Player A",
					points: 0,
					deductions: 0,
					advantages: 0,
					dq: false
				},
				{
					name: "Player B",
					points: 0,
					deductions: 0,
					advantages: 0,
					dq: false
				}
			]
		}
		
		var match1 = {
			winner: -1,
			roundLength: 600,
			overtimeLength: 0,
			timeRemaining: 500,
			timeElapsed: 100,
			division: "Male :: Black Belt :: HeavyWeight",
			players: [
				{
					name: "Raphael Lovato",
					points: 0,
					deductions: 0,
					advantages: 0,
					dq: false
				},
				{
					name: "Cobrinha",
					points: 0,
					deductions: 0,
					advantages: 0,
					dq: false
				}
			]
		}
		var timer;
		
		function startTimer(){
			if (match.timeElapsed == 0){
				//playStartSound();
			}
			
			timer = setInterval(tickTimer, 1000);
			$("#start").hide();
			$("#submit").hide();
			$("#reset").hide();
			$("#pause").show();
		}
		
		function pauseTimer(){
			clearInterval(timer);
			$("#start").show();
			$("#submit").show();
			$("#reset").show();
			$("#pause").hide();
		}
		
		function tickTimer(){
			match.timeRemaining -= 1;
			match.timeElapsed += 1;
			if (match.timeRemaining <= 0){
				pauseTimer();
				playEndSound();
			}
				displayTime(1);
		}
		
		function displayTime(f_amount){
			var prevMins = $("#mins .display.next").text();
			$("#mins .display.next").text(addLeadingZeros((match.timeRemaining / 60) >> 0, 2))
			$("#secs .display.next").text(addLeadingZeros((match.timeRemaining % 60), 2))
			
			if (typeof(f_amount) != "undefined"){
				if (f_amount >= 0 && f_amount < 60){
					$("#secs .display.next").css("top", "-175px").show();
					$("#secs .display.next").animate({top:"0px"}, TWEEN_TIME, "swing");
					$("#secs .display.current").animate({top:"175px"}, TWEEN_TIME, "swing", function(){resetNumberDisplay("#secs")});
				} else if (f_amount < 0 && f_amount > -60){
					$("#secs .display.next").css("top", "175px").show();
					$("#secs .display.next").animate({top:"0px"}, TWEEN_TIME, "swing");
					$("#secs .display.current").animate({top:"-175px"}, TWEEN_TIME, "swing", function(){resetNumberDisplay("#secs")});
				} 
				
				if (f_amount >= 0 && prevMins != $("#mins .display.next").text()){
					$("#mins .display.next").css("top", "-175px").show();
					$("#mins .display.next").animate({top:"0px"}, TWEEN_TIME, "swing");
					$("#mins .display.current").animate({top:"175px"}, TWEEN_TIME, "swing", function(){resetNumberDisplay("#mins")});
				} else if (f_amount < 0 && prevMins != $("#mins .display.next").text()){
					$("#mins .display.next").css("top", "175px").show();
					$("#mins .display.next").animate({top:"0px"}, TWEEN_TIME, "swing");
					$("#mins .display.current").animate({top:"-175px"}, TWEEN_TIME, "swing", function(){resetNumberDisplay("#mins")});
				}
			}
		}
		
		function adjustMins(f_amount){
			match.timeRemaining += f_amount * 60;
			if (match.timeRemaining < 0){
				match.timeRemaining = 0;
			}
			displayTime(f_amount * 60)
		}
		
		function adjustSecs(f_amount){
			match.timeRemaining += f_amount;
			if (match.timeRemaining < 0){
				match.timeRemaining = 0;
			}
			displayTime(f_amount * 1)
		}
		
		function endMatch(){
			pauseTimer()
		}
		
		function playStartSound(){
			document.getElementById('endSound').play();
		}
		
		function playEndSound(){
			document.getElementById('endSound').play();
		}
		
		function confirmReset(){
			var r = confirm("Reset the match?");
			if (r == true)
			{
				reset();
			}
			else
			{
				return false;
			}
		}
		
		function reset(){
			match.timeRemaining = match.roundLength;
			match.timeElapsed = 0;
			match.players[0].points = match.players[0].deductions = match.players[0].advantages = 0
			match.players[1].points = match.players[1].deductions = match.players[1].advantages = 0;
			adjustScore(match.players[0], 'points', 0, '#player0 #points')
			adjustScore(match.players[0], 'deductions', 0, '#player0 #deductions')
			adjustScore(match.players[0], 'advantages', 0, '#player0 #advantages')
			adjustScore(match.players[1], 'points', 0, '#player1 #points')
			adjustScore(match.players[1], 'deductions', 0, '#player1 #deductions')
			adjustScore(match.players[1], 'advantages', 0, '#player1 #advantages')
			$(".player .minus").hide();
			displayTime(0);
		}
		
		function adjustScore(f_player, f_scoreCategory, f_amount, f_displayObject){
			f_player[f_scoreCategory] += f_amount;
						
			if (f_player[f_scoreCategory] <= 0){
				f_player[f_scoreCategory] = 0;
				$(f_displayObject + " .minus").hide();
			} else {
				$(f_displayObject + " .minus").show();
			}
			
			var txt = (f_scoreCategory == "deductions" && f_player[f_scoreCategory] > 0) ? "-" + f_player[f_scoreCategory] : f_player[f_scoreCategory].toString();
			$(f_displayObject + " .display.next").text(txt);
			
			if (f_amount >= 0){
				$(f_displayObject + " .display.next").css("top", "-270px").show();
				$(f_displayObject + " .display.next").animate({top:"0px"}, TWEEN_TIME, "swing");
				$(f_displayObject + " .display.current").animate({top:"270px"}, TWEEN_TIME, "swing", function(){resetNumberDisplay(f_displayObject)});
			} else {
				$(f_displayObject + " .display.next").css("top", "270px").show();
				$(f_displayObject + " .display.next").animate({top:"0px"}, TWEEN_TIME, "swing");
				$(f_displayObject + " .display.current").animate({top:"-270px"}, TWEEN_TIME, "swing", function(){resetNumberDisplay(f_displayObject)});
			}
		}
		
		function addLeadingZeros(f_num, f_minLength){
			f_num = f_num.toString();
			var numZeros = f_minLength - f_num.length;
			var i = -1;
			while (++i < numZeros){
				f_num = "0".concat(f_num);
			}
			return f_num;
		}
		
		function resetNumberDisplay(f_elementName){
			//console.log(f_elementName);
			var newText = $(f_elementName + " .display.next").text()
			$(f_elementName + " .display.next").hide();
			$(f_elementName + " .display.current").text(newText).css("top", "0px");
			
		}
		
		function showOverlay(){
			$(state.activePane + " #overlay").show().animate({opacity: "0.7"}, TWEEN_TIME);
		}
		
		function hideOverlay(){
			$(state.activePane + " #overlay").animate({opacity: "0.0"}, TWEEN_TIME, null, function(){$("#overlay").hide()});
		}
		
		//---------------------------------------------
		// Settings Menu
		
		function showMenu(){
			$("#nib").bind("click",hideMenu).unbind("click",showMenu);
			showOverlay()
			//$("#nib").css({right:"auto", left:"-10px"});
			$("#adminMenu").animate(
				{
					left:"0px",
					useTranslate3d: true
				}, 
				TWEEN_TIME, 
				"swing", 
				function(){
				}
				
			);
		}
		
		function hideMenu(){
			$("#nib").bind("click",showMenu).unbind("click",hideMenu);
			hideOverlay()
			$("#adminMenu").animate(
				{
					left:"-204px",
					useTranslate3d: true
				}, 
				TWEEN_TIME, 
				"swing", 
				function(){
				}
			);
		}
		
		function showBracket(f_bracketData){
			bracketData = f_bracketData;
			$("#bracketTransitioner #contents").html($("#bracket #contents").html())
			makeBracketDisplay()
			var direction = 1;
			var startTop = 0;
			if (state.activePane == "#bracket"){
				startTop = -600 * direction;
			}
			$("#bracket").css({
				display:"block",
				top: startTop + "px"
			});
			$("#bracket").animate(
				{
					top:0,
					left: 0
				}, 
				TWEEN_TIME,
				"swing",
				function(){
					state.activePane = "#bracket";
				}
			);	
			
			
			
			
			if (state.activePane == "#bracket"){
				$("#bracketTransitioner").css({
					display:"block",
					top:"0px"
				});

				$("#bracketTransitioner").animate(
					{
						top:(600 * direction) + "px"
					}, 
					TWEEN_TIME,
					"swing",
					function(){}
				);
			}

				$("#matchScore").animate(
					{
						left:1024
					}, 
					TWEEN_TIME,
					"swing",
					function(){}
				);
			
			hideMenu()
		}
		
		
		// End Settings Menu
		//---------------------------------------------
		// Bracket Builder
		
		function showMatch(f_matchData){
			
			match = f_matchData;
			$("#matchScore").animate(
				{
					left:0
				}, 
				TWEEN_TIME,
				"swing",
				function(){}
			);
			$("#bracket").animate({left:-1024}, TWEEN_TIME, null, function(){
					state.activePane = "#matchScore";
				}
			)
			
			$("#player0 #name").text(match.players[0].name)
			$("#player1 #name").text(match.players[1].name)
			$("#division").text(match.division)
			
			$(".player .minus").hide();
			$(".display.next").hide();
			displayTime(0);
		}
		
		var bracketData0 = {
			status: "prestart",
			sex: "Male",
			age: "Adult",
			weight: "Absolute",
			rank: "Black Belt",
			time: 600,
			competitors: [
				{name: "Player 0", academy: "School 0"},
				{name: "Player 1", academy: "School 1"},
				{name: "Player 2", academy: "School 2"},
				{name: "Player 3", academy: "School 3"},
				{name: "Player 4", academy: "School 4"},
				{name: "Player 5", academy: "School 5"},
				{name: "Player 6", academy: "School 6"},
				{name: "Player 7", academy: "School 7"}
			],
			results: [],
			numRounds: null,
			seedOrder: null
		};
		
		var bracketData1 = {
			status: "prestart",
			sex: "Male",
			age: "Adult",
			weight: "Absolute",
			rank: "Black Belt",
			time: 600,
			competitors: [
				{name: "Player 0 1", academy: "School 0"},
				{name: "Player 1 1", academy: "School 1"},
				{name: "Player 2 1", academy: "School 2"},
				{name: "Player 3 1", academy: "School 3"},
				{name: "Player 4 1", academy: "School 4"},
				{name: "Player 5 1", academy: "School 5"},
				{name: "Player 6 1", academy: "School 6"},
				{name: "Player 7 1", academy: "School 7"}
			],
			results: [],
			numRounds: null,
			seedOrder: null
		};
		
		function getBracketData(){
			
		}
		
		function onGetBracketDataComplete(){
			
		}
		
		function makeBracketDisplay(){
			$("#bracket #contents").html("");
			// numRounds = Math.ceil(log2(numCompetitors))
			bracketData.numRounds = Math.ceil(Math.log(bracketData.competitors.length) / Math.log(2));
			bracketData.numSlots = Math.pow(2, bracketData.numRounds);
			bracketData.seedOrder = getSeedOrder();
			var i = -1;
			while (++i < bracketData.numRounds){
				buildRound(i);
			}
			
			
			//setup first round display
			i = -1;
			while (++i < bracketData.competitors.length){
				$("#bracket .round:eq(0) > .matchup .competitorName:eq("+bracketData.seedOrder[i]+") ").text(bracketData.competitors[i].name);		
			}
			
			i = -1;
			while (++i < $("#bracket .round:eq(0) > .matchup").length){
				console.log($("#bracket .round:eq(0) > .matchup").length)
				if (i % 2 == 0){ // if switch only for test data
					$("#bracket .round:eq(0) > .matchup:eq("+i+") #showMatchBtn").bind("click", function(){ showMatch(match0) })
				} else {
					$("#bracket .round:eq(0) > .matchup:eq("+i+") #showMatchBtn").bind("click", function(){ showMatch(match1) })
				}
			}
			
			
			//setup subsequent round displays
			if (bracketData.results.length > 0){
				var r = -1;
				while (++r < bracketData.results.length){
					i = -1;
					while (++i < bracketData.compresultsetitors){
						$("#bracket .round:eq("+r+") > .matchup .competitorName:eq("+i+")  ").text(bracketData.results[r].winner[i].name)
					}
				}
			}
		}
		
		function buildRound(f_round){
			var i = -1;
			var numMatches = bracketData.numSlots / Math.pow(2, f_round + 1);
			$("#bracket #contents").append($("#bracketRoundTemplate").html());
			$("#bracket .round").last().attr("num", f_round);
			var multiple = (f_round + 3)
			var matchupHeight = (40 * Math.pow(2, multiple));
			
			while (++i < numMatches){
				$("#bracket  .round").last().append($("#bracketMatchTemplate").html());
				$("#bracket .round:last > .matchup").eq(i).css("height", matchupHeight + "px").css("top",(i * matchupHeight) + "px");
			}
			$("#bracket .round").last().css("left",(240 * f_round) + "px");
			
			var connectorHeight = matchupHeight >> 1;
			var baseline = 10 * Math.pow(2, multiple) - 20;
			$("#bracket .round:last > .matchup > .player0").css("bottom", (baseline + connectorHeight) + "px");
			//console.log("Round: " + f_round , (80 * (f_round + 1)) + "px")
			$("#bracket .round:last > .matchup > .player1").css("bottom", baseline + "px");
			$("#bracket .round:last > .matchup > .connector").css("height", connectorHeight + "px").css("bottom", baseline + "px");
			
			$("#bracket .round:last > .matchup > .winner").css("bottom", baseline + (connectorHeight >> 1) + "px");
			
		}
		
		function getSeedOrder(){
			var correctOrder = [0,4,7,3,6,2,5,1]
			var order = [];
			var slotId = 0;
			var i = -1;
			while(++i < bracketData.numSlots){
				switch (i){
					case 0:
						slotId = 0
						break;
					case 1:
						slotId = bracketData.numSlots >> 1;
						break;
					case 2:
						slotId = bracketData.numSlots - 1;
						break;
					default:
						if (i % 2 == 1){
							//odds
							slotId -= bracketData.numSlots >> 1;
						} else {
							//evens
							slotId += (bracketData.numSlots >> 1) - 1;
						}
						break;
				}
				order.push(slotId);
			}
			var inverseIndex
			
//			console.log(correctOrder)
//			console.log(order)
			return order;
		}
		// End Bracket Builder
		//---------------------------------------------
			
		var init = function (){
			
			$("div").bind("selectstart", function(){return false});
			$("#nib").bind("click",showMenu)
			$(".menuItem").hover(
			function () {
				$(this).addClass("over");
			},
			function () {
				$(this).removeClass("over");
			});
			//redefine easing as easeOutQuart
			$.easing.swing =  function (x, t, b, c, d) {
				return -c * ((t=t/d-1)*t*t*t - 1) + b;
			};
		}();
	</script>
</body>
</html>
