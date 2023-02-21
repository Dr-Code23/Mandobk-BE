<script src="{{ asset('build/assets/app-23a62441.js') }}"></script>

<script>
    Echo.private('google').listen('TestEvent', function(e) {
        console.log('Your Message Is ', e.message)
    })

    Echo.private('newUserRegister').listen('RegisterUserEvent', function(e) {
        console.log('New User Registred , His Data is ' + e.payload.data)
        console.log('New User Registred , His Data is ', e.payload)
    })
    // Echo.channel('events')
    //     .listen('RealTimeMessage', (e) => console.log('RealTimeMessage: ' + e.message));
</script>
