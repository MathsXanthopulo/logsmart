<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Amigo Secreto</title>
    <!-- Bootstrap CSS -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
        .name-column {
            width: 45%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        @yield('content')
    </div>
    <!-- Bootstrap JS e dependências opcionais (jQuery) -->
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
