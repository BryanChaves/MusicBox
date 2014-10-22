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
		$ruta="../Subidos/";	
		echo " [x] Received ", $msg->body, "\n";//se imprime el cuerpo del mensaje
		$informacionMensaje = json_decode($msg->body,true);//aca se obtiene la informacion que trae la cola que es formato jason
		//se procede a separarlos la informacion que trae el json en sus repectivas variables
		$id=$informacionMensaje['id'];
		$file=$informacionMensaje['file'];
		$parts=$informacionMensaje['parts'];
		$time=$informacionMensaje['time_per_chunk'];
	
       	$extension=end(explode(".", $file));//guarda la extension que trae el archivo mediante el explode
        $nombre=end(explode("/", $file));

        list($nombreCompleto) = split("[.]", $nombre);//Con el list obtiene el nombre del audio
        $nuevoNombre=$nombreCompleto.".".$extension;  
        //obtiene el tiempo de duracion del archivo de audio mediante el metadata	
        $tiempo=exec('ffmpeg -i ' . $ruta . $nuevoNombre . ' 2>&1 |grep -oP "[0-9]{2}:[0-9]{2}:[0-9]{2}.[0-9]{2}"');
        //separa en variables el archivo de tiempo (hora,minutos,segundos y microsegundos)

        list($horas,$minutos,$segundos,$microsegundos) = split("[:.]", $tiempo);      
        /*Se invoca una funcion dentro de otra para obtener duracion en segundos 
        la otra la toma y dice cuando debe ser partida cada parte*/
        if ($parts!=0) {
        $tamanoPartes=conversor_segundos(cantidadSegundos($parts,$horas,$minutos,$segundos));
		//El metodo devuelve un arreglo y se saca los valores que vienen en el
		$valorHora=$tamanoPartes[0];
		$valorMinutos=$tamanoPartes[1];
		$valorSegundos=$tamanoPartes[2];
		$valorMicrosegundos=$tamanoPartes[3]; 
		//Se invoca otra funcion encarda de procesar o separar el audio en sus partes
        particiones($valorHora,$valorMinutos,$valorSegundos,$valorMicrosegundos,$parts,$nombreCompleto,$extension);
        }
        elseif($horas>0){
				$segundos3=($time*60);
				$horas3=($horas*3600);

				$cantidadPartes2=floor($horas3/$segundos3);

       $tamanoPartes=conversor_segundos($segundos3);
		//El metodo devuelve un arreglo y se saca los valores que vienen en el
		$valorHora=$tamanoPartes[0];
		$valorMinutos=$tamanoPartes[1];
		$valorSegundos=$tamanoPartes[2];
		$valorMicrosegundos=$tamanoPartes[3];

		//Se invoca otra funcion encarda de procesar o separar el audio en sus partes
        particiones3($valorHora,$valorMinutos,$valorSegundos,$valorMicrosegundos,$cantidadPartes2,$nombreCompleto,$extension,$horas,$minutos,$segundos);        	

        	
        }else{
        	$tiempoArchivo=cantidadSegundos2($horas,$minutos,$segundos);
        	$tiempoTime=($time*60);
        	$cantidadPartes=floor($tiempoArchivo/$tiempoTime);
        	$tamanoPartes=conversor_segundos($tiempoTime);

        $valorHora=$tamanoPartes[0];
		$valorMinutos=$tamanoPartes[1];
		$valorSegundos=$tamanoPartes[2];
		$valorMicrosegundos=$tamanoPartes[3];
		particiones2($valorHora,$valorMinutos,$valorSegundos,$valorMicrosegundos,$cantidadPartes,$nombreCompleto,$extension,$minutos,$segundos);
        }


		
	};
		$channel->basic_consume('hola', '', false, true, false, false, $callback);//Se le dice al canal que consuma hola y los mensajes si han sido recibidos
		//funcion encargada de tomar un tiempo y convertirlo todo en segundos

	function cantidadSegundos($parts,$horas,$minutos,$segundos){
   		$acumulado=0;
   		$acumulado=(($horas*3600)+($minutos*60)+$segundos)/$parts;
   	    return $acumulado;
	}
	function cantidadSegundos2($horas,$minutos,$segundos){
   		$acumulado=0;
   		$acumulado=(($horas*3600)+($minutos*60)+$segundos);
   	    return $acumulado;
	}


