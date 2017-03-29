jQuery.orderingDevolucoes = function(baseUrl, devolucao) {

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

    /** IMPLEMENTA AÇÃO PARA EVENTO DE ALTERAÇÃO DE SELECT DE TIPO DE DEVOLUÇÃO */
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

    /** IMPLEMENTA AÇÃO PARA EVENTO DE ALTERAÇÃO DE SELECT DE TIPO DE DEVOLUÇÃO */
    $(function() {
        if (devolucao === 'debito-conta') {
            reexibirCamposPostal();
            removeRequired();
            setUpDebitoConta();
        } else if (devolucao === 'conta-poupanca') {
            reexibirCamposPostal();
            removeRequired();
            setUpDebitoConta();
        } else if (devolucao === 'caixa-postal') {
            esconderCamposPostal();
            removeRequired();
            setUpCaixaPostal();
        }
    });

    /** intercepta eventos de clique */
    $('.devolucoes').click(function(e) {
        processModal('show', 'Buscando informações ...');
        var selected = $(e.currentTarget);
        $.ajax({
            type: 'POST',
            url: baseUrl + '/devolucao-premios/financeiro-devolucao',
            data: {id: selected.data('devolucao')},
            success: function(res) {
                $('#box-devolucao-premios').css('display', 'none');
                $('#box-devolucao-premios-form').html(res);
                $('#box-devolucao-premios-form').css('display', 'block');
                processModal('hide');
            }
        });
    });

    /** intercepta eventos de clique no botão de voltar */
    $('#devolucao-voltar').click(function() {
        $('#box-devolucao-premios').css('display', 'block');
        $('#box-devolucao-premios-form').css('display', 'none');
    });

    /** IMPLEMENTA AÇÃO PARA EVENTO DE ALTERAÇÃO DO SELECT */
    $('#fk_id_bid').change(function() {
        var productData = $(this).val().split('_');
        var price = 'R$ ' + productData[2].replace('.', ',');
        var cents = price.substr(price.indexOf(','));
        $('#str_total_devolucao').val((cents.length < 3) ? price + "0" : price);
    });
};

