/**
 * SHOPPE - Main Application Controller & Unified Store Engine
 * Handles Unified Data Store (Products, Orders, Inquiries, Settings),
 * Navigation, Search, Quick View Modal, Wishlist, Newsletter, and Toast alerts.
 */

// Global HTML Escape utility to prevent DOM XSS across storefront & admin
function escapeHtml(value) {
  return String(value ?? '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');
}
window.escapeHtml = escapeHtml;

// Master Product Catalog (P1 - P12) shared between Storefront and Admin
const DEFAULT_PRODUCTS_LIST = [
  {
    id: 'p1',
    title: 'Lira Earrings',
    price: 20.00,
    oldPrice: 25.00,
    image: 'assets/images/product-1.png',
    badge: '-%21',
    sku: 'SKU-0012',
    category: 'earrings',
    categoryLabel: 'Earrings',
    inStock: true,
    onSale: true,
    sales: 48,
    description: 'Distinctive minimalist hoops cast in brass with a polished 18k gold-dipped finish. Designed for lightweight, all-day comfortable wear.'
  },
  {
    id: 'p2',
    title: 'Hal Earrings',
    price: 25.00,
    oldPrice: null,
    image: 'assets/images/product-2.png',
    badge: null,
    sku: 'SKU-0014',
    category: 'earrings',
    categoryLabel: 'Earrings',
    inStock: true,
    onSale: false,
    sales: 32,
    description: 'Sculptural silhouette double loop earrings crafted with delicate curved lines and a modern architectural aesthetic.'
  },
  {
    id: 'p3',
    title: 'Kaede Hair Pin Set Of 3',
    price: 30.00,
    oldPrice: null,
    image: 'assets/images/product-3.png',
    badge: null,
    sku: 'SKU-0018',
    category: 'hairpins',
    categoryLabel: 'Hair pins',
    inStock: true,
    onSale: false,
    sales: 29,
    description: 'A curated trio of organic leaf-shaped hair pins. Subtle, elegant accents designed to gently secure updos and loose waves alike.'
  },
  {
    id: 'p4',
    title: 'Hair Pin Set of 3',
    price: 30.00,
    oldPrice: null,
    image: 'assets/images/product-4.png',
    badge: null,
    sku: 'SKU-0021',
    category: 'hairpins',
    categoryLabel: 'Hair pins',
    inStock: true,
    onSale: false,
    sales: 18,
    description: 'Minimal geometric hair accessories with a satin-matte metallic coat. Lightweight yet secure grip for effortless everyday styling.'
  },
  {
    id: 'p5',
    title: 'Plaine Necklace',
    price: 19.00,
    oldPrice: null,
    image: 'assets/images/product-5.png',
    badge: 'Sold out',
    sku: 'SKU-0025',
    category: 'necklaces',
    categoryLabel: 'Necklaces',
    inStock: false,
    onSale: false,
    sales: 64,
    description: 'A delicate chain featuring an understated pendant disc. An ideal layering base or an understated standalone piece for everyday sophistication.'
  },
  {
    id: 'p6',
    title: 'Yuki Hair Pin Set of 3',
    price: 29.00,
    oldPrice: null,
    image: 'assets/images/product-6.png',
    badge: null,
    sku: 'SKU-0029',
    category: 'hairpins',
    categoryLabel: 'Hair pins',
    inStock: true,
    onSale: false,
    sales: 12,
    description: 'Refined botanical hairpin ensemble, hand-finished with warm gold luster. Brings an artisan touch to contemporary hairstyles.'
  },
  {
    id: 'p7',
    title: 'Lira Earrings Silver',
    price: 24.00,
    oldPrice: null,
    image: 'assets/images/product-1.png',
    badge: null,
    sku: 'SKU-0031',
    category: 'earrings',
    categoryLabel: 'Earrings',
    inStock: true,
    onSale: false,
    sales: 15,
    description: 'Cool-toned sterling silver plated variation of our signature Lira hoops, offering a sleek mirror-polished finish.'
  },
  {
    id: 'p8',
    title: 'Hal Classic Earrings',
    price: 26.00,
    oldPrice: 30.00,
    image: 'assets/images/product-2.png',
    badge: '-%15',
    sku: 'SKU-0032',
    category: 'earrings',
    categoryLabel: 'Earrings',
    inStock: true,
    onSale: true,
    sales: 21,
    description: 'Timeless double-drop silhouette refined for evening occasions and effortless daytime pairing.'
  },
  {
    id: 'p9',
    title: 'Kaede Hair Pin Single',
    price: 14.00,
    oldPrice: null,
    image: 'assets/images/product-3.png',
    badge: null,
    sku: 'SKU-0033',
    category: 'hairpins',
    categoryLabel: 'Hair pins',
    inStock: true,
    onSale: false,
    sales: 9,
    description: 'Single botanical leaf hairpin crafted in brushed brass for minimalist hair styling.'
  },
  {
    id: 'p10',
    title: 'Plaine Gold Pendant',
    price: 21.00,
    oldPrice: null,
    image: 'assets/images/product-5.png',
    badge: null,
    sku: 'SKU-0034',
    category: 'necklaces',
    categoryLabel: 'Necklaces',
    inStock: true,
    onSale: false,
    sales: 27,
    description: '18k gold-dipped coin pendant suspended on an adjustable fine cable chain (40cm + 5cm extender).'
  },
  {
    id: 'p11',
    title: 'Ollie Hoop Earrings',
    price: 20.00,
    oldPrice: null,
    image: 'assets/images/product-4.png',
    badge: null,
    sku: 'SKU-0035',
    category: 'earrings',
    categoryLabel: 'Earrings',
    inStock: true,
    onSale: false,
    sales: 19,
    description: 'Modern sculpted huggie hoops with a smooth rounded profile and hypoallergenic post closure.'
  },
  {
    id: 'p12',
    title: 'Yuki Blossom Hair Pin',
    price: 32.00,
    oldPrice: null,
    image: 'assets/images/product-6.png',
    badge: null,
    sku: 'SKU-0036',
    category: 'hairpins',
    categoryLabel: 'Hair pins',
    inStock: true,
    onSale: false,
    sales: 14,
    description: 'Artisan floral hairpin set inspired by spring blossoms, finished in warm champagne gold.'
  }
];

