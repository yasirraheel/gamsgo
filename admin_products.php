<?php
// Check auth on server side
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Products - Admin Panel</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: '#3b82f6',
            secondary: '#8b5cf6',
          }
        }
      }
    }
  </script>
</head>
<body class="bg-gray-900 text-white">
  <div id="root"></div>

  <script src="https://unpkg.com/react@18/umd/react.production.min.js"></script>
  <script src="https://unpkg.com/react-dom@18/umd/react-dom.production.min.js"></script>
  <script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>

  <script type="text/babel" data-presets="env,react">
    const { useState, useEffect } = React;

    <?php include 'admin_layout_component.php'; ?>

    // Products View Component
    const ProductsView = () => {
      const [products, setProducts] = useState([]);
      const [loading, setLoading] = useState(true);

      useEffect(() => {
        loadProducts();
      }, []);

      const loadProducts = async () => {
        setLoading(true);
        try {
          const res = await fetch('products.php?action=list');
          const data = await res.json();
          if (Array.isArray(data)) {
            setProducts(data);
          }
        } catch (err) {
          console.error('Failed to load products', err);
        } finally {
          setLoading(false);
        }
      };

      return (
        <>
          <header className="h-16 bg-gray-800 border-b border-gray-700 flex items-center justify-between px-6 flex-shrink-0">
            <div>
              <h1 className="text-xl font-bold">Products Management</h1>
              <p className="text-sm text-gray-400">Manage your product catalog</p>
            </div>
            <a href="index.php?stay" target="_blank" className="px-4 py-2 bg-primary hover:bg-blue-600 rounded-lg text-sm transition-colors">
              <i className="fas fa-plus mr-2"></i>
              Add New Product
            </a>
          </header>

          <div className="p-6 flex-1 overflow-y-auto">
            {loading ? (
              <div className="flex items-center justify-center h-64">
                <i className="fas fa-spinner fa-spin text-4xl text-primary"></i>
              </div>
            ) : products.length === 0 ? (
              <div className="bg-gray-800 rounded-lg p-12 text-center">
                <i className="fas fa-box-open text-6xl text-gray-700 mb-4"></i>
                <h2 className="text-2xl font-bold mb-2">No Products Found</h2>
                <p className="text-gray-400 mb-6">Start by adding your first product</p>
                <a href="index.php?stay" target="_blank" className="inline-block bg-primary hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition-colors">
                  <i className="fas fa-plus mr-2"></i>
                  Add Product
                </a>
              </div>
            ) : (
              <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {products.map((product) => (
                  <div key={product.id} className="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden hover:border-primary transition-all">
                    <div className="p-6">
                      <div className="flex items-start justify-between mb-4">
                        <div className="w-12 h-12 rounded-lg bg-gradient-to-br from-primary to-secondary flex items-center justify-center text-2xl">
                          {product.icon || '📦'}
                        </div>
                        <div className="flex gap-2">
                          {product.is_hot == 1 && (
                            <span className="bg-red-500 text-white text-xs px-2 py-1 rounded-full">
                              <i className="fas fa-fire mr-1"></i>Hot
                            </span>
                          )}
                          <span className={`text-xs px-2 py-1 rounded-full ${product.is_visible == 1 ? 'bg-green-500/20 text-green-400' : 'bg-gray-700 text-gray-400'}`}>
                            {product.is_visible == 1 ? 'Visible' : 'Hidden'}
                          </span>
                        </div>
                      </div>
                      
                      <h3 className="text-lg font-bold mb-2">{product.name}</h3>
                      <p className="text-sm text-gray-400 mb-3">{product.service_type} - {product.account_type}</p>
                      
                      {product.description && (
                        <p className="text-sm text-gray-300 mb-4 line-clamp-2">{product.description}</p>
                      )}

                      <div className="flex items-center justify-between pt-4 border-t border-gray-700">
                        <div>
                          <div className="text-sm text-gray-400">Stock</div>
                          <div className="font-semibold">{product.stock || 0} available</div>
                        </div>
                        <div className="text-right">
                          <div className="text-sm text-gray-400">Price</div>
                          <div className="font-bold text-primary">
                            {product.discountedPrice ? (
                              <>
                                <span className="line-through text-gray-500 text-sm mr-2">${product.prices?.[0]?.price || 0}</span>
                                ${product.discountedPrice}
                              </>
                            ) : (
                              `$${product.prices?.[0]?.price || 0}`
                            )}
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            )}
          </div>
        </>
      );
    };

    // Main App Component
    const App = () => {
      const [currentUser, setCurrentUser] = useState(null);
      const [stats, setStats] = useState({ pendingOrders: 0 });

      useEffect(() => {
        checkAuth();
        loadStats();
      }, []);

      const checkAuth = async () => {
        try {
          const res = await fetch('auth.php?action=me');
          const data = await res.json();
          if (data.email && data.role === 'admin') {
            setCurrentUser(data);
          } else {
            window.location.href = 'index.php';
          }
        } catch (err) {
          window.location.href = 'index.php';
        }
      };

      const loadStats = async () => {
        try {
          const res = await fetch('orders.php?action=list_all');
          const orders = await res.json();
          if (Array.isArray(orders)) {
            const pending = orders.filter(o => o.status === 'pending').length;
            setStats({ pendingOrders: pending });
          }
        } catch (err) {
          console.error('Failed to load stats', err);
        }
      };

      const menuItems = [
        { name: 'Dashboard', icon: 'fa-chart-line', href: 'admin.php' },
        { name: 'Orders', icon: 'fa-list-check', href: 'admin_orders.php', badge: stats.pendingOrders },
        { name: 'Products', icon: 'fa-box', href: 'admin_products.php' },
        { name: 'Payment Gateways', icon: 'fa-credit-card', href: 'admin_gateways.php' },
        { name: 'Settings', icon: 'fa-cog', href: 'admin_settings.php' },
      ];

      if (!currentUser) {
        return (
          <div className="flex items-center justify-center h-screen bg-gray-900">
            <i className="fas fa-spinner fa-spin text-4xl text-primary"></i>
          </div>
        );
      }

      return (
        <AdminLayout 
          currentPage="admin_products.php"
          currentUser={currentUser}
          stats={stats}
          menuItems={menuItems}
        >
          <ProductsView />
        </AdminLayout>
      );
    };

    ReactDOM.render(<App />, document.getElementById('root'));
  </script>
</body>
</html>
