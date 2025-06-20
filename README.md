# Gestión de Archivos en Laravel

Este proyecto implementa un sistema básico para la carga, almacenamiento y consulta de archivos usando Laravel.

## Estructura y Flujo

1. **Base de datos**
   - Tabla `archivos` con campos: `id`, `nombre`, `ruta`, `carpeta_id`, `user_id`, `mime_type`, `tamaño`, `created_at`, etc.
   - Guarda la ruta relativa del archivo para facilitar cambios futuros.

2. **Formulario de carga**
   - Usa `enctype="multipart/form-data"`.
   - Incluye campos para seleccionar archivo y asociarlo a una carpeta.
   - Muestra mensajes de error y éxito.

3. **Rutas y autenticación**
   - Rutas protegidas con middleware de autenticación.
   - Uso de rutas RESTful (`POST` para subir, `DELETE` para eliminar, etc.).

4. **Validación (Form Request)**
   - Valida tipo, tamaño y nombre del archivo.
   - Rechaza archivos peligrosos o demasiado grandes.

5. **Controlador**
   - Define la ruta de almacenamiento: `archivo/{carpeta_id}_{carpeta_nombre}/{nombre_archivo}`.
   - Usa `Storage::putFileAs()` para guardar el archivo en el disco `public`.
   - Guarda el registro en la base de datos con Eloquent.

6. **Enlace simbólico**
   - Ejecuta una vez: `php artisan storage:link` para acceder a los archivos desde `/storage`.

7. **Vista de consulta**
   - Usa `Storage::url()` para mostrar la URL pública del archivo.
   - Ejemplo:
     ```blade
     <a href="{{ Storage::url('archivo/' . $carpeta->id . '_' . Str::slug($carpeta->nombre) . '/' . $archivo->nombre) }}">
         {{$archivo->nombre}}
     </a>
     ```

## Buenas prácticas
- Usa nombres de archivos y carpetas limpios (sin espacios ni caracteres raros).
- Valida siempre los archivos antes de guardarlos.
- Si los archivos son sensibles, usa rutas protegidas y no el disco `public`.
- Documenta el flujo para tu equipo.

## Comandos útiles
```bash
php artisan storage:link   # Crear enlace simbólico
php artisan migrate        # Migrar base de datos
```

---

> _Este archivo README.md es una guía básica. Puedes ampliarlo con instrucciones de instalación, dependencias, ejemplos de uso, etc._
