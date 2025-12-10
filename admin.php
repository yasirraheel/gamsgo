<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - DigiMarket</title>
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

    const StatCard = ({ title, value, icon, color, trend }) => (
      <div className="bg-gray-800 rounded-xl p-6 border border-gray-700 hover:border-gray-600 transition-all">
        <div className="flex items-center justify-between">
          <div>
            <p className="text-gray-400 text-sm mb-1">{title}</p>
            <h3 className="text-3xl font-bold">{value}</h3>
            {trend && (
              <p className={`text-sm mt-2 ${trend.positive ? 'text-green-400' : 'text-red-400'}`}>
                <i className={`fas fa-arrow-${trend.positive ? 'up' : 'down'} mr-1`}></i>
                {trend.value}
              </p>
            )}
          </div>
          <div className={`w-16 h-16 rounded-xl ${color} flex items-center justify-center`}>
            <i className={`fas ${icon} text-2xl text-white`}></i>
          </div>
        </div>
      </div>
    );

    const App = () => {
      const [sidebarOpen, setSidebarOpen] = useState(true);
      const [currentUser, setCurrentUser] = useState(null);
      const [stats, setStats] = useState({
        totalOrders: 0,
        pendingOrders: 0,
        totalRevenue: 0,
        totalProducts: 0,
        activeGateways: 0
      });
      const [recentOrders, setRecentOrders] = useState([]);
      const [loading, setLoading] = useState(true);

      useEffect(() => {
        checkAuth();
        loadDashboardData();
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

      const loadDashboardData = async () => {
        setLoading(true);
        try {
          const [ordersRes, productsRes, gatewaysRes] = await Promise.all([
            fetch('orders.php?action=list_all'),
            fetch('products.php?action=list'),
            fetch('payment_gateways.php?action=list')
          ]);

          const orders = await ordersRes.json();
          const products = await productsRes.json();
          const gateways = await gatewaysRes.json();

          if (Array.isArray(orders)) {
            const pending = orders.filter(o => o.status === 'pending').length;
            const revenue = orders
              .filter(o => o.status === 'approved')
              .reduce((sum, o) => sum + parseFloat(o.total_amount), 0);

            setStats({
              totalOrders: orders.length,
              pendingOrders: pending,
              totalRevenue: revenue,
              totalProducts: Array.isArray(products) ? products.length : 0,
              activeGateways: Array.isArray(gateways) ? gateways.filter(g => g.is_active == 1).length : 0
            });

            setRecentOrders(orders.slice(0, 5));
          }
        } catch (err) {
          console.error('Failed to load dashboard data', err);
        } finally {
          setLoading(false);
        }
      };

      const handleLogout = async () => {
        await fetch('auth.php?action=logout', { method: 'POST' });
        window.location.href = 'index.php';
      };

      const menuItems = [
        { name: 'Dashboard', icon: 'fa-chart-line', path: 'admin.php', active: true },
        { name: 'Orders', icon: 'fa-list-check', path: 'admin_orders.php', badge: stats.pendingOrders },
        { name: 'Products', icon: 'fa-box', path: 'index.php' },
        { name: 'Payment Gateways', icon: 'fa-credit-card', path: 'payment_gateways_admin.php' },
        { name: 'Settings', icon: 'fa-cog', path: 'settings_admin.php' },
      ];

      const getStatusColor = (status) => {
        switch (status) {
          case 'approved': return 'text-green-400 bg-green-400/10';
          case 'rejected': return 'text-red-400 bg-red-400/10';
          default: return 'text-yellow-400 bg-yellow-400/10';
        }
      };

      return (
        <div className="flex h-screen bg-gray-900">
          {/* Sidebar */}
          <aside className={`${sidebarOpen ? 'w-64' : 'w-20'} bg-gray-800 border-r border-gray-700 transition-all duration-300 flex flex-col`}>
            {/* Logo */}
            <div className="h-16 flex items-center justify-between px-4 border-b border-gray-700">
              {sidebarOpen ? (
                <>
                  <div className="flex items-center gap-3">
                    <div className="w-8 h-8 rounded bg-gradient-to-br from-primary to-secondary flex items-center justify-center">
                      <i className="fas fa-shield-halved text-white text-sm"></i>
                    </div>
                    <span className="font-bold text-lg">Admin Panel</span>
                  </div>
                  <button onClick={() => setSidebarOpen(false)} className="text-gray-400 hover:text-white">
                    <i className="fas fa-angles-left"></i>
                  </button>
                </>
              ) : (
                <button onClick={() => setSidebarOpen(true)} className="text-gray-400 hover:text-white mx-auto">
                  <i className="fas fa-angles-right"></i>
                </button>
              )}
            </div>

            {/* Navigation */}
            <nav className="flex-1 py-6 px-3 space-y-1 overflow-y-auto">
              {menuItems.map((item, idx) => (
                <a
                  key={idx}
                  href={item.path}
                  className={`flex items-center gap-3 px-3 py-3 rounded-lg transition-all ${
                    item.active 
                      ? 'bg-primary text-white shadow-lg shadow-primary/20' 
                      : 'text-gray-400 hover:bg-gray-700 hover:text-white'
                  }`}
                  title={!sidebarOpen ? item.name : ''}
                >
                  <i className={`fas ${item.icon} text-lg ${sidebarOpen ? '' : 'mx-auto'}`}></i>
                  {sidebarOpen && (
                    <>
                      <span className="flex-1">{item.name}</span>
                      {item.badge > 0 && (
                        <span className="bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">
                          {item.badge}
                        </span>
                      )}
                    </>
                  )}
                </a>
              ))}
            </nav>

            {/* User Profile */}
            <div className="border-t border-gray-700 p-4">
              <div className={`flex items-center gap-3 ${!sidebarOpen && 'justify-center'}`}>
                <div className="w-10 h-10 rounded-full bg-gradient-to-br from-primary to-secondary flex items-center justify-center text-white font-bold">
                  <i className="fas fa-user"></i>
                </div>
                {sidebarOpen && (
                  <div className="flex-1 min-w-0">
                    <p className="text-sm font-medium truncate">{currentUser?.email}</p>
                    <p className="text-xs text-gray-400">Administrator</p>
                  </div>
                )}
              </div>
              {sidebarOpen && (
                <button
                  onClick={handleLogout}
                  className="w-full mt-3 px-3 py-2 bg-gray-700 hover:bg-gray-600 rounded-lg text-sm transition-colors flex items-center justify-center gap-2"
                >
                  <i className="fas fa-sign-out-alt"></i>
                  <span>Logout</span>
                </button>
              )}
            </div>
          </aside>

          {/* Main Content */}
          <main className="flex-1 overflow-y-auto">
            {/* Header */}
            <header className="h-16 bg-gray-800 border-b border-gray-700 flex items-center justify-between px-6">
              <div>
                <h1 className="text-xl font-bold">Dashboard Overview</h1>
                <p className="text-sm text-gray-400">Welcome back, {currentUser?.email?.split('@')[0]}</p>
              </div>
              <div className="flex items-center gap-3">
                <a href="index.php" className="px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded-lg text-sm transition-colors">
                  <i className="fas fa-store mr-2"></i>
                  View Store
                </a>
              </div>
            </header>

            {/* Dashboard Content */}
            <div className="p-6">
              {loading ? (
                <div className="flex items-center justify-center h-64">
                  <i className="fas fa-spinner fa-spin text-4xl text-primary"></i>
                </div>
              ) : (
                <>
                  {/* Stats Grid */}
                  <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <StatCard
                      title="Total Orders"
                      value={stats.totalOrders}
                      icon="fa-shopping-cart"
                      color="bg-blue-500"
                    />
                    <StatCard
                      title="Pending Orders"
                      value={stats.pendingOrders}
                      icon="fa-clock"
                      color="bg-yellow-500"
                    />
                    <StatCard
                      title="Total Revenue"
                      value={`$${stats.totalRevenue.toFixed(2)}`}
                      icon="fa-dollar-sign"
                      color="bg-green-500"
                    />
                    <StatCard
                      title="Total Products"
                      value={stats.totalProducts}
                      icon="fa-box"
                      color="bg-purple-500"
                    />
                  </div>

                  {/* Quick Actions */}
                  <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <a href="admin_orders.php?filter=pending" className="bg-gray-800 border border-gray-700 rounded-xl p-6 hover:border-primary transition-all group">
                      <div className="flex items-center gap-4">
                        <div className="w-12 h-12 rounded-lg bg-yellow-500/10 flex items-center justify-center group-hover:bg-yellow-500/20 transition-all">
                          <i className="fas fa-clock text-2xl text-yellow-400"></i>
                        </div>
                        <div>
                          <h3 className="font-semibold mb-1">Review Orders</h3>
                          <p className="text-sm text-gray-400">{stats.pendingOrders} pending approval</p>
                        </div>
                      </div>
                    </a>

                    <a href="index.php" className="bg-gray-800 border border-gray-700 rounded-xl p-6 hover:border-primary transition-all group">
                      <div className="flex items-center gap-4">
                        <div className="w-12 h-12 rounded-lg bg-blue-500/10 flex items-center justify-center group-hover:bg-blue-500/20 transition-all">
                          <i className="fas fa-plus text-2xl text-blue-400"></i>
                        </div>
                        <div>
                          <h3 className="font-semibold mb-1">Add Product</h3>
                          <p className="text-sm text-gray-400">Create new listing</p>
                        </div>
                      </div>
                    </a>

                    <a href="payment_gateways_admin.php" className="bg-gray-800 border border-gray-700 rounded-xl p-6 hover:border-primary transition-all group">
                      <div className="flex items-center gap-4">
                        <div className="w-12 h-12 rounded-lg bg-green-500/10 flex items-center justify-center group-hover:bg-green-500/20 transition-all">
                          <i className="fas fa-credit-card text-2xl text-green-400"></i>
                        </div>
                        <div>
                          <h3 className="font-semibold mb-1">Payment Gateways</h3>
                          <p className="text-sm text-gray-400">{stats.activeGateways} active</p>
                        </div>
                      </div>
                    </a>
                  </div>

                  {/* Recent Orders */}
                  <div className="bg-gray-800 rounded-xl border border-gray-700">
                    <div className="p-6 border-b border-gray-700 flex items-center justify-between">
                      <h2 className="text-xl font-bold">Recent Orders</h2>
                      <a href="admin_orders.php" className="text-primary hover:text-blue-400 text-sm font-medium">
                        View All <i className="fas fa-arrow-right ml-1"></i>
                      </a>
                    </div>
                    <div className="overflow-x-auto">
                      <table className="w-full">
                        <thead className="bg-gray-700/50">
                          <tr>
                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Order ID</th>
                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Customer</th>
                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Total</th>
                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Status</th>
                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Date</th>
                          </tr>
                        </thead>
                        <tbody className="divide-y divide-gray-700">
                          {recentOrders.length === 0 ? (
                            <tr>
                              <td colSpan="5" className="px-6 py-8 text-center text-gray-400">
                                No orders yet
                              </td>
                            </tr>
                          ) : (
                            recentOrders.map((order) => (
                              <tr key={order.id} className="hover:bg-gray-750">
                                <td className="px-6 py-4 font-medium">#{order.id}</td>
                                <td className="px-6 py-4 text-sm text-gray-300">{order.user_email}</td>
                                <td className="px-6 py-4 font-semibold text-primary">${parseFloat(order.total_amount).toFixed(2)}</td>
                                <td className="px-6 py-4">
                                  <span className={`px-3 py-1 rounded-full text-xs font-medium ${getStatusColor(order.status)}`}>
                                    {order.status.charAt(0).toUpperCase() + order.status.slice(1)}
                                  </span>
                                </td>
                                <td className="px-6 py-4 text-sm text-gray-400">
                                  {new Date(order.created_at).toLocaleDateString()}
                                </td>
                              </tr>
                            ))
                          )}
                        </tbody>
                      </table>
                    </div>
                  </div>
                </>
              )}
            </div>
          </main>
        </div>
      );
    };

    ReactDOM.render(<App />, document.getElementById('root'));
  </script>
</body>
</html>
