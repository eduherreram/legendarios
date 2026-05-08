# Funcionamiento del Proyecto por Rol

## 1. Resumen general

Legendario Manager es una aplicacion web construida en Laravel para administrar la operacion interna de una organizacion Legendarios. El sistema centraliza usuarios, departamentos, actividades, asistencia, pagos, finanzas, reconocimientos y registros publicos mediante enlaces QR.

La aplicacion usa autenticacion de Laravel Breeze y control de roles con `spatie/laravel-permission`. Los accesos principales se definen en `routes/web.php`, mientras que las reglas finas por recurso se aplican en policies como `DepartamentoPolicy`, `DepartamentoActividadPolicy`, `AsistenciaPolicy` y `FinanzasMovimientoPolicy`.

## 2. Roles del sistema

Los roles base se crean desde `database/seeders/RolesAndPermissionsSeeder.php`:

| Rol | Proposito general |
| --- | --- |
| Administrador | Control completo del panel y de la configuracion operativa. |
| Supervisor | Gestion operativa amplia: usuarios, departamentos, actividades, finanzas y reportes. |
| Encargado | Gestion de asistencia y seguimiento de actividades de su departamento. |
| Lider | Vista y gestion acotada a su departamento, especialmente usuarios y asistencia. |
| Finanzas | Control de movimientos financieros y balances. |
| Servidor | Usuario interno activo que participa en departamentos y actividades. |
| Senderista | Usuario registrado desde QR, aun no convertido a servidor interno. |

Nota tecnica: en algunos archivos el rol Lider puede verse con problemas de codificacion en consola, pero funcionalmente representa el rol "Lider".

## 3. Vista por rol

### Administrador

El Administrador es el rol con mayor alcance. Puede entrar al dashboard administrativo, administrar usuarios, administrar accesos, crear y modificar departamentos, crear actividades globales, gestionar asistencia, revisar y registrar finanzas, enviar reconocimientos, crear campanas QR y administrar tokens QR.

Funciones principales:

- Ver metricas generales del sistema en `/admin/dashboard`.
- Crear, editar, eliminar y convertir usuarios.
- Cambiar contrasenas desde el modulo de accesos.
- Administrar departamentos y responsables.
- Crear actividades globales.
- Revisar asistencia por departamento.
- Registrar pagos y movimientos financieros.
- Crear reconocimientos personalizados o de cumpleanos.
- Crear campanas QR, tokens QR y tribus.
- Convertir un Senderista a Servidor asignandole numero legendario y departamento.

Flujo tipico:

1. Crea departamentos y asigna responsables.
2. Crea usuarios o revisa senderistas registrados por QR.
3. Crea actividades globales.
4. Supervisa asistencia y pagos.
5. Revisa finanzas y reconocimientos.

### Supervisor

El Supervisor opera gran parte del sistema, con permisos amplios sobre usuarios, departamentos, actividades y finanzas. En el codigo, muchas rutas de gestion aceptan `Administrador|Supervisor`, y las policies le permiten ver y actuar sobre departamentos, actividades, asistencia y finanzas.

Funciones principales:

- Acceder al dashboard administrativo.
- Crear, editar y eliminar usuarios, excepto que desde la vista de roles se evita asignar Administrador si el usuario autenticado solo es Supervisor.
- Crear, editar y eliminar departamentos.
- Crear actividades globales.
- Revisar y actualizar asistencia.
- Registrar movimientos financieros.
- Enviar reconocimientos y felicitaciones de cumpleanos.
- Gestionar campanas QR, tokens y tribus.

Flujo tipico:

1. Mantiene actualizados usuarios y departamentos.
2. Programa actividades para todos los departamentos.
3. Supervisa compromisos, asistencia y pagos.
4. Revisa balances financieros por departamento.

### Encargado

El Encargado trabaja principalmente sobre su departamento. Su acceso fuerte esta en las actividades asignadas a su departamento y en la marcacion de asistencia.

Funciones principales:

- Ver sus actividades en `/admin/actividades/mis`.
- Entrar a la asistencia de una actividad de su departamento.
- Marcar usuarios como presentes o ausentes.
- Registrar pago total, abono o freepass al marcar asistencia.
- Ver departamentos cuando la policy lo autoriza por relacion con el departamento.

Limites importantes:

