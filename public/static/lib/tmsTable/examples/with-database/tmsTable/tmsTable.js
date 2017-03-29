
/*
 * Plugin: tmsTable 
 * Version: 1.0
 * Author: Thiago Mallon Santos
 * E-mail: thiagomallon@gmail.com
 * Date Modified: 10/25/2013
 */

jQuery.tmsTable = function(urlBusca, limitable, ordering) {
// variáveis de armazenamento das url's dos ajax
    var ajaxBusca = (urlBusca !== '') ? urlBusca : '#';

    // armazena ultimo valor de ordenação da tabela
    var limite = (typeof limitable !== 'undefined') ? limitable : 10; // armazena quantidade de ítens que serão exibidos por página
    var ordem = (typeof ordering !== 'undefined') ? ordering : null; // armazena instrução de ordenação da coluna
    var paginacao = null; // armazena total de botões de paginação
    var busca = ''; // armazena instrução de busca - cláusula where        
    var totalRegistros = null; // armazena total de registros do banco

    //-------------------   
    // alert(window.location.protocol + window.location.host + "/" + window.location.pathname);    
    //------------------

    if (ajaxBusca !== '#') { // verifica se o parâmetro de url do ajax de busca foi setado
        (function() { // função Self-Invoking para colocar imagem de ordenação nos th's da tabela, div de paginação e select de ítens por página
            var colunas = $('[id^="oTms_"]').length; // pega quantidade de th's da coluna                            
            for (var i = 0; i < colunas; i++) { // coloca imagens de ordenção nos th's da tabela                
                // $('[id^="oTms_"]').eq(i).append('<img src="' + publicFolder + '/js/tmsTable/img/sort_both.png">'); // coloca imagem em todos os th's                    
                $('[id^="oTms_"]').eq(i).append('<img src="tmsTable/img/sort_both.png">'); // coloca imagem em todos os th's                    
            }
            $('#box_tmsTable').append('<div id="pTms"></div>'); // insere a div de botões de paginação
            var selectOptions = ''; // declara variável de armazenamento dos options do select de ítens por página            
            for (var i = 10; i <= 50; i += 10) { // monta option's do select de ítens por página
                selectOptions += '<option value="' + i + '"' + ((limite === i) ? 'selected="selected"' : '') + '>' + i + '</option>'; // concatena option's do select de ítens por página
            }
            $('#box_tmsTable').prepend('<select id="iTms">' + selectOptions + '</select>'); // insere o select de ítens por página
        }());
    }

    function ordenarBotoesPaginacao(totalRegistrosAtual) { // faz cálculos para implementar os botões de paginação            
        totalRegistros = totalRegistrosAtual; // atribui valor recebido à variável de total de registros                    
        $('#pTms').html(''); // esvazia div de botões de paginação                       
        var totalParcial = totalRegistrosAtual % limite; // pega resto da divisão do total de registros pela quantidade de ítens por página            
        var total = (totalParcial > 0) ? ((totalRegistrosAtual / limite) + 1) : totalParcial; // verifica se existe resto na divisão acima e atribui mais um botão de paginação, caso tenha resto                                  
        for (var i = 1; i < total; i++) { // insere botões de paginação na div de botões de paginação                
            var hifen = (i > 1) ? ' - ' : ''; // verifica se é o primeiro botão e coloca hífen entre os demais                                            
            $('#pTms').append(hifen + '<span id="pTms_page' + i + '">' + i + '</span>  '); // adiciona botão à div de botões de paginação 
            // atribui cor vermelha ao primeiro botão da paginação ou ao botão da pagina atual                                                 
            ((paginacao > 1) ? $('#pTms_page' + paginacao).css('color', 'red') : $('#pTms_page1').css('color', 'red'));
        }
    }

    $('#iTms').on('change', function() { // eventos para mudança no select box            
        limite = $(this).val(); // pega valor do select box de ítens por página
        // verifica novamente o número de paginação com base no valor de registros 
        // retornados dividido pelo número de ítens por página, escolhido no select
        ordenarBotoesPaginacao(totalRegistros);
        $(this).find(function() { // faz reload nos botões de paginação                
            $('#pTms [id^="pTms_page"]').click(function() { // implementa evento para clique nos botões de paginação                      
                paginacao = $(this).attr('id').replace('pTms_page', ''); // atribui à variável o valor do botão de paginação clicado                    
                ordenar(ordem); // chama função que faz requisição
            });
        });
        paginacao = null; // seta paginação como null - coloca a paginação na página 1        
        ordenar(ordem); // chama função que faz requisição 
    });

    $('[id^="oTms_"]').click(function() { // altera ordenação da tabela
        paginacao = null; // seta paginação como null - coloca a paginação na página 1                                      
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

    function alterarImagem(id, direc) { // altera imagem de ordenação            
        //$('[id^="oTms_"] img').attr('src', '' + publicFolder + '/js/tmsTable/img/sort_both.png'); // tira imagem de ordenação das colunas                    
        //$('#' + id + ' img').attr('src', '' + publicFolder + '/js/tmsTable/img/sort_' + direc + '.png'); // coloca imagem específica 
        $('[id^="oTms_"] img').attr('src', 'tmsTable/img/sort_both.png'); // tira imagem de ordenação das colunas                    
        $('#' + id + ' img').attr('src', 'tmsTable/img/sort_' + direc + '.png'); // coloca imagem específica 
    }

    function ordenar(ordem) { // função envia requisição   
        if (ajaxBusca !== '#') {
            $.ajax({url: ajaxBusca, // envia dados via post, via ajax à página setada                
                type: "POST",
                data: {ordem: ordem, limite: limite, paginacao: paginacao, busca: busca}, // envia dados da requisição
                success: function(res) { // traz resultado da requisição
                    $('#tmsTable_body').html(res); // joga resultado para a div
                    ordenarBotoesPaginacao($('#total').val());

                    $('#pTms [id^="pTms_page"]').click(function() { // implementa evendo para clique no botão de paginação                           
                        paginacao = $(this).attr('id').replace('pTms_page', ''); // atribui valor de paginação, segundo o botão de paginação clicado                    
                        ordenar(ordem); // chama função que faz requisição
                    });
                },
                error: function() {
                    alert(" -> Ocorreu um erro na requisição ajax.\n\n - Verifique o endereço url que foi passado no parâmetro 'urlBusca'.");
                }
            });
        } else {
            alert('O parâmetro de busca do ajax de busca, não pode ser vazio');
        }
    }
    ordenar(ordem);

    $('[id^="sTms_"]').keyup(function() { // implementa eventa para preenchimento de campos de busca            
        var posicoes = $('[id^="sTms_"]').length; // variável armazena o número de elementos dom com tal posição            
        busca = ''; // zera variável de armazenamento de dados de busca (cláusula where)            
        for (var i = 0; i < posicoes; i++) { // monta string de termos de busca (cláusula where)
            if ($('[id^="sTms_"]').eq(i).val() !== '') { // verifica se o campo de termo de busca não está vazio
                // concatena termos de busca na variável 'busca' - cláusulo where
                busca += $('[id^="sTms_"]').eq(i).attr('id').replace('sTms_', ' ') + ' like "%' + $('[id^="sTms_"]').eq(i).val() + '%" - ';
            }
        }
        paginacao = null; // coloca página na posição 1            
        ordenar(ordem); // chama função que faz requisição                        
    });
};