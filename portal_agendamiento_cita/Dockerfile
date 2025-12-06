# Etapa de construcción
FROM node:18-alpine AS builder

WORKDIR /app

# Copiar archivos de dependencias
COPY package*.json ./

# Instalar dependencias
RUN npm ci

# Copiar el resto de los archivos
COPY . .

# Construir la aplicación
ARG REACT_APP_API_URL
ENV REACT_APP_API_URL=$REACT_APP_API_URL
RUN npm run build

# Etapa de producción con Nginx
FROM nginx:alpine

# Copiar los archivos construidos
COPY --from=builder /app/build /usr/share/nginx/html

# Copiar configuración de nginx personalizada
COPY nginx.conf /etc/nginx/conf.d/default.conf

# Exponer puerto 80
EXPOSE 80

# Iniciar nginx
CMD ["nginx", "-g", "daemon off;"]