- No crea actividades globales.
- No administra usuarios desde las rutas de creacion o edicion.
- No tiene acceso general al modulo de finanzas segun las rutas actuales.
- Su alcance depende de `departamento_id` o de estar asignado como encargado del departamento.

Flujo tipico:

1. Ingresa a "Mis actividades".
2. Abre la actividad de su departamento.
3. Marca asistencia.
4. Registra pagos asociados a la asistencia cuando corresponde.

### Lider

El Lider tiene una vista acotada a su departamento. Puede consultar usuarios y departamentos, pero el controlador filtra usuarios y departamentos por `departamento_id` cuando el usuario autenticado tiene rol Lider.

Funciones principales:

- Ver usuarios de su propio departamento.
- Ver detalle de usuarios de su departamento.
- Ver su departamento.
- Ver sus actividades.
- Actualizar asistencia de actividades de su departamento.

Limites importantes:

- No crea ni edita usuarios desde las rutas protegidas para Administrador o Supervisor.
- No crea actividades globales.
- No tiene acceso general a finanzas.
- Si intenta ver usuarios de otro departamento, el sistema responde con acceso denegado.

Flujo tipico:

1. Revisa los usuarios de su departamento.
2. Consulta actividades asignadas.
3. Gestiona asistencia y seguimiento de su equipo.

### Finanzas

El rol Finanzas esta enfocado en revisar y registrar movimientos economicos. La policy `FinanzasMovimientoPolicy` le da acceso completo al recurso financiero mediante el metodo `before`.

Funciones principales:

- Ver el modulo `/admin/finanzas`.
- Filtrar movimientos por departamento y mes.
- Ver balance por departamento.
- Crear ingresos y egresos.
- Eliminar movimientos financieros.
- Ver departamentos, ya que la policy de departamentos tambien permite Finanzas.

Limites importantes:

- No administra usuarios.
- No crea actividades globales.
- No administra QR ni reconocimientos.
- Su objetivo es financiero, no operativo general.

Flujo tipico:

1. Entra al modulo de finanzas.
2. Filtra por departamento o mes.
3. Registra ingresos o egresos.
4. Revisa disponible, ingresos y egresos.

### Servidor

El Servidor representa a una persona interna activa de la organizacion. Puede quedar asociado a un departamento, tener numero legendario, participar en actividades y ser marcado en asistencia.

Funciones principales:

- Participar en actividades mediante el registro publico de actividad usando su numero legendario.
- Aparecer en listados de asistencia.
- Ser considerado en pagos por actividad.
- Recibir reconocimientos por email o WhatsApp si es seleccionado.

Limites importantes:

- No tiene acceso administrativo por defecto.
- Su participacion en actividades depende de estar activo, tener numero legendario y tener departamento asignado.

Flujo tipico:

1. Recibe enlace publico de compromiso de actividad.
2. Ingresa su numero legendario.
3. El sistema crea o confirma su compromiso y deja su asistencia inicialmente como ausente.
4. El Encargado, Lider, Supervisor o Administrador marca la asistencia final.

### Senderista

El Senderista es un usuario creado desde un token QR publico. Se registra con sus datos personales, contacto y contacto de emergencia. Al completar el formulario queda como usuario activo con rol Senderista, pero sin numero legendario ni departamento.

Funciones principales:

- Registrarse desde `/registro/senderista/{token}`.
- Quedar asociado al token QR usado.
- Quedar asociado a una campana QR si el token pertenece a una campana.
- Ser asignado a una tribu dentro de una campana QR.
- Ser convertido posteriormente a Servidor por Administrador o Supervisor.

Limites importantes:

- El token QR solo puede usarse una vez.
- El token puede expirar.
- Si la campana QR esta inactiva, el registro se bloquea.
- El Senderista no administra modulos internos.

Flujo tipico:

1. Escanea un QR o abre un enlace de registro.
2. Completa el formulario publico.
3. El sistema crea el usuario con rol Senderista.
4. El token queda marcado como usado.
5. Mas adelante, un Administrador o Supervisor puede convertirlo a Servidor.

## 4. Modulos principales

### Autenticacion y perfil

Laravel Breeze gestiona login, registro, recuperacion de contrasena, verificacion de email y perfil. Las rutas protegidas requieren usuario autenticado y, para el panel, usuario verificado.

Archivos clave:

- `routes/auth.php`
- `app/Http/Controllers/Auth/*`
- `resources/views/auth/*`
- `resources/views/profile/*`

