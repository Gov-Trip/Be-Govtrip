// Gov.Trip Interactive Application Script

window.GovTrip = {
    toggleMobileSidebar: function () {
        const drawer = document.getElementById('mobile-drawer');
        if (drawer) {
            drawer.classList.toggle('hidden');
        }
    },
    closeMobileSidebar: function () {
        const drawer = document.getElementById('mobile-drawer');
        if (drawer) {
            drawer.classList.add('hidden');
        }
    },
    filterTable: function (inputId, tableId) {
        const input = document.getElementById(inputId);
        const table = document.getElementById(tableId);
        if (!input || !table) return;

        const filter = input.value.toLowerCase();
        const rows = table.getElementsByTagName('tr');

        for (let i = 1; i < rows.length; i++) {
            const row = rows[i];
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        }
    },
    setAuthToken: function (token) {
        if (token) {
            localStorage.setItem('gov_trip_token', token);
        } else {
            localStorage.removeItem('gov_trip_token');
        }
    },
    getAuthToken: function () {
        return localStorage.getItem('gov_trip_token');
    }
};
