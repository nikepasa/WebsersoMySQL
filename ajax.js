function loadXMLDoc()
{
	var xmlhttp;
	
	var precio = document.getElementById('precio').value;
	var sinavion = document.getElementById('sinavion').checked;
	var fecha = document.getElementById('fecha').value;


	if (window.XMLHttpRequest)
	{// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
	}
	else
	{// code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}

	xmlhttp.onreadystatechange=function()
	{
		if (xmlhttp.readyState==4 && xmlhttp.status==200)
		{
			document.getElementById("myDiv").innerHTML=xmlhttp.responseText;
		}
	}
	
	xmlhttp.open("POST","buscador.php",true);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	xmlhttp.send("precio="+precio+"&sinavion="+sinavion+"&fecha="+fecha);
}