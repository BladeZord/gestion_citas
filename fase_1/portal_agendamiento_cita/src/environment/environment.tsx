
// Archivo de configuración para desarrollo
// Usa localhost cuando se ejecuta fuera de Docker
const apiUrl = process.env.REACT_APP_API_URL || 'http://127.0.0.1:8000/';
export const urlBase: string = apiUrl.endsWith('/') ? apiUrl : apiUrl + '/';

export const environment = {
    production: false,
    apiTiposCita: urlBase + 'api/tipos-cita',
    apiCitas: urlBase + 'api/citas',
    apiLogin: urlBase + 'api/login/',
    apiUsuarios: urlBase + 'api/usuarios',
}