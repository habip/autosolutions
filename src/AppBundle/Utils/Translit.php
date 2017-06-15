<?php

namespace AppBundle\Utils;

class Translit
{
    static $ru = array(
        'а'=>'a',
        'б'=>'b',
        'в'=>'v',
        'г'=>'g',
        'д'=>'d',
        'е'=>'e',
        'ё'=>'yo',
        'ж'=>'zh',
        'з'=>'z',
        'и'=>'i',
        'й'=>'y',
        'к'=>'k',
        'л'=>'l',
        'м'=>'m',
        'н'=>'n',
        'о'=>'o',
        'п'=>'p',
        'р'=>'r',
        'с'=>'s',
        'т'=>'t',
        'у'=>'u',
        'ф'=>'f',
        'х'=>'kh',
        'ц'=>'c',
        'ч'=>'ch',
        'ш'=>'sh',
        'щ'=>'sch',
        'ъ'=>'',
        'ы'=>'y',
        'ь'=>'',
        'э'=>'e',
        'ю'=>'yu',
        'я'=>'ya');

    public function translit($text)
    {
        $ruText = str_replace(array_keys(self::$ru), array_values(self::$ru), $text);
        if ($ruText != $text) {
            return $ruText;
        }

        return $text;
    }
}