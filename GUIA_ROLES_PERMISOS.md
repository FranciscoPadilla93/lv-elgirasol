# Guía: Creación de Roles y Asignación de Permisos

## 📋 Conceptos Clave

### **Roles**
Son grupos de usuarios con los mismos permisos. Ejemplo: Admin, User, Developer.
- **Código**: Identificador único (ej: `admin`, `developer`)
- **Nombre**: Descripción legible (ej: "Administrador", "Desarrollador")
- **Estado**: Puede estar `active` o `inactive`

### **Módulos**
Son áreas del sistema sobre las que se controlan permisos. Ejemplo: Users, Roles, Permissions.
- **Código**: Identificador único (ej: `users`, `roles`)
- **Nombre**: Descripción (ej: "Usuarios", "Roles")

### **Permisos**
Son acciones específicas que un rol puede realizar en un módulo.
- **read**: Leer/Visualizar datos
- **create**: Crear nuevos registros
- **update**: Editar registros existentes
- **delete**: Eliminar registros
- **export**: Exportar datos
- **import**: Importar datos
- **assign_permissions**: Asignar permisos a otros roles

### **Relación: Role ↔ Permisos ↔ Módulos**
La tabla `role_permissions` conecta roles, módulos y permisos:
```
Role + Módulo + Permiso = Acceso
Ejemplo: Admin + Users + Delete = El Admin puede eliminar usuarios
```

---

## 🚀 **Procedimiento 1: Crear un Nuevo Rol**

### **Opción A: Mediante la API REST**

**Endpoint:**
```
POST /api/v1/roles
```

**Headers requeridos:**
```
Authorization: Bearer <tu_token>
Content-Type: application/json
```

**Body (ejemplo):**
```json
{
  "code": "manager",
  "name": "Manager",
  "status": "active"
}
```

**Respuesta exitosa (201):**
```json
{
  "status": true,
  "status_code": 201,
  "message": "Rol creado correctamente.",
  "data": {
    "id": 5,
    "code": "manager",
    "name": "Manager",
    "status": "active",
    "created_at": "2026-04-28T10:30:00Z",
    "updated_at": "2026-04-28T10:30:00Z"
  }
}
```

**Reglas de validación:**
- `code`: Requerido, único, solo letras minúsculas, números y guiones bajos (`[a-z0-9_]+`)
- `name`: Requerido, máximo 255 caracteres
- `status`: Opcional, puede ser "active" o "inactive" (por defecto "active")

---

### **Opción B: Mediante Tinker (PHP Interactive Shell)**

```bash
php artisan tinker
```

Luego en la consola de Tinker:
```php
App\Models\Role::create([
    'code' => 'manager',
    'name' => 'Manager',
    'status' => 'active'
]);
```

---

### **Opción C: Crear un Seeder personalizado**

Crea un archivo nuevo: `database/seeders/CustomRoleSeeder.php`

```php
<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class CustomRoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'manager' => 'Manager',
            'supervisor' => 'Supervisor',
            'guest' => 'Guest',
        ];

        foreach ($roles as $code => $name) {
            Role::withTrashed()->updateOrCreate(
                ['code' => $code],
                [
                    'name' => $name,
                    'status' => 'active',
                ],
            );
        }
    }
}
```

Luego ejecuta:
```bash
php artisan db:seed --class=CustomRoleSeeder
```

---

## 🔐 **Procedimiento 2: Asignar Permisos a Módulos**

### **Paso 1: Obtener los IDs necesarios**

Primero necesitas conocer los IDs de:
1. **Roles** disponibles
2. **Módulos** disponibles
3. **Permisos** disponibles

**Opción A: Mediante Tinker**
```bash
php artisan tinker
```

```php
# Ver todos los roles
App\Models\Role::all(['id', 'code', 'name']);

# Ver todos los módulos
App\Models\Module::all(['id', 'code', 'name']);

# Ver todos los permisos
App\Models\Permission::all(['id', 'code', 'name']);
```

**Ejemplo de salida:**
```
Roles:
id | code         | name
1  | super_admin  | Super Admin
2  | developer    | Developer
3  | admin        | Admin
4  | user         | User
5  | manager      | Manager

Módulos:
id | code        | name
1  | users       | Users
2  | roles       | Roles
3  | modules     | Modules
4  | permissions | Permissions

Permisos:
id | code               | name
1  | read               | Read
2  | create             | Create
3  | update             | Update
4  | delete             | Delete
5  | export             | Export
6  | import             | Import
7  | assign_permissions | Assign Permissions
```

---

