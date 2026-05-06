# 🚀 GUÍA RÁPIDA - Cómo Poner el Sistema en Funcionamiento

## ⚡ Inicio Rápido (5 minutos)

### 1. Preparar Entorno

```bash

composer install
npm install


cp .env.example .env


php artisan key:generate
```

### 2. Configurar Base de Datos

**Editar `.env`:**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravelcore
DB_USERNAME=root
DB_PASSWORD=
```

**Crear BD (si no existe):**
```bash
mysql -u root -p
> CREATE DATABASE laravelcore;
> EXIT;
```

### 3. Ejecutar Migraciones y Seeds

```bash
# Crear tablas
php artisan migrate

# Cargar datos iniciales
php artisan db:seed
```

### 4. Iniciar Servidor

**Terminal 1:**
```bash
php artisan serve
# http://localhost:8000
```

**Terminal 2:**
```bash
npm run dev
```

---

## 🔑 Usuarios por Defecto (Después del seed)

| Email | Contraseña | Rol | Permisos |
|-------|-----------|-----|----------|
| `user@example.com` | `password` | admin | Todos |
| `editor@example.com` | `password` | editor | Create, Read, Update |
| `viewer@example.com` | `password` | viewer | Read |

---

## 🧪 Pruebas Rápidas

### Con cURL

#### 1. Login

```bash
curl -X POST http://localhost:8000/api/v1/login \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","password":"password"}'
```

**Respuesta:**
```json
{
    "status": true,
    "status_code": 200,
    "message": "Inicio de sesion exitoso.",
    "access_token": "5|abc123xyz..."
}
```

👉 **Guarda este token para usar en los próximos requests**

#### 2. Listar Usuarios

```bash
curl -X GET "http://localhost:8000/api/v1/users" \
  -H "Authorization: Bearer 5|abc123xyz..."
```

#### 3. Crear Usuario

```bash
curl -X POST http://localhost:8000/api/v1/users \
  -H "Authorization: Bearer 5|abc123xyz..." \
  -H "Content-Type: application/json" \
  -d '{
    "name":"Juan García",
    "email":"juan@example.com",
    "password":"Pass123456",
    "role_id":2,
    "status":"active"
  }'
```

#### 4. Actualizar Usuario

```bash
curl -X PUT http://localhost:8000/api/v1/users/2 \
  -H "Authorization: Bearer 5|abc123xyz..." \
  -H "Content-Type: application/json" \
  -d '{
    "name":"Juan García Actualizado",
    "status":"active"
  }'
```

#### 5. Eliminar Usuario

```bash
curl -X DELETE http://localhost:8000/api/v1/users/2 \
  -H "Authorization: Bearer 5|abc123xyz..."
```

#### 6. Listar Roles

```bash
curl -X GET http://localhost:8000/api/v1/roles \
  -H "Authorization: Bearer 5|abc123xyz..."
```

#### 7. Asignar Rol a Usuario

```bash
curl -X POST http://localhost:8000/api/v1/users/3/role \
  -H "Authorization: Bearer 5|abc123xyz..." \
  -H "Content-Type: application/json" \
  -d '{"role_id":2}'
```

#### 8. Logout

```bash
curl -X POST http://localhost:8000/api/v1/logout \
  -H "Authorization: Bearer 5|abc123xyz..."
```

---

### Con Postman

1. **Importar colección** (Crear en Postman):

   - **New → API Request**
   - **Method:** POST
   - **URL:** `http://localhost:8000/api/v1/login`
   - **Body (JSON):**
     ```json
     {
       "email": "user@example.com",
       "password": "password"
     }
     ```
   - **Send**
   - Copiar `access_token` de la respuesta

2. **Para rutas protegidas:**
   - **Auth → Type → Bearer Token**
   - **Token:** Pega el token copiado
   - **Send**

---

## 🗄️ Entender la Base de Datos

### Ver estructura actual

```bash
php artisan tinker
```

```php
# Ver todos los usuarios
>>> User::all()

# Ver todos los roles
>>> Role::all()

# Ver todos los módulos
>>> Module::all()

# Ver todos los permisos
>>> Permission::all()

# Ver relaciones de un usuario
>>> User::find(1)->with(['role', 'role.rolePermissions'])->first()

# Ver permisos de un rol
>>> Role::find(1)->rolePermissions()->get()

# Crear usuario nuevo
>>> User::create([
    'name' => 'Pedro',
    'email' => 'pedro@test.com',
    'password' => Hash::make('12345678'),
    'role_id' => 2,
    'status' => 'active'
])

# Limpiar caché de permisos
>>> User::flushRolePermissionsCache()

# Salir
>>> exit
```

---

## 🔧 Tareas Comunes

### Crear un nuevo Módulo

**Opción 1: Por API**
```bash
curl -X POST http://localhost:8000/api/v1/modules \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "code":"reportes",
    "name":"Gestión de Reportes",
    "status":"active"
  }'
```

**Opción 2: Por Tinker**
```bash
php artisan tinker
>>> Module::create([
    'code' => 'reportes',
    'name' => 'Gestión de Reportes',
    'status' => 'active'
])
```

### Crear un nuevo Rol

```bash
php artisan tinker
>>> Role::create([
    'code' => 'manager',
    'name' => 'Gerente',
    'status' => 'active'
])
```

