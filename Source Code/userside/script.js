/**
 * ELEGANCE SALON KARACHI - MASTER CONSOLIDATED JAVASCRIPT
 * Clean, Professional, Centralized PHP Architecture
 */

document.addEventListener('DOMContentLoaded', () => {
    // ==================================================
    // 1. GLOBAL LENIS SMOOTH SCROLL INITIALIZATION
    // ==================================================
    let globalLenis = null;
    if (typeof Lenis !== 'undefined') {
        globalLenis = new Lenis({
            duration: 1.2,
            easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
            smooth: true,
        });

        window.lenis = globalLenis;

        function raf(time) {
            globalLenis.raf(time);
            requestAnimationFrame(raf);
        }
        requestAnimationFrame(raf);
    }

    // ==================================================
    // 2. GLOBAL AOS ANIMATION INITIALIZATION
    // ==================================================
    if (typeof AOS !== 'undefined') {
        AOS.init({
            once: true,
            offset: 80,
            duration: 1000,
            easing: 'cubic-bezier(0.16, 1, 0.3, 1)',
        });
    }

    // ==================================================
    // 3. UNIVERSAL STICKY NAVBAR & BACK TO TOP LOGIC
    // ==================================================
    const navbar = document.getElementById('main-navbar');
    const backToTopBtn = document.getElementById('backToTopBtn');

    function handleScrollEvents() {
        const scrollYPos = window.pageYOffset || document.documentElement.scrollTop || window.scrollY || 0;
        
        const heroSection = document.getElementById('hero-section') || 
                            document.querySelector('.reviews-hero') || 
                            document.querySelector('.about-hero') || 
                            document.querySelector('.contact-hero') || 
                            document.querySelector('.blog-hero') || 
                            document.querySelector('.service-hero') || 
                            document.querySelector('.hero-wrapper') ||
                            document.querySelector('.appointment-hero');
        
        // Calculate hero section bottom threshold
        const heroHeight = heroSection ? heroSection.offsetHeight : 300;
        const triggerThreshold = Math.max(heroHeight - 100, 220);

        // Sticky Navbar effect
        if (navbar) {
            if (scrollYPos > 40) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        }

        // Floating Back to Top Button: Becomes active immediately after hero section
        if (backToTopBtn) {
            if (scrollYPos > triggerThreshold) {
                backToTopBtn.classList.add('show');
            } else {
                backToTopBtn.classList.remove('show');
            }
        }
    }

    // Attach to window scroll & Lenis
    window.addEventListener('scroll', handleScrollEvents, { passive: true });
    if (globalLenis) {
        globalLenis.on('scroll', handleScrollEvents);
    }
    // Initial check on page load
    handleScrollEvents();

    if (backToTopBtn) {
        backToTopBtn.addEventListener('click', (e) => {
            e.preventDefault();
            if (window.lenis) {
                window.lenis.scrollTo(0, { duration: 1.2 });
            }
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    if (navbar) {
        const navbarCollapse = document.getElementById('navbarNav');
        const mobileMenuToggler = document.getElementById('mobileMenuToggler');
        const drawerCloseBtn = document.getElementById('drawerCloseBtn');
        const mobileNavBackdrop = document.getElementById('mobileNavBackdrop');

        function openMobileDrawer() {
            if (navbarCollapse) navbarCollapse.classList.add('mobile-menu-active');
            if (mobileNavBackdrop) mobileNavBackdrop.classList.add('active');
            if (mobileMenuToggler) {
                mobileMenuToggler.classList.remove('collapsed');
                mobileMenuToggler.setAttribute('aria-expanded', 'true');
            }
            document.body.style.overflow = 'hidden';
        }

        function closeMobileDrawer() {
            if (navbarCollapse) navbarCollapse.classList.remove('mobile-menu-active');
            if (mobileNavBackdrop) mobileNavBackdrop.classList.remove('active');
            if (mobileMenuToggler) {
                mobileMenuToggler.classList.add('collapsed');
                mobileMenuToggler.setAttribute('aria-expanded', 'false');
            }
            navbar.querySelectorAll('li.dropdown').forEach(d => d.classList.remove('mobile-open'));
            document.body.style.overflow = '';
        }

        // Toggler click
        if (mobileMenuToggler) {
            mobileMenuToggler.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                if (navbarCollapse && navbarCollapse.classList.contains('mobile-menu-active')) {
                    closeMobileDrawer();
                } else {
                    openMobileDrawer();
                }
            });
        }

        // Close button inside drawer
        if (drawerCloseBtn) {
            drawerCloseBtn.addEventListener('click', (e) => {
                e.preventDefault();
                closeMobileDrawer();
            });
        }

        // Backdrop tap closes drawer
        if (mobileNavBackdrop) {
            mobileNavBackdrop.addEventListener('click', closeMobileDrawer);
        }

        // Mobile Dropdown Tap Support (Smooth Accordion Toggle)
        const dropdownToggles = navbar.querySelectorAll('li.dropdown > a');
        dropdownToggles.forEach(toggle => {
            toggle.addEventListener('click', function(e) {
                if (window.innerWidth <= 991) {
                    e.preventDefault();
                    e.stopPropagation();
                    const parentLi = this.closest('li.dropdown');
                    if (!parentLi) return;

                    const isAlreadyOpen = parentLi.classList.contains('mobile-open');

                    // Close all other open dropdowns
                    navbar.querySelectorAll('li.dropdown').forEach(d => {
                        if (d !== parentLi) d.classList.remove('mobile-open');
                    });

                    // Toggle current dropdown
                    parentLi.classList.toggle('mobile-open', !isAlreadyOpen);
                }
            });
        });

        // Auto close mobile drawer on clicking any direct navigational link
        const navLinks = navbar.querySelectorAll('.nav-links > li:not(.dropdown) > a, .dropdown-list a, .account-dropdown a, .mobile-nav-book-btn');
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 991) {
                    closeMobileDrawer();
                }
            });
        });

        // Close drawer on ESC key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeMobileDrawer();
            }
        });

        // Window resize safeguard
        window.addEventListener('resize', () => {
            if (window.innerWidth > 991) {
                closeMobileDrawer();
            }
        });
    }

    // ==================================================
    // 4. HOME PAGE STYLISTS CAROUSEL SLIDER
    // ==================================================
    const track = document.querySelector('.stylists-track-custom');
    const sliderContainer = document.querySelector('.stylists-right-slider');
    const nextBtn = document.querySelector('.next-btn');
    const prevBtn = document.querySelector('.prev-btn');

    if (track && nextBtn && prevBtn) {
        let currentIndex = 0;
        let isDragging = false;
        let startPos = 0;
        let currentTranslate = 0;
        let prevTranslate = 0;

        function getCardsPerView() {
            if (window.innerWidth < 768) return 1;
            if (window.innerWidth < 1200) return 2;
            return 3;
        }

        function updateSliderPosition() {
            if (!track.children.length) return;
            const cardItem = track.children[0];
            const cardWidth = cardItem.getBoundingClientRect().width;
            const gap = 20;
            const cardsPerView = getCardsPerView();
            const maxIndex = Math.max(0, track.children.length - cardsPerView);

            if (currentIndex > maxIndex) {
                currentIndex = maxIndex;
            }
            if (currentIndex < 0) {
                currentIndex = 0;
            }

            const moveAmount = currentIndex * (cardWidth + gap);
            currentTranslate = -moveAmount;
            prevTranslate = currentTranslate;
            track.style.transition = 'transform 0.5s cubic-bezier(0.16, 1, 0.3, 1)';
            track.style.transform = `translateX(${currentTranslate}px)`;
        }

        nextBtn.addEventListener('click', () => {
            const cardsPerView = getCardsPerView();
            const totalSlides = track.children.length;
            const maxIndex = Math.max(0, totalSlides - cardsPerView);

            if (currentIndex < maxIndex) {
                currentIndex++;
            } else {
                currentIndex = 0;
            }
            updateSliderPosition();
        });

        prevBtn.addEventListener('click', () => {
            const cardsPerView = getCardsPerView();
            const totalSlides = track.children.length;
            const maxIndex = Math.max(0, totalSlides - cardsPerView);

            if (currentIndex > 0) {
                currentIndex--;
            } else {
                currentIndex = maxIndex;
            }
            updateSliderPosition();
        });

        // Mouse Drag / Grab & Touch Swipe Support
        if (sliderContainer) {
            // Touch events
            sliderContainer.addEventListener('touchstart', (e) => {
                isDragging = true;
                startPos = e.touches[0].clientX;
                track.style.transition = 'none';
            }, { passive: true });

            sliderContainer.addEventListener('touchmove', (e) => {
                if (!isDragging) return;
                const currentPosition = e.touches[0].clientX;
                const diff = currentPosition - startPos;
                track.style.transform = `translateX(${prevTranslate + diff}px)`;
            }, { passive: true });

            sliderContainer.addEventListener('touchend', (e) => {
                if (!isDragging) return;
                isDragging = false;
                const endPos = e.changedTouches[0].clientX;
                const diff = endPos - startPos;
                if (diff < -40) {
                    nextBtn.click();
                } else if (diff > 40) {
                    prevBtn.click();
                } else {
                    updateSliderPosition();
                }
            });

            // Desktop Mouse Drag events
            sliderContainer.addEventListener('mousedown', (e) => {
                isDragging = true;
                startPos = e.clientX;
                track.style.transition = 'none';
            });

            sliderContainer.addEventListener('mousemove', (e) => {
                if (!isDragging) return;
                e.preventDefault();
                const diff = e.clientX - startPos;
                track.style.transform = `translateX(${prevTranslate + diff}px)`;
            });

            const endDrag = (e) => {
                if (!isDragging) return;
                isDragging = false;
                const diff = e.clientX - startPos;
                if (diff < -50) {
                    nextBtn.click();
                } else if (diff > 50) {
                    prevBtn.click();
                } else {
                    updateSliderPosition();
                }
            };

            sliderContainer.addEventListener('mouseup', endDrag);
            sliderContainer.addEventListener('mouseleave', () => {
                if (isDragging) {
                    isDragging = false;
                    updateSliderPosition();
                }
            });

            // Prevent drag ghost image
            sliderContainer.querySelectorAll('img').forEach(img => {
                img.addEventListener('dragstart', (e) => e.preventDefault());
            });
        }

        window.addEventListener('resize', () => {
            updateSliderPosition();
        });

        // Initialize on load
        setTimeout(updateSliderPosition, 100);
    }

    // ==================================================
    // 5. ABOUT PAGE: DYNAMIC OPENING HOURS & FAQ ACCORDION
    // ==================================================
    const hourCards = document.querySelectorAll('.hour-card');
    if (hourCards.length > 0) {
        const daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        const currentDayName = daysOfWeek[new Date().getDay()];

        hourCards.forEach(card => {
            const cardDay = card.getAttribute('data-day');
            card.classList.remove('active-day');
            if (cardDay === currentDayName) {
                card.classList.add('active-day');
            }
        });
    }

    const faqItems = document.querySelectorAll('.faq-item');
    if (faqItems.length > 0) {
        faqItems.forEach(item => {
            item.addEventListener('click', () => {
                const isActive = item.classList.contains('active');
                faqItems.forEach(other => other.classList.remove('active'));
                if (!isActive) {
                    item.classList.add('active');
                }
            });
        });
    }

    // ==================================================
    // 6. CONTACT PAGE: FORM VALIDATION
    // ==================================================
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        const fullName = document.getElementById('fullName');
        const email = document.getElementById('email');
        const phone = document.getElementById('phone');
        const service = document.getElementById('service');
        const message = document.getElementById('message');

        const nameError = document.getElementById('nameError');
        const emailError = document.getElementById('emailError');
        const phoneError = document.getElementById('phoneError');
        const serviceError = document.getElementById('serviceError');
        const messageError = document.getElementById('messageError');
        const successAlert = document.getElementById('successAlert');

        function showError(inputElement, errorElement) {
            if (inputElement) inputElement.classList.add('is-invalid');
            if (errorElement) errorElement.style.display = 'block';
        }

        function clearError(inputElement, errorElement) {
            if (inputElement) inputElement.classList.remove('is-invalid');
            if (errorElement) errorElement.style.display = 'none';
        }

        contactForm.addEventListener('submit', function(e) {
            let isValid = true;

            if (fullName && fullName.value.trim().length < 3) {
                showError(fullName, nameError);
                isValid = false;
            } else {
                clearError(fullName, nameError);
            }

            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (email && !emailPattern.test(email.value.trim())) {
                showError(email, emailError);
                isValid = false;
            } else {
                clearError(email, emailError);
            }

            const phonePattern = /^03[0-9]{9}$/;
            if (phone && !phonePattern.test(phone.value.trim())) {
                showError(phone, phoneError);
                isValid = false;
            } else {
                clearError(phone, phoneError);
            }

            if (service && service.value === "") {
                showError(service, serviceError);
                isValid = false;
            } else {
                clearError(service, serviceError);
            }

            if (message && message.value.trim().length < 5) {
                showError(message, messageError);
                isValid = false;
            } else {
                clearError(message, messageError);
            }

            if (!isValid) {
                e.preventDefault();
            }
        });

        [fullName, email, phone, service, message].forEach(element => {
            if (element) {
                element.addEventListener('input', function() {
                    if (this.classList.contains('is-invalid')) {
                        this.classList.remove('is-invalid');
                        const errorElem = document.getElementById(this.id + 'Error');
                        if (errorElem) errorElem.style.display = 'none';
                    }
                });
            }
        });
    }

    // ==================================================
    // 7. AUTH PAGES: LOGIN, REGISTER, ADMIN-LOGIN
    // ==================================================
    const salonLoginForm = document.getElementById('salonLoginForm');
    if (salonLoginForm) {
        salonLoginForm.addEventListener('submit', function(e) {
            const email = document.getElementById('loginEmail');
            const pass = document.getElementById('loginPass');
            const emailErr = document.getElementById('emailErr');
            const passErr = document.getElementById('passErr');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            let isValid = true;

            if (email && !emailRegex.test(email.value.trim())) {
                if (emailErr) emailErr.style.display = 'block';
                email.style.borderColor = '#ff5c5c';
                isValid = false;
            } else if (email) {
                if (emailErr) emailErr.style.display = 'none';
                email.style.borderColor = 'rgba(255,255,255,0.12)';
            }

            if (pass && pass.value.length < 6) {
                if (passErr) passErr.style.display = 'block';
                pass.style.borderColor = '#ff5c5c';
                isValid = false;
            } else if (pass) {
                if (passErr) passErr.style.display = 'none';
                pass.style.borderColor = 'rgba(255,255,255,0.12)';
            }

            if (!isValid) {
                e.preventDefault();
            }
        });
    }

    const salonRegisterForm = document.getElementById('salonRegForm') || document.getElementById('salonRegisterForm');
    if (salonRegisterForm) {
        salonRegisterForm.addEventListener('submit', function(e) {
            const name = document.getElementById('fullname') || document.getElementById('regName');
            const email = document.getElementById('email') || document.getElementById('regEmail');
            const phone = document.getElementById('phone') || document.getElementById('regPhone');
            const pass = document.getElementById('pass') || document.getElementById('regPass');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            const phoneRegex = /^[0-9]{10,12}$/;
            let isValid = true;

            if (name && name.value.trim().length < 3) {
                showRegError('nameErr', name.id);
                isValid = false;
            } else if (name) hideRegError('nameErr', name.id);

            if (email && !emailRegex.test(email.value.trim())) {
                showRegErrorCustom('emailErr', email.id, 'Enter a valid email structure.');
                isValid = false;
            } else if (email) {
                hideRegError('emailErr', email.id);
            }

            if (phone && !phoneRegex.test(phone.value.trim())) {
                showRegError('phoneErr', phone.id);
                isValid = false;
            } else if (phone) hideRegError('phoneErr', phone.id);

            if (pass && pass.value.length < 6) {
                showRegError('passErr', pass.id);
                isValid = false;
            } else if (pass) hideRegError('passErr', pass.id);

            if (!isValid) {
                e.preventDefault();
            }
        });

        function showRegError(errId, inputId) {
            const err = document.getElementById(errId);
            const input = document.getElementById(inputId);
            if (err) err.style.display = 'block';
            if (input) input.style.borderColor = '#ff5c5c';
        }

        function showRegErrorCustom(errId, inputId, message) {
            const err = document.getElementById(errId);
            const input = document.getElementById(inputId);
            if (err) {
                err.innerText = message;
                err.style.display = 'block';
            }
            if (input) input.style.borderColor = '#ff5c5c';
        }

        function hideRegError(errId, inputId) {
            const err = document.getElementById(errId);
            const input = document.getElementById(inputId);
            if (err) err.style.display = 'none';
            if (input) input.style.borderColor = 'rgba(255,255,255,0.15)';
        }
    }

    const adminLoginForm = document.getElementById('adminLoginForm');
    if (adminLoginForm) {
        adminLoginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const username = document.getElementById('adminUser');
            const pass = document.getElementById('adminPass');
            const role = document.getElementById('adminRole');
            const err = document.getElementById('adminErr');

            if (username && pass && username.value.trim() && pass.value.trim()) {
                if (err) err.style.display = 'none';
                alert("✓ Access Granted! Redirecting to Dashboard...");
                if (role && role.value === 'reception') {
                    window.location.href = 'dashboard/reception.php';
                } else {
                    window.location.href = 'dashboard/admin.php';
                }
            } else {
                if (err) err.style.display = 'block';
            }
        });
    }

    // Initialize Reviews Pagination if on reviews page
    if (document.getElementById('reviewsGrid')) {
        initReviewsPagination();
    }
});

