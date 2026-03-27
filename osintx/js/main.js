// ============================================
// OsintX.net - Main JavaScript File
// ============================================

(function() {
  'use strict';

  // ============================================
  // Utility Functions
  // ============================================

  const utils = {
    // Debounce function for performance
    debounce: function(func, wait) {
      let timeout;
      return function executedFunction(...args) {
        const later = () => {
          clearTimeout(timeout);
          func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
      };
    },

    // Check if element is in viewport
    isInViewport: function(element) {
      const rect = element.getBoundingClientRect();
      return (
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
        rect.right <= (window.innerWidth || document.documentElement.clientWidth)
      );
    },

    // Smooth scroll to element
    smoothScrollTo: function(element, offset = 0) {
      const elementPosition = element.getBoundingClientRect().top;
      const offsetPosition = elementPosition + window.pageYOffset - offset;

      window.scrollTo({
        top: offsetPosition,
        behavior: 'smooth'
      });
    }
  };

  // ============================================
  // Header Scroll Effect
  // ============================================

  const initHeaderScroll = () => {
    const header = document.querySelector('header');
    if (!header) return;

    let lastScroll = 0;
    const scrollThreshold = 100;

    const handleScroll = () => {
      const currentScroll = window.pageYOffset;

      // Add shadow on scroll
      if (currentScroll > 20) {
        header.style.boxShadow = '0 4px 20px rgba(0, 0, 0, 0.3)';
      } else {
        header.style.boxShadow = 'none';
      }

      // Hide/show header on scroll
      if (currentScroll > scrollThreshold) {
        if (currentScroll > lastScroll && !header.classList.contains('scroll-down')) {
          header.classList.add('scroll-down');
          header.style.transform = 'translateY(-100%)';
        } else if (currentScroll < lastScroll && header.classList.contains('scroll-down')) {
          header.classList.remove('scroll-down');
          header.style.transform = 'translateY(0)';
        }
      }

      lastScroll = currentScroll;
    };

    window.addEventListener('scroll', utils.debounce(handleScroll, 10));
  };

  // ============================================
  // Smooth Scroll for Navigation Links
  // ============================================

  const initSmoothScroll = () => {
    const links = document.querySelectorAll('a[href^="#"]');
    
    links.forEach(link => {
      link.addEventListener('click', (e) => {
        const href = link.getAttribute('href');
        if (href === '#' || href === '') return;

        const target = document.querySelector(href);
        if (target) {
          e.preventDefault();
          const headerHeight = document.querySelector('header')?.offsetHeight || 80;
          utils.smoothScrollTo(target, headerHeight);
        }
      });
    });
  };

  // ============================================
  // Animated Counters
  // ============================================

  const initCounters = () => {
    const counters = document.querySelectorAll('.stat-number');
    if (counters.length === 0) return;

    const animateCounter = (element) => {
      const target = parseInt(element.getAttribute('data-target') || element.textContent.replace(/[^0-9]/g, ''));
      const duration = 2000;
      const increment = target / (duration / 16);
      let current = 0;

      const updateCounter = () => {
        current += increment;
        if (current < target) {
          element.textContent = Math.ceil(current).toLocaleString() + (element.textContent.includes('+') ? '+' : '');
          requestAnimationFrame(updateCounter);
        } else {
          element.textContent = target.toLocaleString() + (element.textContent.includes('+') ? '+' : '');
        }
      };

      updateCounter();
    };

    const observerOptions = {
      threshold: 0.5,
      rootMargin: '0px'
    };

    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting && !entry.target.classList.contains('counted')) {
          entry.target.classList.add('counted');
          animateCounter(entry.target);
        }
      });
    }, observerOptions);

    counters.forEach(counter => {
      // Set data-target attribute if not present
      if (!counter.hasAttribute('data-target')) {
        const value = parseInt(counter.textContent.replace(/[^0-9]/g, ''));
        counter.setAttribute('data-target', value);
        counter.textContent = '0';
      }
      observer.observe(counter);
    });
  };

  // ============================================
  // FAQ Accordion
  // ============================================

  const initFAQ = () => {
    const faqItems = document.querySelectorAll('.faq-item');
    if (faqItems.length === 0) return;

    faqItems.forEach(item => {
      const question = item.querySelector('.faq-question');
      const answer = item.querySelector('.faq-answer');
      
      if (!question || !answer) return;

      question.addEventListener('click', () => {
        const isActive = item.classList.contains('active');
        
        // Close all other items
        faqItems.forEach(otherItem => {
          if (otherItem !== item) {
            otherItem.classList.remove('active');
          }
        });

        // Toggle current item
        item.classList.toggle('active');
      });
    });
  };

  // ============================================
  // Testimonials Carousel
  // ============================================

  const initTestimonialsCarousel = () => {
    const track = document.querySelector('.testimonials-track');
    if (!track) return;

    // Clone testimonials for infinite scroll effect
    const cards = Array.from(track.children);
    cards.forEach(card => {
      const clone = card.cloneNode(true);
      track.appendChild(clone);
    });

    // Pause on hover
    track.addEventListener('mouseenter', () => {
      track.style.animationPlayState = 'paused';
    });

    track.addEventListener('mouseleave', () => {
      track.style.animationPlayState = 'running';
    });
  };

  // ============================================
  // Scroll Reveal Animations
  // ============================================

  const initScrollReveal = () => {
    const reveals = document.querySelectorAll('.feature-card, .pricing-card, .stat-card');
    if (reveals.length === 0) return;

    const observerOptions = {
      threshold: 0.1,
      rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry, index) => {
        if (entry.isIntersecting) {
          setTimeout(() => {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
          }, index * 100);
          observer.unobserve(entry.target);
        }
      });
    }, observerOptions);

    reveals.forEach(element => {
      element.style.opacity = '0';
      element.style.transform = 'translateY(30px)';
      element.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
      observer.observe(element);
    });
  };

  // ============================================
  // Mobile Menu Toggle
  // ============================================

  const initMobileMenu = () => {
    const menuToggle = document.querySelector('.mobile-menu-toggle');
    const nav = document.querySelector('.header-nav');
    
    if (!menuToggle || !nav) return;

    menuToggle.addEventListener('click', () => {
      nav.classList.toggle('active');
      menuToggle.classList.toggle('active');
      document.body.classList.toggle('menu-open');
    });

    // Close menu when clicking outside
    document.addEventListener('click', (e) => {
      if (!nav.contains(e.target) && !menuToggle.contains(e.target)) {
        nav.classList.remove('active');
        menuToggle.classList.remove('active');
        document.body.classList.remove('menu-open');
      }
    });

    // Close menu when clicking on a link
    const navLinks = nav.querySelectorAll('a');
    navLinks.forEach(link => {
      link.addEventListener('click', () => {
        nav.classList.remove('active');
        menuToggle.classList.remove('active');
        document.body.classList.remove('menu-open');
      });
    });
  };

  // ============================================
  // Code Copy Functionality
  // ============================================

  const initCodeCopy = () => {
    const codeBlocks = document.querySelectorAll('.code-block');
    
    codeBlocks.forEach(block => {
      const copyBtn = block.querySelector('.copy-btn');
      const code = block.querySelector('code');
      
      if (!copyBtn || !code) return;

      copyBtn.addEventListener('click', async () => {
        try {
          await navigator.clipboard.writeText(code.textContent);
          const originalText = copyBtn.textContent;
          copyBtn.textContent = 'Copied!';
          copyBtn.style.background = '#10b981';
          
          setTimeout(() => {
            copyBtn.textContent = originalText;
            copyBtn.style.background = '';
          }, 2000);
        } catch (err) {
          console.error('Failed to copy:', err);
        }
      });
    });
  };

  // ============================================
  // Search Demo Functionality
  // ============================================

  const initSearchDemo = () => {
    const searchForm = document.querySelector('.search-form');
    const searchInput = document.querySelector('.search-input');
    const searchBtn = document.querySelector('.search-btn');
    
    if (!searchForm || !searchInput || !searchBtn) return;

    searchForm.addEventListener('submit', (e) => {
      e.preventDefault();
      const query = searchInput.value.trim();
      
      if (query) {
        // Add loading state
        searchBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Searching...';
        searchBtn.disabled = true;
        
        // Simulate search (replace with actual API call)
        setTimeout(() => {
          searchBtn.innerHTML = '<i class="fas fa-search"></i> Search';
          searchBtn.disabled = false;
          alert(`Demo search for: ${query}\n\nThis is a demo. Sign up to access real search functionality.`);
        }, 1500);
      }
    });
  };

  // ============================================
  // Typing Animation
  // ============================================

  const initTypingAnimation = () => {
    const typingElement = document.querySelector('.typing-text');
    if (!typingElement) return;

    const words = ['Email', 'Phone', 'Username', 'IP Address', 'Domain'];
    let wordIndex = 0;
    let charIndex = 0;
    let isDeleting = false;
    const typingSpeed = 100;
    const deletingSpeed = 50;
    const pauseTime = 2000;

    function type() {
      const currentWord = words[wordIndex];
      
      if (isDeleting) {
        typingElement.textContent = currentWord.substring(0, charIndex - 1);
        charIndex--;
      } else {
        typingElement.textContent = currentWord.substring(0, charIndex + 1);
        charIndex++;
      }

      let timeout = isDeleting ? deletingSpeed : typingSpeed;

      if (!isDeleting && charIndex === currentWord.length) {
        timeout = pauseTime;
        isDeleting = true;
      } else if (isDeleting && charIndex === 0) {
        isDeleting = false;
        wordIndex = (wordIndex + 1) % words.length;
      }

      setTimeout(type, timeout);
    }

    type();
  };

  // ============================================
  // Particle Background Effect
  // ============================================

  const initParticles = () => {
    const canvas = document.querySelector('#particles-canvas');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    let particles = [];
    let animationId;

    function resizeCanvas() {
      canvas.width = window.innerWidth;
      canvas.height = window.innerHeight;
    }

    class Particle {
      constructor() {
        this.x = Math.random() * canvas.width;
        this.y = Math.random() * canvas.height;
        this.size = Math.random() * 2 + 0.5;
        this.speedX = Math.random() * 0.5 - 0.25;
        this.speedY = Math.random() * 0.5 - 0.25;
        this.opacity = Math.random() * 0.5 + 0.2;
      }

      update() {
        this.x += this.speedX;
        this.y += this.speedY;

        if (this.x > canvas.width) this.x = 0;
        if (this.x < 0) this.x = canvas.width;
        if (this.y > canvas.height) this.y = 0;
        if (this.y < 0) this.y = canvas.height;
      }

      draw() {
        ctx.fillStyle = `rgba(255, 255, 255, ${this.opacity})`;
        ctx.beginPath();
        ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
        ctx.fill();
      }
    }

    function init() {
      resizeCanvas();
      particles = [];
      for (let i = 0; i < 100; i++) {
        particles.push(new Particle());
      }
    }

    function animate() {
      ctx.clearRect(0, 0, canvas.width, canvas.height);
      particles.forEach(particle => {
        particle.update();
        particle.draw();
      });
      animationId = requestAnimationFrame(animate);
    }

    init();
    animate();

    window.addEventListener('resize', utils.debounce(() => {
      init();
    }, 250));
  };

  // ============================================
  // Form Validation
  // ============================================

  const initFormValidation = () => {
    const forms = document.querySelectorAll('form[data-validate]');
    
    forms.forEach(form => {
      form.addEventListener('submit', (e) => {
        let isValid = true;
        const inputs = form.querySelectorAll('input[required], textarea[required]');
        
        inputs.forEach(input => {
          if (!input.value.trim()) {
            isValid = false;
            input.classList.add('error');
            
            // Remove error class on input
            input.addEventListener('input', () => {
              input.classList.remove('error');
            }, { once: true });
          }
        });

        // Email validation
        const emailInputs = form.querySelectorAll('input[type="email"]');
        emailInputs.forEach(input => {
          const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
          if (input.value && !emailRegex.test(input.value)) {
            isValid = false;
            input.classList.add('error');
          }
        });

        if (!isValid) {
          e.preventDefault();
        }
      });
    });
  };

  // ============================================
  // Back to Top Button
  // ============================================

  const initBackToTop = () => {
    const backToTopBtn = document.querySelector('.back-to-top');
    if (!backToTopBtn) return;

    window.addEventListener('scroll', () => {
      if (window.pageYOffset > 300) {
        backToTopBtn.classList.add('visible');
      } else {
        backToTopBtn.classList.remove('visible');
      }
    });

    backToTopBtn.addEventListener('click', () => {
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      });
    });
  };

  // ============================================
  // Lazy Loading Images
  // ============================================

  const initLazyLoading = () => {
    const images = document.querySelectorAll('img[data-src]');
    
    const imageObserver = new IntersectionObserver((entries, observer) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const img = entry.target;
          img.src = img.dataset.src;
          img.classList.add('loaded');
          observer.unobserve(img);
        }
      });
    });

    images.forEach(img => imageObserver.observe(img));
  };

  // ============================================
  // Initialize All Functions
  // ============================================

  const init = () => {
    // Wait for DOM to be fully loaded
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', init);
      return;
    }

    // Initialize all modules
    initHeaderScroll();
    initSmoothScroll();
    initCounters();
    initFAQ();
    initTestimonialsCarousel();
    initScrollReveal();
    initMobileMenu();
    initCodeCopy();
    initSearchDemo();
    initTypingAnimation();
    initParticles();
    initFormValidation();
    initBackToTop();
    initLazyLoading();

    // Add loaded class to body
    document.body.classList.add('loaded');

    console.log('OsintX.net initialized successfully');
  };

  // Start initialization
  init();

})();