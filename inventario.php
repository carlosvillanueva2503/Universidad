<?php
// 1. Iniciamos la sesión
session_start();

// 2. Definición de la Interfaz y Clases (POO + Polimorfismo)
interface DescontableInterface {
    public function aplicarDescuento(float $porcentaje): void;
}

abstract class Producto {
    protected string $id;
    protected string $nombre;
    protected float $precioBase;
    protected int $stock;
    protected string $categoria;

    public function __construct(string $id, string $nombre, float $precioBase, int $stock, string $categoria) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->precioBase = $precioBase;
        $this->stock = $stock;
        $this->categoria = $categoria;
    }

    public function getId(): string { return $this->id; }
    public function getNombre(): string { return $this->nombre; }
    public function getPrecioBase(): float { return $this->precioBase; }
    public function getStock(): int { return $this->stock; }
    public function getCategoria(): string { return $this->categoria; }

    abstract public function calcularPrecioFinal(): float;
}

class Electronico extends Producto {
    private int $mesesGarantia;

    public function __construct(string $id, string $nombre, float $precioBase, int $stock, int $mesesGarantia) {
        parent::__construct($id, $nombre, $precioBase, $stock, "Electrónico");
        $this->mesesGarantia = $mesesGarantia;
    }

    public function calcularPrecioFinal(): float {
        return $this->precioBase + ($this->mesesGarantia * 5.0);
    }
}

class Alimento extends Producto implements DescontableInterface {
    private float $descuento = 0.0;

    public function __construct(string $id, string $nombre, float $precioBase, int $stock, float $descuento = 0.0) {
        parent::__construct($id, $nombre, $precioBase, $stock, "Alimento");
        $this->descuento = $descuento;
    }

    public function aplicarDescuento(float $porcentaje): void {
        $this->descuento = $porcentaje;
    }

    public function calcularPrecioFinal(): float {
        return $this->precioBase * (1 - ($this->descuento / 100));
    }
}

class Ropa extends Producto implements DescontableInterface {
    private float $descuento = 0.0;

    public function __construct(string $id, string $nombre, float $precioBase, int $stock, float $descuento = 0.0) {
        parent::__construct($id, $nombre, $precioBase, $stock, "Ropa");
        $this->descuento = $descuento;
    }

    public function aplicarDescuento(float $porcentaje): void {
        $this->descuento = $porcentaje;
    }

    public function calcularPrecioFinal(): float {
        return $this->precioBase * (1 - ($this->descuento / 100));
    }
}

// 3. Inicializar la sesión con DATOS PLANOS (sin guardar instancias directas)
if (!isset($_SESSION['datos_inventario']) || isset($_GET['reset'])) {
    $_SESSION['datos_inventario'] = [
        ['tipo' => 'electronico', 'id' => 'E01', 'nombre' => 'Laptop Gaming', 'precio' => 1200.00, 'stock' => 5, 'extra' => 12],
        ['tipo' => 'alimento', 'id' => 'A01', 'nombre' => 'Leche Entera 1L', 'precio' => 2.50, 'stock' => 40, 'extra' => 15],
        ['tipo' => 'ropa', 'id' => 'R01', 'nombre' => 'Camisa Casual', 'precio' => 35.00, 'stock' => 15, 'extra' => 20]
    ];
    if (isset($_GET['reset'])) {
        header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
        exit;
    }
}

// 4. Procesar envío de formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'agregar') {
    $_SESSION['datos_inventario'][] = [
        'tipo' => $_POST['tipo'] ?? 'electronico',
        'id' => "P" . rand(100, 999),
        'nombre' => htmlspecialchars($_POST['nombre']),
        'precio' => (float)$_POST['precio'],
        'stock' => (int)$_POST['stock'],
        'extra' => (float)($_POST['extra'] ?? 0)
    ];
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// 5. Reconstruir los Objetos Polimórficos a partir de los datos guardados
/** @var Producto[] $objetosProductos */
$objetosProductos = [];

