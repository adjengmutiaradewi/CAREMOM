// Admin dashboard functionality
function showSection(sectionName) {
    // Hide all sections
    document.querySelectorAll('.admin-section').forEach(section => {
        section.classList.remove('active');
    });
    
    // Show selected section
    document.getElementById(sectionName + '-section').classList.add('active');
    
    // Update menu active state
    document.querySelectorAll('.admin-menu a').forEach(link => {
        link.classList.remove('active');
    });
    event.target.classList.add('active');
}

function showAddRuleForm() {
    document.getElementById('rule-form-title').textContent = 'Add New Rule';
    document.getElementById('ruleForm').reset();
    document.getElementById('rule_id').value = '';
    document.getElementById('rule-form').style.display = 'block';
}

function hideRuleForm() {
    document.getElementById('rule-form').style.display = 'none';
}

function editRule(ruleId) {
    // Fetch rule data and populate form
    fetch('api/get_rule.php?id=' + ruleId)
        .then(response => response.json())
        .then(rule => {
            document.getElementById('rule-form-title').textContent = 'Edit Rule';
            document.getElementById('rule_id').value = rule.id;
            document.getElementById('kode_aturan').value = rule.kode_aturan;
            document.getElementById('nama_aturan').value = rule.nama_aturan;
            document.getElementById('kondisi').value = rule.kondisi;
            document.getElementById('aksi').value = rule.aksi;
            document.getElementById('keterangan').value = rule.keterangan;
            document.getElementById('rule-form').style.display = 'block';
        });
}

function toggleRule(ruleId, isActive) {
    if (confirm('Are you sure you want to ' + (isActive ? 'disable' : 'enable') + ' this rule?')) {
        fetch('api/toggle_rule.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                rule_id: ruleId,
                is_active: !isActive
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        });
    }
}

// Rule form submission
document.getElementById('ruleForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    
    fetch('api/save_rule.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Rule saved successfully!');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    });
});