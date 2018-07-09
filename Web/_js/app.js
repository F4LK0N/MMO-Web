//############
//### USER ###
//############
var USER = {
	token: "",
	
	nick: "",
	
	fov: 5,
	
	x: 0,
	y: 0,
	offsetX: 0,
	offsetY: 0,
	
	Update: function(data){
		USER.nick = data.nick;
		
		USER.x = data.x;
		USER.y = data.y;
		
		USER.offsetX = USER.x - USER.fov;
		USER.offsetY = USER.y - USER.fov;
	}
};



//###########
//### API ###
//###########
var API = {
	URL: "",
	URL_ONLINE: "http://mmo.web7031.uni5.net/API/API/",
	URL_OFFLINE: "http://127.0.0.1/MMO/API/API/",
	
	Init: function(){
	
		//URL
		if(ENV.SERVER===ENV.SERVER_OFFLINE)
			this.URL = this.URL_OFFLINE;
		else
			this.URL = this.URL_ONLINE;
		
	},
	
	ParseData: function (data){
		
		//PARSED
		if(typeof data === "object"){ return data; }
		
		//PARSE
		var dataParsed = null;
		try{
			dataParsed = $.parseJSON(data);
		}
		catch(errorMessage){
			dataParsed = {
				error: "<b>Error parsing JSON!</b><br><br><small>Technical Details:<br>"+errorMessage,
				data:  data
			}
		}
		
		return dataParsed;
	},
	
	Get: function(url, callbackFunction){
		
		//RESPONSE
		let response = {
			connected: false,
			error: false,
			data: false,
		};
		
		//HEADER
		var header = {};
		if (USER.token!=="") header.AuthToken = USER.token;
		
		//CONNECT
		$.ajax({
			url: this.URL + url + "?time=" + Date.now(),
			method: 'GET',
			headers: header,
			timeout: 500,
			success: function (data) {
				response.connected = true;
				response = API.ParseData(data);
			},
			statusCode: { 401: function(jqXHR) {
				response.connected = true;
				response = API.ParseData(jqXHR.responseText);
			}},
			error: function(){
				response.connected = false;
				response.error     = "Error connecting to server!";
			},
			complete: function (){
				if(typeof callbackFunction === "function") callbackFunction(response);
			}
		});
	
	},
	
	Post: function(url, data, callbackFunction){
		
		//RESPONSE
		let response = {
			connected: false,
			error: false,
			data: false,
		};
		
		//HEADER
		var header = {};
		if (USER.token!=="") header.AuthToken = USER.token;
		
		//CONNECT
		$.ajax({
			url: this.URL + url + "?time=" + Date.now(),
			method: 'POST',
			headers: header,
			dataType: 'json',
			data: data,
			timeout: 500,
			success: function (data) {
				response.connected = true;
				response = API.ParseData(data);
			},
			statusCode: { 401: function(jqXHR) {
				response.connected = true;
				response = API.ParseData(jqXHR.responseText);
			}},
			error: function(){
				response.connected = false;
				response.error     = "Error connecting to server!";
			},
			complete: function (){
				if(typeof callbackFunction === "function") callbackFunction(response);
			}
		});
	},

};
API.Init();



//############
//### AUTH ###
//############
var AUTH = {
	
	Init: function(){
		
		let $sendButton = $("#login button");
		$sendButton.on("click", function(ev){ AUTH.Login(); });
		
		
		let $inputField = $("#login input");
		$inputField.focus();
		$inputField.on("keydown", function(ev){
			if(ev.which === 13 || ev.which === 32){
				AUTH.Login();
				ev.preventDefault();
			}
		});
		
		$("#login").on("click", function(){ $("#login input").focus(); });
		
	},
	
	Login: function(){
		
		let $nick = $("#login input").val();
		
		if($nick===""){
			alert("Please enter a nick name!");
			return;
		}
		
		if($nick.length>20){
			$nick = $nick.substr(0,20);
		}
		
		
		
		$('#login').hide();
		USER.nick = $nick;
		API.Post("Login/", {nick:$nick}, AUTH.LoginCallback);
	},
	
	LoginCallback: function(response){
	
		if('User' in response && 'Token' in response.User){
			USER.token = response.User.Token;
			console.log(USER);
			GAME.Init();
		}
		
		else{
			AUTH.Reset();
		}
		
	},
	
	Reset: function(){
		$("#login").show();
		$("#login input").focus();
	},
	
};
window.onload = AUTH.Init;



