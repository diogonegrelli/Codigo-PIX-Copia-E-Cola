<?php


    function getChaveTam($chave){
        $chaveTam = strlen($chave);
        $tamString = (string) $chaveTam;
        $tamStringFormated = str_pad($tamString, 2, "0", STR_PAD_LEFT);
        return $tamStringFormated;
    }

    function getMerchantAccountInformationTam($idBacen, $idChave, $chavePix){
        $tamChave = getChaveTam($chavePix);
        $Tam = $idBacen . $idChave . $tamChave . $chavePix;
        $MerchantAccountInformationTam = (string) (strlen($Tam));
                    
        return $MerchantAccountInformationTam; 
    }

    function getTransactionAmount($valor){
       $transactionCode = "54"; 
       $amount = (string) number_format($valor, 2, "." , "");
       $tamAmount = (string) strlen($amount);
       $tamTransactionFormated = str_pad($tamAmount, 2, "0", STR_PAD_LEFT);
       return $transactionCode . $tamTransactionFormated . $amount;
    }


    function getNameTamEname($nomeTitular){
        $nome = trim($nomeTitular);
        $nameTam = strlen($nome);
        $SizeAndName = $nameTam . $nome;
        return (string) $SizeAndName;
    }

    function getCityCityTam($cidadeTitular){
        $cidade = trim($cidadeTitular);
        $cityTam = strlen($cidade);
        $tam = (string) $cityTam;
        $tamFormated =  str_pad($tam, 2, "0", STR_PAD_LEFT );
        $city = $tamFormated. $cidade;
       
        return $city;
    }

   

    function geraCodigoSemCRC($titular, $chavePix, $valor){

          $payload = "000201";
          $merchantAccountInformationCode = "26";
          $idBacen = "0014br.gov.bcb.pix";
          $idChave = "01";
          $merchantCategoryCode = "52040000";
          $transactionCurrency_RealBrasileiro = "5303986";
          $countryCode = "5802BR";
          $merchantNameCode = "59";
          $merchantCity = "60"; 
          $cidadeTitular = "ARAUCARIA";
          $additionalDataField = "62070503123";

          $crc16 = "6304";

        
          $codigoCopiaeColaforCRC = 
                $payload . 
                $merchantAccountInformationCode . getMerchantAccountInformationTam($idBacen, $idChave, $chavePix) . $idBacen . $idChave . getChaveTam($chavePix) . $chavePix . 
                $merchantCategoryCode . 
                $transactionCurrency_RealBrasileiro . getTransactionAmount($valor). 
                $countryCode . 
                $merchantNameCode . getNameTamEname($titular) . 
                $merchantCity . getCityCityTam($cidadeTitular) . 
                $additionalDataField .
                $crc16;



        return $codigoCopiaeColaforCRC;

    }



    function crc16($str) {
         function charCodeAt($str, $i) {
            return ord(substr($str, $i, 1));
         }

         $crc = 0xFFFF;
         $strlen = strlen($str);
         for($c = 0; $c < $strlen; $c++) {
            $crc ^= charCodeAt($str, $c) << 8;
            for($i = 0; $i < 8; $i++) {
                  if($crc & 0x8000) {
                     $crc = ($crc << 1) ^ 0x1021;
                  } else {
                     $crc = $crc << 1;
                  }
            }
         }
         $hex = $crc & 0xFFFF;
         $hex = dechex($hex);
         $hex = strtoupper($hex);
         $hex = str_pad($hex, 4, '0', STR_PAD_LEFT);

         return $hex;
    }



    function CopiaEcola($titular, $pix, $valor){
        $baseCrc = geraCodigoSemCRC($titular, $pix, $valor);
        $crc16 = (string) crc16($baseCrc);

        $copiaEcola = $baseCrc . $crc16;
        return $copiaEcola;
    }



    function main(){

            echo "Olá, bem vindo ao gerador copia e cola PIX!\n";
            
            echo "Digite o nome do titular: \n";
            $titular = readline();

            echo "Digite a chave PIX sem espaços: \n";
            $pix = readline();

            echo "Digite o valor da transação: \n";
            $valor = readline();
            
            echo "AQUI ESTÁ SEU CÓDIGO PIX COPIA E COLA: \n";

            echo CopiaEcola($titular, $pix, $valor);
            
    }


    main();

