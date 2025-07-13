import './bootstrap';
import 'preline';
import Alpine from 'alpinejs';
import axios from 'axios';
import mammoth from 'mammoth';

window.mammoth = mammoth;
window.axios = axios;

window.Alpine = Alpine;

Alpine.start();
