<h1 class="nombre-pagina">Reestablecer Password</h1>
<p class="descripcion-pagina">Llena el siguiente campo con tu nuevo Password</p>

<?php include_once __DIR__ . '/../templates/alertas.php'?>
<?php if($error) return null; ?>

<form action="" class="formulario" method="POST">

    <div class="campo">
        <label for="password">Password:</label>
        <input type="password"
                id="password"
                name="password"
                placeholder="Tu Nuevo Password"
        >
    </div>

    <input type="submit" value="Guardar Nuevo Password :)" class="boton">
</form>

<div class="acciones">
    <a href="/">Ya tienes una cuenta? Incia Sesión ya!</a>
    <a href="/crearCuenta">Aun no tienes una cuenta? Crea una cuenta ya!</a>
</div>