var today = new Date().toISOString().split('T')[0];
document.getElementById('dataPassagem').setAttribute('min', today);