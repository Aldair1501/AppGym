<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Golden Fitness Gym</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="icon" href="assets/image/gym_log.png">
</head>
<body class="bg-black text-white font-sans leading-relaxed scroll-smooth">

<header class="sticky top-0 z-50 bg-gradient-to-r from-yellow-400 via-orange-400 to-yellow-500 shadow-md text-black p-4">
  <div class="max-w-7xl mx-auto flex justify-between items-center px-4 md:px-0">
    <!-- Logo -->
    <a href="#" class="flex items-center gap-3" aria-label="Golden Fitness Gym - Inicio">
      <img src="assets/image/gym_log.png" alt="Golden Fitness Gym Logo" class="h-14 w-auto transition-transform duration-300 hover:scale-105" />
      <h1 class="text-3xl font-extrabold tracking-tight select-none">Golden Fitness Gym</h1>
    </a>

    <!-- Navegación desktop -->
    <nav class="hidden md:flex space-x-8 text-lg font-semibold">
      <a href="#mision" class="relative group text-black hover:text-white cursor-pointer transition-colors duration-300">
        Misión
        <span class="absolute left-0 -bottom-1 w-0 h-0.5 bg-white transition-all group-hover:w-full"></span>
      </a>
      <a href="#vision" class="relative group text-black hover:text-white cursor-pointer transition-colors duration-300">
        Visión
        <span class="absolute left-0 -bottom-1 w-0 h-0.5 bg-white transition-all group-hover:w-full"></span>
      </a>
      <a href="#valores" class="relative group text-black hover:text-white cursor-pointer transition-colors duration-300">
        Valores
        <span class="absolute left-0 -bottom-1 w-0 h-0.5 bg-white transition-all group-hover:w-full"></span>
      </a>
      <a href="#ubicacion" class="relative group text-black hover:text-white cursor-pointer transition-colors duration-300">
        Ubicación
        <span class="absolute left-0 -bottom-1 w-0 h-0.5 bg-white transition-all group-hover:w-full"></span>
      </a>
      <a href="#horarios" class="relative group text-black hover:text-white cursor-pointer transition-colors duration-300">
        Horarios
        <span class="absolute left-0 -bottom-1 w-0 h-0.5 bg-white transition-all group-hover:w-full"></span>
      </a>
    </nav>

    <!-- Botón menú móvil -->
    <button id="menu-btn" aria-label="Abrir menú" class="md:hidden focus:outline-none focus:ring-2 focus:ring-yellow-300">
      <svg class="w-8 h-8 text-black" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4 8h16M4 16h16" />
      </svg>
    </button>
  </div>

  <!-- Menú móvil (inicialmente oculto) -->
  <nav id="mobile-menu" class="hidden md:hidden bg-yellow-400 text-black px-6 py-4 space-y-4">
    <a href="#mision" class="block font-semibold hover:text-white transition-colors duration-300">Misión</a>
    <a href="#vision" class="block font-semibold hover:text-white transition-colors duration-300">Visión</a>
    <a href="#valores" class="block font-semibold hover:text-white transition-colors duration-300">Valores</a>
    <a href="#ubicacion" class="block font-semibold hover:text-white transition-colors duration-300">Ubicación</a>
    <a href="#horarios" class="block font-semibold hover:text-white transition-colors duration-300">Horarios</a>
  </nav>

  <script>
    // Script para toggle menú móvil
    const btn = document.getElementById('menu-btn');
    const menu = document.getElementById('mobile-menu');
    btn.addEventListener('click', () => {
      menu.classList.toggle('hidden');
    });
  </script>
