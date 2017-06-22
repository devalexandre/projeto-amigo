<?php

namespace WebService\Util;

use \DateTime;

class Date {

    public static function parseDate($date)
    {
        $begin = '01/01/1970';
        $newDate = $date - 25569;
        $strData = "+$newDate days";
        return date('d/m/Y', strtotime("$strData", strtotime($begin)));
    }

    public static function converteData($data)
    {
        if ( ! strstr( $data, '/' ) )
        {
            // $data está no formato ISO (yyyy-mm-dd) e deve ser convertida
            // para dd/mm/yyyy
            sscanf( $data, '%d-%d-%d', $y, $m, $d );
            return sprintf( '%d/%d/%d', $d, $m, $y );
        }
        else
        {
            // $data está no formato brasileiro e deve ser convertida para ISO
            sscanf( $data, '%d/%d/%d', $d, $m, $y );
            return sprintf( '%d-%d-%d', $y, $m, $d );
        }
        return false;
    }

    public static function comparaData($data1, $data2, $opcao)
    {
        if ( ! strstr( $data, '/' ) ){
            $data1 = converteData($data1);
            $data2 = converteData($data2);
        }
        $data1 = new DateTime($data1);
        $data2 = new DateTime($data2);
        if ($opcao === '=')
        {
            return $data1 == $data2;
        }
        elseif($opcao === '>')
        {
            return $data1 > $data2;
        }
        elseif($opcao === '<'){
            return $data1 < $data2;
        }
    }

    public static function intervaloData($data1, $data2){
        if ( ! strstr( $data, '/' ) ){
            $data1 = converteData($data1);
            $data2 = converteData($data2);
        }
        $data1 = new DateTime($data1);
        $data2 = new Datetime($data2);
        $intervalo = $data1->diff(data2);
        return $intervalo->format('%R%a');
    }

}
?>
