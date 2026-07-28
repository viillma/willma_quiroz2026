# Guía de Desarrollo — Sistema de Servicios de Diseño Gráfico Digital

---

## Índice

- **Capítulo 0** · Visión del Producto
- **Capítulo 1** · Configuración del Proyecto
- **Capítulo 2** · Gestión de Usuarios
- **Capítulo 3** · Gestión de Clientes
- **Capítulo 4** · Gestión de Servicios
- **Capítulo 5** · Registro de Pedidos
- **Capítulo 6** · Gestión de Diseños
- **Capítulo 7** · Gestión de Pagos
- **Capítulo 8** · Reportes
- **Capítulo 9** · Entrega de Trabajos
- **Capítulo 10** · Seguridad
- **Capítulo 11** · Pruebas
- **Capítulo 12** · Despliegue

---

## Capítulo 0 · Visión del Producto

### Objetivo
Establecer una plataforma web integral que permita a una empresa de diseño gráfico digital gestionar todo su flujo de trabajo: desde la captación de clientes, registro de pedidos, asignación de servicios (logotipos, banners, tarjetas de invitación, fotografía, edición y reparación de fotografías, afiches publicitarios, tarjetas de presentación), hasta la entrega final y el cobro.

### Explicación
El sistema centraliza la información de clientes, pedidos, diseños y pagos en un solo lugar, eliminando la dependencia de hojas de cálculo, correos electrónicos sueltos y comunicación informal. Permite a los diseñadores, administradores y vendedores tener visibilidad en tiempo real del estado de cada trabajo.

### Casos de uso
- **CU-00-01**: El administrador da de alta un nuevo servicio (ej. «Logotipos») con precio base y descripción.
- **CU-00-02**: El vendedor registra un pedido para un cliente existente.
- **CU-00-03**: El diseñador sube el archivo final una vez terminado el trabajo.
- **CU-00-04**: El administrador genera un reporte mensual de ingresos.

### Requisitos funcionales
- RF-00-01: El sistema debe permitir la gestión completa de clientes, servicios, pedidos, diseños y pagos.
- RF-00-02: El sistema debe generar reportes en formato PDF o Excel.

### Requisitos no funcionales
- RNF-00-01: Tiempo de respuesta < 2 segundos en operaciones CRUD simples.
- RNF-00-02: Interfaz responsive (escritorio, tablet, móvil).
- RNF-00-03: Disponibilidad 99,5 % en horario laboral.

### Prompt para desarrollo
> Crea un sistema web con Laravel + Inertia + Vue 3 que administre una empresa de diseño gráfico. Los servicios ofrecidos son: Logotipos, Banners Publicitarios, Tarjetas de Invitación, Fotografía, Edición de Fotografías, Reparación de Fotografías, Afiches Publicitarios y Tarjetas de Presentación. Define la visión del producto antes de iniciar el desarrollo.

### Verificación
- [ ] Se ha definido el alcance del proyecto.
- [ ] Se han identificado los actores (admin, vendedor, diseñador).
- [ ] Se ha documentado la lista completa de servicios.

---

## Capítulo 1 · Configuración del Proyecto

### Objetivo
Inicializar un proyecto Laravel 11 con Inertia.js 3, Vue 3, Tailwind CSS v4 y las herramientas base necesarias para comenzar el desarrollo.

### Explicación
Se utiliza Laravel como backend PHP, Inertia.js como puente entre backend y frontend, Vue 3 con Composition API para la interfaz de usuario reactiva, y Tailwind CSS v4 para el diseño visual.

### Casos de uso
- **CU-01-01**: El desarrollador clona el repositorio y ejecuta `composer install` + `npm install`.
- **CU-01-02**: El desarrollador ejecuta `php artisan serve` y `npm run dev` para iniciar el entorno local.

### Requisitos funcionales
- RF-01-01: El proyecto debe tener la estructura base de Laravel 11.
- RF-01-02: Debe integrar Inertia.js v3 con Vue 3.
- RF-01-03: Debe usar Tailwind CSS v4 para los estilos.

### Requisitos no funcionales
- RNF-01-01: Node.js 20+, PHP 8.2+, Composer 2+.
- RNF-01-02: Base de datos MySQL 8 o PostgreSQL 15.

