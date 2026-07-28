<script setup>
import { Link } from '@inertiajs/vue3'
import { login } from '@/routes'
import { ref, onMounted, onBeforeUnmount, nextTick } from 'vue'
import gsap from 'gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'
import Lenis from 'lenis'

defineProps({
  canRegister: Boolean
})

const isMenuOpen = ref(false)
const heroRef = ref(null)
const navbarRef = ref(null)
const cardsRef = ref(null)
const illustrationRef = ref(null)
const leftContentRef = ref(null)
const particlesContainer = ref(null)

let lenis = null
let mouseX = 0
let mouseY = 0
let particles = []

const navItems = [
  { label: 'Inicio', href: '#hero' },
  { label: 'Servicios', href: '#servicios' },
  { label: 'Portafolio', href: '#portafolio' },
  { label: 'Precios', href: '#precios' },
  { label: 'Contacto', href: '#contacto' }
]

const serviceCards = [
  {
    title: 'Diseño Gráfico',
    desc: 'Identidad visual, branding, piezas publicitarias y diseño editorial que conectan con tu audiencia.',
    gradient: 'from-[#00B8C9] to-[#146C94]',
    icon: 'Palette'
  },
  {
    title: 'Fotografía Profesional',
    desc: 'Sesiones fotográficas comerciales, de producto, eventos y retratos con calidad de estudio.',
    gradient: 'from-[#FF6B6B] to-[#FF9F1C]',
    icon: 'Camera'
  },
  {
    title: 'Edición y Retoque',
    desc: 'Postproducción profesional, restauración de imágenes, colorización y mejora de calidad visual.',
    gradient: 'from-[#4ECDC4] to-[#00B8C9]',
    icon: 'Wand'
  },
  {
    title: 'Impresión',
    desc: 'Impresión de alta calidad en distintos formatos y materiales para tus proyectos creativos.',
    gradient: 'from-[#F4C542] to-[#FF9F1C]',
    icon: 'Printer'
  }
]

const stats = [
  { value: '500+', label: 'Proyectos', color: 'text-[#00B8C9]' },
  { value: '98%', label: 'Satisfacción', color: 'text-[#FF6B6B]' },
  { value: '24h', label: 'Respuesta', color: 'text-[#F4C542]' },
  { value: '50+', label: 'Clientes', color: 'text-[#4ECDC4]' }
]

const features = [
  { icon: 'ShieldCheck', title: 'Calidad Premium', desc: 'Diseño profesional de alto nivel' },
  { icon: 'Timer', title: 'Entrega Rápida', desc: 'Proyectos en tiempo récord' },
  { icon: 'BadgePercent', title: 'Precios Justos', desc: 'Sin comprometer la calidad' }
]

