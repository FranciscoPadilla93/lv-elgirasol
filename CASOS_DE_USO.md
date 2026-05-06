# 💼 Casos de Uso Prácticos y Ejemplos

## 📌 Índice

1. [Caso 1: Sistema de Login](#caso-1-sistema-de-login)
2. [Caso 2: Crear y Asignar Usuario](#caso-2-crear-y-asignar-usuario)
3. [Caso 3: Gestionar Roles y Permisos](#caso-3-gestionar-roles-y-permisos)
4. [Caso 4: Control de Acceso](#caso-4-control-de-acceso)
5. [Caso 5: Auditoria y Troubleshooting](#caso-5-auditoria-y-troubleshooting)

---

## Caso 1: Sistema de Login

### Escenario Real
> "Un usuario nuevo entra a la aplicación por primera vez"

### Pasos Detallados

#### Paso 1: Usuario accede a login
```
Frontend (React, Vue, Angular, etc.)
├─ Mostrar formulario de login
│  ├─ Email: [ ]
│  └─ Password: [ ]
└─ Usuario ingresa credenciales
```

#### Paso 2: Frontend envía datos al backend
```javascript
// Ejemplo con JavaScript/Fetch
fetch('http://localhost:8000/api/v1/login', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    email: 'user@example.com',
    password: 'password'
  })
})
.then(response => response.json())
.then(data => {
  if (data.status) {
    // Guardar token en localStorage o usar cookie
    localStorage.setItem('token', data.access_token);
    // Redirigir a dashboard
    window.location.href = '/dashboard';
  } else {
    console.error(data.message);
  }
});
```

#### Paso 3: Backend procesa login
```php
// AuthController::login()

// 1. Validar datos
$validated = $request->validate([
    'email' => 'required|email',
    'password' => 'required|string'
]);

// 2. Buscar usuario
$user = User::where('email', $validated['email'])->first();

// 3. Validar contraseña
if (!$user || !Hash::check($validated['password'], $user->password)) {
    throw ValidationException::withMessages([
        'email' => ['Las credenciales son incorrectas.']
    ]);
}

// 4. Verificar que esté activo
if ($user->status !== 'active') {
    return response()->json([
        'status' => false,
        'message' => 'El usuario está inactivo.'
    ], 403);
}

// 5. Cargar permisos en caché
$user->getCachedRoleWithPermissions();

// 6. Crear token
$token = $user->createToken('api-token')->plainTextToken;

// 7. Retornar respuesta con cookie
return response()->json([
    'status' => true,
    'message' => 'Inicio de sesión exitoso.',
    'access_token' => $token
])->cookie(
    'access_token',
    $token,
    config('sanctum.expiration') ?? 60 * 24 * 7,
    null,
    null,
    true,  // secure (HTTPS)
    true,  // httpOnly (no acceso desde JS)
);
```

#### Paso 4: Frontend guarda token y accede al sistema
```javascript
// El token se guardó automáticamente en la cookie
// Próximas solicitudes lo enviarán automáticamente

// O si prefieres enviar en header:
fetch('http://localhost:8000/api/v1/users', {
  method: 'GET',
  headers: {
    'Authorization': `Bearer ${localStorage.getItem('token')}`
  }
})
```

### Flujo Completo Visual

```
┌──────────────────────────────────────────────────────────┐
│ CLIENTE (Navegador/App)                                  │
├──────────────────────────────────────────────────────────┤
│                                                          │
│ 1. Usuario ingresa credenciales                         │
│    └─ user@example.com / password                       │
│                                                          │
│ 2. Frontend envía JSON                                  │
│    └─ POST /api/v1/login                                │
│                                                          │
│ 4. Recibe token en response + cookie                    │
│    └─ { "access_token": "5|abc..." }                    │
│    └─ Cookie: access_token=5|abc...                     │
│                                                          │
│ 5. Guarda token (automático o localStorage)             │
│                                                          │
│ 6. Redirige a /dashboard                                │
│                                                          │
└──────────────────────────────────────────────────────────┘
                          │
        HTTP POST Request │
                          ▼
┌──────────────────────────────────────────────────────────┐
│ SERVIDOR (Laravel)                                       │
├──────────────────────────────────────────────────────────┤
│                                                          │
│ 3a. AuthController::login()                             │
│     ├─ Valida email y password                          │
│     ├─ Busca usuario en BD                              │
│     ├─ Verifica Hash                                    │
│     ├─ Comprueba status='active'                        │
│     ├─ Carga rol y permisos en caché                    │
│     └─ Crea token con Sanctum                           │
│                                                          │
│ 3b. Actualiza personal_access_tokens en BD              │
│                                                          │
│ 3c. Retorna token en respuesta                          │
│                                                          │
└──────────────────────────────────────────────────────────┘
```

---

## Caso 2: Crear y Asignar Usuario

### Escenario Real
> "Un administrador crea un nuevo usuario y le asigna un rol"

### Supuestos
- Admin está autenticado (tiene token válido)
- Admin tiene permiso `users:create` y `roles:assign_permissions`

### Paso 1: Obtener Roles Disponibles

```bash
# Listar roles existentes
curl -X GET http://localhost:8000/api/v1/roles \
  -H "Authorization: Bearer {TOKEN}"
```

**Respuesta:**
```json
{
    "status": true,
    "data": [
        {
            "id": 1,
            "code": "admin",
            "name": "Administrador",
            "status": "active"
        },
        {
            "id": 2,
            "code": "editor",
            "name": "Editor",
            "status": "active"
        },
        {
            "id": 3,
            "code": "viewer",
            "name": "Viewer",
            "status": "active"
        }
    ]
}
```

### Paso 2: Crear Nuevo Usuario

```bash
curl -X POST http://localhost:8000/api/v1/users \
  -H "Authorization: Bearer {TOKEN}" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Pedro García",
    "email": "pedro@empresa.com",
    "password": "SecurePass123!",
    "role_id": 2,
    "status": "active"
  }'
```

**Validaciones que se ejecutan:**
```php
// En UserController::store()
$request->validate([
    'name' => ['required', 'string', 'max:255'],
    'email' => ['required', 'email', 'unique:users,email'],
    'password' => ['required', 'string', 'min:8'],
    'role_id' => ['nullable', 'integer', 'exists:roles,id'],
    'status' => ['nullable', 'string', Rule::in(['active', 'inactive'])],
]);

// ✅ name = "Pedro García" ✓ (requerido, string)
// ✅ email = "pedro@empresa.com" ✓ (email válido, no existe)
// ✅ password = "SecurePass123!" ✓ (mín 8 caracteres)
// ✅ role_id = 2 ✓ (existe en tabla roles)
// ✅ status = "active" ✓ (valores válidos)
```

**Respuesta:**
```json
{
    "status": true,
    "status_code": 201,
    "message": "Usuario creado correctamente.",
    "data": {
        "id": 5,
        "name": "Pedro García",
        "email": "pedro@empresa.com",
        "role_id": 2,
        "status": "active",
        "role": {
            "id": 2,
            "code": "editor",
            "name": "Editor",
            "status": "active"
        }
    }
}
```

### Paso 3: Cambiar Rol si Necesario

```bash
# Si luego necesita cambiar el rol:
curl -X POST http://localhost:8000/api/v1/users/5/role \
  -H "Authorization: Bearer {TOKEN}" \
  -H "Content-Type: application/json" \
  -d '{
    "role_id": 3
  }'
```

**Backend:**
```php
// RoleController::assignToUser()
$user->update(['role_id' => $request->role_id]);

// Esto dispara:
// 1. Evento: Role::saved()
// 2. Hook: User::flushRolePermissionsCache($user->role_id)
// 3. Limpia caché Redis de permisos
// 4. Próxima solicitud refresca permisos
```

### Paso 4: Verificar Usuario en BD

```bash
php artisan tinker

# Ver usuario creado
>>> User::find(5)
=> App\Models\User {
     id: 5,
     name: "Pedro García",
     email: "pedro@empresa.com",
     role_id: 3,
     status: "active",
}

# Ver relación con rol
>>> User::find(5)->role
=> App\Models\Role {
     id: 3,
     code: "viewer",
     name: "Viewer",
}

# Ver permisos del rol
>>> User::find(5)->role->rolePermissions
=> Illuminate\Database\Eloquent\Collection {
     [...array de permisos...]
}
```

---

## Caso 3: Gestionar Roles y Permisos

### Escenario Real
> "El administrador crea un nuevo rol 'Manager' y le asigna permisos específicos"

### Paso 1: Crear Nuevo Rol

```bash
curl -X POST http://localhost:8000/api/v1/roles \
  -H "Authorization: Bearer {TOKEN}" \
  -H "Content-Type: application/json" \
  -d '{
    "code": "manager",
    "name": "Gerente",
    "status": "active"
  }'
```

**Respuesta:**
```json
{
    "status": true,
    "status_code": 201,
    "data": {
        "id": 4,
        "code": "manager",
        "name": "Gerente",
        "status": "active"
    }
}
```

### Paso 2: Obtener Módulos y Permisos

```bash
# Obtener módulos disponibles
php artisan tinker
>>> Module::all()
>>> Permission::all()
```

**Resultado esperado:**
```
MODULES:
  id=1, code=users,   name=Gestión de Usuarios
  id=2, code=roles,   name=Gestión de Roles
  id=3, code=modules, name=Gestión de Módulos

PERMISSIONS:
  id=1, code=create, name=Crear
  id=2, code=read,   name=Leer
  id=3, code=update, name=Actualizar
  id=4, code=delete, name=Eliminar
```

### Paso 3: Asignar Permisos al Rol

```bash
# El Manager puede: crear, leer y actualizar USUARIOS
# El Manager puede: leer ROLES (no crear ni eliminar)
# El Manager NO puede: acceder a MODULES

curl -X POST http://localhost:8000/api/v1/roles/4/permissions/sync \
  -H "Authorization: Bearer {TOKEN}" \
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
      },
      {
        "module_id": 1,
        "permission_id": 3,
        "allowed": true
      },
      {
        "module_id": 2,
        "permission_id": 2,
        "allowed": true
      }
    ]
  }'
```

### Paso 4: Verificar Permisos Asignados

```bash
php artisan tinker

# Ver el rol creado
>>> $role = Role::find(4)

# Ver sus permisos
>>> $role->rolePermissions()
   ->join('modules', 'role_permissions.module_id', '=', 'modules.id')
   ->join('permissions', 'role_permissions.permission_id', '=', 'permissions.id')
   ->select(['modules.code as module', 'permissions.code as permission', 'allowed'])
   ->get()

# Resultado:
=> [
  {module: "users", permission: "create", allowed: true},
  {module: "users", permission: "read",   allowed: true},
  {module: "users", permission: "update", allowed: true},
  {module: "roles", permission: "read",   allowed: true}
]
```

### Paso 5: Asignar Usuario al Nuevo Rol

```bash
# Crear usuario con rol manager
curl -X POST http://localhost:8000/api/v1/users \
  -H "Authorization: Bearer {TOKEN}" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Carlos López",
    "email": "carlos@empresa.com",
    "password": "SecurePass123!",
    "role_id": 4,
    "status": "active"
  }'
```

### Paso 6: Probar Permisos del Manager

```bash
# Login como Manager
curl -X POST http://localhost:8000/api/v1/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "carlos@empresa.com",
    "password": "SecurePass123!"
  }'

# Guardar token en variable
TOKEN="5|abc123..."

# ✅ Carlos PUEDE: Listar usuarios
curl -X GET http://localhost:8000/api/v1/users \
  -H "Authorization: Bearer $TOKEN"
# → 200 OK ✓

# ✅ Carlos PUEDE: Crear usuario
curl -X POST http://localhost:8000/api/v1/users \
  -H "Authorization: Bearer $TOKEN" \
  -d '{...}'
# → 201 Created ✓

# ❌ Carlos NO PUEDE: Eliminar usuario
curl -X DELETE http://localhost:8000/api/v1/users/2 \
  -H "Authorization: Bearer $TOKEN"
# → 403 Forbidden ✗

# ❌ Carlos NO PUEDE: Crear rol
curl -X POST http://localhost:8000/api/v1/roles \
  -H "Authorization: Bearer $TOKEN" \
  -d '{...}'
# → 403 Forbidden ✗

# ✅ Carlos PUEDE: Ver roles
curl -X GET http://localhost:8000/api/v1/roles \
  -H "Authorization: Bearer $TOKEN"
# → 200 OK ✓
```

---

## Caso 4: Control de Acceso

### Escenario: Validar Acceso a Recurso

```
Usuario intenta: DELETE /api/v1/users/10
                 Permiso requerido: users:delete
                            │
                            ▼
              ┌─────────────────────────┐
              │ CheckPermission         │
              │ middleware              │
              └────────┬────────────────┘
                       │
         ┌─────────────▼─────────────┐
         │ ¿Usuario autenticado?     │
         │ ($request->user())        │
         └────────┬─────────────────┘
                  │
         ┌────────┴─────────┐
        NO                  SÍ
         │                  │
       401              ┌───▼─────────┐
      Error            │ $user ->    │
                       │ hasPermission│
                       │('users',    │
                       │ 'delete')   │
                       └──┬──────────┘
                          │
                   ┌──────┴──────┐
                  SÍ              NO
                   │              │
             ┌─────▼──┐        ┌──▼──┐
             │Continúa│        │403  │
             │ DELETE │        │Error│
             └────────┘        └─────┘
```

### Implementación en UserController

```php
public function destroy(User $user): JsonResponse
{
    // El middleware ya validó que tiene permiso
    // Aquí podemos proceder con seguridad
    
    // Soft Delete (marca como eliminado)
    $user->delete();
    
    return response()->json([
        'status' => true,
        'status_code' => 200,
        'message' => 'Usuario eliminado correctamente.',
    ]);
}
```

### Casos de Permiso Denegado

**Caso 1: Usuario no autenticado**
```bash
curl -X DELETE http://localhost:8000/api/v1/users/10
# → 401 Unauthorized
# "message": "Unauthenticated."
```

**Caso 2: Usuario inactivo**
```bash
# User.status = 'inactive'
curl -X DELETE http://localhost:8000/api/v1/users/10 \
  -H "Authorization: Bearer $TOKEN"
# → 403 Forbidden
# "message": "Tu cuenta está desactivada."
```

**Caso 3: Rol sin permiso**
```bash
# Role manager NO tiene permiso users:delete
curl -X DELETE http://localhost:8000/api/v1/users/10 \
  -H "Authorization: Bearer $MANAGER_TOKEN"
# → 403 Forbidden
# "message": "No tienes permiso para realizar esta acción."
```

**Caso 4: Rol activo sin permiso**
```bash
# Role viewer tiene read pero no delete
curl -X DELETE http://localhost:8000/api/v1/users/10 \
  -H "Authorization: Bearer $VIEWER_TOKEN"
# → 403 Forbidden
```

### Validación de Permiso (Backend)

```php
// En User model
public function hasPermission(string $module, string $permission): bool
{
    $cacheKey = "user:{$this->id}:role:permissions:{$this->role_id}";
    
    try {
        // 1. Buscar en caché Redis
        $cached = Cache::store('redis')->get($cacheKey);
        
        if ($cached) {
            // 2. Si existe en caché, buscar en array
            foreach ($cached as $perm) {
                if ($perm['module'] === $module && 
                    $perm['permission'] === $permission) {
                    return $perm['allowed'];
                }
            }
            return false;
        }
        
        // 3. Si no está en caché, buscar en BD
        $exists = RolePermission::where('role_id', $this->role_id)
            ->whereHas('module', fn($q) => $q->where('code', $module))
            ->whereHas('permission', fn($q) => $q->where('code', $permission))
            ->where('allowed', true)
            ->exists();
        
        // 4. Guardar en caché para próximas consultas
        if ($exists) {
            Cache::store('redis')->forever($cacheKey, [...]);
        }
        
        return $exists;
        
    } catch (Throwable $e) {
        // Si Redis falla, consultar BD directamente
        return RolePermission::where('role_id', $this->role_id)
            ->where('allowed', true)
            ->exists();
    }
}
```

---

## Caso 5: Auditoria y Troubleshooting

### Escenario: Usuario Dice "No puedo acceder a X"

### Diagnóstico Paso a Paso

**Paso 1: Verificar que el usuario exista y esté activo**
```bash
php artisan tinker

>>> $user = User::where('email', 'usuario@empresa.com')->first()
=> App\Models\User {
     id: 5,
     name: "Pedro García",
     email: "usuario@empresa.com",
     status: "active",  ✅
     role_id: 3,
}
```

**Paso 2: Verificar que tenga rol asignado**
```bash
>>> $user->role_id
=> 3

>>> $role = $user->role
=> App\Models\Role {
     id: 3,
     code: "editor",
     name: "Editor",
     status: "active"  ✅
}
```

**Paso 3: Verificar permisos del rol**
```bash
>>> $user->hasPermission('users', 'read')
=> true  ✅

>>> $user->hasPermission('users', 'delete')
=> false  ❌

>>> $user->hasPermission('roles', 'create')
=> false  ❌
```

**Paso 4: Ver permisos completos del rol**
```bash
>>> $role->rolePermissions()
   ->with(['module', 'permission'])
   ->get()

=> [
  RolePermission {
    role_id: 3,
    module: { code: "users", name: "Gestión de Usuarios" },
    permission: { code: "read", name: "Leer" },
    allowed: true
  },
  RolePermission {
    role_id: 3,
    module: { code: "users", name: "Gestión de Usuarios" },
    permission: { code: "create", name: "Crear" },
    allowed: true
  },
  RolePermission {
    role_id: 3,
    module: { code: "users", name: "Gestión de Usuarios" },
    permission: { code: "update", name: "Actualizar" },
    allowed: true
  }
]
```

**Paso 5: Conclusión**

El usuario tiene:
- ✅ Cuenta activa
- ✅ Rol válido (editor)
- ✅ Permisos para READ, CREATE, UPDATE en usuarios
- ❌ NO tiene permisos para DELETE en usuarios

**Acción:** Si necesita DELETE, cambiar el rol o asignar ese permiso.

### Solucionar Problemas Comunes

#### Problema 1: "Error 401 - No autenticado"

**Causas posibles:**
```bash
# 1. Token expirado
php artisan tinker
>>> $token = PersonalAccessToken::where('token', 'abc...')->first()
# Si retorna null → Token no existe o fue eliminado

# 2. Token mal formado
# Verificar que Authorization header sea:
# Authorization: Bearer {token_completo}

# 3. Token de otro usuario
# Cada token solo funciona para su usuario
```

**Solución:**
```bash
# Hacer login nuevamente
curl -X POST http://localhost:8000/api/v1/login \
  -d '{"email":"user@ex.com","password":"pass"}'
```

#### Problema 2: "Error 403 - Sin permisos"

```bash
php artisan tinker

# 1. Verificar rol
>>> User::find(5)->role
# Si es null → Usuario no tiene rol

# 2. Verificar permisos
>>> User::find(5)->hasPermission('users', 'delete')
# Si false → Falta asignar permisos

# Solución: Asignar permiso
>>> RolePermission::create([
    'role_id' => 3,
    'module_id' => 1,
    'permission_id' => 4,  // delete
    'allowed' => true
])

# 3. Limpiar caché para que se refleje
>>> User::flushRolePermissionsCache(3)
```

#### Problema 3: "Usuario desactivado pero sigue accediendo"

```bash
php artisan tinker

# Verificar status
>>> User::find(5)->status
=> "inactive"

# Problema: Token aún es válido
# Solución: Eliminar tokens activos del usuario

>>> $user = User::find(5)
>>> $user->tokens()->delete()
# Ahora el usuario NO puede usar sus tokens viejos

# En próximo login con status inactivo:
# → será rechazado en AuthController::login()
```

#### Problema 4: "Caché no se actualiza después de cambiar permisos"

```bash
php artisan tinker

# Limpiar caché completamente
>>> Cache::store('redis')->flush()

# O solo limpiar permisos de un rol
>>> User::flushRolePermissionsCache(3)

# O resetear todo
>>> redis-cli FLUSHALL
```

### Logs para Debugging

```bash
# Ver errores en logs
tail -f storage/logs/laravel.log

# Buscar errores de permiso
grep -i "permission" storage/logs/laravel.log

# Ver solicitudes en desarrollo
# En .env: APP_DEBUG=true
# Verás stack trace completo en 500 errors
```

---

## 📊 Tabla de Referencia: Acciones por Rol

```
┌───────────────┬────────┬──────┬────────┬────────┬──────────┐
│ Rol/Acción    │ Listar │ Ver  │ Crear  │Actualz │ Eliminar │
├───────────────┼────────┼──────┼────────┼────────┼──────────┤
│ ADMIN         │        │      │        │        │          │
│ - Users       │   ✅   │  ✅  │   ✅   │   ✅   │    ✅    │
│ - Roles       │   ✅   │  ✅  │   ✅   │   ✅   │    ✅    │
│ - Modules     │   ✅   │  ✅  │   ✅   │   ✅   │    ✅    │
├───────────────┼────────┼──────┼────────┼────────┼──────────┤
│ EDITOR        │        │      │        │        │          │
│ - Users       │   ✅   │  ✅  │   ✅   │   ✅   │    ❌    │
│ - Roles       │   ✅   │  ✅  │   ❌   │   ❌   │    ❌    │
│ - Modules     │   ✅   │  ✅  │   ❌   │   ❌   │    ❌    │
├───────────────┼────────┼──────┼────────┼────────┼──────────┤
│ VIEWER        │        │      │        │        │          │
│ - Users       │   ✅   │  ✅  │   ❌   │   ❌   │    ❌    │
│ - Roles       │   ✅   │  ✅  │   ❌   │   ❌   │    ❌    │
│ - Modules     │   ✅   │  ✅  │   ❌   │   ❌   │    ❌    │
└───────────────┴────────┴──────┴────────┴────────┴──────────┘
```

---

## 🚀 Checklist de Implementación

Cuando implementes cambios en roles/permisos:

- [ ] ¿Creaste el rol nuevo?
- [ ] ¿Asignaste los permisos al rol?
- [ ] ¿Limpiaste el caché?
- [ ] ¿Asignaste usuarios al rol?
- [ ] ¿Probaste los permisos?
- [ ] ¿Verificaste que funciona en prod?
- [ ] ¿Documentaste los cambios?

