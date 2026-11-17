import numpy as np
from scipy.signal import butter, lfilter, freqz
import matplotlib.pyplot as plt

# ==========================================
# 1. DEFINICIÓN DE LA SEÑAL DE ENTRADA
# ==========================================

# Parámetros de muestreo
fs = 1000.0         # Frecuencia de muestreo (Hz)
T = 2.0             # Duración total de la señal (segundos)
t = np.linspace(0, T, int(fs * T), endpoint=False)

# Señal compuesta: componentes de baja frecuencia (5 Hz) y alta frecuencia (50 Hz)
f_baja = 5.0
f_alta = 50.0
senal_pura = np.sin(2 * np.pi * f_baja * t) + 0.5 * np.sin(2 * np.pi * f_alta * t)

# Añadir ruido blanco gaussiano para simular ruido en la señal
np.random.seed(42)  # Para reproducibilidad
ruido = 0.5 * np.random.normal(size=t.shape)
senal_con_ruido = senal_pura + ruido

# ==========================================
# 2. DISEÑO DE FILTROS DIGITALES (Funciones)
# ==========================================
def butter_lowpass(cutoff, fs, order=5):
    nyq = 0.5 * fs
    normal_cutoff = cutoff / nyq
    b, a = butter(order, normal_cutoff, btype='low', analog=False)
    return b, a

def butter_highpass(cutoff, fs, order=5):
    nyq = 0.5 * fs
    normal_cutoff = cutoff / nyq
    b, a = butter(order, normal_cutoff, btype='high', analog=False)
    return b, a

def butter_bandpass(lowcut, highcut, fs, order=5):
    nyq = 0.5 * fs
    low = lowcut / nyq
    high = highcut / nyq
    b, a = butter(order, [low, high], btype='band', analog=False)
    return b, a

def aplicar_filtro(data, b, a):
    return lfilter(b, a, data)

# Parámetros de diseño de los filtros
orden = 6
# Filtro Pasa-Bajos (corta frecuencias mayores a 20 Hz para dejar la de 5 Hz)
b_lp, a_lp = butter_lowpass(cutoff=20.0, fs=fs, order=orden)
senal_filtrada_lp = aplicar_filtro(senal_con_ruido, b_lp, a_lp)

# Filtro Pasa-Altos (corta frecuencias menores a 30 Hz para dejar la de 50 Hz y el ruido)
b_hp, a_hp = butter_highpass(cutoff=30.0, fs=fs, order=orden)
senal_filtrada_hp = aplicar_filtro(senal_con_ruido, b_hp, a_hp)

# Filtro Pasa-Bandas (deja pasar solo el rango entre 30 Hz y 70 Hz, aislando la componente de 50 Hz)
b_bp, a_bp = butter_bandpass(lowcut=30.0, highcut=70.0, fs=fs, order=orden)
senal_filtrada_bp = aplicar_filtro(senal_con_ruido, b_bp, a_bp)

# ==========================================
# 3. VISUALIZACIÓN DE LOS RESULTADOS
# ==========================================
plt.figure(figsize=(12, 10))

# Señal original vs con ruido
plt.subplot(4, 1, 1)
plt.plot(t[:200], senal_con_ruido[:200], label='Señal con Ruido', color='gray', alpha=0.7)
plt.plot(t[:200], senal_pura[:200], label='Señal Pura (Referencia)', color='black', linewidth=1.5)
plt.title('Señal de Entrada: Pura vs. Con Ruido (Primeros 0.2 segundos)')
plt.xlabel('Tiempo (s)')
plt.ylabel('Amplitud')
plt.legend(loc='upper right')
plt.grid(True)

# Filtro Pasa-Bajos
plt.subplot(4, 1, 2)
plt.plot(t[:200], senal_filtrada_lp[:200], label='Filtrada Pasa-Bajos (Fc = 20 Hz)', color='blue')
plt.title('Efecto del Filtro Pasa-Bajos (Aislando componente de 5 Hz)')
plt.xlabel('Tiempo (s)')
plt.ylabel('Amplitud')
plt.legend(loc='upper right')
plt.grid(True)

# Filtro Pasa-Altos
plt.subplot(4, 1, 3)
plt.plot(t[:200], senal_filtrada_hp[:200], label='Filtrada Pasa-Altos (Fc = 30 Hz)', color='green')
plt.title('Efecto del Filtro Pasa-Altos (Atenuando bajas frecuencias)')
plt.xlabel('Tiempo (s)')
plt.ylabel('Amplitud')
plt.legend(loc='upper right')
plt.grid(True)

# Filtro Pasa-Bandas
plt.subplot(4, 1, 4)
plt.plot(t[:200], senal_filtrada_bp[:200], label='Filtrada Pasa-Bandas (30 Hz - 70 Hz)', color='red')
plt.title('Efecto del Filtro Pasa-Bandas (Aislando componente de 50 Hz)')
plt.xlabel('Tiempo (s)')
plt.ylabel('Amplitud')
plt.legend(loc='upper right')
plt.grid(True)

plt.tight_layout()
plt.show()