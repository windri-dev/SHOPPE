/**
 * Shoppe - Cart State Management & Drawer Controller
 * Handles items, localStorage persistence, quantity adjustments, coupons, shipping, and UI rendering.
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
    this.couponKey = 'shoppe_cart_coupon';
    this.shippingKey = 'shoppe_cart_shipping';
    this.items = this.loadCart();
    this.drawer = document.getElementById('cartDrawer');
    this.backdrop = document.getElementById('drawerBackdrop') || document.getElementById('cartDrawerBackdrop');
    this.cartBody = document.getElementById('cartDrawerBody') || document.getElementById('cartDrawerItems');
    this.subtotalEl = document.getElementById('cartSubtotal') || document.getElementById('cartDrawerSubtotal');
    this.badgeEl = document.getElementById('cartBadgeCount');
    this.itemCountEl = document.getElementById('drawerItemCount') || document.getElementById('cartDrawerCount');
    this.subtotalLabelEl = document.getElementById('subtotalLabel');
    this.isSubfolder = window.location.pathname.includes('/pages/');

    this.init();
  }

  resolveAssetPath(src) {
    const isSub = window.location.pathname.includes('/pages/');
    if (!src) return isSub ? '../assets/images/product-1.png' : 'assets/images/product-1.png';
    if (/^(https?:)?\/\//i.test(src) || src.startsWith('/')) return escCartHtml(src);
    const clean = src.replace(/^(\.\.\/)+/, '').replace(/^assets\//, '');
    return escCartHtml(isSub ? `../assets/${clean}` : `assets/${clean}`);
  }

  loadCart() {
    const figmaMockKey = 'shoppe_cart_figma_v5';
    if (!localStorage.getItem(figmaMockKey)) {
      localStorage.setItem(figmaMockKey, 'true');
      localStorage.removeItem(this.storageKey);
    }

    try {
      const saved = localStorage.getItem(this.storageKey);
      if (saved) return JSON.parse(saved);
    } catch (e) {
      console.error('Error loading cart:', e);
    }

    // Default initial mock items:
    // Lira Earrings ($20 x 2 = $40), Ollie Hoop Earrings ($20 x 1 = $20), Kaede Hair Pin ($30 x 1 = $30) -> Subtotal $90.00
    return [
      {
        id: 'p1',
        title: 'Lira Earrings',
        price: 20.00,
        variant: 'Black / Medium',
        image: 'assets/images/product-1.png',
        quantity: 2
      },
      {
        id: 'p11',
        title: 'Ollie Hoop Earrings',
        price: 20.00,
        variant: 'Black / Medium',
        image: 'assets/images/product-4.png',
        quantity: 1
      },
      {
        id: 'p3',
        title: 'Kaede Hair Pin Set Of 3',
        price: 30.00,
        variant: 'Black / Medium',
        image: 'assets/images/product-3.png',
        quantity: 1
      }
    ];
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
    localStorage.removeItem(this.couponKey);
    this.saveCart();
  }

  init() {
    this.drawer = document.getElementById('cartDrawer');
    this.backdrop = document.getElementById('drawerBackdrop') || document.getElementById('cartDrawerBackdrop');
    this.cartBody = document.getElementById('cartDrawerBody') || document.getElementById('cartDrawerItems');
    this.subtotalEl = document.getElementById('cartSubtotal') || document.getElementById('cartDrawerSubtotal');
    this.badgeEl = document.getElementById('cartBadgeCount');
    this.itemCountEl = document.getElementById('drawerItemCount') || document.getElementById('cartDrawerCount');
    this.subtotalLabelEl = document.getElementById('subtotalLabel');

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
    if (!this.drawer) this.drawer = document.getElementById('cartDrawer');
    if (!this.backdrop) this.backdrop = document.getElementById('drawerBackdrop') || document.getElementById('cartDrawerBackdrop');

    if (this.drawer) {
      this.drawer.classList.add('open');
      if (this.backdrop) this.backdrop.classList.add('open');
      document.body.style.overflow = 'hidden';
    }
  }

  closeDrawer() {
    if (!this.drawer) this.drawer = document.getElementById('cartDrawer');
    if (!this.backdrop) this.backdrop = document.getElementById('drawerBackdrop') || document.getElementById('cartDrawerBackdrop');

    if (this.drawer) {
      this.drawer.classList.remove('open');
      if (this.backdrop) this.backdrop.classList.remove('open');
      document.body.style.overflow = '';
    }
  }

  addItem(product, qty = 1) {
    if (typeof product === 'string') {
      const pId = product;
      const pInfo = (window.PRODUCT_DATA && window.PRODUCT_DATA[pId]) ? window.PRODUCT_DATA[pId] : null;
      if (pInfo && pInfo.inStock === false) {
        (window.ShoppeApp || window.appController)?.showToast(`Sorry, "${pInfo.title}" is currently out of stock.`);
        return;
      }
      product = {
        id: pId,
        title: pInfo ? pInfo.title : 'Product',
        price: pInfo ? pInfo.price : 20.00,
        variant: 'Black / Medium',
        image: pInfo ? pInfo.image : 'assets/images/product-1.png',
        quantity: qty
      };
    } else if (product && product.id && window.PRODUCT_DATA && window.PRODUCT_DATA[product.id]?.inStock === false) {
      (window.ShoppeApp || window.appController)?.showToast(`Sorry, "${product.title}" is currently out of stock.`);
      return;
    }

    const existingIndex = this.items.findIndex(item => item.id === product.id && item.variant === (product.variant || 'Black / Medium'));
    if (existingIndex > -1) {
      this.items[existingIndex].quantity += (product.quantity || 1);
    } else {
      this.items.push({
        id: product.id,
        title: product.title,
        price: parseFloat(product.price),
        variant: product.variant || 'Black / Medium',
        image: product.image,
        quantity: product.quantity || 1
      });
    }

    this.saveCart();
    this.triggerBadgeBump();
    this.openDrawer();
    (window.ShoppeApp || window.appController)?.showToast(`Added "${product.title}" to your shopping bag!`);
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
    return this.items.reduce((total, item) => total + item.quantity, 0);
  }

  getSubtotal() {
    return this.items.reduce((total, item) => total + (item.price * item.quantity), 0);
  }

  formatPrice(num) {
    return '$ ' + Number(num || 0).toFixed(2).replace('.', ',');
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

    // Ensure both VIEW CART and CHECKOUT buttons work in drawer footer
    const footerActions = this.drawer?.querySelector('.drawer-footer-actions');
    if (footerActions) {
      const cartUrl = this.isSubfolder ? 'cart.html' : 'pages/cart.html';
      const checkoutUrl = this.isSubfolder ? 'checkout.html' : 'pages/checkout.html';
      footerActions.innerHTML = `
        <a href="${cartUrl}" class="btn-drawer-view-cart">VIEW CART</a>
        <a href="${checkoutUrl}" class="btn btn-primary btn-block" style="margin-top: 8px; text-align: center;">PROCEED TO CHECKOUT</a>
      `;
    }

    if (!this.cartBody) return;

    if (this.items.length === 0) {
      this.cartBody.innerHTML = `
        <div class="cart-empty-state">
          <svg class="cart-empty-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
          </svg>
          <p>Your shopping bag is currently empty.</p>
          <button type="button" class="btn btn-outline btn-sm" onclick="window.cartManager.closeDrawer()">Explore Shop</button>
        </div>
      `;
      return;
    }

    const defaultImg = this.resolveAssetPath('assets/images/product-1.png');
    this.cartBody.innerHTML = this.items.map((item, idx) => {
      const safeTitle = escCartHtml(item.title);
      const safeVariant = escCartHtml(item.variant || 'Black / Medium');
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
              <span class="cart-item-qty-label">QTY:</span>
              <button type="button" class="cart-item-qty-btn" onclick="window.cartManager.updateQuantity(${idx}, -1)" aria-label="Decrease quantity">-</button>
              <span class="cart-item-qty-val">${Number(item.quantity) || 1}</span>
              <button type="button" class="cart-item-qty-btn" onclick="window.cartManager.updateQuantity(${idx}, 1)" aria-label="Increase quantity">+</button>
            </div>
          </div>
        </div>
      `;
    }).join('');
  }
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => {
    if (!window.cartManager) window.cartManager = new CartManager();
  });
} else {
  if (!window.cartManager) window.cartManager = new CartManager();
}
