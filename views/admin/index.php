<h1 class="nombre-pagina">Panel de Administración</h1>

<?php include_once __DIR__ . '/../templates/barra.php'; ?>

<h2>Buscar Citas</h2>
<div class="busqueda">
    <form class="formulario" action="">
        <div class="campo">
            <label for="fecha">Fecha: </label>
            <input type="date"
                    id="fecha"
                    name="fecha"
                    value="<?php echo $fecha; ?>">
        </div>
    </form>
</div>

<?php 
    if (count($citas) === 0){
        echo "<h2>No hay citas en esta fecha</h2>";
    } 
    
?>

<div id="cita-admin">
    <ul class="citas">
        <?php $idCita = 0;
        foreach ($citas as $key => $cita): 
            if ($idCita !== $cita->id): 
                $preciototal = 0 
        ?>
            <li>
                <p>Id: <span><?php echo $cita->id; ?></span></p>
                <p>Hora: <span><?php echo $cita->hora; ?></span></p>
                <p>Cliente: <span><?php echo $cita->cliente; ?></span></p>
                <p>Telefono: <span><?php echo $cita->telefono; ?></span></p>
                <p>E-mail: <span><?php echo $cita->email; ?></span></p>
                <h3>Servicios: </h3>
            <?php $idCita = $cita->id;  
            endif; //Fin de if 
            $preciototal += $cita->precio;
            ?>
            <p class="servicio"><?php echo $cita->servicio . ' ' . $cita->precio; ?></p>
            <!-- </li> -->
            <?php 
                $actual = $cita->id;
                $proximo = $citas[$key + 1]->id ?? 0;
            ?>
            <?php if ( esUltimo($actual, $proximo) ): ?> 
                <p>Total: <span>$<?php echo $preciototal; ?></span></p>

                <form action="/api/eliminar" method="POST">
                    <input type="hidden" 
                            name="id" 
                            value="<?php echo $cita->id; ?>">
                    <input type="submit" class="boton-eliminar" value="Eliminar Cita">
                </form>
            <?php endif; ?>
        <?php endforeach; //Fin de foreach?>
    </ul>
</div>

<?php
    $script = "<script src='build/js/buscador.js'></script>";
?>