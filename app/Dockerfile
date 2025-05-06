FROM node:22.15.0 as build-stage
WORKDIR /var/www/html
COPY package*.json ./
RUN npm install

ARG API_URL

RUN if [ -n "$API_URL" ]; then echo "VITE_API_URL=${API_URL}" > .env.docker; fi


COPY . .
RUN npm run build

# estágio de produção
FROM nginx:1.24.0-alpine as production-stage
COPY --from=build-stage /var/www/html/dist /usr/share/nginx/html
COPY --from=build-stage /var/www/html/nginx.conf /etc/nginx/conf.d/default.conf
EXPOSE 5173
CMD ["nginx", "-g", "daemon off;"]