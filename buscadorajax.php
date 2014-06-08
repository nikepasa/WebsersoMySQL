<div class="contenidoBasico">
<form id="buscadorajax" name="buscadorajax"  action="buscador.php" method="POST">
    <h1><b>Buscador de Viajes La Edad Dorada</b></h1>
	<h2>¡Busque aquí los viajes con las características que más le convengan!</h2>

    Fecha de Salida <input type="date" id="fecha" name="fecha" />
    Precio Máximo <input type="text" id="precio" name="precio" onkeyup="loadXMLDoc()" />
    Sólo viajes sin avión <input type="checkbox" id="sinavion" name="sinavion" onclick="loadXMLDoc()"/>
    <button type="button" value="Buscar" id="bus" name="button" onclick="loadXMLDoc()">Buscar</button>
    
    
</form>
<div id="myDiv"></div>
</div>