### **Paso 2: Asignar Permisos mediante API**

**Endpoint:**
```
POST /api/v1/roles/{role_id}/permissions/sync
```

**Headers:**
```
Authorization: Bearer <tu_token>
Content-Type: application/json
```

**Body (ejemplo: Dar al rol "manager" permisos sobre "users"):**
```json
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
    },
    {
      "module_id": 1,
      "permission_id": 3,
      "allowed": true
    },
    {
      "module_id": 1,
      "permission_id": 4,
      "allowed": false
    }
  ]
}
```

**Explicación:**
- Manager módulo Users: puede `read`, `create`, `update` pero NO puede `delete`
- El `allowed: true/false` controla si el permiso está habilitado

**Respuesta exitosa:**
```json
{
  "status": true,
  "status_code": 200,
  "message": "Permisos del rol sincronizados correctamente.",
  "data": {
    "id": 5,
    "code": "manager",
    "name": "Manager",
    "status": "active",
    "role_permissions": [
      {
        "id": 1,
        "role_id": 5,
        "module_id": 1,
        "permission_id": 1,
        "allowed": true,
        "module": { "id": 1, "code": "users", "name": "Users" },
        "permission": { "id": 1, "code": "read", "name": "Read" }
      },
      ...
    ]
  }
}
```

---

### **Asignar Permisos Completos (Todos los módulos)**

Si quieres que el Manager tenga ciertos permisos en TODOS los módulos:

```json
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
    },
    {
      "module_id": 1,
      "permission_id": 3,
      "allowed": true
    },
    {
      "module_id": 2,
      "permission_id": 1,
      "allowed": true
    },
    {
      "module_id": 2,
      "permission_id": 2,
      "allowed": true
    },
    {
      "module_id": 2,
      "permission_id": 3,
      "allowed": true
    },
    {
      "module_id": 3,
      "permission_id": 1,
      "allowed": true
    },
    {
      "module_id": 3,
      "permission_id": 2,
      "allowed": true
    },
    {
      "module_id": 3,
      "permission_id": 3,
      "allowed": true
    },
    {
      "module_id": 4,
      "permission_id": 1,
      "allowed": true
    },
    {
      "module_id": 4,
      "permission_id": 2,
      "allowed": true
    },
    {
      "module_id": 4,
      "permission_id": 3,
      "allowed": true
    }
  ]
}
```

---

### **Mediante Seeder**

Crea `database/seeders/CustomRolePermissionSeeder.php`:

```php
<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\Permission;
use App\Models\Role;
use App\Models\RolePermission;
use Illuminate\Database\Seeder;

class CustomRolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Definir qué permisos tiene cada rol
        $permissionsByRole = [
            'manager' => [
                'users' => ['read', 'create', 'update'],  // read, create, update BUT NO delete
                'roles' => ['read'],                       // solo lectura
                'modules' => ['read'],
                'permissions' => ['read'],
            ],
            'supervisor' => [
                'users' => ['read'],
                'roles' => ['read'],
                'modules' => ['read'],
                'permissions' => ['read'],
            ],
        ];

        foreach ($permissionsByRole as $roleCode => $modulePermissions) {
            $role = Role::where('code', $roleCode)->first();

            if (!$role) {
                continue;
            }

            // Limpiar permisos existentes (opcional)
            $role->rolePermissions()->delete();

            // Crear nuevos permisos
            foreach ($modulePermissions as $moduleCode => $permissionCodes) {
                $module = Module::where('code', $moduleCode)->first();

                if (!$module) {
                    continue;
                }

                foreach ($permissionCodes as $permissionCode) {
                    $permission = Permission::where('code', $permissionCode)->first();

                    if (!$permission) {
                        continue;
                    }

                    RolePermission::updateOrCreate(
                        [
                            'role_id' => $role->id,
                            'module_id' => $module->id,
                            'permission_id' => $permission->id,
                        ],
                        ['allowed' => true],
                    );
                }
            }
        }
    }
}
```

Luego ejecuta:
```bash
php artisan db:seed --class=CustomRolePermissionSeeder
```

---

## 👤 **Procedimiento 3: Asignar Rol a un Usuario**

### **Mediante API**

**Endpoint:**
```
POST /api/v1/users/{user_id}/role
```

**Headers:**
```
Authorization: Bearer <tu_token>
Content-Type: application/json
```

**Body:**
```json
{
  "role_id": 5
}
```

