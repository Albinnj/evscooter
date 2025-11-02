'use strict';



/**
 * #PRELOADING
 */

const loadElement = document.querySelector("[data-preloader]");

window.addEventListener("load", function () {
  loadElement.classList.add("loaded");
});



/**
 * #MOBILE NAVBAR TOGGLE
 */

const navbar = document.querySelector("[data-navbar]");
const navToggler = document.querySelector("[data-nav-toggler]");

const toggleNavbar = function () {
  navbar.classList.toggle("active");
  navToggler.classList.toggle("active");
}

navToggler.addEventListener("click", toggleNavbar);



/**
 *  #HEADER
 */

const header = document.querySelector("[data-header]");
const backTopBtn = document.querySelector("[data-go-top-btn]");

window.addEventListener("scroll", function () {
  if (window.scrollY >= 100) {
    header.classList.add("active");
    backTopBtn.classList.add("active");
  } else {
    header.classList.remove("active");
    backTopBtn.classList.remove("active");
  }
});



/**
 * #SCROLL REVEAL
 */

const revealElements = document.querySelectorAll("[data-reveal]");

const scrollReveal = function () {
  for (let i = 0, x = revealElements.length; i < x; i++) {
    if (revealElements[i].getBoundingClientRect().top < window.innerHeight / 1.2) {
      revealElements[i].classList.add("revealed");
    } else {
      revealElements[i].classList.remove("revealed");
    }
  }
}

window.addEventListener("scroll", scrollReveal);
window.addEventListener("load", scrollReveal);

/**
 * #DYNAMIC PRODUCTS
 */

const productListEl = document.getElementById('product-list');

async function loadProducts() {
  if (!productListEl) return;
  try {
    const res = await fetch('./data/products.json', { cache: 'no-store' });
    if (!res.ok) throw new Error('Failed to load products');
    const products = await res.json();
    productListEl.innerHTML = products.map(p => `
      <li>
        <div class="product-card">
          <figure class="card-banner img-holder" style="--width: 400; --height: 300;">
            <img src="${p.image}" width="400" height="300" loading="lazy" alt="${p.name}" class="img-cover">
          </figure>
          <div class="card-content">
            <h3 class="h5 card-title">${p.name}</h3>
            <p class="card-text">${p.description}</p>
            <a href="#contact" class="btn btn-primary">
              <span class="span">Book Now</span>
              <ion-icon name="arrow-forward-outline" aria-hidden="true"></ion-icon>
            </a>
          </div>
        </div>
      </li>
    `).join('');
  } catch (err) {
    console.error(err);
  }
}

window.addEventListener('load', loadProducts);

/**
 * #SITE SETTINGS (Logo & Name)
 */

async function applySettings() {
  try {
    const res = await fetch('./data/settings.json', { cache: 'no-store' });
    if (!res.ok) return;
    const s = await res.json();
    const siteLogo = document.getElementById('site-logo');
    const footerLogo = document.getElementById('footer-logo');
    const setImgWithFallback = (imgEl, src, fallback, altText) => {
      if (!imgEl) return;
      const testImg = new Image();
      testImg.onload = () => {
        imgEl.src = src;
        if (altText) imgEl.alt = altText;
      };
      testImg.onerror = () => {
        imgEl.src = fallback;
        if (altText) imgEl.alt = altText;
      };
      testImg.src = src;
    };

    // Apply header & footer logos with graceful fallback to existing SVGs
    if (siteLogo) {
      setImgWithFallback(siteLogo, s.logo || siteLogo.src, './assets/images/logo.svg', s.site_name);
    }
    if (footerLogo) {
      setImgWithFallback(footerLogo, s.footer_logo || footerLogo.src, './assets/images/footer-logo.svg', s.site_name);
    }
    // Optional: update document title with site name
    if (s.site_name) {
      document.title = s.site_name + ' • Volti';
    }
  } catch (e) {
    console.error('settings load failed', e);
  }
}

window.addEventListener('load', applySettings);