### Dashboard administrativo

El dashboard resume:

- Total de departamentos.
- Usuarios activos e inactivos.
- Balance global.
- Participacion por actividad.
- Totales de asistencia.
- Balance por departamento.
- Ultimos movimientos financieros.

Archivo clave:

- `app/Http/Controllers/Admin/DashboardController.php`

### Usuarios y accesos

El modulo de usuarios permite crear, editar, eliminar, ver detalle y convertir Senderistas en Servidores. La conversion asigna numero legendario, departamento y cambia el rol a Servidor.

El modulo de accesos permite al Administrador filtrar usuarios por rol y cambiar contrasenas.

Archivos clave:

- `app/Http/Controllers/Admin/UserController.php`
- `app/Services/UserService.php`
- `app/Http/Controllers/Admin/AccesosController.php`

### Departamentos

Los departamentos agrupan usuarios y tienen responsables: supervisor, lider y encargado. Las policies permiten que usuarios relacionados con un departamento puedan verlo, mientras Administrador, Supervisor y Finanzas tienen acceso amplio.

Archivos clave:

- `app/Http/Controllers/Admin/DepartamentoController.php`
- `app/Models/Departamento.php`
- `app/Policies/DepartamentoPolicy.php`

### Actividades y asistencia

Una actividad global se crea para todos los departamentos. El servicio crea un registro `DepartamentoActividad` por cada departamento. Ademas genera un token de registro publico para que Servidores confirmen compromiso con su numero legendario.

Cuando un Servidor se compromete, se crea una asistencia inicial como ausente. Luego los roles autorizados pueden marcar presente o ausente y registrar pagos.

Archivos clave:

- `app/Http/Controllers/Admin/ActividadController.php`
- `app/Http/Controllers/ActividadRegistrationController.php`
- `app/Http/Controllers/Admin/AsistenciaController.php`
- `app/Services/ActividadService.php`

### Finanzas

Los movimientos financieros registran ingresos y egresos por departamento. Tambien se crean movimientos automaticamente cuando se registran pagos o abonos de actividades.

Archivos clave:

- `app/Http/Controllers/Admin/FinanzasController.php`
- `app/Models/FinanzasMovimiento.php`
- `app/Policies/FinanzasMovimientoPolicy.php`

### Reconocimientos

Permite enviar reconocimientos personalizados o mensajes de cumpleanos. Puede generar enlaces de WhatsApp y enviar emails segun los canales seleccionados.

Archivos clave:

- `app/Http/Controllers/Admin/ReconocimientoController.php`
- `app/Services/ReconocimientoService.php`
- `app/Mail/ReconocimientoMessageMail.php`

### QR, campanas y tribus

Las campanas QR agrupan tokens de registro de Senderistas. Cada token tiene hash, token crudo, fecha de expiracion, uso y usuario que lo uso. Dentro de una campana se pueden crear tribus y asignar registros historicos a ellas.

Archivos clave:

- `app/Http/Controllers/Admin/QrCampaignController.php`
- `app/Http/Controllers/Admin/QrTokenController.php`
- `app/Http/Controllers/Admin/TribuController.php`
- `app/Http/Middleware/ValidateSenderistaRegistrationToken.php`
- `app/Services/SenderistaRegistrationService.php`

## 5. Flujos importantes

### Registro de Senderista por QR

1. Administrador o Supervisor crea una campana QR o un token QR.
2. El sistema genera una URL publica con token.
3. La persona abre el enlace y completa sus datos.
4. El middleware valida que el token exista, no este usado, no este vencido y pertenezca a una campana activa.
5. El sistema crea el usuario con rol Senderista.
6. El token queda marcado como usado.
7. El registro queda disponible en la campana para seguimiento, tribus y conversion futura.

### Conversion de Senderista a Servidor

1. Administrador o Supervisor abre el detalle del usuario Senderista.
2. Entra a convertir.
3. Asigna numero legendario y departamento.
4. El sistema cambia el rol de Senderista a Servidor.

### Creacion de actividad global

1. Administrador o Supervisor crea una actividad con nombre, fecha y precio.
2. El sistema crea la actividad como abierta.
3. El sistema crea una relacion de actividad por cada departamento.
4. El sistema genera un token publico de compromiso.
5. Los Servidores se comprometen usando su numero legendario.

