<x-layouts.app>
    {{-- Extiende el layout principal --}}
    <x-slot name="title">
        {{ __('My Unit') }}
    </x-slot>

    {{-- mensaje de éxito --}}
    @if(session('success'))
        <div class="bg-green-500 text-white p-2 rounded mb-4">
            {{ session('success') }}
        </div>
        <script>
            setTimeout(() => {
                var msg = document.getElementById('success-message');
                if (msg) msg.style.display = 'none';
            }, 3000);
        </script>
    @endif

    <!-- Botón para abrir el modal -->
    <div class="flex justify-end mb-4">
        <button onclick="document.getElementById('modal').style.display='flex'"
            class="mb-4 bg-gray-600 hover:bg-gray-900 text-white px-4 py-2 rounded flex items-center">
            <x-heroicon-o-folder class="w-8 h-8 m-2 text-white-600 " />
            {{ __('Create Unit') }}
        </button>
    </div>

    <div id="modal" class="fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50 hidden">
        <div class="bg-white p-6 rounded shadow-lg w-full max-w-md mx-auto flex flex-col">
            <h2 class="text-lg font-bold mb-4 text-gray-600">{{ __('Crear nueva carpeta') }}
            </h2>
            <form action="{{ route('mi_unidad.store') }}" method="POST">
                @csrf

                <input type="text" name="nombre" placeholder="Nombre de la carpeta"
                    class="border rounded px-3 py-2 w-full mb-4  text-gray-600" required>
                @error('nombre')
                    <div class="text-red-500 text-xs mb-2">{{ $message }}</div>
                @enderror
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('modal').style.display='none'"
                        class="px-4 py-2 bg-gray-600 text-white rounded">
                        {{ __('Cerrar') }}
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">
                        {{ __('Crear') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    </form>
    <hr>

    {{--Lista de carpetas (opcional, si tienes $carpetas) --}}


    @if(isset($carpetas) && count($carpetas))
        <h2 class="font-bold mb-2 mt-2 p-4">CARPETAS:</h2>
        <hr>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 mx-1 m-2">

            @foreach($carpetas as $carpeta)
                <div class="flex items-center p-2 border rounded bg-gray-300 hover:bg-gray-100 transition">
                    {{-- Enlace a la carpeta --}}
                    <a href="{{ route('mi_unidad.carpeta', $carpeta->id) }}"
                        class="flex items-center text-gray-700 hover:text-yellow-600 transition">
                        <x-heroicon-o-folder class="w-12 h-12 text-black-600 hover:text-blue-200" />
                        <span class="text-gray-900 m-2 hover:text-gray-400">{{ $carpeta->nombre }}</span>
                    </a>

                    <div class="flex items-center ml-auto space-x-2 relative">
                        <!-- Botón principal -->
                        <button onclick="toggleDropdown({{ $carpeta->id }})" type="button"
                            class="flex items-center px-4 py-4 bg-gray-900 text-white rounded hover:bg-gray-500 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 6.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 12.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 18.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5Z" />
                            </svg>

                        </button>
                        <!-- Dropdown -->
                        <div id="dropdown-menu-{{ $carpeta->id }}"
                            class="hidden absolute left-0 mt-12 w-40 bg-white border rounded shadow-lg z-10">
                            <a href="{{ route('mi_unidad.carpeta', $carpeta->id) }}" class="px-4 py-2 text-gray-700 hover:bg-gray-100 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 0 0-1.883 2.542l.857 6a2.25 2.25 0 0 0 2.227 1.932H19.05a2.25 2.25 0 0 0 2.227-1.932l.857-6a2.25 2.25 0 0 0-1.883-2.542m-16.5 0V6A2.25 2.25 0 0 1 6 3.75h3.879a1.5 1.5 0 0 1 1.06.44l2.122 2.12a1.5 1.5 0 0 0 1.06.44H18A2.25 2.25 0 0 1 20.25 9v.776" />
                                </svg>
                                      <span class="text-gray-900 m-2 hover:text-gray-400">Abrir</span>
                            </a>
                            <a href="#" class="px-4 py-2 text-gray-700 hover:bg-gray-100 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                                    <path
                                        d="M21.731 2.269a2.625 2.625 0 0 0-3.712 0l-1.157 1.157 3.712 3.712 1.157-1.157a2.625 2.625 0 0 0 0-3.712ZM19.513 8.199l-3.712-3.712-12.15 12.15a5.25 5.25 0 0 0-1.32 2.214l-.8 2.685a.75.75 0 0 0 .933.933l2.685-.8a5.25 5.25 0 0 0 2.214-1.32L19.513 8.2Z" />
                                </svg>
                                <span class="text-gray-900 m-2 hover:text-gray-400">Editar</span>
                            </a>
                            <a href="#" class="px-4 py-2 text-gray-700 hover:bg-gray-100 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                </svg>

                                <span class="text-gray-900 m-2 hover:text-gray-400">Eliminar</span>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach

            <script>
                function toggleDropdown(id) {
                    document.querySelectorAll('[id^="dropdown-menu-"]').forEach(function (menu) {
                        if (menu.id === 'dropdown-menu-' + id) {
                            menu.classList.toggle('hidden');
                        } else {
                            menu.classList.add('hidden');
                        }
                    });
                }
                // Cierra el menú al hacer clic fuera
                document.addEventListener('click', function (event) {
                    document.querySelectorAll('[id^="dropdown-menu-"]').forEach(function (menu) {
                        if (!menu.contains(event.target) && !menu.previousElementSibling.contains(event.target)) {
                            menu.classList.add('hidden');
                        }
                    });
                });
            </script>
        </div>

    @endif

    {{-- Script para cerrar el modal al hacer click fuera del contenido --}}
    <script>
        document.addEventListener('click', function (event) {
            var modal = document.getElementById('modal');
            if (modal && event.target === modal) {
                modal.style.display = 'none';
            }
        });
    </script>

</x-layouts.app>>