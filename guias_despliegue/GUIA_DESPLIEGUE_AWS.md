# 🚀 Guía Completa de Despliegue - Sistema de Archivos Laravel

## 📋 Índice
1. [Introducción y Objetivos](#introducción-y-objetivos)
2. [Arquitectura Propuesta](#arquitectura-propuesta)
3. [Prerrequisitos](#prerrequisitos)
4. [Fase 1: Preparación del Proyecto](#fase-1-preparación-del-proyecto)
5. [Fase 2: Configuración de Docker](#fase-2-configuración-de-docker)
6. [Fase 3: Configuración de AWS](#fase-3-configuración-de-aws)
7. [Fase 4: CI/CD con GitHub Actions](#fase-4-cicd-con-github-actions)
8. [Fase 5: Infraestructura como Código (Terraform)](#fase-5-infraestructura-como-código-terraform)
9. [Fase 6: Despliegue y Pruebas](#fase-6-despliegue-y-pruebas)
10. [Fase 7: Monitoreo y Mantenimiento](#fase-7-monitoreo-y-mantenimiento)

---

## 1. Introducción y Objetivos

### 🎯 Objetivo Principal
Migrar tu aplicación Laravel desde un entorno XAMPP local a una infraestructura moderna en AWS, implementando mejores prácticas de DevOps y CI/CD.

### 📊 Estado Actual vs Estado Deseado

**Estado Actual:**
- XAMPP local (Apache + MySQL + PHP)
- Sin versionado de infraestructura
- Despliegue manual
- Sin pruebas automatizadas

**Estado Deseado:**
- AWS Cloud (ECS Fargate + RDS + S3)
- Infraestructura como código (Terraform)
- CI/CD automatizado (GitHub Actions)
- Contenedorización (Docker)
- Escalabilidad automática
- Alta disponibilidad

### 💰 Estimación de Costos AWS (mensual)
- **Opción Mínima (Dev/Test):** ~$30-50/mes
  - ECS Fargate (1 tarea): ~$15
  - RDS db.t3.micro: ~$15
  - S3 + otros: ~$5-10
  
- **Opción Producción:** ~$100-200/mes
  - ECS Fargate (2+ tareas): ~$30-60
  - RDS db.t3.small: ~$30-40
  - ElastiCache: ~$15
  - S3 + CloudFront + otros: ~$20-30

---

## 2. Arquitectura Propuesta

### 🏗️ Diagrama de Arquitectura

```
┌─────────────────────────────────────────────────────────────────┐
│                         INTERNET                                │
└────────────────────────┬────────────────────────────────────────┘
                         │
                         ↓
              ┌──────────────────────┐
              │   Route 53 (DNS)     │  ← Opcional: Tu dominio
              └──────────┬───────────┘
                         │
                         ↓
              ┌──────────────────────┐
              │  CloudFront (CDN)    │  ← Opcional: Cache global
              └──────────┬───────────┘
                         │
                         ↓
┌────────────────────────────────────────────────────────────────┐
│                      AWS REGION (us-east-1)                    │
│                                                                │
│    ┌───────────────────────────────────────────────────┐      │
│    │              Application Load Balancer             │      │
│    │         (443/HTTPS → 80/HTTP redirect)            │      │
│    └─────────────────────┬─────────────────────────────┘      │
│                          │                                     │
│         ┌────────────────┼────────────────┐                   │
│         ↓                ↓                ↓                   │
│    ┌─────────┐     ┌─────────┐     ┌─────────┐              │
│    │ ECS Task│     │ ECS Task│     │ ECS Task│  ← Laravel   │
│    │ Fargate │     │ Fargate │     │ Fargate │    App       │
│    │ (PHP +  │     │ (PHP +  │     │ (PHP +  │              │
│    │ Nginx)  │     │ Nginx)  │     │ Nginx)  │              │
│    └────┬────┘     └────┬────┘     └────┬────┘              │
│         │               │               │                    │
│         └───────────────┼───────────────┘                    │
│                         │                                     │
│         ┌───────────────┼───────────────┐                    │
│         ↓               ↓               ↓                    │
│    ┌────────────┐  ┌──────────┐  ┌──────────┐              │
│    │   RDS      │  │ ElastiC  │  │   S3     │              │
│    │  MySQL 8   │  │  Redis   │  │ (Archivos│              │
│    │ (Base de   │  │ (Cache/  │  │ subidos) │              │
│    │  Datos)    │  │ Sesiones)│  │          │              │
│    └────────────┘  └──────────┘  └──────────┘              │
│                                                              │
│    ┌─────────────────────────────────────────────┐          │
│    │          Secrets Manager                     │          │
│    │    (APP_KEY, DB_PASSWORD, etc.)             │          │
│    └─────────────────────────────────────────────┘          │
│                                                              │
│    ┌─────────────────────────────────────────────┐          │
│    │         CloudWatch Logs & Metrics            │          │
│    │         (Monitoreo y Alertas)                │          │
│    └─────────────────────────────────────────────┘          │
└────────────────────────────────────────────────────────────────┘

┌────────────────────────────────────────────────────────────────┐
│                    CI/CD PIPELINE                              │
│                                                                │
│  GitHub → GitHub Actions → ECR → ECS Deploy                   │
│  (Código)  (Testing/Build)  (Docker) (Producción)            │
└────────────────────────────────────────────────────────────────┘
```

### 🔑 Componentes Clave

#### 1. **Application Load Balancer (ALB)**
- **¿Qué es?** Distribuidor de tráfico HTTP/HTTPS
- **¿Por qué?** 
  - Distribuye carga entre múltiples instancias
  - Termina SSL/TLS (HTTPS)
  - Health checks automáticos
  - Desacoplamiento del frontend

#### 2. **ECS Fargate**
- **¿Qué es?** Servicio de contenedores serverless
- **¿Por qué?** 
  - No gestionas servidores (AWS lo hace)
  - Escalado automático
  - Pago por uso (CPU/RAM)
  - Más barato que EC2 para cargas variables
  
**Alternativa: EC2 + Docker Compose**
- Más control, pero más gestión
- Mejor para empezar si quieres simplicidad
- Costo fijo mensual

#### 3. **RDS MySQL**
- **¿Qué es?** Base de datos administrada
- **¿Por qué?**
  - Backups automáticos
  - Alta disponibilidad (Multi-AZ)
  - Escalado vertical fácil
  - Parches automáticos
  - No gestionas MySQL manualmente

#### 4. **S3**
- **¿Qué es?** Almacenamiento de objetos
- **¿Por qué?**
  - Almacenamiento ilimitado
  - Muy económico
  - Alta durabilidad (99.999999999%)
  - Mejor que guardar archivos en disco local
  - Los archivos persisten aunque se reinicie el contenedor

#### 5. **ElastiCache Redis**
- **¿Qué es?** Cache en memoria administrado
- **¿Por qué?**
  - Sesiones rápidas
  - Cache de consultas
  - Colas de trabajo (opcional)
  - Mejora rendimiento 10x-100x

#### 6. **ECR (Elastic Container Registry)**
- **¿Qué es?** Registro privado de imágenes Docker
- **¿Por qué?**
  - Guarda tus imágenes Docker
  - Integración nativa con ECS
  - Escaneo de seguridad automático

---

## 3. Prerrequisitos

### ✅ Checklist Antes de Empezar

#### A. Cuenta AWS
- [ ] Cuenta de AWS creada
- [ ] Método de pago configurado
- [ ] IAM User con permisos de administrador
- [ ] AWS CLI instalado localmente
- [ ] Credenciales configuradas (`aws configure`)

#### B. Herramientas Locales
- [ ] Git instalado
- [ ] Docker Desktop instalado
- [ ] Node.js 20+ instalado
- [ ] PHP 8.2+ instalado
- [ ] Composer instalado
- [ ] Terraform instalado (opcional)
- [ ] Editor de código (VS Code recomendado)

#### C. Repositorio GitHub
- [ ] Repositorio creado en GitHub
- [ ] Código subido al repositorio
- [ ] Archivo `.gitignore` configurado
- [ ] Branch `main` o `master` como principal

#### D. Conocimientos Mínimos
- [ ] Git básico (commit, push, pull)
- [ ] Línea de comandos básica
- [ ] Conceptos de Docker (imagen, contenedor)
- [ ] Laravel básico (Artisan commands)

---

## 4. Fase 1: Preparación del Proyecto

### 📝 Objetivo
Preparar el proyecto Laravel para funcionar en un entorno en la nube.

### 4.1 Configurar `.env` para Múltiples Entornos

**¿Por qué?**
Necesitas diferentes configuraciones para local, desarrollo y producción.

**Pasos:**
1. Crear `.env.example` actualizado
2. Configurar variables para S3
3. Configurar variables para Redis
4. Preparar para 12-factor app

**Archivos a modificar:**
- `.env.example`
- `config/filesystems.php`
- `config/cache.php`
- `config/session.php`

### 4.2 Migrar Almacenamiento a S3

**¿Por qué?**
Los contenedores son efímeros (se pueden destruir). Los archivos deben estar en S3.

**Tareas:**
1. Instalar `league/flysystem-aws-s3-v3`
2. Configurar disk S3 en `filesystems.php`
3. Actualizar código que use `Storage::disk('local')` a `Storage::disk('s3')`
4. Probar subida/descarga localmente

### 4.3 Optimizar para Producción

**Tareas:**
1. Agregar health check endpoint (`/health`)
2. Configurar logging a stdout/stderr
3. Optimizar autoload de Composer
4. Configurar OPcache
5. Separar assets estáticos

### 4.4 Testing

**¿Por qué?**
GitHub Actions ejecutará tests antes de desplegar.

**Tareas:**
1. Configurar PHPUnit
2. Crear tests básicos
3. Verificar que pasen localmente

---

## 5. Fase 2: Configuración de Docker

### 🐳 Objetivo
Contenedorizar la aplicación Laravel para que funcione igual en cualquier entorno.

### 5.1 Entender la Estrategia Docker

**¿Qué es un Dockerfile?**
- Receta para construir una imagen Docker
- Lista de instrucciones paso a paso
- Resultado: Imagen inmutable

**¿Qué es Docker Compose?**
- Orquestador de múltiples contenedores
- Define servicios, redes, volúmenes
- Útil para desarrollo local

**Nuestra Estrategia:**
```
Multi-stage build:
1. Stage "composer": Instala dependencias PHP
2. Stage "node": Compila assets (npm run build)
3. Stage "final": Copia todo y configura Nginx + PHP-FPM
```

### 5.2 Crear Dockerfile

**Componentes:**
1. **Base Image**: `php:8.2-fpm-alpine`
   - ¿Por qué Alpine? Más ligero (5MB vs 200MB)
   
2. **Extensiones PHP necesarias:**
   - pdo_mysql (base de datos)
   - gd (imágenes)
   - intl (internacionalización)
   - opcache (performance)
   - bcmath (cálculos precisos)

3. **Nginx**: Servidor web
   - ¿Por qué no Apache? Más ligero y rápido
   
4. **Supervisor**: Gestor de procesos
   - Ejecuta Nginx + PHP-FPM simultáneamente

**Archivos a crear:**
- `Dockerfile`
- `docker/nginx/nginx.conf`
- `docker/nginx/default.conf`
- `docker/php/php.ini`
- `docker/supervisor/supervisord.conf`

### 5.3 Crear docker-compose.yml

**Servicios a definir:**

1. **app** (Laravel)
2. **db** (MySQL 8) - solo para desarrollo local
3. **redis** (Cache/Sesiones)
4. **queue** (Worker de colas - opcional)
5. **scheduler** (Cron de Laravel - opcional)

### 5.4 Pruebas Locales

**Comandos:**
```bash
# Construir imagen
docker-compose build

# Levantar servicios
docker-compose up -d

# Ver logs
docker-compose logs -f app

# Ejecutar migraciones
docker-compose exec app php artisan migrate

# Probar aplicación
http://localhost:8080
```

---

## 6. Fase 3: Configuración de AWS

### ☁️ Objetivo
Crear manualmente los recursos básicos de AWS para entender cómo funciona.

### 6.1 Decisión Importante: ECS Fargate vs EC2

#### Opción A: ECS Fargate (Recomendado)
**Pros:**
- Sin gestión de servidores
- Escalado automático
- Pago por uso (más barato si no tienes tráfico constante)
- Más "moderno"

**Contras:**
- Curva de aprendizaje más alta
- Menos control directo

#### Opción B: EC2 + Docker Compose (Más Simple)
**Pros:**
- Similar a DigitalOcean (tu experiencia previa)
- Control total del servidor
- Más fácil debuggear
- Docker Compose conocido

**Contras:**
- Gestionas el servidor (actualizaciones, seguridad)
- Escalado manual
- Costo fijo (aunque esté sin usar)

**¿Cuál elegir?**
- **Para aprender:** EC2 + Docker Compose
- **Para producción profesional:** ECS Fargate

### 6.2 Configuración Inicial AWS (Consola Web)

#### Paso 1: VPC y Redes
**¿Qué es VPC?** Red privada virtual en AWS

**Crear:**
1. VPC con CIDR `10.0.0.0/16`
2. 2 Subnets públicas (para ALB)
3. 2 Subnets privadas (para ECS/EC2)
4. Internet Gateway
5. NAT Gateway (para que las instancias privadas accedan a internet)
6. Route Tables

**¿Por qué 2 subnets?** Alta disponibilidad (diferentes zonas)

#### Paso 2: RDS MySQL
1. Ir a RDS Console
2. Crear DB Subnet Group (con subnets privadas)
3. Crear Security Group (permitir puerto 3306 desde ECS)
4. Crear RDS Instance:
   - Engine: MySQL 8.0
   - Template: Free Tier o Dev/Test
   - Instance: db.t3.micro
   - Storage: 20GB (con autoscaling)
   - Multi-AZ: No (para dev), Yes (para prod)
   - Backups: 7 días

#### Paso 3: S3 Bucket
1. Crear bucket: `laravel-app-files-production`
2. Región: Misma que RDS
3. Bloquear acceso público (Laravel accederá con credenciales)
4. Versionado: Habilitado
5. Encriptación: AES-256

#### Paso 4: ElastiCache Redis (Opcional)
1. Crear Subnet Group
2. Crear Security Group (puerto 6379 desde ECS)
3. Crear cluster Redis:
   - Engine: Redis 7.0
   - Node type: cache.t3.micro
   - Number of nodes: 1

#### Paso 5: Secrets Manager
**¿Por qué?** No poner contraseñas en código

Crear secretos:
1. `laravel/APP_KEY`
2. `laravel/DB_PASSWORD`
3. `laravel/AWS_ACCESS_KEY_ID`
4. `laravel/AWS_SECRET_ACCESS_KEY`

#### Paso 6: IAM Roles
**Roles necesarios:**

1. **ecsTaskExecutionRole**
   - Permisos: Leer ECR, escribir logs, leer secretos
   
2. **ecsTaskRole**
   - Permisos: Acceder a S3, RDS, Redis

### 6.3 Configuración ECR (Docker Registry)

```bash
# Crear repositorio
aws ecr create-repository --repository-name laravel-app

# Login a ECR
aws ecr get-login-password --region us-east-1 | \
  docker login --username AWS --password-stdin \
  YOUR_ACCOUNT_ID.dkr.ecr.us-east-1.amazonaws.com

# Tag y push de imagen
docker tag laravel-app:latest \
  YOUR_ACCOUNT_ID.dkr.ecr.us-east-1.amazonaws.com/laravel-app:latest

docker push YOUR_ACCOUNT_ID.dkr.ecr.us-east-1.amazonaws.com/laravel-app:latest
```

### 6.4 Configuración ECS

#### Crear Cluster
1. Nombre: `laravel-cluster`
2. Tipo: Fargate
3. Habilitar Container Insights

#### Crear Task Definition
**Especificaciones:**
- CPU: 512 (.5 vCPU)
- Memory: 1024 MB
- Container:
  - Nombre: laravel-app
  - Imagen: URI de ECR
  - Puerto: 8080
  - Environment variables
  - Secrets (desde Secrets Manager)
  - Log configuration (CloudWatch)

#### Crear Service
1. Launch type: Fargate
2. Task definition: la que creaste
3. Desired count: 2 (para alta disponibilidad)
4. VPC y subnets privadas
5. Security group (puerto 8080 desde ALB)
6. Load balancer: Conectar con ALB

### 6.5 Configuración Application Load Balancer

1. **Crear ALB:**
   - Scheme: Internet-facing
   - IP type: IPv4
   - Subnets: Públicas (ambas zonas)
   - Security group: Puertos 80 y 443

2. **Target Group:**
   - Type: IP
   - Protocol: HTTP
   - Port: 8080
   - Health check: `/health`

3. **Listeners:**
   - HTTP:80 → Redirect a HTTPS:443
   - HTTPS:443 → Forward a target group

---

## 7. Fase 4: CI/CD con GitHub Actions

### 🔄 Objetivo
Automatizar el proceso: push código → test → build → deploy

### 7.1 Entender el Pipeline

```
┌─────────┐     ┌──────────┐     ┌─────────┐     ┌──────────┐
│  Push   │ →   │  Tests   │ →   │  Build  │ →   │  Deploy  │
│ GitHub  │     │ (PHPUnit)│     │ (Docker)│     │  (ECS)   │
└─────────┘     └──────────┘     └─────────┘     └──────────┘
    ↓                ↓                 ↓               ↓
 Trigger        ✅ Pass?          Push ECR      Update Service
                ❌ Fail → STOP
```

### 7.2 Configurar GitHub Secrets

**En tu repositorio GitHub:**
Settings → Secrets → Actions → New repository secret

**Secrets necesarios:**
- `AWS_ACCESS_KEY_ID`
- `AWS_SECRET_ACCESS_KEY`
- `AWS_REGION` (ej: us-east-1)
- `ECR_REPOSITORY` (nombre del repo ECR)
- `ECS_CLUSTER` (nombre del cluster)
- `ECS_SERVICE` (nombre del servicio)

### 7.3 Crear Workflow de GitHub Actions

**Archivo:** `.github/workflows/deploy.yml`

**Jobs:**

1. **test** (Siempre se ejecuta)
   - Setup PHP
   - Instalar dependencias
   - Ejecutar tests
   - Si falla → detener pipeline

2. **build-and-push** (Solo en main/master)
   - Construir imagen Docker
   - Push a ECR
   - Tag con SHA del commit

3. **deploy** (Solo si build OK)
   - Actualizar task definition
   - Desplegar a ECS
   - Esperar estabilidad

### 7.4 Estrategias de Deployment

#### Blue-Green Deployment
- Despliega nueva versión junto a la vieja
- Cambia tráfico gradualmente
- Rollback instantáneo

#### Rolling Update
- Reemplaza tareas una por una
- Sin downtime
- Más lento

**Recomendación:** Rolling update para empezar

---

## 8. Fase 5: Infraestructura como Código (Terraform)

### 🏗️ Objetivo
Definir toda la infraestructura en código (reproducible, versionable).

### 8.1 ¿Por qué Terraform?

**Sin Terraform:**
- Clicks manuales en consola AWS
- No reproducible
- Difícil documentar cambios
- Propenso a errores

**Con Terraform:**
- Código versionado en Git
- Reproducible en cualquier cuenta AWS
- Documentación automática
- Preview de cambios (terraform plan)
- Destroy todo fácilmente

### 8.2 Estructura de Archivos

```
terraform/
├── main.tf           # Provider AWS
├── variables.tf      # Variables configurables
├── outputs.tf        # Valores de salida
├── vpc.tf           # Red VPC
├── rds.tf           # Base de datos
├── s3.tf            # Almacenamiento
├── elasticache.tf   # Redis
├── ecs.tf           # Contenedores
├── alb.tf           # Load balancer
├── secrets.tf       # Secrets Manager
└── terraform.tfvars  # Valores específicos (NO subir a Git)
```

### 8.3 Comandos Terraform Básicos

```bash
# Inicializar (primera vez)
terraform init

# Ver qué va a crear
terraform plan

# Crear infraestructura
terraform apply

# Destruir todo
terraform destroy

# Ver estado actual
terraform show
```

### 8.4 Ventajas para Tu Proyecto

1. **Desarrollo:**
   - Crear entorno de test completo
   - Destruir al terminar
   - Ahorrar costos

2. **Producción:**
   - Infraestructura documentada
   - Disaster recovery rápido
   - Migración a otra región fácil

---

## 9. Fase 6: Despliegue y Pruebas

### ✅ Checklist de Despliegue

#### Pre-Despliegue
- [ ] Tests pasando localmente
- [ ] Docker build exitoso localmente
- [ ] Variables de entorno configuradas
- [ ] Secrets en AWS Secrets Manager
- [ ] Base de datos RDS accesible
- [ ] S3 bucket creado

#### Primer Despliegue
1. [ ] Push imagen a ECR manualmente
2. [ ] Crear task definition
3. [ ] Desplegar 1 tarea (desired count: 1)
4. [ ] Verificar logs en CloudWatch
5. [ ] Ejecutar migraciones
6. [ ] Probar endpoint health check
7. [ ] Probar aplicación a través de ALB

#### Post-Despliegue
- [ ] Configurar CloudWatch Alarms
- [ ] Configurar backup de RDS
- [ ] Documentar URLs importantes
- [ ] Configurar dominio (Route 53)
- [ ] Configurar SSL (ACM)

### 9.1 Ejecutar Migraciones en Producción

**Opción 1: AWS ECS Exec**
```bash
aws ecs execute-command \
  --cluster laravel-cluster \
  --task TASK_ID \
  --container laravel-app \
  --command "php artisan migrate --force" \
  --interactive
```

**Opción 2: Task dedicado**
```bash
# Crear task definition para migraciones
# Ejecutar como one-off task
```

### 9.2 Troubleshooting Común

#### Problema: Task no inicia
**Soluciones:**
- Revisar logs en CloudWatch
- Verificar Security Groups
- Verificar que la imagen existe en ECR
- Verificar CPU/Memory suficiente

#### Problema: No conecta a RDS
**Soluciones:**
- Verificar Security Group de RDS permite 3306 desde ECS
- Verificar subnet privada con NAT Gateway
- Verificar credenciales en Secrets Manager

#### Problema: No sube archivos a S3
**Soluciones:**
- Verificar IAM role del task tiene permisos S3
- Verificar nombre del bucket correcto
- Verificar región correcta

---

## 10. Fase 7: Monitoreo y Mantenimiento

### 📊 Métricas Importantes

#### CloudWatch Metrics
1. **ECS:**
   - CPUUtilization
   - MemoryUtilization
   - Running task count

2. **RDS:**
   - DatabaseConnections
   - FreeableMemory
   - ReadLatency / WriteLatency

3. **ALB:**
   - TargetResponseTime
   - HealthyHostCount
   - RequestCount

### 7.1 Configurar Alarmas

**Alarmas Críticas:**
1. ECS tasks < 1 (servicio caído)
2. RDS CPU > 80%
3. RDS FreeableMemory < 500MB
4. ALB HealthyHosts < 1

### 7.2 Logs

**CloudWatch Log Groups:**
- `/ecs/laravel-app` - Logs de aplicación
- `/aws/rds/instance/laravel-db/error` - Errores MySQL

**Queries útiles:**
```
# Errores de Laravel
fields @timestamp, @message
| filter @message like /ERROR/
| sort @timestamp desc

# Requests lentos
fields @timestamp, @message
| filter @message like /php artisan/
| sort @timestamp desc
```

### 7.3 Backups

#### RDS
- Backups automáticos: 7 días
- Snapshots manuales: Antes de cambios mayores

#### S3
- Versionado habilitado
- Lifecycle policy: Mover a Glacier después de 90 días

---

## 📚 Recursos Adicionales

### Documentación Oficial
- [AWS ECS](https://docs.aws.amazon.com/ecs/)
- [Laravel Deployment](https://laravel.com/docs/deployment)
- [Terraform AWS Provider](https://registry.terraform.io/providers/hashicorp/aws/latest/docs)
- [GitHub Actions](https://docs.github.com/en/actions)

### Tutoriales Recomendados
- AWS ECS Fargate Tutorial
- Laravel in Docker
- Terraform AWS Infrastructure

---

## 🎯 Plan de Trabajo Sugerido

### Semana 1: Preparación
- [ ] Día 1-2: Configurar Docker localmente
- [ ] Día 3-4: Probar Docker Compose completo
- [ ] Día 5: Migrar almacenamiento a S3 (probar localmente)

### Semana 2: AWS Básico
- [ ] Día 1: Crear cuenta AWS y configurar IAM
- [ ] Día 2-3: Crear VPC, subnets, RDS manualmente
- [ ] Día 4: Crear S3 y probar desde local
- [ ] Día 5: Subir primera imagen a ECR

### Semana 3: Despliegue
- [ ] Día 1-2: Configurar ECS Cluster y Task Definition
- [ ] Día 3: Configurar ALB
- [ ] Día 4: Primer despliegue manual
- [ ] Día 5: Troubleshooting y ajustes

### Semana 4: Automatización
- [ ] Día 1-2: Configurar GitHub Actions
- [ ] Día 3: Primer deploy automático
- [ ] Día 4-5: Terraform (opcional)

---

## ❓ Preguntas Frecuentes

### P: ¿Puedo usar EC2 en lugar de ECS?
**R:** Sí, es más simple para empezar. Usa Docker Compose en EC2.

### P: ¿Necesito Redis obligatoriamente?
**R:** No, puedes usar sesiones en base de datos. Redis mejora performance.

### P: ¿Cuánto cuesta AWS?
**R:** ~$30-50/mes mínimo. Usa AWS Calculator para estimar.

### P: ¿Puedo usar MySQL en contenedor en lugar de RDS?
**R:** Para desarrollo sí, para producción NO (riesgo de pérdida de datos).

### P: ¿GitHub Actions es gratis?
**R:** 2000 minutos/mes gratis para repositorios públicos, 3000 para privados.

---

## 📞 Próximos Pasos

**¿Por dónde empezamos?**

1. **Si quieres empezar simple:** Fase 2 (Docker local)
2. **Si quieres ir directo a AWS:** Fase 3.1 (Decidir ECS vs EC2)
3. **Si quieres automatización:** Fase 4 (GitHub Actions)

**Dime cuál fase te interesa y profundizamos juntos. ¿Qué prefieres abordar primero?**
