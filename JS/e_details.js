/**
 * Employee Details Management JavaScript
 */

let currentEditEmployee = null;
// let currentCheckinEmployee = null; // removed

// Load employees when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Check admin session
    const adminName = sessionStorage.getItem('admin_name');
    if (!adminName) {
        alert('Session expired. Please login again.');
        window.location.href = 'login.html';
        return;
    }

    loadEmployees();

    // Handle edit form submission
    document.getElementById('editForm').addEventListener('submit', handleEditSubmit);
    // document.getElementById('checkinForm').addEventListener('submit', handleCheckinSubmit); // removed
});

// Load all employees from database
function loadEmployees() {
    console.log('Starting to load employees...');
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
    window.allEmployees = employees;

    tbody.innerHTML = employees.map(emp => `
        <tr>
            <td>${escapeHtml(emp.E_id)}</td>
            <td>${escapeHtml(emp.First_Name)}</td>
            <td>${escapeHtml(emp.Last_Name)}</td>
            <td>${escapeHtml(emp.Department)}</td>
            <td>${escapeHtml(emp.Phone || '')}</td>
            <td>${escapeHtml(emp.email)}</td>
            <td>
                <div class="action-buttons">
                    <button class="btn-edit" onclick="openEditModal('${emp.E_id}')">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn-delete" onclick="deleteEmployee('${emp.E_id}')">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

// Show empty state message
function showEmptyState(message) {
    const tbody = document.getElementById('employeeList');
    tbody.innerHTML = `
        <tr>
            <td colspan="7" style="text-align: center; padding: 60px 20px;">
                <i class="fas fa-inbox" style="font-size: 3em; color: rgba(255, 255, 255, 0.5); margin-bottom: 20px; display: block;"></i>
                <p style="color: rgba(255, 255, 255, 0.7); font-size: 1.1em;">${message}</p>
            </td>
        </tr>
    `;
}

// Open select edit modal (big button)
function openSelectEditModal() {
    document.getElementById('selectEditModal').classList.add('active');
    displayEmployeeSelectList();
}

// Close select edit modal
function closeSelectEditModal() {
    document.getElementById('selectEditModal').classList.remove('active');
}

// Display list of employees for selection
function displayEmployeeSelectList() {
    const selectList = document.getElementById('employeeSelectList');
    
    if (!window.allEmployees || window.allEmployees.length === 0) {
        selectList.innerHTML = '<p style="padding: 20px; text-align: center; color: #999;">No employees found</p>';
        return;
    }
    
    selectList.innerHTML = window.allEmployees.map(emp => `
        <div style="padding: 15px; border-bottom: 1px solid #f0f0f0; cursor: pointer; transition: background 0.3s ease;" 
             onmouseover="this.style.background='#f9f9f9'" 
             onmouseout="this.style.background='white'"
             onclick="selectEmployeeToEdit('${emp.E_id}')">
            <div style="font-weight: 600; color: #333;">${escapeHtml(emp.First_Name)} ${escapeHtml(emp.Last_Name)}</div>
            <div style="font-size: 0.9em; color: #666;">ID: ${escapeHtml(emp.E_id)} | Department: ${escapeHtml(emp.Department)}</div>
        </div>
    `).join('');
}

// Filter employee select list
function filterSelectList() {
    const searchInput = document.getElementById('selectEditSearch').value.toLowerCase();
    const selectList = document.getElementById('employeeSelectList');
    
    if (!window.allEmployees) {
        return;
    }
    
    const filtered = window.allEmployees.filter(emp => {
        return emp.E_id.toLowerCase().includes(searchInput) ||
               emp.First_Name.toLowerCase().includes(searchInput) ||
               emp.Last_Name.toLowerCase().includes(searchInput);
    });
    
    if (filtered.length === 0) {
        selectList.innerHTML = '<p style="padding: 20px; text-align: center; color: #999;">No employees found</p>';
        return;
    }
    
    selectList.innerHTML = filtered.map(emp => `
        <div style="padding: 15px; border-bottom: 1px solid #f0f0f0; cursor: pointer; transition: background 0.3s ease;" 
             onmouseover="this.style.background='#f9f9f9'" 
             onmouseout="this.style.background='white'"
             onclick="selectEmployeeToEdit('${emp.E_id}')">
            <div style="font-weight: 600; color: #333;">${escapeHtml(emp.First_Name)} ${escapeHtml(emp.Last_Name)}</div>
            <div style="font-size: 0.9em; color: #666;">ID: ${escapeHtml(emp.E_id)} | Department: ${escapeHtml(emp.Department)}</div>
        </div>
    `).join('');
}

// Select employee and open edit modal
function selectEmployeeToEdit(employeeId) {
    closeSelectEditModal();
    openEditModal(employeeId);
}

// Open edit modal and load employee data
function openEditModal(employeeId) {
    // Show loading state
    document.getElementById('editModal').classList.add('active');
    
    fetch(`PHP/get_employee.php?E_id=${encodeURIComponent(employeeId)}`, {
        method: 'GET',
        credentials: 'include'
    })
    .then(response => {
        // Handle both JSON and text responses
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            return response.json().then(data => ({
                ok: response.ok,
                data: data
            }));
        } else {
            return response.text().then(text => ({
                ok: response.ok,
                data: { success: false, message: text }
            }));
        }
    })
    .then(result => {
        console.log('Employee data response:', result);
        
        if (result.ok && result.data.success) {
            const emp = result.data.employee;
            currentEditEmployee = emp;
            
            // Populate the form with employee data
            document.getElementById('editE_id').value = emp.E_id || '';
            document.getElementById('editFirstName').value = emp.First_Name || '';
            document.getElementById('editLastName').value = emp.Last_Name || '';
            document.getElementById('editDepartment').value = emp.Department || '';
            document.getElementById('editPhone').value = emp.Phone || emp.phone || '';
            document.getElementById('editEmail').value = emp.email || '';
            
            console.log('Form populated with:', emp);
            // Modal is already open from above
        } else {
            document.getElementById('editModal').classList.remove('active');
            alert('❌ Error loading employee details: ' + (result.data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        document.getElementById('editModal').classList.remove('active');
        alert('❌ Failed to load employee details: ' + error.message);
    });
}


// Close edit modal
function closeEditModal() {
    document.getElementById('editModal').classList.remove('active');
    currentEditEmployee = null;
}

// Handle edit form submission
function handleEditSubmit(e) {
    e.preventDefault();
    
    const employeeId = document.getElementById('editE_id').value;
    
    const data = {
        E_id: employeeId,
        First_Name: document.getElementById('editFirstName').value,
        Last_Name: document.getElementById('editLastName').value,
        Department: document.getElementById('editDepartment').value,
        phone: document.getElementById('editPhone').value,
        email: document.getElementById('editEmail').value
    };

    fetch('PHP/update_employee.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data),
        credentials: 'include'
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert('✓ Employee details updated successfully!');
            closeEditModal();
            loadEmployees();
        } else {
            alert('✗ Error: ' + result.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('❌ Error updating employee details');
    });
}

// Delete employee
function deleteEmployee(employeeId) {
    if (!confirm(`Are you sure you want to delete employee ${employeeId}? This action cannot be undone.`)) {
        return;
    }

    fetch('PHP/delete_employee.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ E_id: employeeId }),
        credentials: 'include'
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert('✓ Employee deleted successfully!');
            loadEmployees();
        } else {
            alert('✗ Error: ' + result.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('❌ Error deleting employee');
    });
}

// Go back to admin dashboard
function goBack() {
    window.location.href = 'Admin_db.html';
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
    const editModal = document.getElementById('editModal');
    if (e.target === editModal) {
        closeEditModal();
    }
});

// Search employees function
function searchEmployees() {
    const searchInput = document.getElementById('searchInput').value.toLowerCase();
    const tbody = document.getElementById('employeeList');
    
    if (!window.allEmployees) {
        return;
    }
    
    // Filter employees based on search query
    const filteredEmployees = window.allEmployees.filter(emp => {
        const E_id = emp.E_id.toLowerCase();
        const firstName = emp.First_Name.toLowerCase();
        const lastName = emp.Last_Name.toLowerCase();
        const department = emp.Department.toLowerCase();
        const phone = (emp.Phone || '').toLowerCase();
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
                <td colspan="7" style="text-align: center; padding: 40px;">
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
            <td>${escapeHtml(emp.First_Name)}</td>
            <td>${escapeHtml(emp.Last_Name)}</td>
            <td>${escapeHtml(emp.Department)}</td>
            <td>${escapeHtml(emp.Phone || '')}</td>
            <td>${escapeHtml(emp.email)}</td>
            <td>
                <div class="action-buttons">
                    <button class="btn-edit" onclick="openEditModal('${emp.E_id}')">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn-delete" onclick="deleteEmployee('${emp.E_id}')">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

