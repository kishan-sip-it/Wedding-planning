// Samaaroh Cart System - v1.0
class SamaarohCart {
    constructor() {
        this.cart = JSON.parse(localStorage.getItem('samaaroh_cart')) || [];
        this.mode = localStorage.getItem('samaaroh_mode') || 'individual';
        this.init();
    }

    init() {
        this.bindEvents();
        this.updateDisplay();
    }

    bindEvents() {
        // Individual service buttons
        document.querySelectorAll('.add-service').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const service = {
                    id: parseInt(e.currentTarget.dataset.id),
                    title: e.currentTarget.dataset.title,
                    price: parseFloat(e.currentTarget.dataset.price),
                    provider: e.currentTarget.dataset.provider,
                    type: 'service'
                };
                this.addToCart(service);
            });
        });

        // Package buttons
        document.querySelectorAll('.add-package').forEach(btn => {
            btn.addEventListener('click', (e) => {
                try {
                    const packageData = JSON.parse(e.currentTarget.dataset.package);
                    this.setMode('package');
                    this.clearCart();
                    if (packageData.services && Array.isArray(packageData.services)) {
                        packageData.services.forEach(service => {
                            this.addToCart({
                                ...service,
                                type: 'service'
                            });
                        });
                    }
                } catch (err) {
                    console.error('Invalid package data:', err);
                }
            });
        });

        // Mode toggle buttons
        document.querySelectorAll('[data-mode]').forEach(btn => {
            btn.addEventListener('click', (e) => {
                this.setMode(e.currentTarget.dataset.mode);
            });
        });

        // Clear cart button
        const clearBtn = document.getElementById('clear-cart');
        if (clearBtn) {
            clearBtn.addEventListener('click', () => {
                if (confirm('Clear all selections?')) {
                    this.clearCart();
                    this.setMode('individual');
                }
            });
        }

        // Checkout button
        const checkoutBtn = document.getElementById('checkout-btn');
        if (checkoutBtn) {
            checkoutBtn.addEventListener('click', () => {
                if (this.cart.length === 0) return;
                
                // In real app: send to server
                alert('Checkout initiated! (Demo mode)\nTotal: ₹' + this.getTotal().toFixed(2));
                console.log('Cart items:', this.cart);
            });
        }
    }

    addToCart(item) {
        if (this.mode === 'package') {
            // Package mode: add all services as-is
            this.cart.push(item);
        } else {
            // Individual mode: allow quantity
            const existing = this.cart.find(i => i.id === item.id && i.type === 'service');
            if (existing) {
                existing.quantity = (existing.quantity || 1) + 1;
            } else {
                item.quantity = 1;
                this.cart.push(item);
            }
        }
        this.save();
        this.updateDisplay();
    }

    clearCart() {
        this.cart = [];
        this.save();
        this.updateDisplay();
    }

    setMode(mode) {
        this.mode = mode;
        this.save();
        this.updateDisplay();
    }

    save() {
        localStorage.setItem('samaaroh_cart', JSON.stringify(this.cart));
        localStorage.setItem('samaaroh_mode', this.mode);
    }

    getTotal() {
        return this.cart.reduce((sum, item) => sum + (item.price * (item.quantity || 1)), 0);
    }

    updateDisplay() {
        const total = this.getTotal();
        
        // Update cart summary
        const summaryEl = document.getElementById('cart-summary');
        if (summaryEl) {
            summaryEl.innerHTML = `
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div class="mb-2 mb-md-0">
                        <strong>Total:</strong> ₹${total.toFixed(2)}
                        ${this.mode === 'package' ? '<span class="badge bg-success ms-2">Package Mode</span>' : ''}
                    </div>
                    <button id="checkout-btn" class="btn btn-success ${total > 0 ? '' : 'disabled'}">
                        Proceed to Checkout
                    </button>
                </div>
            `;
            // Rebind checkout button
            document.getElementById('checkout-btn')?.addEventListener('click', () => {
                if (this.cart.length === 0) return;
                alert('Checkout initiated! (Demo mode)\nTotal: ₹' + total.toFixed(2));
            });
        }

        // Update cart items list
        const itemsEl = document.getElementById('cart-items');
        if (itemsEl) {
            if (this.cart.length === 0) {
                itemsEl.innerHTML = '<p class="text-muted">Your cart is empty.</p>';
            } else {
                itemsEl.innerHTML = this.cart.map((item, index) => `
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <div>
                            <strong>${item.title}</strong>
                            ${item.provider ? `<br><small class="text-muted">by ${item.provider}</small>` : ''}
                            ${item.quantity > 1 ? `<br><small class="text-muted">Qty: ${item.quantity}</small>` : ''}
                        </div>
                        <div>₹${(item.price * (item.quantity || 1)).toFixed(2)}</div>
                    </div>
                `).join('');
            }
        }

        // Toggle UI sections
        const individualSection = document.getElementById('individual-services');
        const packageSection = document.getElementById('package-section');
        if (individualSection && packageSection) {
            if (this.mode === 'package') {
                individualSection.classList.add('d-none');
                packageSection.classList.remove('d-none');
            } else {
                individualSection.classList.remove('d-none');
                packageSection.classList.add('d-none');
            }
        }

        // Update mode buttons
        document.querySelectorAll('[data-mode]').forEach(btn => {
            const isActive = btn.dataset.mode === this.mode;
            btn.classList.toggle('active', isActive);
            if (isActive) {
                btn.classList.add('btn-primary');
                btn.classList.remove('btn-outline-primary', 'btn-outline-success');
            } else {
                btn.classList.remove('btn-primary');
                if (btn.dataset.mode === 'individual') {
                    btn.classList.add('btn-outline-primary');
                } else {
                    btn.classList.add('btn-outline-success');
                }
            }
        });
    }
    
}
// Package Creation Logic (for admin/create_package.php)
document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('services-container');
    const addButton = document.getElementById('add-service-btn');
    const template = document.getElementById('service-template');
    const totalPriceEl = document.getElementById('total-price');
    const totalPriceInput = document.getElementById('total-price-input');

    if (!container || !addButton) return; // Not on package page

    function updateTotal() {
        let total = 0;
        container.querySelectorAll('.service-select').forEach(select => {
            const price = parseFloat(select.options[select.selectedIndex]?.dataset.price) || 0;
            total += price;
        });
        totalPriceEl.textContent = total.toFixed(2);
        totalPriceInput.value = total;
    }

    addButton.addEventListener('click', () => {
        const clone = document.importNode(template.content, true);
        container.appendChild(clone);
        
        // Bind remove button
        const removeBtn = container.lastElementChild.querySelector('.remove-service');
        removeBtn.addEventListener('click', () => {
            container.lastElementChild.remove();
            updateTotal();
        });
        
        // Bind select change
        const select = container.lastElementChild.querySelector('.service-select');
        select.addEventListener('change', updateTotal);
        
        updateTotal();
    });

    // Initialize with one service
    addButton.click();
});
// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.samaarohCart = new SamaarohCart();
});