
from ultralytics import YOLO
import cv2
import json
import time
import os

# --- CONFIG ---
MODEL_PATH = "entrenamiento/entrenamiento_estacionamiento/yolov8n_v6/weights/best.pt"
JSON_PATH  = r"C:\xampp\htdocs\Proyecto\output\estado_cajones.json"
FRAME_PATH = r"C:\xampp\htdocs\Proyecto\output\frame.jpg"
CONF_THRES = 0.35   # umbral de confianza (ajusta si hace falta)

# Coordenadas (x1,y1,x2,y2) de cada cajón (usa el calibrador para obtenerlas)
cajones_roi = [
    (430, 493, 642, 600),
    (652, 496, 838, 608),
    (429, 390, 645, 488),
    (657, 396, 858, 492),
    (426, 288, 646, 384),
    (659, 289, 859, 390),
    (440, 180, 649, 281),
    (659, 181, 856, 284),
    (444, 84, 650, 177),
    (661, 79, 859, 174),
]


# Etiquetas que consideramos como "libre" (si el modelo usa otro nombre, agrégalo aquí)
FREE_LABELS = {'libre', 'free', 'empty', 'vacant'}

model = YOLO(MODEL_PATH)
cap = cv2.VideoCapture(0)
if not cap.isOpened():
    print("No se pudo abrir la cámara")
    exit(1)

def get_class_name(model, idx):
    names = model.names
    # model.names puede ser lista o dict
    if isinstance(names, dict):
        return names.get(idx, str(idx))
    else:
        return names[idx] if idx < len(names) else str(idx)

while True:
    ret, frame = cap.read()
    if not ret:
        print("Error leyendo frame")
        break

    # Predict
    results = model.predict(source=frame, save=False, conf=CONF_THRES)
    r = results[0]

    # Inicializa todos libres
    estado_cajones = [0] * len(cajones_roi)

    # Recolectar detecciones (x1,y1,x2,y2, conf, class_name)
    detecciones = []
    for box in r.boxes:
        # extraer coords y clase de la detección
        try:
            x1, y1, x2, y2 = box.xyxy[0].tolist()
        except:
            # fallback por si cambia la API
            arr = box.xyxy.cpu().numpy()[0]
            x1, y1, x2, y2 = arr.tolist()

        # confianza y clase
        try:
            conf = float(box.conf[0])
        except:
            conf = float(box.conf)
        try:
            cls_idx = int(box.cls[0])
        except:
            cls_idx = int(box.cls)

        class_name = get_class_name(model, cls_idx).lower()
        detecciones.append((int(x1), int(y1), int(x2), int(y2), conf, class_name))

    # Asignar detecciones a ROIs
    for (x1, y1, x2, y2, conf, class_name) in detecciones:
        cx = (x1 + x2) / 2
        cy = (y1 + y2) / 2
        for i, (rx1, ry1, rx2, ry2) in enumerate(cajones_roi):
            if rx1 <= cx <= rx2 and ry1 <= cy <= ry2:
                # Si la clase NO es 'libre', la tomamos como ocupada
                if class_name not in FREE_LABELS:
                    estado_cajones[i] = 1
                # si es 'libre' no forzamos 0 si ya hay una detección ocupada
                # (la prioridad la tiene 'ocupado' si aparece)
                # Por eso solo ponemos 1 y no reescribimos con 0 después.
                break

    # Dibujar detecciones y ROIs en la imagen para la web
    vis = frame.copy()
    # dibujar detecciones (cajas y etiquetas)
    for (x1, y1, x2, y2, conf, class_name) in detecciones:
        cv2.rectangle(vis, (x1, y1), (x2, y2), (255,255,255), 1)
        label = f"{class_name} {conf:.2f}"
        cv2.putText(vis, label, (x1, max(15, y1-5)), cv2.FONT_HERSHEY_SIMPLEX, 0.45, (255,255,255), 1)

    # dibujar ROIs con color según estado
    for i, (rx1, ry1, rx2, ry2) in enumerate(cajones_roi):
        color = (0,255,0) if estado_cajones[i] == 0 else (0,0,255)  # verde libre, rojo ocupado
        cv2.rectangle(vis, (rx1, ry1), (rx2, ry2), color, 2)
        cv2.putText(vis, f"C{i+1}", (rx1, ry1 - 6), cv2.FONT_HERSHEY_SIMPLEX, 0.6, color, 2)
        estado_text = "Libre" if estado_cajones[i] == 0 else "Ocupado"
        cv2.putText(vis, estado_text, (rx1, ry2 + 18), cv2.FONT_HERSHEY_SIMPLEX, 0.5, color, 1)

    # Guardar imagen (para la web)
    cv2.imwrite(FRAME_PATH, vis)

    # Guardar JSON atómico (primero temp, luego replace)
    tmp_path = JSON_PATH + ".tmp"
    try:
        with open(tmp_path, "w") as f:
            json.dump(estado_cajones, f)
        os.replace(tmp_path, JSON_PATH)
    except Exception as e:
        print("Error guardando JSON:", e)

    # Debug en consola
    print(f"[{time.strftime('%H:%M:%S')}] Cajones: {estado_cajones}")

    time.sleep(0.5)

cap.release()
