# Diagramas y Visualización - Sistema de Gestión de Permisos

## 1. Arquitectura General del Sistema

```
┌────────────────────────────────────────────────────────────┐
│                      CLIENTE (Frontend)                     │
│              (Web, Mobile, Desktop, etc.)                   │
└────────────────────┬─────────────────────────────────────┘
                     │
        HTTP/HTTPS (JSON Requests/Responses)
                     │
┌────────────────────▼─────────────────────────────────────┐
│                  ROUTER/ROUTES API                        │
│         (Define endpoints y aplica middleware)             │
│  POST /login  →  GET /users  →  POST /roles  etc.        │
└────────────────────┬─────────────────────────────────────┘
                     │
┌────────────────────▼─────────────────────────────────────┐
│                MIDDLEWARE STACK                           │
│  ┌──────────────────────────────────────────────────┐    │
│  │ 1. UseAccessTokenCookie  (Extrae token)         │    │
│  │ 2. auth:sanctum          (Valida autenticación) │    │
│  │ 3. EnsureUserIsActive    (Verifica status)      │    │
│  │ 4. CheckPermission       (Valida permisos)      │    │
│  └──────────────────────────────────────────────────┘    │
└────────────────────┬─────────────────────────────────────┘
                     │
┌────────────────────▼─────────────────────────────────────┐
│           CONTROLADORES (Controllers)                      │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐ │
│  │AuthCtrl  │  │UserCtrl  │  │RoleCtrl  │  │PermCtrl  │ │
│  └──────────┘  └──────────┘  └──────────┘  └──────────┘ │
│   Lógica de negocio - Acceso a BD                        │
└────────────────────┬─────────────────────────────────────┘
                     │
┌────────────────────▼─────────────────────────────────────┐
│            MODELOS (Eloquent ORM)                         │
│  ┌─────────────────────────────────────────────┐         │
│  │ User ↔ Role ↔ RolePermission                │         │
│  │              ↓         ↓                     │         │
│  │           Module   Permission                │         │
│  └─────────────────────────────────────────────┘         │
│     Mapeo a tablas BD + relaciones                        │
└────────────────────┬─────────────────────────────────────┘
                     │
┌────────────────────▼─────────────────────────────────────┐
│           CAPA DE DATOS (Database Layer)                  │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐    │
│  │  users   │ │  roles   │ │  modules │ │ perms    │    │
│  └──────────┘ └──────────┘ └──────────┘ └──────────┘    │
│  ┌──────────────────────────────────┐                    │
│  │    role_permissions (Intermedia) │                    │
│  └──────────────────────────────────┘                    │
└────────────────────────────────────────────────────────────┘
                     │
         ┌───────────┴───────────┐
         ▼                       ▼
    ┌─────────┐            ┌──────────┐
    │ MySQL   │            │ Redis    │
    │ Base    │            │ Cache    │
    │ Datos   │            │(Permisos)│
    └─────────┘            └──────────┘
```

---

## 2. Flujo de Autenticación

