<?php

    function checkCpf($chavePix){
        $cpf = preg_replace('/[^0-9]/', '', $chavePix);
        $cpfSize = strlen($cpf);
        if($cpfSize != 11){
            return false;
        }
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }
        for ($t = 9; $t < 11; $t++) {
                for ($d = 0, $c = 0; $c < $t; $c++) {
                    $d += $cpf[$c] * (($t + 1) - $c);
                }
                $d = ((10 * $d) % 11) % 10;
                if ($cpf[$c] != $d) {
                    return false;
                }
        }
        return $cpf;     
    }


    function checkCnpj($chavePix){
        $cnpj = preg_replace('/[^0-9]/', '', $chavePix);
        $cnpjSize= strlen($cnpj);
        if ($cnpjSize != 14) {
            return false; 
        }
          
        if (preg_match('/(\d)\1{13}/', $cnpj))
            return false;

        for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++) {
            $soma += $cnpj[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }
        $resto = $soma % 11;

        if ($resto < 2) {
                $digito1 = 0;
        } 
        else {
            $digito1 = 11 - $resto;
        }
          
        for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++) {
            $soma += $cnpj[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }

            $resto = $soma % 11;

            if ($resto < 2) {
                $digito2 = 0;
            } else {
                $digito2 = 11 - $resto;
            }

          
            if(((int)$cnpj[12] == $digito1) && ($cnpj[13] == $digito2)){
                return $cnpj;
            }
            else{
                return false;
            }
    }
    

    function checkTelefone($chavePix){
        $telefoneNumbersOnly = preg_replace('/\D/', '', $chavePix);
        if (preg_match('/^(?:[14689][0-9]|2[12478]|3([1-5]|[7-8])|5([13-5])|7[193-7])9[0-9]{8}$/', $telefoneNumbersOnly)) {
            $brCode = '+55'; 
            $foneWithbrCode = $brCode . $telefoneNumbersOnly; 
            return $foneWithbrCode;
        }
        return false;
    }


    function checkChaveAleatoria($chavePix){    
        $regexChave = '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i';
        if (preg_match($regexChave, $chavePix)){
            return $chavePix;
        }
        else{
            return false;
        }
    }

    function checkEmail($chave){
        if (filter_var($chave, FILTER_VALIDATE_EMAIL)){
            return $chave;
        }
        else{
            return false;
        }

    }

    function validatePix($chave){
        $cpf = checkCpf($chave);
        $cnpj = checkCnpj($chave);
        $telefone = checkTelefone($chave);
        $email = checkEmail($chave);
        $chaveAleatoria = checkChaveAleatoria($chave);

        $validations = [1=> $cpf, 2=> $cnpj, 3=> $telefone, 4=> $chaveAleatoria, 5=> $email];

        foreach ($validations as $key=> $validation){
            if ($validations[$key]){
                return $validations[$key];
            }
        }
        return false;

    }

