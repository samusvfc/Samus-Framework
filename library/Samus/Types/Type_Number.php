<?php
require_once 'Samus/types/DataTypeInterface.php';
/**
 * Representa um número
 *
 * @author w
 */
class Type_Number extends Samus_Object implements DataTypeInterface {

    public $num;

    const SUM_NEUTRAL_VALUE      = 0;
    const SUBTRACT_NEUTRAL_VALUE = 0;
    const MULTIPLY_NEUTRAL_VALUE = 1;
    const DIVIDE_NEUTRAL_VALUE   = 1;

    public function  __construct($num=null) {
        if(!empty($num)) {
            $this->num = $num;
        }
    }

    public function __tostring() {
        return $this->num;
    }

    /**
     * Realiza a soma de 1 ou mais elementos
     * @param float $num1
     * @param float $num2 ...
     * @return Number
     */
    public function sum($num1) {
        if(empty($this->num)) {
            $this->num = self::SUM_NEUTRAL_VALUE;
        }

        foreach(func_get_args() as $arg) {
            $this->num += (float) $arg;
        }

        return $this;
    }


    /**
     * Realiza a soma de 1 ou mais elementos
     * @param float $num1
     * @param float $num2 ...
     * @return Number
     */
    public function subtract($num1) {
        if(empty($this->num)) {
            $this->num = self::SUBTRACT_NEUTRAL_VALUE;
        }

        foreach(func_get_args() as $arg) {
            $this->num -= (float) $arg;
        }

        return $this;
    }

    /**
     * Realiza a multiplicação de 1 ou mais elementos
     * @param float $num1
     * @param float $num2 ...
     * @return Number
     */
    public function multiply($num1) {
        if(empty($this->num)) {
            $this->num = self::MULTIPLY_NEUTRAL_VALUE;
        }

        foreach(func_get_args() as $arg) {
            $this->num *= (float) $arg;
        }

        return $this;
    }

    /**
     * Realiza a divisão de 1 ou mais elementos
     * @param float $num1
     * @param float $num2 ...
     * @return Number
     */
    public function divide($num1) {
        if(empty($this->num)) {
            $this->num = self::MULTIPLY_NEUTRAL_VALUE;
        }

        foreach(func_get_args() as $arg) {
            $this->num /= (float) $arg;
        }

        return $this;
    }

    /**
     * Realiza o calculo de modulo de um ou vários elemenots
     * @param float $num1
     * @param float $num2 ...
     * @return Number
     */
    public function module($num1) {
        if(empty($this->num)) {
            $this->num = self::MULTIPLY_NEUTRAL_VALUE;
        }

        foreach(func_get_args() as $arg) {
            $this->num %= (float) $arg;
        }

        return $this;
    }

    /**
     * Calcula a potencia de um numero
     * @param float $num1
     * @param float $num2 ...
     * @return Number
     */
    public function pow($num1) {
        if(empty($this->num)) {
            $this->num = self::MULTIPLY_NEUTRAL_VALUE;
        }

        foreach(func_get_args() as $arg) {
            $this->num = pow($this->num, (float) $arg);
        }

        return $this;
    }

    /**
     * Calcula a raiz quadrada de um numero
     * @param float $num1
     * @param float $num2 ...
     * @return Number
     */
    public function sqrt($num1) {
        $this->num = sqrt($this->num);
        return $this;
    }



    /**
     * Arredonda um número
     * @param int $precision casas decimais
     * @return Number
     */
    public function round($precision=2) {
        $this->num = round($this->num);
        return $this;
    }



    /**
     * Obtem o valor inteiro atual
     * @return int
     */
    public function getInteger() {
        return (int) $this->num;
    }

    /**
     * Obtem um float do valor atual
     * @return float
     */
    public function getFloat() {
        return (float) $this->num;
    }

    /**
     * Obtem o double do valor atual
     * @return double
     */
    public function getDouble() {
        return (double) $this->num;
    }



    /**
    * abs ? Valor absoluto

    * Descrição
    * number abs ( mixed $number )

    * Retorna o valor absoluto do numero
     * @return Number
     */
    public function abs() {
        $this->num = abs($this->num);
        return $this;
    }

    /**
     * Cosseno Inverso (arco cosseno)     *
     *
     * Retorna o inverso do cosseno de arg em radianos. acos() é a função
     * complementar de cos(), o que significa que a==cos(acos(a)) para qualquer
     * valor de var que esteja dentro dos limites de acos().
     * @return Number
     */
    public function accos() {
        $this->num = acos($this->num);
        return $this;
    }

