jQuery.consultaHistorico = function(baseUrl) {

    $("#nm_cpf").mask("999.999.999-99");
    $("#str_phone").mask("(99) 9999-9999");

    $('#item-devolucao').click(function() {
        $('#box-lista-devolucao').css('display', 'block');
        $('#form-nova-devolucao').css('display', 'none');
    });

    /** intercepta evento de clique no botão e implementa ação */
    $('#btn-devolucao-novo').click(function(e) {
        var elemento = $(e.currentTarget);
        if (elemento.data('protocolo')) {
            processModal('show', 'Gerando protocolo de atendimento ...');
            $.ajax({
                type: 'POST',
                data: {
                    openAttendanceName: elemento.data('clientname'),
                    openAttendanceClientId: elemento.data('clientid')
                },
                url: baseUrl + '/protocol/abrir-ajax-req',
                success: function(res) {
                    var res2 = res.split(":");
                    if (res2[0] == "false") {
                        $('.container-fluid').prepend('<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">x</button>' + res2[1] + '</div>');
                    } else if(res2[0] == "true"){
                        window.location = baseUrl + '/consulta/historico/i/'+ res2[1] +'/novaDevolucao/1#tabs-7';
                    } else {
                        alert('Erro, contate o administrador.');
                    }
                }
            });
        } else {
            $('#box-lista-devolucao').css('display', 'none');
            $('#form-nova-devolucao').css('display', 'block');
        }
    });

    /** intercepta evento de clique no botão de visualizar */
    $('.client-devolucoes').click(function(e) {
        processModal('show', 'Buscando informações ...');
        var devolucao = $(e.currentTarget);
        $.ajax({
            type: 'POST',
            url: baseUrl + '/devolucao-premios/detalhes-devolucao',
            data: {
                id: devolucao.data('devolucao')
            },
            success: function(res) {
                $('#detalhesDevolucao').html(res);
                $('.btn-devolucao-close').click(function() {
                    $('#detalhesDevolucao').html('');
                });
                processModal('hide');
            }
        });
        $('#detalhesDevolucao').fadeIn(1000);
        $('html,body').animate({scrollTop: $('#detalhesDevolucao').offset().top}, 1500);
    });
    
    /** remove todos os atributos required */
    function removeRequired() {
        $('#fk_id_banco').removeAttr('required');
        $('#nm_agencia').removeAttr('required');
        $('#nm_conta_corrente').removeAttr('required');
        $('#str_conta_corrente_digito').removeAttr('required');
        $('#str_titular').removeAttr('required');
        $('#nm_cpf').removeAttr('required');
        $('#fk_id_bid').removeAttr('required');
        $('#str_rg').removeAttr('required');
        $('#nm_cep').removeAttr('required');
        $('#str_endereco').removeAttr('required');
        $('#str_numero').removeAttr('required');
        $('#str_cidade').removeAttr('required');
        $('#str_estado').removeAttr('required');
        $('#str_bairro').removeAttr('required');
        $('#str_phone').removeAttr('required');
    }
    
    /* 
     * Banco, Agencia, Conta Corrente, Conta Corrente Digito, 
     * Titula, CPF, ID Bid  
     */
    function setUpDebitoConta() {
        $('#fk_id_banco').prop('required', true);
        $('#nm_agencia').prop('required', true);
        $('#nm_conta_corrente').prop('required', true);
        $('#str_conta_corrente_digito').prop('required', true);
        $('#str_titular').prop('required', true);
        $('#nm_cpf').prop('required', true);
        $('#fk_id_bid').prop('required', true);
    }
    
    /* 
     * Nome, CPF, RG e Endereço COMPLETO, observação, produtos 
     */
    function setUpCaixaPostal() {
        $('#str_titular').prop('required', true);
        $('#nm_cpf').prop('required', true);
        $('#str_rg').prop('required', false);
        $('#fk_id_bid').prop('required', true);
        $('#nm_cep').prop('required', true);
        $('#str_endereco').prop('required', true);
        $('#str_numero').prop('required', true);
        $('#str_cidade').prop('required', true);
        $('#str_estado').prop('required', true);
        $('#str_bairro').prop('required', true);
    }
    /** exibe os campos escondidos após ocorrência de seleção de caixa postal */
    function reexibirCamposPostal() {
        $('.postal-exclude').show();
    }
    
    /* 
     * esconde os campos quando ocorre seleção de caixa postal 
     */
    function esconderCamposPostal() {
        $('.postal-exclude').hide();
        $('#fk_id_banco').val(0);
        $('#nm_agencia').val('');
        $('#str_agencia_digito').val('');
        $('#nm_conta_corrente').val('');
        $('#str_conta_corrente_digito').val('');
    }

    /** MÉTODO VALIDA NÚMERO DE CPF */
    function checkCPF(cpf) {
        var result = {cpfResult: ''};
        $.ajax({
            type: 'POST',
            url: baseUrl + '/consulta/check-cpf',
            async: false, // ! sincrono !
            data: {
                cpf: cpf
            },
            success: function(res) {
                //alert(result.cpfResult = res);
                result.cpfResult = res;
            }
        });
        return result.cpfResult;
    }

    /** MÉTODO VALIDA FORMATO DE CPF */
    function validateCPF(cpf) {
        var reg = /^\d{3}\.\d{3}\.\d{3}\-\d{2}$/;
        return reg.test(cpf);
    }

    /** MÉTODO VALIDA FORMATO DE TELEFONE */
    function validatePHONE(phone) {
        var reg = /^(\d{2}\.)\d{4}\-\d{4}$/;
        return reg.test(phone);
    }

    function showDialog(title, msg) {
        $('body').append('<div id="tmp-dialog-box" style="display: none; padding: 20px;" title="' + title + '">' + msg + '</div>');
        $('#tmp-dialog-box').dialog({position: ['center', 'center'],
            resizable: false,
            height: 200,
            width: 350,
            modal: true,
            close: function() {
                $("#nm_cpf").focus();
                $('#tmp-dialog-box').remove();
            },
            hide: {effect: 'fade', duration: 350},
            show: {effect: 'fade', duration: 350}
        });
    }

    /** MÉTODO IMPLEMENTA AÇÃO PARA EVENTO DE CLIQUE EM BOTÕES class="closeDialog" */
    function setUpCloseDialog() {
        $('.closeDialog').click(function() {
            $('#tmp-dialog-box').dialog("close");
        });
    }

    /** IMPLEMENTA AÇÃO PARA EVENTOS DE ESCRITA NO CAMPO DE CPF */
    $('.nm_cpf').keyup(function() {
        if (validateCPF($('.nm_cpf').val())) {
            if (!checkCPF($('.nm_cpf').val())) {
                $('.nm_cpf').unmask();
                $('.nm_cpf').val('');
                showDialog('Atenção!', 'O <span style="color: red;">CPF</span> digitado é inválido. <br />Por gentileza, verifique o número digitado.<br /><br /><input type="button" class="btn btn-success closeDialog" value="Voltar" style="float: right;">');
                setUpCloseDialog();
                $("#nm_cpf").mask("999.999.999-99");
            }
        }
    });

    $('#enm_tipo_devolucao').change(function() {
        var choose = $(this).val();
        if (choose === 'debito-conta') {
            reexibirCamposPostal();
            removeRequired();
            setUpDebitoConta();
        } else if (choose === 'conta-poupanca') {
            reexibirCamposPostal();
            removeRequired();
            setUpDebitoConta();
        } else if (choose === 'caixa-postal') {
            esconderCamposPostal();
            removeRequired();
            setUpCaixaPostal();
        }
    });

    var envioProtocol = $("#send-mail-protocol");

    $('input[name="send-protocol"]').click(function(e) {
        if ($(this).prop('checked')) {
            var element = $(e.currentTarget);
            switch (element.data('send')) {
                case 'sms':
                    envioProtocol = $("#send-sms-protocol");
                    $("#send-mail-protocol").prop('readonly', true).val('');
                    $("#send-sms-protocol").removeAttr('readonly');
                    $("#send-sms-protocol").mask('(99) 99999-9999');
                    $("#send-operadora-protocol").removeAttr('disabled');
                    break;
                case 'email':
                    envioProtocol = $("#send-mail-protocol");
                    $("#send-sms-protocol").prop('readonly', true).val('');
                    $("#send-mail-protocol").removeAttr('readonly');
                    $("#send-operadora-protocol").prop('disabled', true);
                    break;
            }
        }
    });

    /** FUNÇÃO ENVIA EMAIL COM PROTOCOLO */
    function sendProtocolEmail() {
        $('#protocol-error-msgs').html('<span style="color: blue;">Enviando e-mail. Aguarde...</span>');
        $.ajax({
            type: 'POST',
            url: baseUrl + '/consulta/send-protocol-mail',
            data: {
                protocol: $('#protocol-number').val(),
                client_name: $('#protocol-number').data('client'),
                client_mail: $("#send-mail-protocol").val(),
                client_id: $("#protocol-number").data('clientid')
            },
            success: function(res) {
                $('#protocol-error-msgs').html('<span style="color: blue;">' + res + '</span>');
                var timer = setInterval(function() {
                    $('#box-protocol-data').dialog('close');
                    $('#protocol-error-msgs').html('');
                    clearInterval(timer);
                }, 2000);
            }
        });
    }

    /** FUNÇÃO ENVIA SMS COM PROTOCOLO  */
    function sendProtocolSms() {
        $('#protocol-error-msgs').html('<span style="color: blue;">Sms enviado.</span>');
        $.ajax({
            type: 'POST',
            url: baseUrl + '/consulta/save-sended-protocol',
            data: {
                protocol: $('#protocol-number').val(),
                client_name: $('#protocol-number').data('client'),
                client_phone: $("#send-sms-protocol").val(),
                client_operadora: $('#send-operadora-protocol').val(),
                client_id: $("#protocol-number").data('clientid')
            },
            complete: function() {
                $('body').append('<iframe style="display:none;" src="http://int.whitelabel.com.br/qe-fsvas/MTReceiver?username=fsvas&password=oifsvas&msisdn=55' + ($("#send-sms-protocol").val().replace(/[^\d]+/g, '')) + '&msg=MAPFRE Seguros informa o seu protocolo de atendimento: ' + ($('#protocol-number').val()) + ' Duvidas? ligue para 4002-7041 / 0800-570-7041 ou acesse www.mapfrevivo.com.br&la=43657&partnerId=' + ($('#send-operadora-protocol').val()) + '"></iframe>');
                //$('body').append('<iframe style="display:none;" src="http://54.232.178.12:8081/qe-fsvas/MTReceiver?username=fsvas&password=oifsvas&msisdn=55' + ($("#send-sms-protocol").val().replace(/[^\d]+/g, '')) + '&msg=' + ($('#protocol-number').val()) + '&la=43657&partnerId=' + ($('#send-operadora-protocol').val()) + '"></iframe>');
                var timer = setInterval(function() {
                    $('#box-protocol-data').dialog('close');
                    $('#protocol-error-msgs').html('');
                    clearInterval(timer);
                }, 2000);
            }
        });
    }

    /** FUNÇÃO CAPTURA EVENTO DE CLIQUE */
    $('#close-send-protocol').click(function(e) {
        $('#protocol-error-msgs').html('');
        if (envioProtocol.prop('id') == 'send-mail-protocol') {
            if (envioProtocol.val() != '') {
                sendProtocolEmail();
            } else {
                $('#protocol-error-msgs').html('<span style="color: red;">O e-mail não pode estar em branco.</span>');
            }
        } else if (envioProtocol.prop('id') == 'send-sms-protocol') {
            if (envioProtocol.val() != '' && $('#send-operadora-protocol').val() != '') {
                sendProtocolSms();
            } else if (envioProtocol.val() == '') {
                $('#protocol-error-msgs').html('<span style="color: red;">O telefone não pode estar em branco.</span>');
            } else {
                $('#protocol-error-msgs').html('<span style="color: red;">Selecione uma operadora.</span>');
            }
        }
    });
    
    /** FUNÇÃO CAPTURA EVENTO DE CLIQUE Certificado */
    var envioCertificado = $("#send-mail-certificado");
    $('#close-send-certificado').click(function(e) {
        $('#certificado-error-msgs').html('');
        if (envioCertificado.prop('id') == 'send-mail-certificado') {
            if (envioCertificado.val() != '') {
                sendCertificadoEmail();
            } else {
                $('#certificado-error-msgs').html('<span style="color: red;">O e-mail não pode estar em branco.</span>');
            }
        }
    });
    /** FUNÇÃO ENVIA EMAIL COM Certificado */
    function sendCertificadoEmail() {
        $('#certificado-error-msgs').html('<span style="color: blue;">Enviando e-mail. Aguarde...</span>');
        $.ajax({
            type: 'POST',
            url: baseUrl + '/consulta/send-certificado-mail',
            data: {
                certificado: $('#certificado-url').attr( "href" ),
                client_mail: $("#send-mail-certificado").val()
            },
            success: function(res) {
                $('#certificado-error-msgs').html('<span style="color: blue;">E-mail enviado.</span><br>' + res);
                var timer = setInterval(function() {
                    $('#box-certificado-data').dialog('close');
                    $('#certificado-error-msgs').html('');
                    clearInterval(timer);
                }, 2000);
            }
        });
    }

};

