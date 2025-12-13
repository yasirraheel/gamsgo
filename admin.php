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

    // StatCard Component
    const StatCard = ({ title, value, icon, color, trend }) => (
      <div className="bg-gray-800 rounded-lg sm:rounded-xl p-4 sm:p-6 border border-gray-700 hover:border-gray-600 transition-all">
        <div className="flex items-center justify-between">
          <div className="flex-1 min-w-0">
            <p className="text-gray-400 text-xs sm:text-sm mb-1 truncate">{title}</p>
            <h3 className="text-xl sm:text-3xl font-bold truncate">{value}</h3>
            {trend && (
              <p className={`text-xs sm:text-sm mt-1 sm:mt-2 ${trend.positive ? 'text-green-400' : 'text-red-400'}`}>
                <i className={`fas fa-arrow-${trend.positive ? 'up' : 'down'} mr-1`}></i>
                {trend.value}
              </p>
            )}
          </div>
          <div className={`w-12 h-12 sm:w-16 sm:h-16 rounded-lg sm:rounded-xl ${color} flex items-center justify-center flex-shrink-0 ml-3`}>
            <i className={`fas ${icon} text-lg sm:text-2xl text-white`}></i>
          </div>
        </div>
      </div>
    );

    <?php include 'admin_layout_component.php'; ?>

    // Dashboard View
    const DashboardView = ({ stats, recentOrders, loading, currentUser }) => {
      const getStatusColor = (status) => {
        switch (status) {
          case 'approved': return 'text-green-400 bg-green-400/10';
          case 'rejected': return 'text-red-400 bg-red-400/10';
          default: return 'text-yellow-400 bg-yellow-400/10';
        }
      };

      return (
        <>
          {/* Header */}
          <header className="bg-gray-800 border-b border-gray-700 px-4 sm:px-6 py-3 sm:py-4 flex-shrink-0">
            <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 sm:gap-3">
              <div className="min-w-0">
                <h1 className="text-base sm:text-lg lg:text-xl font-bold truncate">Dashboard Overview</h1>
                <p className="text-xs sm:text-sm text-gray-400 truncate">Welcome back, {currentUser?.email?.split('@')[0]}</p>
              </div>
              <a href="index.php?stay" className="inline-flex items-center justify-center px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded-lg text-sm transition-colors w-full sm:w-auto">
                <i className="fas fa-store mr-2"></i>
                View Store
              </a>
            </div>
          </header>

          {/* Dashboard Content */}
          <div className="p-4 sm:p-6 flex-1 overflow-y-auto">
            {loading ? (
              <div className="flex items-center justify-center h-64">
                <i className="fas fa-spinner fa-spin text-4xl text-primary"></i>
              </div>
            ) : (
              <>
                {/* Stats Grid */}
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 lg:gap-6 mb-6 sm:mb-8">
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

                {/* Recent Orders */}
                <div className="bg-gray-800 rounded-xl border border-gray-700">
                  <div className="p-4 sm:p-6 border-b border-gray-700 flex items-center justify-between">
                    <h2 className="text-lg sm:text-xl font-bold">Recent Orders</h2>
                  </div>
                  
                  {recentOrders.length === 0 ? (
                    <div className="p-8 sm:p-12 text-center">
                      <i className="fas fa-inbox text-4xl sm:text-6xl text-gray-700 mb-4"></i>
                      <p className="text-gray-400">No orders yet</p>
                    </div>
                  ) : (
                    <>
                      {/* Mobile Card View */}
                      <div className="lg:hidden divide-y divide-gray-700">
                        {recentOrders.map((order) => (
                          <div key={order.id} className="p-4 hover:bg-gray-750 transition-colors">
                            <div className="flex justify-between items-start mb-2">
                              <div>
                                <div className="font-bold text-white">#{order.id}</div>
                                <div className="text-sm text-gray-400 mt-1">{order.user_email}</div>
                              </div>
                              <span className={`px-3 py-1 rounded-full text-xs font-medium ${getStatusColor(order.status)}`}>
                                {order.status.charAt(0).toUpperCase() + order.status.slice(1)}
                              </span>
                            </div>
                            <div className="flex justify-between items-center mt-3 pt-3 border-t border-gray-700">
                              <div className="font-bold text-primary text-lg">
                                ${parseFloat(order.total_amount).toFixed(2)}
                              </div>
                              <div className="text-xs text-gray-400">
                                {new Date(order.created_at).toLocaleDateString()}
                              </div>
                            </div>
                          </div>
                        ))}
                      </div>

                      {/* Desktop Table View */}
                      <div className="hidden lg:block overflow-x-auto">
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
                            {recentOrders.map((order) => (
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
                            ))}
                          </tbody>
                        </table>
                      </div>
                    </>
                  )}
                </div>
              </>
            )}
          </div>
        </>
      );
    };

    // Orders View Component
    const OrdersView = () => {
      const [orders, setOrders] = useState([]);
      const [loading, setLoading] = useState(true);
      const [statusFilter, setStatusFilter] = useState('all');
      const [selectedOrder, setSelectedOrder] = useState(null);
      const [approveOrder, setApproveOrder] = useState(null);
      const [rejectOrder, setRejectOrder] = useState(null);
      const [toast, setToast] = useState(null);
      const [settings, setSettings] = useState(null);

      useEffect(() => {
        loadOrders();
        loadSettings();
      }, [statusFilter]);

      const loadOrders = async () => {
        setLoading(true);
        try {
          const res = await fetch(`orders.php?action=list_all&status=${statusFilter}`);
          const data = await res.json();
          
          if (data.error) {
            showToast(data.error, 'error');
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

      const showToast = (message, type = 'success') => {
        setToast({ message, type });
        setTimeout(() => setToast(null), 3000);
      };

      const handleApprove = async (orderId, expiryDate, adminNotes) => {
        try {
          const res = await fetch('orders.php?action=approve', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: orderId, expiry_date: expiryDate, admin_notes: adminNotes })
          });
          
          const data = await res.json();
          if (data.success) {
            showToast('Order approved successfully');
            setApproveOrder(null);
            loadOrders();
          } else {
            showToast(data.error || 'Failed to approve order', 'error');
          }
        } catch (err) {
          showToast('Failed to approve order', 'error');
        }
      };

      const handleReject = async (orderId, adminNotes) => {
        try {
          const res = await fetch('orders.php?action=reject', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: orderId, admin_notes: adminNotes })
          });
          
          const data = await res.json();
          if (data.success) {
            showToast('Order rejected');
            setRejectOrder(null);
            loadOrders();
          } else {
            showToast(data.error || 'Failed to reject order', 'error');
          }
        } catch (err) {
          showToast('Failed to reject order', 'error');
        }
      };

      const handleDelete = async (orderId) => {
        if (!confirm('Are you sure you want to delete this order?')) return;
        
        try {
          const res = await fetch('orders.php?action=delete', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: orderId })
          });
          
          const data = await res.json();
          if (data.success) {
            showToast('Order deleted');
            loadOrders();
          } else {
            showToast(data.error || 'Failed to delete order', 'error');
          }
        } catch (err) {
          showToast('Failed to delete order', 'error');
        }
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
      const pendingCount = orders.filter(o => o.status === 'pending').length;

      // Modals
      const ApproveModal = ({ order, onClose, onApprove }) => {
        const [expiryDate, setExpiryDate] = useState('');
        const [adminNotes, setAdminNotes] = useState('');

        const handleSubmit = (e) => {
          e.preventDefault();
          onApprove(order.id, expiryDate, adminNotes);
        };

        return (
          <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
            <div className="bg-gray-800 rounded-lg max-w-md w-full">
              <div className="p-6 border-b border-gray-700 flex justify-between items-center">
                <h2 className="text-2xl font-bold">Approve Order #{order.id}</h2>
                <button onClick={onClose} className="text-gray-400 hover:text-white">
                  <i className="fas fa-times text-xl"></i>
                </button>
              </div>
              <form onSubmit={handleSubmit} className="p-6 space-y-4">
                <div>
                  <label className="block text-sm font-medium mb-2">Expiry Date *</label>
                  <input
                    type="date"
                    required
                    value={expiryDate}
                    onChange={(e) => setExpiryDate(e.target.value)}
                    min={new Date().toISOString().split('T')[0]}
                    className="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-primary text-white"
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium mb-2">Admin Notes (Optional)</label>
                  <textarea
                    value={adminNotes}
                    onChange={(e) => setAdminNotes(e.target.value)}
                    className="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-primary text-white"
                    rows="3"
                    placeholder="Add notes for the customer"
                  ></textarea>
                </div>
                <div className="flex gap-3 pt-4">
                  <button type="submit" className="flex-1 bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg transition-colors">
                    <i className="fas fa-check mr-2"></i>
                    Approve Order
                  </button>
                  <button type="button" onClick={onClose} className="px-6 bg-gray-700 hover:bg-gray-600 text-white py-2 rounded-lg transition-colors">
                    Cancel
                  </button>
                </div>
              </form>
            </div>
          </div>
        );
      };

      const RejectModal = ({ order, onClose, onReject }) => {
        const [adminNotes, setAdminNotes] = useState('');

        const handleSubmit = (e) => {
          e.preventDefault();
          onReject(order.id, adminNotes);
        };

        return (
          <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
            <div className="bg-gray-800 rounded-lg max-w-md w-full">
              <div className="p-6 border-b border-gray-700 flex justify-between items-center">
                <h2 className="text-2xl font-bold">Reject Order #{order.id}</h2>
                <button onClick={onClose} className="text-gray-400 hover:text-white">
                  <i className="fas fa-times text-xl"></i>
                </button>
              </div>
              <form onSubmit={handleSubmit} className="p-6 space-y-4">
                <div>
                  <label className="block text-sm font-medium mb-2">Reason for Rejection (Optional)</label>
                  <textarea
                    value={adminNotes}
                    onChange={(e) => setAdminNotes(e.target.value)}
                    className="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-primary text-white"
                    rows="3"
                    placeholder="Explain why the order is being rejected"
                  ></textarea>
                </div>
                <div className="flex gap-3 pt-4">
                  <button type="submit" className="flex-1 bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-lg transition-colors">
                    <i className="fas fa-times mr-2"></i>
                    Reject Order
                  </button>
                  <button type="button" onClick={onClose} className="px-6 bg-gray-700 hover:bg-gray-600 text-white py-2 rounded-lg transition-colors">
                    Cancel
                  </button>
                </div>
              </form>
            </div>
          </div>
        );
      };

      const OrderDetailsModal = ({ order, onClose, settings }) => {
        if (!order) return null;
        const currency = settings?.currency || '$';

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
                <div>
                  <h3 className="text-lg font-bold mb-3">Customer</h3>
                  <div className="bg-gray-700 p-4 rounded-lg">
                    <p className="font-medium">{order.user_email}</p>
                  </div>
                </div>
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
                <div>
                  <h3 className="text-lg font-bold mb-3">Shipping Address</h3>
                  <div className="bg-gray-700 p-4 rounded-lg">
                    <p>{order.city}, {order.country}</p>
                    <p className="text-gray-400">Postal Code: {order.postal_code}</p>
                  </div>
                </div>
                {order.expiry_date && (
                  <div>
                    <h3 className="text-lg font-bold mb-3">Subscription Expiry</h3>
                    <div className="bg-gray-700 p-4 rounded-lg">
                      {new Date(order.expiry_date).toLocaleDateString()}
                    </div>
                  </div>
                )}
                {order.admin_notes && (
                  <div>
                    <h3 className="text-lg font-bold mb-3">Admin Notes</h3>
                    <div className="bg-gray-700 p-4 rounded-lg text-gray-300">
                      {order.admin_notes}
                    </div>
                  </div>
                )}
                <div className="text-sm text-gray-400">
                  <i className="fas fa-clock mr-2"></i>
                  Order placed: {new Date(order.created_at).toLocaleString()}
                </div>
              </div>
            </div>
          </div>
        );
      };

      return (
        <>
          {/* Header */}
          <header className="h-16 bg-gray-800 border-b border-gray-700 flex items-center justify-between px-6 flex-shrink-0">
            <div>
              <h1 className="text-xl font-bold">Orders Management</h1>
              <p className="text-sm text-gray-400">Manage customer orders and approvals</p>
            </div>
          </header>

          {/* Orders Content */}
          <div className="p-6 flex-1 overflow-y-auto">
            {/* Filter Tabs */}
            <div className="flex gap-2 mb-6 overflow-x-auto">
              <button
                onClick={() => setStatusFilter('all')}
                className={`px-4 py-2 rounded-lg font-medium transition-colors whitespace-nowrap ${
                  statusFilter === 'all' ? 'bg-primary text-white' : 'bg-gray-800 text-gray-400 hover:bg-gray-700'
                }`}
              >
                All Orders ({orders.length})
              </button>
              <button
                onClick={() => setStatusFilter('pending')}
                className={`px-4 py-2 rounded-lg font-medium transition-colors whitespace-nowrap ${
                  statusFilter === 'pending' ? 'bg-yellow-600 text-white' : 'bg-gray-800 text-gray-400 hover:bg-gray-700'
                }`}
              >
                <i className="fas fa-clock mr-1"></i>
                Pending {pendingCount > 0 && `(${pendingCount})`}
              </button>
              <button
                onClick={() => setStatusFilter('approved')}
                className={`px-4 py-2 rounded-lg font-medium transition-colors whitespace-nowrap ${
                  statusFilter === 'approved' ? 'bg-green-600 text-white' : 'bg-gray-800 text-gray-400 hover:bg-gray-700'
                }`}
              >
                <i className="fas fa-check-circle mr-1"></i>
                Approved
              </button>
              <button
                onClick={() => setStatusFilter('rejected')}
                className={`px-4 py-2 rounded-lg font-medium transition-colors whitespace-nowrap ${
                  statusFilter === 'rejected' ? 'bg-red-600 text-white' : 'bg-gray-800 text-gray-400 hover:bg-gray-700'
                }`}
              >
                <i className="fas fa-times-circle mr-1"></i>
                Rejected
              </button>
            </div>

            {loading ? (
              <div className="text-center py-12">
                <i className="fas fa-spinner fa-spin text-4xl text-primary"></i>
              </div>
            ) : orders.length === 0 ? (
              <div className="bg-gray-800 rounded-lg p-12 text-center">
                <i className="fas fa-inbox text-6xl text-gray-700 mb-4"></i>
                <h2 className="text-2xl font-bold mb-2">No Orders Found</h2>
                <p className="text-gray-400">
                  {statusFilter === 'all' ? 'No orders have been placed yet' : `No ${statusFilter} orders`}
                </p>
              </div>
            ) : (
              <div className="bg-gray-800 rounded-lg overflow-hidden">
                <table className="w-full">
                  <thead className="bg-gray-700">
                    <tr>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Order ID</th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Customer</th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Products</th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Total</th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Status</th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Date</th>
                      <th className="px-6 py-3 text-right text-xs font-medium text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-gray-700">
                    {orders.map((order) => (
                      <tr key={order.id} className="hover:bg-gray-750">
                        <td className="px-6 py-4 font-medium">#{order.id}</td>
                        <td className="px-6 py-4 text-sm">{order.user_email}</td>
                        <td className="px-6 py-4 text-sm">
                          <div className="flex flex-wrap gap-1">
                            {order.products.slice(0, 2).map((p, i) => (
                              <span key={i} className="bg-gray-700 px-2 py-1 rounded text-xs">{p.name}</span>
                            ))}
                            {order.products.length > 2 && (
                              <span className="text-gray-400 text-xs">+{order.products.length - 2} more</span>
                            )}
                          </div>
                        </td>
                        <td className="px-6 py-4 font-semibold text-primary">{currency}{parseFloat(order.total_amount).toFixed(2)}</td>
                        <td className="px-6 py-4">
                          <span className={`px-3 py-1 rounded-full text-xs font-medium ${getStatusColor(order.status)} inline-flex items-center gap-1`}>
                            <i className={`fas ${getStatusIcon(order.status)}`}></i>
                            {order.status.charAt(0).toUpperCase() + order.status.slice(1)}
                          </span>
                        </td>
                        <td className="px-6 py-4 text-sm text-gray-400">{new Date(order.created_at).toLocaleDateString()}</td>
                        <td className="px-6 py-4 text-right text-sm">
                          <div className="flex justify-end gap-2">
                            <button onClick={() => setSelectedOrder(order)} className="text-blue-400 hover:text-blue-300 p-2" title="View Details">
                              <i className="fas fa-eye"></i>
                            </button>
                            {order.status === 'pending' && (
                              <>
                                <button onClick={() => setApproveOrder(order)} className="text-green-400 hover:text-green-300 p-2" title="Approve">
                                  <i className="fas fa-check"></i>
                                </button>
                                <button onClick={() => setRejectOrder(order)} className="text-red-400 hover:text-red-300 p-2" title="Reject">
                                  <i className="fas fa-times"></i>
                                </button>
                              </>
                            )}
                            <button onClick={() => handleDelete(order.id)} className="text-red-400 hover:text-red-300 p-2" title="Delete">
                              <i className="fas fa-trash"></i>
                            </button>
                          </div>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            )}
          </div>

          {approveOrder && <ApproveModal order={approveOrder} onClose={() => setApproveOrder(null)} onApprove={handleApprove} />}
          {rejectOrder && <RejectModal order={rejectOrder} onClose={() => setRejectOrder(null)} onReject={handleReject} />}
          {selectedOrder && <OrderDetailsModal order={selectedOrder} onClose={() => setSelectedOrder(null)} settings={settings} />}
          {toast && <Toast message={toast.message} type={toast.type} onClose={() => setToast(null)} />}
        </>
      );
    };

    // Products View (links to index.php for product management)
    const ProductsView = () => {
      return (
        <>
          <header className="h-16 bg-gray-800 border-b border-gray-700 flex items-center justify-between px-6 flex-shrink-0">
            <div>
              <h1 className="text-xl font-bold">Products Management</h1>
              <p className="text-sm text-gray-400">Manage your product catalog</p>
            </div>
          </header>
          <div className="p-6 flex-1 overflow-y-auto flex items-center justify-center">
            <div className="text-center">
              <i className="fas fa-box-open text-6xl text-gray-700 mb-6"></i>
              <h2 className="text-2xl font-bold mb-4">Product Management</h2>
              <p className="text-gray-400 mb-6">Manage products from the main storefront interface</p>
              <a href="index.php?stay" className="inline-block bg-primary hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition-colors">
                <i className="fas fa-external-link-alt mr-2"></i>
                Go to Product Management
              </a>
            </div>
          </div>
        </>
      );
    };

    // Main App Component
    const App = () => {
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
          currentPage="admin.php"
          currentUser={currentUser}
          stats={stats}
          menuItems={menuItems}
        >
          <DashboardView stats={stats} recentOrders={recentOrders} loading={loading} currentUser={currentUser} />
        </AdminLayout>
      );
    };

    ReactDOM.render(<App />, document.getElementById('root'));
  </script>
</body>
</html>