```
FASE 1: LOGIN
═════════════════════════════════════════════════════════════

      Usuario                        Sistema
         │                              │
         │─── POST /login ────────────→ │
         │    { email, password }       │
         │                              │
         │                      ┌─────▼─────┐
         │                      │ Validar   │
         │                      │ credenciales
         │                      └─────┬─────┘
         │                            │
         │                      ┌─────▼──────────┐
         │                      │ ¿Email existe? │
         │                      │ ¿Password ok?  │
         │                      │ ¿Status=active?
         │                      └─────┬──────────┘
         │                            │
         │                      ┌─────▼──────────┐
         │                      │ Crear token    │
         │                      │ con Sanctum    │
         │                      └─────┬──────────┘
         │                            │
         │                      ┌─────▼──────────┐
         │                      │ Cargar permisos│
         │                      │ en caché Redis │
         │                      └─────┬──────────┘
         │                            │
         │ ◀─── 200 OK ──────────────│
         │    { access_token }        │
         │    Cookie: access_token    │
         │                            │


FASE 2: SOLICITUD PROTEGIDA
═════════════════════════════════════════════════════════════

      Cliente                        Servidor
         │
         │─── GET /users ──────────┐
         │    Cookie: token        │
         │                         │
         │                    ┌────▼───────────┐
         │                    │ UseAccessToken │
         │                    │ Extrae token   │
         │                    │ de cookie      │
         │                    └────┬───────────┘
         │                         │
         │                    ┌────▼───────────┐
         │                    │ auth:sanctum   │
         │                    │ Valida token   │
         │                    │ en BD          │
         │                    └────┬───────────┘
         │                         │
         │                    ┌────▼───────────┐
         │                    │ EnsureActive   │
         │                    │ user.status==  │
         │                    │ 'active'?      │
         │                    └────┬───────────┘
         │                         │
         │                    ┌────▼───────────────┐
         │                    │ CheckPermission    │
         │                    │ users:read?        │
         │                    │ Consulta caché     │
         │                    │ Redis              │
         │                    └────┬───────────────┘
         │                         │
         │                    ┌────▼───────────┐
         │                    │ Controlador    │
         │                    │ index()        │
         │                    │ Query BD       │
         │                    └────┬───────────┘
         │                         │
         │ ◀─── 200 OK ──────────┤
         │    { users... }        │
         │                        │
```

---

## 3. Estructura de Tablas de BD

```
USUARIOS
╔════╦═══════════╦════════════════╦══════════╦═════════╗
║ id ║ name      ║ email          ║ role_id  ║ status  ║
╠════╬═══════════╬════════════════╬══════════╬═════════╣
║ 1  ║ John      ║ john@ex.com    ║ 1        ║ active  ║
║ 2  ║ Jane      ║ jane@ex.com    ║ 2        ║ active  ║
║ 3  ║ Bob       ║ bob@ex.com     ║ 3        ║ inactive║
╚════╩═══════════╩════════════════╩══════════╩═════════╝
       └─────────────┬─────────────┘
                     │
                     ▼ FK
ROLES
╔════╦════════╦═══════════════╦════════╗
║ id ║ code   ║ name          ║ status ║
╠════╬════════╬═══════════════╬════════╣
║ 1  ║ admin  ║ Administrador ║ active ║
║ 2  ║ editor ║ Editor        ║ active ║
║ 3  ║ viewer ║ Viewer        ║ active ║
╚════╩════════╩═══════════════╩════════╝
       │
       └──────────┬──────────┘
                  │
    ┌─────────────▼─────────────┐
    │   ROLE_PERMISSIONS        │
    │   (Tabla Intermedia)       │
    │                           │
    │ ╔════╦═════════╦═════════╗│
    │ ║ id ║ role_id ║ mod_id  ║│
    │ ╠════╬═════════╬═════════╣│
    │ ║ 1  ║ 1       ║ 1       ║│  (Admin puede hacer TODO
    │ ║ 2  ║ 1       ║ 2       ║│   en users, roles, modules)
    │ ║ 3  ║ 1       ║ 3       ║│
    │ ║ 4  ║ 2       ║ 1       ║│  (Editor solo en users)
    │ ║ 5  ║ 3       ║ 1       ║│  (Viewer solo lectura)
    │ ╚════╩═════════╩═════════╝│
    │
    └────────┬─────────────┬──────────┐
             │             │          │
    ┌────────▼──┐   ┌──────▼────┐   │
    │ MODULES   │   │PERMISSIONS│   │
    │           │   │           │   │
    │ ╔════╦───╗│   │ ╔════╦───╗│   │
    │ ║ id ║cod║│   │ ║ id ║cod║│   │
    │ ╠════╬───╣│   │ ╠════╬───╣│   │
    │ ║ 1  ║usr║│   │ ║ 1  ║cr ║│   │
    │ ║ 2  ║rol║│   │ ║ 2  ║re ║│   │
    │ ║ 3  ║mod║│   │ ║ 3  ║up ║│   │
    │ ║ 4  ║per║│   │ ║ 4  ║de ║│   │
    │ ╚════╩───╝│   │ ╚════╩───╝│   │
    │           │   │           │   │
    └───────────┘   └───────────┘   │
                    │                │
                    └────────────────┘
```

---

## 4. Matriz de Permisos

