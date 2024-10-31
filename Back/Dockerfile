# Dockerfile para el Backend en Python
FROM python:3.12.3

# Establece el directorio de trabajo
WORKDIR /app

# Copia los archivos de la aplicación
COPY . /app

# Instala las dependencias necesarias
RUN pip install -r requirements.txt

# Expone el puerto para Flask
EXPOSE 3500

# Comando para ejecutar la aplicación
CMD ["python", "serve.py"]




