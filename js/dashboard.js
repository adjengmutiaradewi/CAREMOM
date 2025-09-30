// Dashboard functionality
let formData = {
    data_diri: {},
    kondisi_kesehatan: {},
    pola_makan: {}
};

function showStep(stepId) {
    // Hide all steps
    document.querySelectorAll('.step-content').forEach(step => {
        step.classList.remove('active');
    });
    
    // Show selected step
    document.getElementById(stepId).classList.add('active');
    
    // Update step indicator
    document.querySelectorAll('.step').forEach(step => {
        step.classList.remove('active');
    });
    document.querySelector(`.step[data-step="${stepId}"]`).classList.add('active');
}

function nextStep(nextStepId) {
    // Save current form data
    saveFormData();
    showStep(nextStepId);
}

function prevStep(prevStepId) {
    showStep(prevStepId);
}

function saveFormData() {
    // Save data from current form
    const currentStep = document.querySelector('.step-content.active');
    const formId = currentStep.querySelector('form').id;
    
    const formElements = currentStep.querySelector('form').elements;
    const formDataObj = {};
    
    for (let element of formElements) {
        if (element.name) {
            formDataObj[element.name] = element.value;
        }
    }
    
    if (formId === 'formDataDiri') {
        formData.data_diri = formDataObj;
    } else if (formId === 'formKondisiKesehatan') {
        formData.kondisi_kesehatan = formDataObj;
    } else if (formId === 'formPolaMakan') {
        formData.pola_makan = formDataObj;
    }
}

async function submitAllData() {
    saveFormData();
    
    // Show loading
    document.getElementById('loading').style.display = 'block';
    document.getElementById('results-container').style.display = 'none';
    
    showStep('hasil-rekomendasi');
    
    try {
        const response = await fetch('api/process_recommendation.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(formData)
        });
        
        const results = await response.json();
        
        if (results.success) {
            displayResults(results.data);
        } else {
            alert('Error: ' + results.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat memproses data');
    }
}

function displayResults(data) {
    const container = document.getElementById('results-container');
    
    let html = `
        <div class="recommendation-section">
            <h3>📊 Ringkasan Kebutuhan Gizi</h3>
            <div class="recommendation-item">
                <strong>Total Kalori Harian:</strong> ${data.total_kalori} kkal<br>
                <strong>Total Protein Harian:</strong> ${data.total_protein} gram<br>
                <strong>Trimester:</strong> ${data.trimester}
            </div>
        </div>
    `;
    
    if (data.rekomendasi_gizi && data.rekomendasi_gizi.length > 0) {
        html += `
            <div class="recommendation-section">
                <h3>🍽️ Rekomendasi Pola Makan</h3>
                ${data.rekomendasi_gizi.map(rec => `
                    <div class="recommendation-item">${rec}</div>
                `).join('')}
            </div>
        `;
    }
    
    if (data.rekomendasi_suplemen && data.rekomendasi_suplemen.length > 0) {
        html += `
            <div class="recommendation-section">
                <h3>💊 Rekomendasi Suplemen</h3>
                ${data.rekomendasi_suplemen.map(rec => `
                    <div class="recommendation-item">${rec}</div>
                `).join('')}
            </div>
        `;
    }
    
    if (data.catatan_khusus) {
        html += `
            <div class="recommendation-section">
                <h3>📝 Catatan Khusus</h3>
                <div class="recommendation-item">${data.catatan_khusus}</div>
            </div>
        `;
    }
    
    container.innerHTML = html;
    document.getElementById('loading').style.display = 'none';
    container.style.display = 'block';
}