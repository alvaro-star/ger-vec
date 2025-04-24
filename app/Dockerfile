# Etapa 1: build
FROM node:22 AS build

WORKDIR /app

# Copia os arquivos de dependências (package.json e package-lock.json)
COPY package*.json ./
RUN npm install

# Copia o restante do código do projeto
COPY . .

# Executa o build do Vue.js
RUN npm run build

# Etapa 2: servidor NGINX
FROM nginx:alpine

# Copia os arquivos gerados pelo build para o diretório do NGINX
COPY --from=build /app/dist /usr/share/nginx/html

# Remove o arquivo de configuração padrão do NGINX
RUN rm /etc/nginx/conf.d/default.conf

# Copia a configuração customizada do NGINX
COPY nginx.conf /etc/nginx/conf.d/

EXPOSE 80

# Comando para iniciar o NGINX
CMD ["nginx", "-g", "daemon off;"]
