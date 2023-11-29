<h1 class="nombre-pagina">Olvidaste tu Password</h1>
<p class="descripcion-pagina">Reestablece tu Password escribiendo tu E-mail a continuacion ;)</p>

<?php include_once __DIR__ . '/../templates/alertas.php'?>

<form action="/olvide" class="formulario" method="POST">
    <div class="campo">
        <label for="email">E-mail</label>
        <input type="email"
                id="email"
                name="email"
                placeholder="Tu E-mail"
        >
    </div>

    <input type="submit" class="boton" value="Enviar Instrucciones">
</form>

<div class="acciones">
    <a href="/">Ya tienes una cuenta? Incia Sesión ya!</a>
    <a href="/crearCuenta">Aun no tienes una cuenta? Crea una cuenta ya!</a>
</div>