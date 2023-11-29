let paso = 1;
const pasoInicial = 1;
const pasoFinal = 3;

const cita = {
    id: '',
    nombre: '',
    fecha: '',
    hora: '',
    servicios: []

}

document.addEventListener('DOMContentLoaded', function () {
    iniciarApp();
});

function iniciarApp() {
    mostrarSeccion(); //Muesta y oculta las secciones
    tabs(); //Cambia la seccion cuando se presione un tabs o button
    botonesPaginas() //Agrega o quita los botones siguiente, anterior
    
    paginaAnterior();
    paginaSiguiente();

    consultarApi(); //Consulta la api en el backend de PHP

    idCliente();
    nombreCliente(); //Añade nombre del cliente al objeto cita
    fechaCliente(); //Añade la fecha de la cita del cliente al objeto cita
    horaCliente(); //Añade la hora de la cita del cliente al objeto citamos
    mostrarResumen(); //Muestra el resumen de la cita

}

function mostrarSeccion() {
    //ocular la seccion que tenga la clase mostrar
    const seccionAnterior = document.querySelector('.mostrar');
    if(seccionAnterior) {
        seccionAnterior.classList.remove('mostrar');
    }

    //selecionar la seccion con el paso
    const pasoSeleccionado = `#paso-${paso}`;
    const seccion = document.querySelector(pasoSeleccionado);
    seccion.classList.add('mostrar');

    //Quita la clase "actual" al anterior
    const tabAnterior = document.querySelector('.actual');
    if(tabAnterior) {
        tabAnterior.classList.remove('actual');
    }

    //Cambia de color el tab o button actual
    const tabSeleccionado = `[data-paso="${paso}"]`;
    const tab = document.querySelector(tabSeleccionado);
    tab.classList.add('actual');
}

function tabs() {
    const botones = document.querySelectorAll('.tabs button');

    botones.forEach( boton => {
        boton.addEventListener('click', function (e) {
            paso = parseInt( e.target.dataset.paso );

            mostrarSeccion();
            botonesPaginas();
        });
    });
    
}

function botonesPaginas() {
    const paginaAnterior = document.querySelector('#anterior');
    const paginaSiguiente = document.querySelector('#siguiente');

    if (paso === 1) {
        paginaAnterior.classList.add('ocultar');
        paginaSiguiente.classList.remove('ocultar');
    } else if (paso === 3) {
        paginaAnterior.classList.remove('ocultar');
        paginaSiguiente.classList.add('ocultar');

        mostrarResumen();
    } else if (paso === 2) {
        paginaAnterior.classList.remove('ocultar');
        paginaSiguiente.classList.remove('ocultar');
    }

    mostrarSeccion();
}

function paginaAnterior() {
    const paginaAnterior = document.querySelector('#anterior');
    paginaAnterior.addEventListener('click', function () {
        if (paso <= pasoInicial) return;
        paso--;
        botonesPaginas();
    })
}

function paginaSiguiente() {
    const paginaSiguiente = document.querySelector('#siguiente');
    paginaSiguiente.addEventListener('click', function () {
        if (paso >= pasoFinal) return;
        paso++;
        botonesPaginas();
    })
}

//async esta funcion se va a ejecutar al arrancar la app o el "sitio", y puede ejecutar otras funciones
async function consultarApi() {
    
    try {
        const url = `${location.origin}/api/servicios`;
        //await espera hasta que descargue (o espera hasta que realice la consulta) todo para despues seguir con las demas intrucciones o funciones
        const resultado = await fetch(url);
        const servicios = await resultado.json();
        mostrarServicios(servicios);
    } catch (error) {
        console.log(error);
    }
}

function mostrarServicios(servicios) {
    servicios.forEach( servicio => {
        const {id, nombre, precio} = servicio;

        const nombreServicio = document.createElement('P');
        nombreServicio.classList.add('nombre-servicio');
        nombreServicio.textContent = nombre;

        const precioServicio = document.createElement('P');
        precioServicio.classList.add('precio-servicio');
        precioServicio.textContent = `$${precio}`;

        const servicioDiv = document.createElement('DIV');
        servicioDiv.classList.add('servicio');
        servicioDiv.dataset.idServicio = id;
        servicioDiv.onclick = function () {
            seleccionarServicio(servicio);
        };

        servicioDiv.appendChild(nombreServicio);
        servicioDiv.appendChild(precioServicio);

        document.querySelector('#servicios').appendChild(servicioDiv);
    }); 
}

function seleccionarServicio(servicio) {
    //Crea un objeto servicios
    const {servicios} = cita;
    const {id} = servicio;

    //Identificar el elemento al que se le da click
    const divServicio = document.querySelector(`[data-id-servicio="${id}"]`);
    //Comprobar si un servicio ya fue agregado
    if ( servicios.some( agregado => agregado.id === id ) ) {
        //Eliminar o quitar
        cita.servicios = servicios.filter( agregado => agregado.id !== id );
        divServicio.classList.remove('seleccionado');
    } else {
        //Agregar
        cita.servicios = [...servicios, servicio];
        divServicio.classList.add('seleccionado');
    }
    console.log(cita);
}

function idCliente() {
    const id = document.querySelector('#id').value;
    cita.id = id;
}

function nombreCliente() {
    const nombre = document.querySelector('#nombre').value;
    cita.nombre = nombre;
}

