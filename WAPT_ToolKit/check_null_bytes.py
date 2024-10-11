def find_null_bytes(file_path):
    with open(file_path, 'rb') as file:
        content = file.read()
        if b'\x00' in content:
            print(f"Null bytes found in {file_path}")
        else:
            print(f"No null bytes found in {file_path}")

# Replace 'app/__init__.py' and 'config.py' with the paths to your files
find_null_bytes('app/__init__.py')
find_null_bytes('config.py')