//############
//### GAME ###
//############
var GAME = {
	
	running: false,
	callerID: 0,
	updateInterval: 250,
	lastUpdate: 0,
	skipedPackages: 0,
	skipedPackagesTimeout: 15,
	
	Init: function(){
		
		MAP.Init();
		PLAYERS.Init();
		
		GAME.running = true;
		GAME.callerID = setInterval(GAME.UpdateRequest, GAME.updateInterval);
		GAME.lastUpdate = 0;
		GAME.skipedPackages = 0;
		
		GAME.PositionElements();
		if(window.onresize===null)
			window.onresize = GAME.PositionElements;
		
		ACTIONS.Init();
		
		$("#game").show();
		GAME.UpdateRequest();
	},
	
	End: function(){
		
		MAP.End();
		PLAYERS.End();
		ACTIONS.End();
		
		GAME.running = false;
		clearInterval(GAME.callerID);
		GAME.callerID = 0;
		$("#game").hide();
		
		AUTH.Reset();
	},
	
	PositionElements: function(){
		
		if(!GAME.running)
			return;
		
		//Get Refs and Sizes
		let $window = $(window);
		let windowWidth = $window.width();
		let windowHeight = $window.height();
		
		let $map = $("#game #map");
		let $players = $("#game #players");
		let tableWidth = $map.width();
		let tableHeight = $map.height();
		
		//Calculate
		let top = (windowHeight/2.0) - (tableHeight/2.0);
		let left = (windowWidth/2.0) - (tableWidth/2.0);
		
		//Position
		$map.css({top:top+'px', left:left+'px'});
		$players.css({top:top+'px', left:left+'px'});
	},
	
	UpdateRequest: function(){
		API.Get("Update/", GAME.Update);
	},
	
	Update: function(response){
		
		//ERROR
		if(!response)
			return;
		
		//SESSION EXPIRE
		if('User' in response && response.User===0){
			GAME.End();
			alert("Sessão expirada");
			return;
		}
		
		//PACKAGE TIME ORDER
		if(!('Time' in response) || GAME.lastUpdate > response.Time){
			
			console.log("Package Skiped! ("+GAME.skipedPackages+")");
			GAME.skipedPackages++;
			
			//Lost Connection
			if(GAME.skipedPackages > GAME.skipedPackagesTimeout){
				alert("Server Connection Lost!");
				GAME.End();
			}
			return;
		}
		GAME.lastUpdate = response.Time;
		GAME.skipedPackages = 0;
		
		//USER
		if('User' in response)
			USER.Update(response.User);
		
		//MAP
		if('Map' in response)
			MAP.Update(response.Map);
		
		//PLAYERS
		if('Players' in response)
			PLAYERS.Update(response.Players);
		
	},
	
};



//###########
//### MAP ###
//###########
var MAP = {
	
	refArray: null,
	tilesX: 11,
	tilesY: 11,
	
	Init: function(){
		let $table = $("#map");
		$table.empty();
		
		MAP.refArray = [];
		for(let y=0; y<MAP.tilesY; y++){
			MAP.refArray[y] = [];
			let tableRow = $("<tr>");
			$table.append(tableRow);
			
			for(let x=0; x<MAP.tilesX; x++){
				MAP.refArray[y][x] = $("<td>");
				tableRow.append(MAP.refArray[y][x]);
			}
		}
	},
	
	End: function(){
	
	},
	
	Update(data){
		
		for(let y=0; y<MAP.tilesY; y++){
			
			let dataRow = data[y];
			for(let x=0; x<MAP.tilesX; x++){
				
				let dataCel = dataRow[x];
				if(dataCel===0)
					MAP.refArray[y][x].css({"background-color":""});
				else{
					MAP.refArray[y][x].css({"background-color":PLAYERS.GetColorRGB(dataCel)});
				}
			}
		}
		
	},

};



