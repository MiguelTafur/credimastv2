<?php

    class RutasModel extends Mysql
    {
	    PRIVATE $strNombre;
        PRIVATE $intIdRuta;
        PRIVATE $strDia;

        public function __construct()
        {
            parent::__construct();
        }
        
        public function insertRuta(string $nombre, string $dia)
        {
            $this->strNombre = $nombre;
            $this->strDia = $dia;

            $query_insert = "INSERT INTO ruta(nombre, datecreated) VALUES(?,?)";
            $arrData = array($this->strNombre, $this->strDia);
            $request_insert = $this->insert($query_insert, $arrData);

            return $request_insert;
        }

        public function selectRutas()
        {
            $sql = "SELECT idruta as codigo, nombre, datecreated as pagamento FROM ruta WHERE estado = 1";
            $request = $this->select_all($sql);
            return $request;
        }

        public function selectRuta(int $idruta)
        {
            $this->intIdRuta = $idruta;
            $sql = "SELECT idruta as codigo, nombre, datecreated as pagamento FROM ruta WHERE idruta = $this->intIdRuta";
            $request = $this->select($sql);
            return $request;
        }

        public function updateRuta(int $codigo, string $nombre, string $dia)
        {
            $this->intIdRuta = $codigo;
            $this->strNombre = $nombre;
            $this->strDia = $dia;

            $sql = "UPDATE ruta SET nombre = ?, datecreated = ? WHERE idruta = $this->intIdRuta";
            $arrData = array($this->strNombre, $this->strDia);
            $request = $this->update($sql, $arrData);

            return $request;
        }

        public function deleteRuta(int $codigo)
        {
            $this->intIdRuta = $codigo;

            $sqlPr = "SELECT * FROM ruta ru INNER JOIN persona pe ON(ru.idruta = pe.codigoruta) WHERE pe.codigoruta = $this->intIdRuta AND pe.rolid = 1 AND ru.estado = 1 AND pe.status = 1";
            $requestPr = $this->select_all($sqlPr);
            
            if(empty($requestPr)){
                $sql = "UPDATE ruta SET estado = ? WHERE idruta = $this->intIdRuta";
                $arrData = array(0);
                $request = $this->update($sql, $arrData);
            }else{
                $request = "0";	
            }

            return $request;
        }
    }
?>