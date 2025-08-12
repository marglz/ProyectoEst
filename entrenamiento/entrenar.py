from roboflow import Roboflow
from ultralytics import YOLO

# Descargar dataset desde Roboflow
rf = Roboflow(api_key="QJ6reay5wDOSVOn0T0lk")
project = rf.workspace("datasetbyte").project("estacionamiento-eum1g")
version = project.version(6)
dataset = version.download("yolov8")  # Esto descarga y crea el data.yaml

# Ruta al archivo data.yaml
data_yaml_path = f"{dataset.location}/data.yaml"

# Entrenar modelo YOLOv8
model = YOLO("yolov8n.pt")  # Puedes usar yolov8s.pt, yolov8m.pt, etc.

model.train(
    data=data_yaml_path,
    epochs=100,
    imgsz=640,
    batch=8,
    project="entrenamiento_estacionamiento",
    name="yolov8n_v6"
)
