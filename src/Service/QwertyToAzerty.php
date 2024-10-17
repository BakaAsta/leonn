<?php

namespace App\Service;

class QwertyToAzerty 
{

   
    public function convertAzertyToQwerty($inputText) {
        // Mapping des caractères de AZERTY à QWERTY
        $mapping = [
            '1' => '!',
            '2' => '@',
            '3' => '#',
            '4' => '$',
            '5' => '%',
            '6' => '^',
            '7' => '&',
            '8' => '*',
            '9' => '(',
            '0' => ')',
            '@' => '2',
            '#' => '3',
            '$' => '4',
            '%' => '5',
            '^' => '6',
            '&' => '7',
            '*' => '8',
            '(' => '9',
            ')' => '0',
            'a' => 'q',
            'z' => 'w',
            'q' => 'a',
            'w' => 'z',
            'A' => 'Q',
            'Z' => 'W',
            'Q' => 'A',
            'W' => 'Z',
            "'" => '{',
            '^' => '[',
            '£' => '}',
            '$' => ']',
            'M' => ':',
            'm' => ';',
            ',' => 'm',
            '%' => "'",
            'ù' => "'",
            'µ' => '|',
            '*' => '\\',
            '?' => 'M',
            ',' => 'm',
            '.' => '<',
            ';' => 'm',
            '/' => '>',
            ':' => '.',
            '§' => '?',
            '!' => '/',
            'à' => '0',
            '&' => '1',
            'é' => '2',
            '"' => '3',
            "'" => '4',
            '(' => '5',
            '-' => '6',
            'è' => '7',
            '_' => '8',
            "ç" => '9',
        ];
    
       
    
        // Convertir chaque caractère selon le tableau de mapping
        
    
        return strtr($inputText, $mapping);
    }

}
    
