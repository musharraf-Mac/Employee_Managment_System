/**
 * Public Employee Directory JavaScript
 * Read-only employee information display
 */

let allEmployeesData = null;

// Load employees when page loads
document.addEventListener('DOMContentLoaded', function() {
    loadEmployees();
});

// Load all employees from database
function loadEmployees() {
    console.log('Loading employees for public directory...');
    fetch('PHP/get_employees.php', {
        method: 'GET',
        credentials: 'include'
    })
    .then(response => {
        console.log('Response status:', response.status);
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            return response.json();
        } else {
            return response.text().then(text => { 
                console.error('Non-JSON response:', text);
                throw new Error(text); 
            });
        }
    })
    .then(data => {
        console.log('Employee data loaded:', data);
        if (data.success) {
            console.log('Employees:', data.employees);
            displayEmployees(data.employees);
        } else {
            showEmptyState('Error loading employees: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        showEmptyState('Failed to load employees. Please refresh the page.');
    });
}

// Display employees in table
function displayEmployees(employees) {
    const tbody = document.getElementById('employeeList');
    
    if (employees.length === 0) {
        showEmptyState('No employees found');
        return;
    }

    // Store all employees for search functionality
    allEmployeesData = employees;

    tbody.innerHTML = employees.map(emp => `
        <tr>
            <td>${escapeHtml(emp.E_id)}</td>
            <td>${escapeHtml(emp.First_Name)} ${escapeHtml(emp.Last_Name)}</td>
            <td>${escapeHtml(emp.Department)}</td>
            <td>${escapeHtml(emp.phone)}</td>
            <td>
                <button class="view-details-btn" onclick="openDetailsModal('${emp.E_id}')">
                    <i class="fas fa-eye"></i> View Details
                </button>
            </td>
        </tr>
    `).join('');
}

// Show empty state message
function showEmptyState(message) {
    const tbody = document.getElementById('employeeList');
    tbody.innerHTML = `
        <tr>
            <td colspan="5" style="text-align: center; padding: 60px 20px;">
                <i class="fas fa-inbox" style="font-size: 3em; color: rgba(255, 255, 255, 0.5); margin-bottom: 20px; display: block;"></i>
                <p style="color: rgba(255, 255, 255, 0.7); font-size: 1.1em;">${message}</p>
            </td>
        </tr>
    `;
}

// Open details modal
function openDetailsModal(employeeId) {
    const employee = allEmployeesData.find(emp => emp.E_id === employeeId);
    
    if (!employee) {
        alert('Employee not found');
        return;
    }

    // Populate modal with employee data
    document.getElementById('modalEmployeeName').textContent = `${escapeHtml(employee.First_Name)} ${escapeHtml(employee.Last_Name)}`;
    document.getElementById('detailE_id').textContent = escapeHtml(employee.E_id);
    document.getElementById('detailFirstName').textContent = escapeHtml(employee.First_Name);
    document.getElementById('detailLastName').textContent = escapeHtml(employee.Last_Name);
    document.getElementById('detailDepartment').textContent = escapeHtml(employee.Department);
    document.getElementById('detailPhone').textContent = escapeHtml(employee.phone);
    document.getElementById('detailEmail').textContent = escapeHtml(employee.email);

    document.getElementById('detailsModal').classList.add('active');
}

// Close details modal
function closeDetailsModal() {
    document.getElementById('detailsModal').classList.remove('active');
}

// Search employees function
function searchEmployees() {
    const searchInput = document.getElementById('searchInput').value.toLowerCase();
    const tbody = document.getElementById('employeeList');
    
    if (!allEmployeesData) {
        return;
    }
    
    // Filter employees based on search query
    const filteredEmployees = allEmployeesData.filter(emp => {
        const E_id = emp.E_id.toLowerCase();
        const firstName = emp.First_Name.toLowerCase();
        const lastName = emp.Last_Name.toLowerCase();
        const department = emp.Department.toLowerCase();
        const phone = emp.phone.toLowerCase();
        const email = emp.email.toLowerCase();
        
        return E_id.includes(searchInput) || 
               firstName.includes(searchInput) || 
               lastName.includes(searchInput) || 
               department.includes(searchInput) ||
               phone.includes(searchInput) ||
               email.includes(searchInput);
    });
    
    // Display filtered results
    if (filteredEmployees.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="5" style="text-align: center; padding: 40px;">
                    <i class="fas fa-search" style="font-size: 2em; color: #ccc; margin-bottom: 10px;"></i>
                    <p style="color: #666; font-size: 1.1em;">No employees found matching "<strong>${escapeHtml(searchInput)}</strong>"</p>
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = filteredEmployees.map(emp => `
        <tr>
            <td>${escapeHtml(emp.E_id)}</td>
            <td>${escapeHtml(emp.First_Name)} ${escapeHtml(emp.Last_Name)}</td>
            <td>${escapeHtml(emp.Department)}</td>
            <td>${escapeHtml(emp.phone)}</td>
            <td>
                <button class="view-details-btn" onclick="openDetailsModal('${emp.E_id}')">
                    <i class="fas fa-eye"></i> View Details
                </button>
            </td>
        </tr>
    `).join('');
}

// Go back to home
function goBack() {
    window.location.href = 'index.html';
}

// Escape HTML to prevent XSS
function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}

// Close modal when clicking outside
document.addEventListener('click', function(e) {
    const modal = document.getElementById('detailsModal');
    
    if (e.target === modal) {
        closeDetailsModal();
    }
});
