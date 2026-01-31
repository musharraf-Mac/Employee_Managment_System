/**
 * Admin Dashboard JavaScript
 * Handles modal interactions and form submissions
 */

// Open Employee Add Modal
function openf() {
    const modal = document.getElementById('emp_add_c');
    modal.classList.add('active');
}

// Close Employee Add Modal
function closef() {
    const modal = document.getElementById('emp_add_c');
    modal.classList.remove('active');
}

// Close modal when clicking outside the modal card
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('emp_add_c');
    
    if (modal) {
        // Close when clicking outside the card
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closef();
            }
        });
    }
    
    // Handle form submission
    const form = document.getElementById('Emp_add');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            submitEmployeeForm();
        });
    }
    
    // Load admin name from session
    const adminName = sessionStorage.getItem('admin_name');
    if (adminName) {
        const nameElement = document.getElementById('admin-name');
        if (nameElement) {
            nameElement.textContent = adminName.split(' ')[0];
        }
    } else {
        // If no session, redirect to login
        alert('Session expired. Please login again.');
        window.location.href = 'login.html';
    }
});

// Submit Employee Form
function submitEmployeeForm() {
    const form = document.getElementById('Emp_add');
    const formData = new FormData(form);
    
    // Show loading state
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
    submitBtn.disabled = true;
    
    // Send to backend with credentials to include session
    fetch('PHP/emp_add.php', {
        method: 'POST',
        body: formData,
        credentials: 'include' // IMPORTANT: Include session cookies
    })
    .then(response => {
        // Try to parse as JSON first
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            return response.json().then(data => ({
                ok: response.ok,
                status: response.status,
                data: data,
                isJson: true
            }));
        } else {
            return response.text().then(text => ({
                ok: response.ok,
                status: response.status,
                text: text,
                isJson: false
            }));
        }
    })
    .then(result => {
        // Reset button
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        
        console.log('Response:', result);
        
        if (result.ok || result.status === 200) {
            const message = result.isJson ? result.data.message : result.text;
            alert('✓ ' + message);
            form.reset();
            closef();
            // Optionally refresh the page
            location.reload();
        } else {
            const errorMsg = result.isJson ? result.data.message : result.text;
            alert('✗ Error: ' + errorMsg);
        }
    })
    .catch(error => {
        // Reset button
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        console.error('Error:', error);
        alert('❌ Error adding employee: ' + error.message);
    });
}

// Open Edit Employees (redirect to edit page)
function openU() {
    window.location.href = 'E_details.html';
}

// Logout function
function logout() {
    if (confirm('Are you sure you want to logout?')) {
        sessionStorage.clear();
        window.location.href = 'index.html';
    }
}

function viewEmployees(){
    window.location.href = "E_det_rep.html"
}