# Arquitectura del Sistema - Backend Laravel

## 📋 Tabla de Contenidos

1. [Descripción General](#descripción-general)
2. [Estructura de Base de Datos](#estructura-de-base-de-datos)
3. [Models (Modelos)](#models-modelos)
4. [Controllers (Controladores)](#controllers-controladores)
5. [Middleware (Intermediarios)](#middleware-intermediarios)
6. [Routes/API (Rutas)](#routesapi-rutas)
7. [Flujo del Sistema](#flujo-del-sistema)
8. [Instructivo de Instalación y Uso](#instructivo-de-instalación-y-uso)
9. [Endpoints Disponibles](#endpoints-disponibles)

---

## Descripción General

Este es un sistema **Laravel** de gestión de usuarios, roles y permisos basado en módulos. La arquitectura sigue el patrón **RBAC (Role-Based Access Control)** donde:

- **Usuarios** tienen asignados **Roles**
- **Roles** tienen asignados **Permisos** para acceder a **Módulos**
- **Módulos** agrupan funcionalidades del sistema
- El acceso se controla mediante **Middleware** que valida permisos

---

## Estructura de Base de Datos

### 📊 Diagrama de Relaciones

```
┌─────────────┐
│   USERS     │
│ ┌─────────┐ │
│ │ id      │ │◄──── foreignKey
│ │ email   │ │      role_id
│ │ role_id │ │
│ └─────────┘ │
└──────┬──────┘
       │
       ▼
┌─────────────┐       ┌──────────────┐       ┌──────────────┐
│   ROLES     │───────│ ROLE_PERMS   │───────│  PERMISSIONS │
│ ┌─────────┐ │       │ ┌──────────┐ │       │ ┌──────────┐ │
│ │ id      │ │       │ │ role_id  │ │       │ │ id       │ │
│ │ code    │ │       │ │ module_id│ │       │ │ code     │ │
│ │ name    │ │       │ │ perm_id  │ │       │ │ name     │ │
│ │ status  │ │       │ │ allowed  │ │       │ │ status   │ │
│ └─────────┘ │       │ └──────────┘ │       │ └──────────┘ │
└─────────────┘       └──────┬───────┘       └──────────────┘
                              │
                              ▼
                       ┌──────────────┐
                       │   MODULES    │
                       │ ┌──────────┐ │
                       │ │ id       │ │
                       │ │ code     │ │
                       │ │ name     │ │
                       │ │ status   │ │
                       │ └──────────┘ │
                       └──────────────┘
```

### Tablas Principales

#### 1. **USERS** (Usuarios)
```sql
CREATE TABLE users (
    id              BIGINT PRIMARY KEY AUTO_INCREMENT,
    name            VARCHAR(255) NOT NULL,
    email           VARCHAR(255) UNIQUE NOT NULL,
    email_verified  TIMESTAMP NULL,
    password        VARCHAR(255) NOT NULL,
    role_id         BIGINT FOREIGN KEY (roles.id),
    status          VARCHAR(50) DEFAULT 'active',
    created_at      TIMESTAMP,
    updated_at      TIMESTAMP
)
```
- **Propósito**: Almacena los usuarios del sistema
- **Campos importantes**:
  - `role_id`: Referencia al rol asignado al usuario
  - `status`: Controla si el usuario está activo o inactivo
  - `password`: Contraseña hasheada

#### 2. **ROLES** (Roles)
```sql
CREATE TABLE roles (
    id          BIGINT PRIMARY KEY AUTO_INCREMENT,
    code        VARCHAR(255) UNIQUE,
    name        VARCHAR(255) NOT NULL,
    status      VARCHAR(50) DEFAULT 'active',
    created_at  TIMESTAMP,
    updated_at  TIMESTAMP,
    deleted_at  TIMESTAMP (soft delete)
)
```
- **Propósito**: Define los roles disponibles en el sistema
- **Ejemplos de roles**: `admin`, `editor`, `viewer`
- **Soft Delete**: Puede eliminarse lógicamente sin perder datos

#### 3. **PERMISSIONS** (Permisos)
```sql
CREATE TABLE permissions (
    id          BIGINT PRIMARY KEY AUTO_INCREMENT,
    code        VARCHAR(255) UNIQUE,
    name        VARCHAR(255) NOT NULL,
    created_at  TIMESTAMP,
    updated_at  TIMESTAMP,
    deleted_at  TIMESTAMP
)
```
- **Propósito**: Define las acciones que se pueden realizar
- **Ejemplos**: `create`, `read`, `update`, `delete`

#### 4. **MODULES** (Módulos)
```sql
CREATE TABLE modules (
    id          BIGINT PRIMARY KEY AUTO_INCREMENT,
    code        VARCHAR(255) UNIQUE,
    name        VARCHAR(255) NOT NULL,
    status      VARCHAR(50) DEFAULT 'active',
    created_at  TIMESTAMP,
    updated_at  TIMESTAMP,
    deleted_at  TIMESTAMP
)
```
- **Propósito**: Agrupa funcionalidades del sistema
- **Ejemplos**: `users`, `roles`, `modules`, `reports`

#### 5. **ROLE_PERMISSIONS** (Tabla Intermedia)
```sql
CREATE TABLE role_permissions (
    id              BIGINT PRIMARY KEY AUTO_INCREMENT,
    role_id         BIGINT FOREIGN KEY (roles.id),
    module_id       BIGINT FOREIGN KEY (modules.id),
    permission_id   BIGINT FOREIGN KEY (permissions.id),
    allowed         BOOLEAN DEFAULT true,
    created_at      TIMESTAMP,
    updated_at      TIMESTAMP,
    UNIQUE (role_id, module_id, permission_id)
)
```
- **Propósito**: Almacena qué permisos tiene cada rol en cada módulo
- **Ejemplo**: Admin puede `create`, `read`, `update`, `delete` en el módulo `users`

---

## Models (Modelos)

Los Models son clases PHP que representan tablas de la base de datos y definen relaciones.

### 1. **User.php** - Modelo de Usuarios

```php
namespace App\Models;

class User extends Authenticatable {
    // Atributos asignables en masa
    protected $fillable = ['name', 'email', 'password', 'role_id', 'status'];
    
    // Relación: Un usuario pertenece a un rol
    public function role(): BelongsTo {
        return $this->belongsTo(Role::class);
    }
}
```

**Funciones principales:**
- Autenticación con Laravel Sanctum (tokens API)
- Validación de credenciales
- Sistema de caché con Redis para permisos
- Métodos para invalidar caché cuando cambian roles

**Métodos importantes:**
- `cacheStore()`: Obtiene la instancia de cache (Redis)
- `flushRolePermissionsCache()`: Limpia el caché de permisos
- `getCachedRoleWithPermissions()`: Obtiene rol y permisos en caché

---

### 2. **Role.php** - Modelo de Roles

```php
namespace App\Models;

class Role extends Model {
    use SoftDeletes;
    
    protected $fillable = ['code', 'name', 'status'];
    
    // Relación: Un rol tiene muchos usuarios
    public function users(): HasMany {
        return $this->hasMany(User::class);
    }
    
    // Relación: Un rol tiene muchos permisos
    public function rolePermissions(): HasMany {
        return $this->hasMany(RolePermission::class);
    }
    
    // Relación: Un rol pertenece a muchos módulos
    public function modules(): BelongsToMany {
        return $this->belongsToMany(Module::class, 'role_permissions')
            ->withPivot(['permission_id', 'allowed']);
    }
}
```

**Características:**
- Usa `SoftDeletes`: El registro no se elimina de la BD, solo se marca como eliminado
- Cuando se guarda o elimina un rol, **limpia automáticamente el caché** de permisos
- Relaciones muchos-a-muchos con módulos y permisos

---

### 3. **Permission.php** - Modelo de Permisos

```php
namespace App\Models;

class Permission extends Model {
    use SoftDeletes;
    
    protected $fillable = ['code', 'name'];
    
    // Relación con RolePermission
    public function rolePermissions(): HasMany {
        return $this->hasMany(RolePermission::class);
    }
    
    // Relación muchos-a-muchos con roles
    public function roles(): BelongsToMany {
        return $this->belongsToMany(Role::class, 'role_permissions')
            ->withPivot(['module_id', 'allowed'])
            ->withTimestamps();
    }
    
    // Relación muchos-a-muchos con módulos
    public function modules(): BelongsToMany {
        return $this->belongsToMany(Module::class, 'role_permissions')
            ->withPivot(['role_id', 'allowed'])
            ->withTimestamps();
    }
}
```

**Características:**
- Define las acciones posibles en el sistema
- Conecta roles con módulos a través de la tabla intermedia

---

### 4. **Module.php** - Modelo de Módulos

```php
namespace App\Models;

class Module extends Model {
    use SoftDeletes;
    
    protected $fillable = ['code', 'name', 'status'];
    
    // Relación con RolePermission
    public function rolePermissions(): HasMany {
        return $this->hasMany(RolePermission::class);
    }
    
    // Relación muchos-a-muchos con roles
    public function roles(): BelongsToMany {
        return $this->belongsToMany(Role::class, 'role_permissions')
            ->withPivot(['permission_id', 'allowed']);
    }
    
    // Relación muchos-a-muchos con permisos
    public function permissions(): BelongsToMany {
        return $this->belongsToMany(Permission::class, 'role_permissions')
            ->withPivot(['role_id', 'allowed']);
    }
    
    // Scope: Obtener solo módulos activos
    public function scopeActive($query) {
        return $query->where('status', 'active');
    }
}
```

---

### 5. **RolePermission.php** - Modelo de Relación

```php
namespace App\Models;

class RolePermission extends Model {
    protected $fillable = ['role_id', 'module_id', 'permission_id', 'allowed'];
    
    // Define las relaciones inversas
    public function role() { return $this->belongsTo(Role::class); }
    public function module() { return $this->belongsTo(Module::class); }
    public function permission() { return $this->belongsTo(Permission::class); }
}
```

**Propósito**: Une los tres conceptos - qué permiso tiene cada rol en cada módulo

---

## Controllers (Controladores)

Los Controllers manejan la lógica de las rutas API y retornan respuestas JSON.

### 1. **AuthController.php** - Autenticación

**Métodos:**

#### `login(Request $request)`
```php
POST /api/v1/login
{
    "email": "user@example.com",
    "password": "password123"
}
```
**Flujo:**
1. Valida email y password
2. Busca el usuario en la BD
3. Verifica la contraseña hasheada
4. Comprueba que el usuario esté activo
5. Crea un token API con Sanctum
6. Retorna el token en una cookie `access_token`

**Respuesta exitosa:**
```json
{
    "status": true,
    "status_code": 200,
    "message": "Inicio de sesion exitoso.",
    "access_token": "5|abc123..."
}
```

#### `logout(Request $request)`
```php
POST /api/v1/logout
Authorization: Bearer {token}
```
**Flujo:**
1. Obtiene el usuario autenticado
2. Elimina el token actual
3. Limpia la cookie de acceso

---

### 2. **UserController.php** - Gestión de Usuarios

#### `index(Request $request)` - Listar usuarios
```php
GET /api/v1/users?search=John&per_page=15&page=1
```
**Flujo:**
1. Obtiene parámetros de búsqueda, paginación
2. Filtra por nombre o email
3. Carga relación con roles
4. Retorna usuarios paginados

**Respuesta:**
```json
{
    "status": true,
    "status_code": 200,
    "data": {
        "data": [...usuarios...],
        "current_page": 1,
        "total": 50,
        "per_page": 15
    }
}
```

#### `store(Request $request)` - Crear usuario
```php
POST /api/v1/users
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "SecurePass123!",
    "role_id": 2,
    "status": "active"
}
```
**Validaciones:**
- `name`: Requerido, máx 255 caracteres
- `email`: Email válido, único en BD
- `password`: Mínimo 8 caracteres
- `role_id`: Debe existir en tabla roles
- `status`: 'active' o 'inactive'

#### `update(Request $request, User $user)` - Actualizar usuario
```php
PUT /api/v1/users/{user_id}
{
    "name": "John Updated",
    "email": "newemail@example.com",
    ...
}
```
**Nota:** Usa `sometimes` para validar solo los campos enviados

#### `destroy(User $user)` - Eliminar usuario
```php
DELETE /api/v1/users/{user_id}
```
**Nota:** Usa soft delete de Laravel (registra deleted_at)

---

### 3. **RoleController.php** - Gestión de Roles

#### `index()` - Listar roles
```php
GET /api/v1/roles
```

#### `store(Request $request)` - Crear rol
```php
POST /api/v1/roles
{
    "code": "editor",
    "name": "Editor",
    "status": "active"
}
```

#### `assignToUser(Request $request, User $user)` - Asignar rol a usuario
```php
POST /api/v1/users/{user_id}/role
{
    "role_id": 2
}
```

---

### 4. **RolePermissionController.php** - Sincronizar Permisos

#### `sync(Request $request, Role $role)` - Asignar permisos a un rol
```php
POST /api/v1/roles/{role_id}/permissions/sync
{
    "permissions": [
        {
            "module_id": 1,
            "permission_id": 1,
            "allowed": true
        },
        {
            "module_id": 1,
            "permission_id": 2,
            "allowed": true
        }
    ]
}
```
**Flujo:**
1. Recibe array de permisos
2. Sincroniza con tabla role_permissions
3. Limpia caché de permisos
4. Retorna confirmación

---

### 5. **ModuleController.php** - Gestión de Módulos

#### `store(Request $request)` - Crear módulo
```php
POST /api/v1/modules
{
    "code": "users",
    "name": "Gestión de Usuarios",
    "status": "active"
}
```

---

## Middleware (Intermediarios)

El middleware actúa como filtros que procesan las solicitudes antes de llegar a los controladores.

### 1. **CheckPermission.php** - Validar Permisos

```php
namespace App\Http\Middleware;

class CheckPermission {
    public function handle($request, Closure $next, string $module, string $permission) {
        $user = $request->user();
        
        // Verifica si el usuario tiene permiso para el módulo y acción
        if (!$user->hasPermission($module, $permission)) {
            return response()->json([
                'status' => false,
                'status_code' => 403,
                'message' => 'No tienes permiso para realizar esta accion.'
            ], 403);
        }
        
        return $next($request);
    }
}
```

**Uso en rutas:**
```php
Route::get('/users', [UserController::class, 'index'])
    ->middleware('permission:users,read');
    // "users" = módulo
    // "read" = permiso
```

**Flujo:**
1. Obtiene el usuario autenticado
2. Llama `$user->hasPermission('users', 'read')`
3. Si NO tiene permiso → Retorna error 403
4. Si SÍ tiene permiso → Continúa a siguiente middleware/controlador

---

### 2. **EnsureUserIsActive.php** - Validar Usuario Activo

```php
namespace App\Http\Middleware;

class EnsureUserIsActive {
    public function handle($request, Closure $next) {
        // Verifica que el usuario esté activo
        if ($request->user() && $request->user()->status !== 'active') {
            return response()->json([
                'status' => false,
                'status_code' => 403,
                'message' => 'Tu cuenta esta desactivada.'
            ], 403);
        }
        
        return $next($request);
    }
}
```

---

### 3. **UseAccessTokenCookie.php** - Obtener Token de Cookie

```php
namespace App\Http\Middleware;

class UseAccessTokenCookie {
    public function handle($request, Closure $next) {
        // Si no hay Authorization header pero hay cookie, usa el token de la cookie
        if (!$request->header('Authorization') && $request->hasCookie('access_token')) {
            $request->headers->set('Authorization', 'Bearer ' . $request->cookie('access_token'));
        }
        
        return $next($request);
    }
}
```

---

## Routes/API (Rutas)

Las rutas definen los endpoints disponibles y aplican middleware.

### Estructura de Rutas

```php
// Ruta pública
Route::post('/v1/login', [AuthController::class, 'login']);

// Rutas protegidas
Route::middleware([
    'access.token.cookie',    // Middleware 1: Lee token de cookie
    'auth:sanctum',           // Middleware 2: Valida autenticación
    'active.user'             // Middleware 3: Verifica usuario activo
])->prefix('v1')->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Usuarios
    Route::get('/users', [UserController::class, 'index'])
        ->middleware('permission:users,read');
    Route::post('/users', [UserController::class, 'store'])
        ->middleware('permission:users,create');
    Route::put('/users/{user}', [UserController::class, 'update'])
        ->middleware('permission:users,update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])
        ->middleware('permission:users,delete');
    
    // Roles
    Route::get('/roles', [RoleController::class, 'index'])
        ->middleware('permission:roles,read');
    Route::post('/roles', [RoleController::class, 'store'])
        ->middleware('permission:roles,create');
    Route::post('/users/{user}/role', [RoleController::class, 'assignToUser'])
        ->middleware('permission:roles,assign_permissions');
    
    // Módulos
    Route::post('/modules', [ModuleController::class, 'store'])
        ->middleware('permission:modules,create');
    
    // Permisos
    Route::post('/roles/{role}/permissions/sync', [RolePermissionController::class, 'sync'])
        ->middleware('permission:roles,assign_permissions');
});
```

### Orden de Middleware

```
Solicitud HTTP
    ↓
1. UseAccessTokenCookie → Extrae token de cookie si existe
    ↓
2. auth:sanctum → Valida que el token sea válido
    ↓
3. EnsureUserIsActive → Verifica que el usuario esté activo
    ↓
4. CheckPermission → Valida permisos específicos (si aplica)
    ↓
Controller → Ejecuta la lógica
    ↓
Respuesta JSON
```

---

## Flujo del Sistema

### 1️⃣ **Flujo de Login**

```
Usuario entra credenciales
        ↓
POST /api/v1/login { email, password }
        ↓
AuthController::login()
    ├─ Valida email y password
    ├─ Busca usuario en BD
    ├─ Verifica Hash password
    ├─ Comprueba status = 'active'
    ├─ Carga rol y permisos en caché
    ├─ Crea token con Sanctum
    └─ Retorna token en cookie + JSON
        ↓
Cliente recibe token (en cookie + respuesta)
        ↓
Cliente guarda token (automático en cookie)
```

### 2️⃣ **Flujo de Solicitud Protegida**

```
Cliente hace solicitud
GET /api/v1/users
Cookie: access_token=5|abc123...
        ↓
Servidor procesa middleware:

1. UseAccessTokenCookie
   └─ Lee cookie access_token
   └─ Establece header: Authorization: Bearer 5|abc123...
        ↓
2. auth:sanctum
   └─ Valida token en tabla personal_access_tokens
   └─ Obtiene usuario asociado
   └─ Si no válido → 401 Unauthorized
        ↓
3. EnsureUserIsActive
   └─ Verifica $user->status = 'active'
   └─ Si inactivo → 403 Forbidden
        ↓
4. CheckPermission:users,read
   └─ Obtiene user->role
   └─ Consulta caché/BD: ¿Rol tiene permiso 'read' en módulo 'users'?
   └─ Si NO tiene → 403 Forbidden
   └─ Si SÍ tiene → Continúa
        ↓
UserController::index()
        ↓
Retorna respuesta JSON con usuarios
```

### 3️⃣ **Flujo de Verificación de Permisos**

```
CheckPermission middleware llama:
$user->hasPermission('users', 'read')
        ↓
Busca en caché Redis:
cache_key = "user:{user_id}:role:permissions:{role_id}"
        ↓
Si está en caché:
└─ Retorna datos en caché
        ↓
Si NO está en caché:
├─ SELECT * FROM role_permissions
│  WHERE role_id = {role_id}
│  AND module.code = 'users'
│  AND permission.code = 'read'
│
├─ Guarda resultado en caché (Redis)
└─ Retorna resultado
        ↓
true/false → Permite/Niega acceso
```

### 4️⃣ **Flujo de Crear Usuario**

```
Cliente envia POST /api/v1/users con datos
        ↓
Middleware valida autenticación y permisos
    └─ Middleware 'permission:users,create'
        ↓
UserController::store()
        ↓
$request->validate([
    'name' => 'required|string|max:255',
    'email' => 'required|email|unique:users',
    'password' => 'required|string|min:8',
    'role_id' => 'nullable|integer|exists:roles,id',
    'status' => 'nullable|in:active,inactive'
])
        ↓
Si validación falla → Retorna errores 422
        ↓
Si validación ok:
├─ Hash password
├─ User::create($data)
├─ Carga relación role
└─ Retorna usuario creado con status 201
        ↓
Respuesta JSON con nuevo usuario
```

### 5️⃣ **Flujo de Asignar Rol a Usuario**

```
POST /api/v1/users/{user_id}/role { role_id: 2 }
        ↓
RoleController::assignToUser()
        ↓
$user->update(['role_id' => 2])
        ↓
Hook en Role model (evento saved):
User::flushRolePermissionsCache($user->role_id)
        ↓
Limpia caché de permisos del usuario
        ↓
Respuesta exitosa
```

---

## Instructivo de Instalación y Uso

### ✅ Requisitos Previos

- **PHP** 8.2+
- **Composer**
- **MySQL** o PostgreSQL
- **Redis** (para caché)
- **Node.js** (para Assets)

### 📦 Paso 1: Instalación Inicial

```bash
# Clonar proyecto (si es necesario)
git clone <repo_url>
cd laravelCore

# Instalar dependencias PHP
composer install

# Instalar dependencias Node.js
npm install

# Copiar variables de entorno
cp .env.example .env

# Generar clave de aplicación
php artisan key:generate
```

### 🗄️ Paso 2: Configurar Base de Datos

**Editar `.env`:**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravelcore
DB_USERNAME=root
DB_PASSWORD=your_password

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### 🚀 Paso 3: Ejecutar Migraciones

```bash
# Crear tablas en BD
php artisan migrate

# Si necesitas resetear
php artisan migrate:reset
php artisan migrate
```

### 🌱 Paso 4: Ejecutar Seeders (Datos Iniciales)

```bash
# Ejecutar todos los seeders
php artisan db:seed

# O ejecutar seeders específicos
php artisan db:seed --class=ModuleSeeder
php artisan db:seed --class=PermissionSeeder
php artisan db:seed --class=RoleSeeder
php artisan db:seed --class=RolePermissionSeeder
php artisan db:seed --class=UserSeeder
```

**Datos que se crean:**
- **Módulos**: users, roles, modules
- **Permisos**: create, read, update, delete
- **Roles**: admin, editor, viewer
- **Usuarios**: user@example.com (admin), editor@example.com (editor)
- **Asignaciones**: Permisos para cada rol

### 🔧 Paso 5: Configurar Redis (si usas caché)

**Instalar Redis en Windows:**
```bash
# Usar Docker (recomendado)
docker run --name redis -d -p 6379:6379 redis:latest

# O descargar de: https://github.com/microsoftarchive/redis/releases
```

**Verificar conexión:**
```bash
php artisan tinker
> Cache::store('redis')->put('test', 'value')
> Cache::store('redis')->get('test')
// Debe retornar 'value'
```

### ▶️ Paso 6: Iniciar Servidor

```bash
# Servidor de desarrollo
php artisan serve

# O con Laravel Sail (Docker)
./vendor/bin/sail up

# En otra terminal, compilar assets
npm run dev
```

**Servidor corriendo en:** `http://localhost:8000`

---

## Endpoints Disponibles

### 🔓 Públicos (Sin autenticación)

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| POST | `/api/v1/login` | Login usuario |

### 🔐 Protegidos (Requieren autenticación + permisos)

#### **Usuarios**
| Método | Endpoint | Permiso Requerido | Descripción |
|--------|----------|------------------|-------------|
| GET | `/api/v1/users` | `users:read` | Listar usuarios |
| POST | `/api/v1/users` | `users:create` | Crear usuario |
| PUT | `/api/v1/users/{id}` | `users:update` | Actualizar usuario |
| DELETE | `/api/v1/users/{id}` | `users:delete` | Eliminar usuario |

#### **Roles**
| Método | Endpoint | Permiso Requerido | Descripción |
|--------|----------|------------------|-------------|
| GET | `/api/v1/roles` | `roles:read` | Listar roles |
| POST | `/api/v1/roles` | `roles:create` | Crear rol |
| POST | `/api/v1/users/{id}/role` | `roles:assign_permissions` | Asignar rol a usuario |
| POST | `/api/v1/roles/{id}/permissions/sync` | `roles:assign_permissions` | Sincronizar permisos del rol |

#### **Módulos**
| Método | Endpoint | Permiso Requerido | Descripción |
|--------|----------|------------------|-------------|
| POST | `/api/v1/modules` | `modules:create` | Crear módulo |

#### **Autenticación**
| Método | Endpoint | Descripción |
|--------|----------|-------------|
| POST | `/api/v1/logout` | Logout usuario (requiere token) |

---

### 📋 Ejemplos de Uso

#### 1. Login

**Request:**
```bash
curl -X POST http://localhost:8000/api/v1/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "user@example.com",
    "password": "password"
  }'
```

**Response:**
```json
{
    "status": true,
    "status_code": 200,
    "message": "Inicio de sesion exitoso.",
    "access_token": "5|abc123..."
}
```

#### 2. Listar Usuarios

**Request:**
```bash
curl -X GET "http://localhost:8000/api/v1/users?search=john&per_page=15" \
  -H "Authorization: Bearer 5|abc123..." \
  -H "Cookie: access_token=5|abc123..."
```

**Response:**
```json
{
    "status": true,
    "status_code": 200,
    "message": "Usuarios obtenidos correctamente.",
    "data": {
        "data": [
            {
                "id": 1,
                "name": "John Doe",
                "email": "john@example.com",
                "role_id": 1,
                "status": "active",
                "role": {
                    "id": 1,
                    "code": "admin",
                    "name": "Administrator"
                }
            }
        ],
        "current_page": 1,
        "total": 1,
        "per_page": 15
    }
}
```

#### 3. Crear Usuario

**Request:**
```bash
curl -X POST http://localhost:8000/api/v1/users \
  -H "Authorization: Bearer 5|abc123..." \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Jane Smith",
    "email": "jane@example.com",
    "password": "SecurePass123!",
    "role_id": 2,
    "status": "active"
  }'
```

#### 4. Asignar Rol a Usuario

**Request:**
```bash
curl -X POST http://localhost:8000/api/v1/users/3/role \
  -H "Authorization: Bearer 5|abc123..." \
  -H "Content-Type: application/json" \
  -d '{
    "role_id": 2
  }'
```

#### 5. Sincronizar Permisos de Rol

**Request:**
```bash
curl -X POST http://localhost:8000/api/v1/roles/2/permissions/sync \
  -H "Authorization: Bearer 5|abc123..." \
  -H "Content-Type: application/json" \
  -d '{
    "permissions": [
        {
            "module_id": 1,
            "permission_id": 1,
            "allowed": true
        },
        {
            "module_id": 1,
            "permission_id": 2,
            "allowed": true
        }
    ]
  }'
```

#### 6. Logout

**Request:**
```bash
curl -X POST http://localhost:8000/api/v1/logout \
  -H "Authorization: Bearer 5|abc123..."
```

---

## 🎯 Resumen de Flujo Completo

```
1. Usuario hace LOGIN
   ↓
2. Sistema retorna TOKEN (en cookie)
   ↓
3. Cliente almacena TOKEN en cookie
   ↓
4. En cada solicitud protegida:
   ├─ Middleware extrae TOKEN de cookie
   ├─ Valida que TOKEN sea válido
   ├─ Verifica que usuario esté ACTIVO
   ├─ Comprueba PERMISOS del usuario
   └─ Si todo ok → Ejecuta controlador
   ↓
5. Controlador realiza operación en BD
   ↓
6. Retorna respuesta JSON
```

---

## 📚 Comandos Útiles

```bash
# Ver todas las rutas
php artisan route:list

# Limpiar caché
php artisan cache:clear
php artisan config:clear

# Ejecutar una migración específica
php artisan migrate --path=database/migrations/filename.php

# Crear modelo con migration y seeder
php artisan make:model ModelName -mcs

# Verificar base de datos
php artisan tinker
> User::all()
> Role::with('users')->get()

# Generar documentación
php artisan scribe:generate
```

---

## 🔒 Seguridad

- ✅ Passwords hasheados con bcrypt
- ✅ Tokens API con Sanctum (corta duración)
- ✅ Validación de entrada en todos los endpoints
- ✅ CSRF protection habilitada
- ✅ SQL injection prevenida con Eloquent ORM
- ✅ Caché de permisos con invalidación automática

---

## 📞 Soporte

Para preguntas o problemas:
1. Revisa los logs en `storage/logs/`
2. Ejecuta `php artisan tinker` para debugging
3. Consulta documentación oficial de Laravel: https://laravel.com/docs

