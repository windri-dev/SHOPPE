/**
 * PUTRI.J - Main Storefront Application Controller
 * Handles Navigation, Search Overlay, Product Card Actions (Quick Add, Wishlist, Quick View),
 * Newsletter Subscription, and Accessible Toast Notifications.
 */

function escapeHtml(value) {
  return String(value ?? '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');
}
window.escapeHtml = escapeHtml;

class AppController {
  constructor() {
    this.toastContainer = this.ensureToastContainer();
    this.quickViewModal = document.getElementById('quickViewModal');
    this.mobileNav = document.getElementById('mobileNavDrawer');
    this.mobileNavBackdrop = document.getElementById('mobileNavBackdrop');
    this.searchOverlay = document.getElementById('searchOverlay');
    this.siteHeader = document.querySelector('.site-header');

    this.wishlist = this.loadWishlist();
    this.init();
  }

  isUserAuthenticated() {
    if (window.ShoppeUser && typeof window.ShoppeUser.isAuthenticated === 'boolean') {
      return window.ShoppeUser.isAuthenticated;
    }
    const meta = document.querySelector('meta[name="user-auth"]');
    return meta ? meta.getAttribute('content') === '1' : false;
  }

  getLoginUrl() {
    return window.ShoppeUser?.loginUrl || document.querySelector('meta[name="login-url"]')?.getAttribute('content') || '/login';
  }

  ensureToastContainer() {
    let container = document.getElementById('toastContainer');
    if (!container && document.body) {
      container = document.createElement('div');
      container.id = 'toastContainer';
      container.className = 'toast-container';
      container.setAttribute('aria-live', 'polite');
      document.body.appendChild(container);
    }
    return container;
  }

  loadWishlist() {
    try {
      const saved = JSON.parse(localStorage.getItem('shoppe_wishlist_v1'));
      if (Array.isArray(saved)) return new Set(saved.map(String));
    } catch (e) {
      // ignore
    }
    return new Set();
  }

  saveWishlist() {
    try {
      localStorage.setItem('shoppe_wishlist_v1', JSON.stringify([...this.wishlist]));
    } catch (e) {
      // ignore
    }
  }

  init() {
    this.syncWishlistButtons();
    this.setupHeaderScroll();
    this.setupMobileNav();
    this.setupSearch();
    this.setupProductCards();
    this.setupQuickView();
    this.setupNewsletter();
  }

  syncWishlistButtons() {
    document.querySelectorAll('[data-action="wishlist"]').forEach(btn => {
      const card = btn.closest('.product-card');
      const pid = String(btn.dataset.productId || card?.dataset.productId || card?.dataset.id || '');
      if (pid) {
        btn.classList.toggle('active', this.wishlist.has(pid));
      }
    });
  }

  extractProductData(element) {
    const card = element.closest('.product-card');
    const id = element.dataset.productId || card?.dataset.productId || card?.dataset.id;
    const title = element.dataset.productTitle || card?.dataset.title || card?.querySelector('.product-title')?.textContent?.trim() || 'Apparel Piece';
    const price = parseFloat(element.dataset.productPrice || card?.dataset.price || '35') || 35;
    const image = element.dataset.productImg || card?.dataset.image || card?.querySelector('img')?.src || '/images/product-1.png';
    const url = card?.dataset.url || card?.querySelector('.product-title a')?.href || '/shop';
    const description = card?.dataset.description || 'Busana kontemporer rancangan eksklusif PUTRI.J berbahan premium lembut dan berkarakter anggun.';
    const inStock = (card?.dataset.stock ?? 'true') !== 'false';
    const quantity = Math.max(1, parseInt(element.dataset.productQty || '1', 10) || 1);
    const video = card?.dataset.video || '';
    const videoType = card?.dataset.videoType || 'none';
    const videoEmbed = card?.dataset.videoEmbed || '';

    return { id, title, price, image, url, description, inStock, quantity, video, videoType, videoEmbed };
  }

