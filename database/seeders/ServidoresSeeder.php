<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Driver;
use Illuminate\Support\Str;

class ServidoresSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sua lista crua (copiada e colada)
        $rawData = <<<TEXT
Adalcheila Alves dos Santos	048.952.256-40
Adalvan Soares de Oliveira	061.765.996-60
Adeilton de Sousa Menezes	038.785.825-36
Adriana Silva Lucio	092.933.996-79
Ailana Fernanda Silva Dutra Santos	292.709.328-85
Alan Fernandes Cabral	045.340.876-16
Alan Teixeira de Oliveira	839.845.775-91
Aldo Jose Conceicao da Silva	962.636.795-49
Alexandra Bittencourt de Carvalho	052.491.606-38
Alexandre Goncalves Barbosa	113.257.296-71
Alexandre Guimaraes Santos	911.316.696-49
Aline Simoes Aguiar	066.688.356-43
Alisson Gomes Santana	006.251.056-80
Alvaro Felipe Matos Oliveira	109.197.206-09
Amanda Pereira Carvalho	052.832.446-28
Ana Claudia Oliveira Azevedo	075.956.155-93
Ana Claudia Sierra Martins	028.276.737-11
Ana Cristina de Souza Maria Maciel	103.472.506-88
Ana Paula Almeida Porto	071.982.636-58
Andres Alves Costa	070.731.036-98
Ane Catarine Tosi Costa	632.962.057-18
Annanda Mendes Costa	084.334.606-09
Anne Caroline Vieira Cangussu	043.167.695-02
Antonio Clarette Santiago Tavares	029.806.216-09
Arlene Moreira Gois	836.648.156-53
Arquino Ramalho	072.975.096-55
Atila Caires de Medeiros	012.576.526-64
Bianca Araújo de Oliveira	900.475.229-66
Bianca Candida Martins Veloso	117.309.246-38
Bruna Cristina da Silva Gomes	097.943.116-69
Bruno Cesar Magalhaes Alquimim	024.558.515-08
Bruno Guimaraes Ventorim	130.296.247-73
Bruno Tadeu Lopes	121.442.727-86
Camila Rodrigues Freitas	016.404.266-04
Camilo Siqueira Miranda	070.148.966-95
Carolina Machado e Andrade	064.044.346-01
Caroline Goncalves Campos	097.610.596-93
Celio Medina Goncalo	049.119.016-67
Claudia Adriana Souza Santos	006.700.426-17
Claudia Cristina de Oliveira Alves	049.806.316-06
Clayton Chaves Silva Nascimento	094.686.736-42
Creonice Santos Bigatello	041.913.126-42
Daiana Batista de Araujo	058.460.696-65
Daiane Prates Mendonca	015.611.365-14
Daniele Alves dos Reis Miranda	077.229.216-71
Daniele Lopes Ribeiro	089.647.286-88
Daniel Frank Castro	107.066.166-00
Daniel Silva Moraes	098.038.796-50
Dayane Patricia Cunha de Franca	068.067.266-40
Debora Alves Ribeiro	079.281.666-82
Debora Dias Ferreira	031.068.856-61
Debora Soares de Araujo	552.886.773-87
Deivison Porto de Sousa	024.464.705-48
Deivson Vinicius Barroso	108.094.176-26
Diana Otoni Meiras	012.599.356-04
Diego Firme Lacerda Borges	076.084.196-95
Douglas Danton Nepomuceno	083.123.406-70
Edimilson Alves Barbosa	079.559.716-98
Ednilton Moreira Gama	785.246.365-72
Edson Alexandre de Queiroz	033.237.176-00
Eduardo Charles Barbosa Ayres	744.899.845-53
Eliezer Mendes Costa	092.856.836-90
Elis Clara Meira Costa	078.365.366-29
Emanuelly Alves Pelogio	012.258.014-19
Erica Sudario Bodevan	085.228.606-61
Estefania Cristina da Costa Mendes	076.467.336-01
Eyleen Nabyla Alvarenga Niitsuma	016.064.946-35
Fabiano Santos Ferreira	904.875.166-72
Fabiola Davila Reis Moreira	047.642.096-24
Fabio Martins de Carvalho	939.467.675-91
Fabio Vinicius Silva Rocha	084.794.356-90
Fabricia Lobo Pinto	064.188.446-02
Fabricio Longuinhos Silva	044.465.976-55
Felix Horacio Munoz Muniz Junior	067.601.776-22
Fernanda Helena Marques	095.618.326-31
Fernando Jose Ferraz de Almeida	558.739.796-34
Flavio Alves dos Santos	962.894.066-04
Flavio Heleno Graciano	038.375.556-58
Gessimar Nunes Camelo	030.732.396-00
Giancarlo de Moura Souza	083.129.116-85
Gilvania Antunes Meireles	688.180.506-30
Glauber Antonio dos Reis Andrade	071.884.496-35
Heleno Tavares Mendes	259.140.811-49
Helimacio Barbosa dos Santos	985.906.776-72
Hudson da Rocha Pereira	043.471.876-96
Hugo Leonardo Souza Pinto	071.983.516-03
Ian Coelho de Souza Almeida	092.836.476-37
Igor de Oliveira Costa	065.971.826-00
Inacio de Loyola Ruas Lima	072.251.436-03
Irene Candida dos Reis Alves	925.657.356-34
Isabelle Arruda Barbosa	007.945.296-58
Jackson Lener Assuncao	704.440.296-80
Jairo Lucas Souza Barros	115.454.116-97
James Jesuino Souza	628.966.456-82
Jamille Santos dos Passos	018.110.805-45
Jandresson Dias Pires	842.674.615-20
Janine Couto Cruz Macedo	019.160.625-12
Jansen Pessoa Dias	757.636.567-68
Jeane Macedo Porto	139.304.126-40
Jean Freitas Lima	073.572.126-29
Jefferson Rodrigues de Souza	015.803.436-86
Jennifer Guimaraes Silva	004.296.655-80
Jessica Adalgisa Barbosa Silva	111.026.536-08
Jiego Balduino Fernandes Ribeiro	098.838.287-30
Joan Bralio Mendes Pereira Lima	047.142.286-09
Joao Alison Alves Oliveira	082.089.946-18
Joao Gabriel Loures Tury	067.723.426-07
João Paulo Araújo Souza	021.269.055-86
Joao Ramon Alves Costa	049.040.789-77
Joaquim Neto de Sousa Santos	040.812.596-90
Jorge Luiz Teixeira Ribas	112.047.156-70
Jose Ilton Chiaradia Fernandes Junior	081.034.227-89
Jose Maria Gomes Neves	057.890.846-80
Julipe de Cassia Dias de Oliveira	073.647.286-00
Karllos Gomes Santos	085.047.126-56
Keila de Oliveira Diniz	025.206.235-35
Leandro Ramalho Mendes	069.889.476-63
Leandro Rocha Santos	042.130.976-84
Lecinaide Cordeiro de Carvalho Santana	282.749.318-74
Leila Conceicao de Paula Miranda	064.758.656-80
Leomir Batista Neres	097.557.456-61
Leonan Teixeira de Oliveira	038.139.735-14
Leonardo Augusto Lopes Rodrigues	100.663.766-45
Leonardo Tavares de Souza	047.947.186-08
Lilliam Freitas Souza	104.031.536-46
Lissandra Ruas Lima	036.691.466-95
Livia Sousa Santos Medina	805.413.055-49
Lourdes Machado Pereira Ferreira	155.791.807-48
Luana Souza Marques	040.206.795-92
Lucas Lima de Resende	015.258.736-52
Lucineide Sousa Miranda	007.405.026-54
Ludmila Ameno Ribeiro Martins Santiago	045.583.026-65
Luiz Celio Souza Rocha	067.915.696-89
Luiz Eduardo Barreto de Souza	234.309.959-98
Luiz Henrique Costa Mota	083.032.116-09
Manoel Bezerra da Silva Junior	879.173.276-04
Manoel Ferreira de Souza	039.776.486-32
Marcella Nascimento Fernandes	012.262.811-09
Marco Antonio Silveira	034.573.908-60
Marco Aurelio Madureira de Carvalho	569.471.516-00
Marcony Meneguelli Alhadas	089.934.486-04
Marcos de Jesus Oliveira	703.301.821-53
Marcos Vinicius Montanari	091.051.046-61
Marcus Leonardo Figueiredo Silva	756.420.006-59
Maria de Lourdes Cunha	916.510.346-72
Mariana Mapelli de Paiva	080.586.436-96
Mariana Xavier de Souza	116.360.376-74
Marilia Paraiso de Matos	043.498.536-85
Mario Cesar Ruas Silveira	097.206.126-65
Marival Pereira de Sousa	759.518.606-44
Mateus Sena Lopes	046.356.646-78
Matheus Silva Freitas	118.385.366-12
Maykol Miranda e Silva	081.075.416-93
Mona Rezende de Almeida	022.489.361-09
Monica Moreira Scarpellino	078.786.295-95
Monik Evelin Leite Diniz	073.378.306-61
Neander Pinheiro Cabral	091.002.127-92
Nemia Ribeiro Alves Lopes	075.696.446-62
Nicolas Wilker Fioratti Silva	130.753.556-90
Pablo Castro Antunes Silva	119.523.766-96
Paulo Gustavo Macedo de Almeida Martins	054.104.396-00
Philippe Araujo Leboeuf	000.050.046-16
Poliany Pereira Cruz	019.230.716-96
Priscila Alves Lima	085.719.687-13
Quezia Mota Aguiar Alves	096.384.196-36
Rafael de Araujo Braga	053.419.915-16
Rafael Lara Martins	087.442.836-01
Rafael Rodrigues Silva	061.563.866-02
Raiane Ferreira de Miranda	040.995.631-70
Raimundo Viana Lopes Junior	070.549.326-17
Rangel Gonçalves de Souza	031.718.931-00
Regiane de Melo	110.369.626-28
Regiany Lopes Ferraz	039.500.866-25
Regina Lacerda Siqueira	041.917.706-08
Regis Fernando Ferreira Prates	809.606.850-49
Renato Duarte Souza Pinheiro	056.423.566-07
Rita Manuele Porto Sales	826.861.655-53
Roberto de Souza Teodoro Junior	076.096.176-01
Roberto Wagner Ferraz Lacerda Junior	089.061.236-38
Rodrigo Fernandes Caldeira	804.803.576-68
Romario Rocha Sousa	106.290.706-01
Romualdo Machado Ferreira	941.461.525-34
Romulo Lima Meira	914.821.355-15
Rondinei Almeida da Silva	027.267.745-04
Rosane Goncalves da Silva Roesberg	084.640.536-90
Rosangela Ferreira Ribeiro	493.466.876-49
Rosimaria Sapucaia Rocha	082.022.206-29
Samuel Real Mota	058.909.916-71
Sara Dutra Nunes	079.920.366-11
Sergio Renato Oliveira	106.286.836-69
Silvano Batista dos Santos	076.632.576-85
Simeia Dias Costa Cesar	083.715.106-67
Sirlei da Conceicao Dias	003.374.895-05
Sulamita Dias Medina Almeida	110.670.066-05
Sumaia da Silva Laurindo	083.142.367-64
Suzana Viana Mota	064.905.266-89
Tania Maria Mares Figueiredo	593.942.346-91
Telma Oliveira Soares Velloso	132.508.207-41
Thays Lopes de Almeida	086.666.296-06
Thiago Alves Moreira	121.562.506-50
Thiago Carvalho Peixoto	042.374.005-90
Thiago de Jesus Filho	031.067.735-10
Thiago Henrique Oliveira da Costa	084.460.866-11
Tiago Caminha de Lima	037.597.493-80
Tulio Botelho Moreira de Castro	107.910.426-77
Uendel Goncalves de Almeida	061.798.316-02
Valdete Maria Goncalves de Almeida	036.675.226-06
Valdirlen do Nascimento Loyolla	904.441.757-68
Vanessa de Amaral Santos	049.689.785-37
Vanessa Dias Medina	074.281.266-90
Vanessa Gregorio Rodrigues	067.175.426-24
Vico Mendes Pereira Lima	051.743.376-17
Virginia de Souza Avila Oliveira	062.027.826-92
Viviana Maria Vieira	083.266.046-90
Viviane Amaral Toledo Coelho	055.998.256-98
Waldilainy de Campos	043.521.396-29
Washington Luiz Barros Couto	803.260.806-00
Willian Fernandes	069.110.536-76
Willyane Mara Costa de Paula	115.537.466-51
Xenia Macedo Souto	062.856.926-29
Yale Christine Costa Ferreira	093.861.476-29
TEXT;

        // Separa linha por linha
        $lines = explode("\n", trim($rawData));
        
        $count = 0;
        foreach ($lines as $line) {
            // Separa o Nome do CPF usando tabulação (que é como os dados vieram colados)
            $parts = preg_split('/\t+/', trim($line));
            
            if (count($parts) >= 2) {
                $name = trim($parts[0]);
                // Remove todos os caracteres que não sejam números (limpa o CPF)
                $cpf = preg_replace('/\D/', '', $parts[1]);
                
                if (!empty($cpf)) {
                    // UpdateOrCreate garante que se o CPF já existir, ele só atualiza o nome, sem duplicar
                    Driver::updateOrCreate(
                        ['document' => $cpf],
                        [
                            'name' => $name,
                            'type' => 'Servidor',
                            // Por segurança, mantemos is_authorized como false. 
                            // O Fiscal Oficial do campus depois entra no sistema e marca os que podem dirigir viatura oficial.
                            'is_authorized' => false, 
                        ]
                    );
                    $count++;
                }
            }
        }

        $this->command->info("{$count} servidores cadastrados/atualizados com sucesso!");
    }
}