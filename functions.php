<?php

/*
Se define esta funcion para formatear el precio de los productos
la funcion va a ser llamada desde el template
*/
function formatPrice(float $vlprice){

    return number_format($vlprice, 2, ",", ".");

}

?>