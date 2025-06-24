from ultralytics import YOLO
import cv2
import json
import time
import os

# Cargar modelo YOLO
model = YOLO("entrenamiento/Estacionamiento_deteccion/runs/detect/train2/weights/best.pt")

# Abrir la cámara
cap = cv2.VideoCapture(0)

# Verificar si la cámara está disponible
if not cap.isOpened():
    print("No se pudo acceder a la cámara")
    exit()

# Bucle de ejecución continua
while True:
    ret, frame = cap.read()
    if not ret:
        print("Error al leer el frame")
        break

    # Detección con YOLO
    results = model.predict(source=frame, save=False, conf=0.5)
    boxes = results[0].boxes

    # Simulación de ocupación: detectar número de vehículos (clase "car", "truck", etc.)
    num_cajones = 10
    ocupados = min(len(boxes), num_cajones)  # ← Aquí adaptas según tu lógica real
    estado_cajones = [1 if i < ocupados else 0 for i in range(num_cajones)]

    # Guardar frame anotado para mostrar en la web
    annotated_frame = results[0].plot()
    frame_path = "C:/xampp/htdocs/Proyecto Auditoria/output/frame.jpg"
    cv2.imwrite(frame_path, annotated_frame)

    # Guardar JSON para que PHP lo lea
    json_path = "C:/xampp/htdocs/Proyecto Auditoria/output/estado_cajones.json"
    with open(json_path, "w") as f:
        json.dump(estado_cajones, f)

    print(f"[{time.strftime('%H:%M:%S')}] Cajones: {estado_cajones}")

    # Espera 3 segundos antes de volver a analizar
    time.sleep(3)

# Liberar recursos si se sale del bucle (en caso de error)
cap.release()
