import sys
import json
import os
import warnings
import contextlib
import base64  # <-- INI BARIS PENTING YANG HILANG
import numpy as np
from io import BytesIO
from PIL import Image

# --- Blok untuk membungkam error sampingan ---
warnings.filterwarnings('ignore')
os.environ['TF_CPP_MIN_LOG_LEVEL'] = '3' 
os.environ["DEEPFACE_HOME"] = "C:\\.deepface"
# --- Akhir Blok ---

# Import DeepFace setelah path diatur
from deepface import DeepFace

MODEL_NAME = "Dlib"

def get_image_from_file(file_path):
    with open(file_path, 'r') as f:
        base64_string = f.read()
    if ";base64," in base64_string:
        header, encoded = base64_string.split(",", 1)
    else:
        encoded = base64_string
    
    image_data = base64.b64decode(encoded)
    image = Image.open(BytesIO(image_data))
    if image.mode == 'RGBA':
        image = image.convert('RGB')
    return np.array(image)

def main(args):
    try:
        command = args[1]
        
        if command == "generate":
            image_path = args[2]
            image_np = get_image_from_file(image_path)
            try:
                embedding_objs = DeepFace.represent(img_path=image_np, model_name=MODEL_NAME, enforce_detection=True)
                embedding = embedding_objs[0]["embedding"]
                print(json.dumps({"success": True, "descriptor": embedding}))
            except Exception:
                print(json.dumps({"success": False, "message": "Server (DeepFace) gagal mendeteksi wajah pada gambar."}))

        elif command == "verify":
            live_image_path = args[2]
            registered_photo_path = args[3]
            try:
                result = DeepFace.verify(
                    img1_path=live_image_path,
                    img2_path=registered_photo_path,
                    model_name=MODEL_NAME,
                    enforce_detection=True
                )
                print(json.dumps({"success": True, "match": bool(result["verified"])}))
            except Exception:
                # Gagal jika wajah tidak terdeteksi di salah satu gambar
                print(json.dumps({"success": True, "match": False, "message": "Wajah tidak terdeteksi di salah satu gambar."}))
                
        else:
            print(json.dumps({"success": False, "message": "Perintah tidak dikenal."}))

    except Exception as e:
        print(json.dumps({"success": False, "message": str(e), "args": sys.argv}))

# Fungsi untuk membungkam stderr
@contextlib.contextmanager
def suppress_stderr():
    original_stderr = sys.stderr
    sys.stderr = open(os.devnull, 'w')
    try:
        yield
    finally:
        sys.stderr.close()
        sys.stderr = original_stderr

if __name__ == "__main__":
    with suppress_stderr():
        main(sys.argv)