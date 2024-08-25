<?php

namespace App\Libraries;

class FormatterNumberLetter
{
    /**
     * @var array
     */
    private $units  = [
        '',
        'UNO ',
        'DOS ',
        'TRES ',
        'CUATRO ',
        'CINCO ',
        'SEIS ',
        'SIETE ',
        'OCHO ',
        'NUEVE ',
        'DIEZ ',
        'ONCE ',
        'DOCE ',
        'TRECE ',
        'CATORCE ',
        'QUINCE ',
        'DIECISÉIS ',
        'DIECISIETE ',
        'DIECIOCHO ',
        'DIECINUEVE ',
        'VEINTE ',
    ];

    /**
     * @var array
     */
    private $teens = [
        'VEINTI',
        'TREINTA ',
        'CUARENTA ',
        'CINCUENTA ',
        'SESENTA ',
        'SETENTA ',
        'OCHENTA ',
        'NOVENTA ',
        'CIEN ',
    ];

    /**
     * @var array
     */
    private $tens  = [
        'CIENTO ',
        'DOSCIENTOS ',
        'TRESCIENTOS ',
        'CUATROCIENTOS ',
        'QUINIENTOS ',
        'SEISCIENTOS ',
        'SETECIENTOS ',
        'OCHOCIENTOS ',
        'NOVECIENTOS ',
    ];

    /**
     * @var array
     */
    private $exception_accents = [
        'VEINTIDOS'  => 'VEINTIDÓS ',
        'VEINTITRES' => 'VEINTITRÉS ',
        'VEINTISEIS' => 'VEINTISÉIS ',
    ];

    /**
     * @var string
     */
    public $conector = 'CON';

    /**
     * @var bool
     */
    public $apocope = false;
   
    /**
     * Formatea y convierte un número a letras en formato facturación electrónica.
     *
     * @param int|float $number
     * @param int       $decimals
     * @param string    $currency
     *
     * @return string
     */
    public function to_invoice($number, $decimals = 2, $currency = '')
    {
        $this->check_apocope();

        $number = number_format($number, $decimals, '.', '');

        $split_number = explode('.', $number);

        $split_number[0] = $this->whole_number($split_number[0]);

        if (!empty($split_number[1])) {
            $split_number[1] .= '/100 ';
        } else {
            $split_number[1] = '00/100 ';
        }

        return $this->glue($split_number) . mb_strtoupper($currency, 'UTF-8');
    }

    /**
     * Valida si debe aplicarse apócope de uno.
     *
     * @return void
     */
    private function check_apocope()
    {
        if ($this->apocope === true) {
            $this->units [1] = 'UN ';
        }
    }

    /**
     * Formatea la parte entera del número a convertir.
     *
     * @param string $number
     *
     * @return string
     */
    private function whole_number($number)
    {
        if ($number == '0') {
            $number = 'CERO ';
        } else {
            $number = $this->convert_number($number);
        }

        return $number;
    }

    /**
     * Concatena las partes formateadas del número convertido.
     *
     * @param array $split_number
     *
     * @return string
     */
    private function glue($split_number)
    {
        return implode(' ' . mb_strtoupper($this->conector, 'UTF-8') . ' ', array_filter($split_number));
    }

    /**
     * Convierte número a letras.
     *
     * @param string $number
     *
     * @return string
     */
    private function convert_number($number)
    {
        $converted = '';

        if (($number < 0) || ($number > 999999999)) {
            throw new ParseError('Wrong parameter number');
        }

        $numberStrFill = str_pad($number, 9, '0', STR_PAD_LEFT);
        $millones = substr($numberStrFill, 0, 3);
        $miles = substr($numberStrFill, 3, 3);
        $cientos = substr($numberStrFill, 6);

        if (intval($millones) > 0) {
            if ($millones == '001') {
                $converted .= 'UN MILLÓN ';
            } elseif (intval($millones) > 0) {
                $converted .= sprintf('%sMILLONES ', $this->convert_group($millones));
            }
        }

        if (intval($miles) > 0) {
            if ($miles == '001') {
                $converted .= 'MIL ';
            } elseif (intval($miles) > 0) {
                $converted .= sprintf('%sMIL ', $this->convert_group($miles));
            }
        }

        if (intval($cientos) > 0) {
            if ($cientos == '001') {
                $this->apocope === true ? $converted .= 'UN ' : $converted .= 'UNO ';
            } elseif (intval($cientos) > 0) {
                $converted .= sprintf('%s ', $this->convert_group($cientos));
            }
        }

        return trim($converted);
    }

    /**
     * @param string $n
     *
     * @return string
     */
    private function convert_group($n)
    {
        $output = '';

        if ($n == '100') {
            $output = 'CIEN ';
        } elseif ($n[0] !== '0') {
            $output = $this->tens [$n[0] - 1];
        }

        $k = intval(substr($n, 1));

        if ($k <= 20) {
            $units  = $this->units [$k];
        } else {
            if (($k > 30) && ($n[2] !== '0')) {
                $units  = sprintf('%sY %s', $this->teens[intval($n[1]) - 2], $this->units [intval($n[2])]);
            } else {
                $units  = sprintf('%s%s', $this->teens[intval($n[1]) - 2], $this->units [intval($n[2])]);
            }
        }

        $output .= array_key_exists(trim($units ), $this->exception_accents) ?
            $this->exception_accents[trim($units )] : $units ;

        return $output;
    }
}