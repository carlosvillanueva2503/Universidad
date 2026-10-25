import numpy as np
import matplotlib.pyplot as plt

# ==========================================
# 1. Señal Continua vs Discreta
# ==========================================
# Creamos un vector de "tiempo continuo" muy denso
t_continuo = np.linspace(0, 1, 1000)
# Creamos un vector de "tiempo discreto" (muestreo)
n_discreto = np.arange(0, 1, 0.05)

# Frecuencia de la señal (Hz)
f = 2 
senal_continua = np.sin(2 * np.pi * f * t_continuo)
senal_discreta = np.sin(2 * np.pi * f * n_discreto)

plt.figure(figsize=(10, 4))
plt.plot(t_continuo, senal_continua, label='Señal Continua x(t)', color='blue')
plt.stem(n_discreto, senal_discreta, linefmt='r-', markerfmt='ro', basefmt='k-', label='Señal Discreta x[n]')
plt.title('Clasificación: Señal Continua vs Señal Discreta')
plt.xlabel('Tiempo (s)')
plt.ylabel('Amplitud')
plt.legend()
plt.grid(True)
plt.show()

# ==========================================
# 2. Sistema LTI: Respuesta a una señal (Convolución)
# ==========================================
# Generamos una señal con ruido (Aperiódica)
t = np.linspace(0, 2, 200)
senal_original = np.sin(2 * np.pi * 1 * t)
ruido = np.random.normal(0, 0.3, senal_original.shape)
senal_ruidosa = senal_original + ruido

# Definimos un Sistema LTI (Filtro de media móvil para suavizar)
# Es invariante en el tiempo y lineal. Su respuesta al impulso es 'h'
h = np.ones(10) / 10 

# La salida del sistema LTI se obtiene mediante CONVOLUCIÓN
salida_sistema = np.convolve(senal_ruidosa, h, mode='same')

plt.figure(figsize=(10, 4))
plt.plot(t, senal_ruidosa, label='Entrada: Señal con Ruido', color='gray', alpha=0.7)
plt.plot(t, salida_sistema, label='Salida: Señal Filtrada (Sistema LTI)', color='red', linewidth=2)
plt.title('Respuesta de un Sistema LTI (Filtro) ante una señal')
plt.xlabel('Tiempo (s)')
plt.ylabel('Amplitud')
plt.legend()
plt.grid(True)
plt.show()