/**
 * Shoppe - Master Store Management Dashboard Controller
 * Central controller to handle store overview, orders, catalog products,
 * live inquiries, and store configuration.
 */

// Escape any user/localStorage-provided value before injecting into innerHTML (XSS guard)
function esc(value) {
  return String(value ?? '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');
}

// Only allow relative paths or http(s) URLs for images
function safeImg(src) {
  const s = String(src || '').trim();
  if (/^(https?:)?\/\//i.test(s) || /^(\.\.\/|\.\/|assets\/)/.test(s)) return esc(s);
  return '../assets/images/product-1.png';
}

class AdminDashboard {
  constructor() {
    this.storageProductsKey = 'shoppe_admin_products_v1';
    this.storageOrdersKey = 'shoppe_admin_orders_v1';
    this.storageSettingsKey = 'shoppe_store_settings_v1';
    this.storageInquiriesKey = 'shoppe_admin_inquiries_v1';

    this.products = this.loadProducts();
    this.orders = this.loadOrders();
    this.settings = this.loadSettings();
    this.inquiries = this.loadInquiries();

    this.currentOrderFilter = 'all';
    this.searchQuery = '';
    this.editingProductId = null;

    this.init();
  }

  // 0. Toast Notification Helper
  showToast(message) {
    let container = document.getElementById('toastContainer');
    if (!container) {
      container = document.createElement('div');
      container.id = 'toastContainer';
      container.className = 'toast-container';
      container.setAttribute('aria-live', 'polite');
      document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = 'toast';
    toast.innerHTML = `
      <div class="toast-message">
        <span class="toast-check">&#10003;</span>
        <span>${esc(message)}</span>
      </div>
      <a href="../index.html" class="toast-action" target="_blank">LIHAT TOKO</a>
    `;

    container.appendChild(toast);
    requestAnimationFrame(() => toast.classList.add('show'));

    setTimeout(() => {
      toast.classList.remove('show');
      setTimeout(() => toast.remove(), 350);
    }, 3800);
  }

  // 1. Initial Data Loaders with Fallbacks
  loadProducts() {
    try {
      const saved = localStorage.getItem(this.storageProductsKey);
      if (saved) {
        const parsed = JSON.parse(saved);
        if (Array.isArray(parsed) && parsed.length > 0) return parsed;
      }
    } catch (e) {
      console.warn('Error reading stored products:', e);
    }

    // Default matching Shoppe catalog
    return [
      { id: 'p1', title: 'Lira Earrings', price: 20.00, sku: 'SKU-0012', category: 'Earrings', inStock: true, image: '../assets/images/product-1.png', sales: 48 },
      { id: 'p2', title: 'Hal Earrings', price: 25.00, sku: 'SKU-0014', category: 'Earrings', inStock: true, image: '../assets/images/product-2.png', sales: 32 },
      { id: 'p3', title: 'Kaede Hair Pin Set Of 3', price: 30.00, sku: 'SKU-0018', category: 'Hair pins', inStock: true, image: '../assets/images/product-3.png', sales: 29 },
      { id: 'p4', title: 'Hair Pin Set of 3', price: 30.00, sku: 'SKU-0021', category: 'Hair pins', inStock: true, image: '../assets/images/product-4.png', sales: 18 },
      { id: 'p5', title: 'Plaine Necklace', price: 19.00, sku: 'SKU-0025', category: 'Necklaces', inStock: true, image: '../assets/images/product-5.png', sales: 64 },
      { id: 'p6', title: 'Yuki Hair Pin Set of 3', price: 29.00, sku: 'SKU-0029', category: 'Hair pins', inStock: false, image: '../assets/images/product-6.png', sales: 12 }
    ];
  }

  saveProducts() {
    try {
      localStorage.setItem(this.storageProductsKey, JSON.stringify(this.products));
    } catch (e) {
      console.error(e);
    }
    this.renderProductsTable();
    this.updateStats();
    this.renderOverviewWidgets();
  }

  loadOrders() {
    try {
      const saved = localStorage.getItem(this.storageOrdersKey);
      if (saved) {
        const parsed = JSON.parse(saved);
        if (Array.isArray(parsed) && parsed.length > 0) return parsed;
      }
    } catch (e) {
      console.warn('Error reading stored orders:', e);
    }

    // Default orders matching store history & order-details.html
    return [
      { id: '7643980998990', date: '2026-11-04', customer: 'Vitatheme / Putri J.', email: 'vitatheme@gmail.com', phone: '+62 812 3456 7890', address: 'Jl. Jenderal Sudirman No. 45, Jakarta Selatan 12190, Indonesia', items: 'Lira Earrings (x3), Kaede Hair Pin (x1)', total: 105.00, status: 'delivered', payment: 'Bank Transfer' },
      { id: '943980998990', date: '2026-11-03', customer: 'Amalia Rahma', email: 'amalia@example.com', phone: '+62 813 9876 5432', address: 'Jl. Dago No. 88, Bandung 40135, Indonesia', items: 'Hal Earrings (x1), Plaine Necklace (x1)', total: 44.00, status: 'processing', payment: 'Credit Card' },
      { id: '8549120499211', date: '2026-11-02', customer: 'Budi Santoso', email: 'budi@example.com', phone: '+62 857 1122 3344', address: 'Jl. Pemuda No. 12, Surabaya 60271, Indonesia', items: 'Plaine Necklace (x2)', total: 38.00, status: 'shipped', payment: 'E-Wallet' },
      { id: '6419200481230', date: '2026-11-01', customer: 'Dewi Lestari', email: 'dewi@example.com', phone: '+62 811 2233 4455', address: 'Jl. Malioboro No. 19, Yogyakarta 55213, Indonesia', items: 'Hair Pin Set of 3 (x1)', total: 30.00, status: 'delivered', payment: 'Bank Transfer' },
      { id: '5219481029410', date: '2026-10-30', customer: 'Rian Pratama', email: 'rian@example.com', phone: '+62 818 7788 9900', address: 'Jl. Gajah Mada No. 7, Semarang 50134, Indonesia', items: 'Lira Earrings (x1)', total: 20.00, status: 'pending', payment: 'COD' }
    ];
  }

  saveOrders() {
    try {
      localStorage.setItem(this.storageOrdersKey, JSON.stringify(this.orders));
    } catch (e) {
      console.error(e);
    }
    this.renderOrdersTable();
    this.updateStats();
    this.renderOverviewWidgets();
  }

  loadSettings() {
    try {
      const saved = localStorage.getItem(this.storageSettingsKey);
      if (saved) return JSON.parse(saved);
    } catch (e) {
      console.warn(e);
    }
    return {
      storeName: 'SHOPPE',
      whatsappNumber: '6281234567890',
      currency: 'USD ($)',
      freeShippingMin: 50.00,
      bannerText: 'Gratis ongkir untuk pesanan perhiasan di atas $50 ke seluruh Indonesia!'
    };
  }

  saveSettings(newSettings) {
    this.settings = { ...this.settings, ...newSettings };
    try {
      localStorage.setItem(this.storageSettingsKey, JSON.stringify(this.settings));
    } catch (e) {
      console.error(e);
    }
  }

  loadInquiries() {
    try {
      const saved = localStorage.getItem(this.storageInquiriesKey);
      if (saved) {
        const parsed = JSON.parse(saved);
        if (Array.isArray(parsed) && parsed.length > 0) return parsed;
      }
    } catch (e) {
      console.warn(e);
    }

    return [
      { id: 'inq-1', name: 'Nadia S.', contact: '08129883192', topic: 'Konsultasi Perhiasan', message: 'Apakah Lira Earrings aman untuk telinga sensitif?', date: 'Hari ini, 14:20', status: 'answered', reply: 'Halo Nadia, semua anting kami hypoallergenic bebas timbal & nikel.' },
      { id: 'inq-2', name: 'Rizky Kurniawan', contact: '08571829101', topic: 'Cek Resi & Pengiriman', message: 'Kapan resi pesanan #8549120499211 dikirimkan?', date: 'Hari ini, 11:05', status: 'waiting', reply: '' },
      { id: 'inq-3', name: 'Clara Wijaya', contact: 'clara@example.com', topic: 'Kado Ulang Tahun', message: 'Bisa minta tolong sertakan kartu ucapan custom?', date: 'Kemarin', status: 'answered', reply: 'Bisa banget Kak Clara! Silakan tuliskan ucapan saat konfirmasi WhatsApp.' }
    ];
  }

  saveInquiries() {
    try {
      localStorage.setItem(this.storageInquiriesKey, JSON.stringify(this.inquiries));
    } catch (e) {
      console.error(e);
    }
    this.renderInquiriesTable();
    this.updateStats();
    this.renderOverviewWidgets();
  }

  init() {
    this.setupAuthGate();
    this.setupNavigation();
    this.renderAll();
    this.setupModals();
    this.setupEventListeners();
  }

    setupAuthGate() {
    const authGate = document.getElementById('adminAuthGate');
    const dashboardApp = document.getElementById('dashboardApp') || document.querySelector('.dashboard-app');
    const loginForm = document.getElementById('adminLoginForm');
    const emailInput = document.getElementById('adminEmailInput');
    const passwordInput = document.getElementById('adminPasswordInput');
    const authError = document.getElementById('adminAuthError');
    const btnQuick = document.getElementById('btnDemoQuickLogin');
    const logoutBtn = document.getElementById('adminLogoutBtn');

    const checkAuth = () => {
      const isAuth = localStorage.getItem('shoppe_admin_logged_in') === 'true';
      if (!isAuth) {
        if (dashboardApp) dashboardApp.style.display = 'none';
        if (authGate) authGate.style.display = 'flex';
        return false;
      } else {
        if (dashboardApp) dashboardApp.style.display = 'flex';
        if (authGate) authGate.style.display = 'none';
        return true;
      }
    };

    const doLogin = (email, pass) => {
      const cleanEmail = String(email || '').trim().toLowerCase();
      const cleanPass = String(pass || '').trim();

      // Verified Admin Credentials (strictly for store staff)
      const validAdmins = ['admin@shoppe.com', 'admin', 'manager@shoppe.com', 'owner@shoppe.com'];
      const validPass = ['admin123', 'admin', 'shoppe2026', 'manager123'];

      if (validAdmins.includes(cleanEmail) && validPass.includes(cleanPass)) {
        localStorage.setItem('shoppe_admin_logged_in', 'true');
        localStorage.setItem('shoppe_user_role', 'admin');
        if (authError) authError.style.display = 'none';
        checkAuth();
        this.renderAll();
        this.showToast('Selamat datang, Pengelola Toko SHOPPE!');
        return true;
      } else {
        if (authError) {
          authError.textContent = 'Akses ditolak: Kredensial pengelola toko tidak cocok.';
          authError.style.display = 'block';
        }
        return false;
      }
    };

    loginForm?.addEventListener('submit', (e) => {
      e.preventDefault();
      doLogin(emailInput?.value, passwordInput?.value);
    });

    btnQuick?.addEventListener('click', () => {
      if (emailInput) emailInput.value = 'admin@shoppe.com';
      if (passwordInput) passwordInput.value = 'admin123';
      doLogin('admin@shoppe.com', 'admin123');
    });

    logoutBtn?.addEventListener('click', (e) => {
      e.preventDefault();
      localStorage.removeItem('shoppe_admin_logged_in');
      localStorage.removeItem('shoppe_user_role');
      if (authError) authError.style.display = 'none';
      if (loginForm) loginForm.reset();
      checkAuth();
      this.showToast('Sesi pengelola toko telah ditutup.');
    });

    // Check on startup
    checkAuth();

    // Ensure initial store catalog & orders seed exists in storage
    try {
      if (!localStorage.getItem(this.storageProductsKey)) {
        localStorage.setItem(this.storageProductsKey, JSON.stringify(this.products));
      }
      if (!localStorage.getItem(this.storageOrdersKey)) {
        localStorage.setItem(this.storageOrdersKey, JSON.stringify(this.orders));
      }
      if (!localStorage.getItem(this.storageInquiriesKey)) {
        localStorage.setItem(this.storageInquiriesKey, JSON.stringify(this.inquiries));
      }
    } catch (e) { /* ignore */ }
  }

  renderAll() {
    this.updateStats();
    this.renderOrdersTable();
    this.renderProductsTable();
    this.renderInquiriesTable();
    this.renderSettingsForm();
    this.renderOverviewWidgets();
  }

  // 2. Navigation & Tabs
  setupNavigation() {
    const navLinks = document.querySelectorAll('.admin-nav-link[data-section]');
    const sections = document.querySelectorAll('.admin-panel-section');
    const pageTitle = document.getElementById('adminCurrentSectionTitle');
    const pageSub = document.getElementById('adminCurrentSectionSub');

    const titles = {
      'overview': { title: 'Overview & Metrik Toko', sub: 'Ringkasan performa dan transaksi real-time SHOPPE' },
      'orders': { title: 'Manajemen Pesanan', sub: 'Kelola status pesanan, pengiriman, dan rincian transaksi' },
      'products': { title: 'Katalog Produk', sub: 'Atur inventaris, harga, dan ketersediaan perhiasan' },
      'inquiries': { title: 'Live Chat & Pesan Masuk', sub: 'Pertanyaan pelanggan dari Live Chat dan formulir kontak' },
      'settings': { title: 'Pengaturan Toko', sub: 'Konfigurasi WhatsApp, kontak, dan sistem toko' }
    };

    const switchSection = (rawTargetId) => {
      const targetId = titles[rawTargetId] ? rawTargetId : 'overview';

      navLinks.forEach(link => {
        link.classList.toggle('active', link.dataset.section === targetId);
      });

      sections.forEach(sec => {
        sec.classList.toggle('active', sec.id === `section-${targetId}`);
      });

      if (pageTitle && titles[targetId]) {
        pageTitle.textContent = titles[targetId].title;
        pageSub.textContent = titles[targetId].sub;
      }

      // Close mobile sidebar if open
      document.getElementById('adminSidebar')?.classList.remove('open');
    };

    navLinks.forEach(link => {
      link.addEventListener('click', (e) => {
        e.preventDefault();
        const target = link.dataset.section;
        window.location.hash = target;
        switchSection(target);
      });
    });

    // Check URL hash on load
    const hash = window.location.hash.replace('#', '') || 'overview';
    switchSection(hash);

    // Mobile sidebar toggle
    document.getElementById('adminMobileToggle')?.addEventListener('click', () => {
      document.getElementById('adminSidebar')?.classList.toggle('open');
    });
  }

  // 3. Stats & Overview
  updateStats() {
    const totalRevenue = this.orders
      .filter(o => o.status !== 'cancelled')
      .reduce((sum, o) => sum + (Number(o.total) || 0), 0);

    const activeProducts = this.products.filter(p => p.inStock).length;
    const pendingOrders = this.orders.filter(o => o.status === 'processing' || o.status === 'pending').length;
    const waitingInquiries = this.inquiries.filter(i => i.status === 'waiting').length;

    const statRev = document.getElementById('statTotalRevenue');
    const statOrders = document.getElementById('statTotalOrders');
    const statProd = document.getElementById('statActiveProducts');
    const statInq = document.getElementById('statWaitingInquiries');

    if (statRev) statRev.textContent = `$ ${totalRevenue.toLocaleString('en-US', { minimumFractionDigits: 2 })}`;
    if (statOrders) statOrders.textContent = this.orders.length;
    if (statProd) statProd.textContent = `${activeProducts} / ${this.products.length}`;
    if (statInq) statInq.textContent = waitingInquiries;

    // Update nav pills
    const pillOrders = document.getElementById('navPillOrders');
    const pillInq = document.getElementById('navPillInquiries');
    if (pillOrders) pillOrders.textContent = pendingOrders;
    if (pillInq) pillInq.textContent = waitingInquiries;
  }

  renderOverviewWidgets() {
    // 1. Top Selling list
    const topContainer = document.getElementById('overviewTopProducts');
    if (topContainer) {
      topContainer.innerHTML = '';
      const sorted = [...this.products].sort((a, b) => (b.sales || 0) - (a.sales || 0)).slice(0, 4);
      sorted.forEach(p => {
        const salesCount = Number(p.sales) || 0;
        const revenue = (Number(p.price) || 0) * salesCount;
        const item = document.createElement('div');
        item.className = 'top-product-item';
        item.innerHTML = `
          <div class="top-product-left">
            <img src="${safeImg(p.image)}" alt="${esc(p.title)}" class="top-product-thumb" onerror="this.src='../assets/images/product-1.png'">
            <div class="top-product-meta">
              <h4>${esc(p.title)}</h4>
              <span>${salesCount} terjual • ${esc(p.category)}</span>
            </div>
          </div>
          <div class="top-product-revenue">$ ${revenue.toFixed(2)}</div>
        `;
        topContainer.appendChild(item);
      });
    }

    // 2. Recent 4 orders on overview
    const recentOrdersContainer = document.getElementById('overviewRecentOrdersTableBody');
    if (recentOrdersContainer) {
      recentOrdersContainer.innerHTML = '';
      this.orders.slice(0, 4).forEach(o => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td><strong>#${esc(o.id)}</strong></td>
          <td>${esc(o.customer)}</td>
          <td>${esc(o.date)}</td>
          <td><strong>$ ${Number(o.total).toFixed(2)}</strong></td>
          <td><span class="badge-status ${esc(o.status)}">${esc(o.status)}</span></td>
          <td><a href="order-details.html?order=${encodeURIComponent(o.id)}" class="table-action-btn">Detail</a></td>
        `;
        recentOrdersContainer.appendChild(tr);
      });
    }
  }

  // 4. Orders Table
  renderOrdersTable() {
    const tbody = document.getElementById('adminOrdersTableBody');
    if (!tbody) return;

    tbody.innerHTML = '';
    const q = this.searchQuery;
    const filtered = this.orders.filter(o => {
      const statusMatch = this.currentOrderFilter === 'all' || o.status === this.currentOrderFilter;
      if (!statusMatch) return false;
      if (!q) return true;
      const haystack = `${o.id} ${o.customer} ${o.items} ${o.date} ${o.status} ${o.payment || ''}`.toLowerCase();
      return haystack.includes(q);
    });

    if (filtered.length === 0) {
      tbody.innerHTML = `<tr><td colspan="7" style="text-align: center; padding: 32px; color: #888;">Tidak ada pesanan yang cocok dengan filter / pencarian ini.</td></tr>`;
      return;
    }

    filtered.forEach(o => {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td><strong>#${esc(o.id)}</strong></td>
        <td>${esc(o.date)}</td>
        <td>
          <div style="font-weight: 500;">${esc(o.customer)}</div>
          <div style="font-size: 11.5px; color: #777;">${esc(o.payment || 'Direct')}</div>
        </td>
        <td style="max-width: 240px; font-size: 12.5px; color: #555;">${esc(o.items)}</td>
        <td><strong>$ ${Number(o.total).toFixed(2)}</strong></td>
        <td>
          <select class="status-select" data-order-id="${esc(o.id)}" aria-label="Status pesanan #${esc(o.id)}">
            <option value="pending" ${o.status === 'pending' ? 'selected' : ''}>Pending</option>
            <option value="processing" ${o.status === 'processing' ? 'selected' : ''}>Processing</option>
            <option value="shipped" ${o.status === 'shipped' ? 'selected' : ''}>Shipped</option>
            <option value="delivered" ${o.status === 'delivered' ? 'selected' : ''}>Delivered</option>
            <option value="cancelled" ${o.status === 'cancelled' ? 'selected' : ''}>Cancelled</option>
          </select>
        </td>
        <td>
          <div style="display: flex; gap: 6px;">
            <a href="order-details.html?order=${encodeURIComponent(o.id)}" class="table-action-btn" title="Buka Detail">
              Invoice
            </a>
            <button type="button" class="table-action-btn btn-danger" data-action="delete-order" data-order-id="${esc(o.id)}" title="Hapus">
              &times;
            </button>
          </div>
        </td>
      `;
      tbody.appendChild(tr);
    });

    // Attach order status changes
    tbody.querySelectorAll('.status-select').forEach(select => {
      select.addEventListener('change', (e) => {
        const orderId = select.dataset.orderId;
        const newStatus = e.target.value;
        const order = this.orders.find(o => o.id === orderId);
        if (order) {
          order.status = newStatus;
          this.saveOrders();
          this.showToast(`Status pesanan #${orderId} diperbarui menjadi ${newStatus}.`);
        }
      });
    });

    // Attach delete order
    tbody.querySelectorAll('[data-action="delete-order"]').forEach(btn => {
      btn.addEventListener('click', () => {
        const id = btn.dataset.orderId;
        if (confirm(`Yakin ingin menghapus pesanan #${id}?`)) {
          this.orders = this.orders.filter(o => o.id !== id);
          this.saveOrders();
          this.showToast(`Pesanan #${id} telah dihapus.`);
        }
      });
    });
  }

  // 5. Products Table
  renderProductsTable() {
    const tbody = document.getElementById('adminProductsTableBody');
    if (!tbody) return;

    tbody.innerHTML = '';
    const q = this.searchQuery;
    const filtered = this.products.filter(p => {
      if (!q) return true;
      const haystack = `${p.id} ${p.title} ${p.sku || ''} ${p.category} ${p.price}`.toLowerCase();
      return haystack.includes(q);
    });

    if (filtered.length === 0) {
      tbody.innerHTML = `<tr><td colspan="6" style="text-align: center; padding: 32px; color: #888;">Tidak ada produk yang cocok dengan pencarian "${esc(this.searchQuery)}".</td></tr>`;
      return;
    }

    filtered.forEach(p => {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>
          <div style="display: flex; align-items: center; gap: 12px;">
            <img src="${safeImg(p.image)}" alt="${esc(p.title)}" class="top-product-thumb" onerror="this.src='../assets/images/product-1.png'">
            <div>
              <div style="font-weight: 600;">${esc(p.title)}</div>
              <div style="font-size: 11px; color: #888;">${esc(p.sku || p.id)}</div>
            </div>
          </div>
        </td>
        <td>${esc(p.category)}</td>
        <td><strong>$ ${Number(p.price).toFixed(2)}</strong></td>
        <td>${p.sales || 0} unit</td>
        <td>
          <span class="badge-status ${p.inStock ? 'instock' : 'outofstock'}">
            ${p.inStock ? 'Tersedia' : 'Habis'}
          </span>
        </td>
        <td>
          <div style="display: flex; gap: 6px; flex-wrap: wrap;">
            <button type="button" class="table-action-btn" data-action="edit-product" data-prod-id="${esc(p.id)}">
              Edit
            </button>
            <button type="button" class="table-action-btn" data-action="toggle-stock" data-prod-id="${esc(p.id)}">
              ${p.inStock ? 'Set Habis' : 'Set Ada'}
            </button>
            <button type="button" class="table-action-btn btn-danger" data-action="delete-product" data-prod-id="${esc(p.id)}">
              Hapus
            </button>
          </div>
        </td>
      `;
      tbody.appendChild(tr);
    });

    // Edit product
    tbody.querySelectorAll('[data-action="edit-product"]').forEach(btn => {
      btn.addEventListener('click', () => {
        const id = btn.dataset.prodId;
        const prod = this.products.find(p => p.id === id);
        if (prod) {
          this.openProductModal(prod);
        }
      });
    });

    // Stock toggle
    tbody.querySelectorAll('[data-action="toggle-stock"]').forEach(btn => {
      btn.addEventListener('click', () => {
        const id = btn.dataset.prodId;
        const prod = this.products.find(p => p.id === id);
        if (prod) {
          prod.inStock = !prod.inStock;
          this.saveProducts();
          this.showToast(`Stok "${prod.title}" diubah menjadi ${prod.inStock ? 'Tersedia' : 'Habis'}.`);
        }
      });
    });

    // Delete product
    tbody.querySelectorAll('[data-action="delete-product"]').forEach(btn => {
      btn.addEventListener('click', () => {
        const id = btn.dataset.prodId;
        const prod = this.products.find(p => p.id === id);
        if (confirm(`Hapus produk "${prod ? prod.title : id}" dari katalog?`)) {
          this.products = this.products.filter(p => p.id !== id);
          this.saveProducts();
          this.showToast('Produk berhasil dihapus dari katalog.');
        }
      });
    });
  }

  // 6. Inquiries Table
  renderInquiriesTable() {
    const tbody = document.getElementById('adminInquiriesTableBody');
    if (!tbody) return;

    tbody.innerHTML = '';
    const q = this.searchQuery;
    const filtered = this.inquiries.filter(inq => {
      if (!q) return true;
      const haystack = `${inq.name} ${inq.contact} ${inq.topic} ${inq.message} ${inq.reply || ''}`.toLowerCase();
      return haystack.includes(q);
    });

    if (filtered.length === 0) {
      tbody.innerHTML = `<tr><td colspan="5" style="text-align: center; padding: 32px; color: #888;">Tidak ada pesan masuk yang cocok.</td></tr>`;
      return;
    }

    filtered.forEach(inq => {
      const rawContact = String(inq.contact || '');
      const digits = rawContact.replace(/\D/g, '');
      const isEmail = rawContact.includes('@');
      const contactHref = isEmail
        ? `mailto:${encodeURIComponent(rawContact)}?subject=${encodeURIComponent('Re: ' + (inq.topic || 'SHOPPE Inquiry'))}`
        : `https://wa.me/${digits || (this.settings.whatsappNumber || '6281234567890').replace(/\D/g, '')}?text=${encodeURIComponent('Halo ' + (inq.name || 'Kak') + ', perihal pertanyaan Anda di SHOPPE: ')}`;
      const contactLabel = isEmail ? 'Email' : 'WhatsApp';

      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>
          <strong>${esc(inq.name)}</strong>
          <div style="font-size: 11.5px; color: #777;">${esc(rawContact)} • ${esc(inq.date || '')}</div>
        </td>
        <td><span style="font-weight: 500;">${esc(inq.topic)}</span></td>
        <td style="max-width: 280px; font-size: 13px;">
          <div>"${esc(inq.message)}"</div>
          ${inq.reply ? `<div style="font-size: 11.5px; color: #10B981; margin-top: 4px;">↳ Balasan: ${esc(inq.reply)}</div>` : ''}
        </td>
        <td><span class="badge-status ${esc(inq.status)}">${inq.status === 'answered' ? 'Terbalas' : 'Menunggu'}</span></td>
        <td>
          <div style="display: flex; gap: 6px;">
            <button type="button" class="table-action-btn" data-action="reply-inquiry" data-inq-id="${esc(inq.id)}">
              Balas
            </button>
            <a href="${contactHref}" target="_blank" rel="noopener noreferrer" class="table-action-btn" style="color: ${isEmail ? '#2563EB' : '#25D366'};">
              ${contactLabel}
            </a>
          </div>
        </td>
      `;
      tbody.appendChild(tr);
    });

    // Reply trigger (note: prompt() takes plain text, not HTML entities)
    tbody.querySelectorAll('[data-action="reply-inquiry"]').forEach(btn => {
      btn.addEventListener('click', () => {
        const inqId = btn.dataset.inqId;
        const inq = this.inquiries.find(i => i.id === inqId);
        if (inq) {
          const ans = prompt(`Balas pertanyaan dari ${inq.name}:\n"${inq.message}"`, inq.reply || '');
          if (ans !== null && ans.trim()) {
            inq.reply = ans.trim();
            inq.status = 'answered';
            this.saveInquiries();
            this.showToast(`Balasan untuk ${inq.name} berhasil dikirim.`);
          }
        }
      });
    });
  }

  // 7. Settings Form
  renderSettingsForm() {
    const inputStore = document.getElementById('settingStoreName');
    const inputWa = document.getElementById('settingWhatsApp');
    const inputFreeShip = document.getElementById('settingFreeShipping');
    const inputBanner = document.getElementById('settingBanner');

    if (inputStore) inputStore.value = this.settings.storeName;
    if (inputWa) inputWa.value = this.settings.whatsappNumber;
    if (inputFreeShip) inputFreeShip.value = this.settings.freeShippingMin;
    if (inputBanner) inputBanner.value = this.settings.bannerText;

    const form = document.getElementById('adminSettingsForm');
    if (form && !form.dataset.bound) {
      form.dataset.bound = 'true';
      form.addEventListener('submit', (e) => {
        e.preventDefault();
        const parsedFreeShip = Number(inputFreeShip.value);
        this.saveSettings({
          storeName: inputStore.value.trim() || 'SHOPPE',
          whatsappNumber: inputWa.value.trim(),
          freeShippingMin: Number.isFinite(parsedFreeShip) && parsedFreeShip >= 0 ? parsedFreeShip : 50,
          bannerText: inputBanner.value.trim()
        });
        this.showToast('Pengaturan toko berhasil disimpan!');
      });
    }
  }

  // 8. Modals & Add/Edit Product
  openProductModal(prod = null) {
    const modalAddProduct = document.getElementById('adminModalAddProduct');
    const modalTitle = document.getElementById('modalAddTitle');
    const submitBtn = document.getElementById('modalSubmitProductBtn');
    const formAddProduct = document.getElementById('adminFormAddProduct');

    if (!modalAddProduct || !formAddProduct) return;

    if (prod) {
      this.editingProductId = prod.id;
      if (modalTitle) modalTitle.textContent = 'Edit Produk Perhiasan';
      if (submitBtn) submitBtn.textContent = 'Simpan Perubahan';
      document.getElementById('addProdTitle').value = prod.title || '';
      document.getElementById('addProdPrice').value = Number(prod.price || 0).toFixed(2);
      document.getElementById('addProdCategory').value = prod.category || 'Earrings';
      document.getElementById('addProdSku').value = prod.sku || '';
      document.getElementById('addProdImage').value = prod.image || '../assets/images/product-1.png';
    } else {
      this.editingProductId = null;
      if (modalTitle) modalTitle.textContent = 'Tambah Produk Perhiasan';
      if (submitBtn) submitBtn.textContent = 'Tambahkan ke Katalog';
      formAddProduct.reset();
      document.getElementById('addProdImage').value = '../assets/images/product-1.png';
    }

    modalAddProduct.classList.add('open');
  }

  setupModals() {
    const modalAddProduct = document.getElementById('adminModalAddProduct');
    const btnOpenAdd = document.getElementById('btnOpenAddProduct');
    const btnCloseAdd = document.getElementById('btnCloseAddProduct');
    const formAddProduct = document.getElementById('adminFormAddProduct');

    btnOpenAdd?.addEventListener('click', () => {
      this.openProductModal(null);
    });

    btnCloseAdd?.addEventListener('click', () => {
      modalAddProduct?.classList.remove('open');
    });

    modalAddProduct?.addEventListener('click', (e) => {
      if (e.target === modalAddProduct) {
        modalAddProduct.classList.remove('open');
      }
    });

    formAddProduct?.addEventListener('submit', (e) => {
      e.preventDefault();
      const title = document.getElementById('addProdTitle').value.trim();
      const price = parseFloat(document.getElementById('addProdPrice').value) || 0;
      const category = document.getElementById('addProdCategory').value;
      const sku = document.getElementById('addProdSku').value.trim();
      const image = document.getElementById('addProdImage').value.trim() || '../assets/images/product-1.png';

      if (this.editingProductId) {
        const existing = this.products.find(p => p.id === this.editingProductId);
        if (existing) {
          existing.title = title;
          existing.price = price;
          existing.category = category;
          existing.sku = sku || existing.sku;
          existing.image = image;
        }
        this.saveProducts();
        this.editingProductId = null;
        formAddProduct.reset();
        modalAddProduct.classList.remove('open');
        this.showToast(`Produk "${title}" berhasil diperbarui!`);
      } else {
        const newProd = {
          id: 'p' + (Date.now() % 100000),
          title: title,
          price: price,
          category: category,
          sku: sku || 'SKU-' + Math.floor(Math.random() * 9000 + 1000),
          image: image,
          inStock: true,
          sales: 0
        };

        this.products.unshift(newProd);
        this.saveProducts();

        formAddProduct.reset();
        modalAddProduct.classList.remove('open');
        this.showToast(`Produk "${title}" berhasil ditambahkan ke katalog SHOPPE!`);
      }
    });
  }

  setupEventListeners() {
    // Filter tabs for orders
    document.querySelectorAll('.admin-filter-tab[data-order-filter]').forEach(tab => {
      tab.addEventListener('click', () => {
        document.querySelectorAll('.admin-filter-tab[data-order-filter]').forEach(t => t.classList.remove('active'));
        tab.classList.add('active');
        this.currentOrderFilter = tab.dataset.orderFilter;
        this.renderOrdersTable();
      });
    });

    // Global Search across orders, products, and inquiries
    const searchInput = document.getElementById('adminGlobalSearch');
    searchInput?.addEventListener('input', (e) => {
      this.searchQuery = e.target.value.toLowerCase().trim();
      this.renderOrdersTable();
      this.renderProductsTable();
      this.renderInquiriesTable();
    });

    // Listen for cross-tab storage updates (e.g., new order from checkout.html or livechat inquiry)
    window.addEventListener('storage', (e) => {
      if (e.key === this.storageOrdersKey) {
        this.orders = this.loadOrders();
        this.renderOrdersTable();
        this.updateStats();
        this.renderOverviewWidgets();
      } else if (e.key === this.storageProductsKey) {
        this.products = this.loadProducts();
        this.renderProductsTable();
        this.updateStats();
        this.renderOverviewWidgets();
      } else if (e.key === this.storageInquiriesKey) {
        this.inquiries = this.loadInquiries();
        this.renderInquiriesTable();
        this.updateStats();
      }
    });
  }
}

// Instantiate on DOM ready
document.addEventListener('DOMContentLoaded', () => {
  window.ShoppeDashboard = new AdminDashboard();
});
