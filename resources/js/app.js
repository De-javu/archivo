import Dropzone from 'dropzone';
import 'dropzone/dist/dropzone.css';

Dropzone.autoDiscover = false;

document.addEventListener('DOMContentLoaded', function () {
    const dropzoneElement = document.getElementById('mi-dropzone');
    if (dropzoneElement) {
        new Dropzone("#mi-dropzone", {
            paramName: "file",
            dictDefaultMessage: "Arrastra y suelta archivos aquí o haz clic para seleccionar",
            init: function() {
                this.on("success", function(file, response) {
                    // Mostrar el nombre del archivo recibido en la respuesta
                    // Puedes ajustar esto según lo que devuelva tu backend
                    if (response.files && response.files.file) {
                        // Si es un solo archivo
                        alert('Archivo subido: ' + response.files.file.originalName);
                    } else {
                        // Si quieres ver toda la respuesta
                        alert('Respuesta del backend: ' + JSON.stringify(response));
                    }
                });
                this.on("error", function(file, errorMessage) {
                    alert('Error al subir el archivo');
                });
            }
        });
    }
});