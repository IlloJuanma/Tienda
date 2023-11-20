<?php
class productoCesta
{
    public int $idProducto;

    public int $idCesta;

    public int $cantidad;

    function __construct($idProducto, $idCesta, $cantidad)
    {
        $this->idProducto = $idProducto;
        $this->idCesta = $idCesta;
        $this->cantidad = $cantidad;
    }
}
