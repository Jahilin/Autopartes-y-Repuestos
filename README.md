# Autopartes & Repuestos "Don Pancho"

Plataforma web de la empresa Autopartes & Repuestos "Don Pancho", distribuidora de repuestos para vehículos japoneses, coreanos y americanos en Tegucigalpa, San Pedro Sula y La Ceiba.

El sistema tiene dos partes:

- **Portal de clientes y talleres:** catálogo de repuestos desde la base de datos, cotizador "Arma tu Kit Tune-Up", facturación con RTN e ISV del 15% y directorio de sucursales con historial de compras.
- **Panel administrativo de bodega:** área privada con inicio de sesión y roles (Admin y Bodega) para administrar el inventario con ventanas modales y subida segura de fotografías.

## Tecnologías

PHP 8 con PDO, MySQL (MariaDB), Bootstrap 5, HTML, CSS y JavaScript.

## Requisitos

- XAMPP con PHP 8 o superior, Apache y MySQL
- Extensiones `pdo_mysql` y `fileinfo` activas en `php.ini`
- `upload_max_filesize = 8M` y `post_max_size = 10M` en `php.ini`
- Conexión a internet (Bootstrap 5 se carga desde un CDN)

## Instalación

1. Clonar el repositorio `Autopartes-y-Repuestos` dentro de `C:\xampp\htdocs\`.
2. Abrir el panel de control de XAMPP e iniciar **Apache** y **MySQL**.
3. Abrir `http://localhost/phpmyadmin`, ir a la pestaña **Importar**, elegir el archivo `database.sql` y presionar **Importar**. Esto crea la base `repuestos_don_pancho` con sus tablas y datos de ejemplo.
4. Abrir `http://localhost/Autopartes-y-Repuestos/` en el navegador.

La conexión a la base de datos está en `config/conexion.php` (servidor `localhost`, usuario `root`, sin contraseña). Si el MySQL del equipo usa otros datos, se cambian ahí.

## Credenciales de prueba

El inicio de sesión se hace con el correo y la contraseña.

| Correo | Contraseña | Rol |
|---|---|---|
| admin@donpancho.hn | Admin123 | Admin |
| bodega@donpancho.hn | Bodega123 | Bodega |

## Páginas del sistema

- `index.php`: página de inicio
- `catalogo.php`: catálogo de repuestos por categoría
- `tuneup.php`: arma tu kit Tune-Up
- `facturacion.php`: facturación
- `sucursales.php`: sucursales e historial de compras
- `login.php`: inicio de sesión
- `panel.php`: panel de inventario (solo Admin y Bodega)

## Estructura del proyecto

- `config/`: conexión a la base de datos
- `clases/`: clases PHP que consultan la base de datos con PDO
- `css/`: hojas de estilo
- `includes/`: piezas compartidas (cabecera, menú, pie de página, protección de rutas y scripts)
- `uploads/`: fotografías de los repuestos
- `database.sql`: script de la base de datos

## Integrantes

- 1. Maria Fernanda Gallegos Bejarano - 20221004280
- 2. Jahilin Jahirixi Cantero Santos - 20211023950
- 3. Jorge DAniel Espinal Donaire - 20231002426
- 4. Jose Ramon Corea Aviles - 20231003748
- 5. Eduardo Jose Galindo - 20181006498