<script src="{{ asset('build/assets/app-d72bd59b.js') }}"></script>

<script>
    Echo.private('google').listen('TestEvent', function(e) {
        console.log('Your Message Is ', e.message)
    })
    // Echo.channel('events')
    //     .listen('RealTimeMessage', (e) => console.log('RealTimeMessage: ' + e.message));
</script>