function buildProductMap() {
  const map = {};
  DEFAULT_PRODUCTS_LIST.forEach(p => {
    map[p.id] = { ...p };
  });

  // Merge any admin updates / additions from localStorage['shoppe_admin_products_v1']
  try {
    const savedAdminProds = localStorage.getItem('shoppe_admin_products_v1');
    if (savedAdminProds) {
      const parsed = JSON.parse(savedAdminProds);
      if (Array.isArray(parsed)) {
        parsed.forEach(ap => {
          if (!ap || !ap.id) return;
          const cleanImg = String(ap.image || 'assets/images/product-1.png').replace(/^(\.\.\/)+/, '');
          const normCat = String(ap.category || '').toLowerCase().includes('neck')
            ? 'necklaces'
            : String(ap.category || '').toLowerCase().includes('hair')
            ? 'hairpins'
            : 'earrings';
          map[ap.id] = {
            ...(map[ap.id] || {}),
            id: ap.id,
            title: ap.title || 'Jewelry Item',
            price: Number(ap.price) || 20.00,
            image: cleanImg,
            sku: ap.sku || ap.id,
            category: normCat,
            categoryLabel: ap.category || 'Earrings',
            inStock: ap.inStock !== false,
            onSale: Boolean(ap.onSale || (map[ap.id] && map[ap.id].onSale)),
            badge: ap.inStock === false ? 'Sold out' : (map[ap.id]?.badge === 'Sold out' ? null : (map[ap.id]?.badge || null)),
            sales: Number(ap.sales) || 0,
            description: ap.description || map[ap.id]?.description || 'Handcrafted minimalist jewelry piece cast in premium brass with an 18k gold-dipped finish.'
          };
        });
      }
    }
  } catch (e) {
    console.warn('Could not sync admin products:', e);
  }
  return map;
}

