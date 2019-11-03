<?php
/* Aluno: Roberto Santos
   Turma: FS06
   
   Objetivo: Criar um formulário para captar o dados de um produto e produzir uma página com as informações validadas.
             1. Criar um formulário em HTML.
             2. Pegar valores dos campos através do PHP utilizando método POST.
             3. Armazenar informações cadastradas na session.
             4. Exibir os dados recebidos em uma tabela de produtos.

             Itens obrigatórios:
             ❏ formulário com os seguintes campos:
             ❏ nome do produto
             ❏ descrição do produto
             ❏ quantidade em estoque
             ❏ preço
             ❏ foto (upload)
             ❏ verificar se os campos estão preenchidos e com dados válidos (quantidade e preço só podem receber números)
             ❏ exibir todos os produtos cadastrados em uma tabela
             ❏ Exibir imagem e detalhes do produto em página individual de forma dinâmica (utilizar método GET para pegar id do produto).
*/


// Este array vai ser usado para gerar uma categoria dinâmica.
$categorias = ["Camiseta", "Vestido", "Bermuda", "Casaco", "Calça"];


// Esta função serve para cadastrar produtos.
function cadastrarProduto($nomeProduto,$categoriaProduto,$descricaoProduto,$quantidadeProduto,$precoProduto,$imagemProduto){

    $ArquivoJson = "produtosRegistrados.json";

    //  A função file_exists é para ver se o arquivo Json existe. A função isset é para saber se o conteúdo da variável existe.
    if(file_exists($ArquivoJson)) {

        // Abrindo e pegando informações do arquivo json (GET=pegar)
        $dentro_do_Json = file_get_contents($ArquivoJson);

        // Transformar o json em array (decode) - é preciso colocar como segundo parâmetro true, para informar que quero transformar em um array - se não colocar, ele transforma em objeto.
        $produtosRegistrados = json_decode($dentro_do_Json,true);

        // Incluindo id no produto, caso não tenha produto registrado.
        if($produtosRegistrados==[]) { 
            $produtosRegistrados[] = ["id"=>1, "nome"=>$nomeProduto, "categoria"=>$categoriaProduto, "descricao"=>$descricaoProduto, "quantidade"=>$quantidadeProduto, "preco"=>$precoProduto, "imagem"=>$imagemProduto];
    
            // Colocando id no produto se já tem produto registrado anteriormente.
            } else { 
                $ultimoId = end($produtosRegistrados);
                $somandoId = $ultimoId["id"] + 1;
    
                $produtosRegistrados[] = ["id"=>$somandoId, "nome"=>$nomeProduto, "categoria"=>$categoriaProduto, "descricao"=>$descricaoProduto, "quantidade"=>$quantidadeProduto, "preco"=>$precoProduto, "imagem"=>$imagemProduto];
            }
    
            // pegando o array de $produtosRegistrados e convertendo para formato .json
            $json = json_encode($produtosRegistrados); 

            // colocando o conteúdo json no arquivo .json
            $ficouCerto = file_put_contents($ArquivoJson, $json); //coloca o conteúdo json no arquivo
        
    } else {
        // $produtosRegistrados - mesmo nome da variável definida no arquivo variaveis.php
        // Aqui declaramos a estrutura do array, vazio ainda, para depois adicionar elementos dentro dele (é possível já fazer tudo em uma linha, então ficaria: $produtos = [["nome"=>$nomeProduto, "preco"=>$precoProduto, "img"=>$imgProduto, "descricao"=>$descricaoProduto]];)
        // Criando um array vazio.
        $produtosRegistrados = [];

        // faz o mesmo que array_push:
        // o nome da etiqueta tem que ser o mesmo que chamo lá na index; o nome da variável é a mesma definida na function
        // incluindo conteúdo no array que estava vazio.
        $produtosRegistrados[] = ["id"=>1, "nome"=>$nomeProduto, "categoria"=>$categoriaProduto, "descricao"=>$descricaoProduto, "quantidade"=>$quantidadeProduto, "preco"=>$precoProduto, "imagem"=>$imagemProduto];

        // Tranformando o array associativo em json:
        $json = json_encode($produtosRegistrados);

        // Salvando os arquivos no produtos.json. Estrutura - primeiro nome do arquivo, depois o encode (variável que faz o encode).
        $ficouCerto = file_put_contents($ArquivoJson, $json);

    }

};