foreach ($_SESSION['datos_inventario'] as $item) {
    if ($item['tipo'] === 'electronico') {
        $objetosProductos[] = new Electronico($item['id'], $item['nombre'], $item['precio'], $item['stock'], (int)$item['extra']);
    } elseif ($item['tipo'] === 'alimento') {
        $objetosProductos[] = new Alimento($item['id'], $item['nombre'], $item['precio'], $item['stock'], (float)$item['extra']);
    } elseif ($item['tipo'] === 'ropa') {
        $objetosProductos[] = new Ropa($item['id'], $item['nombre'], $item['precio'], $item['stock'], (float)$item['extra']);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión de Inventarios</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 text-slate-800 font-sans p-6">

    <div class="max-w-6xl mx-auto space-y-6">
        
        <header class="bg-indigo-600 text-white p-6 rounded-lg shadow-md flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold">📦 Sistema de Gestión de Inventario</h1>
                <p class="text-indigo-200 text-sm">Demostración de Polimorfismo, Clases Abstractas e Interfaces</p>
            </div>
            <a href="?reset=true" class="bg-indigo-500 hover:bg-indigo-700 text-xs px-3 py-2 rounded transition font-medium">
                Resetear Inventario
            </a>
        </header>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Formulario -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-slate-200">
                <h2 class="text-lg font-semibold mb-4 text-slate-700">Agregar Producto</h2>
                <form method="POST" class="space-y-4">
                    <input type="hidden" name="accion" value="agregar">
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-1">Nombre</label>
                        <input type="text" name="nombre" required placeholder="Ej. Audífonos Bluetooth" class="w-full border border-slate-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-sm font-medium text-slate-600 mb-1">Precio Base ($)</label>
                            <input type="number" step="0.01" name="precio" required placeholder="50.00" class="w-full border border-slate-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-600 mb-1">Stock</label>
                            <input type="number" name="stock" required placeholder="10" class="w-full border border-slate-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-1">Tipo de Producto</label>
                        <select name="tipo" id="tipoProducto" onchange="actualizarCampoExtra()" class="w-full border border-slate-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="electronico">Electrónico (+Garantía)</option>
                            <option value="alimento">Alimento (% Descuento)</option>
                            <option value="ropa">Ropa (% Descuento)</option>
                        </select>
                    </div>

                    <div id="campoExtraContenedor">
                        <label id="lblExtra" class="block text-sm font-medium text-slate-600 mb-1">Meses de Garantía (+$5 c/u)</label>
                        <input type="number" name="extra" id="inputExtra" placeholder="12" class="w-full border border-slate-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 rounded text-sm transition">
                        + Registrar Producto
                    </button>
                </form>
            </div>

            <!-- Tabla Polimórfica -->
            <div class="lg:col-span-2 bg-white p-6 rounded-lg shadow-sm border border-slate-200">
                <h2 class="text-lg font-semibold mb-4 text-slate-700">Reporte de Inventario</h2>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 text-xs uppercase tracking-wider">
                                <th class="p-3">ID</th>
                                <th class="p-3">Producto</th>
                                <th class="p-3">Categoría</th>
                                <th class="p-3">Stock</th>
                                <th class="p-3">Precio Base</th>
                                <th class="p-3">Precio Final</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            <?php foreach ($objetosProductos as $producto): ?>
                                <tr class="hover:bg-slate-50">
                                    <td class="p-3 font-mono text-xs text-slate-500"><?= $producto->getId() ?></td>
                                    <td class="p-3 font-medium text-slate-800"><?= $producto->getNombre() ?></td>
                                    <td class="p-3">
                                        <span class="px-2 py-1 text-xs rounded-full font-semibold
                                            <?php
                                                if($producto instanceof Electronico) echo 'bg-blue-100 text-blue-700';
                                                elseif($producto instanceof Alimento) echo 'bg-green-100 text-green-700';
                                                else echo 'bg-purple-100 text-purple-700';
                                            ?>">
                                            <?= $producto->getCategoria() ?>
                                        </span>
                                    </td>
                                    <td class="p-3 text-slate-600"><?= $producto->getStock() ?> unid.</td>
                                    <td class="p-3 text-slate-600">$<?= number_format($producto->getPrecioBase(), 2) ?></td>
                                    <!-- POLIMORFISMO EN ACCIÓN -->
                                    <td class="p-3 font-bold text-slate-900">
                                        $<?= number_format($producto->calcularPrecioFinal(), 2) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <script>
        function actualizarCampoExtra() {
            const tipo = document.getElementById('tipoProducto').value;
            const lbl = document.getElementById('lblExtra');
            const input = document.getElementById('inputExtra');

            if (tipo === 'electronico') {
                lbl.innerText = 'Meses de Garantía (+$5 c/u)';
                input.placeholder = '12';
            } else {
                lbl.innerText = 'Porcentaje de Descuento (%)';
                input.placeholder = '15';
            }
        }
    </script>
</body>
</html>