```
┌───────────────┬─────────┬────────┬────────┬────────┐
│ Rol/Módulo    │ CREATE  │ READ   │ UPDATE │ DELETE │
├───────────────┼─────────┼────────┼────────┼────────┤
│ ADMIN - Users │   ✅    │   ✅   │   ✅   │   ✅   │
│ ADMIN - Roles │   ✅    │   ✅   │   ✅   │   ✅   │
│ ADMIN - Mods  │   ✅    │   ✅   │   ✅   │   ✅   │
├───────────────┼─────────┼────────┼────────┼────────┤
│ EDITOR - User │   ✅    │   ✅   │   ✅   │   ❌   │
│ EDITOR - Role │   ❌    │   ✅   │   ❌   │   ❌   │
│ EDITOR - Mods │   ❌    │   ✅   │   ❌   │   ❌   │
├───────────────┼─────────┼────────┼────────┼────────┤
│ VIEWER - User │   ❌    │   ✅   │   ❌   │   ❌   │
│ VIEWER - Role │   ❌    │   ✅   │   ❌   │   ❌   │
│ VIEWER - Mods │   ❌    │   ✅   │   ❌   │   ❌   │
└───────────────┴─────────┴────────┴────────┴────────┘

✅ = Permitido     ❌ = Denegado
```

---

## 5. Ciclo de Vida de una Solicitud HTTP

```
┌─────────────────────────────────────────────────────────────┐
│ 1. CLIENTE ENVÍA REQUEST                                    │
│    GET /api/v1/users                                        │
│    Headers: {Authorization: Bearer token}                   │
│    Cookie: {access_token=token}                             │
└────────────────────┬────────────────────────────────────────┘
                     │
┌────────────────────▼────────────────────────────────────────┐
│ 2. ROUTER DETECTA RUTA                                      │
│    Route::get('/users', [UserController::class, 'index'])   │
│      ->middleware('permission:users,read')                  │
└────────────────────┬────────────────────────────────────────┘
                     │
┌────────────────────▼────────────────────────────────────────┐
│ 3. KERNEL APLICA MIDDLEWARE GLOBAL                          │
│    App\Http\Kernel → middleware[] global                    │
│    ├─ UseAccessTokenCookie                                  │
│    ├─ auth:sanctum                                          │
│    └─ EnsureUserIsActive                                    │
└────────────────────┬────────────────────────────────────────┘
                     │
         ┌───────────┴───────────┐
         │                       │
         ▼                       ▼
   ✅ Válido                ❌ Inválido
   Continúa                 Retorna error
         │                       │
         │                   401/403 JSON
    ┌────▼─────┐
    │MIDDLEWARE │
    │POR RUTA   │
    │permission:│
    │users,read │
    └────┬─────┘
         │
      ¿User tiene permiso?
         │
    ┌────┴─────┐
    │           │
    ▼           ▼
  ✅           ❌
  Continúa    403 Error
    │
┌───▼──────────────────────────────────────────────────────┐
│ 4. CONTROLADOR EJECUTA LÓGICA                            │
│    UserController::index()                               │
│    ├─ $search = $request->query('search')                │
│    ├─ $users = User::where(...)->get()                   │
│    └─ return response()->json([...])                     │
└───┬──────────────────────────────────────────────────────┘
    │
┌───▼──────────────────────────────────────────────────────┐
│ 5. RESPUESTA SE ENVÍA AL CLIENTE                         │
│    200 OK                                                │
│    {                                                     │
│      "status": true,                                     │
│      "data": [...]                                       │
│    }                                                     │
└───────────────────────────────────────────────────────────┘
```

---

## 6. Flujo de Validación de Permisos (Detallado)

