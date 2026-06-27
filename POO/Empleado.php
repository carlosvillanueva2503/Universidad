<?php
/**
 * Clase Empleado
 * Representa la estructura y el comportamiento de un empleado dentro del sistema.
 */
class Empleado {
    // 1. Declaración de Atributos (Propiedades de la clase)
    private string $nombre;
    private string $puesto;
    private float $salarioMensual;

    /**
     * 2. Constructor de la Clase
     * Se ejecuta automáticamente al instanciar un objeto para inicializar sus atributos.
     */
    public function __construct(string $nombre, string $puesto, float $salarioMensual) {
        $this->nombre = $nombre;
        $this->puesto = $puesto;
        $this->salarioMensual = $salarioMensual;
    }

    // 3. Métodos (Comportamiento del objeto)

    /**
     * Método para obtener los detalles completos del empleado en formato de texto.
     */
    public function obtenerDetalles(): string {
        return "Empleado: {$this->nombre} | Puesto: {$this->puesto} | Salario Mensual: $" . number_format($this->salarioMensual, 2);
    }

    /**
     * Método para calcular el aguinaldo o un bono anual basado en un porcentaje.
     */
    public function calcularBonoAnual(float $porcentaje): float {
        return $this->salarioMensual * ($porcentaje / 100);
    }

    /**
     * Método de modificación (Setter) para aplicar un aumento de salario.
     */
    public function aplicarAumento(float $montoAumento): void {
        if ($montoAumento > 0) {
            $this->salarioMensual += $montoAumento;
        }
    }
}
?>