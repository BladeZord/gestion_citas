// Helper para obtener headers de autenticación
import { authService } from '../../autenticacion/services/auth.service';

export const getAuthHeaders = (): { [key: string]: string } => {
    const token = authService.getToken();
    
    if (!token) {
        throw new Error('No hay token de autenticación. Por favor, inicia sesión.');
    }

    return {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
    };
};

