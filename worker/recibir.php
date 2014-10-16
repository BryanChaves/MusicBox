<?php
	
	require_once __DIR__ . '/vendor/autoload.php';// se requiere cargar el archivo autoload
	
	use PhpAmqpLib\Connection\AMQPConnection;//se usa la libreria de php

	$connection = new AMQPConnection('localhost', 5672, 'guest', 'guest');//se establece la conexion

	$channel = $connection->channel();//se abre el canal

	$channel->queue_declare('hola', false, false, false, false);//se declara la cola

	echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";//se impreme un mensaje que si desea salir oprimir control c


	$callback = function($msg) {
		echo " [x] Received ", $msg->body, "\n";//se imprime el cuerpo del mensaje
	};

	$channel->basic_consume('hola', '', false, true, false, false, $callback);//Se le dice al canal que consuma hola y los mensajes si han sido recibidos

	//Se crea un cliclo while en el cual pretendemos saber la cantidad de mensajes que hay en el calnal y si hay poner este en espera
	while(count($channel->callbacks)) {	
		$channel->wait();
	}

	$channel->close();//Se cierra el canal

	$connection->close();//Se cierra la conexion
?>