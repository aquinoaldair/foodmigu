<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>PDF Día - {{ $title }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; margin: 20px; }
        h1 { font-size: 16px; margin-bottom: 4px; }
        .meta { color: #666; font-size: 10px; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #333; padding: 6px 8px; text-align: left; }
        th { background-color: #f0f0f0; font-weight: bold; }
        tr:nth-child(even) { background-color: #fafafa; }
        .estado-elige { color: #16a34a; }
        .estado-pendiente { color: #ca8a04; }
    </style>
</head>
<body>
    <h1>{{ $title }}</h1>
    <div class="meta">
        Fecha: {{ $date }}<br>
        Comedor: {{ $comedor }}
    </div>
    <table>
        <thead>
            <tr>
                <th style="width:15%">ID</th>
                <th style="width:25%">Nombre</th>
                <th style="width:12%">Estado</th>
                <th>Opciones elegidas</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $r)
            <tr>
                <td>{{ $r['id_code'] }}</td>
                <td>{{ $r['name'] }}</td>
                <td class="{{ $r['estado'] === 'Eligió' ? 'estado-elige' : 'estado-pendiente' }}">{{ $r['estado'] }}</td>
                <td>{{ $r['opciones'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
