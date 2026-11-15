#!/usr/bin/env bash
set -e

# ==============================================================================
# Institutional Parent Portal (Contineo Recreation) - Spring Boot 3 + React TSX
# Institution: Demo Institute of Engineering
# Backend: Java 21, Spring Boot 3.3.5 (:8080)
# Frontend: React 18, TypeScript, Vite (:5173 / embedded on :8080)
# ==============================================================================

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$ROOT_DIR"

export JAVA_HOME=${JAVA_HOME:-/opt/homebrew/opt/openjdk@21}
export PATH="$JAVA_HOME/bin:$PATH"

BACKEND_PORT=8080
FRONTEND_PORT=5173

echo "========================================================================"
echo " Starting Contineo Institutional Parent Portal"
echo " Stack: Java 21 Spring Boot 3 + React 18 TypeScript"
echo " Institution: Demo Institute of Engineering (DEMO – fictional data)"
echo "========================================================================"

# Free ports if already in use
for port in $BACKEND_PORT $FRONTEND_PORT; do
  if lsof -ti :$port >/dev/null 2>&1; then
    echo "Freeing port $port..."
    kill -9 $(lsof -ti :$port) 2>/dev/null || true
    sleep 1
  fi
done

echo ""
echo "------------------------------------------------------------------------"
echo " DEMO CREDENTIALS"
echo "------------------------------------------------------------------------"
echo " Parent / Student Portal:"
echo "   URL:      http://localhost:$BACKEND_PORT/"
echo "   Vite UI:  http://localhost:$FRONTEND_PORT/ (with HMR & dev proxy)"
echo "   Username: 1DM24CS001"
echo "   DOB:      Day: 15 | Month: Apr | Year: 2005"
echo ""
echo " Institutional Admin Portal:"
echo "   URL:      http://localhost:$BACKEND_PORT/admin"
echo "   Username: admin"
echo "   Password: admin@123"
echo "------------------------------------------------------------------------"
echo ""

# Start backend in background
echo "Starting Spring Boot 3 backend on http://localhost:$BACKEND_PORT..."
(cd backend && java -jar target/parent-portal-backend-1.0.0.jar) &
BACKEND_PID=$!

# Trap Ctrl+C to stop background processes cleanly
trap "echo 'Shutting down services...'; kill $BACKEND_PID 2>/dev/null || true; exit 0" SIGINT SIGTERM

# Wait for backend to be ready
echo "Waiting for backend on port $BACKEND_PORT..."
for i in {1..30}; do
  if curl -s http://localhost:$BACKEND_PORT/api/v1/public/terms >/dev/null 2>&1; then
    echo "Backend is live on http://localhost:$BACKEND_PORT!"
    break
  fi
  sleep 1
done

# Start frontend Vite dev server
echo "Starting Vite dev server on http://localhost:$FRONTEND_PORT..."
cd frontend
npm run dev -- --host 0.0.0.0 --port $FRONTEND_PORT
