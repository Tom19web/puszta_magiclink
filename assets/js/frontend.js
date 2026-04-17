/* * PusztaPlay Magic Login - Frontend Interakciók
 * Ahol az idő lepereg, és a felhasználó sorsa megpecsételődik.
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // 1. Az "Újraküldés tiltva" időzítő (Error/Wait kártya)
    const timerEl = document.getElementById("pp-resend-timer");
    if (timerEl) {
        startTimer(timerEl, function() {
            window.location.reload();
        });
    }

    // 2. A "Sikeres küldés" időzítő (Success kártya)
    const timerElSuccess = document.getElementById("pp-resend-timer-success");
    if (timerElSuccess) {
        startTimer(timerElSuccess, function() {
            window.location.reload();
        });
    }

    // Segédfüggvény az időzítők letudására
    function startTimer(element, onComplete) {
        let remaining = parseInt(element.getAttribute("data-remaining"), 10) || 0;

        function formatTime(seconds) {
            let minutes = Math.floor(seconds / 60);
            let secs = seconds % 60;
            return String(minutes).padStart(2, "0") + ":" + String(secs).padStart(2, "0");
        }

        function tick() {
            element.textContent = formatTime(remaining);

            if (remaining <= 0) {
                element.textContent = "00:00";
                setTimeout(onComplete, 800);
                return;
            }

            remaining--;
            setTimeout(tick, 1000);
        }

        tick();
    }
});