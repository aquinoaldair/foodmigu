<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Foodmigu') }} - Gestión de comedores empresariales</title>
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="antialiased bg-gray-50 text-gray-900" style="font-family: 'Inter', sans-serif;">
    {{-- Hero Section --}}
    <section class="bg-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-24">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="order-2 lg:order-1">
                    <h1 class="text-4xl sm:text-5xl font-bold text-gray-900 leading-tight">
                        Digitaliza la gestión de tu comedor empresarial
                    </h1>
                    <p class="mt-6 text-xl text-gray-600">
                        Controla menús, comensales y pedidos en tiempo real.
                    </p>
                    <div class="mt-10 flex flex-col sm:flex-row gap-4">
                        <a href="#contacto" class="inline-flex items-center justify-center px-6 py-3 text-lg font-semibold text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition-colors">
                            Solicitar demostración
                        </a>
                        <a href="/app" class="inline-flex items-center justify-center px-6 py-3 text-lg font-semibold text-blue-600 bg-white border-2 border-blue-600 rounded-xl hover:bg-blue-50 transition-colors">
                            Ingresar al sistema
                        </a>
                    </div>
                </div>
                <div class="order-1 lg:order-2 flex justify-center">
                    <div class="w-full max-w-md h-64 bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl flex items-center justify-center">
                        <svg class="w-32 h-32 text-blue-600 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Problema Section --}}
    <section class="py-20 lg:py-28">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 text-center">
                ¿Sigues usando listas en papel o Excel?
            </h2>
            <p class="mt-6 text-xl text-gray-600 text-center max-w-3xl mx-auto">
                La gestión manual de comedores genera problemas que afectan la eficiencia y el control.
            </p>
            <div class="mt-16 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="flex flex-col items-center text-center">
                    <div class="w-14 h-14 rounded-xl bg-red-50 flex items-center justify-center">
                        <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                    <h3 class="mt-4 font-semibold text-gray-900">Desorden</h3>
                    <p class="mt-2 text-gray-600">Hojas perdidas, información dispersa y sin respaldo.</p>
                </div>
                <div class="flex flex-col items-center text-center">
                    <div class="w-14 h-14 rounded-xl bg-amber-50 flex items-center justify-center">
                        <svg class="w-7 h-7 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <h3 class="mt-4 font-semibold text-gray-900">Errores de conteo</h3>
                    <p class="mt-2 text-gray-600">Conteos manuales imprecisos que generan desperdicio.</p>
                </div>
                <div class="flex flex-col items-center text-center">
                    <div class="w-14 h-14 rounded-xl bg-orange-50 flex items-center justify-center">
                        <svg class="w-7 h-7 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="mt-4 font-semibold text-gray-900">Pérdida de tiempo</h3>
                    <p class="mt-2 text-gray-600">Horas dedicadas a consolidar datos y generar reportes.</p>
                </div>
                <div class="flex flex-col items-center text-center">
                    <div class="w-14 h-14 rounded-xl bg-rose-50 flex items-center justify-center">
                        <svg class="w-7 h-7 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <h3 class="mt-4 font-semibold text-gray-900">Falta de control</h3>
                    <p class="mt-2 text-gray-600">Sin visibilidad por día, turno o comedor.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Solución Section --}}
    <section class="py-20 lg:py-28 bg-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 text-center">
                Una solución simple y poderosa
            </h2>
            <p class="mt-6 text-xl text-gray-600 text-center max-w-3xl mx-auto">
                Todo lo que necesitas para gestionar tu comedor empresarial en una sola plataforma.
            </p>
            <div class="mt-16 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white rounded-2xl shadow-sm p-6 hover:shadow-md transition-shadow border border-gray-100">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="mt-4 font-semibold text-gray-900">Gestión de menús calendarizados</h3>
                    <p class="mt-2 text-gray-600">Planifica y publica tus menús de forma centralizada.</p>
                </div>
                <div class="bg-white rounded-2xl shadow-sm p-6 hover:shadow-md transition-shadow border border-gray-100">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <h3 class="mt-4 font-semibold text-gray-900">Control por comedor</h3>
                    <p class="mt-2 text-gray-600">Gestiona múltiples comedores desde un mismo panel.</p>
                </div>
                <div class="bg-white rounded-2xl shadow-sm p-6 hover:shadow-md transition-shadow border border-gray-100">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="mt-4 font-semibold text-gray-900">Selección móvil por QR</h3>
                    <p class="mt-2 text-gray-600">Los comensales eligen su menú desde el celular.</p>
                </div>
                <div class="bg-white rounded-2xl shadow-sm p-6 hover:shadow-md transition-shadow border border-gray-100">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 class="mt-4 font-semibold text-gray-900">Reportes automáticos</h3>
                    <p class="mt-2 text-gray-600">Conteos y reportes PDF en tiempo real.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Cómo Funciona Section --}}
    <section class="py-20 lg:py-28">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 text-center">
                Cómo funciona
            </h2>
            <p class="mt-6 text-xl text-gray-600 text-center max-w-3xl mx-auto">
                En 4 simples pasos transforma la gestión de tu comedor.
            </p>
            <div class="mt-16 flex flex-col lg:flex-row gap-8 lg:gap-4">
                <div class="flex-1 flex flex-col items-center text-center">
                    <div class="w-16 h-16 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-xl">1</div>
                    <h3 class="mt-4 font-semibold text-gray-900 text-lg">Crea tu menú semanal</h3>
                    <p class="mt-2 text-gray-600">Define los platos de cada día y publica el menú.</p>
                </div>
                <div class="hidden lg:flex flex-shrink-0 items-center justify-center">
                    <svg class="w-8 h-8 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </div>
                <div class="flex-1 flex flex-col items-center text-center">
                    <div class="w-16 h-16 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-xl">2</div>
                    <h3 class="mt-4 font-semibold text-gray-900 text-lg">Asigna comedores</h3>
                    <p class="mt-2 text-gray-600">Configura los comedores y genera códigos QR.</p>
                </div>
                <div class="hidden lg:flex flex-shrink-0 items-center justify-center">
                    <svg class="w-8 h-8 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </div>
                <div class="flex-1 flex flex-col items-center text-center">
                    <div class="w-16 h-16 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-xl">3</div>
                    <h3 class="mt-4 font-semibold text-gray-900 text-lg">Comensales eligen desde su celular</h3>
                    <p class="mt-2 text-gray-600">Escanean el QR y seleccionan su menú del día.</p>
                </div>
                <div class="hidden lg:flex flex-shrink-0 items-center justify-center">
                    <svg class="w-8 h-8 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </div>
                <div class="flex-1 flex flex-col items-center text-center">
                    <div class="w-16 h-16 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-xl">4</div>
                    <h3 class="mt-4 font-semibold text-gray-900 text-lg">Conteos y reportes automáticos</h3>
                    <p class="mt-2 text-gray-600">Obtén PDF en tiempo real para producción.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Beneficios Section --}}
    <section class="py-20 lg:py-28 bg-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 text-center">
                Beneficios
            </h2>
            <p class="mt-6 text-xl text-gray-600 text-center max-w-3xl mx-auto">
                Resultados tangibles para tu operación.
            </p>
            <div class="mt-16 max-w-2xl mx-auto space-y-6">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-green-100 flex items-center justify-center mt-0.5">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Reduce desperdicio</h3>
                        <p class="mt-1 text-gray-600">Conteos precisos evitan sobreproducción y pérdida de alimentos.</p>
                    </div>
                </div>
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-green-100 flex items-center justify-center mt-0.5">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Ahorra tiempo administrativo</h3>
                        <p class="mt-1 text-gray-600">Deja de consolidar datos manualmente. Todo está automatizado.</p>
                    </div>
                </div>
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-green-100 flex items-center justify-center mt-0.5">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Control total por día y comedor</h3>
                        <p class="mt-1 text-gray-600">Visibilidad completa de cada comedor y fecha.</p>
                    </div>
                </div>
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-green-100 flex items-center justify-center mt-0.5">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Exporta reportes en PDF</h3>
                        <p class="mt-1 text-gray-600">Genera documentos listos para producción y auditoría.</p>
                    </div>
                </div>
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-green-100 flex items-center justify-center mt-0.5">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Optimiza producción de cocina</h3>
                        <p class="mt-1 text-gray-600">Produce exactamente lo necesario según los pedidos.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA Final Section --}}
    <section id="contacto" class="py-20 lg:py-28 bg-blue-600">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl sm:text-4xl font-bold text-white">
                Optimiza tu comedor hoy mismo.
            </h2>
            <p class="mt-6 text-xl text-blue-100">
                Solicita información y conoce cómo podemos ayudarte.
            </p>
            <a href="mailto:contacto@foodmigu.com" class="mt-10 inline-flex items-center justify-center px-10 py-4 text-lg font-semibold text-blue-600 bg-white rounded-xl hover:bg-blue-50 transition-colors shadow-lg">
                Solicitar información
            </a>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="bg-gray-900 text-gray-400 py-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-6">
                <span class="font-semibold text-white">{{ config('app.name', 'Foodmigu') }}</span>
                <div class="flex gap-8">
                    <a href="#contacto" class="hover:text-white transition-colors">Contacto</a>
                    <a href="/app" class="hover:text-white transition-colors">Ingresar al sistema</a>
                </div>
            </div>
            <div class="mt-8 pt-8 border-t border-gray-800 text-center sm:text-left text-sm">
                &copy; {{ date('Y') }} {{ config('app.name', 'Foodmigu') }}. Todos los derechos reservados.
            </div>
        </div>
    </footer>
</body>
</html>
