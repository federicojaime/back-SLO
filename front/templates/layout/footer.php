<?php
// templates/layout/footer.php - Footer común del frontend
?>

    <!-- Main Footer -->
    <footer class="main-footer">
        <div class="container">
            <div class="row g-5">
                <!-- About Section -->
                <div class="col-lg-4 col-md-6">
                    <div class="footer-section">
                        <div class="footer-logo"><?= SITE_NAME ?></div>
                        <p class="footer-description">
                            El portal de noticias más confiable de San Luis. Información veraz, 
                            actualizada y relevante para mantenerte informado sobre todo lo que 
                            acontece en nuestra provincia.
                        </p>
                        
                        <div class="social-links">
                            <a href="<?= FACEBOOK_URL ?>" target="_blank" class="social-link" aria-label="Facebook">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="<?= INSTAGRAM_URL ?>" target="_blank" class="social-link" aria-label="Instagram">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="<?= TWITTER_URL ?>" target="_blank" class="social-link" aria-label="Twitter">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="<?= YOUTUBE_URL ?>" target="_blank" class="social-link" aria-label="YouTube">
                                <i class="fab fa-youtube"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Navigation Links -->
                <div class="col-lg-2 col-md-6">
                    <div class="footer-section">
                        <h5>Navegación</h5>
                        <ul class="footer-links">
                            <li><a href="<?= SITE_URL ?>">Inicio</a></li>
                            <?php 
                            $categories = get_categories();
                            $limitedCategories = array_slice($categories, 0, 5);
                            foreach ($limitedCategories as $category): 
                            ?>
                                <li><a href="<?= SITE_URL ?>/categoria/<?= $category['slug'] ?>"><?= safe_html($category['name']) ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>

                <!-- Radio Section -->
                <div class="col-lg-3 col-md-6">
                    <div class="footer-section">
                        <h5>Radio <?= RADIO_FREQUENCY ?></h5>
                        <ul class="footer-links">
                            <li>
                                <i class="fas fa-broadcast-tower me-2"></i>
                                Frecuencia: <?= RADIO_FREQUENCY ?>
                            </li>
                            <li>
                                <i class="fas fa-phone me-2"></i>
                                Tel: <?= RADIO_PHONE ?>
                            </li>
                            <li>
                                <i class="fas fa-whatsapp me-2"></i>
                                WhatsApp: <?= WHATSAPP_NUMBER ?>
                            </li>
                        </ul>
                        
                        <div class="radio-player mt-3">
                            <button class="btn btn-accent" id="radio-toggle">
                                <i class="fas fa-play" id="radio-icon"></i>
                                <span id="radio-text">Escuchar en Vivo</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Contact Info -->
                <div class="col-lg-3 col-md-6">
                    <div class="footer-section">
                        <h5>Contacto</h5>
                        <ul class="footer-links">
                            <li>
                                <i class="fas fa-envelope me-2"></i>
                                <a href="mailto:<?= CONTACT_EMAIL ?>"><?= CONTACT_EMAIL ?></a>
                            </li>
                            <li>
                                <i class="fas fa-map-marker-alt me-2"></i>
                                <?= CONTACT_ADDRESS ?>
                            </li>
                            <li>
                                <i class="fas fa-clock me-2"></i>
                                Lun - Vie: 8:00 - 20:00
                            </li>
                        </ul>
                        
                        <div class="mt-3">
                            <a href="<?= SITE_URL ?>/contacto" class="btn btn-outline">
                                <i class="fas fa-paper-plane me-2"></i>
                                Enviar Mensaje
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer Bottom -->
        <div class="footer-bottom">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <p class="mb-0">
                            &copy; <?= date('Y') ?> <?= SITE_NAME ?>. Todos los derechos reservados.
                        </p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <div class="footer-links-inline">
                            <a href="<?= SITE_URL ?>/privacidad">Política de Privacidad</a>
                            <a href="<?= SITE_URL ?>/terminos">Términos de Uso</a>
                            <a href="<?= SITE_URL ?>/rss" target="_blank">RSS</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scroll to Top Button -->
    <button class="scroll-top" id="scroll-top" aria-label="Volver arriba">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= SITE_URL ?>/assets/js/app.js"></script>

    <!-- Radio Player Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const radioToggle = document.getElementById('radio-toggle');
            const radioIcon = document.getElementById('radio-icon');
            const radioText = document.getElementById('radio-text');
            let audioPlayer = null;
            let isPlaying = false;

            radioToggle.addEventListener('click', function() {
                if (!isPlaying) {
                    // Start playing
                    if (!audioPlayer) {
                        audioPlayer = new Audio('<?= RADIO_STREAM_URL ?>');
                        audioPlayer.preload = 'none';
                    }
                    
                    audioPlayer.play().then(() => {
                        isPlaying = true;
                        radioIcon.className = 'fas fa-stop';
                        radioText.textContent = 'Detener Radio';
                        radioToggle.classList.add('playing');
                    }).catch(error => {
                        console.error('Error playing radio:', error);
                        alert('Error al conectar con la radio. Intenta nuevamente.');
                    });
                } else {
                    // Stop playing
                    if (audioPlayer) {
                        audioPlayer.pause();
                        audioPlayer.currentTime = 0;
                    }
                    isPlaying = false;
                    radioIcon.className = 'fas fa-play';
                    radioText.textContent = 'Escuchar en Vivo';
                    radioToggle.classList.remove('playing');
                }
            });

            // Handle audio errors
            if (audioPlayer) {
                audioPlayer.addEventListener('error', function() {
                    isPlaying = false;
                    radioIcon.className = 'fas fa-play';
                    radioText.textContent = 'Escuchar en Vivo';
                    radioToggle.classList.remove('playing');
                });
            }
        });

        // Scroll to Top functionality
        window.addEventListener('scroll', function() {
            const scrollTop = document.getElementById('scroll-top');
            if (window.pageYOffset > 300) {
                scrollTop.classList.add('visible');
            } else {
                scrollTop.classList.remove('visible');
            }
        });

        document.getElementById('scroll-top').addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        // Analytics tracking (if needed)
        function trackEvent(category, action, label) {
            if (typeof gtag !== 'undefined') {
                gtag('event', action, {
                    event_category: category,
                    event_label: label
                });
            }
        }

        // Track social media clicks
        document.querySelectorAll('.social-link').forEach(link => {
            link.addEventListener('click', function() {
                const platform = this.getAttribute('aria-label');
                trackEvent('Social Media', 'Click', platform);
            });
        });

        // Track newsletter signups (if form exists)
        const newsletterForm = document.querySelector('.newsletter-form');
        if (newsletterForm) {
            newsletterForm.addEventListener('submit', function() {
                trackEvent('Newsletter', 'Subscribe', 'Footer');
            });
        }

        // Service Worker registration (for PWA features)
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('<?= SITE_URL ?>/sw.js')
                    .then(registration => {
                        console.log('SW registered: ', registration);
                    })
                    .catch(registrationError => {
                        console.log('SW registration failed: ', registrationError);
                    });
            });
        }
    </script>

    <!-- Schema.org Structured Data for Website -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "NewsMediaOrganization",
        "name": "<?= SITE_NAME ?>",
        "url": "<?= SITE_URL ?>",
        "logo": "<?= SITE_URL ?><?= LOGO_URL ?>",
        "sameAs": [
            "<?= FACEBOOK_URL ?>",
            "<?= TWITTER_URL ?>",
            "<?= INSTAGRAM_URL ?>",
            "<?= YOUTUBE_URL ?>"
        ],
        "contactPoint": {
            "@type": "ContactPoint",
            "telephone": "<?= CONTACT_PHONE ?>",
            "contactType": "customer service",
            "email": "<?= CONTACT_EMAIL ?>"
        },
        "address": {
            "@type": "PostalAddress",
            "addressLocality": "San Luis",
            "addressRegion": "San Luis",
            "addressCountry": "AR",
            "streetAddress": "<?= CONTACT_ADDRESS ?>"
        }
    }
    </script>

</body>
</html>