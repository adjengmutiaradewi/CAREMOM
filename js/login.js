document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(loginForm);
        const data = {
            username: formData.get('username'),
            password: formData.get('password')
        };
        
        // Simple validation
        if (!data.username || !data.password) {
            alert('Username dan password harus diisi!');
            return;
        }
        
        // Send login request
        fetch('api/login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.role === 'admin') {
                    window.location.href = 'admin.php';
                } else {
                    window.location.href = 'dashboard.php';
                }
            } else {
                alert('Login gagal: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat login');
        });
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(loginForm);
        const data = {
            username: formData.get('username'),
            password: formData.get('password')
        };
        
        // Simple validation
        if (!data.username || !data.password) {
            showAlert('Username dan password harus diisi!', 'error');
            return;
        }
        
        // Show loading
        const submitBtn = loginForm.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.textContent = 'Loading...';
        submitBtn.disabled = true;
        
        // Send login request
        fetch('api/login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('Login berhasil!', 'success');
                setTimeout(() => {
                    if (data.role === 'admin') {
                        window.location.href = 'admin.php';
                    } else {
                        window.location.href = 'dashboard.php';
                    }
                }, 1000);
            } else {
                showAlert('Login gagal: ' + data.message, 'error');
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Terjadi kesalahan saat login', 'error');
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
        });
    });
    
    function showAlert(message, type) {
        // Remove existing alerts
        const existingAlert = document.querySelector('.alert-message');
        if (existingAlert) {
            existingAlert.remove();
        }
        
        const alert = document.createElement('div');
        alert.className = `alert-message alert-${type}`;
        alert.textContent = message;
        alert.style.cssText = `
            padding: 1rem;
            margin: 1rem 0;
            border-radius: 8px;
            color: white;
            font-weight: 500;
            text-align: center;
        `;
        
        if (type === 'success') {
            alert.style.background = '#4CAF50';
        } else {
            alert.style.background = '#f44336';
        }
        
        loginForm.parentNode.insertBefore(alert, loginForm);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            alert.remove();
        }, 3000);
    }
});