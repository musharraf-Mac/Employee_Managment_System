// JS/e_det_rep.js
// Handles fetching and displaying employee monthly leave and working hours, and report generation

document.addEventListener('DOMContentLoaded', function() {
    fetchMonthlyData();
});

function fetchMonthlyData() {
    // Placeholder: Replace with AJAX call to your PHP backend
    // Example data:
    const data = [
        { id: 'E001', firstName: 'John', leaveDays: 2, workingHours: 160 },
        { id: 'E002', firstName: 'Jane', leaveDays: 1, workingHours: 172 },
        { id: 'E003', firstName: 'Alex', leaveDays: 0, workingHours: 180 }
    ];
    renderMonthlyTable(data);
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
            <td>${emp.id}</td>
            <td>${emp.firstName}</td>
            <td>${emp.leaveDays}</td>
            <td>${emp.workingHours}</td>
            <td><button class="btn-report" onclick="generateReport('${emp.id}')">Generate Report</button></td>
        `;
        tbody.appendChild(tr);
    });
}

function generateReport(empId) {
    // Placeholder: Implement report generation logic (PDF, print, etc.)
    alert('Report generated for Employee ID: ' + empId);
}
