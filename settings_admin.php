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
    <title>Settings - Admin Panel</title>
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
<body className="bg-gray-900 text-white min-h-screen">
    <div id="root"></div>

    <script src="https://unpkg.com/react@18/umd/react.production.min.js"></script>
    <script src="https://unpkg.com/react-dom@18/umd/react-dom.production.min.js"></script>
    <script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>

    <script type="text/babel" data-presets="env,react">
      const { useState, useEffect } = React;

      <?php include 'admin_layout_component.php'; ?>

      const SettingsView = () => {
        const [settings, setSettings] = useState({});
        const [loading, setLoading] = useState(true);

        useEffect(() => {
          loadSettings();
        }, []);

        const loadSettings = async () => {
          try {
            const res = await fetch('settings.php?action=get');
            const data = await res.json();
            if (!data.error) {
              setSettings(data);
            }
          } catch (err) {
            console.error('Failed to load settings', err);
          } finally {
            setLoading(false);
          }
        };

        const handleSubmit = async (e) => {
          e.preventDefault();
          const formData = new FormData(e.target);
          const data = {};
          formData.forEach((value, key) => {
            data[key] = value;
          });
          
          try {
            const res = await fetch('settings.php?action=update', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify(data)
            });
            const result = await res.json();
            if (result.success) {
              alert('Settings saved successfully');
              loadSettings();
            } else {
              alert(result.error || 'Failed to save settings');
            }
          } catch (err) {
            alert('Failed to save settings');
          }
        };

        if (loading) {
          return (
            <div className="flex items-center justify-center h-full">
              <i className="fas fa-spinner fa-spin text-4xl text-primary"></i>
            </div>
          );
        }

        return (
          <div className="p-4 sm:p-6">
            <div className="mb-6 sm:mb-8">
              <h1 className="text-2xl sm:text-3xl font-bold text-white mb-2">Site Settings</h1>
              <p className="text-sm sm:text-base text-gray-400">Configure your marketplace settings and integrations</p>
            </div>

            <form onSubmit={handleSubmit} className="space-y-6">
            {/* General Settings */}
            <div className="bg-gray-800 border border-gray-700 rounded-2xl p-6">
                <h2 className="text-xl font-bold text-white mb-4 flex items-center gap-2">
                    <i className="fa-solid fa-globe text-primary"></i> General Information
                </h2>
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label className="block text-xs font-medium text-gray-400 mb-1">Site Name</label>
                        <input type="text" name="site_name" defaultValue={settings.site_name || ""} className="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-primary" placeholder="DigiMarket" />
                    </div>
                    <div>
                        <label className="block text-xs font-medium text-gray-400 mb-1">Support Email</label>
                        <input type="email" name="support_email" defaultValue={settings.support_email || ""} className="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-primary" placeholder="support@example.com" />
                    </div>
                    <div className="md:col-span-2">
                        <label className="block text-xs font-medium text-gray-400 mb-1">Site Description</label>
                        <textarea name="site_description" rows="2" defaultValue={settings.site_description || ""} className="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-primary" placeholder="Premium Digital Assets Marketplace"></textarea>
                    </div>
                </div>
            </div>

            {/* Contact Settings */}
            <div className="bg-gray-800 border border-gray-700 rounded-2xl p-6">
                <h2 className="text-xl font-bold text-white mb-4 flex items-center gap-2">
                    <i className="fa-solid fa-phone text-primary"></i> Contact Information
                </h2>
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label className="block text-xs font-medium text-gray-400 mb-1">Mobile Number</label>
                        <input type="text" name="mobile_number" defaultValue={settings.mobile_number || ""} className="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-primary" placeholder="+1 234 567 8900" />
                    </div>
                    <div>
                        <label className="block text-xs font-medium text-gray-400 mb-1">WhatsApp Number</label>
                        <input type="text" name="whatsapp_number" defaultValue={settings.whatsapp_number || ""} className="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-primary" placeholder="+1 234 567 8900" />
                    </div>
                </div>
            </div>

            {/* Payment Settings */}
            <div className="bg-gray-800 border border-gray-700 rounded-2xl p-6">
                <h2 className="text-xl font-bold text-white mb-4 flex items-center gap-2">
                    <i className="fa-brands fa-paypal text-primary"></i> Payment Integration
                </h2>
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label className="block text-xs font-medium text-gray-400 mb-1">PayPal ID / Email</label>
                        <input type="text" name="paypal_id" defaultValue={settings.paypal_id || ""} className="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-primary" placeholder="your@paypal.com" />
                    </div>
                    <div>
                        <label className="block text-xs font-medium text-gray-400 mb-1">Currency Symbol</label>
                        <input type="text" name="currency_symbol" defaultValue={settings.currency_symbol || ""} className="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-primary" placeholder="$" />
                    </div>
                </div>
            </div>

            {/* Social Media */}
            <div className="bg-gray-800 border border-gray-700 rounded-2xl p-6">
                <h2 className="text-xl font-bold text-white mb-4 flex items-center gap-2">
                    <i className="fa-solid fa-share-nodes text-primary"></i> Social Media Links
                </h2>
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label className="block text-xs font-medium text-gray-400 mb-1"><i className="fa-brands fa-facebook mr-2"></i>Facebook URL</label>
                        <input type="url" name="facebook_url" defaultValue={settings.facebook_url || ""} className="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-primary" placeholder="https://facebook.com/yourpage" />
                    </div>
                    <div>
                        <label className="block text-xs font-medium text-gray-400 mb-1"><i className="fa-brands fa-twitter mr-2"></i>Twitter URL</label>
                        <input type="url" name="twitter_url" defaultValue={settings.twitter_url || ""} className="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-primary" placeholder="https://twitter.com/yourhandle" />
                    </div>
                    <div>
                        <label className="block text-xs font-medium text-gray-400 mb-1"><i className="fa-brands fa-instagram mr-2"></i>Instagram URL</label>
                        <input type="url" name="instagram_url" defaultValue={settings.instagram_url || ""} className="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-primary" placeholder="https://instagram.com/yourprofile" />
                    </div>
                    <div>
                        <label className="block text-xs font-medium text-gray-400 mb-1"><i className="fa-brands fa-linkedin mr-2"></i>LinkedIn URL</label>
                        <input type="url" name="linkedin_url" defaultValue={settings.linkedin_url || ""} className="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-primary" placeholder="https://linkedin.com/company/yourcompany" />
                    </div>
                    <div>
                        <label className="block text-xs font-medium text-gray-400 mb-1"><i className="fa-brands fa-youtube mr-2"></i>YouTube URL</label>
                        <input type="url" name="youtube_url" defaultValue={settings.youtube_url || ""} className="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-primary" placeholder="https://youtube.com/@yourchannel" />
                    </div>
                </div>
            </div>

            {/* SEO Settings */}
            <div className="bg-gray-800 border border-gray-700 rounded-2xl p-6">
                <h2 className="text-xl font-bold text-white mb-4 flex items-center gap-2">
                    <i className="fa-solid fa-search text-primary"></i> SEO & Meta Tags
                </h2>
                <div className="space-y-4">
                    <div>
                        <label className="block text-xs font-medium text-gray-400 mb-1">Meta Title</label>
                        <input type="text" name="meta_title" defaultValue={settings.meta_title || ""} className="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-primary" placeholder="DigiMarket - Premium Digital Assets" />
                    </div>
                    <div>
                        <label className="block text-xs font-medium text-gray-400 mb-1">Meta Description</label>
                        <textarea name="meta_description" rows="2" defaultValue={settings.meta_description || ""} className="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-primary" placeholder="Discover premium digital subscriptions and assets"></textarea>
                    </div>
                    <div>
                        <label className="block text-xs font-medium text-gray-400 mb-1">Meta Keywords</label>
                        <input type="text" name="meta_keywords" defaultValue={settings.meta_keywords || ""} className="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-primary" placeholder="digital assets, marketplace, subscriptions" />
                    </div>
                    <div>
                        <label className="block text-xs font-medium text-gray-400 mb-1">OG Image URL (Social Share)</label>
                        <input type="url" name="og_image_url" defaultValue={settings.og_image_url || ""} className="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-primary" placeholder="https://yoursite.com/og-image.jpg" />
                    </div>
                </div>
            </div>

            {/* Branding */}
            <div className="bg-gray-800 border border-gray-700 rounded-2xl p-6">
                <h2 className="text-xl font-bold text-white mb-4 flex items-center gap-2">
                    <i className="fa-solid fa-palette text-primary"></i> Branding
                </h2>
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label className="block text-xs font-medium text-gray-400 mb-1">Logo URL</label>
                        <input type="url" name="logo_url" defaultValue={settings.logo_url || ""} className="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-primary" placeholder="https://yoursite.com/logo.png" />
                    </div>
                    <div>
                        <label className="block text-xs font-medium text-gray-400 mb-1">Favicon URL</label>
                        <input type="url" name="favicon_url" defaultValue={settings.favicon_url || ""} className="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-primary" placeholder="https://yoursite.com/favicon.ico" />
                    </div>
                </div>
            </div>

            {/* Analytics */}
            <div className="bg-gray-800 border border-gray-700 rounded-2xl p-6">
                <h2 className="text-xl font-bold text-white mb-4 flex items-center gap-2">
                    <i className="fa-solid fa-chart-line text-primary"></i> Analytics & Tracking
                </h2>
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label className="block text-xs font-medium text-gray-400 mb-1">Google Analytics ID</label>
                        <input type="text" name="google_analytics_id" defaultValue={settings.google_analytics_id || ""} className="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-primary" placeholder="G-XXXXXXXXXX" />
                    </div>
                    <div>
                        <label className="block text-xs font-medium text-gray-400 mb-1">Facebook Pixel ID</label>
                        <input type="text" name="facebook_pixel_id" defaultValue={settings.facebook_pixel_id || ""} className="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-primary" placeholder="123456789012345" />
                    </div>
                </div>
                <div>
                    <label className="block text-xs font-medium text-gray-400 mb-1">Custom Analytics Code (Header)</label>
                    <textarea 
                        name="custom_analytics_code" 
                        defaultValue={settings.custom_analytics_code || ""} 
                        className="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-primary font-mono text-sm" 
                        rows="6"
                        placeholder="<script>&#10;  // Add your custom analytics, tracking, or third-party scripts here&#10;</script>"
                    ></textarea>
                    <p className="text-xs text-gray-500 mt-1">This code will be inserted in the {'<head>'} section of all pages. Perfect for custom analytics, tag managers, or conversion tracking pixels.</p>
                </div>
            </div>

            {/* Legal Pages */}
            <div className="bg-gray-800 border border-gray-700 rounded-2xl p-6">
                <h2 className="text-xl font-bold text-white mb-4 flex items-center gap-2">
                    <i className="fa-solid fa-file-contract text-primary"></i> Legal Pages
                </h2>
                <div className="space-y-4">
                    <div>
                        <label className="block text-xs font-medium text-gray-400 mb-1">Terms & Conditions URL</label>
                        <input type="url" name="terms_url" defaultValue={settings.terms_url || ""} className="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-primary" placeholder="https://yoursite.com/terms" />
                    </div>
                    <div>
                        <label className="block text-xs font-medium text-gray-400 mb-1">Privacy Policy URL</label>
                        <input type="url" name="privacy_url" defaultValue={settings.privacy_url || ""} className="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-primary" placeholder="https://yoursite.com/privacy" />
                    </div>
                    <div>
                        <label className="block text-xs font-medium text-gray-400 mb-1">Refund Policy URL</label>
                        <input type="url" name="refund_policy_url" defaultValue={settings.refund_policy_url || ""} className="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-primary" placeholder="https://yoursite.com/refunds" />
                    </div>
                </div>
            </div>

            {/* System Settings */}
            <div className="bg-gray-800 border border-gray-700 rounded-2xl p-6">
                <h2 className="text-xl font-bold text-white mb-4 flex items-center gap-2">
                    <i className="fa-solid fa-server text-primary"></i> System Settings
                </h2>
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label className="block text-xs font-medium text-gray-400 mb-1">Timezone</label>
                        <input type="text" name="timezone" defaultValue={settings.timezone || ""} className="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-primary" placeholder="UTC" />
                    </div>
                    <div className="flex items-center gap-3 pt-6">
                        <input type="checkbox" name="maintenance_mode" id="maintenance_mode" defaultChecked={settings.maintenance_mode} className="w-4 h-4 rounded border-gray-700 bg-gray-800 text-red-500 focus:ring-red-500" />
                        <label htmlFor="maintenance_mode" className="text-sm text-gray-300 cursor-pointer select-none">Enable Maintenance Mode</label>
                    </div>
                </div>
            </div>

            {/* Action Buttons */}
            <div className="flex justify-end gap-3">
                <button type="button" id="reset-btn" className="px-6 py-3 rounded-xl text-gray-400 hover:text-white font-medium transition-colors border border-gray-700 hover:border-gray-600">
                    <i className="fa-solid fa-rotate-left mr-2"></i>Reset to Defaults
                </button>
                <button type="submit" id="save-btn" className="bg-gradient-to-r from-primary to-secondary text-white px-8 py-3 rounded-xl font-semibold shadow-lg hover:opacity-90 transition-opacity">
                    <i className="fa-solid fa-save mr-2"></i>Save Settings
                </button>
              </div>
            </form>
          </div>
        );
      };

      const App = () => {
        const [currentUser, setCurrentUser] = React.useState(null);
        const [stats, setStats] = React.useState({});

        React.useEffect(() => {
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
            currentPage="admin_settings.php"
            currentUser={currentUser}
            stats={stats}
            menuItems={menuItems}
          >
            <SettingsView />
          </AdminLayout>
        );
      };

      ReactDOM.render(<App />, document.getElementById('root'));
    </script>
</body>
</html>

