/**
 * PUTRIJALVA - Cart State Management, Drawer, Cart Page & Checkout Summary Controller
 * Synchronizes shopping bag items across Drawer, /cart page, and /checkout form.
 */

function escCartHtml(val) {
  return String(val ?? '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');
}

class CartManager {
  constructor() {
    this.storageKey = 'shoppe_cart_items';
    this.items = this.loadCart();
    this.drawer = document.getElementById('cartDrawer');
    this.backdrop = document.getElementById('drawerBackdrop');
    this.cartBody = document.getElementById('cartDrawerBody');
    this.subtotalEl = document.getElementById('cartSubtotal');
    this.badgeEl = document.getElementById('cartBadgeCount');
    this.itemCountEl = document.getElementById('drawerItemCount');
    this.subtotalLabelEl = document.getElementById('subtotalLabel');
    this.currentShippingCost = 0;

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

  normalizeProductId(rawId) {
    if (typeof rawId === 'number' && Number.isInteger(rawId)) return rawId;
    const str = String(rawId || '').trim();
    const match = str.match(/^p?(\d+)$/i);
    return match ? parseInt(match[1], 10) : 1;
  }

  resolveAssetPath(src) {
    if (!src) return '/images/product-1.png';
    const str = String(src).trim();
    if (/^(https?:)?\/\//i.test(str) || str.startsWith('/')) return escCartHtml(str);
    const cleaned = str.replace(/^(\.\.\/)+/, '').replace(/^assets\//, '');
    return escCartHtml('/' + cleaned.replace(/^\/+/, ''));
  }

  loadCart() {
    try {
      const saved = localStorage.getItem(this.storageKey);
      if (saved) {
        const parsed = JSON.parse(saved);
        if (Array.isArray(parsed)) {
          return parsed.map(item => ({
            ...item,
            id: this.normalizeProductId(item.id),
            price: parseFloat(item.price) || 20.00,
            quantity: Math.max(1, parseInt(item.quantity, 10) || 1),
          }));
        }
      }
    } catch (e) {
      console.error('Error loading cart:', e);
    }

    return [];
  }

  saveCart() {
    try {
      localStorage.setItem(this.storageKey, JSON.stringify(this.items));
    } catch (e) {
      console.error('Error saving cart:', e);
    }
    this.render();
  }

  clearCart() {
    this.items = [];
    this.saveCart();
  }

  init() {
    document.querySelectorAll('[data-action="open-cart"], .cart-btn').forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.preventDefault();
        this.openDrawer();
      });
    });

