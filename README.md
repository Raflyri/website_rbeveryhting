# RBeverything - Your IT Consultant Partner

<p align="center">
    <img src="public/logo.png" width="200" alt="RBeverything Logo" onerror="this.style.display='none'">
</p>

RBeverything is a modern web platform providing a comprehensive suite of IT consultancy services and professional developer tools. Built with a focus on visual excellence and performance, it serves as both a service hub and a playground for high-quality web utilities.

## 🚀 Key Features

- **IT Services Hub**: A showcase of tailored technology solutions for businesses and individuals.
- **Developer Tools (SPA)**:
  - High-performance **Base64 Converter** and utilities.
  - Transitions between tools without page reloads for a desktop-like experience.
- **Advanced Admin Panel**: Powered by **Filament 3**, offering a seamless content management experience for pages, services, and settings.
- **Premium UI/UX**:
  - Modern design with glassmorphism and gradient accents.
  - Custom-designed, eye-catching error pages (404, 500, etc.).
  - Multi-language support (English, Indonesian, Malay, Japanese).
- **System Deployment**: Integrated deployment handlers for streamlined updates.

## 🛠 Tech Stack

- **Framework**: [Laravel 11](https://laravel.com)
- **Admin Panel**: [Filament v3](https://filamentphp.com)
- **Frontend**: [Tailwind CSS](https://tailwindcss.com), [Vite](https://vitejs.dev)
- **Language**: PHP 8.2+
- **Database**: SQLite (Default) / MySQL

## 💻 Getting Started

### Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js & NPM
- SQLite (or your preferred database engine)

### Local Setup

1. **Clone the repository**:

   ```bash
   git clone https://github.com/Raflyri/rbeverything.git
   cd rbeverything
   ```

2. **Install dependencies**:

   ```bash
   composer install
   npm install
   ```

3. **Environment configuration**:

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database migration & seeding**:

   ```bash
   php artisan migrate --seed
   ```

5. **Run the development server**:

   ```bash
   npm run dev
   ```

   *This command runs Vite and the Laravel server concurrently.*

## 📄 License

This project is licensed under the [MIT license](LICENSE).

---

*Built with ❤️ by [Raflyri](https://github.com/Raflyri)*