// ==================================================
// 8. GLOBAL FUNCTIONS: REVIEWS PAGE PAGINATION & FILTER
// ==================================================
let visibleReviewCount = 6;

function initReviewsPagination() {
    const items = document.querySelectorAll('.review-item');
    items.forEach((item, index) => {
        if (index >= visibleReviewCount) {
            item.style.display = 'none';
        } else {
            item.style.display = 'block';
        }
    });
}

function loadMoreReviews() {
    const items = document.querySelectorAll('.review-item');
    visibleReviewCount += 6;
    items.forEach((item, index) => {
        if (index < visibleReviewCount && item.style.display === 'none') {
            item.style.display = 'block';
        }
    });
    const btn = document.getElementById('loadMoreBtn');
    if (btn && visibleReviewCount >= items.length) {
        btn.style.display = 'none';
    }
}

function filterReviews(category, btn) {
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
    if (btn) btn.classList.add('active');

    const items = document.querySelectorAll('.review-item');
    items.forEach(item => {
        if (category === 'all' || item.getAttribute('data-category') === category) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });

    const loadMoreBtn = document.getElementById('loadMoreBtn');
    if (category !== 'all') {
        if (loadMoreBtn) loadMoreBtn.style.display = 'none';
    } else {
        if (loadMoreBtn) loadMoreBtn.style.display = 'inline-block';
        visibleReviewCount = 6;
        initReviewsPagination();
    }
}

