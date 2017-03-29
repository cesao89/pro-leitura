function details(i){
    if(i==undefined && i==''){
        alert('Falha inesperada, operação cancelada')
    }

    processModal('show', 'Buscando histórico da venda, aguarde');

    $('#historicoVenda').load(baseUrl + '/consulta/historico-venda/i/' + i, function(){
        processModal('hide');

        $('#historicoVenda').fadeIn(1000);
        $('html,body').animate({scrollTop: $('#historicoVenda').offset().top}, 1500);

        if($('#historicoVenda').html()==''){

        }
    })
}

function logatendimento(i){
    if(i==undefined && i==''){
        alert('Falha inesperada, operação cancelada');
    }

    processModal('show', 'Buscando logs do atendimento, aguarde');

    $('#logAtendimento').load(baseUrl + '/consulta/log-atendimento/i/' + i, function(){
        processModal('hide');

        $('#logAtendimento').fadeIn(1000);
        $('html,body').animate({scrollTop: $('#logAtendimento').offset().top}, 1500);

        if($('#logAtendimento').html()==''){

        }
    });
}

function closeHistory(){
    $('html,body').animate({scrollTop: $('body').offset().top}, 1000);
    $('#historicoVenda').fadeOut(1000);
}

function closeLogAtendimento(){
    $('html,body').animate({scrollTop: $('body').offset().top}, 1000);
    $('#logAtendimento').fadeOut(1000);
}

/** Devolucao de valores INICIO */
function devolucao(id_sale, id_bid, amount, bidName, productGroup, uid, id_client, daysGone, dataAdesao){
    var obj = ajaxUID(uid);

    $("#tabs").tabs("select", "#tabs-7");
    
    $('#box-lista-devolucao').css('display', 'none');
    $('#form-nova-devolucao').css('display', 'block');
    
    $('#form-devolucao-premios-cadastro #fk_id_bid').val(id_bid +"_"+ bidName +"_"+ amount +"_"+ id_sale);
    $('#form-devolucao-premios-cadastro #viewProductName').val(productGroup);
    
    $('#form-nova-devolucao span#infoType').text(obj['accountType']);
    $('#form-devolucao-premios-cadastro #accountType').val(obj['accountType']);
    $('#form-nova-devolucao span#infoParcelas').text(obj['parcelasPagas']);
    $('#form-devolucao-premios-cadastro #parcelasPagas').val(obj['parcelasPagas']);
    
    $('#form-devolucao-premios-cadastro #dt_adesao').val(dataAdesao);
    $('#form-devolucao-premios-cadastro #saleUid').val(uid);
    
    $('#form-devolucao-premios-cadastro #daysGone').val(daysGone);
    
    if(obj['accountType'] == 'Pré'){
        $('#form-devolucao-premios-cadastro #enm_tipo_devolucao').text('');
        $('#form-devolucao-premios-cadastro #enm_tipo_devolucao').append('<option value="recarga" selected="selected">Recarga</option>');
    } else if(obj['accountType'] == 'Pós'){
        $('#form-devolucao-premios-cadastro #enm_tipo_devolucao').text('');
        $('#form-devolucao-premios-cadastro #enm_tipo_devolucao').append('<option value="">Selecione</option>');
        $('#form-devolucao-premios-cadastro #enm_tipo_devolucao').append('<option value="debito-conta">Crédito em conta corrente</option>');
        $('#form-devolucao-premios-cadastro #enm_tipo_devolucao').append('<option value="conta-poupanca">Crédito em conta poupança</option>');
        $('#form-devolucao-premios-cadastro #enm_tipo_devolucao').append('<option value="caixa-postal">Vale Postal</option>');
    }
    
    if(daysGone <= 7){
        $('#form-devolucao-premios-cadastro #nm_parcelas').text('');
        $('#form-devolucao-premios-cadastro #nm_parcelas').append('<option value="1" selected="selected">1 Parcela</option>');
        
        $('#form-devolucao-premios-cadastro #enm_imc').attr('disabled');
        $('#form-devolucao-premios-cadastro #icms_hidden').attr('<input type="hidden" name="enm_icms" value="1">');
    } else {
        if(obj['parcelasPagas'] == 1){
            $('#form-devolucao-premios-cadastro #nm_parcelas').text('');
            $('#form-devolucao-premios-cadastro #nm_parcelas').append('<option value="1" selected="selected">1 Parcela</option>');
        } else if(obj['parcelasPagas'] == 2){
            $('#form-devolucao-premios-cadastro #nm_parcelas').text('');
            $('#form-devolucao-premios-cadastro #nm_parcelas').append('<option>Selecione</option>');
            $('#form-devolucao-premios-cadastro #nm_parcelas').append('<option value="1">1 Parcela</option>');
            $('#form-devolucao-premios-cadastro #nm_parcelas').append('<option value="2">2 Parcela</option>');
        } else if(obj['parcelasPagas'] >= 3){
            $('#form-devolucao-premios-cadastro #nm_parcelas').text('');
            $('#form-devolucao-premios-cadastro #nm_parcelas').append('<option>Selecione</option>');
            $('#form-devolucao-premios-cadastro #nm_parcelas').append('<option value="1">1 Parcela</option>');
            $('#form-devolucao-premios-cadastro #nm_parcelas').append('<option value="2">2 Parcela</option>');
            $('#form-devolucao-premios-cadastro #nm_parcelas').append('<option value="3">3 Parcela</option>');
        }
    }
}

function ajaxUID(uid){
    /*
     * 
     * Return: accountType & parcelasPagas
     */
    var myObj = '';
    $.ajax({
        type: 'POST',
        url: baseUrl + '/consulta/check-uid',
        async: false, // ! sincrono !
        data: { uid: uid },
        success: function(res) {
            myObj = $.parseJSON(res);
        }
    });
    
    return myObj
}
/** Devolucao de valores FIM */
