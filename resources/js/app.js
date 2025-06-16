import Dropzone from 'dropzone';
import 'dropzone/dist/dropzone.css';

// Si quieres inicializar Dropzone manualmente, puedes hacerlo así:
Dropzone.autoDiscover = false;
new Dropzone("#mi-dropzone", {
    paramName: "file",
    dictDefaultMessage: "Arrastra y suelta archivos aquí o haz clic para seleccionar",
    // ...otras opciones
});