// ==================================================
// 9. GLOBAL FUNCTIONS: APPOINTMENT PAGE INTERACTIONS
// ==================================================
function validateNameInput(input) {
    if (input) input.value = input.value.replace(/[0-9]/g, '');
}

function selectPayment(mode, element) {
    document.querySelectorAll('.payment-option-card').forEach(card => {
        card.classList.remove('selected-payment');
    });
    if (element) {
        element.classList.add('selected-payment');
        const input = element.querySelector('input');
        if (input) input.checked = true;
    }

    const detailsBox = document.getElementById('paymentDetailsBox');
    const cardFields = document.getElementById('cardFields');
    const walletFields = document.getElementById('walletFields');

    if (detailsBox && cardFields && walletFields) {
        if (mode === 'Credit / Debit Card') {
            detailsBox.style.display = 'block';
            cardFields.style.display = 'block';
            walletFields.style.display = 'none';
        } else if (mode === 'JazzCash / EasyPaisa') {
            detailsBox.style.display = 'block';
            cardFields.style.display = 'none';
            walletFields.style.display = 'block';
        } else {
            detailsBox.style.display = 'none';
            cardFields.style.display = 'none';
            walletFields.style.display = 'none';
        }
    }
}

function handleValidatedBooking(event) {
    event.preventDefault();
    let isValid = true;

    // 1. Name Validation
    const nameField = document.getElementById('clientName');
    const nameError = document.getElementById('nameError');
    const nameRegex = /^[A-Za-z\s]{3,}$/;
    if (nameField && !nameRegex.test(nameField.value.trim())) {
        nameField.classList.add('is-invalid-input');
        if (nameError) nameError.style.display = 'block';
        isValid = false;
    } else if (nameField) {
        nameField.classList.remove('is-invalid-input');
        if (nameError) nameError.style.display = 'none';
    }

    // 2. Phone Validation
    const phoneField = document.getElementById('clientPhone');
    const phoneError = document.getElementById('phoneError');
    const phoneRegex = /^03[0-9]{9}$/;
    if (phoneField && !phoneRegex.test(phoneField.value.trim())) {
        phoneField.classList.add('is-invalid-input');
        if (phoneError) phoneError.style.display = 'block';
        isValid = false;
    } else if (phoneField) {
        phoneField.classList.remove('is-invalid-input');
        if (phoneError) phoneError.style.display = 'none';
    }

    // 3. Email Validation
    const emailField = document.getElementById('clientEmail');
    const emailError = document.getElementById('emailError');
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (emailField && !emailRegex.test(emailField.value.trim())) {
        emailField.classList.add('is-invalid-input');
        if (emailError) emailError.style.display = 'block';
        isValid = false;
    } else if (emailField) {
        emailField.classList.remove('is-invalid-input');
        if (emailError) emailError.style.display = 'none';
    }

    // 4. Service Selection Validation
    const serviceField = document.getElementById('serviceSelect');
    const serviceError = document.getElementById('serviceError');
    if (serviceField && !serviceField.value) {
        serviceField.classList.add('is-invalid-input');
        if (serviceError) serviceError.style.display = 'block';
        isValid = false;
    } else if (serviceField) {
        serviceField.classList.remove('is-invalid-input');
        if (serviceError) serviceError.style.display = 'none';
    }

    // 5. Stylist Validation
    const stylistField = document.getElementById('stylistSelect');
    const stylistError = document.getElementById('stylistError');
    if (stylistField && !stylistField.value) {
        stylistField.classList.add('is-invalid-input');
        if (stylistError) stylistError.style.display = 'block';
        isValid = false;
    } else if (stylistField) {
        stylistField.classList.remove('is-invalid-input');
        if (stylistError) stylistError.style.display = 'none';
    }

    // 6. Date Validation
    const dateField = document.getElementById('appointmentDate');
    const dateError = document.getElementById('dateError');
    const selectedDate = dateField ? new Date(dateField.value) : null;
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    if (!dateField || !dateField.value || selectedDate < today) {
        if (dateField) dateField.classList.add('is-invalid-input');
        if (dateError) dateError.style.display = 'block';
        isValid = false;
    } else {
        dateField.classList.remove('is-invalid-input');
        if (dateError) dateError.style.display = 'none';
    }

    // 7. Time Slot Validation
    const timeField = document.getElementById('timeSlot');
    const timeError = document.getElementById('timeError');
    if (timeField && !timeField.value) {
        timeField.classList.add('is-invalid-input');
        if (timeError) timeError.style.display = 'block';
        isValid = false;
    } else if (timeField) {
        timeField.classList.remove('is-invalid-input');
        if (timeError) timeError.style.display = 'none';
    }

    // 8. Payment Validation
    const activePaymentElem = document.querySelector('input[name="paymentMode"]:checked');
    const activePayment = activePaymentElem ? activePaymentElem.value : 'Cash on Arrival';
    if (activePayment === 'Credit / Debit Card') {
        const cardNum = document.getElementById('cardNumber');
        if (cardNum && cardNum.value.trim().length < 15) {
            cardNum.classList.add('is-invalid-input');
            isValid = false;
        } else if (cardNum) {
            cardNum.classList.remove('is-invalid-input');
        }
    } else if (activePayment === 'JazzCash / EasyPaisa') {
        const walletNum = document.getElementById('walletNumber');
        const walletRegex = /^03[0-9]{9}$/;
        if (walletNum && !walletRegex.test(walletNum.value.trim())) {
            walletNum.classList.add('is-invalid-input');
            isValid = false;
        } else if (walletNum) {
            walletNum.classList.remove('is-invalid-input');
        }
    }

    // Show Invoice Modal on Success
    if (isValid && typeof bootstrap !== 'undefined') {
        const serviceSelectedOption = serviceField.options[serviceField.selectedIndex];
        const serviceName = serviceSelectedOption.text.split(' - ')[0];
        const priceAttr = serviceSelectedOption.getAttribute('data-price');
        const servicePrice = priceAttr ? ('Rs. ' + parseInt(priceAttr).toLocaleString()) : 'Rs. 3,500';

        const invId = document.getElementById('invId');
        const invClient = document.getElementById('invClient');
        const invPhone = document.getElementById('invPhone');
        const invService = document.getElementById('invService');
        const invStylist = document.getElementById('invStylist');
        const invSchedule = document.getElementById('invSchedule');
        const invPayment = document.getElementById('invPayment');
        const invTotal = document.getElementById('invTotal');

        if (invId) invId.innerText = 'ELG-' + Math.floor(10000 + Math.random() * 90000);
        if (invClient) invClient.innerText = nameField.value;
        if (invPhone) invPhone.innerText = phoneField.value;
        if (invService) invService.innerText = serviceName;
        if (invStylist) invStylist.innerText = stylistField.value;
        if (invSchedule) invSchedule.innerText = `${dateField.value} (${timeField.value})`;
        if (invPayment) invPayment.innerText = activePayment;
        if (invTotal) invTotal.innerText = servicePrice;

        const modalEl = document.getElementById('invoiceModal');
        if (modalEl) {
            const invoiceModal = new bootstrap.Modal(modalEl);
            invoiceModal.show();
        }
    }
}

