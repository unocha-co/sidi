<?
/**
* Maneja todas las propiedades del Objeto ParqueNat
* Valores de Objeto VO
* @author Ruben A. Rojas C.
*/

Class ParqueNat {

  /**
  * Identificador
  * @var int
  */
	var $id;
	
  /**
  * Nombre de la ParqueNat
  * @var string
  */	
	var $nombre;
	
  /**
  * ID de los departamentos que forman la region
  * @var Array
  */	
	var $id_deptos;

  /**
  * ID de los municipios que forman la region
  * @var Array
  */	
	var $id_muns;
	
  /**
  * Nombre de los municipios que forman la region
  * @var Array
  */	
	var $nom_muns;	
	
}

?>