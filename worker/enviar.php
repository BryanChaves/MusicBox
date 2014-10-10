<?php
	
	require_once __DIR__ . '/vendor/autoload.php';//se le indica que require utilizar el archivo autoload que se encuentra en al carpeta vendor
	
	use PhpAmqpLib\Connection\AMQPConnection; //aca pide hacer uso de la libreria de php llamada Amqp la cual utiliza de conexion entre el servidor de colas y php
	use PhpAmqpLib\Message\AMQPMessage;//aca de igual forma usa esta libreria para hacer uso de sus mensajes


	$connection = new AMQPConnection('localhost', 5672, 'guest', 'guest');//Se establece la conecxion con el servidor indicandole que va a correr ennun servidor local y que ademas su puerto va hacer el y su usuario y contrasena es guest para ambos casos

 	$channel = $connection->channel();//se estable  la coneccion con el canal por el cual se va a transmitir

	$channel->queue_declare('hola', false, false, false, false);//se declara la cola dentro del canal

	$msg = new AMQPMessage('Hola Mundo!');//

	$channel->basic_publish($msg, '', 'hola');//SE publica en mensaje en el canal

	echo " [x] Sent 'Hola Mundo!'\n";

	$channel->close();//se procede al cerrar el canal luego de enviar el mensaje

	$connection->close();//se cierra de igual forma la conexion
?>