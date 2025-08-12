import cv2

# Abrir cámara
cap = cv2.VideoCapture(0)

if not cap.isOpened():
    print("No se pudo acceder a la cámara")
    exit()

drawing = False
start_point = ()
end_point = ()
rectangles = []

# Función de callback para mouse
def draw_rectangle(event, x, y, flags, param):
    global start_point, end_point, drawing, rectangles

    if event == cv2.EVENT_LBUTTONDOWN:
        drawing = True
        start_point = (x, y)

    elif event == cv2.EVENT_MOUSEMOVE:
        if drawing:
            end_point = (x, y)

    elif event == cv2.EVENT_LBUTTONUP:
        drawing = False
        end_point = (x, y)
        rectangles.append((start_point[0], start_point[1], end_point[0], end_point[1]))
        print(f"Cajón {len(rectangles)}: ({start_point[0]}, {start_point[1]}, {end_point[0]}, {end_point[1]})")

cv2.namedWindow("Calibrar Cajones")
cv2.setMouseCallback("Calibrar Cajones", draw_rectangle)

while True:
    ret, frame = cap.read()
    if not ret:
        break

    # Dibujar rectángulo temporal mientras se arrastra
    if drawing and start_point and end_point:
        cv2.rectangle(frame, start_point, end_point, (0, 255, 0), 2)

    # Dibujar rectángulos guardados
    for (x1, y1, x2, y2) in rectangles:
        cv2.rectangle(frame, (x1, y1), (x2, y2), (0, 0, 255), 2)

    cv2.imshow("Calibrar Cajones", frame)

    # Tecla 'q' para salir
    if cv2.waitKey(1) & 0xFF == ord('q'):
        break

cap.release()
cv2.destroyAllWindows()

# Imprimir todas las coordenadas al final
print("\nCoordenadas finales:")
for i, rect in enumerate(rectangles):
    print(f"Cajón {i+1}: {rect}")
