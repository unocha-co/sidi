<?
//INICIALIZACION DE VARIABLES
$estado_mina_dao = New EstadoMinaDAO();
$estado_mina_vo = New EstadoMina();

if (isset($_GET["accion"])){
	$accion = $_GET["accion"];
}
else if (isset($_POST["accion"])){
	$accion = $_POST["accion"];
}


$id_cat = 0;
if (isset($_GET["id_cat"])){
  $id_cat = $_GET["id_cat"];
}

//Caso de Actualizacion
if (isset($_GET["accion"]) && $_GET["accion"] == "actualizar"){
	$id = $_GET["id"];
  $estado_mina_vo = $estado_mina_dao->Get($id);
}

?>

<form method="POST" onsubmit="submitForm(event);return false;">
	<table class="tabla_insertar">
		<tr><td align="right">Nombre</td><td><input type="text" id="nombre" name="nombre" size="40" value="<?=$estado_mina_vo->nombre;?>" class="textfield" /></td></tr>
		<tr>
		  <td colspan="2" align='center'>
			  <br>
				<input type="hidden" name="accion" value="<?=$accion?>" />
			<input type="hidden" name="id" value="<?=$estado_mina_vo->id;?>" />									
				<input type="submit" name="submit" value="Aceptar" class="boton" onclick="return validar_forma('nombre,Nombre','');" />
			</td>
		</tr>
	</table>
</form>	