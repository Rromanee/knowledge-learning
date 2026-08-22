import './stimulus_bootstrap.js';
import './styles/app.css';

/*
 * Automatically dismiss flash messages after 5 seconds.
 */

document.addEventListener('DOMContentLoaded', function () {
    setTimeout(function () {
        document
            .querySelectorAll('.auto-dismiss-alert')
            .forEach(function (alert) {

                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';

                setTimeout(function () {
                    alert.remove();
                }, 500);
            });
    }, 5000);
});