### Prompt para desarrollo
> Crea un nuevo proyecto Laravel 11. Instala y configura Inertia.js v3 con Vue 3 y Tailwind CSS v4. Configura Vite como bundler. Verifica que la página de inicio renderice un componente Vue de prueba.

### Verificación
- [ ] `php artisan serve` funciona sin errores.
- [ ] `npm run dev` compila correctamente.
- [ ] La página de inicio muestra el componente Vue.
- [ ] Tailwind CSS aplica estilos correctamente.

---

## Capítulo 2 · Gestión de Usuarios

### Objetivo
Implementar autenticación y gestión de usuarios del sistema (administradores, vendedores, diseñadores) con roles y permisos.

### Explicación
Se utiliza Laravel Fortify como backend de autenticación (headless) y Laravel Permissions (spatie/laravel-permission) para el control de roles. Los roles definidos son: `admin`, `seller` (vendedor) y `designer` (diseñador).

### Casos de uso
- **CU-02-01**: El administrador crea un nuevo usuario vendedor.
- **CU-02-02**: El usuario inicia sesión con correo y contraseña.
- **CU-02-03**: El administrador desactiva la cuenta de un diseñador.
- **CU-02-04**: El usuario cierra sesión.

### Requisitos funcionales
- RF-02-01: Registro, inicio de sesión y cierre de sesión.
- RF-02-02: CRUD de usuarios (solo admin).
- RF-02-03: Asignación de roles (admin, seller, designer).
- RF-02-04: Restablecimiento de contraseña por correo.

### Requisitos no funcionales
- RNF-02-01: Contraseñas almacenadas con bcrypt.
- RNF-02-02: Protección contra fuerza bruta (throttle: 5 intentos/minuto).

### Prompt para desarrollo
> Implementa autenticación con Laravel Fortify + Inertia. Crea migración de usuarios con roles. Usa spatie/laravel-permission para asignar permisos. Crea las vistas Vue para login, registro y perfil. Protege las rutas según el rol.

### Verificación
- [ ] Un usuario puede registrarse e iniciar sesión.
- [ ] Un admin puede crear, editar y desactivar usuarios.
- [ ] Un vendedor NO puede acceder a rutas de admin.
- [ ] El restablecimiento de contraseña funciona.

---

## Capítulo 3 · Gestión de Clientes

### Objetivo
Administrar el directorio de clientes de la empresa, almacenando datos de contacto, historial de pedidos y observaciones.

### Explicación
Cada cliente tiene nombre, teléfono, correo, empresa (opcional) y notas. Los vendedores pueden registrar nuevos clientes al tomar un pedido. El administrador puede ver el historial completo de cada cliente.

### Casos de uso
- **CU-03-01**: El vendedor registra un nuevo cliente.
- **CU-03-02**: El administrador busca un cliente por nombre o teléfono.
- **CU-03-03**: El administrador edita los datos de un cliente.
- **CU-03-04**: El vendedor consulta el historial de pedidos de un cliente.

### Requisitos funcionales
- RF-03-01: CRUD completo de clientes.
- RF-03-02: Búsqueda por nombre, teléfono o correo.
- RF-03-03: Vista de historial de pedidos por cliente.
- RF-03-04: Exportación de lista de clientes a Excel.

### Requisitos no funcionales
- RNF-03-01: Búsqueda con respuesta < 1 segundo para hasta 10 000 clientes.
- RNF-03-02: Paginación de 20 registros por página.

### Prompt para desarrollo
> Crea el modelo Client con migración y controlador. Implementa CRUD con validación. Crea componentes Vue para listar, crear, editar y buscar clientes. Agrega paginación y exportación a Excel.

### Verificación
- [ ] Se puede crear un cliente con todos los campos.
- [ ] La búsqueda devuelve resultados correctos.
- [ ] El historial de pedidos se muestra correctamente.
- [ ] La exportación a Excel genera un archivo válido.

---

## Capítulo 4 · Gestión de Servicios

### Objetivo
Administrar el catálogo de servicios de diseño gráfico que ofrece la empresa.

### Explicación
Los servicios son: Logotipos, Banners Publicitarios, Tarjetas de Invitación, Fotografía, Edición de Fotografías, Reparación de Fotografías, Afiches Publicitarios y Tarjetas de Presentación. Cada servicio tiene un nombre, descripción, precio base, tiempo estimado de entrega y una imagen referencial.

