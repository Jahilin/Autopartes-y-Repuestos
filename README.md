# Participantes
1. Maria Fernanda Gallegos Bejarano - 20221004280
2. Jahilin Jahirixi Cantero Santos - 20211023950
3. Jorge DAniel Espinal Donaire - 20231002426
4. Jose Ramon Corea Aviles - 20231003748
5. Eduardo Jose Galindo - 20181006498



# Descripcion del Proyecto
La reconocida empresa hondureña Autopartes & Repuestos "Don Pancho", referente en la distribución de piezas originales y de reemplazo para vehículos japoneses, coreanos y americanos en Tegucigalpa, San Pedro Sula y La Ceiba, requiere lanzar su nueva plataforma web dividida en dos entornos sinérgicos:

-   **Portal de Clientes y Talleres:** Catálogo interactivo consultado desde base de datos, cotizador interactivo "Arma tu Kit de Mantenimiento Preventivo / Tune-Up", y módulo de checkout fiscal SAR (RTN , 15% de ISV) descuentos automáticos a partir de 3 repuestos, opciones de flete en moto o camioncito y factura digital correlativa.
-   **Panel Administrativo y Bodega de Repuestos:** Área privada protegida por inicio de sesión , control de roles que permite gestionar el inventario mediante operaciones CRUD completas, ejecutadas ágilmente a través de Ventanas Modales , integrando la Subida Segura de Archivos para fotografías de autopartes en formato JPG/PNG/WEBP.

Especificaciones Tecnicas

#### 1\. Base de Datos Relacional y Modelo POO con PDO

#### 2\. Sistema de Autenticación, Roles y Manejo de Sesiones

-   **Inicio de Sesión**
-   **Cierre de Sesión** 
-   **Protección de Rutas :** El panel administrativo debe verificar que el usuario tenga sesión activa y rol de `admin` o `bodeguero`; de lo contrario, redirigir automáticamente a `login.php`.
-   **Navbar Dinámica:** Mostrar botón de "Iniciar Sesión" para visitantes, o el nombre del usuario logueado  de su rol (\`Admin\` o \`Bodega\`) y botón de "Cerrar Sesión".

#### 3\. Panel de Gestión CRUD con MODALES Interactivos 

Toda la gestión administrativa debe realizarse dentro de la misma interfaz mediante ventanas modales  evitando redirecciones toscas:

-   **Tabla de Control de Inventario**
-   **Modal de Registro**
-   **Modal de Edición Dinámico**
-   **Modal de Confirmación de Eliminación**

#### 4\. Subida Segura de Archivos con los 4 Candados de Ciberseguridad 

-   **Candado 1:** Comprobación de que `$_FILES['fotografia']['error'] === UPLOAD_ERR_OK`.
-   **Candado 2:** Límite estricto de tamaño de máximo 4 MB.
-   **Candado 3:** Lista blanca de extensiones (`jpg`, `jpeg`, `png`, `webp`) y comprobación del tipo MIME 
-   **Candado 4:** Renombrado criptográfico único, guardado en la carpeta `uploads/` y almacenamiento de su nombre en la base de datos.

#### 5\. Portal de Clientes y Facturación

-   **Página de Inicio :** Hero banner institucional ("Expertos en Repuestos Genuinos y Compatibles"), vitrina de 5 repuestos insignia destacados consultados desde base de datos, marcas vehiculares aliadas (Toyota, Nissan, Honda, Hyundai, Isuzu, Ford) y pie de página completo.
-   **Catálogo Dinámico :** Renderizado mediante bucles desde base de datos, con fotos reales, especificaciones y precios por categoría (Frenos y Suspensión, Motor y Transmisión, Sistema Eléctrico, Fluidos y Lubricantes).
-   **Arma tu Kit Tune-Up :** Selección de tipo de vehículo (Sedán, SUV o Pick-Up 4x4), marca y viscosidad de aceite , filtros y repuestos adicionales con costo extra individual en Lempiras ( bujías de iridio, pastillas cerámicas, limpiador de inyectores, líquido de frenos, refrigerante).
-   **Facturación SAR:** Formulario enviado por POST, validación de RTN, teléfono , descuento automático del 10% para compras mayoristas (10 repuestos), cálculo obligatorio del 15% de ISV, fletes según modalidad (L. 0.00 retiro en mostrador, L. 50.00 moto express, L. 180.00 flete en camioncito) , numero de factura  guardado en la base de datos.
-   **Sucursales :** Directorio de sucursales físicas en Tegucigalpa (Toncontín y Suyapa), San Pedro Sula (Medina) y La Ceiba (CA-13), con historial de compras.

#### 6\. Control de Versiones con Git 

-   **Commits Progresivos y Atómicos:** 
-   **Archivo `.gitignore`**
-   **Archivo `README.md` Profesional:** Con descripción del proyecto, requisitos, instrucciones para importar el archivo de base de datos y tabla de credenciales de usuario y contraseña para que el docente pueda evaluar el sistema.
-   Subir al espacio de la tarea un archivo comprimido en formato .ZIP o .RAR nombrado bajo la nomenclatura oficial: `Nombre_Apellido_Cuenta_repuestos.zip` conteniendo todos los archivos PHP, hojas de estilo CSS
-   En la caja de comentarios, pegar el enlace publico directo al repositorio de Git para la verificacion de los commits de desarrollo.