    /**
     * Cosseno Hiperbólico Inverso
     * @return Number
     */
    public function acosh() {
        $this->num = acosh($this->num);
        return $this;
    }

    /**
     * Retorna o inverso do seno de arg em radianos. asin() é a função complementar de sin(), o que significa que a==sin(asin(a)) para qualquer valor de var que esteja dentro dos limites de asin().
     * @return Number
     */
    public function asin() {
        $this->num = asin($this->num);
        return $this;
    }



    /**
     * Seno Hiperbólico Inverso
     * @return Number
     */
    public function asinh() {
        $this->num = asinh($this->num);
        return $this;
    }

    /**
     * Retorna o inverso da tangente de arg em radianos. atan() é a função
     * complementar de tan(), o que significa que var == tan(atan(var))
     * para qualquer valor de a que esteja dentro dos limites de atan().
     * @return Number
     */
    public function atan() {
        $this->num = atan($this->num);
        return $this;
    }

    /**
     * Tangente hiperbólica inversa
     * return Number
     */
    public function atanh() {
        $this->num = atanh($this->num);
        return $this;
    }

    /**
     * Arredonda frações para cima
     * @return Number
     */
    public function ceil() {
        $this->num = ceil($this->num);
        return $this;
    }

    /**
     * Coseno
     * @return Number
     */
    public function cos() {
        $this->num = cos($this->num);
        return $this;
    }

    /**
     * Cosseno hiperbólico
     * @return Number
     */
    public function cosh() {
        $this->num = cosh($this->num);
        return $this;
    }


    /**
     * Calcula o expoente de e
     * @return Number
     */
    public function exp() {
        $this->num = exp($this->num);
        return $this;
    }


    /**
     * floor ? Arredonda frações para baixo
     * @return Number
     */
    public function floor() {
        $this->num = floor($this->num);
        return $this;
    }

    /**
     * Calcula o tamanho da hipotenusa de um ângulo reto do triângulo Retorna a raiz quadrada de (num1*num1 + num2*num2)
     * @return Number
     */
    public function hypot($x=0 , $y=0) {
        if($x==0) {
            $x = $this->num;
        }
        $this->num = hypot($x , $y);
        return $this;
    }

    /**
     * Gera um numero aleatorio com o Gerador melhorado de números aleatórios
     * @return Number
     */
    public function randomize($min=null , $max=null) {
        if(empty($min) && empty($max)) {
            $this->num = mt_rand();
        } elseif(empty($max) && !empty($min)) {
            $this->num = mt_rand($min, $max);
        } elseif(!empty($max) && !empty($min)) {
            $this->num = mt_rand($min, $max);
        }
        return $this;
        
    }

    /**
     * Calcula o seno do numero
     * @return Number
     */
    public function sin() {
        $this->num = sin($this->num);
        return $this;
    }

    /**
     * Calcula o seno hiperbólico do numero
     * @return Number
     */
    public function sinh() {
        $this->num = sinh($this->num);
        return $this;
    }


    /**
     * Obtem uma string do valor atual
     * @return string
     */
    public function getString() {
        return (string) $this->num;
    }

    /**
     * Obtem uma Str
     * @return Str
     */
    public function getStr() {
        require_once 'Samus/types/Str.php';
        return new Str((string) $this->num);
    }


    public function porcent($percentage) {
        $this->num = ($this->num * $percentage)/100;
        return $this;
    }

    /**
     * Calcula a tangente do numero
     * @return Number
     */
    public function tan() {
        $this->num = tan($this->num);
        return $this;
    }

    /**
     * Tangente hiperbólica
     * @return Number
     */
    public function tanh() {
        $this->num = tanh($this->num);
        return $this;
    }

    /**
     * Cast de tipo para Number
     * @param $matrix
     * @return Number
     */
    public static function cast($object) {
        if($object instanceof Number) {
            return $object;
        } else {
            return new Number((float) $object);
        }
    }

    /**
     * Obtem uma instancia de Number
     * @return  Number
     */
    public static function getInstance($number="") {
        return new Number($number);
    }

    /**
     * @see DataTypeInterface::getType()
     */
    public function getType() {
        return "Number";
    }

}
?>
