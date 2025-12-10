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

    // Badge Component
    const Badge = ({ type }) => {
      const badges = {
        Private: { bg: 'bg-blue-500/20', text: 'text-blue-400', border: 'border-blue-500/20', label: 'Private' },
        Shared: { bg: 'bg-orange-500/20', text: 'text-orange-400', border: 'border-orange-500/20', label: 'Shared' },
        Hot: { bg: 'bg-red-500/20', text: 'text-red-400', border: 'border-red-500/20', label: '🔥 HOT', icon: 'fa-fire' },
        Hidden: { bg: 'bg-gray-500/20', text: 'text-gray-400', border: 'border-gray-500/20', label: 'Hidden', icon: 'fa-eye-slash' }
      };
      const badge = badges[type] || badges.Private;
      return (
        <span className={`${badge.bg} ${badge.text} border ${badge.border} text-[10px] font-bold px-2 py-1 rounded-full shadow-sm inline-flex items-center gap-1`}>
          {badge.icon && <i className={`fa-solid ${badge.icon}`}></i>}{badge.label}
        </span>
      );
    };

    // Product Card Component
    const ProductCard = ({ product, onEdit, onDelete, onToggleVisible }) => {
      const originalPrice = product.prices?.[0]?.price || 0;
      const discountedPrice = product.discountedPrice || originalPrice;
      const discountPercent = originalPrice > 0 ? Math.round(((originalPrice - discountedPrice) / originalPrice) * 100) : 0;

      return (
        <div className={`bg-gray-800 border border-gray-700 rounded-2xl p-5 hover:border-primary transition-all relative group overflow-hidden flex flex-col h-full ${product.is_visible != 1 ? 'opacity-75 grayscale' : ''}`}>
          <div className="absolute top-2 left-2 z-30 flex gap-2">
            <button onClick={() => onEdit(product)} className="bg-blue-600 hover:bg-blue-500 text-white w-8 h-8 rounded-lg flex items-center justify-center shadow-lg transition-colors" title="Edit Product">
              <i className="fa-solid fa-pen text-xs"></i>
            </button>
            <button onClick={() => onToggleVisible(product.id)} className={`${product.is_visible == 1 ? 'bg-gray-600 hover:bg-gray-500' : 'bg-green-600 hover:bg-green-500'} text-white w-8 h-8 rounded-lg flex items-center justify-center shadow-lg transition-colors`} title={product.is_visible == 1 ? 'Hide Product' : 'Show Product'}>
              <i className={`fa-solid ${product.is_visible == 1 ? 'fa-eye-slash' : 'fa-eye'} text-xs`}></i>
            </button>
            <button onClick={() => { if (confirm('Are you sure you want to delete this listing?')) onDelete(product.id); }} className="bg-red-600 hover:bg-red-500 text-white w-8 h-8 rounded-lg flex items-center justify-center shadow-lg transition-colors" title="Delete Product">
              <i className="fa-solid fa-trash text-xs"></i>
            </button>
          </div>

          <div className="absolute top-0 right-0 -mr-16 -mt-16 w-32 h-32 bg-primary/20 blur-[50px] rounded-full group-hover:bg-primary/30 transition-all duration-500"></div>

          <div className="flex justify-between items-start mb-4 relative z-10 pl-0 mt-2">
            <div className="flex items-center space-x-3">
              <div className="w-12 h-12 rounded-xl bg-gradient-to-br from-gray-800 to-gray-900 flex items-center justify-center border border-gray-700 shadow-lg shrink-0">
                <i className={`fa-solid ${product.icon || 'fa-box-open'} text-2xl text-primary`}></i>
              </div>
              <div>
                <h3 className="font-bold text-white text-lg leading-tight">{product.name}</h3>
                <span className="text-gray-400 text-xs">{product.service_type}</span>
              </div>
            </div>
            <div className="flex flex-col items-end gap-1">
              {product.is_hot == 1 && <Badge type="Hot" />}
              {product.is_visible != 1 && <Badge type="Hidden" />}
              <Badge type={product.account_type} />
            </div>
          </div>

          <p className="text-gray-400 text-sm mb-4 line-clamp-2 min-h-[40px] relative z-10">{product.description}</p>

          <div className="mb-6 flex-grow relative z-10">
            <div className="space-y-2">
              {(product.features || []).slice(0, 3).map((feature, idx) => (
                <div key={idx} className="flex items-center text-xs text-gray-300">
                  <i className="fa-solid fa-check text-green-400 mr-2"></i>{feature}
                </div>
              ))}
              {product.features && product.features.length > 3 && (
                <div className="text-xs text-gray-500 pl-5">+{product.features.length - 3} more features</div>
              )}
            </div>
          </div>

          <div className="flex items-center gap-2 mb-4">
            <div className="bg-gray-800/50 p-1.5 px-2 rounded text-[10px] text-gray-400 flex items-center border border-gray-700/50 flex-1">
              <i className="fa-solid fa-circle-info text-blue-400 mr-1.5"></i>
              <span className="truncate">{product.requirements || 'None'}</span>
            </div>
            <div className={`p-1.5 px-2 rounded text-[10px] border flex items-center ${product.stock > 0 ? 'bg-emerald-900/20 text-emerald-400 border-emerald-500/20' : 'bg-red-900/20 text-red-400 border-red-500/20'}`}>
              <i className="fa-solid fa-cubes mr-1.5"></i>
              <span>{product.stock > 0 ? `${product.stock} Left` : 'Sold Out'}</span>
            </div>
          </div>

          <div className="pt-4 border-t border-gray-700/50 flex items-center justify-between relative z-10">
            <div>
              <div className="flex items-center gap-2">
                <span className="text-gray-500 line-through text-xs">${originalPrice.toFixed(2)}</span>
                {discountPercent > 0 && (
                  <span className="text-green-400 text-xs font-bold bg-green-400/10 px-1.5 py-0.5 rounded">-{discountPercent}%</span>
                )}
              </div>
              <div className="text-2xl font-bold text-white">${discountedPrice.toFixed(2)}</div>
            </div>
          </div>
        </div>
      );
    };

    // Product Modal Component
    const ProductModal = ({ isOpen, onClose, onSave, productToEdit }) => {
      const [formData, setFormData] = useState({
        name: '',
        serviceType: '',
        accountType: 'Private',
        originalPrice: 0,
        discountedPrice: 0,
        features: [],
        requirements: '',
        description: '',
        isHot: false,
        icon: 'fa-box',
        stock: 10,
        isVisible: true
      });
      const [featureInput, setFeatureInput] = useState('');

      useEffect(() => {
        if (productToEdit) {
          setFormData({
            id: productToEdit.id,
            name: productToEdit.name,
            serviceType: productToEdit.service_type,
            accountType: productToEdit.account_type,
            originalPrice: productToEdit.prices?.[0]?.price || 0,
            discountedPrice: productToEdit.discountedPrice || 0,
            features: productToEdit.features || [],
            requirements: productToEdit.requirements || '',
            description: productToEdit.description || '',
            isHot: productToEdit.is_hot == 1,
            icon: productToEdit.icon || 'fa-box',
            stock: productToEdit.stock || 0,
            isVisible: productToEdit.is_visible == 1
          });
        } else {
          setFormData({
            name: '',
            serviceType: '',
            accountType: 'Private',
            originalPrice: 0,
            discountedPrice: 0,
            features: [],
            requirements: '',
            description: '',
            isHot: false,
            icon: 'fa-box',
            stock: 10,
            isVisible: true
          });
        }
      }, [productToEdit, isOpen]);

      if (!isOpen) return null;

      const handleSubmit = (e) => {
        e.preventDefault();
        if (!formData.name || !formData.originalPrice) return;
        
        onSave({
          id: productToEdit?.id,
          name: formData.name,
          service_type: formData.serviceType || 'Digital Service',
          account_type: formData.accountType,
          prices: [{ duration: 'lifetime', price: Number(formData.originalPrice) }],
          discountedPrice: Number(formData.discountedPrice),
          description: formData.description || '',
          features: formData.features || [],
          requirements: formData.requirements || 'None',
          is_hot: formData.isHot ? 1 : 0,
          icon: formData.icon || 'fa-box',
          stock: Number(formData.stock),
          is_visible: formData.isVisible ? 1 : 0
        });
        onClose();
        setFeatureInput('');
      };

      const addFeature = () => {
        if (featureInput.trim()) {
          setFormData(prev => ({ ...prev, features: [...(prev.features || []), featureInput.trim()] }));
          setFeatureInput('');
        }
      };

      return (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
          <div className="absolute inset-0 bg-black/80 backdrop-blur-sm" onClick={onClose}></div>
          <div className="bg-gray-800 border border-gray-700 w-full max-w-2xl rounded-2xl relative z-10 max-h-[90vh] overflow-y-auto shadow-2xl">
            <div className="p-6 border-b border-gray-700 flex justify-between items-center bg-gray-900/50 sticky top-0 backdrop-blur-md z-20">
              <h2 className="text-xl font-bold text-white flex items-center gap-2">
                <i className={`fa-solid ${productToEdit ? 'fa-pen-to-square' : 'fa-plus-circle'} text-primary`}></i>
                {productToEdit ? 'Edit Product' : 'Add New Product'}
              </h2>
              <button onClick={onClose} className="text-gray-400 hover:text-white transition-colors">
                <i className="fa-solid fa-xmark text-xl"></i>
              </button>
            </div>
            
            <form onSubmit={handleSubmit} className="p-6 space-y-6">
              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div className="space-y-4">
                  <div>
                    <label className="block text-xs font-medium text-gray-400 mb-1">Product Name *</label>
                    <input
                      type="text"
                      className="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-primary transition-colors"
                      placeholder="e.g. ChatPlus Pro"
                      value={formData.name}
                      onChange={e => setFormData({ ...formData, name: e.target.value })}
                      required
                    />
                  </div>
                  <div>
                    <label className="block text-xs font-medium text-gray-400 mb-1">Service Category</label>
                    <input
                      type="text"
                      className="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-primary transition-colors"
                      placeholder="e.g. AI Tools"
                      value={formData.serviceType}
                      onChange={e => setFormData({ ...formData, serviceType: e.target.value })}
                    />
                  </div>
                  <div>
                    <label className="block text-xs font-medium text-gray-400 mb-1">Account Type</label>
                    <div className="grid grid-cols-2 gap-3">
                      <button
                        type="button"
                        onClick={() => setFormData({ ...formData, accountType: 'Private' })}
                        className={`py-2 rounded-lg text-sm border transition-all ${formData.accountType === 'Private' ? 'bg-blue-500/20 border-blue-500 text-blue-400' : 'bg-gray-700 border-gray-600 text-gray-400'}`}
                      >
                        Private
                      </button>
                      <button
                        type="button"
                        onClick={() => setFormData({ ...formData, accountType: 'Shared' })}
                        className={`py-2 rounded-lg text-sm border transition-all ${formData.accountType === 'Shared' ? 'bg-orange-500/20 border-orange-500 text-orange-400' : 'bg-gray-700 border-gray-600 text-gray-400'}`}
                      >
                        Shared
                      </button>
                    </div>
                  </div>
                  <div>
                    <label className="block text-xs font-medium text-gray-400 mb-1">Icon (FontAwesome)</label>
                    <div className="flex gap-2">
                      <div className="w-10 h-10 bg-gray-700 rounded flex items-center justify-center border border-gray-600">
                        <i className={`fa-solid ${formData.icon} text-primary`}></i>
                      </div>
                      <input
                        type="text"
                        className="flex-1 bg-gray-700 border border-gray-600 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-primary transition-colors"
                        placeholder="e.g. fa-robot"
                        value={formData.icon}
                        onChange={e => setFormData({ ...formData, icon: e.target.value })}
                      />
                    </div>
                  </div>
                </div>
                
                <div className="space-y-4">
                  <div className="grid grid-cols-2 gap-4">
                    <div>
                      <label className="block text-xs font-medium text-gray-400 mb-1">Original Price ($) *</label>
                      <input
                        type="number"
                        step="0.01"
                        className="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-primary transition-colors"
                        value={formData.originalPrice || ''}
                        onChange={e => setFormData({ ...formData, originalPrice: parseFloat(e.target.value) })}
                        required
                      />
                    </div>
                    <div>
                      <label className="block text-xs font-medium text-gray-400 mb-1">Discount Price ($) *</label>
                      <input
                        type="number"
                        step="0.01"
                        className="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-primary transition-colors"
                        value={formData.discountedPrice || ''}
                        onChange={e => setFormData({ ...formData, discountedPrice: parseFloat(e.target.value) })}
                        required
                      />
                    </div>
                  </div>
                  <div>
                    <label className="block text-xs font-medium text-gray-400 mb-1">Stock Quantity</label>
                    <input
                      type="number"
                      className="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-primary transition-colors"
                      value={formData.stock}
                      onChange={e => setFormData({ ...formData, stock: parseInt(e.target.value) || 0 })}
                    />
                  </div>
                  <div>
                    <label className="block text-xs font-medium text-gray-400 mb-1">Short Description</label>
                    <textarea
                      className="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-primary transition-colors h-24 resize-none"
                      placeholder="What is included?"
                      value={formData.description}
                      onChange={e => setFormData({ ...formData, description: e.target.value })}
                    ></textarea>
                  </div>
                </div>
              </div>
              
              <div className="border-t border-gray-700/50 pt-4">
                <label className="block text-xs font-medium text-gray-400 mb-2">Features List</label>
                <div className="flex gap-2 mb-3">
                  <input
                    type="text"
                    className="flex-1 bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-primary"
                    placeholder="e.g. 4K Support"
                    value={featureInput}
                    onChange={e => setFeatureInput(e.target.value)}
                    onKeyDown={e => { if (e.key === 'Enter') { e.preventDefault(); addFeature(); } }}
                  />
                  <button type="button" onClick={addFeature} className="bg-gray-600 hover:bg-gray-500 text-white px-4 rounded-lg transition-colors">
                    Add
                  </button>
                </div>
                <div className="flex flex-wrap gap-2">
                  {(formData.features || []).map((f, i) => (
                    <span key={i} className="bg-gray-700 text-gray-300 text-xs px-2 py-1 rounded border border-gray-600 flex items-center">
                      {f}
                      <button
                        type="button"
                        onClick={() => setFormData(prev => ({ ...prev, features: prev.features.filter((_, idx) => idx !== i) }))}
                        className="ml-2 text-gray-500 hover:text-red-400"
                      >
                        &times;
                      </button>
                    </span>
                  ))}
                </div>
              </div>
              
              <div className="space-y-4 pt-2">
                <div>
                  <label className="block text-xs font-medium text-gray-400 mb-1">Requirements</label>
                  <input
                    type="text"
                    className="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-primary transition-colors"
                    placeholder="e.g. Email needed"
                    value={formData.requirements}
                    onChange={e => setFormData({ ...formData, requirements: e.target.value })}
                  />
                </div>
                <div className="flex items-center gap-6">
                  <div className="flex items-center gap-3">
                    <input
                      type="checkbox"
                      id="isHot"
                      checked={!!formData.isHot}
                      onChange={e => setFormData({ ...formData, isHot: e.target.checked })}
                      className="w-4 h-4 rounded border-gray-600 bg-gray-700 text-primary focus:ring-primary"
                    />
                    <label htmlFor="isHot" className="text-sm text-gray-300 cursor-pointer select-none">
                      Mark as Hot/Trending
                    </label>
                  </div>
                  <div className="flex items-center gap-3">
                    <input
                      type="checkbox"
                      id="isVisible"
                      checked={!!formData.isVisible}
                      onChange={e => setFormData({ ...formData, isVisible: e.target.checked })}
                      className="w-4 h-4 rounded border-gray-600 bg-gray-700 text-green-500 focus:ring-green-500"
                    />
                    <label htmlFor="isVisible" className="text-sm text-gray-300 cursor-pointer select-none">
                      Visible in Store
                    </label>
                  </div>
                </div>
              </div>
              
              <div className="pt-4 border-t border-gray-700 flex justify-end gap-3">
                <button
                  type="button"
                  onClick={onClose}
                  className="px-6 py-2.5 rounded-xl text-gray-400 hover:text-white font-medium transition-colors"
                >
                  Cancel
                </button>
                <button
                  type="submit"
                  className="bg-gradient-to-r from-primary to-secondary text-white px-8 py-2.5 rounded-xl font-medium shadow-lg hover:opacity-90 transition-opacity"
                >
                  {productToEdit ? 'Save Changes' : 'Add Product'}
                </button>
              </div>
            </form>
          </div>
        </div>
      );
    };

    // Toast Component
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

    // Products View Component
    const ProductsView = () => {
      const [products, setProducts] = useState([]);
      const [loading, setLoading] = useState(true);
      const [isModalOpen, setIsModalOpen] = useState(false);
      const [editingProduct, setEditingProduct] = useState(null);
      const [toast, setToast] = useState(null);

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
          showToast('Failed to load products', 'error');
        } finally {
          setLoading(false);
        }
      };

      const showToast = (message, type = 'success') => {
        setToast({ message, type });
        setTimeout(() => setToast(null), 3000);
      };

      const handleAddProduct = async (productData) => {
        try {
          const res = await fetch('products.php?action=create', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(productData)
          });
          const data = await res.json();
          if (data.success) {
            showToast('Product added successfully');
            loadProducts();
          } else {
            showToast(data.error || 'Failed to add product', 'error');
          }
        } catch (err) {
          showToast('Failed to add product', 'error');
        }
      };

      const handleUpdateProduct = async (productData) => {
        try {
          const res = await fetch('products.php?action=update', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(productData)
          });
          const data = await res.json();
          if (data.success) {
            showToast('Product updated successfully');
            loadProducts();
          } else {
            showToast(data.error || 'Failed to update product', 'error');
          }
        } catch (err) {
          showToast('Failed to update product', 'error');
        }
      };

      const handleDeleteProduct = async (id) => {
        try {
          const res = await fetch('products.php?action=delete', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id })
          });
          const data = await res.json();
          if (data.success) {
            showToast('Product deleted successfully');
            loadProducts();
          } else {
            showToast(data.error || 'Failed to delete product', 'error');
          }
        } catch (err) {
          showToast('Failed to delete product', 'error');
        }
      };

      const handleToggleVisible = async (id) => {
        try {
          const res = await fetch(`products.php?action=toggle_visible&id=${id}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' }
          });
          const data = await res.json();
          if (data.success) {
            showToast('Product visibility updated');
            loadProducts();
          } else {
            showToast(data.error || 'Failed to update visibility', 'error');
          }
        } catch (err) {
          showToast('Failed to update visibility', 'error');
        }
      };

      const openEditModal = (product) => {
        setEditingProduct(product);
        setIsModalOpen(true);
      };

      const openAddModal = () => {
        setEditingProduct(null);
        setIsModalOpen(true);
      };

      return (
        <>
          <header className="h-16 bg-gray-800 border-b border-gray-700 flex items-center justify-between px-6 flex-shrink-0">
            <div>
              <h1 className="text-xl font-bold">Products Management</h1>
              <p className="text-sm text-gray-400">Manage your product catalog</p>
            </div>
            <button onClick={openAddModal} className="px-4 py-2 bg-primary hover:bg-blue-600 rounded-lg text-sm transition-colors">
              <i className="fas fa-plus mr-2"></i>
              Add New Product
            </button>
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
                <button onClick={openAddModal} className="inline-block bg-primary hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition-colors">
                  <i className="fas fa-plus mr-2"></i>
                  Add Product
                </button>
              </div>
            ) : (
              <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {products.map((product) => (
                  <ProductCard
                    key={product.id}
                    product={product}
                    onEdit={openEditModal}
                    onDelete={handleDeleteProduct}
                    onToggleVisible={handleToggleVisible}
                  />
                ))}
              </div>
            )}
          </div>

          <ProductModal
            isOpen={isModalOpen}
            onClose={() => {
              setIsModalOpen(false);
              setEditingProduct(null);
            }}
            onSave={editingProduct ? handleUpdateProduct : handleAddProduct}
            productToEdit={editingProduct}
          />

          {toast && <Toast message={toast.message} type={toast.type} onClose={() => setToast(null)} />}
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
