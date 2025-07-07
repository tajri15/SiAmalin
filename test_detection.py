from deepface import DeepFace

try:
    print("Mencoba memuat gambar: test_image.jpg")
    
    # DeepFace.represent() akan mencoba mendeteksi wajah dan menghasilkan embedding
    # Jika gagal, akan melempar Exception
    embedding_obj = DeepFace.represent(img_path="test_image.jpg", model_name="Dlib", enforce_detection=True)
    
    if embedding_obj and len(embedding_obj) > 0:
         print(f"\n✅ BERHASIL! DeepFace berhasil mendeteksi wajah dan membuat embedding.")
    else:
        # Seharusnya tidak akan pernah sampai sini karena enforce_detection=True akan error jika tidak ada wajah
        print(f"\n❌ GAGAL! Tidak ada wajah yang ditemukan dalam gambar.")

except Exception as e:
    print(f"\n❌ GAGAL! DeepFace tidak dapat memproses gambar. Error: {e}")