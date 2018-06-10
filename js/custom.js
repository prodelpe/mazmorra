/* 
 * Funciones propias
 */

function addBiblio(userId, bookId) {

    //Em de recollir els valors amb FormData
    //I identificar cada dada que passem amb un nom de paràmetre
    //En aquest cas només teniem un
    var formData = new FormData();
    formData.append('userId', userId);
    formData.append('bookId', bookId);

    $.ajax({
        // la URL para la petición
        url: 'action/action-add.php',

        // la información a enviar
        // (también es posible utilizar una cadena de datos)
        data: formData,
        //dataType: 'json',
        //async: false,

        // especifica si será una petición POST o GET
        type: 'POST',

        // el tipo de información que se espera de respuesta
        // Normalmente seria: dataType: 'json'
        // Como no hay tipo, ponemos esto
        processData: false,
        contentType: false,

        //Posar un carregador mentre s'envia la info
        beforeSend: function () {
            $('#boton').html('<div class="loader"></div>');
        },

        // código a ejecutar si la petición es satisfactoria;
        // la respuesta es pasada como argumento a la función
        success: function (data) {
            //data = $.parseJSON(data);
            //console.log(data);
            //TODO - No se sap quan l'ha afegit o ja estava repetit
            $('#boton').empty();
            if (data.wasInFavorites) {
                $('#boton').html('<button type="button" class="btn btn-danger">Ya tienes este libro en Favoritos</button>');
            } else {
                $('#boton').html('<button type="button" class="btn btn-success">Añadido a Favoritos</button>');
            }

        },

        // código a ejecutar si la petición falla;
        // son pasados como argumentos a la función
        // el objeto de la petición en crudo y código de estatus de la petición
        error: function (jqXHR, exception) {
            var msg = '';
            if (jqXHR.status === 0) {
                msg = 'Not connect.\n Verify Network.';
            } else if (jqXHR.status == 404) {
                msg = 'Requested page not found. [404]';
            } else if (jqXHR.status == 500) {
                msg = 'Internal Server Error [500].';
            } else if (exception === 'parsererror') {
                msg = 'Requested JSON parse failed.';
            } else if (exception === 'timeout') {
                msg = 'Time out error.';
            } else if (exception === 'abort') {
                msg = 'Ajax request aborted.';
            } else {
                msg = 'Uncaught Error.\n' + jqXHR.responseText;
            }
            alert(msg);
        },

        // código a ejecutar sin importar si la petición falló o no
        complete: function () {
            //alert('Petición realizada');
        }
    });

}

//Puntuació estrelles
$(".fa-star").on('click', function () {
    var limit = $(this).attr('data-val');

    $('#stars').children('span').each(function () {
        if ($(this).attr('data-val') <= limit){
            $(this).addClass('checked');
        } else {
            $(this).removeClass('checked');
        }
    });
    
    $("#rating").attr('value', limit);
});

$("#columnasHome .btn, #buscarBtn").on('click', function () {
    alert("Not implemented yet");
});

$("#orden").on('change', function () {
    var action = $(this).find(":selected").val();
    window.location.replace("catalog.php?action=" + action);
});