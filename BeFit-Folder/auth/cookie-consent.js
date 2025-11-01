document.addEventListener('DOMContentLoaded', function() {
    const cookieBanner = document.getElementById('cookie-consent');
    const cookieName = 'befit_cookie_consent';
    
    // Check if cookie decision exists
    if (!document.cookie.includes(`${cookieName}=`)) {
        cookieBanner.style.display = 'flex';
    }

    // Accept handler
    document.getElementById('accept-cookies').addEventListener('click', function(e) {
        e.preventDefault();
        setCookie(cookieName, 'accepted', 365);
        cookieBanner.style.display = 'none';
    });

    // Decline handler
    document.getElementById('decline-cookies').addEventListener('click', function(e) {
        e.preventDefault();
        setCookie(cookieName, 'declined', 30);
        cookieBanner.style.display = 'none';
    });

    // Cookie setting function
    function setCookie(name, value, days) {
        const date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        document.cookie = `${name}=${value};expires=${date.toUTCString()};path=/`;
    }
});