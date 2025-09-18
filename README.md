# 🖥️ Proyecto Laravel 12

Este proyecto está desarrollado con **Laravel 12**, sobre **PHP 8.3.20** y **MySQL 9.3.0**.  
Se integran librerías externas y patrones de diseño para una arquitectura más escalable y mantenible.

---

## 📦 Requisitos previos

Asegúrate de tener instalado:

- [PHP](https://www.php.net/) **8.3.20**
- [Composer](https://getcomposer.org/) >= 2.x
- [MySQL](https://dev.mysql.com/) **9.3.0**

---

## ⚙️ Instalación del proyecto

Clona el repositorio y entra en la carpeta del proyecto:

```bash
git clone https://github.com/charlyrm14/ecommerce-lv.git
cd mi-proyecto-laravel
```
## Instalar dependencias
```bash
composer install
```

## Archivo de entorno
```bash
cp .env.example .env
php artisan key:generate
```

## Ejecutar migraciones
```bash
php artisan migrate
```

## Levantar el servidor de desarrollo
```bash
php artisan serve
```
🧰 Librerías externas utilizadas
🔹 Laravel Passport

Gestión de autenticación OAuth2 y JWT tokens.
Instalación básica:

```bash
php artisan install:api --passport
```

## Patrones de diseño implementados

Este proyecto implementa los siguientes patrones de diseño:

## Strategy

Se usa para manejar el guardado de archivos en el servidor aplicando diferentes lógicas según el tipo de archivo:

- Imágenes → Se generan distintas versiones (ejemplo: original, thumbnail, tamaños adaptados).
- Videos → Se planea soportar distintas resoluciones y conversiones.
- Otros archivos → Se podrán manejar reglas personalizadas en el futuro.

Esto permite encapsular cada lógica de procesamiento de archivos en estrategias independientes, manteniendo el código limpio y flexible.

## Observer

- Se utiliza para escuchar eventos de los modelos y reaccionar automáticamente a ciertas acciones:
- En los eventos created de Producto, Categoría y Marca se genera automáticamente un slug en base al nombre o título.

Esto asegura consistencia en las URL amigables y evita lógica repetida en los controladores.

🧑‍💻 Scripts disponibles

- php artisan serve → Levanta el servidor en modo desarrollo.
- php artisan migrate → Ejecuta migraciones.
- php artisan db:seed → Carga datos iniciales.