function fechaCliente() {
    const inputfecha = document.querySelector('#fecha');
    inputfecha.addEventListener('input', function(e) {

        //nos ayuda a obtener la informacion sobre la fecha que se selecciona
        const dia = new Date(e.target.value).getUTCDay();
        // domingo = 0, sabado = 6, viernes = 5 ...
        if ( [6, 0].includes(dia) ) {
            e.target.value = '';
            mostrarAlerta('Fin de semana no permitidos', 'error', '.formulario');
        }else {
            cita.fecha = e.target.value;
        }

        cita.fecha = inputfecha.value;
    });
}

function horaCliente() {
    const inputhora = document.querySelector('#hora');
    inputhora.addEventListener('input', function (e) {

        const horacita = e.target.value;
        const hora = horacita.split(":")[0];
        if ( hora < 10 || hora >20) {
            e.target.value = "";
            mostrarAlerta('Hora no valida (10:00 a 20:00)', 'error' , '.formulario');
        } else {
            cita.hora = e.target.value;
        }

        console.log(cita);
    });
}

function mostrarAlerta(mensaje, tipo, elemento, desaparece = true ) {
    //Previene que se generen mas de una alerta
    const alertaPrevia = document.querySelector('.alerta');
    if(alertaPrevia) {
        alertaPrevia.remove();
    };

    //Codigo para crear la alerta
    const alerta = document.createElement('DIV');
    alerta.textContent = mensaje;
    alerta.classList.add('alerta');
    alerta.classList.add(tipo);

    const referencia = document.querySelector(elemento);
    referencia.appendChild(alerta);

    if (desaparece) {
        //Eliminar la alerta
        setTimeout( () => {
            alerta.remove();
        }, 3000 );
    }
}

function mostrarResumen() {
    const resumen = document.querySelector('.contenido-resumen');

    //Limpiar el contenido de resumen
    while (resumen.firstChild) {
        resumen.removeChild(resumen.firstChild);
    }
    if ( Object.values(cita).includes('') || cita.servicios.length === 0 ) {
        mostrarAlerta('Faltan Datos para la Cita (Servicios, Fecha u Hora)', 'error', '.contenido-resumen', false);
        return;
    }

    //Formatear el div de resumen
    const {nombre, fecha, hora, servicios } = cita;

    //Heading para servicios en resumen
    const HeadingServicios = document.createElement('H3');
    HeadingServicios.textContent = "Resumen de Servicio(s)";
    resumen.appendChild(HeadingServicios);

    //Iterando y mostrando los servicios
    servicios.forEach( servicio => {
        const {id, nombre, precio} = servicio;

        const contenedorServicio = document.createElement('DIV');
        contenedorServicio.classList.add('contenedor-servicio');

        const textoServicio = document.createElement('P')
        textoServicio.textContent = nombre;

        const precioServicio = document.createElement('P')
        precioServicio.innerHTML = `<span>Precio: </span> $${precio}`;

        contenedorServicio.appendChild(textoServicio);
        contenedorServicio.appendChild(precioServicio);

        resumen.appendChild(contenedorServicio);
    });

    //Heading para informacion de la cita en resumen
    const HeadingCita = document.createElement('H3');
    HeadingCita.textContent = 'Detalles de la Cita';
    resumen.appendChild(HeadingCita);

    const nombreCliente = document.createElement('P');
    nombreCliente.innerHTML = `<span>Nombre: </span> ${nombre}`;

    //Formatear la fecha par auna vista amigable
    const fechaOBJ = new Date(fecha);
    const mes = fechaOBJ.getMonth();
    const dia = fechaOBJ.getDate() + 2;
    const year = fechaOBJ.getFullYear();

    const fechaUTC = new Date( Date.UTC(year, mes, dia) );

    const opciones = {weekday: 'long', month: 'long', year: 'numeric', day: 'numeric'}
    const fechaFormateada = fechaUTC.toLocaleDateString('es-MX', opciones)
    // console.log(fechaFormateada);

    const fechaCliente = document.createElement('P');
    fechaCliente.innerHTML = `<span>Fecha de la Cita: </span> ${fechaFormateada}`;

    const horaCliente = document.createElement('P');
    horaCliente.innerHTML = `<span>Hora de la Cita: </span> ${hora} Horas`;

    resumen.appendChild(nombreCliente);
    resumen.appendChild(fechaCliente);
    resumen.appendChild(horaCliente);

    //Boton para crear la cita
    const botonCita = document.createElement('BUTTON');
    botonCita.classList.add('boton');
    botonCita.textContent = 'Confirmar Cita :)';
    botonCita.onclick = confirmarCita;

    resumen.appendChild(botonCita);
}

async function confirmarCita() {
    
    const {id, nombre, fecha, hora, servicios} = cita;

    //map solo devuelve las coincidencias que encuentra en el arreglo, en este caso solo devuelve los id de los servicios que seleciona el cliente
    const idServicios = servicios.map(servicio => servicio.id);
    // console.log(idServicios);

    const datos = new FormData();
    //Aca van los datos de la cita
    datos.append('fecha', fecha);
    datos.append('hora', hora);
    datos.append('usuarioId', id);
    datos.append('servicios', idServicios);

    // console.log([...datos]);

    try {
        //Peticion hacia la Api
        const url = `${location.origin}/api/citas`;
        const respuesta = await fetch(url, {
            method: 'POST',
            body: datos
        }); 

        const resultado = await respuesta.json();
        
        if (resultado.resultado) {
            Swal.fire({
                icon: "success",
                title: "Cita Creada",
                text: "Tu cita fue creada correctamente",
                button: "OK :)"
            }).then( () =>{

                setTimeout( () => {
                    window.location.reload();
                }, 2500 )

            } );
        }
    } catch (error) {
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "Hubo un error al guaardar la cita",
        });
    }
}