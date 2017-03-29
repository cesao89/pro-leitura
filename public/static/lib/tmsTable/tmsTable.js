/*
 * Plugin: tmsTable 
 * Version: 1.0
 * Author: Thiago Mallon Santos
 * E-mail: thiagomallon@gmail.com
 * Date Modified: 07/05/2014
 */

jQuery.tmsTable = function(urlBusca, limitable, ordering, baseUrl) {
    // variáveis de armazenamento das url's dos ajax
    var ajaxBusca = (urlBusca !== undefined && urlBusca !== '') ? urlBusca : '#';

    // armazena ultimo valor de ordenação da tabela
    var limite = (typeof limitable !== 'undefined') ? limitable : 10; // armazena quantidade de ítens que serão exibidos por página
    var ordem = (typeof ordering !== 'undefined') ? ordering : null; // armazena instrução de ordenação da coluna
    var paginacao = 1; // armazena total de botões de paginação
    var busca = ''; // armazena instrução de busca - cláusula where        
    var totalRegistros = null; // armazena total de registros do banco    
    var totalPaginacao = null; // armazena total de paginações para os registros da tabela       

    if (ajaxBusca !== '#') { // verifica se o parâmetro de url do ajax de busca foi setado
        (function() { // função Self-Invoking para colocar imagem de ordenação nos th's da tabela, div de paginação e select de ítens por página
            var colunas = $('[id^="oTms_"]').length; // pega quantidade de th's da coluna                            
            for (var i = 0; i < colunas; i++) { // coloca imagens de ordenção nos th's da tabela                                 
                $('[id^="oTms_"]').eq(i).append('<img style="margin-left: 5px;" src="' + baseUrl + '/static/lib/tmsTable/img/sort_both.png">'); // coloca imagem em todos os th's                   
            }
            $('#box_tmsTable').append('<div id="pTms"></div>'); // insere a div de botões de paginação
            var selectOptions = ''; // declara variável de armazenamento dos options do select de ítens por página            
            for (var i = 10; i <= 50; i += 10) { // monta option's do select de ítens por página
                selectOptions += '<option value="' + i + '"' + ((limite === i) ? 'selected="selected"' : '') + '>' + i + '</option>'; // concatena option's do select de ítens por página
            }
            $('#box_tmsTable').append('<select id="iTms" class="iTms">' + selectOptions + '</select>'); // insere o select de ítens por página             
        }());
    }

    function ordenarBotoesPaginacao(totalRegistrosAtual) { // faz cálculos para implementar os botões de paginação            
        totalRegistros = totalRegistrosAtual; // atribui valor recebido à variável de total de registros                                               
        var totalParcial = totalRegistrosAtual % limite; // pega resto da divisão do total de registros pela quantidade de ítens por página            
        var resPaginacoes = totalRegistrosAtual / limite; // pega resultado da divisão to total de registros pela quantidade de ítens por página        
        var total = (totalParcial > 0) ? ((totalRegistrosAtual / limite) + 1) : ((resPaginacoes === 1) ? 2 : ((resPaginacoes === 2) ? 3 : resPaginacoes)); // verifica se existe resto na divisão acima e atribui mais um botão de paginação, caso tenha resto                                                  
        totalPaginacao = parseInt(total);
        $('#pTms_screen_paginator').val(paginacao + ' de ' + totalPaginacao);
    }

    $('#iTms').on('change', function() { // eventos para mudança no select box            
        limite = $(this).val(); // pega valor do select box de ítens por página
        // verifica novamente o número de paginação com base no valor de registros 
        // retornados dividido pelo número de ítens por página, escolhido no select
        ordenarBotoesPaginacao(totalRegistros);
        paginacao = 1; // seta paginação como null - coloca a paginação na página 1        
        ordenar(ordem); // chama função que faz requisição 
    });

    $('[id^="oTms_"]').click(function() { // altera ordenação da tabela        
        paginacao = 1; // seta paginação como null - coloca a paginação na página 1                                      
        if (ordem === $(this).attr('id').replace('oTms_', '') + ' ASC') { // pega coluna que foi solicitada ordenação e verifica se coluna estava ordenada de forma ASC
            ordem = ordem.replace('ASC', 'DESC'); // altera instrução de ordenação da coluna                
            alterarImagem($(this).attr('id'), 'desc'); // altera imagem de ordenação da coluna                                
            ordenar(ordem); // chama função de requisição                
        } else if (ordem === $(this).attr('id').replace('oTms_', '') + ' DESC') { // pega coluna que foi solicitada ordenação e verifica se coluna estava ordenada de forma DESC                
            ordem = ordem.replace('DESC', 'ASC'); // altera instrução de ordenação da coluna                
            alterarImagem($(this).attr('id'), 'asc'); // altera imagem de ordenação da coluna                
            ordenar(ordem); // chama função de requisição                
        } else { // pega coluna que foi solicitada ordenação e ordena de forma ASC                
            ordem = $(this).attr('id').replace('oTms_', '') + ' ASC'; // altera instrução de ordenação da coluna                
            alterarImagem($(this).attr('id'), 'asc'); // altera imagem de ordenação da coluna                                
            ordenar(ordem); // chama função de requisição
        }
    });

    //function trocarPaginas(ordem, botao) { // função incrementa/decrementa e passa para a primeira/ultima página - paginação
    function trocarPaginas(botao) { // função incrementa/decrementa e passa para a primeira/ultima página - paginação        
        switch (botao) {
            case 'first': // caso o botão clicado seja o 'pTms_first'
                paginacao = 1;
                break;
            case 'last': // caso o botão clicado seja o 'pTms_last'
                paginacao = totalPaginacao;
                break;
            case 'next': // caso o botão clicado seja o 'pTms_next'
                (paginacao < totalPaginacao) ? paginacao += 1 : null;
                break;
            case 'previous': // caso o botão clicado seja o 'pTms_previous'
                (paginacao > 1) ? paginacao -= 1 : null;
                break;
        }
        ordenar(ordem); // chama a função que faz requisição  
    }

    var controlsSet = false; // variável para verificação de box de paginação já setado

    // método monta box de paginação 
    function showTmsControls() {
        $('#box_tmsTable').append('<div id="tms_Pagination"></div>');
        $('#tms_Pagination').append('<div id="pTms_first">&laquo;</div>');
        $('#tms_Pagination').append('<div id="pTms_previous">&lsaquo;</div>');
        $('#tms_Pagination').append('<input type="text" readonly="readonly" id="pTms_screen_paginator"/>');
        $('#tms_Pagination').append('<div id="pTms_next">&rsaquo;</div>');
        $('#tms_Pagination').append('<div id="pTms_last">&raquo;</div>');
        controlsSet = true; // seta variável verificadora com true, informando que box já está criado
        // captura eventos de clique, nos botões do paginator
        $("[id^=pTms_]").click(function() {
            trocarPaginas($(this).attr('id').replace('pTms_', ''));
        });
    }

    // exibir modal 
    function createModal() {
        var timerAguarde = 400;
        $('#tmsTable_body').html('<tr><td colspan="100%"><div id="box_aguardar"><span class="box_aguardar_msg"> Aguarde...</span></div></td></tr>');
        setInterval(function() {
            if ($('.box_aguardar_msg').css('display') === 'none') {
                $('.box_aguardar_msg').fadeIn(timerAguarde);
            } else {
                $('.box_aguardar_msg').fadeOut(timerAguarde);
            }
        }, timerAguarde);
    }

    function alterarImagem(id, direc) { // altera imagem de ordenação            
        $('[id^="oTms_"] img').attr('src', baseUrl + '/static/lib/tmsTable/img/sort_both.png'); // tira imagem de ordenação das colunas                    
        $('#' + id + ' img').attr('src', baseUrl + '/static/lib/tmsTable/img/sort_' + direc + '.png'); // coloca imagem específica 
    }

    function ordenar(ordem) { // função envia requisição      
        createModal();
        if (ajaxBusca !== '#') {
            $.ajax({url: ajaxBusca,
                // envia dados via post, via ajax à página setada
                data: {ordem: ordem, limite: limite, paginacao: paginacao, busca: busca}, // envia dados da requisição
                success: function(res) { // traz resultado da requisição                    
                    $('#tmsTable_body').html(res); // joga resultado para a div
                    if (!controlsSet) { // verifica se box de paginação ainda não foi criado para criação do mesmo
                        showTmsControls();
                    }
                    ordenarBotoesPaginacao($('#total').val());
                },
                error: function() {
                    alert(" -> Ocorreu um erro na requisição ajax.\n\n - Verifique o endereço url que foi passado no parâmetro 'urlBusca'.");
                }
            });
        } else {
            alert('O parâmetro de busca do ajax de busca, não pode ser vazio');
        }
    }
    ordenar(ordem); // chama a função quando a página carrega

    $('[id^="sTms_"]').keyup(function() { // implementa eventa para preenchimento de campos de busca                           
        busca = ''; // zera variável de armazenamento de dados de busca (cláusula where)            
        for (var i = 0; i < $('[id^="sTms_"]').length; i++) { // monta string de termos de busca (cláusula where)
            if ($('[id^="sTms_"]').eq(i).val() !== '') { // verifica se o campo de termo de busca não está vazio
                // concatena termos de busca na variável 'busca' - cláusulo where
                busca += $('[id^="sTms_"]').eq(i).attr('id').replace('sTms_', ' ') + ' like "%' + $('[id^="sTms_"]').eq(i).val() + '%" - ';
            }
        }
        for (var i = 0; i < $('[id^="sbTms_"]').length; i++) { // monta string de termos de busca (cláusula where)
            if ($('[id^="sbTms_"]').eq(i).val() !== '') { // verifica se o campo de termo de busca não está vazio
                // concatena termos de busca na variável 'busca' - cláusulo where
                busca += $('[id^="sbTms_"]').eq(i).attr('id').replace('sbTms_', ' ') + ' like "%' + $('[id^="sbTms_"]').eq(i).val() + '%" - ';
            }
        }
        paginacao = 1; // coloca página na posição 1                  
        ordenar(ordem); // chama função que faz requisição                        
    });

    $('[id^="sbTms_"]').change(function() { // implementa evento para alteração dos selects de busca                         
        busca = ''; // zera variável de armazenamento de dados de busca (cláusula where)            
        for (var i = 0; i < $('[id^="sbTms_"]').length; i++) { // monta string de termos de busca (cláusula where)
            if ($('[id^="sbTms_"]').eq(i).val() !== '') { // verifica se o campo de termo de busca não está vazio
                // concatena termos de busca na variável 'busca' - cláusulo where
                busca += $('[id^="sbTms_"]').eq(i).attr('id').replace('sbTms_', ' ') + ' like "%' + $('[id^="sbTms_"]').eq(i).val() + '%" - ';
            }
        }
        for (var i = 0; i < $('[id^="sTms_"]').length; i++) { // monta string de termos de busca (cláusula where)
            if ($('[id^="sTms_"]').eq(i).val() !== '') { // verifica se o campo de termo de busca não está vazio
                // concatena termos de busca na variável 'busca' - cláusulo where
                busca += $('[id^="sTms_"]').eq(i).attr('id').replace('sTms_', ' ') + ' like "%' + $('[id^="sTms_"]').eq(i).val() + '%" - ';
            }
        }
        paginacao = 1; // coloca página na posição 1             
        ordenar(ordem); // chama função que faz requisição                        
    });

    $("#tmsTable tr:eq(0) th").addClass('tmsSearch');
};