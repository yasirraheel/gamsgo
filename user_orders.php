<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Orders - DigiMarket</title>
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
<body class="bg-gray-900 text-white min-h-screen">
  <div id="root"></div>

  <script src="https://unpkg.com/react@18/umd/react.production.min.js"></script>
  <script src="https://unpkg.com/react-dom@18/umd/react-dom.production.min.js"></script>
  <script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>

  <script type="text/babel" data-presets="env,react">
    const { useState, useEffect } = React;

    const Toast = ({ message, type, onClose }) => (
      <div className={`fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 ${type === 'success' ? 'bg-green-600' : 'bg-red-600'} text-white`}>
        <div className="flex items-center gap-2">
          <i className={`fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}`}></i>
          <span>{message}</span>
          <button onClick={onClose} className="ml-4 text-white hover:text-gray-200">
            <i className="fas fa-times"></i>
          </button>
        </div>
      </div>
    );

    const OrderDetailsModal = ({ order, onClose, settings }) => {
      if (!order) return null;

      const currency = settings?.currency || '$';
      
      const getStatusColor = (status) => {
        switch (status) {
          case 'approved': return 'bg-green-600';
          case 'rejected': return 'bg-red-600';
          default: return 'bg-yellow-600';
        }
      };

      const getStatusIcon = (status) => {
        switch (status) {
          case 'approved': return 'fa-check-circle';
          case 'rejected': return 'fa-times-circle';
          default: return 'fa-clock';
        }
      };

      return (
        <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
          <div className="bg-gray-800 rounded-lg max-w-3xl w-full max-h-[90vh] overflow-y-auto">
            <div className="p-6 border-b border-gray-700 flex justify-between items-center sticky top-0 bg-gray-800">
              <h2 className="text-2xl font-bold">Order #{order.id}</h2>
              <button onClick={onClose} className="text-gray-400 hover:text-white">
                <i className="fas fa-times text-xl"></i>
              </button>
            </div>

            <div className="p-6 space-y-6">
              {/* Status */}
              <div className="flex items-center gap-4">
                <div className={`${getStatusColor(order.status)} px-4 py-2 rounded-lg font-medium flex items-center gap-2`}>
                  <i className={`fas ${getStatusIcon(order.status)}`}></i>
                  {order.status.charAt(0).toUpperCase() + order.status.slice(1)}
                </div>
                {order.expiry_date && order.status === 'approved' && (
                  <div className="text-gray-400">
                    <i className="fas fa-calendar-alt mr-2"></i>
                    Expires: {new Date(order.expiry_date).toLocaleDateString()}
                  </div>
                )}
              </div>

              {/* Products */}
              <div>
                <h3 className="text-lg font-bold mb-3">Products</h3>
                <div className="space-y-2">
                  {order.products.map((item, idx) => (
                    <div key={idx} className="flex justify-between items-center bg-gray-700 p-3 rounded-lg">
                      <div>
                        <div className="font-medium">{item.name}</div>
                        <div className="text-sm text-gray-400">Qty: {item.quantity}</div>
                      </div>
                      <div className="font-semibold">{currency}{(item.price * item.quantity).toFixed(2)}</div>
                    </div>
                  ))}
                </div>
              </div>

              {/* Payment Info */}
              <div>
                <h3 className="text-lg font-bold mb-3">Payment Information</h3>
                <div className="bg-gray-700 p-4 rounded-lg space-y-2">
                  <div className="flex justify-between">
                    <span className="text-gray-400">Method:</span>
                    <span className="font-medium">{order.payment_gateway_name}</span>
                  </div>
                  <div className="flex justify-between text-xl font-bold border-t border-gray-600 pt-2">
                    <span>Total:</span>
                    <span className="text-primary">{currency}{parseFloat(order.total_amount).toFixed(2)}</span>
                  </div>
                </div>
              </div>

              {/* Shipping Address */}
              <div>
                <h3 className="text-lg font-bold mb-3">Shipping Address</h3>
                <div className="bg-gray-700 p-4 rounded-lg">
                  <p>{order.city}, {order.country}</p>
                  <p className="text-gray-400">Postal Code: {order.postal_code}</p>
                </div>
              </div>

              {/* Admin Notes */}
              {order.admin_notes && (
                <div>
                  <h3 className="text-lg font-bold mb-3">Admin Notes</h3>
                  <div className="bg-gray-700 p-4 rounded-lg text-gray-300">
                    {order.admin_notes}
                  </div>
                </div>
              )}

              {/* Order Date */}
              <div className="text-sm text-gray-400">
                <i className="fas fa-clock mr-2"></i>
                Order placed: {new Date(order.created_at).toLocaleString()}
              </div>

              {/* WhatsApp Contact */}
              {settings?.whatsapp_number && (
                <a 
                  href={`https://wa.me/${settings.whatsapp_number.replace(/[^0-9]/g, '')}?text=Hi, I need help with Order #${order.id}`}
                  target="_blank"
                  rel="noopener noreferrer"
                  className="block w-full bg-green-600 hover:bg-green-700 text-white py-3 rounded-lg font-medium transition-colors text-center"
                >
                  <i className="fa-brands fa-whatsapp text-xl mr-2"></i>
                  Contact Support for Fast Delivery
                </a>
              )}
            </div>
          </div>
        </div>
      );
    };

    const App = () => {
      const [orders, setOrders] = useState([]);
      const [loading, setLoading] = useState(true);
      const [selectedOrder, setSelectedOrder] = useState(null);
      const [toast, setToast] = useState(null);
      const [settings, setSettings] = useState(null);
      const [isMenuOpen, setIsMenuOpen] = useState(false);
      const [currentUser, setCurrentUser] = useState(null);

      useEffect(() => {
        loadOrders();
        loadSettings();
        checkAuth();
      }, []);

      const loadOrders = async () => {
        setLoading(true);
        try {
          const res = await fetch('orders.php?action=list_user');
          const data = await res.json();
          
          if (data.error) {
            if (data.error === 'Unauthorized') {
              window.location.href = 'index.php';
            } else {
              showToast(data.error, 'error');
            }
          } else if (Array.isArray(data)) {
            setOrders(data);
          }
        } catch (err) {
          showToast('Failed to load orders', 'error');
        } finally {
          setLoading(false);
        }
      };

      const loadSettings = async () => {
        try {
          const res = await fetch('settings.php?action=get');
          const data = await res.json();
          if (data && !data.error) {
            setSettings(data);
          }
        } catch (err) {
          console.warn('Failed to load settings', err);
        }
      };

      const checkAuth = async () => {
        try {
          const res = await fetch('auth.php?action=check');
          const data = await res.json();
          if (data.authenticated) {
            setCurrentUser(data.user);
          }
        } catch (err) {
          console.warn('Failed to check auth', err);
        }
      };

      const handleLogout = async () => {
        try {
          await fetch('auth.php?action=logout', { method: 'POST' });
          window.location.href = 'index.php';
        } catch (err) {
          showToast('Failed to logout', 'error');
        }
      };

      const showToast = (message, type = 'success') => {
        setToast({ message, type });
        setTimeout(() => setToast(null), 3000);
      };

      const getStatusColor = (status) => {
        switch (status) {
          case 'approved': return 'bg-green-600 text-white';
          case 'rejected': return 'bg-red-600 text-white';
          default: return 'bg-yellow-600 text-white';
        }
      };

      const getStatusIcon = (status) => {
        switch (status) {
          case 'approved': return 'fa-check-circle';
          case 'rejected': return 'fa-times-circle';
          default: return 'fa-clock';
        }
      };

      const currency = settings?.currency || '$';
      const isAdmin = currentUser?.role === 'admin';

      return (
        <div className="min-h-screen bg-gray-900 pb-20">
          {/* Header */}
          <nav className="fixed top-0 w-full z-40 glass-panel border-b-0 border-b-white/5 bg-gray-800">
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
              <div className="flex items-center gap-2">
                <div className={`w-8 h-8 rounded bg-gradient-to-tr ${isAdmin ? 'from-red-500 to-orange-500' : 'from-primary to-secondary'} flex items-center justify-center text-white font-bold transition-all duration-500`}>
                  {isAdmin ? <i className="fa-solid fa-lock-open text-xs"></i> : 'D'}
                </div>
                <span className="font-bold text-xl tracking-tight text-white">{settings?.site_name || 'DigiMarket'}</span>
              </div>
              <button 
                onClick={() => setIsMenuOpen(!isMenuOpen)}
                className="p-2 text-gray-400 hover:text-white transition-colors"
                aria-label="Toggle menu"
              >
                <i className="fa-solid fa-bars text-xl"></i>
              </button>
            </div>
          </nav>

          {/* Sidebar Menu */}
          <div 
            className={`fixed inset-0 bg-black/50 z-50 transition-opacity duration-300 ${isMenuOpen ? 'opacity-100' : 'opacity-0 pointer-events-none'}`}
            onClick={() => setIsMenuOpen(false)}
          ></div>
          <div className={`fixed top-0 right-0 h-full w-80 bg-[#0f172a] border-l border-gray-800 z-50 transform transition-transform duration-300 ${isMenuOpen ? 'translate-x-0' : 'translate-x-full'} flex flex-col`}>
            <div className="p-4 border-b border-gray-800 flex items-center justify-between">
              <h2 className="text-xl font-bold text-white">Menu</h2>
              <button onClick={() => setIsMenuOpen(false)} className="text-gray-400 hover:text-white">
                <i className="fa-solid fa-times text-xl"></i>
              </button>
            </div>
            
            <div className="flex-1 overflow-y-auto p-4">
              {currentUser && (
                <div className="mb-6 p-4 bg-gray-800 rounded-lg border border-gray-700">
                  <div className="text-sm text-gray-400">Logged in as</div>
                  <div className="text-white font-semibold">{currentUser.email}</div>
                  <div className={`text-xs mt-1 ${isAdmin ? 'text-red-400' : 'text-gray-500'}`}>{isAdmin ? 'Admin' : 'User'}</div>
                </div>
              )}

              <div className="space-y-2">
                <button 
                  onClick={() => window.location.href = 'index.php'} 
                  className="w-full flex items-center gap-3 p-3 bg-gray-800 hover:bg-gray-700 rounded-lg transition-colors text-left"
                >
                  <i className="fa-solid fa-home text-primary"></i>
                  <span className="text-white">Home</span>
                </button>

                {currentUser && (
                  <button 
                    onClick={() => window.location.href = 'user_orders.php'} 
                    className="w-full flex items-center gap-3 p-3 bg-primary rounded-lg text-left"
                  >
                    <i className="fa-solid fa-receipt text-white"></i>
                    <span className="text-white font-semibold">My Orders</span>
                  </button>
                )}

                {isAdmin && (
                  <button 
                    onClick={() => window.location.href = 'admin.php'} 
                    className="w-full flex items-center gap-3 p-3 bg-gradient-to-r from-red-600 to-orange-600 hover:from-red-700 hover:to-orange-700 rounded-lg transition-colors text-left"
                  >
                    <i className="fa-solid fa-shield-halved"></i>
                    <span className="text-white">Admin Panel</span>
                  </button>
                )}
              </div>
            </div>

            <div className="p-4 border-t border-gray-800">
              {currentUser ? (
                <button 
                  onClick={handleLogout} 
                  className="w-full px-4 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors flex items-center justify-center gap-2"
                >
                  <i className="fa-solid fa-sign-out-alt"></i>
                  <span>Logout</span>
                </button>
              ) : (
                <button 
                  onClick={() => window.location.href = 'index.php'} 
                  className="w-full px-4 py-3 bg-primary hover:bg-blue-600 text-white rounded-lg transition-colors"
                >
                  Login
                </button>
              )}
            </div>
          </div>

          {/* Content */}
          <div className="pt-20 px-4">
            <div className="max-w-7xl mx-auto">
              <div className="mb-8">
                <h1 className="text-3xl font-bold mb-2">My Orders</h1>
                <p className="text-gray-400">Track your purchases and subscriptions</p>
              </div>

              {loading ? (
                <div className="text-center py-12">
                  <i className="fas fa-spinner fa-spin text-4xl text-primary"></i>
                </div>
              ) : orders.length === 0 ? (
                <div className="bg-gray-800 rounded-lg p-12 text-center">
                  <i className="fas fa-shopping-bag text-6xl text-gray-700 mb-4"></i>
                  <h2 className="text-2xl font-bold mb-2">No Orders Yet</h2>
                  <p className="text-gray-400 mb-6">Start shopping to see your orders here</p>
                  <a href="index.php" className="inline-block bg-primary hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition-colors">
                    <i className="fas fa-shopping-cart mr-2"></i>Start Shopping
                  </a>
                </div>
              ) : (
                <div className="space-y-4">
                  {orders.map((order) => (
                    <div key={order.id} className="bg-gray-800 rounded-lg p-6 hover:bg-gray-750 transition-colors">
                      <div className="flex flex-wrap justify-between items-start gap-4 mb-4">
                        <div>
                          <div className="flex items-center gap-3 mb-2">
                            <h3 className="text-xl font-bold">Order #{order.id}</h3>
                            <span className={`px-3 py-1 rounded-full text-sm font-medium ${getStatusColor(order.status)} flex items-center gap-1`}>
                              <i className={`fas ${getStatusIcon(order.status)}`}></i>
                              {order.status.charAt(0).toUpperCase() + order.status.slice(1)}
                            </span>
                          </div>
                          <div className="text-sm text-gray-400">
                            <i className="fas fa-clock mr-2"></i>
                            {new Date(order.created_at).toLocaleDateString()} at {new Date(order.created_at).toLocaleTimeString()}
                          </div>
                        </div>
                        <div className="text-right">
                          <div className="text-2xl font-bold text-primary">{currency}{parseFloat(order.total_amount).toFixed(2)}</div>
                          <div className="text-sm text-gray-400">{order.payment_gateway_name}</div>
                        </div>
                      </div>

                      <div className="flex flex-wrap gap-2 mb-4">
                        {order.products.map((item, idx) => (
                          <span key={idx} className="bg-gray-700 px-3 py-1 rounded-full text-sm">
                            {item.name} x{item.quantity}
                          </span>
                        ))}
                      </div>

                      {order.expiry_date && order.status === 'approved' && (
                        <div className="bg-green-900/30 border border-green-700 rounded-lg p-3 mb-4">
                          <i className="fas fa-calendar-check mr-2 text-green-400"></i>
                          <span className="text-green-300">Subscription expires on: {new Date(order.expiry_date).toLocaleDateString()}</span>
                        </div>
                      )}

                      <div className="flex gap-3">
                        <button
                          onClick={() => setSelectedOrder(order)}
                          className="flex-1 bg-primary hover:bg-blue-600 text-white py-2 px-4 rounded-lg transition-colors"
                        >
                          <i className="fas fa-eye mr-2"></i>View Details
                        </button>
                        {settings?.whatsapp_number && (
                          <a
                            href={`https://wa.me/${settings.whatsapp_number.replace(/[^0-9]/g, '')}?text=Hi, I need help with Order #${order.id}`}
                            target="_blank"
                            rel="noopener noreferrer"
                            className="bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg transition-colors"
                          >
                            <i className="fa-brands fa-whatsapp mr-2"></i>Contact Support
                          </a>
                        )}
                      </div>
                    </div>
                  ))}
                </div>
              )}
            </div>
          </div>

          {selectedOrder && (
            <OrderDetailsModal 
              order={selectedOrder} 
              onClose={() => setSelectedOrder(null)}
              settings={settings}
            />
          )}

          {toast && <Toast message={toast.message} type={toast.type} onClose={() => setToast(null)} />}
        </div>
      );
    };

    ReactDOM.render(<App />, document.getElementById('root'));
  </script>
</body>
</html>
