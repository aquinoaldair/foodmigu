<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Resumen Semanal - {{ $title }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; margin: 20px; }
        h1 { font-size: 16px; margin-bottom: 4px; }
        .meta { color: #666; font-size: 10px; margin-bottom: 20px; }
        .day-block { margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px solid #ddd; }
        .day-block:last-child { border-bottom: none; }
        .day-title { font-size: 13px; font-weight: bold; margin-bottom: 10px; }
        .category-block { margin-bottom: 10px; }
        .category-name { font-weight: bold; font-size: 11px; margin-bottom: 4px; }
        .option-row { padding: 2px 0 2px 12px; }
    </style>
</head>
<body>
    <h1>Resumen Semana — {{ $comedor }}</h1>
    <div class="meta">{{ $title }}</div>

    @foreach($days as $day)
    <div class="day-block">
        <div class="day-title">Día: {{ $day['date'] }}</div>
        @foreach($day['categories'] as $cat)
        <div class="category-block">
            <div class="category-name">Categoría: {{ $cat['name'] }}</div>
            @foreach($cat['options'] as $opt)
            <div class="option-row">{{ $opt['name'] }} — {{ $opt['total'] }}</div>
            @endforeach
        </div>
        @endforeach
    </div>
    @endforeach
</body>
</html>
