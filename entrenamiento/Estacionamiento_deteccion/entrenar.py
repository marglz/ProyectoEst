# 1. Instalar Roboflow y Ultralytics (solo por si no los tienes)
# pip install roboflow ultralytics

# 2. Importar las librerías necesarias
from roboflow import Roboflow
from ultralytics import YOLO

# 3. Conectarte a Roboflow con tu API KEY
rf = Roboflow(api_key="QJ6reay5wDOSVOn0T0lk")

# 4. Seleccionar el workspace y el proyecto correctos
project = rf.workspace("datasetbyte").project("estacionamiento-eum1g")

# 5. Elegir la versión de tu dataset (V2 en tu caso)
version = project.version(3)

# 6. Descargar el dataset en formato YOLOv5
dataset = version.download("yolov5")

# 7. Crear el modelo YOLOv8 (se puede entrenar desde cero o usar uno preentrenado)
# Vamos a usar un modelo pequeño (yolov8n.pt) para empezar
model = YOLO("yolov8n.pt")  # También puedes probar "yolov8s.pt" si quieres algo más grande

# 8. Entrenar el modelo
# Se entrena usando el archivo de configuración data.yaml que descargó Roboflow
results = model.train(
    data=dataset.location + "/data.yaml",  # Ruta al archivo data.yaml
    epochs=100,  # Número de veces que verá todo el dataset (puedes ajustar)
    imgsz=640,   # Tamaño de las imágenes (640x640 recomendado)
    batch=8,     # Número de imágenes que procesa al mismo tiempo (ajusta si tienes poca RAM)
)

# 9. Al terminar, tendrás el archivo "best.pt" en la carpeta "runs/detect/train/"

print("✅ Entrenamiento terminado. El modelo 'best.pt' está listo en 'runs/detect/train/'")