</header>



  <!-- INTRO -->
  <section class="text-center py-12 px-4 bg-black">
    <h2 class="text-4xl font-extrabold text-orange-500 mb-4">Tu mejor versión empieza aquí</h2>
    <p class="text-lg max-w-2xl mx-auto text-gray-300">
      Golden Fitness Gym es el espacio donde transformas tu cuerpo, tu mente y tu vida. Más que un gimnasio, una comunidad enfocada en resultados reales.
    </p>
  </section>

  <!-- MISIÓN -->
  <section id="mision" class="py-10 px-4 bg-zinc-900">
    <div class="max-w-4xl mx-auto">
      <h2 class="text-3xl font-bold text-yellow-400 mb-4">Misión</h2>
      <p class="text-gray-200 text-lg">
       En Golden Fitness Gym ayudamos a las personas a mejorar su salud física y mental a través del ejercicio. Brindamos un espacio accesible, seguro y motivador, donde cada usuario ya sea principiante o avanzado recibe atención cercana, rutinas personalizadas y apoyo constante. Como gimnasio local, trabajamos con compromiso y pasión para promover una cultura de bienestar que inspire hábitos saludables, esfuerzo y crecimiento personal.
      </p>
    </div>
  </section>

  <!-- VISIÓN -->
  <section id="vision" class="py-10 px-4 bg-zinc-800">
    <div class="max-w-4xl mx-auto">
      <h2 class="text-3xl font-bold text-yellow-400 mb-4">Visión</h2>
      <p class="text-gray-200 text-lg">
        Queremos ser un gimnasio líder en nuestra comunidad y en la región, reconocido por la calidad de nuestros servicios, el trato cercano y nuestro compromiso con el bienestar integral. Aspiramos a motivar a las personas a transformar su vida a través del ejercicio, fomentando valores como la disciplina, la superación y la salud en un ambiente inclusivo y positivo. Nos proyectamos como una empresa en constante crecimiento, innovando en entrenamientos, mejorando nuestras instalaciones y fortaleciendo la relación con cada usuario, siempre con pasión por el deporte y el deseo de generar un impacto real en la sociedad.
      </p>
    </div>
  </section>

  <!-- VALORES -->
  <section id="valores" class="py-10 px-4 bg-zinc-900">
    <div class="max-w-4xl mx-auto">
      <h2 class="text-3xl font-bold text-yellow-400 mb-6">Nuestros Valores</h2>
      <ul class="list-disc list-inside text-gray-200 space-y-2 text-lg">
        <li><strong>Disciplina:</strong> Fomentamos la constancia como base del éxito personal.</li>
        <li><strong>Respeto:</strong> Promovemos un ambiente inclusivo, libre de juicios.</li>
        <li><strong>Compromiso:</strong> Nos dedicamos a apoyar el progreso de cada miembro.</li>
        <li><strong>Excelencia:</strong> Buscamos superar las expectativas en cada detalle.</li>
        <li><strong>Responsabilidad:</strong> Actuamos con ética y profesionalismo.</li>
        <li><strong>Mejora continua:</strong> Evolucionamos con cada paso para ser mejores cada día.</li>
      </ul>
    </div>
  </section>

  <!-- GALERÍA -->
<!-- GALERÍA -->
<section id="galeria" class="py-10 px-4 bg-gradient-to-r from-zinc-900 via-zinc-800 to-zinc-900">
  <div class="max-w-6xl mx-auto">
    <h2 class="text-3xl font-bold text-yellow-400 mb-8 text-center tracking-wide">Galería</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8">
      <img src="assets/image/foto1.jpg" alt="Área de pesas" class="rounded-lg shadow-xl object-cover w-full h-52 hover:scale-105 transition-transform duration-300" />
      <img src="https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?auto=format&fit=crop&w=400&q=80" alt="Clase grupal" class="rounded-lg shadow-xl object-cover w-full h-52 hover:scale-105 transition-transform duration-300" />
      <img src="assets/image/foto2.jpg" alt="Entrenador ayudando a cliente" class="rounded-lg shadow-xl object-cover w-full h-52 hover:scale-105 transition-transform duration-300" />
      <img src="assets/image/foto3.jpg" alt="Zona cardio" class="rounded-lg shadow-xl object-cover w-full h-52 hover:scale-105 transition-transform duration-300" />
      <img src="assets/image/foto4.jpeg" alt="Ejercicio con pesas" class="rounded-lg shadow-xl object-cover w-full h-52 hover:scale-105 transition-transform duration-300" />
      <img src="assets/image/foto5.jpg" alt="Ambiente moderno" class="rounded-lg shadow-xl object-cover w-full h-52 hover:scale-105 transition-transform duration-300" />
    </div>
  </div>
</section>


  <!-- UBICACIÓN -->