### Marcacion de asistencia y pagos

1. Encargado, Lider, Supervisor o Administrador abre la asistencia de su departamento o actividad autorizada.
2. Marca presente o ausente.
3. Puede registrar pago total, abono o freepass.
4. Si hay pago o abono, se crea un movimiento financiero de ingreso.
5. El sistema recalcula el estado de pago del departamento para esa actividad.

### Registro financiero manual

1. Administrador, Supervisor o Finanzas entra a finanzas.
2. Crea un ingreso o egreso con departamento, monto, descripcion y fecha.
3. El movimiento impacta los balances del modulo financiero y del dashboard.

## 6. Matriz resumida de permisos

| Modulo / Accion | Administrador | Supervisor | Encargado | Lider | Finanzas | Servidor | Senderista |
| --- | --- | --- | --- | --- | --- | --- | --- |
| Dashboard admin | Si | Si | No | No | No | No | No |
| Ver usuarios | Si | Si | No | Solo su depto | No | No | No |
| Crear/editar usuarios | Si | Si | No | No | No | No | No |
| Cambiar contrasenas desde accesos | Si | No | No | No | No | No | No |
| Ver departamentos | Si | Si | Segun relacion | Segun relacion | Si | No | No |
| Crear/editar departamentos | Si | Si | No | No | No | No | No |
| Crear actividades globales | Si | Si | No | No | No | No | No |
| Ver mis actividades | Si | Si | Si | Si | No | No | No |
| Marcar asistencia | Si | Si | Si, su depto | Si, su depto | No | No | No |
| Ver finanzas | Si | Si | No | No | Si | No | No |
| Crear/eliminar movimientos | Si | Si | No | No | Si | No | No |
| Reconocimientos | Si | Si | No | No | No | No | No |
| Campanas y tokens QR | Si | Si | No | No | No | No | No |
| Registro publico senderista | No aplica | No aplica | No aplica | No aplica | No aplica | No aplica | Si |
| Registro publico actividad | No aplica | No aplica | No aplica | No aplica | No aplica | Si | No |

## 7. Estructura tecnica

El proyecto sigue una estructura Laravel tradicional:

- `routes/web.php`: rutas web, panel admin y registros publicos.
- `app/Http/Controllers`: controladores por modulo.
- `app/Http/Requests`: validaciones por formulario.
- `app/Models`: entidades principales del dominio.
- `app/Policies`: reglas de autorizacion por recurso.
- `app/Services`: logica de negocio reutilizable.
- `resources/views`: pantallas Blade.
- `database/migrations`: estructura de base de datos.
- `database/seeders`: roles y datos iniciales.

## 8. Datos y relaciones principales

Entidades centrales:

- `User`: usuarios del sistema, con rol, estado, datos personales y departamento.
- `Departamento`: grupo operativo con supervisor, lider y encargado.
- `Actividad`: actividad global.
- `DepartamentoActividad`: relacion entre actividad y departamento.
- `Asistencia`: asistencia de un usuario a una actividad de departamento.
- `FinanzasMovimiento`: ingreso o egreso asociado a un departamento.
- `Reconocimiento`: mensaje o reconocimiento enviado a usuarios.
- `QrCampaign`: campana de registro por QR.
- `QrRegistrationToken`: token de registro publico.
- `SenderistaRegistration`: registro historico de un Senderista.
- `Tribu` y `TribuMember`: agrupacion de registrados dentro de una campana QR.

## 9. Instalacion y ejecucion local

Comandos principales:

```bash
composer install
npm install
php artisan key:generate
php artisan migrate --seed
npm run build
php artisan serve
```

En desarrollo tambien se puede usar:

```bash
composer run dev
```

Usuario inicial documentado en `README.md`:

```text
Email: admin@example.com
Password: password
Rol: Administrador
```

## 10. Observaciones para mantenimiento

- Las rutas definen el primer nivel de acceso por rol.
- Las policies definen reglas mas especificas por departamento, actividad, asistencia o finanzas.
- Los servicios concentran operaciones con transacciones, como crear usuarios, registrar senderistas y crear actividades.
- Algunos textos en consola muestran problemas de codificacion para palabras con tilde. Conviene revisar la codificacion de archivos si se despliega o edita desde entornos distintos.
- La documentacion debe actualizarse si cambian rutas, roles o policies, porque la matriz de permisos depende directamente de esos archivos.
