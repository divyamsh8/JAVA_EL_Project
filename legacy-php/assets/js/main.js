/**
 * Contineo Parent Portal - Client Script
 * Demo Institute of Engineering
 */

function randomstring(length) {
    var s = '';
    var chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    for (var i = 0; i < length; i++) {
        s += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    return s;
}

function isValidDate(year, month, day) {
    var y = parseInt(year, 10);
    var m = parseInt(month, 10) - 1; // 0-indexed in JS
    var d = parseInt(day, 10);
    var date = new Date(y, m, d);
    return date.getFullYear() === y && date.getMonth() === m && date.getDate() === d;
}

function submitLogin(event) {
    var usn = document.getElementById('username').value.trim();
    var dd = document.getElementById('dd').value;
    var mm = document.getElementById('mm').value;
    var yyyy = document.getElementById('yyyy').value;

    if (!usn) {
        alert('Please enter your University Seat Number (USN).');
        document.getElementById('username').focus();
        if (event) event.preventDefault();
        return false;
    }

    if (!dd || !mm || !yyyy) {
        alert('Please select your complete date of birth (Day, Month, Year).');
        if (event) event.preventDefault();
        return false;
    }

    if (!isValidDate(yyyy, mm, dd)) {
        alert('The selected date of birth is invalid (e.g., February 30 or 31). Please choose a valid calendar date.');
        if (event) event.preventDefault();
        return false;
    }

    // Compose DOB string
    var dob = yyyy + '-' + mm.trim() + '-' + dd.trim();
    
    // Contineo encoding emulation
    var randString = '';
    for (var i = 0; i < dob.length; i++) {
        randString += dob.charAt(i) + randomstring(2);
    }
    document.getElementById('passwd').value = btoa(randString);

    return true;
}

// Modal handling
document.addEventListener('DOMContentLoaded', function() {
    var modal = document.getElementById('myloginModal');
    var closeBtn = document.querySelector('.modal .close');

    if (modal && closeBtn) {
        closeBtn.onclick = function() {
            modal.style.display = 'none';
        };

        window.onclick = function(event) {
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        };

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && modal.style.display === 'block') {
                modal.style.display = 'none';
            }
        });
    }
});

function showLoginFailedModal(message, attemptsRemaining, isLocked, retrySeconds) {
    var modal = document.getElementById('myloginModal');
    if (!modal) return;

    var msgElem = document.getElementById('modalErrorMessage');
    var attElem = document.getElementById('modalAttemptsBadge');

    if (msgElem) {
        msgElem.textContent = message || 'You have entered an invalid username or password.';
    }

    if (attElem) {
        if (isLocked) {
            var mins = Math.ceil(retrySeconds / 60);
            attElem.innerHTML = 'Account locked due to multiple failed attempts.<br><span style="font-size:14px;color:#64748b;">Please retry in ' + mins + ' minute(s).</span>';
        } else {
            attElem.textContent = attemptsRemaining + ' attempts remaining';
        }
    }

    modal.style.display = 'block';
}
