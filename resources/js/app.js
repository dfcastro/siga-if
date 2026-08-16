import './bootstrap';

// Importamos APENAS os plugins que queremos adicionar.
import mask from '@alpinejs/mask';

// Não importamos mais 'alpinejs' aqui, pois o Livewire já o fornece.

// Esperamos o evento 'alpine:init', que é disparado pelo Alpine do Livewire.
document.addEventListener('alpine:init', () => {
    // Adicionamos nosso plugin de máscara à instância ÚNICA do Alpine.
    window.Alpine.plugin(mask);
});

// Não há 'Alpine.start()' aqui. O Livewire cuida disso.