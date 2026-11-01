<?php
/**
 * Clase SubidaArchivo
 * Subida segura de fotografías de repuestos con 4 candados de seguridad.
 */
class SubidaArchivo
{
    private const TAMANO_MAXIMO = 4 * 1024 * 1024; // 4 MB
    private const EXTENSIONES_PERMITIDAS = ['jpg', 'jpeg', 'png', 'webp'];
    private const MIME_PERMITIDOS = ['image/jpeg', 'image/png', 'image/webp'];

    private string $carpetaDestino;
    private string $mensajeError = '';
    private string $nombreGuardado = '';

    public function __construct(?string $carpetaDestino = null)
    {
        // Por defecto: carpeta uploads/ en la raiz del proyecto
        $this->carpetaDestino = rtrim($carpetaDestino ?? dirname(__DIR__) . '/uploads', '/\\') . DIRECTORY_SEPARATOR;
    }

    /**
     * Procesa el archivo enviado en $_FILES[$campo].
     * Devuelve true si se guardo; false si algún candado lo rechazo.
     */
    public function subir(string $campo = 'fotografia'): bool
    {
        $this->mensajeError = '';
        $this->nombreGuardado = '';

        // CANDADO 1: la subida llego sin errores
        if (!isset($_FILES[$campo]) || $_FILES[$campo]['error'] !== UPLOAD_ERR_OK) {
            $codigo = $_FILES[$campo]['error'] ?? UPLOAD_ERR_NO_FILE;
            $this->mensajeError = $this->mensajeDeErrorPHP($codigo);
            return false;
        }

        $archivo = $_FILES[$campo];

        // CANDADO 2: tamaño max de 4 MB
        if ($archivo['size'] > self::TAMANO_MAXIMO) {
            $this->mensajeError = 'La fotografía supera el límite de 4 MB.';
            return false;
        }

        // CANDADO 3: lista blanca de extensiones y tipo MIME real
        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, self::EXTENSIONES_PERMITIDAS, true)) {
            $this->mensajeError = 'Extensión no permitida. Solo se aceptan JPG, JPEG, PNG o WEBP.';
            return false;
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($archivo['tmp_name']);
        if (!in_array($mime, self::MIME_PERMITIDOS, true)) {
            $this->mensajeError = 'El contenido del archivo no es una imagen válida.';
            return false;
        }

        // CANDADO 4: nombre unico guardado en uploads/
        $nombreNuevo = bin2hex(random_bytes(16)) . '.' . $extension;
        $rutaFinal = $this->carpetaDestino . $nombreNuevo;

        if (!is_dir($this->carpetaDestino) || !move_uploaded_file($archivo['tmp_name'], $rutaFinal)) {
            $this->mensajeError = 'No se pudo guardar la fotografia en el servidor.';
            return false;
        }

        $this->nombreGuardado = $nombreNuevo; // Este valor se guarda en la base de datos
        return true;
    }

    public function getNombreGuardado(): string
    {
        return $this->nombreGuardado;
    }

    public function getMensajeError(): string
    {
        return $this->mensajeError;
    }

    private function mensajeDeErrorPHP(int $codigo): string
    {
        return match ($codigo) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'El archivo excede el tamaño permitido por el servidor.',
            UPLOAD_ERR_PARTIAL => 'La fotografia se subió de forma incompleta. Intenta de nuevo.',
            UPLOAD_ERR_NO_FILE => 'No se selecciono ninguna fotografia.',
            UPLOAD_ERR_NO_TMP_DIR, UPLOAD_ERR_CANT_WRITE, UPLOAD_ERR_EXTENSION => 'Error del servidor al procesar la fotografia.',
            default => 'Error desconocido al subir la fotografia.',
        };
    }
}