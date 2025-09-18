document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle
    const hamburger = document.querySelector('.hamburger');
    const navMenu = document.querySelector('.nav-menu');

    hamburger.addEventListener('click', function(e) {
        e.stopPropagation();
        navMenu.classList.toggle('active');
    });

    // Close mobile menu when clicking on a link
    document.querySelectorAll('.nav-item a').forEach(link => {
        link.addEventListener('click', function() {
            navMenu.classList.remove('active');
        });
    });

    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault()
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'

            });
        });


    // Product data - extracted from your HTML
    const products = [
        {
            id: 1,
            name: "Mixed flower bouquet",
            price: 599.99,
            type: "bouquet",
            category: "special",
            featured: true,
            image: "C:\\xampp\\htdocs\\web\\mixed_flowers.JPG"
        },
        {
            id: 2,
            name: "White Tulips 6 set stems tall",
            price: 149.99,
            type: "stems",
            category: "tulips",
            featured: false,
            image: "C:\\xampp\\htdocs\\web\\IMG_5613.JPG"
        },
        {
            id: 3,
            name: "Pink Tuplips Bouquet",
            price: 299.99,
            type: "bouquet",
            category: "tulips",
            featured: false,
            image: "C:\\xampp\\htdocs\\web\\pink_tulips.JPG"
        },
        {
            id: 4,
            name: "Red Roses single stem",
            price: 89.99,
            type: "stems",
            category: "roses",
            featured: false,
            image: "C:\\xampp\\htdocs\\web\\IMG_5617.JPG"
        },
        {
            id: 5,
            name: "Mixed Roses Bouquet",
            price: 299.99,
            type: "bouquet",
            category: "roses",
            featured: false,
            image: "C:\\xampp\\htdocs\\web\\mixed_roses_bouquet.JPG"
        },
        {
            id: 6,
            name: "Mixed Tuplips Bouquet",
            price: 299.99,
            type: "bouquet",
            category: "tulips",
            featured: false,
            image: "C:\\xampp\\htdocs\\web\\mixed_tulips_bouquet.JPG"
        }
    ];

    // DOM elements
    const productsContainer = document.querySelector('.products-container');
    const searchInput = document.getElementById('searchInput');
    const searchBtn = document.getElementById('searchBtn');
    const priceFilter = document.getElementById('priceFilter');
    const typeFilter = document.getElementById('typeFilter');
    const bottomNavItems = document.querySelectorAll('.bottom-nav-item');
    const navMenu = document.querySelector('.nav-menu');
    const hamburger = document.querySelector('.hamburger');
    displayProducts(products);

        // Mobile menu toggle
        hamburger.addEventListener('click', () => {
            navMenu.classList.toggle('active');
        });

        // Close mobile menu when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.navbar') && navMenu.classList.contains('active')) {
                navMenu.classList.remove('active');
            }
        });

        // Bottom nav item selection
        bottomNavItems.forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                bottomNavItems.forEach(i => i.classList.remove('active'));
                this.classList.add('active');
            });
        });

