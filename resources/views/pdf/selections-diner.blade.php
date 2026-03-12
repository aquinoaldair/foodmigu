<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Mis selecciones - {{ $diner->name }}</title>
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
    <h1>Mis selecciones — {{ $comedor }}</h1>
    <div class="meta">
        Comensal: {{ $diner->name }} ({{ $diner->id_code }})
    </div>

    @foreach($days as $day)
    <div class="day-block">
        <div class="day-title">{{ $day['date'] }} — {{ $day['build_title'] }}</div>
        @if(!empty($day['updated_at']))
        <div class="meta" style="margin-bottom: 8px;">Última actualización: {{ $day['updated_at'] }}</div>
        @endif
        @foreach($day['categories'] as $cat)
        <div class="category-block">
            <div class="category-name">{{ $cat['name'] }}</div>
            @foreach($cat['items'] as $item)
            <div class="option-row">• {{ $item }}</div>
            @endforeach
        </div>
        @endforeach
    </div>
    @endforeach
</body>
</html>
