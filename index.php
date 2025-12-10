<?php
// Load settings for SEO
$DB_HOST = 'localhost';
$DB_NAME = 'u559276167_gamsgo';
$DB_USER = 'u559276167_gamsgo';
$DB_PASS = 'Gamsgo@123';

try {
    $dsn = "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4";
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    
    $stmt = $pdo->query("SELECT * FROM settings LIMIT 1");
    $settings = $stmt->fetch();
} catch (Exception $e) {
    // If database fails, use defaults
    $settings = [];
}

$pageTitle = $settings['meta_title'] ?? ($settings['site_name'] ?? 'DigiMarket') . ' - Premium Digital Assets';
$metaDescription = $settings['meta_description'] ?? 'Discover premium digital subscriptions and assets';
$metaKeywords = $settings['meta_keywords'] ?? 'digital assets, marketplace, subscriptions';
$ogImage = $settings['og_image_url'] ?? '';
$siteName = $settings['site_name'] ?? 'DigiMarket';
$faviconUrl = $settings['favicon_url'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($metaDescription); ?>" />
    <meta name="keywords" content="<?php echo htmlspecialchars($metaKeywords); ?>" />
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website" />
    <meta property="og:title" content="<?php echo htmlspecialchars($pageTitle); ?>" />
    <meta property="og:description" content="<?php echo htmlspecialchars($metaDescription); ?>" />
    <?php if ($ogImage): ?>
    <meta property="og:image" content="<?php echo htmlspecialchars($ogImage); ?>" />
    <?php endif; ?>
    
    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="<?php echo htmlspecialchars($pageTitle); ?>" />
    <meta name="twitter:description" content="<?php echo htmlspecialchars($metaDescription); ?>" />
    <?php if ($ogImage): ?>
    <meta name="twitter:image" content="<?php echo htmlspecialchars($ogImage); ?>" />
    <?php endif; ?>
    
    <?php if ($faviconUrl): ?>
    <link rel="icon" href="<?php echo htmlspecialchars($faviconUrl); ?>" />
    <?php endif; ?>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
      tailwind.config = {
        darkMode: 'class',
        theme: {
          extend: {
            fontFamily: { sans: ['Inter', 'sans-serif'] },
            colors: {
              primary: '#6366f1',
              primaryDark: '#4f46e5',
              secondary: '#ec4899',
              dark: '#0f172a',
              darker: '#020617',
              card: '#1e293b',
            }
          }
        }
      }
    </script>
    <style>
      body { background-color: #020617; color: #f8fafc; }
      .glass-panel { background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.08); }
      .gradient-text { background: linear-gradient(135deg, #818cf8 0%, #c084fc 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
      ::-webkit-scrollbar { width: 8px; }
      ::-webkit-scrollbar-track { background: #0f172a; }
      ::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
      ::-webkit-scrollbar-thumb:hover { background: #475569; }
    </style>
    
    <?php if (!empty($settings['google_analytics_id'])): ?>
    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo htmlspecialchars($settings['google_analytics_id']); ?>"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', '<?php echo htmlspecialchars($settings['google_analytics_id']); ?>');
    </script>
    <?php endif; ?>
    
    <?php if (!empty($settings['facebook_pixel_id'])): ?>
    <!-- Facebook Pixel -->
    <script>
      !function(f,b,e,v,n,t,s)
      {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
      n.callMethod.apply(n,arguments):n.queue.push(arguments)};
      if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
      n.queue=[];t=b.createElement(e);t.async=!0;
      t.src=v;s=b.getElementsByTagName(e)[0];
      s.parentNode.insertBefore(t,s)}(window, document,'script',
      'https://connect.facebook.net/en_US/fbevents.js');
      fbq('init', '<?php echo htmlspecialchars($settings['facebook_pixel_id']); ?>');
      fbq('track', 'PageView');
    </script>
    <noscript>
      <img height="1" width="1" style="display:none" 
           src="https://www.facebook.com/tr?id=<?php echo htmlspecialchars($settings['facebook_pixel_id']); ?>&ev=PageView&noscript=1" />
    </noscript>
    <?php endif; ?>
    
    <script src="https://unpkg.com/react@18/umd/react.production.min.js"></script>
    <script src="https://unpkg.com/react-dom@18/umd/react-dom.production.min.js"></script>
    <script src="https://unpkg.com/@babel/standalone@7/babel.min.js"></script>
  </head>
  <body>
    <div id="root"></div>
    <script type="text/babel" data-presets="env,react">
      const { useState, useEffect, useCallback } = React;
      const { createRoot } = ReactDOM;

      const CLOUD_API_URL = 'https://jsonblob.com/api/jsonBlob';
      const SYNC_INTERVAL = 10000;

      const DEFAULT_PRODUCTS = [
        { id: '1', name: 'ChatGPT Plus', serviceType: 'AI Assistant', accountType: 'Private', originalPrice: 20.00, discountedPrice: 12.99, description: 'Get full access to GPT-4, DALL-E 3, and advanced data analysis on your own private email.', features: ['GPT-4 Access', 'DALL-E 3 Image Gen', 'Private Email', '1 Month Warranty'], requirements: 'Email address needed', isHot: true, icon: 'fa-robot', stock: 50, isVisible: true },
        { id: '2', name: 'Netflix Premium', serviceType: 'Entertainment', accountType: 'Shared', originalPrice: 15.99, discountedPrice: 3.50, description: '4K Ultra HD streaming. Shared profile with pin lock.', features: ['4K UHD', '1 Profile', 'Pin Protected', 'Works on TV/Mobile'], requirements: 'No requirements', isHot: true, icon: 'fa-film', stock: 120, isVisible: true },
        { id: '3', name: 'Claude 3 Opus', serviceType: 'AI Assistant', accountType: 'Private', originalPrice: 20.00, discountedPrice: 14.50, description: 'Experience the most intelligent AI model from Anthropic securely.', features: ['Claude 3 Opus', '200K Context Window', 'Private Account', 'Full History'], requirements: 'Email not registered with Anthropic', isHot: false, icon: 'fa-brain', stock: 15, isVisible: true },
        { id: '4', name: 'Spotify Premium', serviceType: 'Music', accountType: 'Shared', originalPrice: 10.99, discountedPrice: 1.99, description: 'Ad-free music listening, offline playback, and unlimited skips.', features: ['No Ads', 'Offline Mode', 'High Quality Audio', 'Lifetime Support'], requirements: 'None', isHot: false, icon: 'fa-music', stock: 200, isVisible: true },
        { id: '5', name: 'Adobe Creative Cloud', serviceType: 'Design', accountType: 'Private', originalPrice: 54.99, discountedPrice: 25.00, description: 'Full suite of Adobe apps including Photoshop, Illustrator, Premiere Pro.', features: ['All Apps', '100GB Cloud Storage', 'Private License', 'Commercial Use'], requirements: 'Adobe Email', isHot: true, icon: 'fa-pen-nib', stock: 5, isVisible: true },
      ];

      const CloudService = {
        createStore: async (data) => {
          const response = await fetch(CLOUD_API_URL, { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' }, body: JSON.stringify(data) });
          const location = response.headers.get('Location');
          if (!location) throw new Error('No location header returned');
          const parts = location.split('/');
          return parts[parts.length - 1];
        },
        getStore: async (id) => {
          const response = await fetch(`${CLOUD_API_URL}/${id}`);
          if (!response.ok) throw new Error('Store not found');
          return await response.json();
        },
        updateStore: async (id, data) => {
          await fetch(`${CLOUD_API_URL}/${id}`, { method: 'PUT', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' }, body: JSON.stringify(data) });
        }
      };

      const Toast = ({ message, type, onClose }) => {
        useEffect(() => { const timer = setTimeout(onClose, 3000); return () => clearTimeout(timer); }, [onClose]);
        const colors = { success: 'bg-green-500/10 border-green-500/20 text-green-400', error: 'bg-red-500/10 border-red-500/20 text-red-400', info: 'bg-blue-500/10 border-blue-500/20 text-blue-400' };
        return (
          <div className={`fixed bottom-4 right-4 ${colors[type]} border p-4 rounded-xl backdrop-blur-md shadow-xl flex items-center gap-3 z-50 animate-bounce-in`}>
            <i className={`fa-solid ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-triangle-exclamation' : 'fa-info-circle'}`}></i>
            <span className="font-medium text-sm">{message}</span>
          </div>
        );
      };

      const Badge = ({ type }) => {
        const styles = {
          Private: 'bg-blue-500/10 text-blue-400 border-blue-500/20',
          Shared: 'bg-orange-500/10 text-orange-400 border-orange-500/20',
          Hot: 'bg-red-500/10 text-red-400 border-red-500/20 px-2 py-0.5 text-xs font-bold uppercase tracking-wider',
          Stock: 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
          Hidden: 'bg-gray-500/20 text-gray-400 border-gray-500/30'
        };
        if (type === 'Hot') {
          return (
            <span className={`inline-flex items-center rounded border ${styles[type]}`}>
              <i className="fa-solid fa-fire mr-1"></i> HOT
            </span>
          );
        }

      const AuthModal = ({ isOpen, mode, onClose, onSubmit, setMode, email, setEmail, password, setPassword, role, setRole, loading, error }) => {
          if (!isOpen) return null;
          const isSignup = mode === 'signup';
          return (
            <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
              <div className="absolute inset-0 bg-black/80 backdrop-blur-sm" onClick={onClose}></div>
              <div className="glass-panel w-full max-w-md rounded-2xl relative z-10 overflow-hidden border border-gray-700 shadow-2xl">
                <div className="p-6 border-b border-gray-700 flex justify-between items-center bg-gray-900/50">
                  <h2 className="text-lg font-bold text-white flex items-center gap-2">
                    <i className={`fa-solid ${isSignup ? 'fa-user-plus' : 'fa-right-to-bracket'} text-primary`}></i>
                    {isSignup ? 'Create account' : 'Login'}
                  </h2>
                  <button onClick={onClose} className="text-gray-400 hover:text-white transition-colors"><i className="fa-solid fa-xmark text-xl"></i></button>
                </div>
                <div className="p-6 space-y-4">
                  <div>
                    <label className="block text-xs font-medium text-gray-400 mb-1">Email</label>
                    <input type="email" value={email} onChange={e => setEmail(e.target.value)} className="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-primary" placeholder="you@example.com" required />
                  </div>
                  <div>
                    <label className="block text-xs font-medium text-gray-400 mb-1">Password</label>
                    <input type="password" value={password} onChange={e => setPassword(e.target.value)} className="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-primary" placeholder="At least 6 characters" required />
                  </div>
                  {isSignup && (
                    <div>
                      <label className="block text-xs font-medium text-gray-400 mb-1">Role</label>
                      <select value={role} onChange={e => setRole(e.target.value)} className="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-primary">
                        <option value="user">User</option>
                        <option value="admin">Admin (first admin only)</option>
                      </select>
                      <p className="text-[11px] text-gray-500 mt-1">Admin role is granted only if no admin exists yet.</p>
                    </div>
                  )}
                  {error && <div className="text-red-400 text-sm">{error}</div>}
                  <div className="flex items-center justify-between pt-2">
                    <button onClick={() => setMode(isSignup ? 'login' : 'signup')} className="text-sm text-primary hover:text-primaryDark">
                      {isSignup ? 'Already have an account? Login' : 'Need an account? Sign up'}
                    </button>
                    <button onClick={onSubmit} disabled={loading} className="bg-gradient-to-r from-primary to-secondary text-white px-5 py-2.5 rounded-xl font-semibold shadow-lg shadow-primary/20 disabled:opacity-60">
                      {loading ? 'Please wait...' : isSignup ? 'Sign up' : 'Login'}
                    </button>
                  </div>
                </div>
              </div>
            </div>
          );
      };
        return (
          <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border ${styles[type]}`}>
            {type === 'Private' ? <i className="fa-solid fa-lock mr-1.5 text-[10px]"></i> : type === 'Shared' ? <i className="fa-solid fa-users mr-1.5 text-[10px]"></i> : type === 'Hidden' ? <i className="fa-solid fa-eye-slash mr-1.5 text-[10px]"></i> : null}
            {type}
          </span>
        );
      };

      const ProductCard = ({ product, isAdmin, onEdit, onDelete, onToggleVisible, onAddToCart }) => {
        const discountPercent = Math.round(((product.originalPrice - product.discountedPrice) / product.originalPrice) * 100);
        if (!product.isVisible && !isAdmin) return null;
        return (
          <div className={`glass-panel rounded-2xl p-5 hover:scale-[1.02] transition-transform duration-300 relative group overflow-hidden flex flex-col h-full ${!product.isVisible ? 'opacity-75 grayscale' : ''}`}>
            {isAdmin && (
              <div className="absolute top-2 left-2 z-30 flex gap-2">
                <button onClick={() => onEdit(product)} className="bg-blue-600 hover:bg-blue-500 text-white w-8 h-8 rounded-lg flex items-center justify-center shadow-lg transition-colors" title="Edit Product"><i className="fa-solid fa-pen text-xs"></i></button>
                <button onClick={() => onToggleVisible(product.id)} className={`${product.isVisible ? 'bg-gray-600 hover:bg-gray-500' : 'bg-green-600 hover:bg-green-500'} text-white w-8 h-8 rounded-lg flex items-center justify-center shadow-lg transition-colors`} title={product.isVisible ? 'Hide Product' : 'Show Product'}><i className={`fa-solid ${product.isVisible ? 'fa-eye-slash' : 'fa-eye'} text-xs`}></i></button>
                <button onClick={() => { if (confirm('Are you sure you want to delete this listing?')) onDelete(product.id); }} className="bg-red-600 hover:bg-red-500 text-white w-8 h-8 rounded-lg flex items-center justify-center shadow-lg transition-colors" title="Delete Product"><i className="fa-solid fa-trash text-xs"></i></button>
              </div>
            )}
            <div className="absolute top-0 right-0 -mr-16 -mt-16 w-32 h-32 bg-primary/20 blur-[50px] rounded-full group-hover:bg-primary/30 transition-all duration-500"></div>
            <div className="flex justify-between items-start mb-4 relative z-10 pl-0 mt-2">
              <div className="flex items-center space-x-3">
                <div className="w-12 h-12 rounded-xl bg-gradient-to-br from-gray-800 to-gray-900 flex items-center justify-center border border-gray-700 shadow-lg shrink-0"><i className={`fa-solid ${product.icon || 'fa-box-open'} text-2xl text-primary`}></i></div>
                <div>
                  <h3 className="font-bold text-white text-lg leading-tight">{product.name}</h3>
                  <span className="text-gray-400 text-xs">{product.serviceType}</span>
                </div>
              </div>
              <div className="flex flex-col items-end gap-1">
                {product.isHot && <Badge type="Hot" />}
                {!product.isVisible && <Badge type="Hidden" />}
                <Badge type={product.accountType} />
              </div>
            </div>
            <p className="text-gray-400 text-sm mb-4 line-clamp-2 min-h-[40px] relative z-10">{product.description}</p>
            <div className="mb-6 flex-grow relative z-10">
              <div className="space-y-2">
                {product.features.slice(0, 3).map((feature, idx) => (
                  <div key={idx} className="flex items-center text-xs text-gray-300"><i className="fa-solid fa-check text-green-400 mr-2"></i>{feature}</div>
                ))}
                {product.features.length > 3 && <div className="text-xs text-gray-500 pl-5">+{product.features.length - 3} more features</div>}
              </div>
            </div>
            <div className="flex items-center gap-2 mb-4">
              <div className="bg-gray-800/50 p-1.5 px-2 rounded text-[10px] text-gray-400 flex items-center border border-gray-700/50 flex-1">
                <i className="fa-solid fa-circle-info text-blue-400 mr-1.5"></i><span className="truncate">{product.requirements}</span>
              </div>
              <div className={`p-1.5 px-2 rounded text-[10px] border flex items-center ${product.stock > 0 ? 'bg-emerald-900/20 text-emerald-400 border-emerald-500/20' : 'bg-red-900/20 text-red-400 border-red-500/20'}`}><i className="fa-solid fa-cubes mr-1.5"></i><span>{product.stock > 0 ? `${product.stock} Left` : 'Sold Out'}</span></div>
            </div>
            <div className="flex items-center gap-2 mb-4">
              <div className="bg-purple-900/20 p-1.5 px-2 rounded text-[10px] text-purple-400 flex items-center border border-purple-500/20 flex-1">
                <i className="fa-solid fa-clock mr-1.5"></i><span>Validity: {product.validity_months || product.validityMonths || 1} {(product.validity_months || product.validityMonths || 1) === 1 ? 'Month' : 'Months'}</span>
              </div>
            </div>
            <div className="pt-4 border-t border-gray-700/50 flex items-center justify-between relative z-10">
              <div>
                <div className="flex items-center gap-2">
                  <span className="text-gray-500 line-through text-xs">${product.originalPrice.toFixed(2)}</span>
                  <span className="text-green-400 text-xs font-bold bg-green-400/10 px-1.5 py-0.5 rounded">-{discountPercent}%</span>
                </div>
                <div className="text-2xl font-bold text-white">${product.discountedPrice.toFixed(2)}</div>
              </div>
              <button onClick={() => onAddToCart(product)} disabled={product.stock <= 0} className={`px-5 py-2.5 rounded-xl font-medium transition-colors shadow-lg flex items-center gap-2 transition-all ${product.stock > 0 ? 'bg-primary hover:bg-primaryDark text-white shadow-primary/25 hover:pl-4 hover:pr-6 group-hover:scale-105' : 'bg-gray-700 text-gray-400 cursor-not-allowed'}`}>
                {product.stock > 0 ? <>Buy Now <i className="fa-solid fa-arrow-right text-xs transition-transform group-hover:translate-x-1"></i></> : 'Out of Stock'}
              </button>
            </div>
          </div>
        );
      };

      const ProductModal = ({ isOpen, onClose, onSave, productToEdit }) => {
        const [formData, setFormData] = useState({ name: '', serviceType: '', accountType: 'Private', originalPrice: 0, discountedPrice: 0, features: [], requirements: '', description: '', isHot: false, icon: 'fa-box', stock: 10, isVisible: true });
        const [featureInput, setFeatureInput] = useState('');

        useEffect(() => {
          if (productToEdit) { setFormData(productToEdit); } else {
            setFormData({ name: '', serviceType: '', accountType: 'Private', originalPrice: 0, discountedPrice: 0, features: [], requirements: '', description: '', isHot: false, icon: 'fa-box', stock: 10, isVisible: true });
          }
        }, [productToEdit, isOpen]);

        if (!isOpen) return null;

        const handleSubmit = (e) => {
          e.preventDefault();
          if (!formData.name || !formData.originalPrice) return;
          onSave({ id: productToEdit ? productToEdit.id : Math.random().toString(36).substr(2, 9), name: formData.name, serviceType: formData.serviceType || 'Digital Service', accountType: formData.accountType, originalPrice: Number(formData.originalPrice), discountedPrice: Number(formData.discountedPrice), description: formData.description || '', features: formData.features || [], requirements: formData.requirements || 'None', isHot: !!formData.isHot, icon: formData.icon || 'fa-box', stock: Number(formData.stock), isVisible: formData.isVisible !== undefined ? formData.isVisible : true });
          onClose();
          setFeatureInput('');
        };

        const addFeature = () => {
          if (featureInput.trim()) { setFormData(prev => ({ ...prev, features: [...(prev.features || []), featureInput.trim()] })); setFeatureInput(''); }
        };

        return (
          <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div className="absolute inset-0 bg-black/80 backdrop-blur-sm" onClick={onClose}></div>
            <div className="glass-panel w-full max-w-2xl rounded-2xl relative z-10 max-h-[90vh] overflow-y-auto border border-gray-700 shadow-2xl">
              <div className="p-6 border-b border-gray-700 flex justify-between items-center bg-gray-900/50 sticky top-0 backdrop-blur-md z-20">
                <h2 className="text-xl font-bold text-white flex items-center gap-2"><i className={`fa-solid ${productToEdit ? 'fa-pen-to-square' : 'fa-plus-circle'} text-primary`}></i>{productToEdit ? 'Edit Asset' : 'List New Asset'}</h2>
                <button onClick={onClose} className="text-gray-400 hover:text-white transition-colors"><i className="fa-solid fa-xmark text-xl"></i></button>
              </div>
              <form onSubmit={handleSubmit} className="p-6 space-y-6">
                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div className="space-y-4">
                    <div>
                      <label className="block text-xs font-medium text-gray-400 mb-1">Product Name</label>
                      <input type="text" className="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-primary transition-colors" placeholder="e.g. ChatPlus Pro" value={formData.name} onChange={e => setFormData({ ...formData, name: e.target.value })} required />
                    </div>
                    <div>
                      <label className="block text-xs font-medium text-gray-400 mb-1">Service Category</label>
                      <input type="text" className="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-primary transition-colors" placeholder="e.g. AI Tools" value={formData.serviceType} onChange={e => setFormData({ ...formData, serviceType: e.target.value })} />
                    </div>
                    <div>
                      <label className="block text-xs font-medium text-gray-400 mb-1">Account Type</label>
                      <div className="grid grid-cols-2 gap-3">
                        <button type="button" onClick={() => setFormData({ ...formData, accountType: 'Private' })} className={`py-2 rounded-lg text-sm border transition-all ${formData.accountType === 'Private' ? 'bg-blue-500/20 border-blue-500 text-blue-400 shadow-[0_0_10px_rgba(59,130,246,0.2)]' : 'bg-gray-800 border-gray-700 text-gray-400'}`}>Private</button>
                        <button type="button" onClick={() => setFormData({ ...formData, accountType: 'Shared' })} className={`py-2 rounded-lg text-sm border transition-all ${formData.accountType === 'Shared' ? 'bg-orange-500/20 border-orange-500 text-orange-400 shadow-[0_0_10px_rgba(249,115,22,0.2)]' : 'bg-gray-800 border-gray-700 text-gray-400'}`}>Shared</button>
                      </div>
                    </div>
                    <div>
                      <label className="block text-xs font-medium text-gray-400 mb-1">Icon (FontAwesome Class)</label>
                      <div className="flex gap-2">
                        <div className="w-10 h-10 bg-gray-800 rounded flex items-center justify-center border border-gray-700"><i className={`fa-solid ${formData.icon} text-primary`}></i></div>
                        <input type="text" className="flex-1 bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-primary transition-colors" placeholder="e.g. fa-robot" value={formData.icon} onChange={e => setFormData({ ...formData, icon: e.target.value })} />
                      </div>
                    </div>
                  </div>
                  <div className="space-y-4">
                    <div className="grid grid-cols-2 gap-4">
                      <div>
                        <label className="block text-xs font-medium text-gray-400 mb-1">Original Price ($)</label>
                        <input type="number" step="0.01" className="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-primary transition-colors" value={formData.originalPrice || ''} onChange={e => setFormData({ ...formData, originalPrice: parseFloat(e.target.value) })} required />
                      </div>
                      <div>
                        <label className="block text-xs font-medium text-gray-400 mb-1">Discount Price ($)</label>
                        <input type="number" step="0.01" className="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-primary transition-colors" value={formData.discountedPrice || ''} onChange={e => setFormData({ ...formData, discountedPrice: parseFloat(e.target.value) })} required />
                      </div>
                    </div>
                    <div>
                      <label className="block text-xs font-medium text-gray-400 mb-1">Stock Quantity</label>
                      <input type="number" className="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-primary transition-colors" value={formData.stock} onChange={e => setFormData({ ...formData, stock: parseInt(e.target.value) || 0 })} />
                    </div>
                    <div>
                      <label className="block text-xs font-medium text-gray-400 mb-1">Short Description</label>
                      <textarea className="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-primary transition-colors h-24 resize-none" placeholder="What is included?" value={formData.description} onChange={e => setFormData({ ...formData, description: e.target.value })}></textarea>
                    </div>
                  </div>
                </div>
                <div className="border-t border-gray-700/50 pt-4">
                  <label className="block text-xs font-medium text-gray-400 mb-2">Features List</label>
                  <div className="flex gap-2 mb-3">
                    <input type="text" className="flex-1 bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-primary" placeholder="e.g. 4K Support" value={featureInput} onChange={e => setFeatureInput(e.target.value)} onKeyDown={e => { if (e.key === 'Enter') { e.preventDefault(); addFeature(); } }} />
                    <button type="button" onClick={addFeature} className="bg-gray-700 hover:bg-gray-600 text-white px-4 rounded-lg transition-colors">Add</button>
                  </div>
                  <div className="flex flex-wrap gap-2">
                    {(formData.features || []).map((f, i) => (
                      <span key={i} className="bg-gray-800 text-gray-300 text-xs px-2 py-1 rounded border border-gray-700 flex items-center">{f}<button type="button" onClick={() => setFormData(prev => ({ ...prev, features: prev.features.filter((_, idx) => idx !== i) }))} className="ml-2 text-gray-500 hover:text-red-400">&times;</button></span>
                    ))}
                  </div>
                </div>
                <div className="space-y-4 pt-2">
                  <div>
                    <label className="block text-xs font-medium text-gray-400 mb-1">Requirements</label>
                    <input type="text" className="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-primary transition-colors" placeholder="e.g. Email needed" value={formData.requirements} onChange={e => setFormData({ ...formData, requirements: e.target.value })} />
                  </div>
                  <div className="flex items-center gap-6">
                    <div className="flex items-center gap-3">
                      <input type="checkbox" id="isHot" checked={!!formData.isHot} onChange={e => setFormData({ ...formData, isHot: e.target.checked })} className="w-4 h-4 rounded border-gray-700 bg-gray-800 text-primary focus:ring-primary" />
                      <label htmlFor="isHot" className="text-sm text-gray-300 cursor-pointer select-none">Mark as Hot/Trending</label>
                    </div>
                    <div className="flex items-center gap-3">
                      <input type="checkbox" id="isVisible" checked={!!formData.isVisible} onChange={e => setFormData({ ...formData, isVisible: e.target.checked })} className="w-4 h-4 rounded border-gray-700 bg-gray-800 text-green-500 focus:ring-green-500" />
                      <label htmlFor="isVisible" className="text-sm text-gray-300 cursor-pointer select-none">Visible in Store</label>
                    </div>
                  </div>
                </div>
                <div className="pt-4 border-t border-gray-700 flex justify-end gap-3">
                  <button type="button" onClick={onClose} className="px-6 py-2.5 rounded-xl text-gray-400 hover:text-white font-medium transition-colors">Cancel</button>
                  <button type="submit" className="bg-gradient-to-r from-primary to-secondary text-white px-8 py-2.5 rounded-xl font-medium shadow-lg hover:opacity-90 transition-opacity">{productToEdit ? 'Save Changes' : 'List Item'}</button>
                </div>
              </form>
            </div>
          </div>
        );
      };

      const CartDrawer = ({ isOpen, onClose, cart, onRemove, currentUser, settings, setAuthMode, setIsAuthModalOpen }) => {
        const total = cart.reduce((sum, item) => sum + item.discountedPrice, 0);
        return (
          <>
            {isOpen && <div className="fixed inset-0 bg-black/50 z-40 backdrop-blur-sm" onClick={onClose}></div>}
            <div className={`fixed top-0 right-0 h-full w-full max-w-md bg-[#0f172a] border-l border-gray-800 z-50 transform transition-transform duration-300 ease-in-out ${isOpen ? 'translate-x-0' : 'translate-x-full'} flex flex-col`}>
              <div className="p-6 border-b border-gray-800 flex justify-between items-center bg-[#1e293b]/50">
                <h2 className="text-xl font-bold text-white flex items-center gap-2"><i className="fa-solid fa-cart-shopping text-primary"></i> Your Cart ({cart.length})</h2>
                <button onClick={onClose} className="text-gray-400 hover:text-white"><i className="fa-solid fa-xmark text-xl"></i></button>
              </div>
              <div className="flex-1 overflow-y-auto p-6 space-y-4">
                {cart.length === 0 ? (
                  <div className="h-full flex flex-col items-center justify-center text-gray-500 space-y-4"><i className="fa-solid fa-basket-shopping text-6xl opacity-20"></i><p>Your cart is empty.</p><button onClick={onClose} className="text-primary hover:text-primaryDark text-sm">Continue Shopping</button></div>
                ) : (
                  cart.map((item) => (
                    <div key={item.cartId} className="bg-[#1e293b] p-4 rounded-xl border border-gray-700 flex gap-4 relative group">
                      <div className="w-16 h-16 bg-gray-800 rounded-lg flex items-center justify-center border border-gray-700 shrink-0"><i className={`fa-solid ${item.icon} text-xl text-primary`}></i></div>
                      <div className="flex-1">
                        <h4 className="text-white font-medium">{item.name}</h4>
                        <span className="text-xs text-gray-400 bg-gray-800 px-1.5 py-0.5 rounded">{item.accountType}</span>
                        <div className="mt-2 flex justify-between items-center"><span className="text-white font-bold">${item.discountedPrice.toFixed(2)}</span></div>
                      </div>
                      <button onClick={() => onRemove(item.cartId)} className="absolute top-2 right-2 text-gray-500 hover:text-red-400 transition-colors p-2"><i className="fa-solid fa-trash-can"></i></button>
                    </div>
                  ))
                )}
              </div>
              <div className="p-6 border-t border-gray-800 bg-[#1e293b]/30">
                <div className="flex justify-between items-center mb-4"><span className="text-gray-400">Total</span><span className="text-2xl font-bold text-white">${total.toFixed(2)}</span></div>
                {currentUser ? (
                  <button 
                    onClick={() => window.location.href = 'checkout.php'} 
                    className="w-full bg-gradient-to-r from-primary to-secondary text-white py-3 rounded-xl font-bold shadow-lg shadow-primary/20 hover:opacity-90 transition-opacity disabled:opacity-50 disabled:cursor-not-allowed" 
                    disabled={cart.length === 0}
                  >
                    <i className="fa-solid fa-credit-card mr-2"></i>Checkout Now
                  </button>
                ) : (
                  <button 
                    onClick={() => { setAuthMode('login'); setIsAuthModalOpen(true); onClose(); }} 
                    className="w-full bg-gradient-to-r from-primary to-secondary text-white py-3 rounded-xl font-bold shadow-lg shadow-primary/20 hover:opacity-90 transition-opacity"
                  >
                    <i className="fa-solid fa-right-to-bracket mr-2"></i>Login to Checkout
                  </button>
                )}
                {settings?.whatsapp_number && (
                  <a 
                    href={`https://wa.me/${settings.whatsapp_number.replace(/[^0-9]/g, '')}?text=Hi, I need help with my order`}
                    target="_blank"
                    rel="noopener noreferrer"
                    className="mt-3 w-full bg-green-600 hover:bg-green-700 text-white py-2.5 rounded-xl font-medium transition-colors flex items-center justify-center gap-2"
                  >
                    <i className="fa-brands fa-whatsapp text-xl"></i>
                    Quick Support on WhatsApp
                  </a>
                )}
              </div>
            </div>
          </>
        );
      };

      const AuthModal = ({ isOpen, mode, onClose, onSubmit, setMode, email, setEmail, password, setPassword, role, setRole, loading, error }) => {
        if (!isOpen) return null;
        const isSignup = mode === 'signup';
        return (
          <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div className="absolute inset-0 bg-black/80 backdrop-blur-sm" onClick={onClose}></div>
            <div className="glass-panel w-full max-w-md rounded-2xl relative z-10 overflow-hidden border border-gray-700 shadow-2xl">
              <div className="p-6 border-b border-gray-700 flex justify-between items-center bg-gray-900/50">
                <h2 className="text-lg font-bold text-white flex items-center gap-2">
                  <i className={`fa-solid ${isSignup ? 'fa-user-plus' : 'fa-right-to-bracket'} text-primary`}></i>
                  {isSignup ? 'Create account' : 'Login'}
                </h2>
                <button onClick={onClose} className="text-gray-400 hover:text-white transition-colors"><i className="fa-solid fa-xmark text-xl"></i></button>
              </div>
              <div className="p-6 space-y-4">
                <div>
                  <label className="block text-xs font-medium text-gray-400 mb-1">Email</label>
                  <input type="email" value={email} onChange={e => setEmail(e.target.value)} className="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-primary" placeholder="you@example.com" required />
                </div>
                <div>
                  <label className="block text-xs font-medium text-gray-400 mb-1">Password</label>
                  <input type="password" value={password} onChange={e => setPassword(e.target.value)} className="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-primary" placeholder="At least 6 characters" required />
                </div>
                {isSignup && (
                  <div>
                    <label className="block text-xs font-medium text-gray-400 mb-1">Role</label>
                    <select value={role} onChange={e => setRole(e.target.value)} className="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-primary">
                      <option value="user">User</option>
                      <option value="admin">Admin (first admin only)</option>
                    </select>
                    <p className="text-[11px] text-gray-500 mt-1">Admin role is granted only if no admin exists yet.</p>
                  </div>
                )}
                {error && <div className="text-red-400 text-sm">{error}</div>}
                <div className="flex items-center justify-between pt-2">
                  <button onClick={() => setMode(isSignup ? 'login' : 'signup')} className="text-sm text-primary hover:text-primaryDark">
                    {isSignup ? 'Already have an account? Login' : 'Need an account? Sign up'}
                  </button>
                  <button onClick={onSubmit} disabled={loading} className="bg-gradient-to-r from-primary to-secondary text-white px-5 py-2.5 rounded-xl font-semibold shadow-lg shadow-primary/20 disabled:opacity-60">
                    {loading ? 'Please wait...' : isSignup ? 'Sign up' : 'Login'}
                  </button>
                </div>
              </div>
            </div>
          </div>
        );
      };

      const App = () => {
        const [products, setProducts] = useState(DEFAULT_PRODUCTS);
        const [storeId, setStoreId] = useState(null);
        const [cart, setCart] = useState([]);
        const [currentUser, setCurrentUser] = useState(null);
        const [isLoading, setIsLoading] = useState(false);
        const [isSyncing, setIsSyncing] = useState(false);
        const [filter, setFilter] = useState('All');
        const [isModalOpen, setIsModalOpen] = useState(false);
        const [isCartOpen, setIsCartOpen] = useState(false);
        const [isMenuOpen, setIsMenuOpen] = useState(false);
        const [searchTerm, setSearchTerm] = useState('');
        const [editingProduct, setEditingProduct] = useState(null);
        const [toasts, setToasts] = useState([]);
        const [isAuthModalOpen, setIsAuthModalOpen] = useState(false);
        const [authMode, setAuthMode] = useState('login');
        const [authEmail, setAuthEmail] = useState('');
        const [authPassword, setAuthPassword] = useState('');
        const [authRole, setAuthRole] = useState('user');
        const [authError, setAuthError] = useState('');
        const [authLoading, setAuthLoading] = useState(false);
        const [settings, setSettings] = useState(null);

        const isAdmin = currentUser?.role === 'admin';

        const fetchCurrentUser = useCallback(async () => {
          try {
            const res = await fetch('auth.php?action=me');
            const data = await res.json();
            if (data && data.email) {
              setCurrentUser({ email: data.email, role: data.role });
            }
          } catch (err) {
            console.warn('Auth check failed', err);
          }
        }, []);

        const loadProducts = async () => {
          setIsLoading(true);
          try {
            const res = await fetch('products.php?action=list');
            const data = await res.json();
            if (Array.isArray(data)) {
              setProducts(data);
            }
          } catch (err) {
            console.warn('Failed to load products', err);
            setProducts(DEFAULT_PRODUCTS);
          } finally {
            setIsLoading(false);
          }
        };

        const loadSettings = async () => {
          try {
            const res = await fetch('settings.php?action=get');
            const data = await res.json();
            if (data && !data.error) {
              setSettings(data);
              // Use meta_title for SEO, fallback to site_name
              document.title = data.meta_title || (data.site_name ? data.site_name + ' - Premium Digital Assets' : 'DigiMarket');
              if (data.favicon_url) {
                let link = document.querySelector("link[rel~='icon']");
                if (!link) {
                  link = document.createElement('link');
                  link.rel = 'icon';
                  document.head.appendChild(link);
                }
                link.href = data.favicon_url;
              }
            }
          } catch (err) {
            console.warn('Failed to load settings', err);
          }
        };

        useEffect(() => {
          const urlParams = new URLSearchParams(window.location.search);
          const id = urlParams.get('storeId');
          const localCart = window.localStorage.getItem('digimarket_cart');
          if (localCart) setCart(JSON.parse(localCart));
          setStoreId(id);
          fetchCurrentUser();
          loadProducts();
          loadSettings();
        }, [fetchCurrentUser]);

        useEffect(() => {
          if (!storeId) return;
          const interval = setInterval(() => { loadStore(storeId, true); }, SYNC_INTERVAL);
          return () => clearInterval(interval);
        }, [storeId]);

        useEffect(() => { window.localStorage.setItem('digimarket_cart', JSON.stringify(cart)); }, [cart]);
        useEffect(() => { if (!storeId) { window.localStorage.setItem('digimarket_products', JSON.stringify(products)); } }, [products, storeId]);

        const addToast = (message, type) => { const id = Date.now(); setToasts(prev => [...prev, { id, message, type }]); };
        const removeToast = (id) => setToasts(prev => prev.filter(t => t.id !== id));

        const loadStore = async (id, silent = false) => {
          if (!silent) setIsLoading(true);
          try { const data = await CloudService.getStore(id); setProducts(data.products); if (!silent) addToast('Store loaded from cloud', 'success'); }
          catch (err) { addToast('Failed to load store data', 'error'); }
          finally { if (!silent) setIsLoading(false); }
        };

        const syncToCloud = async (newProducts) => {
          if (!storeId) return;
          setIsSyncing(true);
          try { await CloudService.updateStore(storeId, { name: 'My Digital Store', products: newProducts, lastUpdated: Date.now() }); }
          catch (err) { addToast('Sync failed', 'error'); }
          finally { setIsSyncing(false); }
        };

        const createCloudStore = async () => {
          setIsLoading(true);
          try {
            const id = await CloudService.createStore({ name: 'My New Digital Store', products: products, lastUpdated: Date.now() });
            setStoreId(id);
            const url = new URL(window.location.href); url.searchParams.set('storeId', id); window.history.pushState({}, '', url);
            addToast('Global Store Created! You are now live.', 'success');
          } catch (err) { addToast('Failed to go live', 'error'); }
          finally { setIsLoading(false); }
        };

        const handleAuthSubmit = async () => {
          setAuthLoading(true);
          setAuthError('');
          try {
            const endpoint = authMode === 'signup' ? 'signup' : 'login';
            const res = await fetch(`auth.php?action=${endpoint}`, {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({ email: authEmail, password: authPassword, role: authRole })
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'Authentication failed');
            setCurrentUser({ email: data.email, role: data.role });
            addToast(authMode === 'signup' ? 'Account created' : 'Logged in', 'success');
            setIsAuthModalOpen(false);
            setAuthPassword('');
          } catch (err) {
            setAuthError(err.message || 'Authentication failed');
          } finally {
            setAuthLoading(false);
          }
        };

        const handleLogout = async () => {
          try {
            await fetch('auth.php?action=logout', { method: 'POST' });
          } catch (err) {
            console.warn('Logout failed', err);
          }
          setCurrentUser(null);
          addToast('Logged out', 'info');
        };

        const handleAddProduct = async (newProduct) => {
          try {
            const res = await fetch('products.php?action=create', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify(newProduct)
            });
            if (!res.ok) throw new Error('Failed to create product');
            addToast('Product created', 'success');
            loadProducts();
          } catch (err) {
            addToast('Failed to create product', 'error');
          }
        };

        const handleUpdateProduct = async (updatedProduct) => {
          try {
            const res = await fetch('products.php?action=update', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify(updatedProduct)
            });
            if (!res.ok) throw new Error('Failed to update product');
            addToast('Product updated', 'success');
            loadProducts();
          } catch (err) {
            addToast('Failed to update product', 'error');
          }
        };

        const handleDeleteProduct = async (id) => {
          try {
            const res = await fetch(`products.php?action=delete&id=${id}`, { method: 'POST' });
            if (!res.ok) throw new Error('Failed to delete product');
            addToast('Product deleted', 'success');
            loadProducts();
          } catch (err) {
            addToast('Failed to delete product', 'error');
          }
        };

        const handleToggleVisible = async (id) => {
          try {
            const res = await fetch(`products.php?action=toggle_visible&id=${id}`, { method: 'POST' });
            if (!res.ok) throw new Error('Failed to toggle visibility');
            loadProducts();
          } catch (err) {
            addToast('Failed to toggle visibility', 'error');
          }
        };
        const addToCart = (product) => { setCart([...cart, { ...product, cartId: Math.random().toString(36) }]); setIsCartOpen(true); addToast(`Added ${product.name} to cart`, 'success'); };
        const removeFromCart = (cartId) => setCart(cart.filter(item => item.cartId !== cartId));
        const openEditModal = (product) => { setEditingProduct(product); setIsModalOpen(true); };
        const openAddModal = () => { setEditingProduct(null); setIsModalOpen(true); };
        const copyStoreLink = () => { navigator.clipboard.writeText(window.location.href); addToast('Store link copied to clipboard!', 'success'); };

        const filteredProducts = products.filter(p => {
          const matchesFilter = filter === 'All' || p.accountType === filter;
          const search = searchTerm.toLowerCase();
          const matchesSearch = p.name.toLowerCase().includes(search) || p.serviceType.toLowerCase().includes(search);
          return matchesFilter && matchesSearch;
        });

        const totalStock = products.reduce((acc, p) => acc + p.stock, 0);
        const totalValue = products.reduce((acc, p) => acc + (p.stock * p.discountedPrice), 0);

        return (
          <div className={`min-h-screen pb-20 transition-colors duration-500 ${isAdmin ? 'border-t-4 border-red-500' : ''}`}>
            <div className="fixed bottom-0 right-0 p-4 z-50 flex flex-col gap-2">
              {toasts.map(t => (<Toast key={t.id} message={t.message} type={t.type} onClose={() => removeToast(t.id)} />))}
            </div>
            <nav className="fixed top-0 w-full z-40 glass-panel border-b-0 border-b-white/5">
              <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <div className="flex items-center gap-2">
                  <div className={`w-8 h-8 rounded bg-gradient-to-tr ${isAdmin ? 'from-red-500 to-orange-500' : 'from-primary to-secondary'} flex items-center justify-center text-white font-bold transition-all duration-500`}>{isAdmin ? <i className="fa-solid fa-lock-open text-xs"></i> : (settings?.site_name || 'DigiMarket').charAt(0).toUpperCase()}</div>
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
                {currentUser ? (
                  <div className="mb-6 p-4 bg-gray-800 rounded-lg border border-gray-700">
                    <div className="text-sm text-gray-400">Logged in as</div>
                    <div className="text-white font-semibold">{currentUser.email}</div>
                    <div className={`text-xs mt-1 ${isAdmin ? 'text-red-400' : 'text-gray-500'}`}>{isAdmin ? 'Admin' : 'User'}</div>
                  </div>
                ) : null}

                <div className="space-y-2">
                  <button 
                    onClick={() => setIsCartOpen(true)} 
                    className="w-full flex items-center justify-between p-3 bg-gray-800 hover:bg-gray-700 rounded-lg transition-colors text-left"
                  >
                    <span className="flex items-center gap-3">
                      <i className="fa-solid fa-cart-shopping text-primary"></i>
                      <span className="text-white">Shopping Cart</span>
                    </span>
                    {cart.length > 0 && (
                      <span className="bg-primary text-white text-xs font-bold px-2 py-0.5 rounded-full">{cart.length}</span>
                    )}
                  </button>

                  {currentUser && (
                    <button 
                      onClick={() => window.location.href = 'user_orders.php'} 
                      className="w-full flex items-center gap-3 p-3 bg-gray-800 hover:bg-gray-700 rounded-lg transition-colors text-left"
                    >
                      <i className="fa-solid fa-receipt text-primary"></i>
                      <span className="text-white">My Orders</span>
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
                  <div className="space-y-2">
                    <button 
                      onClick={() => { setAuthMode('login'); setIsAuthModalOpen(true); setIsMenuOpen(false); }} 
                      className="w-full px-4 py-3 bg-gray-800 hover:bg-gray-700 text-white rounded-lg transition-colors"
                    >
                      Login
                    </button>
                    <button 
                      onClick={() => { setAuthMode('signup'); setIsAuthModalOpen(true); setIsMenuOpen(false); }} 
                      className="w-full px-4 py-3 bg-primary hover:bg-primaryDark text-white rounded-lg transition-colors"
                    >
                      Sign up
                    </button>
                  </div>
                )}
              </div>
            </div>

            {isAdmin && (
              <div className="pt-24 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-4">
                <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                  <div className="glass-panel p-4 rounded-xl border border-red-500/20 bg-red-500/5"><div className="text-gray-400 text-xs uppercase tracking-wider mb-1">Total Inventory</div><div className="text-2xl font-bold text-white">{totalStock} <span className="text-sm font-normal text-gray-500">items</span></div></div>
                  <div className="glass-panel p-4 rounded-xl border border-red-500/20 bg-red-500/5"><div className="text-gray-400 text-xs uppercase tracking-wider mb-1">Potential Revenue</div><div className="text-2xl font-bold text-white">${totalValue.toFixed(2)}</div></div>
                  <div className="glass-panel p-4 rounded-xl border border-red-500/20 bg-red-500/5 cursor-pointer" onClick={storeId ? copyStoreLink : createCloudStore}>
                    {storeId ? (
                      <div className="flex flex-col h-full justify-center gap-1 cursor-pointer"><div className="text-emerald-400 text-xs uppercase tracking-wider mb-1 flex items-center gap-2"><span className="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>Online Global</div><div className="text-sm font-bold text-white truncate underline decoration-dotted">Click to Copy Link</div></div>
                    ) : (
                      <div className="flex flex-col items-center justify-center h-full text-blue-400 gap-1 cursor-pointer hover:text-blue-300"><i className="fa-solid fa-globe text-xl"></i><span className="font-bold text-sm">Go Live Globally</span></div>
                    )}
                  </div>
                  <div className="glass-panel p-4 rounded-xl border border-red-500/20 bg-red-500/5 cursor-pointer hover:bg-red-500/10 transition-colors" onClick={openAddModal}><div className="flex flex-col items-center justify-center h-full text-red-400 gap-2"><i className="fa-solid fa-circle-plus text-2xl"></i><span className="font-bold text-sm">Create Listing</span></div></div>
                </div>
              </div>
            )}

            <header className={`${isAdmin ? 'pt-8' : 'pt-32'} pb-16 px-4 relative overflow-hidden transition-all duration-500`}>
              <div className="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[400px] bg-primary/20 blur-[100px] rounded-full -z-10"></div>
              <div className="max-w-4xl mx-auto text-center">
                {!isAdmin && (
                  <>
                    <span className="inline-block py-1 px-3 rounded-full bg-primary/10 border border-primary/20 text-primary text-xs font-semibold tracking-wide mb-6 animate-pulse">PREMIUM DIGITAL ASSETS</span>
                    <h1 className="text-5xl md:text-6xl font-bold text-white mb-6 leading-tight">Discover Premium <br /><span className="gradient-text">Digital Subscriptions</span></h1>
                    <p className="text-gray-400 text-lg md:text-xl max-w-2xl mx-auto mb-10">Access world-class tools and entertainment at a fraction of the cost. Secure, verified, and instant delivery.</p>
                    <div className="inline-flex flex-wrap justify-center gap-4 md:gap-12 p-4 rounded-2xl glass-panel">
                      <div className="text-center px-4"><div className="text-2xl font-bold text-white">{products.filter(p => p.isVisible).length}+</div><div className="text-xs text-gray-500 uppercase tracking-wider">Active Listings</div></div>
                      <div className="w-px bg-gray-700 hidden md:block"></div>
                      <div className="text-center px-4"><div className="text-2xl font-bold text-white">2.4k+</div><div className="text-xs text-gray-500 uppercase tracking-wider">Happy Clients</div></div>
                      <div className="w-px bg-gray-700 hidden md:block"></div>
                      <div className="text-center px-4"><div className="text-2xl font-bold text-white">Instant</div><div className="text-xs text-gray-500 uppercase tracking-wider">Delivery</div></div>
                    </div>
                  </>
                )}
              </div>
            </header>

            <main className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
              <div className="flex flex-col md:flex-row justify-between items-center mb-10 gap-4">
                <div className="flex bg-gray-900/80 p-1 rounded-xl border border-gray-800">
                  {['All', 'Private', 'Shared'].map((type) => (
                    <button key={type} onClick={() => setFilter(type)} className={`px-6 py-2 rounded-lg text-sm font-medium transition-all ${filter === type ? 'bg-gray-800 text-white shadow-sm' : 'text-gray-400 hover:text-gray-200'}`}>{type}</button>
                  ))}
                </div>
                <div className="relative w-full md:w-72">
                  <i className="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-500"></i>
                  <input type="text" placeholder="Search services..." value={searchTerm} onChange={(e) => setSearchTerm(e.target.value)} className="w-full bg-gray-900/50 border border-gray-800 rounded-xl py-2.5 pl-10 pr-4 text-white focus:outline-none focus:border-primary/50 transition-colors" />
                </div>
              </div>

              {isLoading && (
                <div className="flex justify-center items-center py-20">
                  <div className="flex flex-col items-center gap-4"><div className="w-10 h-10 border-4 border-primary border-t-transparent rounded-full animate-spin"></div><div className="text-gray-400 animate-pulse">Loading Global Store...</div></div>
                </div>
              )}

              {!isLoading && (
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                  {filteredProducts.map(product => (
                    <ProductCard key={product.id} product={product} isAdmin={isAdmin} onEdit={openEditModal} onDelete={handleDeleteProduct} onToggleVisible={handleToggleVisible} onAddToCart={addToCart} />
                  ))}
                </div>
              )}

              {!isLoading && filteredProducts.length === 0 && (
                <div className="text-center py-20 text-gray-500"><i className="fa-solid fa-ghost text-4xl mb-4 opacity-50"></i><p>No products found matching your criteria.</p></div>
              )}
            </main>

            <footer className="mt-12 border-t border-gray-800 py-4 text-center">
              <div className="flex flex-wrap items-center justify-center gap-3 text-xs text-gray-500">
                <span>© 2024 {settings?.site_name || 'DigiMarket'}</span>
                {settings && (settings.facebook_url || settings.twitter_url || settings.instagram_url || settings.whatsapp_number) && (
                  <>
                    <span className="hidden sm:inline">•</span>
                    <div className="flex gap-3">
                      {settings.facebook_url && <a href={settings.facebook_url} target="_blank" rel="noopener noreferrer" className="hover:text-primary transition-colors"><i className="fa-brands fa-facebook"></i></a>}
                      {settings.twitter_url && <a href={settings.twitter_url} target="_blank" rel="noopener noreferrer" className="hover:text-primary transition-colors"><i className="fa-brands fa-twitter"></i></a>}
                      {settings.instagram_url && <a href={settings.instagram_url} target="_blank" rel="noopener noreferrer" className="hover:text-primary transition-colors"><i className="fa-brands fa-instagram"></i></a>}
                      {settings.whatsapp_number && <a href={`https://wa.me/${settings.whatsapp_number.replace(/[^0-9]/g, '')}`} target="_blank" rel="noopener noreferrer" className="hover:text-primary transition-colors"><i className="fa-brands fa-whatsapp"></i></a>}
                    </div>
                  </>
                )}
              </div>
            </footer>

            <ProductModal isOpen={isModalOpen} onClose={() => setIsModalOpen(false)} onSave={editingProduct ? handleUpdateProduct : handleAddProduct} productToEdit={editingProduct} />
            <CartDrawer isOpen={isCartOpen} onClose={() => setIsCartOpen(false)} cart={cart} onRemove={removeFromCart} currentUser={currentUser} settings={settings} setAuthMode={setAuthMode} setIsAuthModalOpen={setIsAuthModalOpen} />
            <AuthModal isOpen={isAuthModalOpen} mode={authMode} onClose={() => setIsAuthModalOpen(false)} onSubmit={handleAuthSubmit} setMode={setAuthMode} email={authEmail} setEmail={setAuthEmail} password={authPassword} setPassword={setAuthPassword} role={authRole} setRole={setAuthRole} loading={authLoading} error={authError} />
          </div>
        );
      };

      const root = createRoot(document.getElementById('root'));
      root.render(<App />);
    </script>
  </body>
</html>
