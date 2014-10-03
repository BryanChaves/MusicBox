{{ Form::open(array(
     'url'=>'upload/', 
     'method' => 'post',
     'enctype'=>'multipart/form-data'//con esto permite subir nuestro archivo al servidor
) )}}

{{ Form::file('archivo') }}
<label id="etiqueta1" for="Parts" class="label label-primary">Parts </label><input id="parts" type="number" value="0" min="0" max="99" /> <br>
<label id="etiqueta2" for="Minutes" class="label label-primary">Minutes </label><input id="minutes" type="number" value="0" min="0" max="99" /> <br>
{{ Form::submit('Upload') }}

{{ Form::close()}}