  // 1. Header scroll effect
  setupHeaderScroll() {
    window.addEventListener('scroll', () => {
      if (window.scrollY > 20) {
        this.siteHeader?.classList.add('scrolled');
      } else {
        this.siteHeader?.classList.remove('scrolled');
      }
    }, { passive: true });
  }

  // 2. Mobile Nav Drawer
  setupMobileNav() {
    const hamburger = document.getElementById('hamburgerBtn');
    const closeBtns = document.querySelectorAll('#closeMobileNav, #mobileNavClose, .mobile-nav-drawer .drawer-close-btn');

    hamburger?.addEventListener('click', () => {
      this.mobileNav?.classList.add('open');
      this.mobileNavBackdrop?.classList.add('open');
      hamburger.setAttribute('aria-expanded', 'true');
      document.body.style.overflow = 'hidden';
    });

    const closeNav = () => {
      this.mobileNav?.classList.remove('open');
      this.mobileNavBackdrop?.classList.remove('open');
      hamburger?.setAttribute('aria-expanded', 'false');
      document.body.style.overflow = '';
    };

    closeBtns.forEach(btn => btn.addEventListener('click', closeNav));
    this.mobileNavBackdrop?.addEventListener('click', closeNav);

    document.querySelectorAll('.mobile-nav-link').forEach(link => {
      link.addEventListener('click', closeNav);
    });
  }

  // 3. Search Bar Dropdown Overlay (submits directly to Laravel route('shop.index'))
  setupSearch() {
    const searchToggle = document.getElementById('searchToggle');
    const searchInput = document.getElementById('searchInput') || document.getElementById('siteSearchInput');
    const searchClose = document.getElementById('searchClose');

    searchToggle?.addEventListener('click', (e) => {
      e.preventDefault();
      this.searchOverlay?.classList.toggle('open');
      if (this.searchOverlay?.classList.contains('open')) {
        searchInput?.focus();
      }
    });

    searchClose?.addEventListener('click', (e) => {
      e.preventDefault();
      this.searchOverlay?.classList.remove('open');
    });

    document.addEventListener('click', (e) => {
      if (
        this.searchOverlay?.classList.contains('open') &&
        !this.searchOverlay.contains(e.target) &&
        !searchToggle?.contains(e.target)
      ) {
        this.searchOverlay.classList.remove('open');
      }
    });
  }

  // 4. Product Card & Detail Actions
  setupProductCards() {
    document.addEventListener('click', (e) => {
      // Quick Add Button
      const quickAddBtn = e.target.closest('[data-action="quick-add"]');
      if (quickAddBtn) {
        e.preventDefault();

        if (!this.isUserAuthenticated()) {
          this.showToast('Silakan masuk (login) atau mendaftar terlebih dahulu untuk menambah produk!');
          setTimeout(() => {
            window.location.href = this.getLoginUrl();
          }, 900);
          return;
        }

        const product = this.extractProductData(quickAddBtn);
        if (!product.id) return;

        if (!product.inStock) {
          this.showToast(`Sorry, "${product.title}" is currently out of stock.`);
          return;
        }

        if (window.cartManager) {
          window.cartManager.addItem({
            id: product.id,
            title: product.title,
            price: product.price,
            variant: '18k Gold / Standard',
            image: product.image,
            quantity: product.quantity
          });
        }
        return;
      }

      // Wishlist Heart Button
      const wishBtn = e.target.closest('[data-action="wishlist"]');
      if (wishBtn) {
        e.preventDefault();
        const product = this.extractProductData(wishBtn);
        const productId = String(product.id || '');
        if (!productId) return;

        if (this.wishlist.has(productId)) {
          this.wishlist.delete(productId);
          wishBtn.classList.remove('active');
          this.saveWishlist();
          this.showToast(`Removed "${product.title}" from wishlist.`);
        } else {
          this.wishlist.add(productId);
          wishBtn.classList.add('active');
          this.saveWishlist();
          this.showToast(`Added "${product.title}" to wishlist!`);
        }
        return;
      }

      // Quick View Eye Button
      const qvBtn = e.target.closest('[data-action="quick-view"]');
      if (qvBtn) {
        e.preventDefault();
        const product = this.extractProductData(qvBtn);
        this.openQuickView(product);
      }
    });
  }

