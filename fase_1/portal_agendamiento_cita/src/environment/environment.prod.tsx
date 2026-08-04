// Archivo de configuración para producción
// Este archivo se ignora en desarrollo
// En Docker, usa rutas relativas porque nginx hace proxy a /api -> backend:80
// En producción externa, usa la URL pública de la API desde REACT_APP_API_URL
const apiUrl = process.env.REACT_APP_API_URL;
// Si no hay REACT_APP_API_URL, usar ruta relativa (para Docker con proxy nginx)
const urlBase: string = apiUrl 
    ? (apiUrl.endsWith('/') ? apiUrl : apiUrl + '/')
    : '/'; // Ruta relativa para usar el proxy de nginx

export const environment = {
    production: true,
    apiTiposCita: urlBase + 'api/tipos-cita',
    apiCitas: urlBase + 'api/citas',
    apiLogin: urlBase + 'api/login/',
    apiUsuarios: urlBase + 'api/usuarios',
}

