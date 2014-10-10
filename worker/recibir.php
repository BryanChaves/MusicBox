<?php
	
	require_once __DIR__ . '/vendor/autoload.php';
	
	use PhpAmqpLib\Connection\AMQPConnection;

	$connection = new AMQPConnection('localhost', 5672, 'guest', 'guest');

	$channel = $connection->channel();

	$channel->queue_declare('hola', false, false, false, false);

	echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";


	$callback = function($msg) {
		echo " [x] Received ", $msg->body, "\n";
	};

	$channel->basic_consume('hola', '', false, true, false, false, $callback);//Se le dice al canal que consuma hola y los mensajes si han sido recibidos

	//Se crea un cliclo while en el cual pretendemos saber la cantidad de mensajes que hay en el calnal y si hay poner este en espera
	while(count($channel->callbacks)) {	
		$channel->wait();
	}

	$channel->close();//Se cierra el canal

	$connection->close();//Se cierra la conexion
?>