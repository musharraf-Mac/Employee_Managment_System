// JS/e_det_rep.js
// Handles fetching and displaying employee monthly leave and working hours, and report generation

document.addEventListener('DOMContentLoaded', function() {
    fetchMonthlyData();
    // Admin update form handler
    const updateForm = document.getElementById('updateForm');
    if (updateForm) {
        updateForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const eid = document.getElementById('updateEid').value.trim();
            const leave = document.getElementById('updateLeave').value;
            const hours = document.getElementById('updateHours').value;
            const attendance = document.getElementById('updateAttendance').value;
            const msg = document.getElementById('updateMsg');
            msg.textContent = 'Updating...';
            fetch('PHP/E_det_rep.php?action=update', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ E_id: eid, E_Leave: leave, Working_hour: hours, Attendance: attendance })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    msg.style.color = 'green';
                    msg.textContent = 'Update successful!';
                    fetchMonthlyData();
                } else {
                    msg.style.color = 'red';
                    msg.textContent = data.message || 'Update failed.';
                }
            })
            .catch(() => {
                msg.style.color = 'red';
                msg.textContent = 'Error updating.';
            });
        });
    }
});

function fetchMonthlyData() {
    fetch('PHP/E_det_rep.php?action=fetch')
        .then(res => res.json())
        .then(data => renderMonthlyTable(data))
        .catch(() => {
            document.getElementById('monthlyList').innerHTML = '<tr><td colspan="6" style="text-align:center;">Error loading data.</td></tr>';
        });
}

function renderMonthlyTable(data) {
    const tbody = document.getElementById('monthlyList');
    tbody.innerHTML = '';
    if (!data.length) {
        tbody.innerHTML = `<tr><td colspan="6" style="text-align:center;">No data found.</td></tr>`;
        return;
    }
    data.forEach(emp => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${emp.E_id}</td>
            <td>${emp.Name}</td>
            <td>${emp.Attendance || '0'}</td>
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