```
User intenta: GET /api/v1/users
              Middleware: permission:users,read
                     │
                     ▼
┌────────────────────────────────────────┐
│ CheckPermission::handle()               │
│ Parámetros: module='users', perm='read'│
└────────────────┬───────────────────────┘
                 │
         ┌───────▼────────┐
         │ $user =        │
         │ $request->user │
         └───────┬────────┘
                 │
         ┌───────▼────────────────────┐
         │ $user->hasPermission(       │
         │   'users', 'read')          │
         └───────┬────────────────────┘
                 │
                 ▼
    ┌────────────────────────┐
    │ ¿Existe en caché Redis?│
    └────────┬───────────────┘
             │
     ┌───────┴────────┐
     │                │
    SÍ               NO
     │                │
  ┌──▼─┐         ┌───▼──────────────┐
  │Cache│         │ Query BD:        │
  │     │         │ SELECT * FROM    │
  │return        │ role_permissions │
  │     │         │ WHERE role_id=..│
  └──┬──┘         │ AND module_id=..│
     │            │ AND permission..│
     │            └───┬──────────────┘
     │                │
     └───────┬────────┘
             │
      ┌──────▼────────┐
      │ ¿Registro     │
      │ encontrado?   │
      └──────┬────────┘
             │
       ┌─────┴──────┐
       │            │
      SÍ           NO
       │            │
    ┌──▼─┐      ┌──▼──┐
    │true│      │false│
    │    │      │     │
    └──┬─┘      └──┬──┘
       │           │
       │       ┌───▼──────────┐
       │       │ Retorna 403  │
       │       │ JSON error   │
       │       └──────────────┘
       │
    ┌──▼────────────────┐
    │ Continúa a        │
    │ Controlador       │
    │ (Ejecuta lógica)  │
    └──────────────────┘
```

---

## 7. Caché de Permisos (Redis)

```
Redis Cache Structure
═════════════════════════════════════════════════════════════

Key: "roles:version"
Value: 1

Key: "roles:{roleId}:version"  
Value: 2

Key: "user:{userId}:role:permissions:{roleId}:{version}"
Value: {
  "permissions": [
    {
      "module_id": 1,
      "module_code": "users",
      "permission_id": 1,
      "permission_code": "create",
      "allowed": true
    },
    {
      "module_id": 1,
      "module_code": "users",
      "permission_id": 2,
      "permission_code": "read",
      "allowed": true
    },
    ...
  ]
}


INVALIDACIÓN DE CACHÉ
═════════════════════

Cuando un Rol se actualiza:
  1. Evento: Role::saved()
  2. Llama: User::flushRolePermissionsCache($role->id)
  3. Incrementa: roles:{roleId}:version
  4. Redis detecta cambio
  5. Próxima solicitud recargar desde BD

Ejemplo:
  Initial: roles:2:version = 5
  └─> Alguien actualiza rol 2
  └─> incrementCacheVersion("roles:2:version")
  └─> roles:2:version = 6
  └─> Cache keys antiguas (con :5:) invalidan
  └─> Próxima solicitud genera nueva key con :6:
```

---

## 8. Relaciones entre Modelos (Eloquent)

```
USER MODEL
╔═══════════════════════════════════════════════════╗
║ class User extends Authenticatable                ║
║                                                   ║
║ Relaciones:                                       ║
║ ┌─────────────────────────────────────────────┐  ║
║ │ belongsTo('Role')                           │  ║
║ │                                             │  ║
║ │ $user->role     → Obtiene el rol del user   │  ║
║ └─────────────────────────────────────────────┘  ║
╚═══════════════════════════════════════════════════╝
         │
         │ 1:1 (BelongsTo)
         │
ROLE MODEL
╔═══════════════════════════════════════════════════╗
║ class Role extends Model                          ║
║                                                   ║
║ Relaciones:                                       ║
║ ┌─────────────────────────────────────────────┐  ║
║ │ hasMany('User')                             │  ║
║ │ → Un rol puede tener muchos usuarios        │  ║
║ │                                             │  ║
║ │ hasMany('RolePermission')                   │  ║
║ │ → Permisos del rol                          │  ║
║ │                                             │  ║
║ │ belongsToMany('Module')                     │  ║
║ │ → Módulos a los que accede                  │  ║
║ └─────────────────────────────────────────────┘  ║
└────────────────┬────────────────────────────────┘
                 │
    ┌────────────┴─────────────┬──────────────┐
    │                          │              │
1:Many           BelongsToMany        HasMany
    │                    │              │
    ▼                    ▼              ▼
 USERS            MODULES         ROLEPERMISSION
                      │                   │
                    Many:Many              │
                      │                    │
                  ┌───┴────────┐           │
                  │            │           │
           MODULES      PERMISSIONS◀──────┘
                            │
                        Many:Many
                            │
                           ROLES (back)
```

