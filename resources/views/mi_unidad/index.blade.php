<x-layouts.app>
    <x-slot name="title">
        {{ __('My Unit') }}
    </x-slot>

    <!-- Botón para abrir el modal -->
    <div class="flex justify-end mb-4">
        <button onclick="document.getElementById('modal').style.display='flex'" class="mb-4 bg-blue-600 text-white px-4 py-2 rounded flex items-center">
            <x-icon name="folder" class="mr-2" />
            {{ __('Create Unit') }}
        </button>
    </div>

    <!-- Modal -->
    <div id="modal" class="fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50 hidden">
        <div class="bg-white p-6 rounded shadow-lg w-full max-w-md mx-auto flex flex-col">
            <h2 class="text-lg font-bold mb-4 text-gray-600">{{ __('Crear nueva carpeta') }}</h2>
            <form action="{{ route('mi_unidad.store') }}" method="POST">
                @csrf
                
                <input type="text" name="nombre" placeholder="Nombre de la carpeta" class="border rounded px-3 py-2 w-full mb-4  text-gray-600" required>
                @error('nombre')
                    <div class="text-red-500 text-xs mb-2">{{ $message }}</div>
                @enderror
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('modal').style.display='none'" class="px-4 py-2 bg-gray-600 text-white rounded">
                        {{ __('Cerrar') }}
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">
                        {{ __('Crear') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de carpetas (opcional, si tienes $carpetas) -->
    @if(isset($carpetas) && count($carpetas))
        <h2 class="font-bold mb-2 mt-6">Carpetas:</h2>
        <ul>
            @foreach($carpetas as $carpeta)
                <li>{{ $carpeta->nombre }}</li>
            @endforeach
        </ul>
    @endif

    <!-- Script para cerrar el modal al hacer click fuera del contenido -->
    <script>
        document.addEventListener('click', function(event) {
            var modal = document.getElementById('modal');
            if (modal && event.target === modal) {
                modal.style.display = 'none';
            }
        });
    </script>
</x-layouts.app>>