function conversorMinutos($horas,$minutos){
		$acumulado=0;
   		$acumulado=($horas*60)+$minutos;
   	    return $acumulado;
}
function pasarTiempos($tiempo,$cont){
	$tiempo=$tiempo*($cont-1);
	$nuevoTiempo2="00".":".$tiempo.":"."00";
    return $nuevoTiempo2;
 	
}



	//funcion encargada de partir mi audio en la cantidad digitada por el usuario
	function particiones($horas,$minutos,$segundos,$microsegundos,$parts,$nombreCompleto,$extension){
		$nombreExtension=$nombreCompleto."." .$extension;//aca tiene el nombre con su extension correspondiente
		$ruta="../Subidos/";//aca se indiga la ruta donde se encuentran los archivos de que va a tomar para procesarlos
		$descargas="../Descargas/";//aca indico la ruta donde deseo que me guarde esos archivos ya procesados osea partidos
		$tiempo1="00:00:00";//esta es una variable la cual cumple con el inicio de las particiones del archivo
		$tiempo2=$time = $horas.":".$minutos.":".$segundos;
			for ($i=1; $i <=$parts ; $i++) { 
				if ($i==1) {
				$time = $horas.":".$minutos.":".$segundos;
				exec("ffmpeg -i"." ".$ruta.$nombreExtension ." ". "-acodec copy -t " . $time ." -ss"." ".$tiempo1." ".$descargas.$nombreCompleto."_parte_".$i.".".$extension);
				}if ($i==2) {		
					exec("ffmpeg -i"." ".$ruta.$nombreExtension ." ". "-acodec copy -t " . $time ." -ss"." ".$tiempo2." ".$descargas.$nombreCompleto."_parte_".$i.".".$extension);
				}if ($i>2) {	
					$nuevoTiempo=tamanoPartes($tiempo2,$i);
					$valorHora=$nuevoTiempo[0];
				$valorMinutos=$nuevoTiempo[1];
				$valorSegundos=$nuevoTiempo[2];
				$tiempo3=$valorHora.":".$valorMinutos.":".$valorSegundos;
				exec("ffmpeg -i"." ".$ruta.$nombreExtension ." ". "-acodec copy -t " . $time ." -ss"." ".$tiempo3." ".$descargas.$nombreCompleto."_parte_".$i.".".$extension);
				}		
			}
}


	function particiones2($horas,$minutos,$segundos,$microsegundos,$parts,$nombreCompleto,$extension,$minutos2,$segundos2){
		$nombreExtension=$nombreCompleto."." .$extension;//aca tiene el nombre con su extension correspondiente
		$ruta="../Subidos/";//aca se indiga la ruta donde se encuentran los archivos de que va a tomar para procesarlos
		$descargas="../Descargas/";//aca indico la ruta donde deseo que me guarde esos archivos ya procesados osea partidos
		$tiempo1="00:00:00";//esta es una variable la cual cumple con el inicio de las particiones del archivo
		$tiempo2=$time = $horas.":".$minutos.":".$segundos;
			for ($i=1; $i <=$parts ; $i++) { 
				if ($i==1) {
				$time = $horas.":".$minutos.":".$segundos;
				exec("ffmpeg -i"." ".$ruta.$nombreExtension ." ". "-acodec copy -t " . $time ." -ss"." ".$tiempo1." ".$descargas.$nombreCompleto."_tiempo_".$i.".".$extension);
				}if ($i==2) {		
					exec("ffmpeg -i"." ".$ruta.$nombreExtension ." ". "-acodec copy -t " . $time ." -ss"." ".$tiempo2." ".$descargas.$nombreCompleto."_tiempo_".$i.".".$extension);
				}if ($i>2) {	
					$nuevoTiempo=tamanoPartes($tiempo2,$i);
					$valorHora=$nuevoTiempo[0];
				$valorMinutos=$nuevoTiempo[1];
				$valorSegundos=$nuevoTiempo[2];
				$tiempo3=$valorHora.":".$valorMinutos.":".$valorSegundos;
				exec("ffmpeg -i"." ".$ruta.$nombreExtension ." ". "-acodec copy -t " . $time ." -ss"." ".$tiempo3." ".$descargas.$nombreCompleto."_tiempo_".$i.".".$extension);
				}		
			}
			$tiempo4="00".":".$minutos2.":"."00";
			$tiempo5="00".":"."00".":".$segundos2;
			$parts=$parts+1;
			exec("ffmpeg -i"." ".$ruta.$nombreExtension ." ". "-acodec copy -t " . $tiempo5 ." -ss"." ".$tiempo4." ".$descargas.$nombreCompleto."_tiempo_".$parts.".".$extension);
}

	function particiones3($horas,$minutos,$segundos,$microsegundos,$parts,$nombreCompleto,$extension,$horas3,$minutos3,$segundos3){
		$nombreExtension=$nombreCompleto."." .$extension;//aca tiene el nombre con su extension correspondiente
		$ruta="../Subidos/";//aca se indiga la ruta donde se encuentran los archivos de que va a tomar para procesarlos
		$descargas="../Descargas/";//aca indico la ruta donde deseo que me guarde esos archivos ya procesados osea partidos
		$tiempo1="00:00:00";//esta es una variable la cual cumple con el inicio de las particiones del archivo
		$tiempo2=$time = $horas.":".$minutos.":".$segundos;
			for ($i=1; $i <=$parts ; $i++) { 
				if ($i==1) {
				$time = $horas.":".$minutos.":".$segundos;
				exec("ffmpeg -i"." ".$ruta.$nombreExtension ." ". "-acodec copy -t " . $time ." -ss"." ".$tiempo1." ".$descargas.$nombreCompleto."_tiempo_".$i.".".$extension);
				}if ($i==2) {		
					exec("ffmpeg -i"." ".$ruta.$nombreExtension ." ". "-acodec copy -t " . $time ." -ss"." ".$tiempo2." ".$descargas.$nombreCompleto."_tiempo_".$i.".".$extension);
				}if ($i>2) {	
					$nuevoTiempo=tamanoPartes($tiempo2,$i);
					$valorHora=$nuevoTiempo[0];
				$valorMinutos=$nuevoTiempo[1];
				$valorSegundos=$nuevoTiempo[2];
				$tiempo3=$valorHora.":".$valorMinutos.":".$valorSegundos;
				exec("ffmpeg -i"." ".$ruta.$nombreExtension ." ". "-acodec copy -t " . $time ." -ss"." ".$tiempo3." ".$descargas.$nombreCompleto."_tiempo_".$i.".".$extension);
				}		
			}
			
}

	
	function tamanoPartes($tiempo,$cont){
		 list($horas,$minutos,$segundos) = split("[:]", $tiempo);
		 $segundos2=$segundos*($cont-1);
		 $minutos2=$minutos*($cont-1);
		 $horas2=$horas;
		 if ($segundos2>59) {
		 			$tiempo=conversor_segundos($segundos2);
		 			$valorHora=$tiempo[0];
					$valorMinutos=$tiempo[1];
					$valorSegundos=$tiempo[2];
		 			return array($valorHora,$valorMinutos,$valorSegundos);
		 		}
		 		 if ($minutos2>59) {
		 			$tiempo=conversor_segundos($minutos2);
		 			$valorHora=$tiempo[0];
					$valorMinutos=$tiempo[1];
					$valorSegundos=$tiempo[2];
		 			return array($valorHora,$valorMinutos,$valorSegundos);
		 		}

		return array($horas2, $minutos2,$segundos2);
	}
	//Funcion para convertir segundos
	function conversor_segundos($seg_ini){
		$horas = floor($seg_ini/3600);//El floor redondea la cantidad hacia abajo 
		$minutos = floor(($seg_ini-($horas*3600))/60);
		$segundos = $seg_ini-($horas*3600)-($minutos*60);
		list($segundos2,$microsegundos) = split("[.]", $segundos);
		return array($horas, $minutos,$segundos2,$microsegundos);	
	}

		while(count($channel->callbacks)) {	
			$channel->wait();
		}
		$channel->close();//Se cierra el canal
		$connection->close();
	//	$dbconn = pg_connect("host=localhost dbname=musicBox user=postgres password=12345")
	//	pg_close($dbconn);

		//Se cierra la conexion

/* public static function Insertar($id, $direccion)
    {
        $urls = URLS::findOrFail($id);
        $urls->urls = $direccion;
        $urls->save();
        return $urls;
    }
*/
?>