---

## 9. Flujo de Creación de Usuario

```
POST /api/v1/users
{
  "name": "John",
  "email": "john@example.com",
  "password": "password123",
  "role_id": 2,
  "status": "active"
}
│
├─▶ Middleware: auth:sanctum ✓
├─▶ Middleware: permission:users,create ✓
│
▼
UserController::store()
│
├─▶ $request->validate([...])
│   ├─ name: required, max 255
│   ├─ email: required, email, unique
│   ├─ password: required, min 8
│   ├─ role_id: nullable, exists:roles
│   └─ status: nullable, in:active,inactive
│
├─▶ Si validación falla
│   └─ return 422 Unprocessable Entity
│
├─▶ Si validación ok
│   ├─ Hash::make($data['password'])
│   ├─ User::create($data)
│   ├─ Load relación: ->load('role')
│   └─ return response()->json([...], 201)
│
▼
Respuesta 201 Created
{
  "status": true,
  "status_code": 201,
  "message": "Usuario creado correctamente.",
  "data": {
    "id": 10,
    "name": "John",
    "email": "john@example.com",
    "role_id": 2,
    "status": "active",
    "role": {
      "id": 2,
      "code": "editor",
      "name": "Editor"
    }
  }
}
```

---

## 10. Estados del Usuario y Transiciones

```
┌──────────────┐
│   ACTIVO     │ ◀─── Estado normal del usuario
│   status:    │      Puede hacer login y acciones
│   "active"   │
└──────┬───────┘
       │
       │ Cambio administrativo
       │ update({ status: 'inactive' })
       │
┌──────▼───────┐
│   INACTIVO   │ ◀─── Usuario desactivado
│   status:    │      NO puede hacer login
│   "inactive" │      Si tiene sesión activa,
└──────┬───────┘      middleware rechaza request
       │
       │ Cambio administrativo
       │ update({ status: 'active' })
       │
└─────────────▶ Vuelve a ACTIVO


VALIDACIÓN EN MIDDLEWARE
═════════════════════════

EnsureUserIsActive::handle()
│
├─ $user = $request->user()
├─ if ($user->status !== 'active')
│   └─ return 403 "Tu cuenta está desactivada"
└─ else
   └─ Continúa
```

---

## 11. Tokens API (Sanctum)

```
CICLO DE VIDA DEL TOKEN
═════════════════════════════════════════════════════════════

1️⃣ CREACIÓN (Login)
   ├─ $token = $user->createToken('api-token')
   ├─ Inserta en tabla: personal_access_tokens
   └─ Retorna: "5|abc123xyz..."
                ▲      ▲
                │      └─ Parte secreta (hasheable)
                └─ ID del token


2️⃣ ALMACENAMIENTO (Cliente)
   ├─ Cookie HTTP Only (seguro)
   │  └─ Protegido contra XSS
   └─ LocalStorage (alternativa, menos seguro)


3️⃣ ENVÍO (Cada Solicitud)
   ├─ Authorization: Bearer 5|abc123xyz...
   └─ Cookie: access_token=5|abc123xyz...


4️⃣ VALIDACIÓN (Servidor)
   ├─ Extrae token del header/cookie
   ├─ Busca en personal_access_tokens
   ├─ Obtiene user_id asociado
   ├─ Carga el usuario
   └─ Continúa o rechaza


5️⃣ EXPIRACIÓN
   ├─ Configurable en config/sanctum.php
   ├─ Default: 24*7 minutos (7 días)
   ├─ Usuario debe hacer login nuevamente
   └─ Token en DB se elimina


6️⃣ LOGOUT (Eliminar Token)
   ├─ DELETE FROM personal_access_tokens
   │         WHERE id = 5
   └─ Token queda inválido inmediatamente
```

---

## 12. Estructura de Respuestas HTTP

