// assets/js/script.js

document.addEventListener('DOMContentLoaded', function() {
    // Validación de fechas en el formulario de disponibilidad
    const availabilityForm = document.getElementById('availability-form');
    if (availabilityForm) {
        availabilityForm.addEventListener('submit', function(e) {
            const checkIn = new Date(document.getElementById('check_in').value);
            const checkOut = new Date(document.getElementById('check_out').value);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            if (checkIn < today) {
                alert('La fecha de entrada no puede ser en el pasado.');
                e.preventDefault();
                return false;
            }
            
            if (checkOut <= checkIn) {
                alert('La fecha de salida debe ser posterior a la fecha de entrada.');
                e.preventDefault();
                return false;
            }
            
            return true;
        });
    }
    
    // Mostrar mensajes de alerta con fade out
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.classList.add('fade-out');
        }, 5000);
    });
    
    // Tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Popovers
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
    
    // Establecer fechas mínimas en los inputs de fecha
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('check_in')?.setAttribute('min', today);
    document.getElementById('check_out')?.setAttribute('min', today);
    
    // Actualizar fecha mínima de salida cuando cambia la fecha de entrada
    document.getElementById('check_in')?.addEventListener('change', function() {
        const checkIn = this.value;
        const checkOut = document.getElementById('check_out');
        checkOut.setAttribute('min', checkIn);
        
        if (checkOut.value && checkOut.value < checkIn) {
            checkOut.value = checkIn;
        }
    });
});

// Función para confirmar cancelación de reserva
function confirmCancel() {
    return confirm('¿Estás seguro de que deseas cancelar esta reserva?');
}