const PRODUCT_DATA = buildProductMap();
window.PRODUCT_DATA = PRODUCT_DATA;
window.DEFAULT_PRODUCTS_LIST = DEFAULT_PRODUCTS_LIST;

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
      if (Array.isArray(saved)) return new Set(saved);
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
    this.applyStoreSettingsToDOM();
    this.syncCatalogStockState();
    this.setupHeaderScroll();
    this.setupMobileNav();
    this.setupSearch();
    this.setupProductCards();
    this.setupQuickView();
    this.setupNewsletter();
    this.setupMobileFilters();
    this.setupFooterPolicyLinks();
  }

  // Apply store name & announcement banner if configured in Admin Dashboard
  applyStoreSettingsToDOM() {
    try {
      const raw = localStorage.getItem('shoppe_store_settings_v1');
      if (!raw) return;
      const settings = JSON.parse(raw);
      if (settings.storeName) {
        document.querySelectorAll('.header-logo').forEach(el => {
          el.textContent = settings.storeName;
        });
      }
    } catch (e) {
      // ignore
    }
  }

  // Ensure product cards on index.html and shop.html reflect Admin stock & price changes
  syncCatalogStockState() {
    window.PRODUCT_DATA = buildProductMap();
    document.querySelectorAll('.product-card[data-id]').forEach(card => {
      const pid = card.dataset.id;
      const prod = window.PRODUCT_DATA[pid];
      if (!prod) return;

      card.setAttribute('data-price', prod.price);
      card.setAttribute('data-stock', prod.inStock ? 'true' : 'false');

      const titleLink = card.querySelector('.product-title a');
      if (titleLink) titleLink.textContent = prod.title;

      const priceEl = card.querySelector('.product-price');
      if (priceEl) priceEl.textContent = `$ ${prod.price.toFixed(2).replace('.', ',')}`;

      const media = card.querySelector('.product-media');
      const existingSoldOut = media?.querySelector('.badge-soldout');
      if (!prod.inStock && media && !existingSoldOut) {
        const badge = document.createElement('span');
        badge.className = 'product-badge badge-soldout';
        badge.textContent = 'Sold out';
        media.prepend(badge);
      } else if (prod.inStock && existingSoldOut) {
        existingSoldOut.remove();
      }

      const quickAddBtn = card.querySelector('.product-quick-add');
      if (quickAddBtn) {
        if (!prod.inStock) {
          quickAddBtn.textContent = 'SOLD OUT';
          quickAddBtn.disabled = true;
          quickAddBtn.style.opacity = '0.6';
          quickAddBtn.style.cursor = 'not-allowed';
        } else {
          quickAddBtn.disabled = false;
          quickAddBtn.style.opacity = '';
          quickAddBtn.style.cursor = '';
        }
      }

      // Sync wishlist active heart
      const wishBtn = card.querySelector('[data-action="wishlist"]');
      if (wishBtn) {
        wishBtn.classList.toggle('active', this.wishlist.has(pid));
      }
    });
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

  // 3. Search Bar Dropdown (Supports #searchInput & #siteSearchInput + #searchClose)
  setupSearch() {
    const searchToggle = document.getElementById('searchToggle');
    const searchForm = document.getElementById('searchForm') || this.searchOverlay?.querySelector('form.search-form');
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

    searchForm?.addEventListener('submit', (e) => {
      e.preventDefault();
      const query = searchInput?.value.trim();
      if (!query) return;

      this.searchOverlay?.classList.remove('open');

      // If already on shop.html, filter live in-place
      const shopSearchInput = document.getElementById('shopFilterSearch');
      if (shopSearchInput) {
        shopSearchInput.value = query;
        shopSearchInput.dispatchEvent(new Event('input', { bubbles: true }));
        this.showToast(`Showing results for "${query}"`);
        return;
      }

      // Otherwise navigate to shop.html?search=...
      const isSub = window.location.pathname.includes('/pages/');
      const targetUrl = isSub
        ? `shop.html?search=${encodeURIComponent(query)}`
        : `pages/shop.html?search=${encodeURIComponent(query)}`;
      window.location.href = targetUrl;
    });

    // Close search on clicking outside
    document.addEventListener('click', (e) => {
      if (this.searchOverlay?.classList.contains('open') &&
          !this.searchOverlay.contains(e.target) &&
          !searchToggle?.contains(e.target)) {
        this.searchOverlay.classList.remove('open');
      }
    });
  }

  // 4. Product Card Actions (Event Delegation so dynamically loaded products work!)
  setupProductCards() {
    document.addEventListener('click', (e) => {
      // Quick Add Button
      const quickAddBtn = e.target.closest('[data-action="quick-add"]');
      if (quickAddBtn) {
        e.preventDefault();
        const card = quickAddBtn.closest('.product-card');
        const productId = card?.dataset.id;
        const product = window.PRODUCT_DATA[productId];
        if (!product) return;

        if (product.inStock === false || card?.dataset.stock === 'false') {
          this.showToast(`Sorry, "${product.title}" is currently out of stock.`);
          return;
        }

        if (window.cartManager) {
          window.cartManager.addItem({
            id: product.id,
            title: product.title,
            price: product.price,
            variant: 'Gold / Standard',
            image: product.image,
            quantity: 1
          });
        }
        return;
      }

      // Wishlist Heart Button
      const wishBtn = e.target.closest('[data-action="wishlist"]');
      if (wishBtn) {
        e.preventDefault();
        const card = wishBtn.closest('.product-card');
        const productId = card?.dataset.id;
        const product = window.PRODUCT_DATA[productId];
        if (!product) return;

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
        const card = qvBtn.closest('.product-card');
        const productId = card?.dataset.id;
        this.openQuickView(productId);
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
      const productId = this.quickViewModal?.dataset.currentProductId;
      const product = window.PRODUCT_DATA[productId];
      if (!product) return;

      if (product.inStock === false) {
        this.showToast(`Sorry, "${product.title}" is currently out of stock.`);
        return;
      }

      if (window.cartManager) {
        window.cartManager.addItem({
          id: product.id,
          title: product.title,
          price: product.price,
          variant: 'Standard / 18k Gold',
          image: product.image,
          quantity: 1
        });
        this.closeQuickView();
      }
    });
  }

  openQuickView(productId) {
    const product = window.PRODUCT_DATA[productId];
    if (!product || !this.quickViewModal) return;

    this.quickViewModal.dataset.currentProductId = productId;
    const isSubfolder = window.location.pathname.includes('/pages/');

    const imgEl = document.getElementById('modalProductImg');
    if (imgEl) {
      const cleanImg = product.image.replace(/^(\.\.\/)+/, '').replace(/^assets\//, '');
      imgEl.src = isSubfolder ? `../assets/${cleanImg}` : `assets/${cleanImg}`;
      imgEl.alt = product.title;
    }

    const productUrl = isSubfolder ? `product.html?id=${encodeURIComponent(productId)}` : `pages/product.html?id=${encodeURIComponent(productId)}`;

    const titleEl = document.getElementById('modalProductTitle');
    if (titleEl) {
      titleEl.innerHTML = `<a href="${productUrl}" style="color: inherit; text-decoration: none;">${escapeHtml(product.title)}</a>`;
    }

    const priceEl = document.getElementById('modalProductPrice');
    if (priceEl) priceEl.textContent = window.cartManager ? window.cartManager.formatPrice(product.price) : `$ ${product.price.toFixed(2)}`;

    const descEl = document.getElementById('modalProductDesc');
    if (descEl) descEl.textContent = product.description;

    const detailLink = document.getElementById('modalViewDetailLink');
    if (detailLink) {
      detailLink.href = productUrl;
    }

    const modalAddBtn = document.getElementById('modalAddToCartBtn');
    if (modalAddBtn) {
      if (product.inStock === false) {
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
        this.showToast('Thank you for subscribing to Shoppe newsletter!');
        if (input) input.value = '';
      } else {
        this.showToast('Please enter a valid email address.');
      }
    });
  }

  // 7. Toast Notification System (Safe from XSS)
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

  // 8. Mobile Category Pills & Search Filter
  setupMobileFilters() {
    const pills = document.querySelectorAll('.category-pill');
    const mobileSearchInput = document.getElementById('mobileSearchInput');
    const mobileSearchForm = document.getElementById('mobileSearchForm');
    const grid = document.getElementById('featuredProductGrid');
    const cards = grid ? grid.querySelectorAll('.product-card') : [];

    if (!pills.length && !mobileSearchInput) return;

    let activeCategory = 'all';
    let searchQuery = '';

    const applyFilters = () => {
      cards.forEach(card => {
        const category = card.dataset.category || '';
        const titleEl = card.querySelector('.product-title');
        const title = titleEl ? titleEl.textContent.toLowerCase() : '';

        const matchesCategory = (activeCategory === 'all') || (category === activeCategory);
        const matchesSearch = !searchQuery || title.includes(searchQuery);

        if (matchesCategory && matchesSearch) {
          card.style.display = 'flex';
          card.style.animation = 'fadeIn 0.3s ease';
        } else {
          card.style.display = 'none';
        }
      });
    };

    pills.forEach(pill => {
      pill.addEventListener('click', () => {
        pills.forEach(p => {
          p.classList.remove('active');
          p.setAttribute('aria-selected', 'false');
        });
        pill.classList.add('active');
        pill.setAttribute('aria-selected', 'true');
        activeCategory = pill.dataset.category || 'all';
        applyFilters();
      });
    });

    mobileSearchInput?.addEventListener('input', (e) => {
      searchQuery = e.target.value.trim().toLowerCase();
      applyFilters();
    });

    mobileSearchForm?.addEventListener('submit', (e) => {
      e.preventDefault();
    });
  }

  // 9. Redirect #terms & #shipping links smoothly to privacy-policy.html
  setupFooterPolicyLinks() {
    const isSub = window.location.pathname.includes('/pages/');
    const policyBase = isSub ? 'privacy-policy.html' : 'pages/privacy-policy.html';
    document.querySelectorAll('a[href="#terms"], a[href="#shipping"]').forEach(a => {
      const hash = a.getAttribute('href');
      a.setAttribute('href', `${policyBase}${hash}`);
    });
  }
}

document.addEventListener('DOMContentLoaded', () => {
  const controller = new AppController();
  window.ShoppeApp = controller;
  window.appController = controller; // Alias so all existing page scripts work seamlessly

  // Seamlessly ensure LiveChat is initialized across all pages
  if (typeof LiveChatManager === 'undefined') {
    const isSub = window.location.pathname.includes('/pages/');
    const script = document.createElement('script');
    script.src = isSub ? '../assets/js/livechat.js' : 'assets/js/livechat.js';
    script.defer = true;
    document.head.appendChild(script);
  } else if (!window.ShoppeLiveChat) {
    window.ShoppeLiveChat = new LiveChatManager();
  }
});
