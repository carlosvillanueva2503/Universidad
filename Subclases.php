<?php
require_once 'Empleado.php';

/**
 * Clase Derivada 1: EmpleadoTiempoCompleto
 * Aplica Herencia e introduce un bono fijo corporativo exclusivo.
 */
class EmpleadoTiempoCompleto extends Empleado {
    // Atributo único de la subclase
    private float $bonoFijo;

    public function __construct(string $nombre, string $puesto, float $salarioMensual, float $bonoFijo) {
        parent::__construct($nombre, $puesto, $salarioMensual);
        $this->bonoFijo = $bonoFijo;
    }

    /**
     * SOBRESCRITURA DE MÉTODO (Polimorfismo): Modifica el cálculo sumando el bono fijo exclusivo.
     */
    public function calcularBonoAnual(float $porcentaje): float {
        // Reutilizamos el comportamiento base y le añadimos el atributo único de esta subclase
        $bonoBase = parent::calcularBonoAnual($porcentaje);
        return $bonoBase + $this->bonoFijo;
    }
}

/**
 * Clase Derivada 2: EmpleadoPorHoras
 * Aplica Herencia. Calcula el salario base dinámicamente según horas trabajadas y una tarifa.
 */
class EmpleadoPorHoras extends Empleado {
    // Atributos únicos de la subclase
    private int $horasTrabajadas;
    private float $tarifaHora;

    public function __construct(string $nombre, string $puesto, int $horasTrabajadas, float $tarifaHora) {
        $this->horasTrabajadas = $horasTrabajadas;
        $this->tarifaHora = $tarifaHora;
        
        // Calculamos el salario mensual antes de pasarlo a la clase base
        $salarioCalculado = $horasTrabajadas * $tarifaHora;
        
        // REUTILIZACIÓN DE CÓDIGO: Pasamos los valores calculados al constructor base
        parent::__construct($nombre, $puesto, $salarioCalculado);
    }

    public function obtenerDetalles(): string {
        return "Empleado por Horas: {$this->nombre} | Puesto: {$this->puesto} | Horas: {$this->horasTrabajadas} (Tarifa: $" . number_format($this->tarifaHora, 2) . ") | Salario Base Calculado: $" . number_format($this->salarioMensual, 2);
    }
}
?>