  // 5. Quick View Modal
  setupQuickView() {
    const closeBtn = document.getElementById('closeQuickView');
    const modalBackdrop = this.quickViewModal;

    closeBtn?.addEventListener('click', () => this.closeQuickView());
    modalBackdrop?.addEventListener('click', (e) => {
      if (e.target === modalBackdrop) {
        this.closeQuickView();
      }
    });

    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && this.quickViewModal?.classList.contains('open')) {
        this.closeQuickView();
      }
    });

    const modalAddBtn = document.getElementById('modalAddToCartBtn');
    modalAddBtn?.addEventListener('click', () => {
      if (!this.currentQuickViewProduct) return;
      const product = this.currentQuickViewProduct;

      if (!this.isUserAuthenticated()) {
        this.showToast('Silakan masuk (login) atau mendaftar terlebih dahulu untuk menambah produk!');
        setTimeout(() => {
          window.location.href = this.getLoginUrl();
        }, 900);
        return;
      }

      if (!product.inStock) {
        this.showToast(`Sorry, "${product.title}" is currently out of stock.`);
        return;
      }

      if (window.cartManager) {
        window.cartManager.addItem({
          id: product.id,
          title: product.title,
          price: product.price,
          variant: 'Size M / Standard',
          image: product.image,
          quantity: 1
        });
        this.closeQuickView();
      }
    });

    // Video Toggle in Quick View
    const videoToggleBtn = document.getElementById('modalVideoToggleBtn');
    videoToggleBtn?.addEventListener('click', () => {
      this.toggleQuickViewVideo();
    });
  }

  toggleQuickViewVideo() {
    const videoBox = document.getElementById('modalProductVideoBox');
    const imgEl = document.getElementById('modalProductImg');
    const label = document.getElementById('modalVideoToggleLabel');
    const directVideo = document.getElementById('modalProductVideo');
    const iframeEl = document.getElementById('modalProductIframe');
    const product = this.currentQuickViewProduct;

    if (!videoBox || !product?.video) return;

    const isVideoVisible = videoBox.style.display !== 'none';
    if (isVideoVisible) {
      // Switch back to Image
      videoBox.style.display = 'none';
      if (imgEl) imgEl.style.display = 'block';
      if (label) label.textContent = 'Lihat Video';
      if (directVideo && !directVideo.paused) directVideo.pause();
    } else {
      // Switch to Video
      if (imgEl) imgEl.style.display = 'none';
      videoBox.style.display = 'flex';
      if (label) label.textContent = 'Lihat Foto';

      if (product.videoType === 'youtube' || product.videoType === 'vimeo') {
        if (directVideo) directVideo.style.display = 'none';
        if (iframeEl) {
          iframeEl.style.display = 'block';
          if (!iframeEl.src || iframeEl.src === 'about:blank') {
            iframeEl.src = product.videoEmbed;
          }
        }
      } else {
        if (iframeEl) iframeEl.style.display = 'none';
        if (directVideo) {
          directVideo.style.display = 'block';
          if (directVideo.src !== product.video) {
            directVideo.src = product.video;
          }
          directVideo.play().catch(() => {});
        }
      }
    }
  }

  openQuickView(product) {
    if (!product || !this.quickViewModal) return;
    this.currentQuickViewProduct = product;

    const imgEl = document.getElementById('modalProductImg');
    if (imgEl) {
      imgEl.src = product.image;
      imgEl.alt = product.title;
      imgEl.style.display = 'block';
    }

    const videoBox = document.getElementById('modalProductVideoBox');
    const videoToggleBtn = document.getElementById('modalVideoToggleBtn');
    const label = document.getElementById('modalVideoToggleLabel');
    const directVideo = document.getElementById('modalProductVideo');
    const iframeEl = document.getElementById('modalProductIframe');

    if (videoBox) videoBox.style.display = 'none';
    if (directVideo) {
      if (!directVideo.paused) directVideo.pause();
      directVideo.removeAttribute('src');
      directVideo.load();
    }
    if (iframeEl) iframeEl.src = 'about:blank';

    if (videoToggleBtn) {
      if (product.video) {
        videoToggleBtn.style.display = 'inline-flex';
        if (label) label.textContent = 'Lihat Video';
      } else {
        videoToggleBtn.style.display = 'none';
      }
    }

    const titleEl = document.getElementById('modalProductTitle');
    if (titleEl) {
      titleEl.innerHTML = `<a href="${escapeHtml(product.url)}" style="color: inherit; text-decoration: none;">${escapeHtml(product.title)}</a>`;
    }

    const priceEl = document.getElementById('modalProductPrice');
    if (priceEl) {
      priceEl.textContent = window.cartManager
        ? window.cartManager.formatPrice(product.price)
        : `$ ${Number(product.price).toFixed(2)}`;
    }

    const descEl = document.getElementById('modalProductDesc');
    if (descEl) descEl.textContent = product.description;

    const detailLink = document.getElementById('modalViewDetailLink');
    if (detailLink) {
      detailLink.href = product.url;
    }

    const modalAddBtn = document.getElementById('modalAddToCartBtn');
    if (modalAddBtn) {
      if (!product.inStock) {
        modalAddBtn.textContent = 'Sold Out';
        modalAddBtn.disabled = true;
      } else {
        modalAddBtn.textContent = 'Add To Cart';
        modalAddBtn.disabled = false;
      }
    }

    this.quickViewModal.classList.add('open');
    document.body.style.overflow = 'hidden';
  }

  closeQuickView() {
    const videoBox = document.getElementById('modalProductVideoBox');
    const directVideo = document.getElementById('modalProductVideo');
    const iframeEl = document.getElementById('modalProductIframe');

    if (videoBox) videoBox.style.display = 'none';
    if (directVideo && !directVideo.paused) {
      directVideo.pause();
      directVideo.removeAttribute('src');
    }
    if (iframeEl) iframeEl.src = 'about:blank';

    this.quickViewModal?.classList.remove('open');
    document.body.style.overflow = '';
  }

  // 6. Newsletter Subscription
  setupNewsletter() {
    const form = document.getElementById('newsletterForm');
    form?.addEventListener('submit', (e) => {
      e.preventDefault();
      const input = document.getElementById('newsletterEmail');
      const email = input?.value.trim();
      if (email && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        this.showToast('Thank you for subscribing to PUTRI.J newsletter!');
        if (input) input.value = '';
      } else {
        this.showToast('Please enter a valid email address.');
      }
    });
  }

  // 7. Toast Notification System
  showToast(message, duration = 3000) {
    if (!this.toastContainer) {
      this.toastContainer = this.ensureToastContainer();
    }
    if (!this.toastContainer) return;

    const toast = document.createElement('div');
    toast.className = 'toast';
    toast.innerHTML = `
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--color-accent)" stroke-width="2">
        <circle cx="12" cy="12" r="10"></circle>
        <path d="M9 12l2 2 4-4"></path>
      </svg>
      <span>${escapeHtml(message)}</span>
    `;

    this.toastContainer.appendChild(toast);

    setTimeout(() => {
      toast.classList.add('toast-out');
      toast.addEventListener('animationend', () => {
        toast.remove();
      });
    }, duration);
  }
}

function initApp() {
  if (!window.ShoppeApp) {
    const controller = new AppController();
    window.ShoppeApp = controller;
    window.appController = controller;
  }
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initApp);
} else {
  initApp();
}
