import numpy as np
import matplotlib.pyplot as plt

# ==========================================
# 1. Parámetros de la simulación
# ==========================================
fs = 1000  # Frecuencia de muestreo (Hz)
t = np.arange(-2, 2, 1/fs)  # Vector de tiempo de -2 a 2 segundos
N = len(t) # Número de muestras
frecuencias = np.fft.fftfreq(N, 1/fs) # Vector de frecuencias

# ==========================================
# 2. Definición de señales en el tiempo
# ==========================================
# Pulso rectangular (1 entre -0.5 y 0.5, 0 en el resto)
pulso_rect = np.where((t >= -0.5) & (t <= 0.5), 1, 0)

# Función escalón (1 para t >= 0, 0 para t < 0)
escalon = np.where(t >= 0, 1, 0)

# Función senoidal (frecuencia de 5 Hz)
f0 = 5
senoidal = np.sin(2 * np.pi * f0 * t)

# ==========================================
# 3. Cálculo de la Transformada de Fourier
# ==========================================
# Se utiliza np.fft.fft y se aplica fftshift para centrar en 0 Hz
fft_rect = np.fft.fftshift(np.fft.fft(pulso_rect)) / N
fft_escalon = np.fft.fftshift(np.fft.fft(escalon)) / N
fft_senoidal = np.fft.fftshift(np.fft.fft(senoidal)) / N
freqs_shift = np.fft.fftshift(frecuencias)

# ==========================================
# 4. Función auxiliar para graficar
# ==========================================
def graficar_senal_y_espectro(t, senal, freqs, fft_senal, titulo):
    plt.figure(figsize=(12, 4))
    
    # Dominio del tiempo
    plt.subplot(1, 3, 1)
    plt.plot(t, senal)
    plt.title(f'{titulo} (Tiempo)')
    plt.xlabel('Tiempo (s)')
    plt.ylabel('Amplitud')
    plt.grid(True)
    
    # Magnitud del espectro
    plt.subplot(1, 3, 2)
    plt.plot(freqs, np.abs(fft_senal))
    plt.title('Espectro de Magnitud')
    plt.xlabel('Frecuencia (Hz)')
    plt.xlim(-20, 20) # Limitamos para ver mejor
    plt.grid(True)
    
    # Fase del espectro
    plt.subplot(1, 3, 3)
    # Ignorar la fase donde la magnitud es muy pequeña para evitar ruido
    fase = np.angle(fft_senal)
    fase[np.abs(fft_senal) < 1e-3] = 0
    plt.plot(freqs, fase)
    plt.title('Espectro de Fase')
    plt.xlabel('Frecuencia (Hz)')
    plt.xlim(-20, 20)
    plt.grid(True)
    
    plt.tight_layout()
    plt.show()

# Visualización de señales base
graficar_senal_y_espectro(t, pulso_rect, freqs_shift, fft_rect, "Pulso Rectangular")
graficar_senal_y_espectro(t, escalon, freqs_shift, fft_escalon, "Función Escalón")
graficar_senal_y_espectro(t, senoidal, freqs_shift, fft_senoidal, "Señal Senoidal (5 Hz)")

# ==========================================
# 5. Análisis de Propiedades de Fourier
# ==========================================

# a) Linealidad: F{a*x(t) + b*y(t)} = a*X(f) + b*Y(f)
senal_suma = 2 * pulso_rect + 0.5 * senoidal
fft_suma = np.fft.fftshift(np.fft.fft(senal_suma)) / N
graficar_senal_y_espectro(t, senal_suma, freqs_shift, fft_suma, "Linealidad (2*Rect + 0.5*Sen)")

# b) Desplazamiento en el tiempo: x(t - t0)
# Desplazamos el pulso rectangular 1 segundo a la derecha
pulso_desplazado = np.where((t >= 0.5) & (t <= 1.5), 1, 0)
fft_desplazado = np.fft.fftshift(np.fft.fft(pulso_desplazado)) / N
graficar_senal_y_espectro(t, pulso_desplazado, freqs_shift, fft_desplazado, "Desplazamiento (Pulso t-1)")

# c) Escalamiento en el tiempo (y frecuencia): x(at)
# Comprimimos el pulso rectangular en el tiempo (a = 2)
pulso_comprimido = np.where((t >= -0.25) & (t <= 0.25), 1, 0)
fft_comprimido = np.fft.fftshift(np.fft.fft(pulso_comprimido)) / N
graficar_senal_y_espectro(t, pulso_comprimido, freqs_shift, fft_comprimido, "Escalamiento (Pulso comprimido)")