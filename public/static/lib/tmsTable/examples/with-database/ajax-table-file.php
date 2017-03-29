
<?php
require_once './DbConnection.php';

// pega da tabela
$limite = ((isset($_POST['limite'])) ? $_POST['limite'] : '');
$ordem = ((isset($_POST['ordem'])) ? $_POST['ordem'] : '');
$busca = ((isset($_POST['busca'])) ? $_POST['busca'] : '');
$paginacaoParcial = (!empty($_POST['paginacao'])) ? $_POST['paginacao'] : 1;
$paginacao = ($paginacaoParcial * $limite) - $limite;

// monta e retorna resultado da query de busca
function buscarPontos($limite, $ordem, $paginacao, $busca) {
    // monta statement
    $statement = 'SELECT *
    FROM vw_distribuidores_pontos
    WHERE id_vendedor IS NOT NULL ';

    // faz verificações de valores setados para as cláusulas de ordenação e busca, da query
    if (!empty($busca)) { // verifica se foi setada a cláusula de busca
        $statement .= $busca;
    }
    if (!empty($ordem)) { // verifica se foi setada a cláusula de ordenação
        $statement .= "ORDER BY $ordem ";
    }
    if (!empty($limite)) { // verifica se foi setado a cláusula de limite
        $statement .= "LIMIT 0, $limite ";
    }
    if (!empty($paginacao)) { // verifica se foi setada a cláusula de paginação
        $statement .= '';
    }
    // retorna valor
    return DbConnection::executeFetchAll($statement);
}

// atribui a lista de resultados à variável $dados
$vendedores = buscarPontos($limite, $ordem, $paginacao, $busca);

// atribui a lista de resultados à variável $dados
$registros = buscarPontos(NULL, NULL, NULL, $busca);

// disponibiliza dados na variável de view
$total = count($registros);
?>

<?php foreach ($vendedores as $vendedor) : ?>
    <tr>           
        <td><?php echo $vendedor['id_vendedor']; ?></td>
        <td><?php echo $vendedor['vendedor']; ?></td>
        <td><?php echo utf8_encode($vendedor['email']); ?></td>
        <td><?php echo utf8_encode($vendedor['distribuidor']); ?></td>                
        <td><?php echo utf8_encode($vendedor['equipe']); ?></td>                                
        <td><?php echo implode('/', array_reverse(explode('-', $vendedor['periodo']))); ?></td>                                               
        <td><?php echo utf8_encode($vendedor['pontos']); ?></td>                                                
    </tr>
<?php endforeach; ?>
<input type='hidden' id='total' value='<?php echo $total; ?>'>