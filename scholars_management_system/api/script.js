document.getElementById('applicationForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    
    const response = await fetch('api/controllers/application.php', {
        method: 'POST',
        body: formData
    });
    
    const result = await response.json();
    alert(result.message);
});