    document.querySelectorAll('[data-action="close-cart"]').forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.preventDefault();
        this.closeDrawer();
      });
    });

    if (this.backdrop) {
      this.backdrop.addEventListener('click', () => this.closeDrawer());
    }

    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && this.isDrawerOpen()) {
        this.closeDrawer();
      }
    });

    this.render();
  }

  isDrawerOpen() {
    return this.drawer && this.drawer.classList.contains('open');
  }

  openDrawer() {
    if (this.drawer) {
      this.drawer.classList.add('open');
      this.backdrop?.classList.add('open');
      document.body.style.overflow = 'hidden';
    }
  }

  closeDrawer() {
    if (this.drawer) {
      this.drawer.classList.remove('open');
      this.backdrop?.classList.remove('open');
      document.body.style.overflow = '';
    }
  }

  addItem(product, openDrawer = true) {
    if (!product) return;

    const normalizedId = this.normalizeProductId(product.id);
    const size = product.size || 'M';
    const variant = product.variant || `Size ${size}`;
    const qtyToAdd = Math.max(1, parseInt(product.quantity, 10) || 1);

    const existingIndex = this.items.findIndex(
      item => this.normalizeProductId(item.id) === normalizedId && (item.size || 'M') === size
    );

    if (existingIndex > -1) {
      this.items[existingIndex].quantity += qtyToAdd;
    } else {
      this.items.push({
        id: normalizedId,
        title: product.title || 'Apparel Item',
        price: parseFloat(product.price) || 20.00,
        size: size,
        variant: variant,
        image: product.image || '/images/product-1.png',
        quantity: qtyToAdd
      });
    }

    this.saveCart();
    this.triggerBadgeBump();
    if (openDrawer) {
      this.openDrawer();
      (window.ShoppeApp || window.appController)?.showToast(`Added "${product.title}" to your shopping bag!`);
    }
  }

  updateQuantity(index, delta) {
    if (!this.items[index]) return;
    this.items[index].quantity += delta;
    if (this.items[index].quantity <= 0) {
      this.removeItem(index);
      return;
    }
    this.saveCart();
  }

  removeItem(index) {
    if (!this.items[index]) return;
    const item = this.items[index];
    this.items.splice(index, 1);
    this.saveCart();
    (window.ShoppeApp || window.appController)?.showToast(`Removed "${item.title}" from bag.`);
  }

  triggerBadgeBump() {
    if (this.badgeEl) {
      this.badgeEl.classList.remove('bump');
      void this.badgeEl.offsetWidth;
      this.badgeEl.classList.add('bump');
    }
  }

  getTotalCount() {
    return this.items.reduce((total, item) => total + (Number(item.quantity) || 0), 0);
  }

  getSubtotal() {
    return this.items.reduce((total, item) => total + ((Number(item.price) || 0) * (Number(item.quantity) || 1)), 0);
  }

  formatPrice(num) {
    const val = Number(num || 0);
    const curr = window.ShoppeCurrency || { locale: 'id', symbol: 'Rp', rate: 16000 };
    if (curr.locale === 'id') {
      const idr = Math.round((val * (Number(curr.rate) || 16000)) / 1000) * 1000;
      return 'Rp ' + idr.toLocaleString('id-ID');
    }
    return '$ ' + val.toFixed(2);
  }

  render() {
    const count = this.getTotalCount();
    const subtotal = this.getSubtotal();

    if (this.badgeEl) {
      this.badgeEl.textContent = count;
      this.badgeEl.style.display = count > 0 ? 'flex' : 'none';
    }

    if (this.itemCountEl) {
      this.itemCountEl.textContent = `${count} ${count === 1 ? 'item' : 'items'}`;
    }

    if (this.subtotalLabelEl) {
      this.subtotalLabelEl.textContent = `Subtotal (${count} ${count === 1 ? 'item' : 'items'})`;
    }

    if (this.subtotalEl) {
      this.subtotalEl.textContent = this.formatPrice(subtotal);
    }

    this.renderDrawerItems();
    this.renderCartPage(subtotal);
    this.renderCheckoutSummary(subtotal);
  }

  renderDrawerItems() {
    if (!this.cartBody) return;

    const t = window.__trans || {};

    if (this.items.length === 0) {
      this.cartBody.innerHTML = `
        <div class="cart-empty-state">
          <svg class="cart-empty-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
          </svg>
          <p>${t.emptyCartDrawer || 'Your shopping bag is currently empty.'}</p>
          <a href="/shop" class="btn btn-outline btn-sm">${t.exploreShop || 'Explore Shop'}</a>
        </div>
      `;
      return;
    }

    const defaultImg = '/images/product-1.png';
    this.cartBody.innerHTML = this.items.map((item, idx) => {
      const safeTitle = escCartHtml(item.title);
      const safeVariant = escCartHtml(item.variant || 'Standard');
      const safeId = escCartHtml(item.id);
      return `
        <div class="cart-item" data-id="${safeId}">
          <img src="${this.resolveAssetPath(item.image)}" alt="${safeTitle}" class="cart-item-img" onerror="this.onerror=null;this.src='${defaultImg}';">
          <div class="cart-item-details">
            <div class="cart-item-header">
              <h4 class="cart-item-title">${safeTitle}</h4>
              <button type="button" class="cart-item-remove" onclick="window.cartManager.removeItem(${idx})" aria-label="Remove ${safeTitle}">&times;</button>
            </div>
            <span class="cart-item-variant">${safeVariant}</span>
            <span class="cart-item-price">${this.formatPrice(item.price)}</span>
            <div class="cart-item-qty-row">
              <span class="cart-item-qty-label">${t.qty || 'QTY'}:</span>
              <button type="button" class="cart-item-qty-btn" onclick="window.cartManager.updateQuantity(${idx}, -1)" aria-label="Decrease quantity">&minus;</button>
              <span class="cart-item-qty-val">${Number(item.quantity) || 1}</span>
              <button type="button" class="cart-item-qty-btn" onclick="window.cartManager.updateQuantity(${idx}, 1)" aria-label="Increase quantity">&#43;</button>
            </div>
          </div>
        </div>
      `;
    }).join('');
  }

  renderCartPage(subtotal) {
    const tableContainer = document.getElementById('cartTableContainer');
    const pageSubtotal = document.getElementById('cartPageSubtotal');
    const pageTotal = document.getElementById('cartPageTotal');
    const t = window.__trans || {};

    if (pageSubtotal) pageSubtotal.textContent = this.formatPrice(subtotal);
    if (pageTotal) pageTotal.textContent = this.formatPrice(subtotal);

    if (!tableContainer) return;

    if (this.items.length === 0) {
      tableContainer.innerHTML = `
        <div style="text-align: center; padding: 60px 24px; background: #FAF9F6; border: 1px dashed #D8D8D8; border-radius: 6px;">
          <p style="color: #707070; font-size: 16px; margin-bottom: 20px;">${t.emptyCartPage || 'Keranjang belanja Anda saat ini kosong.'}</p>
          <a href="/shop" class="btn btn-primary">${t.startShopping || 'Mulai Belanja'}</a>
        </div>
      `;
      return;
    }

    const defaultImg = '/images/product-1.png';
    tableContainer.innerHTML = this.items.map((item, idx) => {
      const safeTitle = escCartHtml(item.title);
      const safeVariant = escCartHtml(item.variant || 'Standard');
      return `
        <div class="cart-page-item">
          <div class="cart-page-img-wrap">
            <img src="${this.resolveAssetPath(item.image)}" alt="${safeTitle}" class="cart-page-img" onerror="this.onerror=null;this.src='${defaultImg}';">
          </div>
          <div class="cart-page-item-info">
            <h3 class="cart-page-item-title">${safeTitle}</h3>
            <p class="cart-page-item-variant">${safeVariant}</p>
            <span class="cart-page-item-price">${this.formatPrice(item.price)}</span>
          </div>
          <div class="cart-page-stepper">
            <button type="button" class="cart-page-stepper-btn" onclick="window.cartManager.updateQuantity(${idx}, -1)" aria-label="Decrease quantity">&minus;</button>
            <span class="cart-page-stepper-val">${Number(item.quantity) || 1}</span>
            <button type="button" class="cart-page-stepper-btn" onclick="window.cartManager.updateQuantity(${idx}, 1)" aria-label="Increase quantity">&#43;</button>
          </div>
          <button type="button" class="cart-page-remove-btn" onclick="window.cartManager.removeItem(${idx})" aria-label="Remove ${safeTitle}">&times;</button>
        </div>
      `;
    }).join('');
  }

  renderCheckoutSummary(subtotal) {
    const checkoutList = document.getElementById('checkoutItemsList');
    const hiddenInputs = document.getElementById('hiddenCartInputs');
    const t = window.__trans || {};

    if (checkoutList) {
      if (this.items.length === 0) {
        checkoutList.innerHTML = `
          <p style="color: #707070; font-size: 14px; margin-bottom: 20px;">
            ${t.emptyCheckout || 'Keranjang Anda kosong.'} <a href="/shop" style="color: var(--color-accent); text-decoration: underline;">${t.chooseProduct || 'Pilih produk di katalog'}</a>.
          </p>
        `;
      } else {
        const itemsHtml = this.items.map(item => {
          const lineTotal = (Number(item.price) || 0) * (Number(item.quantity) || 1);
          return `
            <li class="order-summary-item">
              <span class="order-summary-item-name">${escCartHtml(item.title)} &times; ${Number(item.quantity) || 1}</span>
              <span class="order-summary-item-price">${this.formatPrice(lineTotal)}</span>
            </li>
          `;
        }).join('');

        const shippingFee = Number(this.currentShippingCost) || 0;
        const grandTotal = subtotal + shippingFee;

        checkoutList.innerHTML = `
          <div class="order-summary-header">
            <span>PRODUCT</span>
            <span>TOTAL</span>
          </div>
          <ul class="order-summary-list">
            ${itemsHtml}
          </ul>
          <div class="order-summary-calc-row">
            <span>SUBTOTAL</span>
            <span>${this.formatPrice(subtotal)}</span>
          </div>
          <div class="order-summary-calc-row">
            <span>SHIPPING</span>
            <span>${shippingFee > 0 ? this.formatPrice(shippingFee) : (t.freeShipping || 'Free shipping')}</span>
          </div>
          <div class="order-summary-total-row">
            <span>TOTAL</span>
            <span>${this.formatPrice(grandTotal)}</span>
          </div>
        `;
      }
    }

    if (hiddenInputs) {
      hiddenInputs.innerHTML = this.items.map((item, idx) => {
        const cleanId = this.normalizeProductId(item.id);
        const cleanQty = Math.max(1, parseInt(item.quantity, 10) || 1);
        const cleanSize = item.size || 'M';
        return `
          <input type="hidden" name="items[${idx}][id]" value="${cleanId}">
          <input type="hidden" name="items[${idx}][quantity]" value="${cleanQty}">
          <input type="hidden" name="items[${idx}][size]" value="${cleanSize}">
        `;
      }).join('');
    }
  }

  renderCheckoutPage() {
    this.updateTotals();
  }
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => {
    if (!window.cartManager) window.cartManager = new CartManager();
  });
} else {
  if (!window.cartManager) window.cartManager = new CartManager();
}
