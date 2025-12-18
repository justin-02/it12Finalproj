(function(){
    // simple heartbeat that pings server every 60 seconds if user is authenticated
    function ping() {
        fetch('/heartbeat', { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }})
            .catch(()=>{});
    }

    setInterval(ping, 60000);
    // also ping on focus
    window.addEventListener('focus', ping);
})();
