import cv2

# Abrir la cámara
cap = cv2.VideoCapture(0)

if not cap.isOpened():
    print("No se pudo abrir la cámara")
    exit()

# Leer un solo frame
ret, frame = cap.read()
if not ret:
    print("No se pudo leer el frame")
else:
    # Mostrar la imagen capturada
    cv2.imshow("Captura única", frame)
    cv2.waitKey(0)  # Espera hasta que presiones una tecla

# Liberar la cámara y cerrar la ventana
cap.release()
cv2.destroyAllWindows()

