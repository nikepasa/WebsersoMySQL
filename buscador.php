<?php	
	// Función para validar en formato dd/mm/aaaa devolviendo true/false
	function valFechaDMY($cadena){
		//dd / mm / yyyy
		//01 2 34 5 6789
		$dia = str_pad((int) substr($cadena,0,2), 2, "0", STR_PAD_LEFT);
		$mes = str_pad((int) substr($cadena,3,2), 2, "0", STR_PAD_LEFT);
		$anio = str_pad((int) substr($cadena,6,4), 4, "0", STR_PAD_LEFT);

		return ($dia>0 && $dia<=31 && $mes>0 && $mes<=12 && $anio>=1970 && @checkdate($mes, $dia, $anio));
	}
	
	// Función para convertir fechas de formato dd/mm/aaaa a Y-m-D de mysql
	function fechaMysql($cadena) {
		//tenemos que pasar de d/m/y a Y-m-D
		//dd / mm / yyyy
		//01 2 34 5 6789
		$dia = substr($cadena,0,2);
		$mes = substr($cadena,3,2);
		$anio = substr($cadena,6,4);
		$nuevacadena = $anio.'-'.$mes.'-'.$dia;
		return $nuevacadena;       
	}
	
	// inicializamos los datos
	$precio = $_POST['precio'];
	$sinavion = $_POST['sinavion'];
	$fecha = $_POST['fecha'];

	$navegador = $_SERVER['HTTP_USER_AGENT'];
	
	
	// si se intenta hacer una búsqueda sin haber rellenado el form, pedimos datos
	if(($precio == null) && ($fecha == null))
		echo "Por favor, añada algún parámetro para iniciar la búsqueda.";
	else {
	/************************ CONEXION A LA BD *****************************/
	$conexion = mysql_connect('localhost', "root", "")
				or exit('No se pudo conectar con el servidor');
		// Abrimos la base de datos
		$abreBD = mysql_select_db('webimserso', $conexion);
		if (!$abreBD) {
			die('No se pudo abrir la base de datos.Error: ' . mysql_error());
		}
		mysql_set_charset('utf8');
	/************************ CONEXION A LA BD *****************************/

		
		$preciovalido = false;
		$fechavalida = false;
		$avionchecked = false;
		
		// Cogemos el ID del precio consultado, si existe
		if($precio != null)
			//validamos el precio insertado, para evitar que se añada cualquier cosa que no sea un número
			if(!is_numeric($precio)){
				echo "Variable precio no válido. Debe ser un número.";
			}else{
				$preciomax = 'SELECT ID FROM viajes WHERE Precio<=' . $precio;
				$preciovalido = true;
				}

		// Cogemos el ID de la fecha consultada, si existe
		if($fecha != null){
			// el tipo "date" es nuevo en HTML5, y por tanto sólo es soportado por Chrome
			// por ese motivo, si el navegador no es Google Chrome, debemos validar la fecha y convertirla al formato de la BD
			// (cosa que ya hace por defecto el tipo "date" cuando es soportado)
			// $navegador = $_SERVER['HTTP_USER_AGENT'];
			if(!preg_match('/Chrome/i',$navegador)){
				if(!valFechaDMY($fecha)){
					echo "Formato de fecha erróneo. Utilizar el formato dd/mm/aaaa. Por ejemplo: 14/09/2014.";
				}else{
					$fecha = fechaMysql($fecha);
					$fechavalida = true;
				}	
			}else
				$fechavalida = true;
			if($fechavalida)
				$fechaSal = 'SELECT ID FROM buscar WHERE fechaSalida="' . $fecha . '"';
		}
		
		// vemos si se quieren solo viajes sin avion o no
		if (($sinavion) == "true"){
			$vuelossinavion = 'SELECT ID FROM buscar WHERE avionObligatorio="NO"';
			$avionchecked = true;
		}
	
		// hacemos la consulta según los datos que se hayan añadido
		$total = 'SELECT * FROM viajes';
		if($preciovalido){
			$total = $total . ' WHERE ID in (' . $preciomax . ')';
			if($fechavalida)
				$total = $total . ' AND ID in (' . $fechaSal . ')';
			if($avionchecked)
				$total = $total . ' AND ID in (' . $vuelossinavion . ')';
		}elseif($fechavalida){
				$total = $total . ' WHERE ID in (' . $fechaSal . ')';
				if($avionchecked)
					$total = $total . ' AND ID in (' . $vuelossinavion . ')';
		}

		// si al menos una de las 2 cadenas principales es válida...		
		if($fechavalida || $preciovalido){
			// ...almacenamos la consulta en un query
			$resultado = mysql_query($total, $conexion);
			$numFilas = mysql_num_rows($resultado);
				
			// y mostramos el resultado por pantalla
			if (mysql_num_rows($resultado) == 0) {
				echo '<b>No hay sugerencias</b>';
			} else {
				echo '<b>Sugerencias:</b>';
				while ($fila = mysql_fetch_array($resultado)) {
					$tipoViaje = substr($fila['ID'], 0, 3);
					echo "<li>" . $fila['Titulo'] . ": <a href=\"./?p=$fila[0]\"><img src=\"$fila[11]\" alt=\"Img\"></a></li>
						Precio: " . $fila['Precio'] . "€";
				}
			}
		}
	}
?>