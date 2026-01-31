// JS/e_det_rep.js
// Handles fetching and displaying employee monthly leave and working hours, and report generation

document.addEventListener('DOMContentLoaded', function() {
    fetchMonthlyData();
});

function fetchMonthlyData() {
    fetch('PHP/E_det_rep.php?action=fetch')
        .then(res => res.json())
        .then(data => renderMonthlyTable(data))
        .catch(() => {
            document.getElementById('monthlyList').innerHTML = '<tr><td colspan="5" style="text-align:center;">Error loading data.</td></tr>';
        });
}

function renderMonthlyTable(data) {
    const tbody = document.getElementById('monthlyList');
    tbody.innerHTML = '';
    if (!data.length) {
        tbody.innerHTML = `<tr><td colspan="5" style="text-align:center;">No data found.</td></tr>`;
        return;
    }
    data.forEach(emp => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${emp.E_id}</td>
            <td>${emp.Name}</td>
            <td>${emp['leave days']}</td>
            <td>${emp['Working hours']}</td>
            <td><button class="btn-report" onclick="generateReport('${emp.E_id}')">Generate Report</button></td>
        `;
        tbody.appendChild(tr);
    });
}

function generateReport(empId) {
    window.open('PHP/E_det_rep.php?action=pdf&id=' + encodeURIComponent(empId), '_blank');
}