const portfolio = [
  { category: 'Branding', title: 'Identidad Corporativa', image: 'https://images.unsplash.com/photo-1561070791-2526d30994b5?w=600&h=400&fit=crop' },
  { category: 'Fotografía', title: 'Campamento Visual', image: 'https://images.unsplash.com/photo-1519741497674-611481863552?w=600&h=400&fit=crop' },
  { category: 'Diseño', title: 'Packaging Premium', image: 'https://images.unsplash.com/photo-1542744094-3a31f272c490?w=600&h=400&fit=crop' },
  { category: 'Edición', title: 'Retrato Editorial', image: 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=600&h=400&fit=crop' }
]

const testimonials = [
  {
    name: 'María González',
    role: 'CEO, TechStart',
    content: 'CREAPIX transformó completamente nuestra identidad de marca. El equipo entendió perfectamente nuestra visión.',
    avatar: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=100&h=100&fit=crop&crop=face'
  },
  {
    name: 'Carlos Rivera',
    role: 'Director Marketing',
    content: 'La calidad y profesionalismo son excepcionales. Cada proyecto supera nuestras expectativas.',
    avatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&h=100&fit=crop&crop=face'
  },
  {
    name: 'Ana Martínez',
    role: 'Propietaria, Boutique',
    content: 'Incrementaron nuestro engagement en redes sociales en un 300%. Altamente recomendados.',
    avatar: 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=100&h=100&fit=crop&crop=face'
  }
]

function handleMouseMove(e) {
  mouseX = (e.clientX / window.innerWidth - 0.5) * 2
  mouseY = (e.clientY / window.innerHeight - 0.5) * 2
  if (illustrationRef.value) {
    gsap.to(illustrationRef.value, {
      x: mouseX * 20,
      y: mouseY * 20,
      duration: 2,
      ease: 'power2.out'
    })
  }
}

function createParticles() {
  if (!particlesContainer.value) return
  const container = particlesContainer.value
  for (let i = 0; i < 40; i++) {
    const particle = document.createElement('div')
    const size = Math.random() * 4 + 1
    const isCircle = Math.random() > 0.4
    particle.className = 'absolute pointer-events-none'
    particle.style.cssText = `
      width: ${size}px;
      height: ${size}px;
      ${isCircle
        ? `border-radius: 50%; background: ${['rgba(0,184,201,0.4)','rgba(255,107,107,0.3)','rgba(244,197,66,0.3)','rgba(78,205,196,0.3)','rgba(255,159,28,0.3)'][Math.floor(Math.random() * 5)]};`
        : `width: ${size * 2}px; height: 1px; background: rgba(47,53,66,0.1); border-radius: 1px; transform: rotate(${Math.random() * 360}deg);`
      }
      top: ${Math.random() * 100}%;
      left: ${Math.random() * 100}%;
      opacity: ${Math.random() * 0.6 + 0.2};
    `
    container.appendChild(particle)
    particles.push(particle)
    gsap.to(particle, {
      y: `random(${-30}, ${-80})`,
      x: `random(${-20}, ${20})`,
      duration: Math.random() * 8 + 6,
      repeat: -1,
      yoyo: true,
      ease: 'sine.inOut',
      delay: Math.random() * 5
    })
  }
}

onMounted(async () => {
  await nextTick()

  gsap.registerPlugin(ScrollTrigger)

  lenis = new Lenis({
    duration: 1.8,
    easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
    orientation: 'vertical',
    smoothWheel: true,
    wheelMultiplier: 1,
    touchMultiplier: 1.5
  })

  lenis.on('scroll', () => {
    ScrollTrigger.update()
  })

  requestAnimationFrame(function raf(time) {
    lenis.raf(time)
    requestAnimationFrame(raf)
  })

  createParticles()

  const heroTl = gsap.timeline({ defaults: { ease: 'power3.out' } })

  heroTl
    .fromTo(
      leftContentRef.value?.querySelector('.hero-badge'),
      { opacity: 0, y: 30, scale: 0.9 },
      { opacity: 1, y: 0, scale: 1, duration: 0.8 }
    )
    .fromTo(
      leftContentRef.value?.querySelector('.hero-title-line1'),
      { opacity: 0, x: -60, filter: 'blur(10px)' },
      { opacity: 1, x: 0, filter: 'blur(0px)', duration: 0.9 },
      '-=0.4'
    )
    .fromTo(
      leftContentRef.value?.querySelector('.hero-title-line2'),
      { opacity: 0, x: -60, filter: 'blur(10px)' },
      { opacity: 1, x: 0, filter: 'blur(0px)', duration: 0.9 },
      '-=0.5'
    )
    .fromTo(
      leftContentRef.value?.querySelector('.hero-subtitle'),
      { opacity: 0, y: 30 },
      { opacity: 1, y: 0, duration: 0.8 },
      '-=0.5'
    )
    .fromTo(
      leftContentRef.value?.querySelector('.hero-buttons'),
      { opacity: 0, y: 30 },
      { opacity: 1, y: 0, duration: 0.8 },
      '-=0.4'
    )
    .fromTo(
      leftContentRef.value?.querySelector('.hero-trust'),
      { opacity: 0, y: 20 },
      { opacity: 1, y: 0, duration: 0.7 },
      '-=0.3'
    )

  gsap.fromTo(
    illustrationRef.value?.querySelector('.illustration-main'),
    { opacity: 0, scale: 0.8, y: 40, rotateX: 5 },
    { opacity: 1, scale: 1, y: 0, rotateX: 0, duration: 1.3, ease: 'power3.out', delay: 0.5 }
  )

  gsap.fromTo(
    illustrationRef.value?.querySelectorAll('.float-item'),
    { opacity: 0, scale: 0.5, y: 30 },
    { opacity: 1, scale: 1, y: 0, duration: 0.8, stagger: 0.12, ease: 'back.out(1.7)', delay: 0.7 }
  )

  const floatingEls = illustrationRef.value?.querySelectorAll('.float-anim')
  if (floatingEls?.length) {
    floatingEls.forEach((el, i) => {
      gsap.to(el, {
        y: -12 + (i % 2 === 0 ? -10 : 10),
        x: i % 3 === 0 ? 6 : -6,
        rotation: i % 2 === 0 ? 3 : -3,
        duration: 3.5 + (i * 0.3),
        ease: 'sine.inOut',
        yoyo: true,
        repeat: -1,
        delay: i * 0.4
      })
    })
  }

  gsap.utils.toArray('.scroll-reveal').forEach((el) => {
    gsap.fromTo(
      el,
      { opacity: 0, y: 50 },
      {
        opacity: 1, y: 0,
        duration: 1,
        ease: 'power3.out',
        scrollTrigger: {
          trigger: el,
          start: 'top 85%',
          toggleActions: 'play none none reverse'
        }
      }
    )
  })

  gsap.utils.toArray('.scale-reveal').forEach((el) => {
    gsap.fromTo(
      el,
      { opacity: 0, scale: 0.85, filter: 'blur(5px)' },
      {
        opacity: 1, scale: 1, filter: 'blur(0px)',
        duration: 1,
        ease: 'power3.out',
        scrollTrigger: {
          trigger: el,
          start: 'top 85%',
          toggleActions: 'play none none reverse'
        }
      }
    )
  })

  document.addEventListener('mousemove', handleMouseMove)
})

onBeforeUnmount(() => {
  document.removeEventListener('mousemove', handleMouseMove)
  if (lenis) lenis.destroy()
  ScrollTrigger.getAll().forEach(t => t.kill())
  particles.forEach(p => {
    if (p.parentNode) p.parentNode.removeChild(p)
  })
  particles = []
})
</script>

<template>
  <div class="min-h-screen overflow-x-hidden bg-[#F5F5F0] font-sans selection:bg-[#00B8C9]/20 selection:text-[#146C94]">
    <!-- Artistic Canvas Texture with Noise Filter -->
    <div class="fixed inset-0 -z-10">
      <div class="absolute inset-0 opacity-[0.35]" style="background-image: repeating-linear-gradient(0deg, transparent, transparent 1px, rgba(0,0,0,0.012) 1px, rgba(0,0,0,0.012) 2px), repeating-linear-gradient(90deg, transparent, transparent 1px, rgba(0,0,0,0.012) 1px, rgba(0,0,0,0.012) 2px);"></div>
      <svg class="absolute inset-0 w-full h-full opacity-[0.035]" preserveAspectRatio="none">
        <filter id="canvasNoise">
          <feTurbulence type="fractalNoise" baseFrequency="0.65" numOctaves="4" stitchTiles="stitch"/>
          <feColorMatrix type="matrix" values="1 0 0 0 0 0 1 0 0 0 0 0 1 0 0 0 0 0 0.15 0"/>
        </filter>
        <rect width="100%" height="100%" filter="url(#canvasNoise)"/>
      </svg>
    </div>

    <!-- Hero Background Artistic Composition -->
    <div ref="particlesContainer" class="fixed inset-0 -z-10 overflow-hidden pointer-events-none" style="background: radial-gradient(ellipse at 30% 40%, rgba(0,184,201,0.03) 0%, rgba(20,108,148,0.02) 25%, rgba(255,107,107,0.02) 50%, rgba(244,197,66,0.015) 75%, rgba(78,205,196,0.02) 100%);">

      <!-- ===== LARGE AMBIENT BREATHING GRADIENT ORBS ===== -->
      <div class="absolute top-[-10%] left-[-5%] w-[1100px] h-[1100px] bg-gradient-to-br from-[#00B8C9]/15 via-[#146C94]/8 to-transparent rounded-full blur-[160px] animate-breath-1"></div>
      <div class="absolute bottom-[-15%] right-[-8%] w-[1000px] h-[1000px] bg-gradient-to-tl from-[#FF6B6B]/15 via-[#FF9F1C]/10 to-transparent rounded-full blur-[160px] animate-breath-2"></div>
      <div class="absolute top-[25%] left-[55%] -translate-x-1/2 -translate-y-1/2 w-[1200px] h-[1200px] bg-gradient-to-r from-[#4ECDC4]/10 via-[#F4C542]/8 to-[#00B8C9]/10 rounded-full blur-[200px] animate-breath-3"></div>
      <div class="absolute top-[60%] left-[5%] w-[700px] h-[700px] bg-gradient-to-tr from-[#FF9F1C]/10 to-[#FF6B6B]/6 rounded-full blur-[140px] animate-breath-4"></div>
      <div class="absolute top-[10%] right-[20%] w-[600px] h-[600px] bg-gradient-to-bl from-[#F4C542]/8 to-[#4ECDC4]/5 rounded-full blur-[120px] animate-breath-5"></div>
      <div class="absolute bottom-[5%] left-[40%] w-[800px] h-[800px] bg-gradient-to-r from-[#146C94]/6 via-[#00B8C9]/8 to-[#4ECDC4]/5 rounded-full blur-[150px] animate-breath-2" style="animation-delay: -8s;"></div>

      <!-- ===== GIANT WATERCOLOR PAINT BLOBS (behind composition) ===== -->
      <!-- Large turquoise blob behind monitor area -->
      <svg class="absolute top-[15%] left-[35%] w-[550px] h-[550px] opacity-[0.18] animate-splash-drift" viewBox="0 0 200 200">
        <path fill="#00B8C9" d="M52.7,-71.2C68.5,-63.7,81.2,-50.1,87.5,-34.2C93.8,-18.3,93.7,-0.1,88.6,15.1C83.5,30.3,73.4,42.4,61.2,52.5C49,62.6,34.7,70.7,19.2,75.1C3.7,79.5,-13,80.2,-28,74.8C-43,69.4,-56.4,57.9,-65.4,43.9C-74.4,29.9,-79,13.3,-77.8,-2.3C-76.6,-17.9,-69.6,-32.4,-59.1,-43.8C-48.6,-55.2,-34.6,-63.5,-20.1,-68.9C-5.6,-74.3,9.4,-76.8,24.8,-74C40.2,-71.2,36.9,-78.7,52.7,-71.2Z" transform="translate(100 100)">
          <animateTransform attributeName="transform" type="translate" values="100 100;107 93;95 104;100 100" dur="25s" repeatCount="indefinite"/>
        </path>
      </svg>

      <!-- Large coral blob behind camera area -->
      <svg class="absolute top-[50%] right-[5%] w-[450px] h-[450px] opacity-[0.15] animate-splash-drift" style="animation-delay: -6s;" viewBox="0 0 200 200">
        <path fill="#FF6B6B" d="M46.2,-62.3C59.3,-56.3,68.5,-43.1,73.9,-28.4C79.3,-13.7,80.9,2.4,76.2,16.2C71.5,30,60.6,41.5,48.2,50.2C35.8,58.9,21.9,64.8,6.9,68.2C-8.1,71.6,-24.2,72.5,-37.6,65.9C-51,59.3,-61.7,45.2,-67.9,29.6C-74.1,14,-75.8,-3.1,-70.6,-17.4C-65.4,-31.7,-53.3,-43.2,-39.7,-50C-26.1,-56.8,-11.1,-58.9,3.1,-63.3C17.3,-67.7,33.1,-68.2,46.2,-62.3Z" transform="translate(100 100)">
          <animateTransform attributeName="transform" type="translate" values="100 100;93 106;104 96;100 100" dur="22s" repeatCount="indefinite"/>
        </path>
      </svg>

      <!-- Large gold blob behind center -->
      <svg class="absolute top-[35%] left-[20%] w-[380px] h-[380px] opacity-[0.12] animate-splash-drift" style="animation-delay: -11s;" viewBox="0 0 200 200">
        <path fill="#F4C542" d="M38.8,-52.3C51.6,-46.2,64.4,-38.9,70.8,-28C77.2,-17.1,77.2,-2.7,73.3,10.5C69.4,23.7,61.6,35.7,51.2,44.9C40.8,54.1,27.8,60.5,13.7,64.5C-0.4,68.5,-15.6,70.1,-28.3,65.2C-41,60.3,-51.2,48.9,-57.6,35.8C-64,22.7,-66.6,8,-64.8,-5.5C-63,-19,-56.8,-31.3,-47.1,-40.2C-37.4,-49.1,-24.3,-54.6,-10.7,-57.9C2.9,-61.2,16.8,-62.3,30.4,-59.8C44,-57.3,25.9,-58.5,38.8,-52.3Z" transform="translate(100 100)">
          <animateTransform attributeName="transform" type="translate" values="100 100;104 95;96 103;100 100" dur="28s" repeatCount="indefinite"/>
        </path>
      </svg>

      <!-- Mint splash behind left content -->
      <svg class="absolute top-[60%] left-[-5%] w-[400px] h-[400px] opacity-[0.13] animate-splash-drift" style="animation-delay: -15s;" viewBox="0 0 200 200">
        <path fill="#4ECDC4" d="M43.5,-56.8C56.4,-49.9,66.6,-39.2,72.3,-26.2C78,-13.2,79.2,2,73.9,14.8C68.6,27.6,56.8,38,44,46.5C31.2,55,17.6,61.6,2.9,63.8C-11.8,66,-27.6,63.8,-39.6,56.1C-51.6,48.4,-59.8,35.2,-64.1,20.9C-68.4,6.6,-68.8,-8.8,-63.8,-21.8C-58.8,-34.8,-48.4,-45.4,-36.1,-52.2C-23.8,-59,-9.8,-62,4.1,-66.2C18,-70.4,30.6,-63.7,43.5,-56.8Z" transform="translate(100 100)">
          <animateTransform attributeName="transform" type="translate" values="100 100;97 102;103 98;100 100" dur="24s" repeatCount="indefinite"/>
        </path>
      </svg>

      <!-- Orange splash top right -->
      <svg class="absolute top-[5%] right-[10%] w-[300px] h-[300px] opacity-[0.11] animate-splash-drift" style="animation-delay: -4s;" viewBox="0 0 200 200">
        <path fill="#FF9F1C" d="M41.5,-53.8C54.8,-49,66.7,-39.4,72.2,-27C77.7,-14.6,76.8,0.6,71.7,14.1C66.6,27.6,57.3,39.4,45.5,48.2C33.7,57,19.4,62.8,4.4,65.1C-10.6,67.4,-26.2,66.2,-38.7,58.8C-51.2,51.4,-60.6,37.8,-65.2,23C-69.8,8.2,-69.6,-7.8,-63.8,-21C-58,-34.2,-46.6,-44.6,-33.8,-50.4C-21,-56.2,-6.8,-57.4,7.4,-60C21.6,-62.6,28.2,-58.5,41.5,-53.8Z" transform="translate(100 100)">
          <animateTransform attributeName="transform" type="translate" values="100 100;103 102;97 97;100 100" dur="20s" repeatCount="indefinite"/>
        </path>
      </svg>

      <!-- Deep teal splash bottom -->
      <svg class="absolute bottom-[5%] right-[30%] w-[350px] h-[350px] opacity-[0.10] animate-splash-drift" style="animation-delay: -9s;" viewBox="0 0 200 200">
        <path fill="#146C94" d="M35.2,-46.2C46.9,-41.4,58.6,-35.3,64.6,-25.8C70.6,-16.3,70.9,-3.4,67.5,8.3C64.1,20,57.1,30.5,47.6,38.5C38.1,46.5,26.1,52,13.3,55.5C0.5,59,-13.1,60.5,-24.8,56.5C-36.5,52.5,-46.3,43,-52.6,31.8C-58.9,20.6,-61.7,7.8,-60.3,-4.2C-58.9,-16.2,-53.3,-27.4,-44.2,-35.4C-35.1,-43.4,-22.6,-48.2,-9.8,-51.1C3,-54,16.1,-55,25.6,-50.5C35.1,-46,23.5,-51,35.2,-46.2Z" transform="translate(100 100)">
          <animateTransform attributeName="transform" type="translate" values="100 100;98 105;102 96;100 100" dur="26s" repeatCount="indefinite"/>
        </path>
      </svg>

      <!-- ===== DIGITAL BRUSH STROKES ACROSS SCREEN ===== -->
      <svg class="absolute top-[12%] left-[-5%] w-[600px] h-[20px] opacity-[0.09]" viewBox="0 0 600 20">
        <path d="M0 10 Q 80 2, 160 10 T 320 8 T 480 12 L 600 10" stroke="#00B8C9" stroke-width="4" fill="none" stroke-linecap="round" class="animate-brush-stroke"/>
      </svg>
      <svg class="absolute top-[48%] right-[-5%] w-[500px] h-[18px] opacity-[0.07]" style="animation-delay: -4s;" viewBox="0 0 500 18">
        <path d="M0 9 Q 70 15, 140 9 T 280 6 T 420 11 L 500 9" stroke="#FF6B6B" stroke-width="3.5" fill="none" stroke-linecap="round" class="animate-brush-stroke" style="animation-delay: -4s"/>
      </svg>
      <svg class="absolute top-[72%] left-[10%] w-[450px] h-[14px] opacity-[0.06]" style="animation-delay: -8s;" viewBox="0 0 450 14">
        <path d="M0 7 Q 60 2, 120 7 T 240 10 T 360 6 L 450 7" stroke="#4ECDC4" stroke-width="3" fill="none" stroke-linecap="round" class="animate-brush-stroke" style="animation-delay: -8s"/>
      </svg>
      <svg class="absolute top-[85%] left-[50%] w-[400px] h-[16px] opacity-[0.08]" style="animation-delay: -2s;" viewBox="0 0 400 16">
        <path d="M0 8 Q 50 14, 100 8 T 200 5 T 300 10 L 400 8" stroke="#F4C542" stroke-width="3.5" fill="none" stroke-linecap="round" class="animate-brush-stroke" style="animation-delay: -2s"/>
      </svg>
      <svg class="absolute top-[28%] left-[40%] w-[350px] h-[12px] opacity-[0.05]" style="animation-delay: -6s;" viewBox="0 0 350 12">
        <path d="M0 6 Q 45 10, 90 6 T 180 4 T 270 8 L 350 6" stroke="#FF9F1C" stroke-width="2.5" fill="none" stroke-linecap="round" class="animate-brush-stroke" style="animation-delay: -6s"/>
      </svg>

      <!-- ===== PAINT SPLATTERS (clusters of dots) ===== -->
      <div class="absolute top-[18%] left-[25%] flex gap-[2px] flex-wrap w-[60px] opacity-[0.08]" style="animation: splash-drift 20s ease-in-out infinite; animation-delay: -3s;">
        <div class="w-[3px] h-[3px] rounded-full bg-[#00B8C9]"></div>
        <div class="w-[2px] h-[2px] rounded-full bg-[#00B8C9] mt-[3px]"></div>
        <div class="w-[4px] h-[4px] rounded-full bg-[#00B8C9]"></div>
        <div class="w-[2px] h-[2px] rounded-full bg-[#146C94] mt-[2px]"></div>
        <div class="w-[3px] h-[3px] rounded-full bg-[#00B8C9]"></div>
        <div class="w-[2px] h-[2px] rounded-full bg-[#4ECDC4]"></div>
        <div class="w-[3px] h-[3px] rounded-full bg-[#146C94] mt-[1px]"></div>
      </div>
      <div class="absolute top-[55%] right-[15%] flex gap-[2px] flex-wrap w-[50px] opacity-[0.07]" style="animation: splash-drift 22s ease-in-out infinite; animation-delay: -7s;">
        <div class="w-[2px] h-[2px] rounded-full bg-[#FF6B6B]"></div>
        <div class="w-[4px] h-[4px] rounded-full bg-[#FF6B6B] mt-[2px]"></div>
        <div class="w-[2px] h-[2px] rounded-full bg-[#FF9F1C]"></div>
        <div class="w-[3px] h-[3px] rounded-full bg-[#FF6B6B]"></div>
        <div class="w-[2px] h-[2px] rounded-full bg-[#FF9F1C] mt-[3px]"></div>
      </div>
      <div class="absolute bottom-[25%] left-[8%] flex gap-[2px] flex-wrap w-[55px] opacity-[0.06]" style="animation: splash-drift 18s ease-in-out infinite; animation-delay: -11s;">
        <div class="w-[3px] h-[3px] rounded-full bg-[#F4C542]"></div>
        <div class="w-[2px] h-[2px] rounded-full bg-[#F4C542]"></div>
        <div class="w-[4px] h-[4px] rounded-full bg-[#FF9F1C] mt-[2px]"></div>
        <div class="w-[2px] h-[2px] rounded-full bg-[#F4C542]"></div>
      </div>

      <!-- ===== ROTATING GRADIENT CIRCLES ===== -->
      <div class="absolute top-[8%] left-[18%] w-[180px] h-[180px] rounded-full border-[2px] border-[#00B8C9]/25 animate-rotate-slow">
        <div class="absolute inset-[15px] rounded-full border border-[#4ECDC4]/30"></div>
        <div class="absolute inset-[35px] rounded-full bg-gradient-to-br from-[#00B8C9]/15 to-[#4ECDC4]/8"></div>
        <div class="absolute inset-[55px] rounded-full border border-[#00B8C9]/20"></div>
      </div>
      <div class="absolute bottom-[25%] right-[5%] w-[160px] h-[160px] rounded-full border-[2px] border-[#FF6B6B]/25 animate-rotate-slow" style="animation-duration: 22s; animation-delay: -6s;">
        <div class="absolute inset-[12px] rounded-full border border-[#FF9F1C]/30"></div>
        <div class="absolute inset-[28px] rounded-full bg-gradient-to-tl from-[#FF6B6B]/15 to-[#FF9F1C]/8"></div>
        <div class="absolute inset-[45px] rounded-full border border-[#FF6B6B]/20"></div>
      </div>
      <div class="absolute top-[40%] left-[45%] w-[140px] h-[140px] rounded-full border-[2px] border-[#F4C542]/25 animate-rotate-slow" style="animation-duration: 28s; animation-delay: -12s;">
        <div class="absolute inset-[10px] rounded-full border border-[#FF9F1C]/30"></div>
        <div class="absolute inset-[24px] rounded-full bg-gradient-to-r from-[#F4C542]/15 to-[#FF9F1C]/8"></div>
        <div class="absolute inset-[38px] rounded-full border border-[#F4C542]/20"></div>
      </div>
      <div class="absolute top-[78%] left-[30%] w-[120px] h-[120px] rounded-full border-[2px] border-[#4ECDC4]/25 animate-rotate-slow" style="animation-duration: 32s; animation-delay: -18s;">
        <div class="absolute inset-[8px] rounded-full border border-[#00B8C9]/30"></div>
        <div class="absolute inset-[20px] rounded-full bg-gradient-to-bl from-[#4ECDC4]/15 to-[#00B8C9]/8"></div>
        <div class="absolute inset-[32px] rounded-full border border-[#4ECDC4]/20"></div>
      </div>
      <div class="absolute top-[20%] right-[35%] w-[100px] h-[100px] rounded-full border-[2px] border-[#FF9F1C]/25 animate-rotate-slow" style="animation-duration: 26s; animation-delay: -9s;">
        <div class="absolute inset-[6px] rounded-full border border-[#FF6B6B]/30"></div>
        <div class="absolute inset-[16px] rounded-full bg-gradient-to-br from-[#FF9F1C]/15 to-[#FF6B6B]/8"></div>
      </div>

      <!-- ===== CURVED DECORATIVE LINES ===== -->
      <svg class="absolute top-0 left-0 w-full h-full opacity-[0.04] pointer-events-none" preserveAspectRatio="none" viewBox="0 0 1440 900">
        <path d="M0 200 Q 360 100, 720 250 T 1440 200" stroke="#00B8C9" stroke-width="0.8" fill="none" class="animate-line-draw"/>
        <path d="M0 500 Q 360 600, 720 450 T 1440 550" stroke="#FF6B6B" stroke-width="0.8" fill="none" class="animate-line-draw" style="animation-delay: -2s"/>
        <path d="M0 700 Q 360 650, 720 750 T 1440 680" stroke="#4ECDC4" stroke-width="0.8" fill="none" class="animate-line-draw" style="animation-delay: -4s"/>
        <path d="M200 0 Q 300 450, 250 900" stroke="#F4C542" stroke-width="0.8" fill="none" class="animate-line-draw" style="animation-delay: -3s"/>
        <path d="M800 0 Q 700 450, 850 900" stroke="#FF9F1C" stroke-width="0.8" fill="none" class="animate-line-draw" style="animation-delay: -5s"/>
        <path d="M1200 0 Q 1300 450, 1150 900" stroke="#00B8C9" stroke-width="0.8" fill="none" class="animate-line-draw" style="animation-delay: -1s"/>
        <path d="M0 300 C 250 200, 500 400, 750 280 C 1000 160, 1250 350, 1440 250" stroke="#4ECDC4" stroke-width="0.5" fill="none" class="animate-line-draw" style="animation-delay: -2.5s"/>
        <path d="M0 600 C 300 700, 600 500, 900 650 C 1200 800, 1350 600, 1440 650" stroke="#FF6B6B" stroke-width="0.5" fill="none" class="animate-line-draw" style="animation-delay: -4.5s"/>
      </svg>

      <!-- ===== GEOMETRIC SHAPES & ABSTRACT FORMS ===== -->
      <div class="absolute top-[6%] right-[30%] w-[70px] h-[70px] border-[2px] border-[#00B8C9]/30 rotate-45 animate-float-shape" style="border-radius: 4px;"></div>
      <div class="absolute bottom-[15%] right-[28%] w-[85px] h-[85px] animate-float-shape" style="animation-delay: -3s; clip-path: polygon(50% 0%, 100% 100%, 0% 100%); background: linear-gradient(135deg, rgba(255,107,107,0.12), rgba(255,159,28,0.06));"></div>
      <div class="absolute top-[45%] right-[2%] w-[72px] h-[72px] border-[2px] border-[#4ECDC4]/30 animate-float-shape" style="animation-delay: -5s; border-radius: 50%; border-style: dashed;"></div>
      <div class="absolute top-[78%] left-[18%] w-[65px] h-[65px] animate-float-shape" style="animation-delay: -2s; clip-path: polygon(50% 0%, 100% 50%, 50% 100%, 0% 50%); background: linear-gradient(135deg, rgba(244,197,66,0.15), rgba(255,159,28,0.08));"></div>
      <div class="absolute top-[22%] right-[6%] w-[56px] h-[56px] border-[2px] border-[#FF9F1C]/30 animate-float-shape" style="animation-delay: -7s; border-radius: 3px; transform: rotate(15deg);"></div>
      <div class="absolute bottom-[40%] left-[35%] w-[75px] h-[75px] animate-float-shape" style="animation-delay: -4s; clip-path: polygon(25% 0%, 75% 0%, 100% 50%, 75% 100%, 25% 100%, 0% 50%); background: linear-gradient(135deg, rgba(0,184,201,0.1), rgba(78,205,196,0.05));"></div>
      <div class="absolute top-[10%] left-[10%] w-[50px] h-[50px] border-[2px] border-[#4ECDC4]/30 animate-float-shape" style="animation-delay: -1s; border-radius: 3px; transform: rotate(30deg);"></div>
      <div class="absolute bottom-[50%] right-[40%] w-[45px] h-[45px] animate-float-shape" style="animation-delay: -6s; clip-path: polygon(50% 0%, 100% 100%, 0% 100%); background: radial-gradient(circle at 30% 30%, rgba(0,184,201,0.12), transparent); transform: rotate(180deg);"></div>

      <!-- ===== DOT PATTERN OVERLAY ===== -->
      <div class="absolute inset-0 opacity-[0.02]" style="background-image: radial-gradient(circle, #2F3542 1px, transparent 1px); background-size: 18px 18px;"></div>

      <!-- ===== SPARKLES ===== -->
      <div class="absolute top-[8%] left-[28%] w-[6px] h-[6px] bg-[#00B8C9] rounded-full animate-sparkle" style="box-shadow: 0 0 10px rgba(0,184,201,0.6);"></div>
      <div class="absolute top-[30%] right-[32%] w-[5px] h-[5px] bg-[#FF6B6B] rounded-full animate-sparkle" style="animation-delay: -2s; box-shadow: 0 0 8px rgba(255,107,107,0.6);"></div>
      <div class="absolute bottom-[35%] left-[38%] w-[6px] h-[6px] bg-[#F4C542] rounded-full animate-sparkle" style="animation-delay: -4s; box-shadow: 0 0 10px rgba(244,197,66,0.6);"></div>
      <div class="absolute top-[48%] right-[8%] w-[5px] h-[5px] bg-[#4ECDC4] rounded-full animate-sparkle" style="animation-delay: -1.5s; box-shadow: 0 0 8px rgba(78,205,196,0.6);"></div>
      <div class="absolute bottom-[18%] right-[52%] w-[6px] h-[6px] bg-[#FF9F1C] rounded-full animate-sparkle" style="animation-delay: -3.5s; box-shadow: 0 0 10px rgba(255,159,28,0.6);"></div>
      <div class="absolute top-[85%] left-[5%] w-[5px] h-[5px] bg-[#00B8C9] rounded-full animate-sparkle" style="animation-delay: -5s; box-shadow: 0 0 8px rgba(0,184,201,0.6);"></div>
      <div class="absolute top-[38%] left-[2%] w-[4px] h-[4px] bg-[#146C94] rounded-full animate-sparkle" style="animation-delay: -0.5s;"></div>
      <div class="absolute top-[65%] left-[22%] w-[4px] h-[4px] bg-[#4ECDC4] rounded-full animate-sparkle" style="animation-delay: -2.5s;"></div>

      <!-- ===== COLOR PALETTE STRIP (horizontal) ===== -->
      <div class="absolute top-[5%] left-[50%] -translate-x-1/2 w-[160px] h-[4px] flex rounded-full overflow-hidden opacity-[0.12]" style="animation: float-shape 10s ease-in-out infinite;">
        <div class="flex-1" style="background: #00B8C9;"></div>
        <div class="flex-1" style="background: #146C94;"></div>
        <div class="flex-1" style="background: #FF6B6B;"></div>
        <div class="flex-1" style="background: #FF9F1C;"></div>
        <div class="flex-1" style="background: #F4C542;"></div>
        <div class="flex-1" style="background: #4ECDC4;"></div>
      </div>

      <!-- ===== SMALL COLOR SWATCHES FLOATING ===== -->
      <div class="absolute top-[70%] right-[12%] flex flex-col gap-1.5" style="animation: float-shape 12s ease-in-out infinite; animation-delay: -3s;">
        <div class="w-[14px] h-[10px] rounded-sm shadow-sm" style="background: #00B8C9; transform: rotate(-3deg);"></div>
        <div class="w-[14px] h-[10px] rounded-sm shadow-sm" style="background: #FF6B6B; transform: rotate(2deg);"></div>
        <div class="w-[14px] h-[10px] rounded-sm shadow-sm" style="background: #F4C542; transform: rotate(-1deg);"></div>
      </div>

      <!-- ===== LARGE SHADOW OVERLAY for depth ===== -->
      <div class="absolute inset-0" style="background: radial-gradient(ellipse at 30% 50%, transparent 0%, transparent 50%, rgba(47,53,66,0.02) 80%, rgba(47,53,66,0.04) 100%);"></div>
    </div>

    <!-- Navbar -->
    <nav ref="navbarRef" class="fixed top-0 left-0 right-0 z-50 transition-all duration-500"
      :class="isMenuOpen ? 'bg-white shadow-2xl shadow-black/5' : 'bg-white/60 backdrop-blur-2xl border-b border-white/20'">
      <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">
          <Link href="/" class="flex items-center gap-3 group">
            <div class="w-11 h-11 rounded-2xl flex items-center justify-center relative overflow-hidden transition-all duration-500 group-hover:scale-105 group-hover:shadow-lg"
              style="background: linear-gradient(135deg, #00B8C9, #146C94, #FF6B6B, #F4C542, #4ECDC4); background-size: 300% 300%; animation: gradientShift 8s ease infinite;">
              <span class="text-white font-bold text-lg tracking-tight relative z-10 drop-shadow-sm">C</span>
              <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-500" style="background: radial-gradient(circle at 30% 30%, rgba(255,255,255,0.2), transparent);"></div>
            </div>
            <span class="text-2xl font-extrabold tracking-tight text-[#2F3542] group-hover:opacity-80 transition-opacity">CREAPIX</span>
          </Link>

          <div class="hidden lg:flex items-center gap-10">
            <a v-for="item in navItems" :key="item.label" :href="item.href"
              class="text-sm font-medium text-[#2F3542]/70 hover:text-[#00B8C9] transition-all duration-300 relative after:absolute after:bottom-[-2px] after:left-0 after:w-0 after:h-[2px] after:bg-gradient-to-r after:from-[#00B8C9] after:to-[#4ECDC4] after:transition-all after:duration-300 hover:after:w-full">
              {{ item.label }}
            </a>
          </div>

          <div class="hidden lg:flex items-center gap-4">
            <Link :href="login()"
              class="text-sm font-medium text-[#2F3542]/60 hover:text-[#2F3542] transition-colors px-4 py-2">
              Iniciar sesión
            </Link>
            <Link :href="login()"
              class="relative group/btn px-7 py-3 rounded-full font-semibold text-sm text-white overflow-hidden transition-all duration-300 shadow-lg hover:shadow-xl hover:shadow-[#00B8C9]/20 active:scale-95"
              style="background: linear-gradient(135deg, #00B8C9, #146C94, #FF6B6B); background-size: 200% 200%; animation: gradientShift 8s ease infinite;">
              <span class="relative z-10">Comenzar</span>
              <div class="absolute inset-0 bg-white/20 translate-x-[-100%] group-hover/btn:translate-x-[100%] transition-transform duration-500"></div>
              <div class="absolute inset-0 rounded-full ring-2 ring-white/20 opacity-0 group-hover/btn:opacity-100 transition-opacity duration-300"></div>
            </Link>
          </div>

          <button @click="isMenuOpen = !isMenuOpen"
            class="lg:hidden relative w-10 h-10 flex items-center justify-center rounded-xl bg-[#F5F5F5] hover:bg-[#CFCFCF]/30 transition-colors">
            <div class="w-5 h-4 flex flex-col justify-between">
              <span class="block h-[2px] bg-[#2F3542] rounded-full transition-all duration-300"
                :class="{ 'rotate-45 translate-y-[7px]': isMenuOpen }"></span>
              <span class="block h-[2px] bg-[#2F3542] rounded-full transition-all duration-300"
                :class="{ 'opacity-0': isMenuOpen }"></span>
              <span class="block h-[2px] bg-[#2F3542] rounded-full transition-all duration-300"
                :class="{ '-rotate-45 -translate-y-[7px]': isMenuOpen }"></span>
            </div>
          </button>
        </div>
      </div>

      <Transition
        enter-active-class="transition-all duration-300 ease-out"
        enter-from-class="opacity-0 max-h-0"
        enter-to-class="opacity-100 max-h-96"
        leave-active-class="transition-all duration-200 ease-in"
        leave-from-class="opacity-100 max-h-96"
        leave-to-class="opacity-0 max-h-0">
        <div v-if="isMenuOpen" class="lg:hidden overflow-hidden bg-white/95 backdrop-blur-xl border-t border-[#CFCFCF]/30">
          <div class="px-6 py-6 space-y-1">
            <a v-for="item in navItems" :key="item.label" :href="item.href" @click="isMenuOpen = false"
              class="block px-4 py-3 rounded-xl text-[#2F3542]/80 hover:text-[#00B8C9] hover:bg-[#00B8C9]/5 font-medium transition-all">
              {{ item.label }}
            </a>
            <div class="pt-4 mt-4 border-t border-[#CFCFCF]/30 space-y-3">
              <Link :href="login()" @click="isMenuOpen = false"
                class="block text-center py-3.5 rounded-xl text-[#2F3542]/70 hover:text-[#2F3542] font-medium border border-[#CFCFCF]/50 transition-colors">
                Iniciar sesión
              </Link>
              <Link :href="login()" @click="isMenuOpen = false"
                class="block text-center py-3.5 rounded-xl text-white font-bold shadow-lg"
                style="background: linear-gradient(135deg, #00B8C9, #146C94);">
                Comenzar ahora
              </Link>
            </div>
          </div>
        </div>
      </Transition>
    </nav>

    <!-- Hero Section -->
    <section id="hero" ref="heroRef" class="relative min-h-screen pt-32 pb-20 lg:pt-40 lg:pb-32 flex items-center overflow-hidden">
      <!-- Artistic depth overlay blending background with foreground -->
      <div class="absolute inset-0 pointer-events-none" style="background:
        radial-gradient(ellipse at 20% 30%, rgba(0,184,201,0.04) 0%, transparent 50%),
        radial-gradient(ellipse at 80% 70%, rgba(255,107,107,0.03) 0%, transparent 50%),
        radial-gradient(ellipse at 50% 50%, rgba(244,197,66,0.02) 0%, transparent 50%),
        radial-gradient(ellipse at 10% 80%, rgba(78,205,196,0.03) 0%, transparent 40%),
        radial-gradient(ellipse at 90% 20%, rgba(255,159,28,0.025) 0%, transparent 40%);">
      </div>
      <!-- Subtle shadow vignette for depth -->
      <div class="absolute inset-0 pointer-events-none" style="background: radial-gradient(ellipse at 50% 50%, transparent 40%, rgba(47,53,66,0.015) 80%, rgba(47,53,66,0.03) 100%);"></div>
      <div class="max-w-7xl mx-auto px-6 lg:px-8 w-full">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-center">
          <!-- Left Content -->
          <div ref="leftContentRef" class="relative z-10">
            <!-- Badge -->
            <div class="hero-badge inline-flex items-center gap-2.5 px-5 py-2.5 rounded-full mb-8 border shadow-sm"
              style="background: linear-gradient(135deg, rgba(0,184,201,0.08), rgba(78,205,196,0.08)); border-color: rgba(0,184,201,0.2); backdrop-filter: blur(8px);">
              <span class="w-2 h-2 rounded-full" style="background: #00B8C9; box-shadow: 0 0 8px rgba(0,184,201,0.5); animation: pulseGlow 2s ease-in-out infinite;"></span>
              <span class="text-sm font-semibold" style="color: #146C94;">Estudio creativo profesional</span>
            </div>

            <!-- Title -->
            <h1 class="hero-title-line1 text-5xl sm:text-6xl lg:text-7xl xl:text-8xl font-extrabold leading-[1.05] tracking-tight mb-3"
              style="color: #2F3542;">
              Diseño creativo
            </h1>
            <h1 class="hero-title-line2 text-5xl sm:text-6xl lg:text-7xl xl:text-8xl font-extrabold leading-[1.05] tracking-tight mb-8">
              <span class="creative-gradient">para tu marca</span>
            </h1>

            <!-- Subtitle -->
            <p class="hero-subtitle text-base sm:text-lg lg:text-xl leading-relaxed max-w-lg mb-10"
              style="color: #2F3542/70;">
              Transformamos ideas en identidad visual. Diseño gráfico, fotografía profesional, edición creativa e impresión de alta calidad para hacer destacar tu negocio.
            </p>

            <!-- Buttons -->
            <div class="hero-buttons flex flex-col sm:flex-row items-stretch sm:items-center gap-4 mb-12">
              <Link :href="login()"
                class="group relative px-8 py-4 rounded-full font-bold text-sm text-white overflow-hidden transition-all duration-300 shadow-lg hover:shadow-xl hover:shadow-[#00B8C9]/20 active:scale-[0.97] text-center"
                style="background: linear-gradient(135deg, #00B8C9, #146C94, #FF6B6B); background-size: 200% 200%; animation: gradientShift 8s ease infinite;">
                <span class="relative z-10">Solicitar presupuesto</span>
                <div class="absolute inset-0 bg-white/15 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-500"></div>
                <div class="absolute inset-0 rounded-full ring-2 ring-white/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300" style="box-shadow: 0 0 40px rgba(0,184,201,0.3);"></div>
              </Link>
              <a href="#portafolio"
                class="group relative px-8 py-4 rounded-full font-semibold text-sm text-center transition-all duration-300 border-2 hover:shadow-lg active:scale-[0.97] overflow-hidden"
                style="background: rgba(255,255,255,0.7); border-color: #CFCFCF; color: #2F3542; backdrop-filter: blur(12px);"
                @mouseenter="($event) => { $event.currentTarget.style.borderColor = '#00B8C9'; $event.currentTarget.style.backgroundColor = 'rgba(0,184,201,0.05)' }"
                @mouseleave="($event) => { $event.currentTarget.style.borderColor = '#CFCFCF'; $event.currentTarget.style.backgroundColor = 'rgba(255,255,255,0.7)' }">
                <span class="flex items-center justify-center gap-2 relative z-10">
                  Ver trabajos
                  <svg class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                  </svg>
                </span>
              </a>
            </div>

            <!-- Trust indicators -->
            <div class="hero-trust flex flex-wrap items-center gap-6">
              <div class="flex items-center gap-2.5 px-4 py-2 rounded-xl transition-all duration-300 hover:scale-105" style="background: rgba(78,205,196,0.08);">
                <div class="w-6 h-6 rounded-full flex items-center justify-center" style="background: #4ECDC4;">
                  <svg class="w-3.5 h-3.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                  </svg>
                </div>
                <span class="text-xs font-medium" style="color: #146C94;">Sin compromiso</span>
              </div>
              <div class="flex items-center gap-2.5 px-4 py-2 rounded-xl transition-all duration-300 hover:scale-105" style="background: rgba(255,107,107,0.08);">
                <div class="w-6 h-6 rounded-full flex items-center justify-center" style="background: #FF6B6B;">
                  <svg class="w-3.5 h-3.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                  </svg>
                </div>
                <span class="text-xs font-medium" style="color: #146C94;">Revisiones ilimitadas</span>
              </div>
              <div class="flex items-center gap-2.5 px-4 py-2 rounded-xl transition-all duration-300 hover:scale-105" style="background: rgba(244,197,66,0.08);">
                <div class="w-6 h-6 rounded-full flex items-center justify-center" style="background: #F4C542;">
                  <svg class="w-3.5 h-3.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                  </svg>
                </div>
                <span class="text-xs font-medium" style="color: #146C94;">Soporte prioritario</span>
              </div>
            </div>
          </div>

          <!-- Right Illustration - Premium Composition -->
          <div ref="illustrationRef" class="relative z-10 hidden lg:block">
            <div class="relative illustration-main" style="perspective: 1200px;">
              <div class="relative w-full aspect-[4/3] rounded-3xl overflow-visible">
                <!-- Background glow for composition -->
                <div class="absolute inset-0 rounded-3xl" style="background: radial-gradient(ellipse at 50% 50%, rgba(0,184,201,0.04), transparent 70%);"></div>

                <!-- Main Laptop/Monitor -->
                <div class="absolute top-[5%] left-[5%] w-[62%] h-[68%] rounded-2xl overflow-hidden shadow-2xl float-item" style="background: linear-gradient(145deg, #2F3542, #1a1e27); transform: rotateY(-4deg) rotateX(2deg); box-shadow: 0 30px 60px rgba(0,0,0,0.2), 0 0 40px rgba(0,184,201,0.04);">
                  <!-- Screen bezel -->
                  <div class="absolute inset-[4%] rounded-xl overflow-hidden" style="background: #1a1e27;">
                    <!-- Screen content - Photoshop-like interface -->
                    <div class="absolute inset-0" style="background: linear-gradient(135deg, #2d2d2d, #1a1a2e, #16213e);">
                      <!-- Gradient design canvas -->
                      <div class="absolute top-[10%] left-[8%] w-[55%] h-[55%] rounded-lg overflow-hidden" style="background: linear-gradient(135deg, #00B8C9, #146C94, #FF6B6B, #F4C542); background-size: 300% 300%; animation: gradientShift 6s ease infinite;">
                        <!-- Abstract design elements on screen -->
                        <div class="absolute top-[15%] left-[12%] w-8 h-8 rounded-full bg-white/15 blur-[3px]"></div>
                        <div class="absolute bottom-[15%] right-[15%] w-6 h-6 bg-white/12 rounded-md"></div>
                        <div class="absolute top-[30%] right-[8%] w-2 h-16 bg-white/10 rounded-full"></div>
                        <div class="absolute bottom-[25%] left-[8%] w-10 h-[3px] bg-white/15 rounded-full"></div>
                        <div class="absolute top-[50%] left-[50%] -translate-x-1/2 -translate-y-1/2 w-7 h-7 border-2 border-white/15 rounded-full"></div>
                        <!-- Type layers -->
                        <div class="absolute bottom-[10%] left-[8%] right-[8%] h-[4px] rounded-sm bg-white/10"></div>
                        <div class="absolute bottom-[5%] left-[8%] w-[40%] h-[3px] rounded-sm bg-white/8"></div>
                      </div>
                      <!-- Toolbar -->
                      <div class="absolute left-[3%] top-[8%] bottom-[8%] w-[5%] rounded-lg" style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.04);">
                        <div class="flex flex-col items-center gap-[5px] py-[10px]">
                          <div class="w-[6px] h-[6px] rounded bg-[#00B8C9]/70"></div>
                          <div class="w-[6px] h-[6px] rounded bg-[#FF6B6B]/50"></div>
                          <div class="w-[6px] h-[6px] rounded bg-[#4ECDC4]/50"></div>
                          <div class="w-[6px] h-[6px] rounded bg-[#F4C542]/50"></div>
                          <div class="w-[6px] h-[6px] rounded bg-white/20"></div>
                          <div class="w-[6px] h-[6px] rounded bg-white/15"></div>
                        </div>
                      </div>
                      <!-- Top menu bar -->
                      <div class="absolute top-[2%] left-[10%] right-[4%] h-[4%] flex items-center gap-3" style="opacity: 0.4;">
                        <div class="w-6 h-[2px] rounded bg-white/30"></div>
                        <div class="w-4 h-[2px] rounded bg-white/20"></div>
                        <div class="w-5 h-[2px] rounded bg-white/25"></div>
                      </div>
                      <!-- Bottom status bar -->
                      <div class="absolute bottom-[2%] left-[10%] right-[4%] h-[3%] rounded" style="background: rgba(255,255,255,0.03);"></div>
                    </div>
                  </div>
                  <!-- Camera dot -->
                  <div class="absolute top-[1.5%] left-[50%] -translate-x-1/2 w-[6px] h-[6px] rounded-full bg-[#2F3542] border border-[#3d4555]"></div>
                  <!-- Keyboard hint -->
                  <div class="absolute -bottom-[6%] left-[5%] right-[5%] h-[5%] rounded-b-lg" style="background: linear-gradient(180deg, #2F3542, #1a1e27);"></div>
                </div>

                <!-- DSLR Camera - Premium -->
                <div class="absolute bottom-[5%] right-[6%] w-[30%] h-[32%] float-item" style="filter: drop-shadow(0 20px 40px rgba(0,0,0,0.2)); animation-delay: -0.6s;">
                  <div class="w-full h-full rounded-2xl relative overflow-hidden" style="background: linear-gradient(180deg, #2F3542, #15181f); box-shadow: inset 0 1px 0 rgba(255,255,255,0.05);">
                    <!-- Main body texture lines -->
                    <div class="absolute top-[5%] left-[5%] right-[5%] h-[4px] rounded-full" style="background: repeating-linear-gradient(90deg, rgba(255,255,255,0.05) 0px, rgba(255,255,255,0.05) 4px, transparent 4px, transparent 8px);"></div>
                    <!-- Lens -->
                    <div class="absolute top-[18%] left-[50%] -translate-x-1/2 w-[58%] aspect-square rounded-full border-[3.5px]" style="border-color: #3d4555;">
                      <div class="absolute inset-[7px] rounded-full" style="background: radial-gradient(circle at 35% 30%, #4a5568, #1a1e27);">
                        <div class="absolute inset-[5px] rounded-full" style="background: radial-gradient(circle at 35% 30%, #2d3748, #0d1117);">
                          <div class="absolute top-[18%] left-[22%] w-[35%] h-[35%] rounded-full bg-white/12 blur-[3px]"></div>
                          <!-- Lens reflection -->
                          <div class="absolute top-[10%] right-[15%] w-[8%] h-[8%] rounded-full bg-[#00B8C9]/10 blur-[1px]"></div>
                        </div>
                      </div>
                      <!-- Lens ring dots -->
                      <div class="absolute -top-[2px] -left-[2px] -right-[2px] -bottom-[2px] rounded-full border" style="border-color: rgba(255,255,255,0.03); border-style: dashed;"></div>
                    </div>
                    <!-- Flash -->
                    <div class="absolute top-[8%] right-[12%] w-[14%] aspect-square rounded-sm overflow-hidden" style="background: linear-gradient(135deg, #4a5568, #2d3748);">
                      <div class="absolute inset-[2px] rounded-sm bg-white/8"></div>
                    </div>
                    <!-- Shutter button -->
                    <div class="absolute top-[6%] left-[12%] w-[10%] aspect-[2/1] rounded-sm" style="background: linear-gradient(180deg, #4a5568, #2d3748);"></div>
                    <!-- Grip texture -->
                    <div class="absolute bottom-[15%] left-[6%] right-[6%] flex flex-col gap-[3px]">
                      <div class="h-[1.5px] rounded-full bg-white/8"></div>
                      <div class="h-[1.5px] rounded-full bg-white/6"></div>
                      <div class="h-[1.5px] rounded-full bg-white/5"></div>
                    </div>
                    <!-- Brand -->
                    <div class="absolute bottom-[6%] left-[50%] -translate-x-1/2 text-[5px] font-bold tracking-[0.15em] text-white/25">CREAPIX</div>
                  </div>
                </div>

                <!-- Graphics Tablet -->
                <div class="absolute top-[12%] right-[0%] w-[24%] h-[30%] float-item" style="filter: drop-shadow(0 12px 30px rgba(0,0,0,0.15)); animation-delay: -1.8s; transform: rotate(10deg);">
                  <div class="w-full h-full rounded-xl overflow-hidden shadow-xl" style="background: linear-gradient(180deg, #1a1e27, #0d1117);">
                    <!-- Screen area -->
                    <div class="absolute inset-[6%] rounded-lg overflow-hidden" style="background: #111318;">
                      <!-- Drawing on tablet -->
                      <div class="absolute inset-0 flex items-center justify-center">
                        <div class="w-[55%] h-[45%] rounded-lg opacity-60" style="background: linear-gradient(135deg, #4ECDC4, #00B8C9);">
                          <div class="w-full h-full rounded-lg flex items-center justify-center">
                            <div class="w-6 h-6 rounded-full border-2 border-white/20"></div>
                          </div>
                        </div>
                      </div>
                      <!-- Drawing cursor -->
                      <div class="absolute top-[30%] left-[35%] w-[2px] h-[8px] bg-[#FF6B6B]/60 rounded-full -rotate-45"></div>
                      <div class="absolute top-[35%] left-[34%] w-[5px] h-[1px] bg-[#FF6B6B]/40 rounded-full -rotate-45"></div>
                    </div>
                    <!-- Home button -->
                    <div class="absolute bottom-[4%] left-[50%] -translate-x-1/2 w-[18px] h-[18px] rounded-full border border-white/8"></div>
                    <!-- Side buttons -->
                    <div class="absolute top-[15%] right-[2%] flex flex-col gap-[3px]">
                      <div class="w-[3px] h-[6px] rounded-full bg-white/10"></div>
                      <div class="w-[3px] h-[6px] rounded-full bg-white/8"></div>
                    </div>
                  </div>
                  <!-- Stylus pen -->
                  <div class="absolute -top-[5%] -right-[8%] w-[30%] h-[8%] rounded-full" style="background: linear-gradient(90deg, #2F3542, #1a1e27); transform: rotate(-30deg); box-shadow: 0 4px 8px rgba(0,0,0,0.15);">
                    <div class="absolute top-[50%] -translate-y-1/2 right-[8%] w-[3px] h-[3px] rounded-full bg-[#00B8C9]/50"></div>
                  </div>
                </div>

                <!-- Color Wheel -->
                <div class="absolute top-[1%] right-[15%] w-[20%] aspect-square rounded-full float-item" style="filter: drop-shadow(0 8px 25px rgba(0,0,0,0.12)); animation-delay: -2.5s;">
                  <div class="w-full h-full rounded-full overflow-hidden rotate-45 shadow-lg" style="background: conic-gradient(#00B8C9 0deg 60deg, #146C94 60deg 120deg, #FF6B6B 120deg 180deg, #FF9F1C 180deg 240deg, #F4C542 240deg 300deg, #4ECDC4 300deg 360deg);">
                    <div class="absolute inset-[15%] rounded-full bg-white"></div>
                    <div class="absolute inset-[28%] rounded-full" style="background: conic-gradient(#00B8C9 0deg 60deg, #146C94 60deg 120deg, #FF6B6B 120deg 180deg, #FF9F1C 180deg 240deg, #F4C542 240deg 300deg, #4ECDC4 300deg 360deg);"></div>
                    <div class="absolute inset-[42%] rounded-full bg-white"></div>
                    <div class="absolute inset-[55%] rounded-full" style="background: conic-gradient(#00B8C9 0deg 60deg, #146C94 60deg 120deg, #FF6B6B 120deg 180deg, #FF9F1C 180deg 240deg, #F4C542 240deg 300deg, #4ECDC4 300deg 360deg);"></div>
                    <div class="absolute inset-[65%] rounded-full bg-white"></div>
                  </div>
                </div>

                <!-- Pantone Swatches Stack -->
                <div class="absolute top-[35%] left-[0%] w-[9%] flex flex-col gap-[3px] float-item" style="animation-delay: -1.2s;">
                  <div class="w-full aspect-[3/2] rounded-md shadow-md" style="background: #00B8C9; transform: rotate(-2deg);"></div>
                  <div class="w-full aspect-[3/2] rounded-md shadow-md" style="background: #FF6B6B; transform: rotate(1deg);"></div>
                  <div class="w-full aspect-[3/2] rounded-md shadow-md" style="background: #F4C542; transform: rotate(-1deg);"></div>
                  <div class="w-full aspect-[3/2] rounded-md shadow-md" style="background: #4ECDC4; transform: rotate(0.5deg);"></div>
                  <div class="w-full aspect-[3/2] rounded-md shadow-md" style="background: #146C94; transform: rotate(-1.5deg);"></div>
                  <div class="w-full aspect-[3/2] rounded-md shadow-md" style="background: #FF9F1C; transform: rotate(0.8deg);"></div>
                </div>

                <!-- Pencil Set -->
                <div class="absolute top-[52%] left-[2%] w-[5%] flex flex-col gap-1 float-item" style="animation-delay: -3s;">
                  <div class="w-full aspect-[1/5] rounded-sm shadow-md" style="background: linear-gradient(180deg, #00B8C9 60%, #146C94 60%); transform: rotate(5deg);"></div>
                  <div class="w-full aspect-[1/5] rounded-sm shadow-md" style="background: linear-gradient(180deg, #FF6B6B 60%, #c0392b 60%); transform: rotate(-3deg);"></div>
                  <div class="w-full aspect-[1/5] rounded-sm shadow-md" style="background: linear-gradient(180deg, #F4C542 60%, #d4a017 60%); transform: rotate(2deg);"></div>
                  <div class="w-full aspect-[1/5] rounded-sm shadow-md" style="background: linear-gradient(180deg, #4ECDC4 60%, #2ecc71 60%); transform: rotate(-4deg);"></div>
                </div>

                <!-- Brush / Design Tool -->
                <div class="absolute bottom-[20%] left-[8%] w-[4%] aspect-[1/6] float-item" style="animation-delay: -0.9s; transform: rotate(8deg);">
                  <div class="w-full h-full rounded-sm shadow-md" style="background: linear-gradient(180deg, #2F3542 50%, #CFCFCF 48%, #d4a017 2%);"></div>
                </div>

                <!-- Floating color circles -->
                <div class="absolute top-[55%] left-[18%] w-[18px] h-[18px] rounded-full float-item" style="background: #00B8C9; box-shadow: 0 0 20px rgba(0,184,201,0.3); animation-delay: -0.4s;"></div>
                <div class="absolute top-[8%] left-[42%] w-[12px] h-[12px] rounded-full float-item" style="background: #FF6B6B; box-shadow: 0 0 16px rgba(255,107,107,0.3); animation-delay: -2s;"></div>
                <div class="absolute bottom-[15%] left-[38%] w-[20px] h-[20px] rounded-full float-item" style="background: #F4C542; box-shadow: 0 0 22px rgba(244,197,66,0.3); animation-delay: -3.5s;"></div>
                <div class="absolute top-[48%] right-[22%] w-[14px] h-[14px] rounded-full float-item" style="background: #4ECDC4; box-shadow: 0 0 18px rgba(78,205,196,0.3); animation-delay: -0.2s;"></div>
                <div class="absolute top-[75%] right-[40%] w-[10px] h-[10px] rounded-full float-item" style="background: #FF9F1C; box-shadow: 0 0 14px rgba(255,159,28,0.3); animation-delay: -4s;"></div>
                <div class="absolute bottom-[45%] left-[50%] w-[8px] h-[8px] rounded-full float-item" style="background: #146C94; box-shadow: 0 0 12px rgba(20,108,148,0.3); animation-delay: -1.5s;"></div>

                <!-- Mockup frame element -->
                <div class="absolute top-[55%] right-[2%] w-[20%] h-[16%] float-item" style="animation-delay: -2.8s; transform: rotate(4deg);">
                  <div class="w-full h-full rounded-lg overflow-hidden shadow-lg" style="border: 2px solid #CFCFCF; background: white;">
                    <div class="h-[25%] flex items-center px-2" style="background: #F5F5F5;">
                      <div class="flex gap-1">
                        <div class="w-[4px] h-[4px] rounded-full" style="background: #FF6B6B;"></div>
                        <div class="w-[4px] h-[4px] rounded-full" style="background: #F4C542;"></div>
                        <div class="w-[4px] h-[4px] rounded-full" style="background: #4ECDC4;"></div>
                      </div>
                    </div>
                    <div class="p-2 flex items-center justify-center" style="height: 75%;">
                      <div class="w-[70%] h-[6px] rounded-sm" style="background: linear-gradient(90deg, #00B8C9, #4ECDC4);"></div>
                    </div>
                  </div>
                </div>

                <!-- Small decorative branding element -->
                <div class="absolute top-[65%] left-[12%] float-item" style="animation-delay: -1.7s;">
                  <div class="text-[6px] font-bold tracking-[0.15em] text-[#00B8C9]/30">BRANDING</div>
                </div>

                <!-- Backlight glow effects -->
                <div class="absolute bottom-[15%] right-[20%] w-[350px] h-[350px] bg-gradient-to-br from-[#00B8C9]/12 to-[#4ECDC4]/8 rounded-full blur-[90px] -z-10 animate-breath-glow"></div>
                <div class="absolute top-[8%] right-[25%] w-[280px] h-[280px] bg-gradient-to-tr from-[#FF6B6B]/8 to-[#F4C542]/6 rounded-full blur-[80px] -z-10 animate-breath-glow" style="animation-delay: -4s;"></div>
                <div class="absolute top-[40%] left-[30%] w-[200px] h-[200px] bg-gradient-to-bl from-[#4ECDC4]/6 to-[#00B8C9]/4 rounded-full blur-[60px] -z-10"></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Scroll indicator -->
      <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex flex-col items-center gap-2 animate-bounce-subtle">
        <span class="text-[10px] font-medium uppercase tracking-[0.2em]" style="color: #CFCFCF;">Scroll</span>
        <div class="w-[1px] h-8" style="background: linear-gradient(to bottom, #CFCFCF, transparent);"></div>
      </div>
    </section>

    <!-- Service Cards -->
    <section ref="cardsRef" class="relative pb-20 lg:pb-28 -mt-10">
      <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 lg:gap-6">
          <div v-for="(card, index) in serviceCards" :key="card.title"
            class="service-card group relative rounded-2xl p-7 lg:p-8 transition-all duration-500 cursor-pointer"
            style="background: rgba(255,255,255,0.55); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border: 1px solid rgba(207,207,207,0.25); box-shadow: 0 8px 32px rgba(0,0,0,0.04);"
            @mouseenter="($event) => {
              const el = $event.currentTarget;
              el.style.transform = 'translateY(-10px) scale(1.02)';
              el.style.boxShadow = '0 25px 60px rgba(0,0,0,0.08), 0 0 50px rgba(0,184,201,0.06)';
              el.style.borderColor = 'rgba(0,184,201,0.15)';
              el.style.backgroundColor = 'rgba(255,255,255,0.75)';
            }"
            @mouseleave="($event) => {
              const el = $event.currentTarget;
              el.style.transform = 'translateY(0px) scale(1)';
              el.style.boxShadow = '0 8px 32px rgba(0,0,0,0.04)';
              el.style.borderColor = 'rgba(207,207,207,0.25)';
              el.style.backgroundColor = 'rgba(255,255,255,0.55)';
            }">
            <!-- Card glow overlay -->
            <div class="absolute inset-0 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"
              style="background: radial-gradient(600px circle at var(--mouse-x, 50%) var(--mouse-y, 50%), rgba(0,184,201,0.06), transparent 40%);">
            </div>

            <div class="relative z-10">
              <!-- Icon container -->
              <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-5 transition-all duration-500 group-hover:scale-110 group-hover:shadow-xl"
                :style="{ background: `linear-gradient(135deg, ${card.gradient.split(' ')[0].replace('from-[', '').replace(']', '')}, ${card.gradient.split(' ')[1].replace('to-[', '').replace(']', '')})` }">
                <!-- Palette Icon -->
                <svg v-if="card.icon === 'Palette'" class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <!-- Camera Icon -->
                <svg v-else-if="card.icon === 'Camera'" class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <!-- Wand Icon -->
                <svg v-else-if="card.icon === 'Wand'" class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7.5 7.5l-.292.293a1 1 0 01-1.414 0 1 1 0 010-1.414L7.5 5.5m6 6l.292-.293a1 1 0 011.414 0 1 1 0 010 1.414L13.5 13.5M7.5 13.5l.292.293a1 1 0 001.414 0 .999.999 0 000-1.414L7.5 11.5M13.5 7.5l.292.293a1 1 0 001.414 0 .999.999 0 000-1.414L13.5 5.5" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z" />
                </svg>
                <!-- Printer Icon -->
                <svg v-else class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
              </div>

              <h3 class="text-lg font-bold mb-2" style="color: #2F3542;">{{ card.title }}</h3>
              <p class="text-sm leading-relaxed" style="color: #2F3542/60;">{{ card.desc }}</p>

              <!-- Hover bottom gradient line -->
              <div class="mt-5 h-[2px] rounded-full w-0 group-hover:w-full transition-all duration-500"
                :style="{ background: `linear-gradient(90deg, ${card.gradient.split(' ')[0].replace('from-[', '').replace(']', '')}, ${card.gradient.split(' ')[1].replace('to-[', '').replace(']', '')})` }">
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Stats Section -->
    <section class="py-16 scroll-reveal" style="background: rgba(255,255,255,0.4); backdrop-filter: blur(8px);">
      <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
          <div v-for="stat in stats" :key="stat.label" class="text-center p-6 rounded-2xl transition-all duration-300"
            style="background: rgba(255,255,255,0.5); backdrop-filter: blur(12px); border: 1px solid rgba(207,207,207,0.2);"
            @mouseenter="($event) => { $event.currentTarget.style.transform = 'translateY(-4px)'; $event.currentTarget.style.boxShadow = '0 12px 40px rgba(0,0,0,0.06)' }"
            @mouseleave="($event) => { $event.currentTarget.style.transform = 'translateY(0)'; $event.currentTarget.style.boxShadow = 'none' }">
            <div class="text-3xl lg:text-4xl font-extrabold mb-2" :class="stat.color">{{ stat.value }}</div>
            <div class="text-sm font-medium" style="color: #2F3542/60;">{{ stat.label }}</div>
          </div>
        </div>
      </div>
    </section>

    <!-- Services Section -->
    <section id="servicios" class="py-20 lg:py-32">
      <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="text-center mb-16 scroll-reveal">
          <span class="font-bold text-sm uppercase tracking-[0.25em]" style="color: #00B8C9;">Nuestros servicios</span>
          <h2 class="text-4xl lg:text-5xl font-extrabold mt-5 mb-5" style="color: #2F3542;">Soluciones creativas <br class="hidden sm:block">integrales</h2>
          <p class="text-lg max-w-2xl mx-auto" style="color: #2F3542/60;">
            Todo lo que necesitas para destacar: diseño, fotografía, edición e impresión profesional.
          </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <div v-for="(category, idx) in serviceCards.slice(0, 3)" :key="category.title"
            class="scale-reveal rounded-3xl p-8 transition-all duration-500 hover:shadow-2xl group"
            style="background: rgba(255,255,255,0.7); backdrop-filter: blur(12px); border: 1px solid rgba(207,207,207,0.2);"
            @mouseenter="($event) => { $event.currentTarget.style.transform = 'translateY(-4px)'; $event.currentTarget.style.borderColor = 'rgba(0,184,201,0.2)' }"
            @mouseleave="($event) => { $event.currentTarget.style.transform = 'translateY(0)'; $event.currentTarget.style.borderColor = 'rgba(207,207,207,0.2)' }">
            <div class="w-16 h-16 rounded-2xl flex items-center justify-center mb-6 shadow-lg group-hover:scale-110 transition-all duration-500"
              :style="{ background: `linear-gradient(135deg, ${category.title === 'Diseño Gráfico' ? '#00B8C9' : category.title === 'Fotografía Profesional' ? '#FF6B6B' : '#4ECDC4'}, ${category.title === 'Diseño Gráfico' ? '#146C94' : category.title === 'Fotografía Profesional' ? '#FF9F1C' : '#00B8C9'})` }">
              <svg v-if="idx === 0" class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              <svg v-else-if="idx === 1" class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              <svg v-else class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z" />
              </svg>
            </div>
            <h3 class="text-xl font-bold mb-3" style="color: #2F3542;">{{ category.title }}</h3>
            <p class="text-sm leading-relaxed" style="color: #2F3542/60;">{{ category.desc }}</p>
            <a href="#"
              class="mt-6 inline-flex items-center gap-2 text-sm font-semibold transition-colors group/link"
              style="color: #00B8C9;">
              <span>Explorar servicios</span>
              <svg class="w-4 h-4 transition-transform duration-300 group-hover/link:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
              </svg>
            </a>
          </div>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section class="py-16 scroll-reveal" style="background: linear-gradient(135deg, #00B8C9, #146C94, #4ECDC4);">
      <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div v-for="feature in features" :key="feature.title" class="text-center text-white p-6">
            <div class="w-16 h-16 bg-white/15 backdrop-blur rounded-2xl flex items-center justify-center mx-auto mb-5 shadow-lg">
              <svg v-if="feature.icon === 'ShieldCheck'" class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
              </svg>
              <svg v-else-if="feature.icon === 'Timer'" class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <svg v-else class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
              </svg>
            </div>
            <h3 class="text-lg font-bold mb-2">{{ feature.title }}</h3>
            <p class="text-sm text-white/75">{{ feature.desc }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Portfolio Section -->
    <section id="portafolio" class="py-20 lg:py-32">
      <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="text-center mb-16 scroll-reveal">
          <span class="font-bold text-sm uppercase tracking-[0.25em]" style="color: #FF6B6B;">Portafolio</span>
          <h2 class="text-4xl lg:text-5xl font-extrabold mt-5 mb-5" style="color: #2F3542;">Proyectos destacados</h2>
          <p class="text-lg max-w-2xl mx-auto" style="color: #2F3542/60;">
            Cada proyecto cuenta una historia. Conoce nuestro trabajo.
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <div v-for="(item, idx) in portfolio" :key="item.title"
            class="group relative overflow-hidden rounded-3xl scale-reveal transition-all duration-500"
            style="background: rgba(255,255,255,0.5); backdrop-filter: blur(12px); border: 1px solid rgba(207,207,207,0.2);"
            @mouseenter="($event) => { $event.currentTarget.style.transform = 'translateY(-4px)'; $event.currentTarget.style.boxShadow = '0 20px 60px rgba(0,0,0,0.08)' }"
            @mouseleave="($event) => { $event.currentTarget.style.transform = 'translateY(0)'; $event.currentTarget.style.boxShadow = 'none' }">
            <img :src="item.image" :alt="item.title"
              class="w-full aspect-[4/3] object-cover transition-transform duration-700 group-hover:scale-110">
            <div class="absolute inset-0 bg-gradient-to-t from-[#2F3542]/90 via-[#2F3542]/20 to-transparent opacity-0 group-hover:opacity-100 transition-all duration-500">
              <div class="absolute bottom-0 left-0 right-0 p-8">
                <span class="text-sm font-bold uppercase tracking-wider" style="color: #4ECDC4;">{{ item.category }}</span>
                <h3 class="text-white text-2xl font-bold mt-2">{{ item.title }}</h3>
              </div>
            </div>
          </div>
        </div>

        <div class="text-center mt-12 scroll-reveal">
          <a href="#"
            class="group inline-flex items-center gap-3 px-8 py-4 rounded-full font-bold text-sm text-white transition-all duration-300 shadow-lg hover:shadow-xl active:scale-[0.97]"
            style="background: linear-gradient(135deg, #00B8C9, #146C94);">
            <span>Ver todos los proyectos</span>
            <svg class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
            </svg>
          </a>
        </div>
      </div>
    </section>

    <!-- Testimonials -->
    <section class="py-20 lg:py-32 scroll-reveal" style="background: rgba(255,255,255,0.3);">
      <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="text-center mb-16">
          <span class="font-bold text-sm uppercase tracking-[0.25em]" style="color: #F4C542;">Testimonios</span>
          <h2 class="text-4xl lg:text-5xl font-extrabold mt-5 mb-5" style="color: #2F3542;">Lo que dicen nuestros clientes</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div v-for="testimonial in testimonials" :key="testimonial.name"
            class="p-8 rounded-3xl transition-all duration-500 hover:shadow-2xl"
            style="background: rgba(255,255,255,0.6); backdrop-filter: blur(12px); border: 1px solid rgba(207,207,207,0.2);"
            @mouseenter="($event) => { $event.currentTarget.style.transform = 'translateY(-6px)'; $event.currentTarget.style.borderColor = 'rgba(0,184,201,0.2)' }"
            @mouseleave="($event) => { $event.currentTarget.style.transform = 'translateY(0)'; $event.currentTarget.style.borderColor = 'rgba(207,207,207,0.2)' }">
            <div class="flex items-center gap-1 mb-5">
              <svg v-for="i in 5" :key="i" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" style="color: #F4C542;">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
              </svg>
            </div>
            <p class="leading-relaxed mb-6" style="color: #2F3542/75;">"{{ testimonial.content }}"</p>
            <div class="flex items-center gap-4">
              <img :src="testimonial.avatar" :alt="testimonial.name"
                class="w-12 h-12 rounded-full object-cover border-2"
                style="border-color: rgba(0,184,201,0.2);">
              <div>
                <div class="font-bold text-sm" style="color: #2F3542;">{{ testimonial.name }}</div>
                <div class="text-xs" style="color: #2F3542/50;">{{ testimonial.role }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 lg:py-32">
      <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="relative rounded-3xl p-12 lg:p-20 text-center overflow-hidden shadow-2xl scroll-reveal"
          style="background: linear-gradient(135deg, #00B8C9, #146C94, #FF6B6B, #FF9F1C); background-size: 300% 300%; animation: gradientShift 10s ease infinite;">
          <div class="absolute top-0 right-0 w-80 h-80 bg-white/8 rounded-full -translate-y-1/2 translate-x-1/2"></div>
          <div class="absolute bottom-0 left-0 w-64 h-64 bg-white/8 rounded-full translate-y-1/2 -translate-x-1/2"></div>
          <div class="absolute top-1/2 left-1/4 w-32 h-32 bg-white/5 rounded-full blur-3xl"></div>

          <div class="relative">
            <h2 class="text-4xl lg:text-5xl font-extrabold text-white mb-6">
              ¿Listo para crear algo <span class="text-[#F4C542]">increíble</span>?
            </h2>
            <p class="text-lg text-white/80 mb-10 max-w-2xl mx-auto">
              Transforma la imagen de tu marca con diseños profesionales, fotografías impactantes y una identidad visual única.
            </p>
            <Link :href="login()"
              class="group inline-flex items-center gap-3 px-10 py-4 rounded-full font-bold text-sm transition-all duration-300 shadow-xl hover:shadow-2xl active:scale-[0.97]"
              style="background: white; color: #146C94;">
              <span>Solicitar presupuesto gratis</span>
              <svg class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
              </svg>
            </Link>
          </div>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="py-16" style="background: #2F3542;">
      <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
          <div>
            <div class="flex items-center gap-3 mb-6">
              <div class="w-11 h-11 rounded-2xl flex items-center justify-center"
                style="background: linear-gradient(135deg, #00B8C9, #146C94, #FF6B6B, #F4C542, #4ECDC4); background-size: 300% 300%; animation: gradientShift 8s ease infinite;">
                <span class="text-white font-bold text-lg">C</span>
              </div>
              <span class="text-xl font-extrabold text-white">CREAPIX</span>
            </div>
            <p class="text-sm leading-relaxed" style="color: #CFCFCF;">
              Estudio creativo especializado en diseño gráfico, fotografía profesional, edición de imágenes e impresión de alta calidad.
            </p>
          </div>

          <div>
            <h4 class="text-white font-bold mb-5 text-sm uppercase tracking-wider">Servicios</h4>
            <ul class="space-y-3 text-sm" style="color: #CFCFCF;">
              <li><a href="#" class="hover:text-[#00B8C9] transition-colors">Diseño Gráfico</a></li>
              <li><a href="#" class="hover:text-[#FF6B6B] transition-colors">Fotografía</a></li>
              <li><a href="#" class="hover:text-[#4ECDC4] transition-colors">Edición y Retoque</a></li>
              <li><a href="#" class="hover:text-[#F4C542] transition-colors">Impresión</a></li>
              <li><a href="#" class="hover:text-[#FF9F1C] transition-colors">Branding</a></li>
            </ul>
          </div>

          <div>
            <h4 class="text-white font-bold mb-5 text-sm uppercase tracking-wider">Empresa</h4>
            <ul class="space-y-3 text-sm" style="color: #CFCFCF;">
              <li><a href="#" class="hover:text-white transition-colors">Sobre nosotros</a></li>
              <li><a href="#" class="hover:text-white transition-colors">Portafolio</a></li>
              <li><a href="#" class="hover:text-white transition-colors">Blog</a></li>
              <li><a href="#" class="hover:text-white transition-colors">Carreras</a></li>
            </ul>
          </div>

          <div>
            <h4 class="text-white font-bold mb-5 text-sm uppercase tracking-wider">Contacto</h4>
            <ul class="space-y-3 text-sm" style="color: #CFCFCF;">
              <li>hola@creapix.com</li>
              <li>+52 55 1234 5678</li>
              <li class="pt-4">
                <div class="flex gap-3">
                  <a href="#" class="w-10 h-10 flex items-center justify-center rounded-xl transition-all duration-300 hover:scale-110"
                    style="background: rgba(255,255,255,0.08);"
                    @mouseenter="($event) => { $event.currentTarget.style.background = 'linear-gradient(135deg, #00B8C9, #146C94)' }"
                    @mouseleave="($event) => { $event.currentTarget.style.background = 'rgba(255,255,255,0.08)' }">
                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                  </a>
                  <a href="#" class="w-10 h-10 flex items-center justify-center rounded-xl transition-all duration-300 hover:scale-110"
                    style="background: rgba(255,255,255,0.08);"
                    @mouseenter="($event) => { $event.currentTarget.style.background = 'linear-gradient(135deg, #00B8C9, #146C94)' }"
                    @mouseleave="($event) => { $event.currentTarget.style.background = 'rgba(255,255,255,0.08)' }">
                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                  </a>
                  <a href="#" class="w-10 h-10 flex items-center justify-center rounded-xl transition-all duration-300 hover:scale-110"
                    style="background: rgba(255,255,255,0.08);"
                    @mouseenter="($event) => { $event.currentTarget.style.background = 'linear-gradient(135deg, #00B8C9, #146C94)' }"
                    @mouseleave="($event) => { $event.currentTarget.style.background = 'rgba(255,255,255,0.08)' }">
                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                  </a>
                </div>
              </li>
            </ul>
          </div>
        </div>

        <div class="pt-8 border-t flex flex-col md:flex-row items-center justify-between gap-4"
          style="border-color: rgba(255,255,255,0.08);">
          <p class="text-sm" style="color: #CFCFCF;">&copy; 2024 CREAPIX. Todos los derechos reservados.</p>
          <div class="flex gap-6 text-sm" style="color: #CFCFCF;">
            <a href="#" class="hover:text-white transition-colors">Política de privacidad</a>
            <a href="#" class="hover:text-white transition-colors">Términos de servicio</a>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<style>
@import url('https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700;800;900&display=swap');

.font-sans {
  font-family: 'Instrument Sans', sans-serif;
}

.creative-gradient {
  background: linear-gradient(
    135deg,
    #00B8C9 0%,
    #146C94 20%,
    #FF6B6B 40%,
    #FF9F1C 60%,
    #F4C542 75%,
    #4ECDC4 90%,
    #00B8C9 100%
  );
  background-size: 400% 400%;
  -webkit-background-clip: text;
  background-clip: text;
  color: transparent;
  animation: gradientShift 6s ease infinite;
}

.canvas-texture {
  background-color: #F8F8F8;
  background-image:
    repeating-linear-gradient(0deg, transparent, transparent 2px, rgba(0,0,0,0.006) 2px, rgba(0,0,0,0.006) 4px),
    repeating-linear-gradient(90deg, transparent, transparent 2px, rgba(0,0,0,0.006) 2px, rgba(0,0,0,0.006) 4px);
}

@keyframes gradientShift {
  0% { background-position: 0% 50%; }
  25% { background-position: 100% 0%; }
  50% { background-position: 100% 100%; }
  75% { background-position: 0% 100%; }
  100% { background-position: 0% 50%; }
}

@keyframes pulseGlow {
  0%, 100% { opacity: 0.4; transform: scale(1); }
  50% { opacity: 1; transform: scale(1.3); }
}

@keyframes splash-drift {
  0%, 100% { transform: translate(0, 0) scale(1); }
  25% { transform: translate(10px, -8px) scale(1.03); }
  50% { transform: translate(-5px, 5px) scale(0.97); }
  75% { transform: translate(8px, 3px) scale(1.01); }
}

@keyframes brush-stroke {
  0% { stroke-dashoffset: 500; opacity: 0; }
  30% { opacity: 1; }
  100% { stroke-dashoffset: 0; opacity: 0.8; }
}

@keyframes rotate-slow {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

@keyframes float-shape {
  0%, 100% { transform: translateY(0px) rotate(0deg); }
  25% { transform: translateY(-10px) rotate(1.5deg); }
  75% { transform: translateY(5px) rotate(-1deg); }
}

@keyframes sparkle {
  0%, 100% { opacity: 0; transform: scale(0); }
  40% { opacity: 0.9; transform: scale(1); }
  60% { opacity: 0.6; transform: scale(0.8); }
}

@keyframes line-draw {
  from { stroke-dashoffset: 1000; }
  to { stroke-dashoffset: 0; }
}

@keyframes breath-1 {
  0%, 100% { transform: translate(0, 0) scale(1); opacity: 0.5; }
  33% { transform: translate(30px, -25px) scale(1.08); opacity: 0.7; }
  66% { transform: translate(-20px, 15px) scale(0.95); opacity: 0.4; }
}

@keyframes breath-2 {
  0%, 100% { transform: translate(0, 0) scale(1); opacity: 0.4; }
  33% { transform: translate(-30px, 25px) scale(1.06); opacity: 0.6; }
  66% { transform: translate(25px, -15px) scale(0.93); opacity: 0.3; }
}

@keyframes breath-3 {
  0%, 100% { transform: translate(-50%, -50%) scale(1); opacity: 0.3; }
  50% { transform: translate(-45%, -55%) scale(1.15); opacity: 0.5; }
}

@keyframes breath-4 {
  0%, 100% { transform: translate(0, 0) scale(1); opacity: 0.3; }
  50% { transform: translate(-15px, 10px) scale(1.1); opacity: 0.5; }
}

@keyframes breath-5 {
  0%, 100% { transform: translate(0, 0) scale(1); opacity: 0.2; }
  33% { transform: translate(20px, -10px) scale(1.08); opacity: 0.4; }
  66% { transform: translate(-10px, 15px) scale(0.95); opacity: 0.25; }
}

@keyframes breath-glow {
  0%, 100% { opacity: 0.4; transform: scale(1); }
  50% { opacity: 0.7; transform: scale(1.12); }
}

@keyframes bounce-subtle {
  0%, 100% { transform: translateX(-50%) translateY(0); }
  50% { transform: translateX(-50%) translateY(6px); }
}

.animate-splash-drift {
  animation: splash-drift 18s ease-in-out infinite;
}

.animate-brush-stroke {
  stroke-dasharray: 500;
  animation: brush-stroke 6s ease-out infinite;
}

.animate-rotate-slow {
  animation: rotate-slow 30s linear infinite;
}

.animate-float-shape {
  animation: float-shape 8s ease-in-out infinite;
}

.animate-sparkle {
  animation: sparkle 3s ease-in-out infinite;
}

.animate-line-draw {
  stroke-dasharray: 1000;
  animation: line-draw 6s ease-out forwards;
}

.animate-breath-1 {
  animation: breath-1 18s ease-in-out infinite;
}

.animate-breath-2 {
  animation: breath-2 20s ease-in-out infinite;
}

.animate-breath-3 {
  animation: breath-3 22s ease-in-out infinite;
}

.animate-breath-4 {
  animation: breath-4 16s ease-in-out infinite;
}

.animate-breath-5 {
  animation: breath-5 14s ease-in-out infinite;
}

.animate-breath-glow {
  animation: breath-glow 8s ease-in-out infinite;
}

.animate-bounce-subtle {
  animation: bounce-subtle 2.5s ease-in-out infinite;
}

::selection {
  background: rgba(0, 184, 201, 0.2);
  color: #146C94;
}
</style>
