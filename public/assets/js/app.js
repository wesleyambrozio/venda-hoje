document.addEventListener('DOMContentLoaded', function () {
    // Auto-hide alerts after 5s
    document.querySelectorAll('.alert').forEach(function (alert) {
        setTimeout(function () {
            alert.style.opacity = '0';
            alert.style.transition = 'opacity 0.3s';
            setTimeout(function () { alert.remove(); }, 300);
        }, 5000);
    });

    // Toggle follow-up fields
    var resultadoSelect = document.getElementById('resultado');
    var followupFields = document.getElementById('followup-fields');
    if (resultadoSelect && followupFields) {
        function toggleFollowup() {
            followupFields.style.display = resultadoSelect.value === 'follow_up' ? 'block' : 'none';
        }
        resultadoSelect.addEventListener('change', toggleFollowup);
        toggleFollowup();
    }

    // Confirm delete
    document.querySelectorAll('[data-confirm]').forEach(function (el) {
        el.addEventListener('click', function (e) {
            if (!confirm(el.dataset.confirm)) {
                e.preventDefault();
            }
        });
    });
});
