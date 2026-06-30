<?php

//LLAMADA AL CONFIGURACION DE LA BASE DE DATOS
include_once '../../assest/config/BDCONFIG.php';

//INICIALIZAR VARIABLES
$HOST="";
$DBNAME="";
$USER="";
$PASS="";

class RVESPECIES_ADO
{
    private $conexion;

    public function __CONSTRUCT()
    {
        try {
            $BDCONFIG = new BDCONFIG();
            $HOST = $BDCONFIG->__GET('HOST');
            $DBNAME = $BDCONFIG->__GET('DBNAME');
            $USER = $BDCONFIG->__GET('USER');
            $PASS = $BDCONFIG->__GET('PASS');

            $this->conexion = new PDO('mysql:host=' . $HOST . ';dbname=' . $DBNAME, $USER, $PASS);
            $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function buscarPorProductor($IDPRODUCTOR)
    {
        try {
            $datos = $this->conexion->prepare("SELECT * FROM fruta_rvespecies WHERE ID_PRODUCTOR = ?;");
            $datos->execute(array((int)$IDPRODUCTOR));
            $resultado = $datos->fetchAll();
            $datos = null;

            return $resultado;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function eliminarPorProductor($IDPRODUCTOR)
    {
        try {
            $query = "DELETE FROM fruta_rvespecies WHERE ID_PRODUCTOR = ?;";
            $this->conexion->prepare($query)
                ->execute(array((int)$IDPRODUCTOR));
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function guardarVariedadesPorProductor($IDEMPRESA, $IDPRODUCTOR, $ARRAYVESPECIES, $IDUSUARIO)
    {
        try {
            $IDPRODUCTOR = (int)$IDPRODUCTOR;
            $IDEMPRESA = (int)$IDEMPRESA;
            $IDUSUARIO = (int)$IDUSUARIO;

            $ARRAYVESPECIES = array_unique(array_filter(array_map('intval', (array)$ARRAYVESPECIES)));

            $this->eliminarPorProductor($IDPRODUCTOR);

            if (!is_array($ARRAYVESPECIES) || count($ARRAYVESPECIES) == 0) {
                return;
            }

            $datos = $this->conexion->prepare("SELECT IFNULL(MAX(NUMERO_RVESPECIES),0) AS NUMERO FROM fruta_rvespecies WHERE ID_EMPRESA = ?;");
            $datos->execute(array($IDEMPRESA));
            $resultado = $datos->fetchAll();
            $datos = null;

            $contador = (int)$resultado[0]['NUMERO'] + 1;

            $query = "INSERT INTO fruta_rvespecies (
                                            NUMERO_RVESPECIES,
                                            ID_VESPECIES,
                                            ID_PRODUCTOR,
                                            ID_EMPRESA,
                                            ID_USUARIOI,
                                            ID_USUARIOM,
                                            INGRESO,
                                            MODIFICACION,
                                            ESTADO_REGISTRO
                                        ) VALUES (?, ?, ?, ?, ?, ?, SYSDATE(), SYSDATE(), 1);";

            foreach ($ARRAYVESPECIES as $IDVESPECIES) {
                if ($IDVESPECIES <= 0) {
                    continue;
                }

                $this->conexion->prepare($query)
                    ->execute(
                        array(
                            $contador,
                            $IDVESPECIES,
                            $IDPRODUCTOR,
                            $IDEMPRESA,
                            $IDUSUARIO,
                            $IDUSUARIO
                        )
                    );

                $contador++;
            }
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
}
