<!DOCTYPE html>
<html>
    <head>
        <title></title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <script type='text/javascript' src='tmsTable/jquery-1.10.2.js'></script>
        <script type='text/javascript' src='tmsTable/tmsTable.js'></script>
        <link rel='stylesheet' type='text/css' href='tmsTable/style/tmsTable.css'>
    </head>
    <body>
        <!--</script>-->
        <script type="text/javascript">
            $(document).ready(function() {
                $.tmsTable('ajax-table-file.php',
                        10);
            });
        </script>

        <div id="box_tmsTable">
            <!-- campos de busca -->
            Vendedor: <input type='text' id='sTms_vendedor'>
            E-mail: <input type='text' id='sTms_email'>
            Equipe: <input type='text' id='sTms_equipe'>                      

            <!-- tabela de resultados -->
            <table id='tmsTable'>
                <thead>
                    <tr >
                        <th id='oTms_id_vendedor'>Id Vendedor</th>            
                        <th id='oTms_vendedor'>Vendedor</th>            
                        <th id='oTms_email'>E-mail</th>            
                        <th id='oTms_distribuidor'>Distribuidor</th>            
                        <th id='oTms_equipe'>Equipe</th>            
                        <th id='oTms_periodo'>Período</th>            
                        <th id='oTms_pontos'>Pontos</th>            
                    </tr>
                </thead>
                <tbody id='tmsTable_body'>

                </tbody>   
            </table>        
        </div>

    </body>
</html>
