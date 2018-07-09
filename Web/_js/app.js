//############
//### USER ###
//############
var USER = {
	token: "",
	id: 0,
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
	
	Get: function(url, callbackFunction, sendToken=true){
		
		//RESPONSE
		let response = {
			connected: false,
			error: false,
			data: false,
		};
		
		//HEADER
		var header = {};
		if(sendToken) header.AuthToken = USER.token;
		
		//CONNECT
		$.ajax({
			url: this.URL + url,
			method: 'GET',
			headers: header,
			timeout: 500,
			success: function (data, textStatus, jqXHR) {
				response.connected = true;
				response = API.ParseData(data);
			},
			statusCode: { 401: function(jqXHR, textStatus, errorThrown) {
				response.connected = true;
				response = API.ParseData(jqXHR.responseText);
			}},
			error: function(jqXHR, textStatus, errorThrown){
				response.connected = false;
				response.error     = "Error connecting to server!";
			},
			complete: function (jqXHR, textStatus){
				if(typeof callbackFunction === "function") callbackFunction(response);
			}
		});
	
	},
	
	Post: function(url, data, callbackFunction, sendToken=true){
		
		//RESPONSE
		let response = {
			connected: false,
			error: false,
			data: false,
		};
		
		//HEADER
		var header = {};
		if(sendToken) header.AuthToken = USER.token;
		
		
		//CONNECT
		$.ajax({
			url: this.URL+url,
			method: 'POST',
			headers: header,
			dataType: 'json',
			data: data,
			timeout: 500,
			success: function (data, textStatus, jqXHR) {
				response.connected = true;
				response = API.ParseData(data);
			},
			statusCode: { 401: function(jqXHR, textStatus, errorThrown) {
				response.connected = true;
				response = API.ParseData(jqXHR.responseText);
			}},
			error: function(jqXHR, textStatus, errorThrown){
				response.connected = false;
				response.error     = "Error connecting to server!";
			},
			complete: function (jqXHR, textStatus){
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
		
	},
	
	Login: function(){
		
		let $nick = $("#login input");
		
		if($nick.val()===""){
			alert("Please enter a nick name!");
			return;
		}
		
		$("#login").hide();
		API.Post("Login/", {nick:$nick.val()}, this.LoginCallback);
	},
	
	LoginCallback: function(response){
	
		console.log(response);
	
	},
	
};
AUTH.Init();