//###############
//### PLAYERS ###
//###############
var PLAYERS = {
	
	refArray: null,
	
	Init: function(){
		let $table = $("#players");
		$table.empty();
		
		PLAYERS.refArray = [];
		for(let y=0; y<11; y++){
			PLAYERS.refArray[y] = [];
			let tableRow = $("<tr>");
			$table.append(tableRow);
			
			for(let x=0; x<11; x++){
				PLAYERS.refArray[y][x] = $("<td>");
				tableRow.append(PLAYERS.refArray[y][x]);
			}
		}
	},
	
	End: function(){
	
	},
	
	Update(data){
		
		for(let y=0; y<11; y++){
			for(let x=0; x<11; x++){
				PLAYERS.refArray[y][x].empty();
			}
		}
		
		$.each(data, function(id, player) {
			PLAYERS.refArray[(player.y-USER.offsetY)][(player.x-USER.offsetX)].html(PLAYERS.GetPlayer(id, player.nick));
		});
		
	},
	
	GetPlayer: function(id, nick){
		let color = PLAYERS.GetColor(id);
		return '<div style="background-color:rgb('+(color.r+30)+', '+(color.g+30)+', '+(color.b+30)+')">' +
					'<div style="background-color:rgb('+(color.r-30)+', '+(color.g-30)+', '+(color.b-30)+')">' +
						'<div style="background-color:rgb('+(color.r+30)+', '+(color.g+30)+', '+(color.b+30)+')">' +
							'<p>'+nick+'</p>'+
						'</div>' +
					'</div>' +
				'</div>';
	},
	
	
	
	//### COLORS ###
	colorsArray: {},
	GetColor: function(id){
	
		//CACHE
		if(id in PLAYERS.colorsArray)
			return PLAYERS.colorsArray[id];
		
		//CREATE
		let color = {
			r: (Math.floor(Math.random() * 150) + 50),
			g: (Math.floor(Math.random() * 150) + 50),
			b: (Math.floor(Math.random() * 150) + 50),
		};
		
		PLAYERS.colorsArray[id] = color;
		return color;
	},
	GetColorRGB: function(id){
		let color = PLAYERS.GetColor(id);
		return "rgb("+ color.r +","+ color.g +", "+ color.b +")";
	},
	
};



//###############
//### ACTIONS ###
//###############
var ACTIONS = {
	
	cooldown: 50,
	lastAction: 0,
	
	actionsList: {},
	
	listening: false,
	listenerID: 0,
	listenInterval: 50,
	
	Init: function(){
		$(document).on("keydown", ACTIONS.ListenerKeyboard);
		$("#game #overlay").on("click", ACTIONS.ListenerMouse);
		ACTIONS.listening = true;
		ACTIONS.actionsList = {};
		ACTIONS.lastAction = 0;
		ACTIONS.listenerID = setInterval(ACTIONS.Loop, ACTIONS.listenInterval);
	},
	
	End: function(){
		$(document).off("keydown");
		$("#game #overlay").off("click");
		ACTIONS.listening = false;
		clearInterval(ACTIONS.listenerID);
	},
	
	ListenerKeyboard: function(ev){
	
		//WALK
		if(ev.which === 65 || ev.which === 68 || ev.which === 83 || ev.which === 87 || (ev.which>36 && ev.which<41)){
			
			//LEFT
			if(ev.which === 65 || ev.which === 37)
				ACTIONS.actionsList.walk = "L";
			
			//RIGHT
			else if(ev.which === 68 || ev.which === 39)
				ACTIONS.actionsList.walk = "R";
			
			//UP
			else if(ev.which === 87 || ev.which === 38)
				ACTIONS.actionsList.walk = "U";
			
			//DOWN
			else if(ev.which === 83 || ev.which === 40)
				ACTIONS.actionsList.walk = "D";
			
		}
		
		//ATTACK
		else if(ev.which === 32){
			
			console.log("Attack");
			
			ACTIONS.actionsList.attack = "A";
		}
	
	},
	
	ListenerMouse: function(ev){
		
		ACTIONS.actionsList.attack = "A";
	
	},
	
	Loop: function(){
		
		if(!ACTIONS.listening)
			return;
		
		//Cooldown
		if(ACTIONS.lastAction > Date.now()){
			return;
		}
		
		//Send
		if('walk' in ACTIONS.actionsList || 'attack' in ACTIONS.actionsList){
			API.Post("Action/", ACTIONS.actionsList);
			ACTIONS.actionsList = {};
			ACTIONS.lastAction = Date.now() + (ACTIONS.cooldown);
		}
	},
	
};