### Casos de uso
- **CU-04-01**: El administrador agrega un nuevo servicio «Logotipos» con precio base de S/ 150.
- **CU-04-02**: El administrador actualiza el precio base de «Banners Publicitarios».
- **CU-04-03**: El vendedor consulta la lista de servicios disponibles al registrar un pedido.

### Requisitos funcionales
- RF-04-01: CRUD completo de servicios.
- RF-04-02: Cada servicio tiene: nombre, descripción, precio base, tiempo estimado, imagen.
- RF-04-03: Los servicios se listan en el formulario de pedidos.

### Requisitos no funcionales
- RNF-04-01: Las imágenes deben optimizarse a un máximo de 500 KB.
- RNF-04-02: Los precios se manejan con 2 decimales.

### Prompt para desarrollo
> Crea el modelo Service con migración y controlador. Implementa CRUD. Los 8 servicios deben venir precargados vía seeder. Agrega carga de imagen con validación de tamaño y tipo. Crea componentes Vue con listado y formulario.

### Verificación
- [ ] Los 8 servicios aparecen en la base de datos después del seeder.
- [ ] Se puede crear, editar y eliminar un servicio.
- [ ] La imagen se sube correctamente y se muestra en el listado.
- [ ] Los servicios aparecen en el formulario de pedidos.

---

## Capítulo 5 · Registro de Pedidos

### Objetivo
Permitir el registro de pedidos de diseño gráfico, vinculando un cliente con uno o más servicios.

### Explicación
Un pedido contiene: cliente, servicios solicitados (una o varias líneas con servicio, cantidad y precio acordado), fecha de ingreso, fecha de entrega estimada, estado (pendiente, en diseño, en revisión, aprobado, entregado), observaciones y referencia del pedido.

### Casos de uso
- **CU-05-01**: El vendedor registra un pedido para un cliente existente con 2 servicios.
- **CU-05-02**: El vendedor modifica el estado de un pedido a «en diseño».
- **CU-05-03**: El administrador asigna un diseñador a un pedido.

### Requisitos funcionales
- RF-05-01: Registro de pedidos con cliente, servicios y cantidades.
- RF-05-02: Asignación de diseñador responsable.
- RF-05-03: Cambio de estado del pedido con registro de fecha y hora.
- RF-05-04: Generación automática de código de pedido único (ej. PED-2026-0001).

### Requisitos no funcionales
- RNF-05-01: La transacción de registro debe ser atómica (varios servicios en un solo pedido).
- RNF-05-02: El código de pedido debe ser único e incremental.

### Prompt para desarrollo
> Crea el modelo Order con migración, controlador y relación muchos a muchos con servicios (tabla pivote order_service con cantidad y precio_unitario). Implementa registro con transacciones. Crea componentes Vue para formulario de pedidos con selección de cliente y servicios dinámicos.

### Verificación
- [ ] Se puede registrar un pedido con múltiples servicios.
- [ ] El código de pedido se genera automáticamente.
- [ ] Se puede cambiar el estado del pedido.
- [ ] La asignación de diseñador funciona.

---

## Capítulo 6 · Gestión de Diseños

### Objetivo
Gestionar los archivos de diseño asociados a cada pedido, permitiendo la subida de versiones, comentarios y aprobación final.

### Explicación
Cada pedido puede tener múltiples archivos de diseño (bocetos, versiones, final). El diseñador sube los archivos, el vendedor o cliente pueden dejar comentarios, y el administrador aprueba la versión final.

### Casos de uso
- **CU-06-01**: El diseñador sube una primera versión del logotipo.
- **CU-06-02**: El vendedor agrega un comentario solicitando un cambio de color.
- **CU-06-03**: El administrador marca un diseño como «aprobado».
- **CU-06-04**: El diseñador sube la versión final en alta resolución.

### Requisitos funcionales
- RF-06-01: Subida de archivos por pedido (múltiples versiones).
- RF-06-02: Comentarios por archivo.
- RF-06-03: Control de versiones (v1, v2, v3…).
- RF-06-04: Marcado de versión aprobada.

### Requisitos no funcionales
- RNF-06-01: Tamaño máximo de archivo: 50 MB.
- RNF-06-02: Formatos permitidos: .ai, .psd, .pdf, .png, .jpg, .svg, .eps.
- RNF-06-03: Los archivos deben almacenarse en disco local o S3.

