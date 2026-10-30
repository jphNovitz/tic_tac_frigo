<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tic Tac Frigo</title>
    @vite('resources/css/app.css')
</head>
<body>
@session('status')
    <div class="chat chat-end">
        <div class="chat-bubble">
            <p>{{ __('status.'.$value) }}</p>
        </div>
    </div>

@endsession
@yield('content')

</body>
</html>
