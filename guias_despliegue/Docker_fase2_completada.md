# 🐳 Fase 2: Configuración de Docker - COMPLETADA ✅

## 📋 Índice
1. [Objetivo de la Fase](#objetivo-de-la-fase)
2. [Archivos de Configuración](#archivos-de-configuración)
3. [Proceso de Implementación](#proceso-de-implementación)
4. [Problemas Encontrados y Soluciones](#problemas-encontrados-y-soluciones)
5. [Comandos Útiles](#comandos-útiles)
6. [Verificación Final](#verificación-final)

---

## 1. Objetivo de la Fase

Contenedorizar la aplicación Laravel para que funcione de manera consistente en cualquier entorno (local, desarrollo, producción).

### ✅ Resultados Alcanzados:
- Aplicación Laravel corriendo en contenedor Docker
- Nginx configurado como servidor web
- MySQL 8.0 en contenedor separado
- Conexión exitosa entre Laravel y MySQL
- Base de datos con migraciones ejecutadas
- Assets (CSS/JS) compilados y funcionando
- Aplicación accesible en http://localhost:8080

---

## 2. Archivos de Configuración

### 2.1 Estructura de Archivos Docker

```
proyecto/
├── Dockerfile                          # Imagen de PHP-FPM con Laravel
├── docker-compose.yml                  # Orquestación de servicios
├── .env                               # Variables de entorno de Laravel
└── docker/
    └── nginx/
        └── default.conf               # Configuración de Nginx
```

### 2.2 Dockerfile

**Ubicación:** `./Dockerfile`

```dockerfile
FROM php:8.2-fpm

# Instala dependencias del sistema
RUN apt-get update \
    && apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev zip git unzip curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql

# Instala Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Establece el directorio de trabajo
WORKDIR /var/www

# Copia los archivos de la aplicación
COPY . .

# Instala dependencias de PHP
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Da permisos a la carpeta de almacenamiento
RUN chown -R www-data:www-data storage bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
```

### 2.3 docker-compose.yml

```yaml
version: '3.8'
services:
  app:
    build: .
    image: archivos_app:dev
    container_name: archivos_app
    volumes:
      - .:/var/www
    environment:
      - APP_ENV=local
      - APP_DEBUG=true
      - DB_HOST=mysql
      - DB_DATABASE=sistemadearchivo
      - DB_USERNAME=root
      - DB_PASSWORD=root
    depends_on:
      - mysql

  nginx:
    image: nginx:alpine
    container_name: nginx_app
    ports:
      - "8080:80"
    volumes:
      - .:/var/www
      - ./docker/nginx/default.conf:/etc/nginx/conf.d/default.conf
    depends_on:
      - app

  mysql:
    image: mysql:8.0
    container_name: mysql_db
    restart: always
    ports:
      - "3308:3306"
    environment:
      MYSQL_DATABASE: sistemadearchivo
      MYSQL_ROOT_PASSWORD: root
    volumes:
      - dbdata:/var/lib/mysql

volumes:
  dbdata:
```

**⚠️ Nota Importante:** NO definir `APP_KEY` en las variables de entorno de `docker-compose.yml`, ya que sobrescribiría el valor del `.env` local.

### 2.4 docker/nginx/default.conf

```nginx
server {
    listen 80;
    server_name localhost;
    root /var/www/public;

    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass app:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_read_timeout 300;
        fastcgi_connect_timeout 300;
        fastcgi_send_timeout 300;
    }
}
```

### 2.5 .env (Laravel)

**Configuraciones importantes para Docker:**
```env
APP_KEY=base64:ZxqHLIdFK9XXdlhPzLEsp+wGYCtrdX49wH5urTIkK1c=

DB_CONNECTION=mysql
DB_HOST=mysql          # ⚠️ IMPORTANTE: nombre del servicio en docker-compose
DB_PORT=3306
DB_DATABASE=sistemadearchivo
DB_USERNAME=root
DB_PASSWORD=root
```

---

## 3. Proceso de Implementación

### Comandos Ejecutados (En Orden)

```bash
# 1. Construir imagen
docker-compose build --no-cache

# 2. Levantar servicios
docker-compose up -d

# 3. Esperar inicialización de MySQL (20-30 segundos)
docker-compose logs mysql | grep "ready for connections"

# 4. Ejecutar migraciones
docker-compose exec app php artisan migrate

# 5. Compilar assets
npm install
npm run dev

# 6. Limpiar caché
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear

# 7. Verificar
docker-compose ps
```

---

## 4. Problemas Encontrados y Soluciones

### 🔴 Problema 1: "Unsupported cipher or incorrect key length"

**Causa:** Variable `APP_KEY` en `docker-compose.yml` sobrescribiendo el `.env`

**Solución:**
1. Eliminar línea `APP_KEY=...` del `docker-compose.yml`
2. Generar nueva clave:
   ```bash
   docker-compose exec app php -r "echo 'base64:' . base64_encode(random_bytes(32)) . PHP_EOL;"
   ```
3. Actualizar `.env` local
4. Reiniciar: `docker-compose restart app`

---

### 🔴 Problema 2: MySQL en estado "Restarting"

**Causa:** Falta `MYSQL_ROOT_PASSWORD`

**Solución:**
```yaml
environment:
  MYSQL_ROOT_PASSWORD: root  # ⚠️ Debe tener valor
```

Reconstruir:
```bash
docker-compose down -v  # Eliminar volúmenes
docker-compose up -d    # Levantar servicios
```

---

### 🔴 Problema 3: Error 504 Gateway Timeout

**Causa:** Timeouts muy cortos

**Solución:** Agregar en `default.conf`:
```nginx
fastcgi_read_timeout 300;
fastcgi_connect_timeout 300;
fastcgi_send_timeout 300;
```

---

### 🔴 Problema 4: Estilos CSS/JS no cargan

**Solución:**
```bash
npm run dev  # Dejar corriendo
```

---

## 5. Comandos Útiles

### Gestión de Contenedores

```bash
# Ver estado
docker-compose ps

# Logs
docker-compose logs -f app
docker-compose logs -f nginx
docker-compose logs -f mysql

# Reiniciar
docker-compose restart app

# Detener
docker-compose down

# Detener + eliminar volúmenes
docker-compose down -v
```

### Artisan en Contenedor

```bash
docker-compose exec app php artisan migrate
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:list
```

### Limpieza

```bash
# Limpiar todo Docker
docker system prune -a

# Limpiar volúmenes
docker volume prune

# Ver uso de disco
docker system df
```

---

## 6. Verificación Final

### Checklist ✅

- [x] `docker-compose ps` muestra todos "Up"
- [x] MySQL muestra "ready for connections"
- [x] Migraciones ejecutadas correctamente
- [x] http://localhost:8080 carga la app
- [x] Estilos CSS funcionan
- [x] No hay errores en consola

---

## 7. Conceptos Aprendidos

### Volúmenes vs COPY
- **COPY:** Copia al construir imagen (producción)
- **Volumes:** Monta en tiempo real (desarrollo)

### Networking
Los servicios se comunican por nombre:
- `app` → PHP-FPM
- `nginx` → Servidor web
- `mysql` → Base de datos

Por eso `DB_HOST=mysql` en `.env`

### Variables de Entorno - Prioridad
1. docker-compose.yml (environment)
2. .env montado
3. Dockerfile (ENV)

---

## 8. Próximos Pasos

1. ✅ **Fase 2 COMPLETADA**
2. ⏭️ **Fase 1:** Optimizar para producción
3. ⏭️ **Fase 3:** AWS (RDS, S3, ECS)
4. ⏭️ **Fase 4:** GitHub Actions
5. ⏭️ **Fase 5:** Terraform

---

Nota Final:
**Autor:** Para arrancar el proyecto debes navegar a la carpeta raíz ejemplo:
- cd /mnt/d/xampp/htdocs/Laravelpracticas/archivosdel  , rutar
- `docker-compose up -d` : Comado para levantar los contenedores en segundo plano.
- http://localhost:8080 : para ver la aplicación en el navegador.
- docker-compose logs -f :  si se necesitan ver nginx para ver logs de Nginx. 

**Documentado:** 13 de noviembre de 2025  
**Estado:** ✅ COMPLETADO Y FUNCIONANDO  
**URL:** http://localhost:8080
