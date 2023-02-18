<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<body>
    <form action={{ route('v1-login') }} method="POST">
        <input type="text" name="username">
        <input type="password" name="password" id="">
        <input type="submit">
    </form>
</body>

</html>
