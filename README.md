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


# Laravel Archivos - Sistema de Gestión de Carpetas con Roles y Permisos

## Descripción

Aplicación web desarrollada en Laravel para la gestión de carpetas y archivos, con control de acceso basado en roles y permisos usando el paquete [spatie/laravel-permission](https://spatie.be/docs/laravel-permission/v5/introduction).

## Características

- Gestión de carpetas y subcarpetas con límite de profundidad.
- Gestión de archivos dentro de carpetas.
- Sistema de roles: `admin` y `user`.
- Sistema de permisos: crear y eliminar carpetas.
- Solo el usuario con rol `admin` puede eliminar cualquier carpeta; los usuarios solo pueden eliminar sus propias carpetas.
- Interfaz protegida por autenticación.
- Mensajes de éxito y error en las operaciones.

## Instalación

1. **Clona el repositorio:**
   ```sh
   git clone <url-del-repositorio>
   cd <nombre-del-proyecto>
   ```

2. **Instala las dependencias:**
   ```sh
   composer install
   npm install && npm run dev
   ```

3. **Configura el archivo `.env`:**
   - Copia `.env.example` a `.env` y configura tu base de datos y otras variables.

4. **Genera la clave de la aplicación:**
   ```sh
   php artisan key:generate
   ```

5. **Ejecuta las migraciones y seeders:**
   ```sh
   php artisan migrate
   php artisan db:seed --class=RolesPermisosSeeder
   ```

6. **Configura el almacenamiento público (opcional):**
   ```sh
   php artisan storage:link
   ```

## Uso de Roles y Permisos

- El usuario con email `andrespardo5151@gmail.com` es asignado automáticamente como `admin` en el seeder.
- Los usuarios con rol `admin` pueden crear y eliminar cualquier carpeta.
- Los usuarios con rol `user` solo pueden crear carpetas y eliminar las que les pertenecen.

## Estructura de Carpetas

- Las carpetas pueden tener subcarpetas hasta un máximo de 4 niveles de profundidad.
- Los archivos se almacenan en `storage/app/public/archivo/{id}_{nombre}`.

## Ejemplo de Control de Permisos en Controladores

```php
if ($carpeta->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
    abort(403, 'No tienes permiso para eliminar esta carpeta.');
}
```

## Ejemplo de Uso en Vistas Blade

```blade
@role('admin')
    <button>Eliminar cualquier carpeta</button>
@endrole

@can('crear carpetas')
    <button>Crear carpeta</button>
@endcan
```

## Testing

- Puedes crear tests con PHPUnit para verificar la lógica de roles, permisos y operaciones sobre carpetas y archivos.
- Ejecuta los tests con:
  ```sh
  php artisan test
  ```

## Créditos

- [Laravel](https://laravel.com/)
- [spatie/laravel-permission](https://github.com/spatie/laravel-permission)

## Licencia

Este proyecto está bajo la licencia MIT.