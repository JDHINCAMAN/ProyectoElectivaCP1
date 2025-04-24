// Esperar a que el DOM esté cargado
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar los componentes
    initializeModals();
    initializeDeleteConfirmations();
    initializeImagePreviews();
    initializeSearch();
    initializeAlerts();
    initializePriceFormatting();
});

// Inicializar modales
function initializeModals() {
    const modalTriggers = document.querySelectorAll('[data-toggle="modal"]');
    
    modalTriggers.forEach(trigger => {
        trigger.addEventListener('click', function(e) {
            e.preventDefault();
            const target = this.getAttribute('data-target');
            const modal = document.querySelector(target);
            
            if (modal) {
                modal.classList.add('show');
                
                // Cerrar modal al hacer clic fuera
                modal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        this.classList.remove('show');
                    }
                });
                
                // Cerrar modal con botón de cierre
                const closeButtons = modal.querySelectorAll('.close, [data-dismiss="modal"]');
                closeButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        modal.classList.remove('show');
                    });
                });
            }
        });
    });
}

// Inicializar confirmaciones de eliminación
function initializeDeleteConfirmations() {
    const deleteButtons = document.querySelectorAll('.delete-btn');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const confirmMessage = this.getAttribute('data-confirm') || '¿Estás seguro de que deseas eliminar este elemento?';
            
            if (confirm(confirmMessage)) {
                window.location.href = this.getAttribute('href');
            }
        });
    });
}

// Inicializar previsualizaciones de imagen
function initializeImagePreviews() {
    const imageInputs = document.querySelectorAll('input[type="file"][accept*="image"]');
    
    imageInputs.forEach(input => {
        input.addEventListener('change', function() {
            const previewContainer = document.querySelector(this.getAttribute('data-preview') || '#imagePreview');
            
            if (previewContainer) {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        previewContainer.innerHTML = `<img src="${e.target.result}" class="img-preview" alt="Vista previa">`;
                    }
                    
                    reader.readAsDataURL(this.files[0]);
                }
            }
        });
    });
}

// Inicializar búsqueda
function initializeSearch() {
    const searchForm = document.querySelector('.search-form');
    const searchInput = document.querySelector('.search-input');
    
    if (searchForm && searchInput) {
        searchForm.addEventListener('submit', function(e) {
            if (searchInput.value.trim() === '') {
                e.preventDefault();
                alert('Por favor, ingresa un término de búsqueda.');
            }
        });
    }
}

// Inicializar alertas
function initializeAlerts() {
    const alerts = document.querySelectorAll('.alert');
    
    alerts.forEach(alert => {
        // Añadir botón de cierre si no existe
        if (!alert.querySelector('.close')) {
            const closeButton = document.createElement('button');
            closeButton.className = 'close';
            closeButton.setAttribute('type', 'button');
            closeButton.innerHTML = '&times;';
            
            closeButton.addEventListener('click', function() {
                alert.style.display = 'none';
            });
            
            alert.appendChild(closeButton);
        }
        
        // Auto ocultar alertas después de 5 segundos
        setTimeout(() => {
            alert.style.display = 'none';
        }, 5000);
    });
}

// Formatear precios
function initializePriceFormatting() {
    const priceElements = document.querySelectorAll('.price-format');
    
    priceElements.forEach(element => {
        const price = parseFloat(element.textContent);
        if (!isNaN(price)) {
            element.textContent = formatPrice(price);
        }
    });
}

// Formatear precio
function formatPrice(price) {
    return price.toLocaleString('es-ES', {
        style: 'currency',
        currency: 'EUR'
    });
}

// Agregar al carrito
function addToCart(articuloId, nombre, precio) {
    // Obtener el carrito actual del almacenamiento local o crear uno nuevo
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    
    // Verificar si el artículo ya está en el carrito
    const existingItemIndex = cart.findIndex(item => item.id === articuloId);
    
    if (existingItemIndex !== -1) {
        // Incrementar la cantidad si ya existe
        cart[existingItemIndex].cantidad++;
    } else {
        // Añadir nuevo artículo al carrito
        cart.push({
            id: articuloId,
            nombre: nombre,
            precio: precio,
            cantidad: 1
        });
    }
    
    // Guardar el carrito actualizado
    localStorage.setItem('cart', JSON.stringify(cart));
    
    // Actualizar el contador del carrito
    updateCartCounter();
    
    // Mostrar notificación
    showNotification(`"${nombre}" ha sido añadido al carrito`);
}

// Actualizar contador del carrito
function updateCartCounter() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const cartCounter = document.querySelector('.cart-counter');
    
    if (cartCounter) {
        const totalItems = cart.reduce((total, item) => total + item.cantidad, 0);
        cartCounter.textContent = totalItems;
        
        if (totalItems > 0) {
            cartCounter.style.display = 'inline-flex';
        } else {
            cartCounter.style.display = 'none';
        }
    }
}

// Mostrar notificación
function showNotification(message) {
    // Crear elemento de notificación
    const notification = document.createElement('div');
    notification.className = 'notification fade-in';
    notification.textContent = message;
    
    // Añadir al DOM
    document.body.appendChild(notification);
    
    // Eliminar después de 3 segundos
    setTimeout(() => {
        notification.classList.add('fade-out');
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 500);
    }, 3000);
}

// Cargar el carrito al cargar la página
window.addEventListener('DOMContentLoaded', function() {
    updateCartCounter();
});