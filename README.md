# Legendario Manager

Aplicacion Laravel para administrar usuarios, departamentos, actividades, asistencia, finanzas, reconocimientos y registro publico de senderistas mediante tokens QR.

## Requisitos

- PHP 8.4 o superior.
- Composer compatible con PHP 8.4.
- Node.js y npm.
- SQLite para desarrollo local, o una base compatible configurada en `.env`.

## Instalacion local

```bash
cp .env.example .env
composer install
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
npm install
npm run build
```

En Herd, el sitio esperado por defecto es:

```text
http://legendarios-manager.test
```

Usuario inicial creado por el seeder:

```text
Email: admin@example.com
Password: password
Rol: Administrador
```

## Desarrollo

```bash
php artisan serve
npm run dev
```

Tambien existe el script combinado:

```bash
composer run dev
```

## Pruebas

```bash
php artisan test
```

Ultima validacion local conocida:

```text
40 tests passed, 132 assertions
```

## Roles

Roles base creados por `RolesAndPermissionsSeeder`:

- Administrador
- Supervisor
- Encargado
- Lider
- Finanzas
- Servidor
- Senderista

Resumen de acceso:

- `Administrador`: control completo del panel.
- `Supervisor`: dashboard, usuarios en modo lectura, departamentos, actividades y finanzas en lectura.
- `Encargado` y `Lider`: asistencia de su departamento y movimientos financieros autorizados.
- `Finanzas`: vista de finanzas del departamento asignado.
- `Senderista`: rol asignado al registro publico por QR.

## Modulos implementados

- Autenticacion y perfil de usuario.
- Panel administrativo responsive.
- Usuarios y accesos.
- Departamentos.
- Actividades globales.
- Asistencia por departamento.
- Finanzas por departamento.
- Reconocimientos por cumpleanos y motivos personalizados.
- Campanas QR y tokens QR administrables desde el panel.
- Tribus por campana QR, con integrantes asignados desde los inscritos historicos.
- Registro publico de senderistas mediante token.

## Registro QR

Desde el panel administrativo se pueden crear campanas QR y tokens en:

```text
Admin > QR > Campanas QR
Admin > QR > Tokens QR
```

Tambien se puede generar una URL publica de registro de senderista por consola:

```bash
php artisan senderista:token
php artisan senderista:token --expires=1440
```

El comando imprime la URL final y el token crudo para convertirlo en QR.

## Produccion

- Configurar `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL` y `APP_TIMEZONE=America/Santiago`.
- Configurar mail real en `.env`.
- Ejecutar `php artisan storage:link` si se usan imagenes publicas.
- Definir si SQLite seguira siendo la base final o migrar a MySQL/PostgreSQL.
- Cambiar la contrasena del usuario inicial apenas se despliegue.

## Problemas conocidos del entorno actual

- `rg` aparece en el entorno de Windows pero devuelve acceso denegado, por lo que las busquedas locales se hicieron con PowerShell.
- `git` no esta disponible en el `PATH` del shell usado por Codex.
