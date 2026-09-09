# Cord — Discord-like chat server
# Port 555 (published by compose / docker run).
FROM node:20-alpine

ENV NODE_ENV=production
WORKDIR /app

# Install dependencies first (better layer caching).
COPY package.json package-lock.json ./
RUN npm ci --omit=dev

# Application code.
COPY server.js ./
COPY public ./public

# Data directory for the persisted message history.
RUN mkdir -p /app/data && chown node:node /app/data

# Port 555 is < 1024: run as a non-root user and grant NET_BIND_SERVICE
# (compose file adds the capability; plain `docker run` needs --cap-add too).
USER node

EXPOSE 555

CMD ["node", "server.js"]