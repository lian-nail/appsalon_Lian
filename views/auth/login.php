<h1 class="nombre-pagina">Login</h1>
<p class="descripcion-pagina">Inicia Session</p>

<?php include_once __DIR__ . '/../templates/alertas.php'?>

<form class="formulario" action="/" method="POST">
    <div class="campo">
        <label for="email">Email</label>
        <input type="email" 
                id="email" 
                name="email" 
                placeholder="Tu Email"
                value="<?php echo sanitizar($auth->email); ?>"
        >
    </div>
    <div class="campo">
        <label for="password">Password</label>
        <input type="password" 
                id="password" 
                name="password" 
                placeholder="Tu Password"
        >
    </div>

    <input type="submit" class="boton" value="Iniciar Sesión">
</form>

<div class="acciones">
    <a href="/crearCuenta">Aun no tienes una cuenta? Crea una cuenta ya!</a>
    <a href="/olvide">Olvidaste tu Password?</a>
</div>