```
┌────────────────────────────────────────────────────┐
│ RESPUESTA EXITOSA (200 OK)                         │
├────────────────────────────────────────────────────┤
│ {                                                  │
│   "status": true,                                  │
│   "status_code": 200,                              │
│   "message": "Operación exitosa",                  │
│   "data": { ... }                                  │
│ }                                                  │
└────────────────────────────────────────────────────┘

┌────────────────────────────────────────────────────┐
│ RESPUESTA CREACIÓN (201 Created)                   │
├────────────────────────────────────────────────────┤
│ {                                                  │
│   "status": true,                                  │
│   "status_code": 201,                              │
│   "message": "Recurso creado",                     │
│   "data": { "id": 5, ... }                         │
│ }                                                  │
└────────────────────────────────────────────────────┘

┌────────────────────────────────────────────────────┐
│ ERROR DE VALIDACIÓN (422 Unprocessable Entity)     │
├────────────────────────────────────────────────────┤
│ {                                                  │
│   "message": "The given data was invalid.",        │
│   "errors": {                                      │
│     "email": ["El email ya existe"],               │
│     "password": ["Mín 8 caracteres"]               │
│   }                                                │
│ }                                                  │
└────────────────────────────────────────────────────┘

┌────────────────────────────────────────────────────┐
│ NO AUTENTICADO (401 Unauthorized)                  │
├────────────────────────────────────────────────────┤
│ {                                                  │
│   "message": "Unauthenticated."                    │
│ }                                                  │
└────────────────────────────────────────────────────┘

┌────────────────────────────────────────────────────┐
│ SIN PERMISOS (403 Forbidden)                       │
├────────────────────────────────────────────────────┤
│ {                                                  │
│   "status": false,                                 │
│   "status_code": 403,                              │
│   "message": "No tienes permiso para esta acción" │
│ }                                                  │
└────────────────────────────────────────────────────┘

┌────────────────────────────────────────────────────┐
│ NO ENCONTRADO (404 Not Found)                      │
├────────────────────────────────────────────────────┤
│ {                                                  │
│   "message": "No query results found"              │
│ }                                                  │
└────────────────────────────────────────────────────┘

┌────────────────────────────────────────────────────┐
│ ERROR SERVIDOR (500 Internal Server Error)         │
├────────────────────────────────────────────────────┤
│ {                                                  │
│   "message": "Error en la base de datos",          │
│   "debug": { ... } // Solo en desarrollo           │
│ }                                                  │
└────────────────────────────────────────────────────┘
```

---

## 13. Mapeo de Métodos HTTP a Operaciones CRUD

```
┌──────────┬───────────────────────────────────────────┐
│ MÉTODO   │ OPERACIÓN CRUD                            │
├──────────┼───────────────────────────────────────────┤
│ GET      │ READ - Obtener datos (idempotente)       │
│          │ GET /api/v1/users       → Listar todos    │
│          │ GET /api/v1/users/5     → Obtener 1       │
├──────────┼───────────────────────────────────────────┤
│ POST     │ CREATE - Crear nuevo recurso              │
│          │ POST /api/v1/users      → Crear usuario   │
│          │ POST /api/v1/roles      → Crear rol       │
├──────────┼───────────────────────────────────────────┤
│ PUT      │ UPDATE - Actualizar recursos completos    │
│          │ PUT /api/v1/users/5     → Actualizar      │
│          │ (Requiere todos los campos)               │
├──────────┼───────────────────────────────────────────┤
│ PATCH    │ PARTIAL UPDATE - Actualizar parcialmente  │
│          │ PATCH /api/v1/users/5   → Actualizar      │
│          │ (Solo los campos necesarios)              │
├──────────┼───────────────────────────────────────────┤
│ DELETE   │ DELETE - Eliminar recursos                │
│          │ DELETE /api/v1/users/5  → Eliminar        │
└──────────┴───────────────────────────────────────────┘
```

---

## 14. Comparación: Base de Datos vs Caché

