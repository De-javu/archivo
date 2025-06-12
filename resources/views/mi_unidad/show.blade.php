<x-layouts.app>
    {{-- Extiende el layout principal --}}
    <x-slot name="title">
        {{ __('Sub_Carpeta') }}
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
    @endif

        <div class="inline-block  ">
            
        <h1 class=" flex item-center text-2xl font-bold text-white-700 m-2 ">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-48 w-8 h-8 ">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 0 0-1.883 2.542l.857 6a2.25 2.25 0 0 0 2.227 1.932H19.05a2.25 2.25 0 0 0 2.227-1.932l.857-6a2.25 2.25 0 0 0-1.883-2.542m-16.5 0V6A2.25 2.25 0 0 1 6 3.75h3.879a1.5 1.5 0 0 1 1.06.44l2.122 2.12a1.5 1.5 0 0 0 1.06.44H18A2.25 2.25 0 0 1 20.25 9v.776" />
        </svg>
            <span class="ml-2">{{ __('Unidad:  ') }}  {{ $carpeta->nombre}}
        </span>

       {{--Utilizamos una validacion ternaria para determinara si tiene mas sub carpetas asia arriba o si ya es la ultima carpeta --}}
        </h1>
        <a href="{{$carpeta->carpeta_padre_id ? route('mi_unidad.carpeta', $carpeta->carpeta_padre_id) : route('mi_unidad.index')}}"
         class=" flex item-center m-4 text-blue-600 hover:underline   ">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 hover:text-blue-400">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
        </svg>

    <span class="ml-2">
            {{ __('Regresar') }}
    </span>
        </a>
    </div>

    <!-- Botón para abrir el modal -->

    <div class="flex justify-end mb-4 ">
        <button onclick="document.getElementById('modal').style.display='flex'"
            class="mb-4 bg-gray-600 hover:bg-gray-900 text-white px-4 py-5 rounded flex items-center">
         <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 10.5v6m3-3H9m4.06-7.19-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z" />
        </svg>
        <span class="ml-2">
            {{ __(' Crear Sub Carpeta') }}
        </span>
        </button>
        
    </div>

    <!--Modal -->
            <div id="modal" class="fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50 hidden">
                <div class="bg-white p-6 rounded shadow-lg w-full max-w-md mx-auto flex flex-col">
                    <h2
                        class="text-lg font-bold mb-4 text-gray-600">{{ __('SUB CARPETA') }}
                        value="{{ $carpeta->id }}"
                    </h2>
                    <form action="{{ route('mi_unidad.subcarpeta', ['carpeta' => $carpeta->id])}}" method="POST">
                        @csrf

                        <input type="text" name="nombre" placeholder="Nombre de la carpeta"
                            class="border rounded px-3 py-2 w-full mb-4  text-gray-600" required>
                            @error('nombre')
                                <div class="text-red-500 text-xs mb-2">{{ $message }}</div>
                            @enderror
                            <input type="text" value={{$carpeta->id}} name="carpeta_padre_id" hidden>
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

                <hr>






    @if(isset($subcarpetas) && count($subcarpetas))
                        <h2 class="font-bold mb-2 mt-2 p-4">CARPETAS:</h2>
                        <hr>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 mx-2 m-2">
            @foreach($subcarpetas as $subcarpeta)
                <div class="flex items-center p-2 border rounded bg-gray-300 hover:bg-gray-100 transition ">
                    {{-- Enlace a la carpeta --}}


                    <a href="{{ route('mi_unidad.carpeta', $subcarpeta->id) }}"
                        class="flex items-center text-gray-700 hover:text-yellow-600 transition">
                        <x-heroicon-o-folder class="w-12 h-12 text-black-600 hover:text-blue-200" />

                       {{-- Se crea el boto  para ver por encima de la carpeta tipo tooltip --}}
                        <div class="relative group inline-block">
                            <button>
                        <span class="text-gray-900 m-2 hover:text-gray-400">
                            {{ $subcarpeta->nombre }}
                        </span>
                            </button>
                            <span
                                class="absolute left-1/2 -translate-x-1/2 bottom-full mb-2 w-max px-2 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition pointer-events-none z-100">
                                {{ $subcarpeta->nombre }}
                            </span>
                        </div>

                    </a>

                    <div class="flex items-center ml-auto space-x-2 relative">
                     {{-- Botón principal --}}
                        <button onclick="toggleDropdown({{ $subcarpeta->id }})" type="button"
                            class="flex items-center px-4 py-4 bg-gray-900 text-white rounded hover:bg-gray-500 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 6.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 12.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 18.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5Z" />
                            </svg>
                        </button>

                        {{-- Dropdown --}}
                        <div id="dropdown-menu-{{ $subcarpeta->id }}"
                            class="hidden absolute left-0 mt-12 w-40 bg-white border rounded shadow-lg z-10">
                            <a href="{{ route('mi_unidad.carpeta', $subcarpeta->id) }}"
                                class="px-4 py-2 text-gray-700 hover:bg-gray-100 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 0 0-1.883 2.542l.857 6a2.25 2.25 0 0 0 2.227 1.932H19.05a2.25 2.25 0 0 0 2.227-1.932l.857-6a2.25 2.25 0 0 0-1.883-2.542m-16.5 0V6A2.25 2.25 0 0 1 6 3.75h3.879a1.5 1.5 0 0 1 1.06.44l2.122 2.12a1.5 1.5 0 0 0 1.06.44H18A2.25 2.25 0 0 1 20.25 9v.776" />
                                </svg>
                                <span class="text-gray-900 m-2 hover:text-gray-400">Abrir</span>
                            </a>

                          {{-- Se crea el Modela para editar el nombre d ela carpeta--}}
                            <div id="edit-modal-{{$subcarpeta->id}}"
                                class="fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50 hidden"                                
                                onclick="if(event.target === this) this.style.display='none'">
                                <div class="bg-white p-6 rounded shadow-lg w-full max-w-md mx-auto flex flex-col">
                                    <h2 class="text-lg font-bold mb-4 text-gray-600">
                                        {{ __('Cambie el nombre') }}
                                    </h2>
                                    <form action="{{ route('mi_unidad.update', $subcarpeta->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')

                                        <input type="text" name="nombre" value="{{ $subcarpeta->nombre }}"
                                            class="border rounded px-3 py-2 w-full mb-4  text-gray-600" required>
                                        @error('nombre')
                                            <div class="text-red-500 text-xs mb-2">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                        <div class="flex justify-end gap-2">
                                            <button type="button"
                                                onclick="document.getElementById('edit-modal-{{$subcarpeta->id}}').style.display='none'"
                                                class="px-4 py-2 bg-gray-600 text-white rounded">
                                                {{ __('Cerrar') }}
                                            </button>
                                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">
                                                {{ __('Actualizar') }}
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            {{-- Se crea el dropdown para editar --}}
                            <a href="#" class="px-4 py-2 text-gray-700 hover:bg-gray-100 flex items-center"
                                onclick="document.getElementById('edit-modal-{{$subcarpeta->id}}').style.display='flex'">

                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                                    <path
                                        d="M21.731 2.269a2.625 2.625 0 0 0-3.712 0l-1.157 1.157 3.712 3.712 1.157-1.157a2.625 2.625 0 0 0 0-3.712ZM19.513 8.199l-3.712-3.712-12.15 12.15a5.25 5.25 0 0 0-1.32 2.214l-.8 2.685a.75.75 0 0 0 .933.933l2.685-.8a5.25 5.25 0 0 0 2.214-1.32L19.513 8.2Z" />
                                </svg>
                                <span class="text-gray-900 m-2 hover:text-gray-400">
                                    Editar
                                </span>
                            </a>


                              {{-- Se crea el dropdown para eliminar--}}
                            <a href="#" class="px-4 py-2 text-gray-700 hover:bg-gray-100 flex items-center"
                                onclick="document.getElementById('eliminar-modal-{{$subcarpeta->id}}').style.display='flex'">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                </svg>

                                <span class="text-gray-900 m-2 hover:text-gray-400">
                                    Eliminar
                                </span>
                            </a>

                            {{-- Se crea el Modela para editar el nombre d ela carpeta--}}
                            <div id="eliminar-modal-{{$subcarpeta->id}}"
                                class="fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50 hidden"                                
                                onclick="if(event.target === this) this.style.display='none'">
                                <div class="bg-white p-6 rounded shadow-lg w-full max-w-md mx-auto flex flex-col">
                                    <h2 class="text-lg font-bold mb-4 text-gray-600">
                                        {{ __('Escribe el nombre de la carpeta a eliminar: ' )}}  {{$subcarpeta->nombre}}
                                    </h2>
                                    <form action="{{ route('mi_unidad.destroy', $subcarpeta->id) }}" method="POST">
                                        @csrf
                                        @method('delete')  
                                        <input type="hidden" name="carpeta" value="{{ $subcarpeta->id }}">
                                        <input type="text" name="nombre" id="input-nombre-{{ $subcarpeta->id }}"
                                                placeholder="Escribe el nombre de la carpeta para confirmar"
                                                class="border rounded px-3 py-2 w-full mb-4 text-gray-600" required
                                                oninput="validarNombreEliminar({{ $subcarpeta->id }}, '{{ addslashes($subcarpeta->nombre) }}')">

                                                <div id="error-nombre-{{ $subcarpeta->id }}" class="text-red-500 text-xs mb-2"></div>

                                                <div class="flex justify-end gap-2">
                                            <button type="button"
                                                onclick="document.getElementById('eliminar-modal-{{$subcarpeta->id}}').style.display='none'"
                                                class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-500">
                                                {{ __('Cancelar') }}
                                            </button>
                                            <button type="submit" class="px-4 py-2 bg-red-700 text-white rounded hover:bg-red-500">
                                                {{ __('Eliminar') }}
                                            </button>
                                        </div>

                                        <script>
                                                function validarNombreEliminar(id, nombreReal) {
                                                var input = document.getElementById('input-nombre-' + id);
                                                var errorDiv = document.getElementById('error-nombre-' + id);
                                                        if (input.value !== nombreReal) {
                                                        errorDiv.textContent = 'El nombre no coincide con la carpeta seleccionada para eliminar.';
                                                        } else {
                                                        errorDiv.textContent = '';
                                                        }
                                                        }
                                        </script> 

                                        {{-- Mensaje de error si el nombre no coincide --}}   
                                        @error('nombre')
                                            <div class="text-red-500 text-xs mb-2">
                                                {{ $message }}
                                            </div>
                                        @enderror                                    
                                                            

                                        
                                         
                                    </form>  

                                </div>
                            </div>
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
                
                //  Cierra el menú al hacer clic fuera
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

{{-- Fin del layout --}}
    </x-layouts.app>
