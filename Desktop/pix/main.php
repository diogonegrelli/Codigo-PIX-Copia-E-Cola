<?php
    require 'checkPix.php';
    $jsonData = file_get_contents("constant.json");
    $constantData = json_decode($jsonData, true);
    
    function verifyJsonFile(){
        global $constantData;
        if ($constantData === false || empty($constantData))  {
            return false;
        }
        $Keys = [  "payload", "merchantAccountInformationCode", "idBacen", "idChavePix", "merchantCategoryCode", "transactionCurrency_RealBrasileiro","countryCode", "merchantNameCode","merchantCity", "city","additionalDataField", "crc16"];
        foreach ($Keys as $key){
            if (!isset($constantData[$key]) || empty($constantData[$key])){
                return false;
            }
        }
        return true;
    }
    


    function pixSizeField($chave){
        $pixSize = strlen($chave);
        $pixSizeToString = (string) $pixSize;
        $pixSizeFormated = str_pad($pixSizeToString, 2, "0", STR_PAD_LEFT);
        return $pixSizeFormated;
    }

    function merchantAccountInformationSize(array $constantData, $chavePix){
        $idBacen = $constantData["idBacen"];
        $idChavePix = $constantData["idChavePix"]; 

        $pixSize = pixSizeField($chavePix);
        $size = $idBacen . $idChavePix . $pixSize . $chavePix;
        $merchantAccountInformationSize = (string) (strlen($size));
                    
        return $merchantAccountInformationSize; 
    }

    function getTransactionAmount($valor){
       $transactionCode = "54"; 
       $amount = number_format($valor, 2, "." , "");
       $tamAmount = (string) strlen($amount);
       $tamTransactionFormated = str_pad($tamAmount, 2, "0", STR_PAD_LEFT);
       return $transactionCode . $tamTransactionFormated . $amount;
    }


    function nameSizeField($nomeTitular){
        $nome = trim($nomeTitular);
        $nameTam = strlen($nome);
        $SizeAndName = $nameTam . $nome;
        return (string) $SizeAndName;
    }

    function citySizeAndCityNameFields(array $constantData){
        $city = $constantData["city"];
        $cityTrimmed = trim($city);
        $citySize = strlen($cityTrimmed);
        $citySizeToString = (string) $citySize;
        $citySizeFormated =  str_pad($citySizeToString, 2, "0", STR_PAD_LEFT );
        $citySizeAndName = $citySizeFormated. $cityTrimmed;
       
        return $citySizeAndName;
    }


    function generateCrc16($str) {
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


    function pixCodeWithCRC(array $constantData, array $arrayPix){ 
        $nomeTitular = $arrayPix[1];
        $chavePix = validatePix($arrayPix[2]);
        $valor = $arrayPix[3];

        
        $codigoCopiaeColaSemCRC = 

                $constantData["payload"] .
                $constantData["merchantAccountInformationCode"] . merchantAccountInformationSize($constantData, $chavePix). 
                $constantData["idBacen"] . $constantData["idChavePix"] . pixSizeField($chavePix). $chavePix .
                $constantData["merchantCategoryCode"].
                $constantData["transactionCurrency_RealBrasileiro"]. getTransactionAmount($valor) .
                $constantData["countryCode"].
                $constantData["merchantNameCode"]. nameSizeField($nomeTitular).
                $constantData["merchantCity"] . citySizeAndCityNameFields($constantData).
                $constantData["additionalDataField"].
                $constantData["crc16"];

            $crc16 = generateCrc16($codigoCopiaeColaSemCRC);

            return $codigoCopiaeColaSemCRC . $crc16; 
    }


    function main(){
        global $constantData; 
        if(verifyJsonFile($constantData)){
            $arrayPix = [1=> "Nome do titular da conta: ", 2=>"Chave pix: ", 3=>"Valor: "];
            foreach ($arrayPix as $key => $var){
                    echo $var; 
                    $arrayPix[$key] = trim(readline());
            }

            if (validatePix($arrayPix[2])){
                echo pixCodeWithCRC($constantData, $arrayPix) . "\n";
            }
            else{
                echo "Chave Inválida!";
            }
        }
        else{
            echo "Arquivo JSON inválido!";

        }

    }   

    
    
     while (true) {
        echo "Bem-vindo(a) ao gerador copia e cola PIX! Digite 1 para iniciar: ";
        $option = trim(readline());
            if ($option == 1){
                main(); 
                break;
            }            
            else{
                echo "Opção inválida! Digite novamente. \n";    
            }
    }
    