### Prompt para desarrollo
> Crea el modelo DesignFile con migración y controlador. Implementa subida de archivos con validación de tipo y tamaño. Agrega sistema de versionado. Crea componentes Vue para galería de diseños, visor de comentarios y botón de aprobación.

### Verificación
- [ ] Se puede subir un archivo de diseño correctamente.
- [ ] Los comentarios se muestran en orden cronológico.
- [ ] El versionado numérico se incrementa automáticamente.
- [ ] Al aprobar una versión, las demás quedan como «no aprobadas».

---

## Capítulo 7 · Gestión de Pagos

### Objetivo
Registrar y controlar los pagos realizados por los clientes asociados a cada pedido.

### Explicación
Cada pedido puede tener uno o varios pagos (cuando se paga en partes). Se registra el monto, medio de pago (efectivo, transferencia, Yape, Plin), fecha, y si está confirmado. El sistema debe mostrar el saldo pendiente por pedido.

### Casos de uso
- **CU-07-01**: El vendedor registra un pago parcial de S/ 100 para un pedido.
- **CU-07-02**: El administrador confirma un pago por transferencia.
- **CU-07-03**: El administrador consulta el saldo pendiente de todos los pedidos.

### Requisitos funcionales
- RF-07-01: Registro de pagos con monto, medio y fecha.
- RF-07-02: Cálculo automático de saldo pendiente (total - pagos).
- RF-07-03: Confirmación de pago (pendiente / confirmado / rechazado).
- RF-07-04: Historial de pagos por pedido.

### Requisitos no funcionales
- RNF-07-01: Precisión monetaria con 2 decimales.
- RNF-07-02: Los montos deben ser consistentes (el total de pagos no debe exceder el total del pedido).

### Prompt para desarrollo
> Crea el modelo Payment con migración y controlador. Implementa CRUD de pagos con validación de montos. Crea un componente Vue que muestre la tabla de pagos y el saldo pendiente en tiempo real. Agrega notificación al administrador cuando se registre un pago.

### Verificación
- [ ] Se puede registrar un pago y el saldo pendiente se actualiza.
- [ ] No se permite registrar un pago mayor al saldo pendiente.
- [ ] El historial de pagos se muestra ordenado por fecha.
- [ ] El administrador recibe notificación de nuevo pago.

---

## Capítulo 8 · Reportes

### Objetivo
Generar reportes gerenciales que permitan tomar decisiones basadas en datos.

### Explicación
Los reportes incluyen: ingresos por período, servicios más solicitados, clientes frecuentes, pedidos por estado, productividad por diseñador, y morosidad (pedidos con saldo pendiente mayor a 30 días).

### Casos de uso
- **CU-08-01**: El administrador genera un reporte de ingresos mensuales.
- **CU-08-02**: El administrador consulta el top 5 de servicios más vendidos.
- **CU-08-03**: El administrador exporta un reporte de clientes morosos.

### Requisitos funcionales
- RF-08-01: Reporte de ingresos por rango de fechas.
- RF-08-02: Reporte de servicios más solicitados.
- RF-08-03: Reporte de clientes frecuentes.
- RF-08-04: Reporte de pedidos por estado.
- RF-08-05: Exportación a PDF y Excel.

### Requisitos no funcionales
- RNF-08-01: Los reportes con hasta 10 000 registros deben generarse en < 5 segundos.
- RNF-08-02: Los reportes deben usar caché de base de datos para consultas pesadas.

### Prompt para desarrollo
> Crea un módulo de reportes con Laravel. Usa Laravel Excel para exportaciones. Implementa gráficos con Chart.js o ApexCharts en Vue. Crea filtros por fecha, servicio y diseñador. Almacena en caché las consultas más lentas.

### Verificación
- [ ] El reporte de ingresos muestra datos correctos.
- [ ] La exportación a Excel genera el archivo sin errores.
- [ ] Los gráficos se renderizan correctamente en el frontend.
- [ ] Los filtros modifican los resultados del reporte.

---

## Capítulo 9 · Entrega de Trabajos

### Objetivo
Gestionar la entrega formal de los trabajos terminados al cliente, incluyendo la descarga de archivos finales y la generación de una constancia de entrega.

