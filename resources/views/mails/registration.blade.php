<!DOCTYPE html>
<html>
<head>
    <title>{{ config('mail.from.address') }}</title>
</head>
<body>
<h1>{{ $data['first_name'] }} {{ $data['last_name'] }}</h1>

<p>
    Your registration number is: {{ $data['code'] }}
</p>

<p>Thank you</p>
</body>
</html>
