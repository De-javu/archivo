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
            if(msg) msg.style.display = 'none';
        }, 3000);
    @endif

    <!-- Botón para abrir el modal -->
    <div class="flex justify-end mb-4">
        <button onclick="document.getElementById('modal').style.display='flex'"
            class="mb-4 bg-gray-600 text-white px-4 py-2 rounded flex items-center">
           <x-heroicon-o-folder class="w-6 h-6 text-blue-600" />
            {{ __('Create Unit') }}
        </button>
        
    </div>

     
    
    <div id="modal" class="fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50 hidden">
        <div class="bg-white p-6 rounded shadow-lg w-full max-w-md mx-auto flex flex-col">
            <h2
             class="text-lg font-bold mb-4 text-gray-600">{{ __('Crear nueva carpeta') }}
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
 
<div class="flex gap-4 p-4 bg-gray-100 rounded">
    <button class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">Botón 1</button>
    <button class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">Botón 2</button>
    <button class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">Botón 3</button>
</div>


    <hr>

    

    {{--Lista de carpetas (opcional, si tienes $carpetas) --}}


@if(isset($carpetas) && count($carpetas))
    <h2 class="font-bold mb-2 mt-2 p-4">CARPETAS:</h2>
    <hr>
     <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 mx-1 m-2">
        @foreach($carpetas as $carpeta)
            <div class="flex items-center p- border rounded bg-gray-50 hover:bg-gray-200 transition">
                {{-- Enlace a la carpeta --}}
                <a href="{{ route('mi_unidad.carpeta', $carpeta->id) }}" 
                    class="flex items-center text-gray-700 hover:text-blue- transition">
                  <x-heroicon-o-folder class="w-12 h-12 text-blue-600 hover:text-blue-200" />
                <span class="text-gray-900 m-2 hover:text-gray-400" >{{ $carpeta->nombre }}</span>
                </a>

                <a href="{{ route('mi_unidad.carpeta', $carpeta->id) }}"
                class="ml-auto flex items-center px-4 py-4 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 10.5v6m3-3H9m4.06-7.19-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z" />
                    </svg>
                </a>
            </div>
        @endforeach
    </div>
    </form>
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