//############
//### USER ###
//############
var USER = {
	nick: "",
	color: "",
};



//###########
//### API ###
//###########
var API = {
	URL: "",
	URL_ONLINE: "",
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
		
		//CONNECT
		$.ajax({
			url: this.URL + url,
			method: 'GET',
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
		
		//CONNECT
		$.ajax({
			url: this.URL+url,
			method: 'POST',
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
		$sendButton.on("click", this.Login);
		
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
		
		$('#login').hide();
		USER.nick = $nick;
		API.Post("Login/", {nick:$nick}, AUTH.LoginCallback);
	},
	
	LoginCallback: function(response){
	
		if('User' in response && response.User==="LOGGED"){
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
window.onload = function(){
	AUTH.Init();
};



//############
//### GAME ###
//############
var GAME = {
	
	running: false,
	callerID: 0,
	updateInterval: 250,
	lastUpdate: 0,
	skipedPackages: 0,
	skipedPackagesTimeout: 20,
	
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
		
		$("#game").show();
	},
	
	End: function(){
		
		MAP.end();
		PLAYERS.end();
		
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
		if(!response || !('User' in response))
			return;
		
		//SESSION EXPIRE
		if(response.User===0){
			GAME.End();
			alert("Sessão expirada");
			return;
		}
		
		//PACKAGE TIME ORDER
		if(!('Time' in response) || GAME.lastUpdate > response.Time){
			
			console.log("Package Skiped ("+response.Time+")");
			GAME.skipedPackages++;
			
			//Lost Connection
			if(GAME.skipedPackages > GAME.skipedPackagesTimeout){
				alert("Server connection lost!");
				GAME.End();
			}
			return;
		}
		GAME.lastUpdate = response.Time;
		GAME.skipedPackages = 0;
		
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
	
	Init: function(){
		let $table = $("#map");
		$table.empty();
		
		MAP.refArray = [];
		for(let y=0; y<11; y++){
			MAP.refArray[y] = [];
			let tableRow = $("<tr>");
			$table.append(tableRow);
			
			for(let x=0; x<11; x++){
				MAP.refArray[y][x] = $("<td>");
				tableRow.append(MAP.refArray[y][x]);
			}
		}
	},
	
	Update(data){
		console.log(data);
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
		
		MAP.refArray = [];
		for(let y=0; y<11; y++){
			MAP.refArray[y] = [];
			let tableRow = $("<tr>");
			$table.append(tableRow);
			
			for(let x=0; x<11; x++){
				MAP.refArray[y][x] = $("<td>");
				tableRow.append(MAP.refArray[y][x]);
			}
		}
	},
	
};