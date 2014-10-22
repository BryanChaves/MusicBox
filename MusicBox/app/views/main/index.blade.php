<form action="" method="post" enctype="multipart/form-data">

<input type="file" name="mp3" id="origenArchivo" accept="audio/*" >

<label id="etiqueta1" for="Parts" class="label label-primary">Select how you want to split your file:</label><br>

<select id="Opcion">
  <option value="parts">Parts</option>
  <option value="minutes">Minutes</option>
  
 </select><br>
<input id="cantidad" type="number" value="0" min="0" max="60" /><br>


<input type="button" value="Upload" id="subir">

</form>

