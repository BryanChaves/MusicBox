<?php
	
	//include 'conexionPostgree.php';// se incluye la conexion para poder acceder a la base de datos 
	//include 'URLS.PHP'; //Incluyo el modelo de urls el cual me va a permitir introducir  mediante este el urls	
	require_once __DIR__ . '/vendor/autoload.php';// se requiere cargar el archivo autoload
	use PhpAmqpLib\Connection\AMQPConnection;//se usa la libreria de php




	$connection = new AMQPConnection('localhost', 5672, 'guest', 'guest');//se establece la conexion

	$channel = $connection->channel();//se abre el canal

	$channel->queue_declare('hola', false, false, false, false);//se declara la cola

	echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";//se impreme un mensaje que si desea salir oprimir control c
	

	$callback = function($msg) {
		$ruta="/home/BRYAN19/MusicBox/Subidos";
		echo " [x] Received ", $msg->body, "\n";//se imprime el cuerpo del mensaje
		$informacionMensaje = json_decode($msg->body,true);
		$id=$informacionMensaje['id'];
		$file=$informacionMensaje['file'];
		$parts=$informacionMensaje['parts'];
		$time=$informacionMensaje['time_per_chunk'];
		
      
       $extension=end(explode(".", $file));
    
       
        $nombre=end(explode("/", $file));

        list($nombreCompleto) = split("[.]", $nombre);

       

        $nuevoNombre=limpia_espacios($nombreCompleto);
      

       $rutalimpia=$ruta.'/'.$nuevoNombre.".".$extension;
      
        $tiempo=exec('ffmpeg -i ' .$rutalimpia. ' 2>&1 |grep -oP "[0-9]{2}:[0-9]{2}:[0-9]{2}.[0-9]{2}"');
        
        list($horas,$minutos,$segundos,$microsegundos) = split("[:.]", $tiempo);
        
		$tamanoPartes=conversor_segundos(cantidadSegundos($parts,$horas,$minutos,$segundos));
		$valorHora=$tamanoPartes[0];
		$valorMinutos=$tamanoPartes[1];
		$valorSegundos=$tamanoPartes[2];
		$valorMicrosegundos=$tamanoPartes[3];       
        particiones($valorHora,$valorMinutos,$valorSegundos,$valorMicrosegundos,$parts);

	};

	$channel->basic_consume('hola', '', false, true, false, false, $callback);//Se le dice al canal que consuma hola y los mensajes si han sido recibidos

		
	function limpia_espacios($cadena){
    $cadena = str_replace(' ', '', $cadena);
    return $cadena;
	}

	function cantidadSegundos($parts,$horas,$minutos,$segundos){
   		$acumulado=0;
   		$acumulado=(($horas*3600)+($minutos*60)+$segundos)/$parts;
   	    return $acumulado;
	}
	
	function particiones($horas,$minutos,$segundos,$microsegundos,$parts){
   				
	}




	function conversor_segundos($seg_ini){
		$horas = floor($seg_ini/3600);
		$minutos = floor(($seg_ini-($horas*3600))/60);
		$segundos = $seg_ini-($horas*3600)-($minutos*60);
		list($segundos2,$microsegundos) = split("[.]", $segundos);
		return array($horas, $minutos,$segundos2,$microsegundos);

		
		
	}




	//Se crea un cliclo while en el cual pretendemos saber la cantidad de mensajes que hay en el calnal y si hay poner este en espera
	while(count($channel->callbacks)) {	
		$channel->wait();
	}

	$channel->close();//Se cierra el canal

	$connection->close();//Se cierra la conexion

	








/* public static function Insertar($id, $direccion)
    {
        $urls = URLS::findOrFail($id);
        $urls->urls = $direccion;
        $urls->save();
        return $urls;
    }
*/




?>