<section id="ubicacion" class="py-10 px-4 bg-zinc-800">
  <div class="max-w-4xl mx-auto text-center">
    <h2 class="text-3xl font-bold text-yellow-400 mb-4">¿Dónde estamos?</h2>
    <p class="text-gray-200 mb-2 text-lg">Visítanos en nuestra sede central:</p>
    <p class="text-white text-xl font-semibold mb-6">Av. Principal, Zona 1, Ciudad Guatemala</p>
    <div class="relative w-full h-0 pb-[56.25%] overflow-hidden rounded-xl shadow-lg">
      <iframe 
        class="absolute top-0 left-0 w-full h-full border-0" 
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3859.916993511095!2d-90.8258937250748!3d14.660651885832793!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x85896de37f0e0e57%3A0xf3889f404ec4f790!2sGolden%20Fitness%20Gym%20Chimaltenango!5e0!3m2!1ses!2sgt!4v1754345284418!5m2!1ses!2sgt" 
        allowfullscreen 
        loading="lazy" 
        referrerpolicy="no-referrer-when-downgrade">
      </iframe>
    </div>
  </div>
</section>

 <!-- HORARIOS -->
<section id="horarios" class="py-16 px-4 bg-zinc-900">
  <div class="max-w-3xl mx-auto text-center">
    <h2 class="text-4xl font-bold text-yellow-400 mb-8">Horarios de atención</h2>
    <div class="bg-zinc-800 rounded-xl shadow-lg p-6 space-y-4 text-left">
      <div class="flex items-center justify-between text-lg text-gray-300">
        <span class="flex items-center gap-2">
          <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
          Lunes a Viernes
        </span>
        <span class="font-semibold text-white">5:00 AM – 9:00 PM</span>
      </div>
      <div class="flex items-center justify-between text-lg text-gray-300">
        <span class="flex items-center gap-2">
          <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          Sábados
        </span>
        <span class="font-semibold text-white">6:00 AM – 12:00 PM</span>
      </div>
      <div class="flex items-center justify-between text-lg text-gray-300">
        <span class="flex items-center gap-2">
          <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 5.636a9 9 0 010 12.728M5.636 5.636a9 9 0 0112.728 0M3 12h.01M21 12h.01"/></svg>
          Domingos y Feriados
        </span>
        <span class="font-semibold text-white">Cerrado</span>
      </div>
    </div>
  </div>
</section>


  <!-- WHATSAPP FLOATING -->
  <a href="https://wa.me/+50254210344" target="_blank"
     class="fixed bottom-6 right-6 bg-green-500 hover:bg-green-600 text-white p-3 rounded-full shadow-lg transition transform hover:scale-110">
    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="w-6 h-6" viewBox="0 0 24 24">
      <path d="M12.04 0C5.4 0 .04 5.36.04 12a11.88 11.88 0 001.57 5.89L0 24l6.3-1.65A11.95 11.95 0 0012.04 24c6.64 0 12-5.36 12-12S18.68 0 12.04 0zm6.64 17.36c-.28.78-1.6 1.5-2.2 1.58-.58.08-1.28.12-2.06-.13-.47-.16-1.07-.35-1.85-.7-3.26-1.42-5.38-4.93-5.54-5.17-.16-.24-1.32-1.77-1.32-3.38s.84-2.42 1.14-2.76c.3-.34.66-.42.88-.42h.64c.22 0 .48-.03.74.56.26.6.9 2.05.98 2.2.08.14.14.3.02.54-.12.24-.18.38-.34.58-.16.2-.34.44-.48.6-.16.2-.32.42-.14.82.18.38.8 1.33 1.7 2.15 1.16 1.04 2.1 1.36 2.5 1.52.4.16.64.14.88-.1.24-.24 1.02-1.18 1.3-1.58.28-.4.56-.34.94-.2.38.14 2.4 1.14 2.8 1.34.4.2.66.3.76.46.1.16.1.92-.18 1.7z"/>
    </svg>
  </a>

  <!-- FOOTER -->
  <footer class="bg-black text-gray-400 text-center p-6 text-sm">
    &copy; 2025 Golden Fitness Gym. Todos los derechos reservados.
  </footer>

</body>
</html>
