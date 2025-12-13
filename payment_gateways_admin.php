<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: auth.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Payment Gateways - Admin Panel</title>
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

    <?php include 'admin_layout_component.php'; ?>

    const Toast = ({ message, type, onClose }) => (
      <div className={`fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 ${type === 'success' ? 'bg-green-600' : 'bg-red-600'} text-white animate-fade-in`}>
        <div className="flex items-center gap-2">
          <i className={`fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}`}></i>
          <span>{message}</span>
          <button onClick={onClose} className="ml-4 text-white hover:text-gray-200">
            <i className="fas fa-times"></i>
          </button>
        </div>
      </div>
    );

    const GatewayModal = ({ gateway, onClose, onSave }) => {
      const [formData, setFormData] = useState(gateway || {
        gateway_name: '',
        gateway_id: '',
        is_active: 1,
        fee_type: 'percentage',
        fee_value: 0,
        description: '',
        instructions: '',
        logo_url: '',
        sort_order: 0
      });

      const handleSubmit = (e) => {
        e.preventDefault();
        onSave(formData);
      };

      return (
        <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
          <div className="bg-gray-800 rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div className="p-6 border-b border-gray-700 flex justify-between items-center sticky top-0 bg-gray-800">
              <h2 className="text-2xl font-bold">{gateway ? 'Edit' : 'Add'} Payment Gateway</h2>
              <button onClick={onClose} className="text-gray-400 hover:text-white">
                <i className="fas fa-times text-xl"></i>
              </button>
            </div>

            <form onSubmit={handleSubmit} className="p-6 space-y-4">
              <div>
                <label className="block text-sm font-medium mb-2">Gateway Name *</label>
                <input
                  type="text"
                  required
                  value={formData.gateway_name}
                  onChange={(e) => setFormData({...formData, gateway_name: e.target.value})}
                  className="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-primary"
                  placeholder="e.g., PayPal, Stripe, Bank Transfer"
                />
              </div>

              <div>
                <label className="block text-sm font-medium mb-2">Gateway ID / Account *</label>
                <input
                  type="text"
                  required
                  value={formData.gateway_id}
                  onChange={(e) => setFormData({...formData, gateway_id: e.target.value})}
                  className="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-primary"
                  placeholder="e.g., paypal@example.com, API Key, Account Number"
                />
              </div>

              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="block text-sm font-medium mb-2">Fee Type</label>
                  <select
                    value={formData.fee_type}
                    onChange={(e) => setFormData({...formData, fee_type: e.target.value})}
                    className="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-primary"
                  >
                    <option value="percentage">Percentage (%)</option>
                    <option value="fixed">Fixed Amount</option>
                  </select>
                </div>

                <div>
                  <label className="block text-sm font-medium mb-2">Fee Value</label>
                  <input
                    type="number"
                    step="0.01"
                    min="0"
                    value={formData.fee_value}
                    onChange={(e) => setFormData({...formData, fee_value: parseFloat(e.target.value) || 0})}
                    className="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-primary"
                  />
                </div>
              </div>

              <div>
                <label className="block text-sm font-medium mb-2">Description</label>
                <textarea
                  value={formData.description}
                  onChange={(e) => setFormData({...formData, description: e.target.value})}
                  className="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-primary"
                  rows="2"
                  placeholder="Brief description of this payment method"
                ></textarea>
              </div>

              <div>
                <label className="block text-sm font-medium mb-2">Payment Instructions</label>
                <textarea
                  value={formData.instructions}
                  onChange={(e) => setFormData({...formData, instructions: e.target.value})}
                  className="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-primary"
                  rows="3"
                  placeholder="Instructions for customers on how to use this payment method"
                ></textarea>
              </div>

              <div>
                <label className="block text-sm font-medium mb-2">Logo URL</label>
                <input
                  type="url"
                  value={formData.logo_url}
                  onChange={(e) => setFormData({...formData, logo_url: e.target.value})}
                  className="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-primary"
                  placeholder="https://example.com/logo.png"
                />
              </div>

              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="block text-sm font-medium mb-2">Sort Order</label>
                  <input
                    type="number"
                    min="0"
                    value={formData.sort_order}
                    onChange={(e) => setFormData({...formData, sort_order: parseInt(e.target.value) || 0})}
                    className="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-primary"
                  />
                </div>

                <div>
                  <label className="block text-sm font-medium mb-2">Status</label>
                  <select
                    value={formData.is_active}
                    onChange={(e) => setFormData({...formData, is_active: parseInt(e.target.value)})}
                    className="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-primary"
                  >
                    <option value={1}>Active</option>
                    <option value={0}>Inactive</option>
                  </select>
                </div>
              </div>

              <div className="flex gap-3 pt-4">
                <button
                  type="submit"
                  className="flex-1 bg-primary hover:bg-blue-600 text-white py-2 px-4 rounded-lg transition-colors"
                >
                  <i className="fas fa-save mr-2"></i>
                  {gateway ? 'Update' : 'Create'} Gateway
                </button>
                <button
                  type="button"
                  onClick={onClose}
                  className="px-6 bg-gray-700 hover:bg-gray-600 text-white py-2 rounded-lg transition-colors"
                >
                  Cancel
                </button>
              </div>
            </form>
          </div>
        </div>
      );
    };

    const GatewaysView = () => {
      const [gateways, setGateways] = useState([]);
      const [isModalOpen, setIsModalOpen] = useState(false);
      const [editingGateway, setEditingGateway] = useState(null);
      const [toast, setToast] = useState(null);
      const [loading, setLoading] = useState(true);

      const showToast = (message, type = 'success') => {
        setToast({ message, type });
        setTimeout(() => setToast(null), 3000);
      };

      const loadGateways = async () => {
        setLoading(true);
        try {
          const res = await fetch('payment_gateways.php?action=list');
          const data = await res.json();
          if (Array.isArray(data)) {
            setGateways(data);
          }
        } catch (err) {
          showToast('Failed to load gateways', 'error');
        } finally {
          setLoading(false);
        }
      };

      useEffect(() => {
        loadGateways();
      }, []);

      const handleSave = async (gatewayData) => {
        try {
          const action = editingGateway ? 'update' : 'create';
          const payload = editingGateway ? { ...gatewayData, id: editingGateway.id } : gatewayData;
          
          const res = await fetch(`payment_gateways.php?action=${action}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
          });
          
          const data = await res.json();
          if (data.success) {
            showToast(`Gateway ${editingGateway ? 'updated' : 'created'} successfully`);
            loadGateways();
            setIsModalOpen(false);
            setEditingGateway(null);
          } else {
            showToast(data.error || 'Operation failed', 'error');
          }
        } catch (err) {
          showToast('Operation failed', 'error');
        }
      };

      const handleDelete = async (id) => {
        if (!confirm('Are you sure you want to delete this payment gateway?')) return;
        
        try {
          const res = await fetch('payment_gateways.php?action=delete', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id })
          });
          
          const data = await res.json();
          if (data.success) {
            showToast('Gateway deleted successfully');
            loadGateways();
          } else {
            showToast(data.error || 'Delete failed', 'error');
          }
        } catch (err) {
          showToast('Delete failed', 'error');
        }
      };

      const handleToggleActive = async (id) => {
        try {
          const res = await fetch('payment_gateways.php?action=toggle_active', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id })
          });
          
          const data = await res.json();
          if (data.success) {
            showToast('Status updated');
            loadGateways();
          }
        } catch (err) {
          showToast('Update failed', 'error');
        }
      };

      const handleEdit = (gateway) => {
        setEditingGateway(gateway);
        setIsModalOpen(true);
      };

      return (
        <div className="p-4 sm:p-6">
          <div className="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6 sm:mb-8">
            <div>
              <h1 className="text-2xl sm:text-3xl font-bold mb-2">Payment Gateways</h1>
              <p className="text-sm sm:text-base text-gray-400">Manage payment methods for your marketplace</p>
            </div>
            <button
              onClick={() => { setEditingGateway(null); setIsModalOpen(true); }}
              className="bg-primary hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors whitespace-nowrap w-full sm:w-auto"
            >
              <i className="fas fa-plus mr-2"></i>Add Gateway
            </button>
          </div>

            {loading ? (
              <div className="text-center py-12">
                <i className="fas fa-spinner fa-spin text-4xl text-primary"></i>
              </div>
            ) : (
              <div className="bg-gray-800 rounded-lg overflow-hidden">
                <table className="w-full">
                  <thead className="bg-gray-700">
                    <tr>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Gateway</th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Gateway ID</th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Fee</th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Status</th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Order</th>
                      <th className="px-6 py-3 text-right text-xs font-medium text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-gray-700">
                    {gateways.length === 0 ? (
                      <tr>
                        <td colSpan="6" className="px-6 py-8 text-center text-gray-400">
                          No payment gateways found. Add your first gateway to get started.
                        </td>
                      </tr>
                    ) : (
                      gateways.map((gateway) => (
                        <tr key={gateway.id} className="hover:bg-gray-750">
                          <td className="px-6 py-4">
                            <div className="flex items-center gap-3">
                              {gateway.logo_url && (
                                <img src={gateway.logo_url} alt={gateway.gateway_name} className="w-10 h-10 object-contain rounded" />
                              )}
                              <div>
                                <div className="font-medium">{gateway.gateway_name}</div>
                                {gateway.description && (
                                  <div className="text-sm text-gray-400">{gateway.description}</div>
                                )}
                              </div>
                            </div>
                          </td>
                          <td className="px-6 py-4 text-sm text-gray-300">
                            <code className="bg-gray-700 px-2 py-1 rounded">{gateway.gateway_id}</code>
                          </td>
                          <td className="px-6 py-4 text-sm">
                            <span className="text-gray-300">
                              {gateway.fee_value > 0 ? (
                                gateway.fee_type === 'percentage' ? `${gateway.fee_value}%` : `$${gateway.fee_value}`
                              ) : 'Free'}
                            </span>
                          </td>
                          <td className="px-6 py-4">
                            <button
                              onClick={() => handleToggleActive(gateway.id)}
                              className={`px-3 py-1 rounded-full text-xs font-medium ${
                                gateway.is_active ? 'bg-green-600 text-white' : 'bg-gray-600 text-gray-300'
                              }`}
                            >
                              {gateway.is_active ? 'Active' : 'Inactive'}
                            </button>
                          </td>
                          <td className="px-6 py-4 text-sm text-gray-300">{gateway.sort_order}</td>
                          <td className="px-6 py-4 text-right text-sm">
                            <button
                              onClick={() => handleEdit(gateway)}
                              className="text-blue-400 hover:text-blue-300 mr-3"
                            >
                              <i className="fas fa-edit"></i>
                            </button>
                            <button
                              onClick={() => handleDelete(gateway.id)}
                              className="text-red-400 hover:text-red-300"
                            >
                              <i className="fas fa-trash"></i>
                            </button>
                          </td>
                        </tr>
                      ))
                    )}
                  </tbody>
                </table>
              </div>
            )}

          {isModalOpen && (
            <GatewayModal
              gateway={editingGateway}
              onClose={() => { setIsModalOpen(false); setEditingGateway(null); }}
              onSave={handleSave}
            />
          )}

          {toast && <Toast message={toast.message} type={toast.type} onClose={() => setToast(null)} />}
        </div>
      );
    };

    const App = () => {
      const [currentUser, setCurrentUser] = useState(null);
      const [stats, setStats] = useState({});

      useEffect(() => {
        loadUser();
        loadStats();
      }, []);

      const loadUser = async () => {
        try {
          const res = await fetch('auth.php?action=me');
          const data = await res.json();
          if (data.email) {
            setCurrentUser(data);
          }
        } catch (err) {
          console.error('Failed to load user', err);
        }
      };

      const loadStats = async () => {
        try {
          const res = await fetch('orders.php?action=stats');
          const data = await res.json();
          setStats(data);
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
          currentPage="admin_gateways.php"
          currentUser={currentUser}
          stats={stats}
          menuItems={menuItems}
        >
          <GatewaysView />
        </AdminLayout>
      );
    };

    ReactDOM.render(<App />, document.getElementById('root'));
  </script>
</body>
</html>
