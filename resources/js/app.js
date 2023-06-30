import './bootstrap';

import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';
import Toaster from '../../vendor/masmerise/livewire-toaster/resources/js'; // livewire-toaster package

window.Alpine = Alpine;

Alpine.plugin(focus);
Alpine.plugin(Toaster); // livewire-toaster package

Alpine.start();
