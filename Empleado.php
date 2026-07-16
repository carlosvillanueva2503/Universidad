<?php
/**
 * Clase Base: Empleado
 * Representa la estructura común. Se cambia de 'private' a 'protected' 
 * para permitir que las clases derivadas accedan y reutilicen los miembros.
 */
class Empleado {
    // 1. Atributos Protegidos (Heredables por las subclases)
    protected string $nombre;
    protected string $puesto;
    protected float $salarioMensual;

    /**
     * 2. Constructor de la Clase Base
     * Inicializa los atributos comunes del empleado.
     */
    public function __construct(string $nombre, string $puesto, float$salarioMensual) {
        $this->nombre =$nombre;
        $this->puesto =$puesto;
        $this->salarioMensual =$salarioMensual;
    }

    /**
     * Método base para obtener los detalles en texto.
     */
    public function obtenerDetalles(): string {
        return "Empleado: {$this->nombre} \vert{} Puesto: {$this->puesto} | Salario Mensual: $" . number_format($this->salarioMensual, 2);
    }

    /**
     * Método base para calcular el bono basado en un porcentaje.
     */
    public function calcularBonoAnual(float $porcentaje): float {
        return $this->salarioMensual * ($porcentaje / 100);
    }

    /**
     * Método de modificación (Setter) común para aplicar aumentos directos.
     */
    public function aplicarAumento(float $montoAumento): void {
        if ($montoAumento > 0) {
            $this->salarioMensual +=$montoAumento;
        }
    }
}
?>