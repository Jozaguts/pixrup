PIXRUP — MASTER TODO LIST
Prioridad: Frontend → UX → Integraciones → Backend
## 1. FRONTEND — CAMBIOS URGENTES
###  1.1 Cambiar “Living Area” → “Outside Area”

Nuevo dropdown:

Balcony

Front Yard

Back Yard

Swimming Pool

Tareas

Reemplazar labels en todas las vistas

Actualizar componentes y validaciones

Mostrar valor en Overview + report preview

###  1.2 Tarjetas estilo “Home Buttons”

Aplicar estilo de botones grandes a todas las tarjetas de métricas (Worth, SpyHunt, Overview).

Tareas

Rediseñar cards

Radius 16px, sombra suave, icono + texto centrado

Responsivo para desktop y mobile

###  1.3 Mostrar Rental Value (PixrWorth)

Si HouseCanary devuelve renta → mostrar tarjeta.
Si no existe → mostrar mensaje:

“Rental projections are not available yet. Fetch a fresh valuation or add rental data to unlock this card.”

Tareas

Nueva card Rental Value

Estado vacío con mensaje

Estado normal con valor

###  1.4 GlowUp — IA Agent + Prompt Automático

Flujo:

Subir foto

IA describe

Generar prompt final

Enviar a Replicate

Tareas

UI de carga

Mostrar estado “AI analyzing photo…”

Integrar descripción IA

Crear prompt final

Enviar tarea GlowUp

## 2. FRONTEND — PLANES, USO Y LIMITES
###  2.1 Planes disponibles

Basic

Premium

Enterprise

Tareas

Mostrar cards de planes

Ajustar vistas de billing

Diseñar estilo tipo home buttons

###  2.2 Mostrar uso por plan dentro de cada módulo

Debe mostrarse Used X / Limit Y

Si se alcanza → popup:

“You reached your monthly limit. Upgrade your plan to continue.”

Tareas

Crear widget de progreso

Insertar en cada módulo

Mostrar popup al llegar al límite

###  2.3 Dashboard — Usage Global

Tareas

Barra o card con uso total

Botón Upgrade Plan

## 3. WEBSITE — SECCIONES NUEVAS
###  3.1 Blog Section

Página /blog

Grid de artículos

Página individual /blog/{slug}

Link en navbar

###  3.2 Contact Us Section

Basado en referencia enviada.

Tareas

Formulario: nombre, email, mensaje

Mensaje de confirmación

Insertar sección en landing o página independiente

## 4. MARKET OVERVIEW — NUEVOS VALORES

Agregar métricas desde HouseCanary Analytics:

Avg Days on Market

Trend 30D

County / Zip Avg Sale Price

Comps Count

Avg Sale Price Per SqFt

Avg Rent Price Per SqFt (si aplica)

Tareas

Crear nuevas cards estilo home buttons

Colorear tendencias (positivo, negativo)

Añadir flechas ↑ ↓ →

## 5. PIXRSPYHUNT — ELEMENTOS FALTANTES

Ya listo:
✔ Mapa
✔ Hover dinámico sobre pins

Faltante:

Cards de métricas:

Avg Sale Price

Avg Rent Price

Avg Price per SqFt

Trend 30D

Avg Days on Market

Comparaciones con la propiedad:

% arriba/abajo del avg sale price

% arriba/abajo del avg price per sq ft

## 6. BACKEND (SECOND PRIORITY)
###  6.1 Validación de Rental Data en HouseCanary

Revisar si endpoints entregan rental value, rental projections, trends

Si no existen → enviar null para activar mensaje en UI

###  6.2 GlowUp — Pipeline IA Agent

Endpoint para procesar foto

Llamar IA para descripción

Generar prompt final

Enviar tarea a Replicate

Guardar resultado

## 7. UI — ESTILO PIXRUP (GLOBAL)

Radius 16px

Sombras suaves

Color primario #7C4DFF

Tipografía Inter / Poppins

Consistencia en todos los módulos

✔ Archivo listo.

Si quieres, te lo genero como archivo descargable .md listo para colocar en tu repo o en Jira.