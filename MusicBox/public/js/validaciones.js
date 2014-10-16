
$(document).on("click","#subir",function() {

  var origenArchivo  = $('#origenArchivo').get(0).files[0];
  var forma =  $('#Opcion').val();
  var cantidad = $('#cantidad').val();
  
  var formData   = new FormData();
  formData.append('origenArchivo',origenArchivo);
  formData.append('forma',forma);
  formData.append('cantidad',cantidad);

    $.ajax({
    type: "POST",
    url: "/",
      data: formData,
      cache: false,
      contentType: false,
      processData: false,
  }).done(function( data ) {
     
    location.href = '/';

    }).fail(function( data , error) {
    
    console.log(data);
    console.log(error);
  });



});

 