//verificando se tem algo sendo enviado via post
if($_POST) {
    // Salvando arquivo - dentro do files tem que ter o name que vai no input do form, e depois o dado específico que quero pegar dentro desse array (para visualizar esse dado, damos var_dump na $_FILES).
    $arquivoDeImagem = $_FILES["imagemProduto"]["name"];

    $localTmp = $_FILES["imagemProduto"]["tmp_name"];

    // Pegando a data atual para concatenar ao nome da imagem
    $dataAtual = date("d-m-Y");

    // Onde eu quero que esse arquivo seja salvo:
    $localImagem = "imagens/".$dataAtual." ".$arquivoDeImagem;

    // Aqui eu movo do local temporário para o local final.
    $movendo = move_uploaded_file($localTmp, $localImagem);

    // Colocar os parâmetros na mesma ordem que defini na função:
    // o nome que vai dentro do post é o mesmo "name" que está no form.
    // Esse echo imprime o retorno da função.
    echo cadastrarProduto($_POST["nomeProduto"], $_POST["categoriaProduto"], $_POST["descricaoProduto"], $_POST["quantidadeProduto"], $_POST["precoProduto"], $localImagem);  
}

    // Criação da variavel $produtosRegistrados para guardar os dados que estão no arquivo produtosRegistrados.json 
    // $produtosRegistrados = file_get_contents("produtosRegistrados.json");

    $ArquivoJson = "produtosRegistrados.json";

    // Agora vou converter o arquivo produtosRegistrados.json em Array para ser utilizado no meu foreach.
    $produtosRegistrados = json_decode(file_get_contents($ArquivoJson), true);

?>

<!DOCTYPE html>

<html lang="pt-br">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <link href="css/style.css" rel="stylesheet"/>
        <title>*** Cadastrar Produto ***</title>
    </head>

    <body>

        <div class="container mt-5">

            <div class="row">

                <!-- Início da Lista (Tabela) onde é exibido os dados dos produtos cadastrados. -->
                <div class="col-lg-7">
                    <h1>Todos os produtos</h1>
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">Nome</th>
                                <th scope="col">Categoria</th>
                                <th scope="col">Preço</th>
                            </tr>
                        </thead>

                        </tbody>
                            <?php if(isset($produtosRegistrados) && $produtosRegistrados != []) { ?>
                            <?php foreach($produtosRegistrados as $produtoRegistrado) { ?>
                            <tr>
                                <td> <a href="PaginaIndividual.php?id= <?php echo $produtoRegistrado["id"]; ?>" > <?php echo $produtoRegistrado["nome"]; ?> </a> </td>
                                <td> <?php echo $produtoRegistrado["categoria"]; ?> </td>
                                <td> <?php echo "R$".$produtoRegistrado["preco"]; ?> </td>
                        <!-- <td> <a class="btn btn-primary btn-sm href="EditarProduto.php">&nbsp; Editar&nbsp;&nbsp;</a> <a class="btn btn-danger btn-sm" href="ApagarProduto.php">Apagar</a></td> -->
                            </tr>
                            <?php } ?> 
                            <?php } else { ?>
                                <h1> Não tem produto cadastrado  :-( </h1>
                            <?php } ?>
                        </tbody>
                    </table>
                <!-- FIM da Lista (Tabela) onde é exibido os dados dos produtos cadastrados. -->    
                </div>


                <!-- INÍCIO do formulário para cadastrar os produtos. -->
                <div class="col-lg-5 bg-light">
                    <form class="p-5" method="post" enctype="multipart/form-data">
                        <h4>Cadastrar produto</h4>

                        <div class="form-group">
                            <label class="font-weight-bold" for="nomeProduto">Nome</label>
                            <input type="text" name="nomeProduto" class="form-control" placeholder="" required="required">
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold" for="categoriaProduto">Categoria</label>
                            <select class="form-control" name="categoriaProduto" required="required">
                                <option disabled selected></option>
                                <?php foreach ($categorias as $categoria) { ?>
                                <option value="<?php echo $categoria?>"> <?php echo $categoria ?> </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold" for="descricaoProduto">Descrição</label>
                            <textarea class="form-control" name="descricaoProduto" rows="3" required="required"></textarea>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold" for="quantidadeProduto">Quantidade</label>
                            <input type="number" min='0' max='999' name="quantidadeProduto" value = '0' class="form-control" placeholder="">
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold" for="precoProduto">Preço</label>
                            <input type="number" min='0' step="0.01" name="precoProduto" value = '0' class="form-control" placeholder="">
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold" for="imagemProduto">Foto do produto</label>
                            <input type="file" class="form-control-file" name="imagemProduto" required="required">
                        </div>

                        <button type="submit" class="btn btn-primary">ENVIAR</button>
                    </form>

                 <!-- FIM do formulário para cadastrar os produtos. -->   
                </div>
            </div>
        </div>
    
    </body>

</html>
