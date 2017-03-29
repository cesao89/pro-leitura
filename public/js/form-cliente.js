$(function() {
    $("#mobile").mask("(99) 99999-9999");
    $("#phone").mask("(99) 9999-9999");
    $("#phone2").mask("(99) 9999-9999");
    $("#fax").mask("(99) 9999-9999");
    $("#idNumber").mask("999.999.999-99");
    $("#zipCode").mask("99999-999");
    $("#birthDate").mask("99/99/9999");
    $("#brazilianGeneralRegistration").mask("99.999.999-*");
    $("#confPhone").mask("(99) 9999-9999");

    var lastZipCodeChange = $('#zipCode').val();
    var lastIdNumberChange = $('#idNumber').val();

    $('#zipCode').bind('keyup', function () {
        // Caso o CEP não esteja nesse formato ele é inválido!
        var objER = /^[0-9]{5}-[0-9]{3}$/;

        strCEP = Trim($('#zipCode').val());
        if(strCEP.length > 0){
            if(objER.test(strCEP) && lastZipCodeChange != strCEP){
                $('#h3Modal').html('Pesquisando CEP do cliente');
                $('#processAjax').modal('show');
                $.getJSON(
                    baseUrl + "/cliente/busca-cep/cep/" + strCEP,
                    function(json){
                        $('#address').val(json.address);
                        $('#neighborhood').val(json.neighborhood);
                        $('#city').val(json.city);
                        $('#stateUF').val(json.state);
                        $('#processAjax').modal('hide');
                        lastZipCodeChange = $('#zipCode').val();

                        if($('#address').val()!=undefined && $('#address').val()!=''){
                            $('#number').focus();
                        }
                    }
                );
            }
        } else {
            return false;
        }
    });

    $('#idNumber').bind('keyup', function () {
        var objER = /^[0-9]{3}.[0-9]{3}.[0-9]{3}-[0-9]{2}$/;
        strIdNumber = $('#idNumber').val().trim();

        if(strIdNumber.length > 0) {
            if(objER.test(strIdNumber) && lastIdNumberChange != strIdNumber){
                if (TestaCPF(strIdNumber)){
                    return true;
                } else {
                    $('#idNumber').val("");
                }
            }
        } else {
            return false;
        }
    });

    $('#submitButton').bind('click', function () {
        processModal('show', 'Salvando informações do cliente');
    });

    $('#cancelButton').bind('click', function () {
        processModal('show')
    });

    $('#email').bind('blur', function () {
        var campo_email = $('#email').val();

        if(campo_email != "") {
            if(!(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(campo_email))) {
                $('#email').val('');
                resultadoFalhaEmail();
            }
        }
    });
});