### Explicación
Cuando un pedido alcanza el estado «aprobado», se habilita la opción de entrega. El sistema genera un comprobante de entrega (PDF) y envía un correo al cliente con el enlace de descarga de los archivos finales.

### Casos de uso
- **CU-09-01**: El administrador marca un pedido como «entregado».
- **CU-09-02**: El sistema envía automáticamente un correo al cliente con los archivos.
- **CU-09-03**: El cliente descarga los archivos finales desde un enlace temporal.

### Requisitos funcionales
- RF-09-01: Marcado de pedido como «entregado».
- RF-09-02: Generación de comprobante de entrega en PDF.
- RF-09-03: Envío de correo automático al cliente con enlace de descarga.
- RF-09-04: Enlace de descarga con expiración (7 días).

### Requisitos no funcionales
- RNF-09-01: El PDF debe generarse en < 10 segundos.
- RNF-09-02: El correo debe enviarse en menos de 2 minutos después de la entrega (cola de jobs).

### Prompt para desarrollo
> Crea un flujo de entrega: al cambiar el estado del pedido a «entregado», dispara un job para generar el PDF de constancia y otro job para enviar el correo. Usa Laravel Breeze para notificaciones o notificaciones nativas. Implementa enlaces temporales firmados para descarga.

### Verificación
- [ ] Al marcar como entregado, se genera el PDF.
- [ ] El cliente recibe el correo con el enlace.
- [ ] El enlace de descarga caduca después de 7 días.
- [ ] Los archivos descargados coinciden con los aprobados.

---

## Capítulo 10 · Seguridad

### Objetivo
Garantizar la confidencialidad, integridad y disponibilidad de la información del sistema.

### Explicación
Se implementan medidas de seguridad a nivel de aplicación (validación, autorización, sanitización), a nivel de red (HTTPS, firewall) y a nivel de datos (encriptación, backups).

### Casos de uso
- **CU-10-01**: Un usuario sin permisos intenta acceder a la ruta de administración.
- **CU-10-02**: El administrador realiza un backup manual de la base de datos.

### Requisitos funcionales
- RF-10-01: Autorización por roles y permisos en cada ruta y acción.
- RF-10-02: Validación y sanitización de todos los inputs.
- RF-10-03: Política de contraseñas seguras (mín. 8 caracteres, mayúscula, número).
- RF-10-04: Bloqueo temporal tras 5 intentos fallidos de inicio de sesión.
- RF-10-05: Backup automático diario de la base de datos.
- RF-10-06: Registro de logs de auditoría (inicio de sesión, cambios críticos).

### Requisitos no funcionales
- RNF-10-01: Cumplimiento de OWASP Top 10.
- RNF-10-02: Encriptación AES-256 para datos sensibles en reposo.
- RNF-10-03: HTTPS obligatorio en producción.

### Prompt para desarrollo
> Implementa middlewares de rol con spatie/laravel-permission. Agrega rate limiting a las rutas de login. Configura Laravel Sanctum para API tokens si es necesario. Crea un comando artisan para backup diario. Implementa logging de auditoría.

### Verificación
- [ ] Un vendedor no puede acceder a rutas de admin (403).
- [ ] Después de 5 intentos fallidos, la cuenta se bloquea.
- [ ] El backup diario se genera correctamente.
- [ ] Los logs de auditoría registran eventos críticos.

---

## Capítulo 11 · Pruebas

### Objetivo
Asegurar la calidad del software mediante pruebas automatizadas en los niveles unitario, de integración y funcional (E2E).

### Explicación
Se utiliza Pest PHP para pruebas de backend y Playwright o Cypress para pruebas de frontend. Se aplica TDD en los módulos críticos (pedidos, pagos, usuarios).

### Casos de uso
- **CU-11-01**: El desarrollador ejecuta `php artisan test` antes de cada commit.
- **CU-11-02**: El CI ejecuta las pruebas automáticamente al hacer push.

### Requisitos funcionales
- RF-11-01: Pruebas unitarias para modelos y acciones de Fortify.
- RF-11-02: Pruebas de integración para controladores CRUD.
- RF-11-03: Pruebas de autorización (roles y permisos).
- RF-11-04: Pruebas de validación de formularios.
- RF-11-05: Cobertura mínima del 70 %.

