<?php

/**
 * Agrupa métodos para geração de HTML a partir de Objetos e listas de objetos
 * @author Vinicius Fiorio Custodio - Samusdev@gmail.com
 * @version v 1.0.1
 * @copyright GPL - General Public License
 * @license http://www.gnu.org
 * @link http://www.Samus.com.br
 * @category CRUD
 * @package CRUD
 */
class Samus_CRUD_HTMLCodeObjects {

    const deleteConfirmMsg = 'Realmente deseja excluir?';
    const deleteAction = "excluir";
    const editAction = 'editar';
    const confirmBtnName = 'Confirmar';
    const editBtnName = '[Editar]';
    const deleteBtnName = '[Excluir]';

    /**
     * Obtem uma tabela escrita em html bem formatada de todos os dados
     * retornados, para customização da tabela é possível especificar classes
     * css prédefinidas para estilização de cores, são as seguintes
     *
     * crud-table 			(aplicado ao elemento table)
     * crud-table-tr-color1	(aplicado ao elemento tr - linhas)
     * crud-table-tr-color2	(alternando com a cor1 esta classe tbm é aplicada ao tr)
     *
     * crud-table-td-color1	(aplicado ao elemento td - colunas)
     * crud-table-td-color2	(aplicado ao elemento td - colunas)
     *
     *
     *
     * @param string|int $whereCondition
     * @param string $order
     * @param string|int $limit
     * @param string $cssClass
     * @param string $cssClassCor1
     * @param string $cssClassCor2
     * @return string
     */
    public static function htmlTableFromData($object , $whereCondition = "", $order = "", $limit = ""
            , $cssClass="crud-table" , $cssClassTRCor1="crud-table-tr-color1" , $cssClassTRCor2="crud-table-tr-color2"
            , $cssClassTDCor1="crud-table-td-color1" , $cssClassTDCor2="crud-table-td-color2") {

        $array = $object->getDao()->find($whereCondition , $order , $limit);
        $str = "\n<table class='$cssClass' cellpadding='0' cellspacing='0' ><thead>";

        $head = "";
        $head .= "<tr>";
        $ai = new ArrayIterator($array[0]);
        while($ai->valid()) {
            $head .= "<th>".Util_String::underlineToSpace(Util_String::upperToSpace($ai->key()))."</th>";
            $ai->next();
        }
        $head .= "</tr>";

        $str .= $head;

        $str .= "</thead><tbody>";

        $cont = 0;
        foreach($array as $a) {
            if($cont%2==0) {
                $str .= "<tr class='$cssClassTRCor1'>";
            } else {
                $str .= "<tr class='$cssClassTRCor2'>";
            }

            $cont2 = 0;
            foreach($a as $val) {
                $class="";
                if($cont2%2==0) {
                    $class = $cssClassTDCor1;
                } else {
                    $class = $cssClassTDCor2;
                }

                $str .= "<td class='$class'>";
                if(empty($val)) {
                    $val = '&nbsp';
                }
                $str .= $val;

                $str .= "</td>";
                ++$cont2;
            }
            $str .= "</tr>";
            ++$cont;
        }

        $str .=   "</tbody><tfoot>$head</tfoot></table>\n";

        return $str;
    }


    /**
     * Obtem um formulário simples para um objeto DAO
     *
     * @param object $object
     * @return string
     */
    public static function htmlForm($object , $objectName='obj') {
        $crud = $object->getDao()->myCRUD();
        $str = "
<fieldset>
	<legend>".ucwords(Util_String::upperToSpace($crud->getClassName()))."</legend>
        ";
        $str .= "<form method='post' action=''>
        ";

        foreach($crud->getAtributes() as $atr) {

            if($atr == "id") {
                $str .= "<input type='hidden' name='id' value='".Samus::VIEWS_LEFT_DELIMITER." $". $objectName. "->$atr".Samus::VIEWS_RIGHT_DELIMITER."' />
                ";
            } else {

                $str .= "<label for='input-$atr'>".Util_String::underlineToSpace(Util_String::upperToSpace($atr)).'</label>
                ';
                $str .= "<input type='text' name='$atr' id='input-$atr'  value='".Samus::VIEWS_LEFT_DELIMITER." $". $objectName. "->$atr ".Samus::VIEWS_RIGHT_DELIMITER."' />
		<br />
                
                ";
            }

        }

        $str .= "
        <label for='action'></label>
        <input type='submit' name='action' value='".self::confirmBtnName."' />
        ";

        $str .= "</form>
</fieldset>
        ";
        return $str;
    }

    /**
     * Monta uma lista de objetos seguindo um padrão simples, para configurar
     * alguns dos parametros modifique os atributos staticos da classe
     *
     * @param string $object instancia do objeto que será listado
     * @param string $url url para ações de editar e excluir
     * @param string $objectIdentifierProperty nome da propriedade que identificara a lista de objetos na lista
     * @param string $objectArrayName nome da variavel que contem a lsita de objetos
     * @return string
     */
    public static function editList($object , $url , $objectIdentifierProperty='name' , $objectArrayName='objArray' ) {

        $left = Samus::VIEWS_LEFT_DELIMITER;
        $right = Samus::VIEWS_RIGHT_DELIMITER;


        $str = '
            <div>
            <div>List</div>
            <form method="post" action="">';

        $str .= "$left foreach from=$$objectArrayName item=o $right
        ";

        $str .= "
        <div>
        <input type='checkbox' name='ids[]' value='$left \$o->id $right' />
        <strong>#$left  \$o->id $right </strong> $left".' $o->'.$objectIdentifierProperty." $right
        ";

        $str .= "<a href='$url.".self::editAction."=$left \$o->id $right'>".self::editBtnName."</a>
            ";
        $str .= "<a href='$url.".self::deleteAction."=$left \$o->id $right' onclick='javascript: confirm(\" ".self::deleteConfirmMsg." \")' >".self::deleteBtnName."</a>";

        $str .= '
            <hr />
            </div>
            ';

        $str .= "$left /foreach $right";

        $str .= '
            <div><input type="submit" value="'.self::confirmBtnName.'" /></div>
            ';

        $str .= '</form>
            </div>';

        //$str .= '</div>';
        return $str;
    }

    public static function simpleAdmin($object , $url , $objectIdentifierProperty='name' , $objectArrayName='objArray' , $objectName='obj') {
        $str = '<div>
                <div>Form</div>
                ';


        $str .= self::htmlForm($object, $objectName);


        $str .= '</div>';



        $str .= '<div>
                <div>Edit</div>
                ';
        $str .= self::editList($object, $url, $objectIdentifierProperty, $objectArrayName, $objectName );
        $str .= '</div>';

        return $str;

    }


}


?>