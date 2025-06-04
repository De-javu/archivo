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
            if(msg) msg.style.display = 'none';
        }, 3000);
    @endif

        <div class="inline-block  ">
            
        <h1 class=" flex item-center text-2xl font-bold text-white-700 m-4 ">
        <x-heroicon-o-folder class="w-6 h-6 text-blue-600" />{{ $carpeta->nombre}}
        
        </h1>
        <a href="{{ route('mi_unidad.index') }}"
         class="text-blue-600 hover:underline   ">
            {{ __('Volver') }}
        </a>
    </div>

    <!-- Botón para abrir el modal -->

    <div class="flex justify-end mb-4">
        <button onclick="document.getElementById('modal').style.display='flex'"
            class="mb-4 bg-gray-600 text-white px-4 py-2 rounded flex items-center">
         <x-heroicon-o-folder class="w-6 h-6 text-blue-600" />
            {{ __('Sub Carpeta') }}
        </button>
        
    </div>

    <!-- Modal -->
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

    

    <!-- Lista de carpetas (opcional, si tienes $carpetas) -->


@if(isset($subcarpetas) && count($subcarpetas))
    <h2 class="font-bold mb-2 mt-6">CARPETAS:</h2>
    <hr>
     <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 mx-2 m-2">
        @foreach($subcarpetas as $subcarpeta)
            <div class="flex items-center p-4 border rounded bg-gray-50">
                <x-heroicon-o-folder class="w-6 h-6 text-blue-600" />
                <span class="text-gray-700">{{ $subcarpeta->nombre }}</span>
                <button                   
                    class="ml-auto flex items-center px-4 py-4 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                    <a href="{{ route('mi_unidad.carpeta', $carpeta->id) }}" class="flex items-center">
                   <x-heroicon-o-folder class="w-6 h-6 text-blue-600" />
                        <span class="hidden sm:inline">Abril</span>
                    </a>
                </button>
            </div>
        @endforeach
    </div>
    </form>
@endif



    <!-- Script para cerrar el modal al hacer click fuera del contenido -->
    <script>
        document.addEventListener('click', function (event) {
            var modal = document.getElementById('modal');
            if (modal && event.target === modal) {
                modal.style.display = 'none';
            }
        });
   </script>
   
</x-layouts.app>>