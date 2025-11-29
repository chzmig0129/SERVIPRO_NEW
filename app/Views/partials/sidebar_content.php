<?php 
$current_url = current_url(true)->getPath();
?>

<div class="px-3 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Principal</div>

<a href="<?= site_url('Inicio') ?>" 
   class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm transition-all duration-200 <?= $current_url == '/Inicio' ? 'bg-blue-50 text-blue-700 font-medium shadow-sm' : 'text-gray-700 hover:bg-gray-50' ?>">
    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
        <polyline points="9 22 9 12 15 12 15 22"/>
    </svg>
    Inicio
</a>

<div class="px-3 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider mt-4">Gestión</div>

<a href="<?= site_url('locations') ?>" 
   class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm transition-all duration-200 <?= $current_url == '/locations' ? 'bg-blue-50 text-blue-700 font-medium shadow-sm' : 'text-gray-700 hover:bg-gray-50' ?>">
    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"/>
        <path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"/>
        <path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"/>
    </svg>
    Plantas
</a>

<a href="<?= site_url('blueprints') ?>" 
   class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm transition-all duration-200 <?= $current_url == '/blueprints' ? 'bg-blue-50 text-blue-700 font-medium shadow-sm' : 'text-gray-700 hover:bg-gray-50' ?>">
    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M14.106 5.553a2 2 0 0 0 1.788 0l3.659-1.83A1 1 0 0 1 21 4.619v12.764a1 1 0 0 1-.553.894l-4.553 2.277a2 2 0 0 1-1.788 0l-4.212-2.106a2 2 0 0 0-1.788 0l-3.659 1.83A1 1 0 0 1 3 19.381V6.618a1 1 0 0 1 .553-.894l4.553-2.277a2 2 0 0 1 1.788 0z"/>
    </svg>
    Planos
</a>

<a href="<?= site_url('incidents') ?>" 
   class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm transition-all duration-200 <?= $current_url == '/incidents' ? 'bg-blue-50 text-blue-700 font-medium shadow-sm' : 'text-gray-700 hover:bg-gray-50' ?>">
    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="12" r="10"/>
        <line x1="12" y1="8" x2="12" y2="12"/>
        <line x1="12" y1="16" x2="12.01" y2="16"/>
    </svg>
    Evidencias
</a>

<a href="<?= site_url('registro_tecnico') ?>" 
   class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm transition-all duration-200 <?= $current_url == '/registro_tecnico' ? 'bg-blue-50 text-blue-700 font-medium shadow-sm' : 'text-gray-700 hover:bg-gray-50' ?>">
    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
        <circle cx="12" cy="7" r="4"></circle>
    </svg>
    Registro Técnico
</a>

<a href="<?= site_url('quejas') ?>" 
   class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm transition-all duration-200 <?= $current_url == '/quejas' ? 'bg-blue-50 text-blue-700 font-medium shadow-sm' : 'text-gray-700 hover:bg-gray-50' ?>">
    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
    </svg>
    Quejas
</a>

<a href="<?= site_url('ventas') ?>" 
   class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm transition-all duration-200 <?= $current_url == '/ventas' ? 'bg-blue-50 text-blue-700 font-medium shadow-sm' : 'text-gray-700 hover:bg-gray-50' ?>">
    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
    </svg>
    Ventas
</a> 