```
╔════════════════════════════════════════════════════════════╗
║ CONSULTA DE PERMISOS (Performance)                         ║
╠════════════════════════════════════════════════════════════╣
║                                                            ║
║ SIN CACHÉ (Redis)                                          ║
║ ╔═════════════════════════════════════════════════════╗   ║
║ ║ SELECT p.* FROM role_permissions rp                ║   ║
║ ║ JOIN modules m ON m.id = rp.module_id              ║   ║
║ ║ JOIN permissions p ON p.id = rp.permission_id      ║   ║
║ ║ WHERE rp.role_id = 2                               ║   ║
║ ║                                                     ║   ║
║ ║ ⚠️ Lento (300-500ms)                               ║   ║
║ ║ 🔥 Múltiples consultas SQL                         ║   ║
║ ║ 💾 Carga base de datos                             ║   ║
║ ╚═════════════════════════════════════════════════════╝   ║
║                                                            ║
║ CON CACHÉ (Redis)                                          ║
║ ╔═════════════════════════════════════════════════════╗   ║
║ ║ GET "user:1:role:permissions:2:5"                  ║   ║
║ ║ from Redis Cache                                   ║   ║
║ ║                                                     ║   ║
║ ║ ✅ Rápido (1-5ms)                                  ║   ║
║ ║ ⚡ Una sola consulta de caché                      ║   ║
║ ║ 🎯 Respuesta instantánea                          ║   ║
║ ╚═════════════════════════════════════════════════════╝   ║
║                                                            ║
║ INVALIDACIÓN:                                              ║
║ Cuando alguien actualiza un Rol:                           ║
║ ├─ Role::update()                                          ║
║ ├─ Dispara evento: saved                                   ║
║ ├─ Llama: User::flushRolePermissionsCache()                ║
║ ├─ Incrementa version en Redis                            ║
║ └─ Próxima consulta refresca el caché                      ║
║                                                            ║
╚════════════════════════════════════════════════════════════╝
```

---

## 15. Árbol de Decisión: ¿Puedo hacer esta acción?

```
Usuario intenta acción
        │
        ▼
¿Usuario autenticado?
        │
    ┌───┴───┐
   NO      SÍ
    │       │
  401      ▼
 Error  ¿Token válido?
         │
     ┌───┴───┐
    NO      SÍ
     │       │
   401      ▼
  Error   ¿Usuario activo?
            │
        ┌───┴───┐
       NO      SÍ
        │       │
      403      ▼
     Error   ¿Tiene rol asignado?
              │
          ┌───┴───┐
         NO      SÍ
          │       │
        403      ▼
       Error   ¿Rol activo?
                │
            ┌───┴───┐
           NO      SÍ
            │       │
          403      ▼
         Error   ¿Módulo existe?
                  │
              ┌───┴───┐
             NO      SÍ
              │       │
            403      ▼
           Error   ¿Permiso existe?
                    │
                ┌───┴───┐
               NO      SÍ
                │       │
              403      ▼
             Error   ¿allowed = true?
                      │
                  ┌───┴───┐
                 NO      SÍ
                  │       │
                403      ✅
               Error   PERMITIDO
                      Ejecuta acción
```

---

## Resumen Rápido

```
┌─────────────────────────────────────────────────────────┐
│ COMPONENTES CLAVE                                       │
├─────────────────────────────────────────────────────────┤
│                                                         │
│ 📊 BD: MySQL (datos persistentes)                       │
│ ⚡ CACHÉ: Redis (permisos en memoria)                    │
│ 🔐 AUTH: Sanctum (tokens API)                           │
│ 🎛️ MIDDLEWARE: Valida auth, permisos, estado           │
│ 🎮 CONTROLLERS: Ejecutan lógica de negocio             │
│ 📦 MODELS: Representan tablas y relaciones             │
│ 🛣️ ROUTES: Definen endpoints                            │
│                                                         │
└─────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────┐
│ TABLA DE TABLAS                                         │
├─────────────────────────────────────────────────────────┤
│                                                         │
│ users             → Almacena usuarios                  │
│ roles             → Define roles disponibles           │
│ modules           → Define módulos del sistema         │
│ permissions       → Define permisos posibles           │
│ role_permissions  → Vincula todo junto                 │
│ personal_access   → Almacena tokens API               │
│ _tokens                                                │
│                                                         │
└─────────────────────────────────────────────────────────┘
```

