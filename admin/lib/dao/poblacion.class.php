<?
/**
 * DAO de Poblacion
 *
 * Contiene los m�todos de la clase Poblacion
 * @author Ruben A. Rojas C.
 */

Class PoblacionDAO {

	/**
	 * Conexi�n a la base de datos
	 * @var object
	 */
	var $conn;

	/**
	 * Nombre de la Tabla en la Base de Datos
	 * @var string
	 */
	var $tabla;

	/**
	 * Nombre de la columna ID de la Tabla en la Base de Datos
	 * @var string
	 */
	var $columna_id;

	/**
	 * Nombre de la columna Nombre de la Tabla en la Base de Datos
	 * @var string
	 */
	var $columna_nombre;

	/**
	 * Nombre de la columna para ordenar el RecordSet
	 * @var string
	 */
	var $columna_order;

	/**
	 * Constructor
	 * Crea la conexi�n a la base de datos
	 * @access public
	 */
	function PoblacionDAO (){
		$this->conn = MysqlDb::getInstance();
		$this->tabla = "poblacion";
		$this->columna_id = "ID_POBLA";
		$this->columna_nombre = "NOM_POBLA_ES";
		$this->columna_order = "NOM_POBLA_ES";
	}

	/**
	 * Consulta los datos de una Poblacion
	 * @access public
	 * @param int $id ID del Poblacion
	 * @return VO
	 */
	function Get($id){
		$sql = "SELECT * FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = $this->conn->FetchObject($rs);

		//Crea un VO
		$poblacion_vo = New Poblacion();

		//Carga el VO
		$poblacion_vo = $this->GetFromResult($poblacion_vo,$row_rs);

		//Retorna el VO
		return $poblacion_vo;
	}

	/**
	 * Consulta Vos
	 * @access public
	 * @param string $condicion Condici�n que deben cumplir los Tema y que se agrega en el SQL statement.
	 * @param string $limit Limit en el SQL
	 * @param string $order by Order by en el SQL 
	 * @return array Arreglo de VOs
	 */	
	function GetAllArray($condicion,$limit='',$order_by=''){

		$sql = "SELECT * FROM ".$this->tabla;

		if ($condicion != "") $sql .= " WHERE ".$condicion;

		//ORDER
		$sql .= ($order_by != "") ?  " ORDER BY $order_by" : " ORDER BY ".$this->columna_order;

		//LIMIT
		if ($limit != "") $sql .= " LIMIT ".$limit;

		$array = Array();

		$rs = $this->conn->OpenRecordset($sql);
		while ($row_rs = $this->conn->FetchObject($rs)){
			//Crea un VO
			$vo = New Poblacion();
			//Carga el VO
			$vo = $this->GetFromResult($vo,$row_rs);
			//Carga el arreglo
			$array[] = $vo;
		}  
		//Retorna el Arreglo de VO
		return $array;
	}

	/**
	 * Retorna el numero de Registros
	 * @access public
	 * @return int
	 */
	function numRecords($condicion){
		$sql = "SELECT count(".$this->columna_id.") as num FROM ".$this->tabla;
		if ($condicion != ""){
			$sql .= " WHERE ".$condicion;
		}
		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = $this->conn->FetchRow($rs);

		return $row_rs[0];
	}

	/**
	 * Lista los Poblacion que cumplen la condici�n en el formato dado
	 * @access public
	 * @param string $formato Formato en el que se listar�n los Poblacion, puede ser Tabla o ComboSelect
	 * @param int $valor_combo ID del Poblacion que ser� selccionado cuando el formato es ComboSelect
	 * @param string $condicion Condici�n que deben cumplir los Poblacion y que se agrega en el SQL statement.
	 */
	function ListarCombo($formato,$valor_combo,$condicion){
		$arr = $this->GetAllArray($condicion,'','');
		$num_arr = count($arr);
		$v_c_a = is_array($valor_combo);

		for($a=0;$a<$num_arr;$a++){
			$vo = $arr[$a];

			if ($valor_combo == "" && $valor_combo != 0)
				echo "<option value=".$vo->id.">".$vo->nombre_es."</option>";
			else{
				echo "<option value=".$vo->id;

				if (!$v_c_a){
					if ($valor_combo == $vo->id)
						echo " selected ";
				}
				else{
					if (in_array($vo->id,$valor_combo))
						echo " selected ";
				}

				echo ">".$vo->nombre_es."</option>";
			}
		}
	}

	/**
	 * Lista los Poblacion en una Tabla
	 * @access public
	 */
	function ListarTabla($condicion){

		include_once ("lib/common/layout.class.php");

		$layout = new Layout();

		$layout->adminGrid(array('nombre_es' => array ('titulo' => 'Nombre en Espa&ntilde;ol'), 'nombre_in' => array('titulo' => 'Nombre en Ingl&eacute;s')));
	}

	/**
	 * Reportar
	 * @access public
	 */
	function Reportar(){

		$arr = $this->GetAllArray('','','');

		echo "<table align='center' width='700' class='tabla_lista'>
			<tr><td>&nbsp;</td></tr>
			<tr class='titulo_lista'><td align='center' colspan=4><b>POBLACIONES EN EL SISTEMA</b></td></tr>
			<tr><td>&nbsp;</td></tr>";

		foreach($arr as $vo){

			echo "<tr class='fila_lista'>";
			echo "<td>".$vo->nombre_es."</td>";
			echo "</tr>";
		}

		echo "</table>";
	}

	/**
	 * Carga un VO de Poblacion con los datos de la consulta
	 * @access public
	 * @param object $vo VO de Poblacion que se va a recibir los datos
	 * @param object $Resultset Resource de la consulta
	 * @return object $vo VO de Poblacion con los datos
	 */
	function GetFromResult ($vo,$Result){
		
		$vo->id = $Result->{$this->columna_id};
		$vo->nombre_es = $Result->{$this->columna_nombre};
		$vo->nombre_in = $Result->NOM_POBLA_IN;

		return $vo;
	}

	/**
	 * Inserta un Poblacion en la B.D.
	 * @access public
	 * @param object $poblacion_vo VO de Poblacion que se va a insertar
	 */
	function Insertar($poblacion_vo){
		//CONSULTA SI YA EXISTE
		$cat_a = $this->GetAllArray($this->columna_nombre." = '".$poblacion_vo->nombre_es."'",'','');
		if (count($cat_a) == 0){
			$sql =  "INSERT INTO ".$this->tabla." (".$this->columna_nombre.",NOM_POBLA_IN) VALUES ('".$poblacion_vo->nombre_es."','".$poblacion_vo->nombre_in."')";
			$this->conn->Execute($sql);

			//SE EJECUTA INSERT DIECTO
			if ($_POST["return"] == 0){
				echo "Registro insertado con &eacute;xito!";
			}
			//SE EJECUTA INSERT DESDE IMPORT DE ORGS
			else{
				?>
				<script>
					location.href = 'index.php?m_e=org&accion=importar';
				</script>
				<?
			}
		}
		else{
			echo "Error - Existe un registro con el mismo nombre";
		}
	}

	/**
	 * Actualiza un Poblacion en la B.D.
	 * @access public
	 * @param object $poblacion_vo VO de Poblacion que se va a actualizar
	 */
	function Actualizar($poblacion_vo){
		$sql =  "UPDATE ".$this->tabla." SET ";
		$sql .= $this->columna_nombre." = '".$poblacion_vo->nombre_es."',";
		$sql .= "NOM_POBLA_IN = '".$poblacion_vo->nombre_in."'";

		$sql .= " WHERE ".$this->columna_id." = ".$poblacion_vo->id;

		$this->conn->Execute($sql);

	}

	/**
	 * Borra un Poblacion en la B.D.
	 * @access public
	 * @param int $id ID del Poblacion que se va a borrar de la B.D
	 */
	function Borrar($id){

		//BORRA
		$sql = "DELETE FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$this->conn->Execute($sql);

	}
	
	/**
	 * Check de llaves foraneas, para permitir acciones como borrar
	 * @access public
	 * @param int $id ID del registro a consultar
	 */	
	function checkForeignKeys($id){

		$tabla_rel = 'poblacion_org';

		$sql = "SELECT sum(id_pob) FROM $tabla_rel WHERE id_pob = ".$id;
		$rs = $this->conn->OpenRecordset($sql);
		$row = $this->conn->FetchRow($rs);

		$r = ($row[0] > 0) ? true : false;

		return $r;

	}
}

?>