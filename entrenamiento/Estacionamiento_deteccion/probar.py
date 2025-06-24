from ultralytics import YOLO
import cv2

# Cargar tu modelo entrenado
model = YOLO(r'C:/xampp/htdocs/Proyecto Auditoria/entrenamiento/Estacionamiento_deteccion/runs/detect/train2/weights/best.pt')  # ← Asegúrate de que la ruta sea correcta


# Abrir la cámara (0 = cámara predeterminada)
cap = cv2.VideoCapture(0)

if not cap.isOpened():
    print("Error al abrir la cámara.")
    exit()

while True:
    ret, frame = cap.read()
    if not ret:
        print("Error al leer el frame.")
        break

    # Hacer predicción en el frame original
    results = model.predict(source=frame, save=False, conf=0.5)
    annotated_frame = results[0].plot()


    # Redimensionar el frame anotado para la visualización
    max_side = 700
    height, width, _ = annotated_frame.shape

    if height > width:
        scale = max_side / height
    else:
        scale = max_side / width

    new_width = int(width * scale)
    new_height = int(height * scale)
    resized_annotated_frame = cv2.resize(annotated_frame, (new_width, new_height), interpolation=cv2.INTER_LINEAR)

    cv2.imshow("Detección en Vivo", resized_annotated_frame)

    # Presiona 'q' para salir
    if cv2.waitKey(1) & 0xFF == ord('q'):
        break

# Liberar recursos
cap.release()
cv2.destroyAllWindows()