// ==================================================
// 17. LUXURY LIVE NUMBERS COUNT-UP ANIMATION
// ==================================================
function initLuxuryLiveCounters() {
    const counterElements = document.querySelectorAll('.counter-value');
    if (!counterElements.length) return;

    let hasAnimated = false;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !hasAnimated) {
                hasAnimated = true;
                counterElements.forEach(counter => {
                    const target = parseFloat(counter.getAttribute('data-target'));
                    const decimals = parseInt(counter.getAttribute('data-decimals') || 0);
                    const duration = 2000;
                    const startTime = performance.now();

                    function updateCount(currentTime) {
                        const elapsed = currentTime - startTime;
                        const progress = Math.min(elapsed / duration, 1);
                        const easeOutProgress = 1 - Math.pow(1 - progress, 3);
                        const currentVal = target * easeOutProgress;

                        if (decimals > 0) {
                            counter.innerText = currentVal.toFixed(decimals);
                        } else {
                            counter.innerText = Math.floor(currentVal).toLocaleString();
                        }

                        if (progress < 1) {
                            requestAnimationFrame(updateCount);
                        } else {
                            if (decimals > 0) {
                                counter.innerText = target.toFixed(decimals);
                            } else {
                                counter.innerText = target.toLocaleString();
                            }
                        }
                    }

                    requestAnimationFrame(updateCount);
                });
            }
        });
    }, { threshold: 0.25 });

    const section = document.getElementById('stats-counter-section');
    if (section) {
        observer.observe(section);
    }
}

