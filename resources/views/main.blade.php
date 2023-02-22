<!DOCTYPE html>

<head>
    <title>Pusher Test</title>
    {{-- <script src="{{ asset('build/assets/app-23a62441.js') }}"></script> --}}
    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    <script>
        // Enable pusher logging - don't include this in production
        Pusher.logToConsole = true;

        var pusher = new Pusher('14224c2f3aa03933a8eb', {
            cluster: 'mt1'
        });

        var channel = pusher.subscribe('Google');
        channel.bind('App\\Events\\PusherEvent', function(data) {
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
