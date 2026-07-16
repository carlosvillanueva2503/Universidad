<?php
// Importamos el archivo de subclases (que ya incluye a Empleado.php)
require_once 'Subclases.php';

session_start();

if (isset($_GET['limpiar'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

// 3. Captura y procesamiento del Formulario de Alta con Herencia e Instanciación Dinámica
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_empleado'])) {
    $nombre = border_clean($_POST['nombre'] ?? '');
    $puesto = border_clean($_POST['puesto'] ?? '');
    $tipo = $_POST['tipo_empleado'] ?? 'base';

    if (!empty($nombre) && !empty($puesto)) {
        if ($tipo === 'tiempo_completo') {
            $salario = (float)($_POST['salario'] ?? 0);
            $bonoFijo = (float)($_POST['bono_fijo'] ?? 0);
            // Instancia de Subclase 1
            $_SESSION['empleados'][] = new EmpleadoTiempoCompleto($nombre, $puesto, $salario, $bonoFijo);
        } elseif ($tipo === 'por_horas') {
            $horas = (int)($_POST['horas'] ?? 0);
            $tarifa = (float)($_POST['tarifa'] ?? 0);
            // Instancia de Subclase 2
            $_SESSION['empleados'][] = new EmpleadoPorHoras($nombre, $puesto, $horas, $tarifa);
        } else {
            $salario = (float)($_POST['salario'] ?? 0);
            // Instancia de Clase Base
            $_SESSION['empleados'][] = new Empleado($nombre, $puesto, $salario);
        }
    }
}

// Procesamiento de los Cálculos Dinámicos
$resultado_calculo = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ejecutar_accion'])) {
    $index = (int)$_POST['empleado_index'];
    $accion = $_POST['accion'];
    $monto = (float)$_POST['monto'];

    if (isset($_SESSION['empleados'][$index])) {
        $empleadoSeleccionado = $_SESSION['empleados'][$index];

        if ($accion === 'bono') {
            // Llama al método polimórfico (actúa diferente según la clase del objeto)
            $bono = $empleadoSeleccionado->calcularBonoAnual($monto);
            $resultado_calculo = "El bono calculado para el empleado es de: <strong>$" . number_format($bono, 2) . "</strong>";
        } elseif ($accion === 'aumento') {
            $empleadoSeleccionado->aplicarAumento($monto);
            $resultado_calculo = "¡Se han aplicado $" . number_format($monto, 2) . " al salario base con éxito!";
            $_SESSION['empleados'][$index] = $empleadoSeleccionado;
        }
    }
}

function border_clean($data) {
    return htmlspecialchars(trim($data));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión de Empleados Interactivo - POO Herencia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">

    <nav class="navbar navbar-dark bg-dark shadow-sm">
        <div class="container">
            <span class="navbar-brand mb-0 h1">
                <i class="bi bi-cpu-fill me-2"></i>Sistema POO Interactivo - Gestión de Empleados (Herencia)
            </span>
            <a href="index.php?limpiar=1" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash3 me-1"></i>Reiniciar Sistema</a>
        </div>
    </nav>

    <div class="container my-5">
        <div class="row g-4">
            <div class="col-lg-5">
                
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-primary text-white fw-bold">
                        <i class="bi bi-person-plus-fill me-2"></i>1. Dar de Alta Nuevo Empleado
                    </div>
                    <div class="card-body">
                        <form action="index.php" method="POST">
                            <input type="hidden" name="crear_empleado" value="1">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nombre Completo:</label>
                                <input type="text" name="nombre" class="form-control" placeholder="Ej. Carlos Villanueva" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Puesto / Cargo:</label>
                                <input type="text" name="puesto" class="form-control" placeholder="Ej. Desarrollador Web" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Tipo de Contrato (Subclases):</label>
                                <select name="tipo_empleado" id="tipo_empleado" class="form-select" onchange="alternarCamposFormulario()" required>
                                    <option value="base">Empleado Regular (Clase Base)</option>
                                    <option value="tiempo_completo">Tiempo Completo (Subclase - Bono Fijo)</option>
                                    <option value="por_horas">Por Horas (Subclase - Pago x Hora)</option>
                                </select>
                            </div>

                            <div id="campo_salario" class="mb-3">
                                <label class="form-label fw-semibold">Salario Mensual ($):</label>
                                <input type="number" step="0.01" name="salario" class="form-control" value="0">
                            </div>
                            <div id="campo_bono" class="mb-3 d-none">
                                <label class="form-label fw-semibold">Bono Fijo Adicional ($):</label>
                                <input type="number" step="0.01" name="bono_fijo" class="form-control" value="0">
                            </div>
                            <div id="campos_horas" class="row d-none">
                                <div class="col-6 mb-3">
                                    <label class="form-label fw-semibold">Horas:</label>
                                    <input type="number" name="horas" class="form-control" value="0">
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="form-label fw-semibold">Tarifa Hora ($):</label>
                                    <input type="number" step="0.01" name="tarifa" class="form-control" value="0">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 fw-bold">
                                <i class="bi bi-plus-circle me-1"></i>Guardar
                            </button>
                        </form>
                    </div>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-header bg-warning text-dark fw-bold">
                        <i class="bi bi-gear-wide-connected me-2"></i>2. Ejecutar Aumentos
                    </div>
                    <div class="card-body">
                        <?php if (!empty($_SESSION['empleados'])): ?>
                            <form action="index.php" method="POST">
                                <input type="hidden" name="ejecutar_accion" value="1">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Seleccionar Empleado:</label>
                                    <select name="empleado_index" class="form-select" required>
                                        <?php foreach ($_SESSION['empleados'] as $idx => $emp): 
                                            preg_match('/Empleado:\s*([^|]+)/', $emp->obtenerDetalles(), $matches);
                                            $empNombre = trim($matches[1] ?? "Empleado ".($idx+1));
                                            // Mostramos la clase real del objeto en la lista desplegable
                                            $tipoClase = get_class($emp);
                                        ?>
                                            <option value="<?php echo $idx; ?>"><?php echo "[{$tipoClase}] - " . $empNombre; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Acción / Método:</label>
                                    <select name="accion" class="form-select" required>
                                        <option value="bono">Calcular Bono Anual (Ingresar %)</option>
                                        <option value="aumento">Aplicar Aumento Directo (Ingresar $)</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Valor / Monto:</label>
                                    <input type="number" step="0.01" name="monto" class="form-control" placeholder="Ej. 50 o 1500" required>
                                </div>
                                <button type="submit" class="btn btn-warning w-100 fw-bold text-dark">
                                    <i class="bi bi-play-fill me-1"></i>Ejecutar Operación
                                </button>
                            </form>
                        <?php else: ?>
                            <p class="text-muted text-center my-3">Registra al menos un empleado para activar los cálculos.</p>
                        <?php endif; ?>

                        <?php if (!empty($resultado_calculo)): ?>
                            <div class="alert alert-success mt-3 mb-0 border-0 shadow-sm" role="alert">
                                <i class="bi bi-info-circle-fill me-2"></i><?php echo $resultado_calculo; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            </div>

            <div class="col-lg-7">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-success text-white fw-bold d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-people-fill me-2"></i>3. Empleados Activos en Memoria</span>
                        <span class="badge bg-white text-success rounded-pill">
                            <?php echo isset($_SESSION['empleados']) ? count($_SESSION['empleados']) : 0; ?>
                        </span>
                    </div>
                    <div class="card-body bg-dark text-light p-4 rounded-bottom font-monospace">
                        <?php if (!empty($_SESSION['empleados'])): ?>
                            <p class="text-success mb-3"><i class="bi bi-terminal me-2"></i>Ver Objetos e Instancias en Memoria</p>
                            <?php foreach ($_SESSION['empleados'] as $index => $empleado): ?>
                                <div class="bg-secondary bg-opacity-25 p-3 rounded mb-3 border border-secondary border-opacity-50">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="text-info fw-bold">$_SESSION['empleados'][<?php echo $index; ?>]</span>
                                        <span class="badge bg-primary text-white small"><?php echo get_class($empleado); ?></span>
                                    </div>
                                    <div class="text-white-50 py-1">
                                        <i class="bi bi-chevron-right me-1 text-warning"></i><?php echo $empleado->obtenerDetalles(); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center text-muted my-5">
                                <i class="bi bi-folder-x display-4 mb-3 d-block"></i>
                                No hay Empleados actualmente<br>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Cambia la visualización de inputs dependiendo de la subclase elegida
        function alternarCamposFormulario() {
            const tipo = document.getElementById('tipo_empleado').value;
            document.getElementById('campo_salario').classList.toggle('d-none', tipo === 'por_horas');
            document.getElementById('campo_bono').classList.toggle('d-none', tipo !== 'tiempo_completo');
            document.getElementById('campos_horas').classList.toggle('d-none', tipo !== 'por_horas');
        }
    </script>
</body>
</html>