// Initialise Counters
initLuxuryLiveCounters();

// ==================================================
// 18. INTERACTIVE REVIEWS & FEEDBACK SUBMISSION ENGINE
// ==================================================
function setStarRating(rating) {
    const hiddenInput = document.getElementById('selectedStarRating');
    if (hiddenInput) hiddenInput.value = rating;

    const stars = document.querySelectorAll('.star-rating-selector .star-item');
    stars.forEach((s, idx) => {
        if (idx < rating) {
            s.style.color = '#d4af37';
            s.style.opacity = '1';
        } else {
            s.style.color = '#555555';
            s.style.opacity = '0.5';
        }
    });
}

function handlePublicFeedbackSubmit(e) {
    e.preventDefault();

    const name = document.getElementById('fbClientName')?.value.trim();
    const location = document.getElementById('fbLocation')?.value.trim();
    const service = document.getElementById('fbService')?.value;
    const stylist = document.getElementById('fbStylist')?.value;
    const comment = document.getElementById('fbComment')?.value.trim();
    const rating = parseInt(document.getElementById('selectedStarRating')?.value || 5);

    if (!name || !location || !comment) {
        alert('Please fill in all required fields.');
        return;
    }

    // 1. Dynamic Prepend to Reviews Grid (if on overreviews.php)
    const reviewsGrid = document.getElementById('reviewsGrid');
    if (reviewsGrid) {
        const starStr = '★'.repeat(rating) + '☆'.repeat(5 - rating);
        const cardHtml = `
            <div class="col-lg-6 col-md-6 review-item" data-category="all" style="animation: fadeIn 0.6s ease;">
                <div class="screenshot-review-card" style="border: 1px solid rgba(212, 175, 55, 0.4);">
                    <div>
                        <div class="review-stars text-warning">${starStr}</div>
                        <p class="review-body-text">${comment}</p>
                    </div>
                    <div class="review-footer-info">
                        <div class="author-info">
                            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-dark bg-warning" style="width:40px;height:40px;font-size:16px;">
                                ${name.charAt(0).toUpperCase()}
                            </div>
                            <div>
                                <h5 class="author-name">${name} <span class="badge bg-success small" style="font-size:9px;">Verified Visit</span></h5>
                                <p class="author-location">${location} • ${service} (${stylist})</p>
                            </div>
                        </div>
                        <div class="quote-icon-box"><svg viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.998v10h-9.998z"/></svg></div>
                    </div>
                </div>
            </div>
        `;
        reviewsGrid.insertAdjacentHTML('afterbegin', cardHtml);
    }

    // 2. Persist to Admin Dashboard State
    try {
        const saved = localStorage.getItem('elegance_salon_state');
        let state = saved ? JSON.parse(saved) : {};
        if (!state.feedbacks) state.feedbacks = [];
        state.feedbacks.unshift({
            id: 'FB-' + Math.floor(1000 + Math.random() * 9000),
            client: name,
            service: service,
            rating: rating + '★',
            comment: comment,
            status: 'Approved',
            date: 'Today'
        });
        localStorage.setItem('elegance_salon_state', JSON.stringify(state));
    } catch(err) {}

    // 3. Reset and Alert
    document.getElementById('publicFeedbackForm')?.reset();
    setStarRating(5);

    alert(`🎉 Thank you, ${name}! Your verified review for ${service} has been submitted to Elegance Salon Karachi!`);
}
