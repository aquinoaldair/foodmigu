<template>
    <div class="max-w-4xl mx-auto space-y-8">
        <h1 class="text-xl font-semibold text-gray-900">Guía del sistema</h1>

        <!-- Sección 1: Introducción -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Introducción</h2>
            <p class="text-gray-600 leading-relaxed">
                Foodmigu es un sistema diseñado para administrar menús de comedores industriales y empresariales.
                Permite registrar las elecciones de alimentos de los comensales, generar reportes diarios y semanales,
                y optimizar la producción de cocina mediante conteos precisos por día y por comedor.
            </p>
        </div>

        <!-- Sección 2: Flujo general -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Flujo general del sistema</h2>
            <ol class="space-y-4 list-decimal list-inside text-gray-600">
                <li class="pl-2">
                    <strong class="text-gray-900">Registrar comedores</strong> — Crea los lugares físicos donde se sirven los alimentos (ej.: Comedor Norte, Comedor Administrativo).
                </li>
                <li class="pl-2">
                    <strong class="text-gray-900">Crear menús semanales</strong> — Define los platillos de cada día en la Construcción Semanal, asignando menús base y asociándolos a los comedores.
                </li>
                <li class="pl-2">
                    <strong class="text-gray-900">Compartir enlace público o QR</strong> — Cada comedor genera una URL única y un código QR que los comensales pueden escanear o visitar desde su dispositivo.
                </li>
                <li class="pl-2">
                    <strong class="text-gray-900">Comensales seleccionan alimentos</strong> — Los comensales se identifican con su ID, eligen el menú del día y seleccionan las opciones según la configuración de cada categoría.
                </li>
                <li class="pl-2">
                    <strong class="text-gray-900">Generar reportes diarios y semanales</strong> — Desde el Dashboard obtén conteos en tiempo real y descarga PDF con el detalle de elecciones por día o por semana.
                </li>
            </ol>
        </div>

        <!-- Sección 3: Explicación de módulos -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Explicación de módulos</h2>

            <div class="space-y-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Comedores</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Aquí se registran los lugares donde se sirven los alimentos. Cada comedor tiene un nombre y genera automáticamente un enlace público y un código QR. Puedes activar o desactivar comedores y gestionar la lista de comensales asignados a cada uno.
                    </p>
                </div>

                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Menús</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Los menús base definen los platillos disponibles organizados por categorías (ej.: Proteína, Guarnición, Postre). Las categorías pueden ser solo informativas, de selección única o múltiple. En la Construcción Semanal se asignan estos menús a cada día de la semana y se vinculan a los comedores.
                    </p>
                </div>

                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Elecciones de comensales</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Los comensales acceden mediante el enlace público o el QR del comedor. Se identifican con su código de empleado y luego seleccionan su menú del día según las reglas definidas (obligatorio, opcional, una o varias opciones). Las elecciones se guardan y pueden modificarse hasta la fecha límite configurada.
                    </p>
                </div>

                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Dashboard y reportes</h3>
                    <p class="text-gray-600 leading-relaxed">
                        El Dashboard muestra un resumen de los conteos por día y comedor. Desde allí se pueden generar reportes PDF diarios (detalle por comensal y sus elecciones) y semanales (resumen consolidado). Estos reportes son útiles para producción y auditoría.
                    </p>
                </div>
            </div>
        </div>

        <!-- Sección 4: Preguntas frecuentes -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Preguntas frecuentes</h2>

            <div class="space-y-6">
                <div>
                    <h3 class="text-base font-medium text-gray-900 mb-2">¿Qué pasa si un comensal no elige comida?</h3>
                    <p class="text-gray-600 leading-relaxed">
                        El comensal aparecerá como "Pendiente" en los reportes. Si la categoría es obligatoria y tiene fecha límite, el comensal podrá elegir hasta que venza ese plazo. Después de la fecha límite, las elecciones no se pueden modificar y el reporte reflejará el estado final (eligió o pendiente).
                    </p>
                </div>

                <div>
                    <h3 class="text-base font-medium text-gray-900 mb-2">¿Puedo modificar un menú después de publicarlo?</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Sí. Puedes editar la Construcción Semanal y los menús base en cualquier momento. Los cambios se reflejarán de inmediato para los comensales que aún no hayan elegido o que estén dentro del plazo de modificación. Ten en cuenta que alterar categorías o platillos puede afectar elecciones ya realizadas, por lo que se recomienda hacer cambios con precaución.
                    </p>
                </div>

                <div>
                    <h3 class="text-base font-medium text-gray-900 mb-2">¿Cómo comparto el menú con los comensales?</h3>
                    <p class="text-gray-600 leading-relaxed">
                        En el listado de Comedores, usa el botón "Compartir" del comedor deseado. Se mostrará la URL pública y un código QR que puedes imprimir o enviar por correo/WhatsApp. Los comensales pueden escanear el QR con el celular o abrir el enlace directamente. Si cambias el nombre del comedor, se generará una nueva URL y QR; los antiguos dejarán de funcionar.
                    </p>
                </div>

                <div>
                    <h3 class="text-base font-medium text-gray-900 mb-2">¿Qué diferencia hay entre Menús Base y Construcción Semanal?</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Los Menús Base son plantillas reutilizables que definen los platillos y categorías para un día (ej.: "Menú Lunes Standard"). La Construcción Semanal es el calendario donde asignas qué menú base corresponde a cada día de la semana y qué comedores tendrán acceso a esa semana. Un mismo menú base puede usarse en varias semanas.
                    </p>
                </div>

                <div>
                    <h3 class="text-base font-medium text-gray-900 mb-2">¿Cómo agrego comensales a un comedor?</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Desde el listado de Comedores, haz clic en "Ver Comensales" del comedor. Ahí puedes crear comensales uno por uno o usar "Importar Excel" para cargar una lista masiva. Cada comensal necesita un código de identificación único (ej.: número de empleado) que usará para identificarse al elegir su menú.
                    </p>
                </div>

                <div>
                    <h3 class="text-base font-medium text-gray-900 mb-2">¿Qué son los tipos de selección en las categorías (solo informativo, única, múltiple)?</h3>
                    <p class="text-gray-600 leading-relaxed">
                        <strong>Solo informativo:</strong> El comensal solo ve la opción, no puede elegirla (ej.: sopa que se sirve a todos). <strong>Selección única:</strong> El comensal debe elegir exactamente una opción (ej.: pollo o carne). <strong>Selección múltiple:</strong> El comensal puede elegir varias opciones (ej.: arroz, frijoles, ensalada). Además, una categoría puede ser obligatoria u opcional.
                    </p>
                </div>

                <div>
                    <h3 class="text-base font-medium text-gray-900 mb-2">¿Hasta cuándo pueden los comensales elegir o cambiar su menú?</h3>
                    <p class="text-gray-600 leading-relaxed">
                        La fecha límite se configura en la Construcción Semanal. Una vez vencida, los comensales ya no pueden modificar sus elecciones y el sistema mostrará un mensaje indicando que el plazo ha finalizado. Los reportes reflejarán el estado final de cada comensal en ese momento.
                    </p>
                </div>

                <div>
                    <h3 class="text-base font-medium text-gray-900 mb-2">¿Puedo tener varios comedores con menús diferentes?</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Sí. Cada comedor tiene su propio enlace y QR. Puedes asignar la misma Construcción Semanal a varios comedores (todos verán el mismo menú) o crear construcciones semanales distintas si necesitas ofrecer opciones diferentes por ubicación o tipo de servicio.
                    </p>
                </div>

                <div>
                    <h3 class="text-base font-medium text-gray-900 mb-2">¿Para qué sirven los reportes PDF?</h3>
                    <p class="text-gray-600 leading-relaxed">
                        El reporte diario lista cada comensal con sus elecciones (o "Pendiente" si no eligió), útil para producción en cocina. El reporte semanal ofrece un resumen consolidado. Ambos se pueden descargar desde el Dashboard o desde la vista de cada día/semana y sirven para auditoría, planeación de compras y control de inventario.
                    </p>
                </div>

                <div>
                    <h3 class="text-base font-medium text-gray-900 mb-2">¿Qué pasa si un comensal intenta acceder sin estar registrado?</h3>
                    <p class="text-gray-600 leading-relaxed">
                        El comensal debe identificarse con un código que exista en la lista de comensales del comedor. Si el código no está registrado, no podrá continuar. El administrador debe agregar al comensal en "Ver Comensales" del comedor correspondiente antes de que pueda elegir su menú.
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
</script>
