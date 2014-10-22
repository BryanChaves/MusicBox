<?php

require_once( '/home/BRYAN19/MusicBox/worker/vendor/autoload.php');

use PhpAmqpLib\Connection\AMQPConnection; 
use PhpAmqpLib\Message\AMQPMessage;


class MainController extends \BaseController {

	
	protected $layout = 'layouts.default';

	public function index()
	{
		
		 $this->layout->content = View::make('main.index');
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}


	/**k
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{

		$archivo = Input::file('origenArchivo');
 		$forma  = Input::get('forma');
 		$cantidad = Input::get('cantidad');
 		$rutaArchivo_subido="/home/BRYAN19/MusicBox/Subidos";
 		
 		$nombre = $archivo->getClientOriginalName();
 		$nuevoNombre=$this->limpia_espacios($nombre);

 		//$informacionArchivo = pathinfo($nombre);
		//$extension  = $informacionArchivo['extension']; 		
 		
 		$rutaCompleta =$rutaArchivo_subido.'/'.$nuevoNombre;

 		$cola = new Cola();	

//if (($extension == "mp3")||($extension == "wav")||($extension == "ogg")||($extension == "wma")||($extension == "mka")) {
	$subido = $archivo->move($rutaArchivo_subido, $nuevoNombre);

		 		if ($forma=="parts") {
		 			$cola->file = $rutaCompleta;
					$cola->parts = $cantidad;
					$cola->time_per_chunk = "";
					$cola->save();
					$id=$cola->id;
					$file=$rutaCompleta;
					$parts=$cantidad;
					$time_per_chunk="";


					$formatoJason=array('id'=>"$id",'file'=>"$file",'parts'=>"$parts",'time_per_chunk'=>"$time_per_chunk");
					$this->cola(json_encode($formatoJason));
					return Redirect::to('/');
		 		}else{
		 			if ($forma=="minutes") {
		 				
		 				$cola->file = $rutaCompleta;
						$cola->parts = 0;
						$cola->time_per_chunk = $cantidad;
						$cola->save();

						$id=$cola->id;
						$file=$rutaCompleta;
						$parts=0;
						$time_per_chunk=$cantidad;

						$formatoJason=array('id'=>"$id",'file'=>"$file",'parts'=>"$parts",'time_per_chunk'=>"$time_per_chunk");
						$this->cola(json_encode($formatoJason));
						
						return Redirect::to('/');			
		 			}
		 		}
 			
 		//}else{
 			//return Response::json(s"formato invalido");
 		//}

	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function limpia_espacios($cadena){
    $cadena = str_replace(' ', '', $cadena);
    return $cadena;
	}


	public function show($id)
	{
		//
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

	public function cola($cola){
		$connection = new AMQPConnection('localhost', 5672, 'guest', 'guest');
		$channel = $connection->channel();
		$channel->queue_declare('hola', false, false, false, false);
		
		$msg = new AMQPMessage($cola);
		$channel->basic_publish($msg, '', 'hola');
		$channel->close();
		$connection->close();
	}



}
