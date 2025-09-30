document.addEventListener('DOMContentLoaded', function() {
    const registerForm = document.getElementById('registerForm');
    
    registerForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        
        // Validasi password
        if (password !== confirmPassword) {
            alert('Password dan konfirmasi password tidak sama!');
            return;
        }
        
        if (password.length < 6) {
            alert('Password harus minimal 6 karakter!');
            return;
        }
        
        const formData = new FormData(registerForm);
        const data = {
            username: formData.get('username'),
            email: formData.get('email'),
            password: formData.get('password')
        };
        
        // Send register request
        fetch('api/register.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Registrasi berhasil! Silakan login.');
                window.location.href = 'login.html';
            } else {
                alert('Registrasi gagal: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat registrasi');
        });
    });
});