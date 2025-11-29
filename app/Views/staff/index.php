<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Encabezado -->
    <div class="bg-gradient-to-r from-blue-500 to-blue-700 rounded-xl p-6 text-white shadow-lg text-center mb-6">
        <div class="flex flex-col items-center justify-center">
            <h1 class="text-3xl font-bold text-white mb-2">Personal</h1>
            <p class="text-blue-100">Gestión de empleados y técnicos por sede</p>
        </div>
    </div>

    <!-- Controles -->
    <div class="flex flex-col md:flex-row gap-4 items-start md:items-center justify-between">
        <!-- Selector de Sede -->
        <select id="location-select" class="w-full md:w-64 p-2 border border-white rounded-lg bg-white text-blue-700 font-medium focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm">
            <option value="">Seleccionar Sede</option>
            <option value="1">Sede Central</option>
            <option value="2">Sede Norte</option>
            <option value="3">Sede Sur</option>
        </select>

        <div class="flex gap-4 w-full md:w-auto">
            <!-- Campo de Búsqueda -->
            <div id="search-container" class="hidden w-full md:w-64 relative">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search absolute left-2 top-2.5 h-4 w-4 text-gray-400">
                    <circle cx="11" cy="11" r="8"/>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input
                    id="search-input"
                    type="text"
                    placeholder="Buscar personal..."
                    class="w-full pl-8 p-2 border border-gray-300 rounded-lg"
                />
            </div>

            <!-- Botón Agregar Personal -->
            <button class="flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user-plus">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <line x1="19" y1="8" x2="19" y2="14"/>
                    <line x1="16" y1="11" x2="22" y2="11"/>
                </svg>
                Agregar Personal
            </button>
        </div>
    </div>

    <!-- Lista de Personal -->
    <div id="staff-container" class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        <!-- Estado inicial -->
        <div id="empty-state" class="col-span-full flex flex-col items-center justify-center py-12 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users text-gray-400 mb-4">
                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
            <h2 class="text-xl font-semibold mb-2">Seleccione una Sede</h2>
            <p class="text-gray-500">Elija una sede para ver el personal asignado</p>
        </div>
    </div>
</div>

<!-- Script para manejar la lógica -->
<script>
    const locations = [
        {
            id: "1",
            name: "Sede Central",
            staff: [
                {
                    id: "1",
                    name: "Juan Pérez",
                    position: "Técnico Senior",
                    email: "juan.perez@empresa.com",
                    phone: "+57 300 123 4567",
                    avatar: "https://ui-avatars.com/api/?name=Juan+Perez&background=random",
                    status: "active",
                    assignedTraps: 15
                },
                {
                    id: "2",
                    name: "María López",
                    position: "Supervisora",
                    email: "maria.lopez@empresa.com",
                    phone: "+57 300 765 4321",
                    avatar: "https://ui-avatars.com/api/?name=Maria+Lopez&background=random",
                    status: "active",
                    assignedTraps: 8
                }
            ]
        },
        {
            id: "2",
            name: "Sede Norte",
            staff: [
                {
                    id: "3",
                    name: "Carlos Rodríguez",
                    position: "Técnico Junior",
                    email: "carlos.rodriguez@empresa.com",
                    phone: "+57 300 987 6543",
                    avatar: "https://ui-avatars.com/api/?name=Carlos+Rodriguez&background=random",
                    status: "inactive",
                    assignedTraps: 5
                }
            ]
        }
    ];

    const locationSelect = document.getElementById("location-select");
    const searchContainer = document.getElementById("search-container");
    const searchInput = document.getElementById("search-input");
    const staffContainer = document.getElementById("staff-container");
    const emptyState = document.getElementById("empty-state");

    locationSelect.addEventListener("change", (event) => {
        const selectedLocationId = event.target.value;
        if (selectedLocationId) {
            searchContainer.classList.remove("hidden");
            emptyState.classList.add("hidden");
            const selectedLocation = locations.find((loc) => loc.id === selectedLocationId);
            renderStaff(selectedLocation.staff);
        } else {
            searchContainer.classList.add("hidden");
            staffContainer.innerHTML = "";
            emptyState.classList.remove("hidden");
        }
    });

    searchInput.addEventListener("input", (event) => {
        const query = event.target.value.toLowerCase();
        const selectedLocationId = locationSelect.value;
        if (selectedLocationId) {
            const selectedLocation = locations.find((loc) => loc.id === selectedLocationId);
            const filteredStaff = selectedLocation.staff.filter((person) =>
                person.name.toLowerCase().includes(query) ||
                person.position.toLowerCase().includes(query) ||
                person.email.toLowerCase().includes(query)
            );
            renderStaff(filteredStaff);
        }
    });

    function renderStaff(staff) {
        staffContainer.innerHTML = staff
            .map(
                (person) => `
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center gap-4">
                                <img src="${person.avatar}" alt="${person.name}" class="w-12 h-12 rounded-full">
                                <div>
                                    <h2 class="text-xl font-bold">${person.name}</h2>
                                    <p class="text-sm text-gray-500">${person.position}</p>
                                </div>
                            </div>
                            <span class="px-2 py-1 text-xs rounded-full ${
                                person.status === 'active' 
                                    ? 'bg-green-100 text-green-800' 
                                    : 'bg-gray-100 text-gray-800'
                            }">
                                ${person.status === 'active' ? 'Activo' : 'Inactivo'}
                            </span>
                        </div>
                        <div class="space-y-2 text-sm text-gray-600">
                            <div class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-mail">
                                    <rect width="20" height="16" x="2" y="4" rx="2"/>
                                    <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                                </svg>
                                ${person.email}
                            </div>
                            <div class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-phone">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                                </svg>
                                ${person.phone}
                            </div>
                            <div class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-mouse-pointer-click">
                                    <path d="m9 9 5 12 1.8-5.2L21 14Z"/>
                                    <path d="M7.2 2.2 8 5.1"/>
                                    <path d="m5.1 8-2.9-.8"/>
                                    <path d="M14 7.2l1-3.1"/>
                                    <path d="m7.2 14-3.1 1"/>
                                </svg>
                                ${person.assignedTraps} trampas asignadas
                            </div>
                        </div>
                        <div class="mt-4 flex justify-end gap-2">
                            <button class="px-3 py-1 text-sm text-gray-600 hover:text-gray-800">
                                Editar
                            </button>
                            <button class="px-3 py-1 text-sm text-red-600 hover:text-red-800">
                                Eliminar
                            </button>
                        </div>
                    </div>
                </div>
            `
            )
            .join("");
    }
</script>
<?= $this->endSection() ?> 