### Requisitos no funcionales
- RNF-11-01: Las pruebas deben completarse en < 5 minutos.
- RNF-11-02: Usar base de datos en memoria (SQLite) para pruebas rápidas.

### Prompt para desarrollo
> Configura Pest PHP en el proyecto. Escribe pruebas para los modelos User, Client, Service, Order, Payment y DesignFile. Prueba los endpoints de la API con estados HTTP esperados. Verifica que las reglas de autorización funcionen. Agrega un workflow de GitHub Actions para ejecutar pruebas en cada push.

### Verificación
- [ ] `php artisan test` pasa todas las pruebas.
- [ ] Las pruebas de autorización fallan cuando corresponde (403).
- [ ] La cobertura alcanza al menos 70 %.
- [ ] El CI se ejecuta en cada push sin errores.

---

## Capítulo 12 · Despliegue

### Objetivo
Poner el sistema en producción de forma segura, reproducible y automatizada.

### Explicación
El despliegue se realiza en un servidor VPS (Linux Ubuntu 24.04) con Nginx, PHP 8.3, MySQL 8 y Node.js 20. Se utiliza un pipeline de CI/CD con GitHub Actions y Deployer.

### Casos de uso
- **CU-12-01**: El administrador ejecuta el pipeline de CI/CD para desplegar una nueva versión.
- **CU-12-02**: El desarrollador revierte a una versión anterior en caso de error.

### Requisitos funcionales
- RF-12-01: Pipeline de CI/CD automatizado.
- RF-12-02: Script de despliegue con Deployer o Envoyer.
- RF-12-03: Migraciones ejecutadas automáticamente en despliegue.
- RF-12-04: Rollback a versión anterior en un solo comando.
- RF-12-05: Monitoreo básico (uptime, errores 500).

### Requisitos no funcionales
- RNF-12-01: Tiempo de despliegue < 5 minutos.
- RNF-12-02: Downtime < 30 segundos por despliegue.
- RNF-12-03: SSL/TLS con Let's Encrypt.

### Prompt para desarrollo
> Configura un servidor Ubuntu con Nginx, PHP 8.3, MySQL 8 y Node.js 20. Crea un pipeline de GitHub Actions que ejecute las pruebas, compile assets y despliegue vía Deployer. Configura SSL con Certbot. Implementa un health check endpoint.

### Verificación
- [ ] El pipeline de CI/CD se ejecuta correctamente.
- [ ] La aplicación está accesible vía HTTPS.
- [ ] Las migraciones se ejecutan automáticamente al desplegar.
- [ ] El rollback funciona y restaura la versión anterior.

---

## Apéndice A — Diagrama de Base de Datos (Esquema)

```
users
  id, name, email, password, role, is_active, created_at, updated_at

clients
  id, name, phone, email, company, notes, created_at, updated_at

services
  id, name, description, base_price, estimated_days, image, is_active, created_at, updated_at

orders
  id, code, client_id, designer_id, status, notes, total, delivery_date, created_at, updated_at

order_service
  id, order_id, service_id, quantity, unit_price, subtotal

design_files
  id, order_id, version, file_path, file_type, is_approved, comments, uploaded_by, created_at, updated_at

payments
  id, order_id, amount, payment_method, status, reference, confirmed_by, confirmed_at, created_at, updated_at

activity_logs
  id, user_id, action, description, ip_address, created_at
```

## Apéndice B — Tecnologías Recomendadas

| Capa        | Tecnología                  |
|-------------|-----------------------------|
| Backend     | Laravel 11 / PHP 8.3       |
| Frontend    | Vue 3 + Composition API    |
| Puente      | Inertia.js v3              |
| Estilos     | Tailwind CSS v4            |
| BD          | MySQL 8 / PostgreSQL 15    |
| Cache       | Redis                       |
| Colas       | Redis + Laravel Horizon    |
| Auth        | Laravel Fortify + Sanctum  |
| Roles       | spatie/laravel-permission   |
| Pruebas     | Pest PHP + Playwright       |
| CI/CD       | GitHub Actions + Deployer   |
| Servidor    | Ubuntu 24.04 + Nginx       |
| Almacenamiento | Local / AWS S3           |

---

*Documento generado como guía de desarrollo para el Sistema de Servicios de Diseño Gráfico Digital.*
