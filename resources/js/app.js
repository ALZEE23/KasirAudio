import './bootstrap';
import Alpine from 'alpinejs';

// Check if Alpine is already initialized
if (!window.Alpine) {
    window.Alpine = Alpine;

    // Wait for Livewire before starting Alpine
    document.addEventListener('livewire:initialized', () => {
        Alpine.start();
    });
}