// Search functionality
        searchBtn.addEventListener('click', filterProducts);
        searchInput.addEventListener('keyup', filterProducts);

        // Filter functionality
        priceFilter.addEventListener('change', filterProducts);
        typeFilter.addEventListener('change', filterProducts);

        // Function to filter products based on search and filters
        function filterProducts() {
            const searchText = searchInput.value.toLowerCase();
            const priceValue = priceFilter.value;
            const typeValue = typeFilter.value;
            
            let filteredProducts = products.filter(product => {
                // Search filter
                const matchesSearch = product.name.toLowerCase().includes(searchText);
                
                // Price filter
                let matchesPrice = true;
                if (priceValue) {
                    if (priceValue === "0-100") {
                        matchesPrice = product.price <= 100;
                    } else if (priceValue === "100-200") {
                        matchesPrice = product.price > 100 && product.price <= 200;
                    } else if (priceValue === "200-500") {
                        matchesPrice = product.price > 200 && product.price <= 500;
                    } else if (priceValue === "500") {
                        matchesPrice = product.price > 500;
                    }
                }
                
                // Type filter
                const matchesType = typeValue ? product.type === typeValue : true;
                
                return matchesSearch && matchesPrice && matchesType;
            });
            
            displayProducts(filteredProducts);
        }

        // Function to display products
        function displayProducts(productsToDisplay) {
            productsContainer.innerHTML = '';
            
            if (productsToDisplay.length === 0) {
                productsContainer.innerHTML = '<div class="no-results">No products found matching your criteria.</div>';
                return;
            }
            
            productsToDisplay.forEach(product => {
                const productCard = document.createElement('div');
                productCard.className = 'product-card';
                
                if (product.tags.includes('special')) {
                    productCard.innerHTML = `
                        <div class="product-tag">Special</div>
                        <img src="${product.image}" alt="${product.name}">
                        <div class="product-info">
                            <h3>${product.name}</h3>
                            <p class="price">R ${product.price.toFixed(2)}</p>
                            <button class="shop-btn">Add to cart</button>
                        </div>
                    `;
                } else {
                    productCard.innerHTML = `
                        <img src="${product.image}" alt="${product.name}">
                        <div class="product-info">
                            <h3>${product.name}</h3>
                            <p class="price">R ${product.price.toFixed(2)}</p>
                            <button class="shop-btn">Add to cart</button>
                        </div>
                    `;
                }
                
                productsContainer.appendChild(productCard);
            });
        }


    // Initialize by displaying all products
    displayProducts(products);

    // Event listeners
    searchBtn.addEventListener('click', filterProducts);
    searchInput.addEventListener('keyup', function(e) {
        if (e.key === 'Enter') {
            filterProducts();
        }
    });
    priceFilter.addEventListener('change', filterProducts);
    typeFilter.addEventListener('change', filterProducts);

    // Main filtering function
    function filterProducts() {
        const searchTerm = searchInput.value.toLowerCase();
        const priceRange = priceFilter.value;
        const typeValue = typeFilter.value;

        const filteredProducts = products.filter(product => {
            // Search term matching
            const matchesSearch = product.name.toLowerCase().includes(searchTerm);
            
            // Price range matching
            let matchesPrice = true;
            if (priceRange) {
                const [min, max] = priceRange.split('-').map(Number);
                if (max) {
                    matchesPrice = product.price >= min && product.price <= max;
                } else {
                    matchesPrice = product.price >= min;
                }
            }
            
            // Type/category matching
            const matchesType = !typeValue || 
                              (typeValue === 'tulips' && product.category === 'tulips') ||
                              (typeValue === 'roses' && product.category === 'roses') ||
                              (typeValue === 'bouquet' && product.type === 'bouquet') ||
                              (typeValue === 'special' && product.featured);

            return matchesSearch && matchesPrice && matchesType;
        });

        displayProducts(filteredProducts);
    }

    // Display products in the container
    function displayProducts(productsToDisplay) {
        productsContainer.innerHTML = '';

        if (productsToDisplay.length === 0) {
            productsContainer.innerHTML = `
                <div class="no-results">
                    <i class="fas fa-search"></i>
                    <p>No products found matching your criteria</p>
                </div>
            `;
            return;
        }

        productsToDisplay.forEach(product => {
            const productCard = document.createElement('div');
            productCard.className = product.featured ? 'product-card featured' : 'product-card';
            
            productCard.innerHTML = `
                ${product.featured ? '<div class="product-tag">Special</div>' : ''}
                <img src="${product.image}" alt="${product.name}">
                <div class="product-info">
                    <h3>${product.name}</h3>
                    <p class="price">R ${product.price.toFixed(2)}</p>
                    <button class="shop-btn">${product.featured ? 'Shop now' : 'Add to cart'}</button>
                </div>
            `;
            
            productsContainer.appendChild(productCard);
        });

        // Add event listeners to all shop buttons
        document.querySelectorAll('.shop-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                // Here you would add cart functionality
                alert('Product added to cart!');
            });
        });
    }

    // Hero section CTA button
    document.querySelector('.cta-button').addEventListener('click', function() {
        // Scroll to products section
        document.querySelector('.products-section').scrollIntoView({ 
            behavior: 'smooth' 
        });
    });
});
 // Global variables
        let allProducts = [];
        let currentProducts = [];

        // Load products when page loads
        document.addEventListener('DOMContentLoaded', function() {
            loadProducts();
            
            // Add event listeners for search and filter
            document.getElementById('searchBtn').addEventListener('click', filterProducts);
            document.getElementById('searchInput').addEventListener('keyup', function(event) {
                if (event.key === 'Enter') {
                    filterProducts();
                }
            });
            document.getElementById('priceFilter').addEventListener('change', filterProducts);
            document.getElementById('categoryFilter').addEventListener('change', filterProducts);
        });

        // Function to load products from database
        function loadProducts() {
            const loadingElement = document.getElementById('loading');
            const productsContainer = document.getElementById('productsContainer');
            
            loadingElement.style.display = 'block';
            productsContainer.innerHTML = '';
            
            // Create AJAX request to fetch products
            const xhr = new XMLHttpRequest();
            xhr.open('GET', 'get_products.php', true);
            
            xhr.onload = function() {
                loadingElement.style.display = 'none';
                
                if (this.status === 200) {
                    try {
                        const response = JSON.parse(this.responseText);
                        
                        if (response.success) {
                            allProducts = response.products;
                            currentProducts = allProducts;
                            displayProducts(allProducts);
                        } else {
                            productsContainer.innerHTML = '<div class="no-results"><p>Error loading products: ' + response.message + '</p></div>';
                        }
                    } catch (e) {
                        productsContainer.innerHTML = '<div class="no-results"><p>Error parsing product data</p></div>';
                    }
                } else {
                    productsContainer.innerHTML = '<div class="no-results"><p>Error loading products from server</p></div>';
                }
            };
            
            xhr.onerror = function() {
                loadingElement.style.display = 'none';
                productsContainer.innerHTML = '<div class="no-results"><p>Network error loading products</p></div>';
            };
            
            xhr.send();
        }

        // Function to display products
        function displayProducts(products) {
            const productsContainer = document.getElementById('productsContainer');
            
            if (products.length === 0) {
                productsContainer.innerHTML = '<div class="no-results"><p>No products found. Try different search criteria.</p></div>';
                return;
            }
            
            let productsHTML = '';
            
            products.forEach(product => {
                productsHTML += `
                <div class="product-card" data-price="${product.price}" data-category="${product.category ? product.category.toLowerCase() : ''}">
                    ${product.featured == 1 ? '<div class="product-tag">Special</div>' : ''}
                    <img src="${product.image_url}" alt="${product.name}">
                    <div class="product-info">
                        <h3>${product.name}</h3>
                        <p class="price">R ${parseFloat(product.price).toFixed(2)}</p>
                        <p class="category" style="display:none;">${product.category || ''}</p>
                        <button class="shop-btn">Add to cart</button>
                    </div>
                </div>`;
            });
            
            productsContainer.innerHTML = productsHTML;
        }

        // Function to filter products based on search and filter criteria
        function filterProducts() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const priceFilter = document.getElementById('priceFilter').value;
            const categoryFilter = document.getElementById('categoryFilter').value;
            
            let filteredProducts = allProducts;
            
            // Apply search filter
            if (searchTerm) {
                filteredProducts = filteredProducts.filter(product => 
                    product.name.toLowerCase().includes(searchTerm) || 
                    (product.description && product.description.toLowerCase().includes(searchTerm)) ||
                    (product.category && product.category.toLowerCase().includes(searchTerm))
                );
            }
            
            // Apply price filter
            if (priceFilter) {
                const [min, max] = priceFilter.split('-').map(Number);
                
                filteredProducts = filteredProducts.filter(product => {
                    const price = parseFloat(product.price);
                    
                    if (max) {
                        return price >= min && price <= max;
                    } else {
                        return price >= min;
                    }
                });
            }
            
            // Apply category filter
            if (categoryFilter) {
                filteredProducts = filteredProducts.filter(product => 
                    product.category && product.category.toLowerCase().includes(categoryFilter)
                );
            }
            
            currentProducts = filteredProducts;
            displayProducts(filteredProducts);
        }


    });
    



    



    