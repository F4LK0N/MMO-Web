<?php

//OFFLINE
if($_SERVER['SERVER_ADDR'] === "127.0.0.1" || $_SERVER['SERVER_ADDR'] === "127.0.0.1:80" || $_SERVER['SERVER_ADDR'] === "127.0.0.1:8080" || $_SERVER['SERVER_ADDR'] === "127.0.0.1"){
	header("Location: http://127.0.0.1/MMO/Web");
}

//ONLINE
else{
	header("Location: /Web");
}