### Asignar Permisos a un Rol

```bash
# Primero obten los IDs:
# role_id = 3 (manager)
# module_id = 1 (users)
# permission_id = 1 (create)

curl -X POST http://localhost:8000/api/v1/roles/3/permissions/sync \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "permissions": [
      {"module_id": 1, "permission_id": 1, "allowed": true},
      {"module_id": 1, "permission_id": 2, "allowed": true},
      {"module_id": 1, "permission_id": 3, "allowed": true},
      {"module_id": 1, "permission_id": 4, "allowed": true}
    ]
  }'
```

---

## 🐛 Troubleshooting

### Error: "SQLSTATE[HY000]: General error: 1030 Got error..."

**Solución:**
```bash
php artisan cache:clear
php artisan config:clear
php artisan migrate:refresh --seed
```

### Error: "No tienes permiso para realizar esta accion (403)"

**Causas posibles:**
1. El usuario no tiene el rol asignado
2. El rol no tiene el permiso para ese módulo
3. El permiso no existe

**Solución:**
```bash
# Ver permisos del usuario
php artisan tinker
>>> $user = User::find(1)
>>> $user->hasPermission('users', 'read')
# Debe retornar true/false

# Si retorna false, asigna permiso:
>>> $role = $user->role
>>> RolePermission::create([
    'role_id' => $role->id,
    'module_id' => 1,
    'permission_id' => 1,
    'allowed' => true
])
```

### Error: "Token invalido" (401)

**Soluciones:**
1. El token expiró - hacer login nuevamente
2. El token está mal - verificar que esté completo
3. El usuario se inactivó - reactivar usuario

```bash
php artisan tinker
>>> User::find(1)->update(['status' => 'active'])
```

### El caché no se actualiza

```bash
# Limpiar todo el caché
php artisan cache:clear

# Limpiar caché de permisos específicamente
php artisan tinker
>>> User::flushRolePermissionsCache()
```

---

## 📊 Diagrama de Flujo (Resumido)

```
┌─────────────────┐
│   LOGIN         │
│ email/password  │
└────────┬────────┘
         │
         ▼
┌─────────────────────────┐
│ AuthController::login() │
│ ✓ Valida credenciales   │
│ ✓ Crea token API        │
│ ✓ Carga permisos caché  │
└────────┬────────────────┘
         │
         ▼
┌─────────────────────────┐
│ Retorna TOKEN (cookie)  │
└────────┬────────────────┘
         │
         │ Cliente almacena token
         │
         ▼
┌─────────────────────────────────┐
│  Cliente hace solicitud con      │
│  Authorization: Bearer {token}   │
└────────┬────────────────────────┘
         │
         ▼
┌────────────────────────┐
│  Middleware valida:    │
│  ✓ Token válido        │
│  ✓ Usuario activo      │
│  ✓ Tiene permisos      │
└────────┬───────────────┘
         │
         ▼
┌────────────────────────┐
│  Controlador ejecuta   │
│  lógica de negocio     │
│  (CRUD en BD)          │
└────────┬───────────────┘
         │
         ▼
┌────────────────────────┐
│  Retorna respuesta     │
│  JSON con datos        │
└────────────────────────┘
```

---

## 📝 Estructura de Respuestas

### Respuesta Exitosa
```json
{
    "status": true,
    "status_code": 200,
    "message": "Operacion exitosa",
    "data": { ... }
}
```

### Respuesta con Error de Validación
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "email": ["El email ya existe"],
        "password": ["La contraseña debe tener mínimo 8 caracteres"]
    }
}
```

### Respuesta sin Autenticación
```json
{
    "message": "Unauthenticated."
}
```

### Respuesta sin Permisos
```json
{
    "status": false,
    "status_code": 403,
    "message": "No tienes permiso para realizar esta accion."
}
```

---

## 🚢 Deployment (Producción)

```bash
# 1. Compilar assets
npm run build

# 2. Optimizar código
php artisan optimize

# 3. Cachear rutas
php artisan route:cache

# 4. Cachear configuración
php artisan config:cache

# 5. Verificar todo está correcto
php artisan migrate --force
php artisan serve
```

---

## 📚 Comandos Artisan Útiles

```bash
# Ver todas las rutas
php artisan route:list

# Ver base de datos
php artisan db:show

# Limpiar caché
php artisan cache:clear

# Resetear BD (⚠️ elimina todo)
php artisan migrate:reset
php artisan migrate --seed

# Ver estructura de tabla
php artisan migrate:status

# Crear nueva migración
php artisan make:migration create_tabla_name

# Crear modelo con todo
php artisan make:model NombreModelo -mcr
```

---

## 🎓 Próximos Pasos

1. ✅ Entender el sistema actual (base de datos, modelos, controladores)
2. ✅ Crear nuevos módulos si es necesario
3. ✅ Definir roles y permisos según tu necesidad
4. ✅ Conectar con frontend
5. ✅ Agregar validaciones adicionales
6. ✅ Implementar logs y monitoreo
7. ✅ Hacer deploy a producción

---

## 📞 Documentación Completa

Revisa `ARQUITECTURA_SISTEMA.md` para documentación detallada de:
- Cada modelo y sus relaciones
- Cada controlador y su lógica
- Middleware y cómo funcionan
- Flujos completos del sistema
- Ejemplos de uso