**Respuesta exitosa:**
```json
{
  "status": true,
  "status_code": 200,
  "message": "Rol asignado correctamente.",
  "data": {
    "id": 1,
    "name": "Juan Pérez",
    "email": "juan@example.com",
    "role_id": 5,
    "role": {
      "id": 5,
      "code": "manager",
      "name": "Manager",
      "status": "active"
    }
  }
}
```

---

### **Mediante Tinker**

```bash
php artisan tinker
```

```php
$user = App\Models\User::find(1);
$user->update(['role_id' => 5]);
```

---

## 📊 **Procedimiento 4: Verificar Permisos**

### **Obtener todos los roles**

```bash
GET /api/v1/roles
```

**Con búsqueda:**
```bash
GET /api/v1/roles?search=manager&per_page=10
```

**Respuesta:**
```json
{
  "status": true,
  "status_code": 200,
  "message": "Roles obtenidos correctamente.",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 5,
        "code": "manager",
        "name": "Manager",
        "status": "active",
        "users_count": 2,
        "created_at": "2026-04-28T10:30:00Z"
      }
    ],
    "per_page": 15,
    "total": 1
  }
}
```

---

### **Ver los permisos de un rol en Tinker**

```bash
php artisan tinker
```

```php
$role = App\Models\Role::with('rolePermissions.module', 'rolePermissions.permission')->find(5);
$role->rolePermissions;

// Salida:
// [
//   { role_id: 5, module_id: 1, permission_id: 1, allowed: true, module: { code: 'users', name: 'Users' }, permission: { code: 'read', name: 'Read' } },
//   { role_id: 5, module_id: 1, permission_id: 2, allowed: true, module: { code: 'users', name: 'Users' }, permission: { code: 'create', name: 'Create' } },
//   ...
// ]
```

---

## 🔍 **Middleware de Permisos**

El sistema usa el middleware `permission:modulo,permiso` para proteger rutas.

**Ejemplo en rutas:**
```php
Route::delete('/users/{user}', [UserController::class, 'destroy'])
    ->middleware('permission:users,delete');  // Solo usuarios con permiso delete en módulo users
```

Esto significa:
- El usuario debe estar autenticado
- El usuario debe tener el rol asignado
- El rol debe tener permiso para `delete` en el módulo `users`

---

## 💡 **Ejemplos Prácticos**

### **Ejemplo 1: Crear rol "Content Manager"**

```bash
# 1. Crear el rol
curl -X POST http://localhost:8000/api/v1/roles \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "code": "content_manager",
    "name": "Content Manager",
    "status": "active"
  }'

# 2. Asignar permisos (ID del rol: 6, por ejemplo)
curl -X POST http://localhost:8000/api/v1/roles/6/permissions/sync \
  -H "Authorization: Bearer YOUR_TOKEN" \
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
      }
    ]
  }'

# 3. Asignar rol a usuario (ID del usuario: 2, por ejemplo)
curl -X POST http://localhost:8000/api/v1/users/2/role \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "role_id": 6
  }'
```

---

### **Ejemplo 2: Crear rol "Viewer" (Solo lectura)**

```php
// En Tinker
$role = App\Models\Role::create([
    'code' => 'viewer',
    'name' => 'Viewer',
    'status' => 'active'
]);

$modules = App\Models\Module::all();
$readPermission = App\Models\Permission::where('code', 'read')->first();

foreach ($modules as $module) {
    App\Models\RolePermission::create([
        'role_id' => $role->id,
        'module_id' => $module->id,
        'permission_id' => $readPermission->id,
        'allowed' => true
    ]);
}
```

---

## ⚠️ **Notas Importantes**

1. **Token de Autenticación**: Todas las peticiones API requieren un token válido de Sanctum en el header `Authorization`
2. **Permisos Requeridos**: Solo usuarios con permiso `assign_permissions` pueden crear roles o asignar permisos
3. **Soft Deletes**: Los roles se pueden "eliminar" suavemente, pero se pueden restaurar
4. **Caché**: Después de cambiar permisos, la caché se limpia automáticamente
5. **Estado del Rol**: Un rol inactivo no se puede asignar a usuarios

---

## 📝 **Resumen Rápido**

| Acción | Endpoint | Requisito |
|--------|----------|-----------|
| Crear rol | `POST /api/v1/roles` | Permisos de `roles,create` |
| Listar roles | `GET /api/v1/roles` | Permisos de `roles,read` |
| Asignar permisos | `POST /api/v1/roles/{id}/permissions/sync` | Permiso de `assign_permissions` |
| Asignar rol a usuario | `POST /api/v1/users/{id}/role` | Permiso de `assign_permissions` |

---

**¿Necesitas ayuda con algo específico? Consulta los ejemplos anteriores.**
