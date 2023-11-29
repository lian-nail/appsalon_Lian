<?php 

namespace Controllers;

use Model\Cita;
use Model\CitaServicio;
use Model\Servicio;

class ApiController {
    public static function index( ) {
        
        $servicios = Servicio::all();
        echo json_encode($servicios);
    } 

    public static function guardar() {
        
        //Almacena la cita y devuelve el id de la cita
        $cita = new Cita($_POST);
        $resultado = $cita->guardar();
        $id = $resultado['id'];

        //Almacena la cita y sus servicios
        $idServicios = explode(',', $_POST['servicios']);

        foreach ($idServicios as $idServicio) {
            $args = [
                'citaId' => $id,
                'servicioId' => $idServicio
            ];
            
            $citaServicio = new CitaServicio($args);
            $citaServicio->guardar();
        }

        //Retornamos una respuesta
        echo json_encode(['resultado' => $resultado]);

        // $resultadoFinal = [
        //     'resultado' => $resultado
        // ];
        // echo json_encode($resultadoFinal);
    }

    public static function eliminar() {
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $cita = Cita::find($id);

            $cita->eliminar();
            header('Location: ' . $_SERVER['HTTP_REFERER']);

        }

    }
}