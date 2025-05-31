import './bootstrap';
import Alpine from 'alpinejs';

if (!window.Alpine) {
    window.Alpine = Alpine;

    document.addEventListener('livewire:initialized', () => {
        Alpine.start();
    });
}
