<!DOCTYPE html>

<head>
    <title>Pusher Test</title>
    {{-- <script src="{{ asset('build/assets/app-23a62441.js') }}"></script> --}}
    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    <script>
        // Enable pusher logging - don't include this in production
        Pusher.logToConsole = false;

        var pusher = new Pusher('14224c2f3aa03933a8eb', {
            cluster: 'mt1',
            authEndpoint: "http://localhost:8000/broadcasting/auth",
            auth: {
                headers: {
                    "Authorization": "Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbG9jYWxob3N0OjgwMDAvdjEvYXV0aC9sb2dpbiIsImlhdCI6MTY3NzA1NTc4OSwibmJmIjoxNjc3MDU1Nzg5LCJqdGkiOiJxQTJQWVRqdGVzMDdnNlpWIiwic3ViIjoiOCIsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.mCFtam03Fnx446ugEap_JwTdujjZG0b8dZCvRA-CbDo",
                    // "Access-Control-Allow-Origin": "*"
                }
            }
        });

        var channel = pusher.subscribe('private-newUserRegister');

        channel.bind('App\\Events\\RegisterUserEvent', function(data) {
            console.log(data);
        });
    </script>
</head>

<body>
    <h1>Pusher Test</h1>
    <p>
        Try publishing an event to channel <code>my-channel</code>
        with event name <code>my-event</code>.
    </p>
</body>
