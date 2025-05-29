# Etapa de build
FROM node:18 as build-stage

WORKDIR /app

COPY . .

RUN npm install

RUN npm run build

# Etapa de produção
FROM nginx:stable-alpine as production-stage

# Copia arquivos do build
COPY --from=build-stage /app/dist /usr/share/nginx/html

# Remove o arquivo default do nginx
RUN rm /etc/nginx/conf.d/default.conf

# Adiciona configuração customizada
COPY nginx.conf /etc/nginx/conf.d

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
