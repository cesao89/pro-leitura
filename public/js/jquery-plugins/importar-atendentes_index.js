jQuery.importarAtendentesIndex = function(buscarGruposUrl) {

    // INTECEPTA EVENTO DE MUDANÇA NO SELECT DE EMPRESAS
    $('#empresa').change(function() {
        $.ajax({
            type: 'POST',
            url: buscarGruposUrl,
            data: {workplaceId: $('#empresa').val()},
            success: function(res) {
                $('#grupo').html(res);
            }
        });
    });
};