<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Checkout - DigiMarket</title>
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

  <script type="text/babel">
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

    const App = () => {
      const [cart, setCart] = useState([]);
      const [gateways, setGateways] = useState([]);
      const [selectedGateway, setSelectedGateway] = useState(null);
      const [country, setCountry] = useState('');
      const [city, setCity] = useState('');
      const [postalCode, setPostalCode] = useState('');
      const [isSubmitting, setIsSubmitting] = useState(false);
      const [toast, setToast] = useState(null);
      const [settings, setSettings] = useState(null);

      useEffect(() => {
        // Load cart from localStorage
        const savedCart = localStorage.getItem('digimarket_cart');
        if (savedCart) {
          setCart(JSON.parse(savedCart));
        } else {
          window.location.href = 'index.php';
        }

        // Load active gateways
        fetch('payment_gateways.php?action=list')
          .then(res => res.json())
          .then(data => {
            const active = Array.isArray(data) ? data.filter(g => g.is_active == 1) : [];
            setGateways(active);
            if (active.length > 0) setSelectedGateway(active[0]);
          })
          .catch(err => console.error('Failed to load gateways', err));

        // Load settings
        fetch('settings.php?action=get')
          .then(res => res.json())
          .then(data => {
            if (data && !data.error) {
              setSettings(data);
            }
          })
          .catch(err => console.warn('Failed to load settings', err));
      }, []);

      const showToast = (message, type = 'success') => {
        setToast({ message, type });
        setTimeout(() => setToast(null), 4000);
      };

      const calculateSubtotal = () => {
        return cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
      };

      const calculateFee = () => {
        if (!selectedGateway || selectedGateway.fee_value == 0) return 0;
        const subtotal = calculateSubtotal();
        if (selectedGateway.fee_type === 'percentage') {
          return (subtotal * selectedGateway.fee_value) / 100;
        }
        return parseFloat(selectedGateway.fee_value);
      };

      const calculateTotal = () => {
        return calculateSubtotal() + calculateFee();
      };

      const handleSubmit = async (e) => {
        e.preventDefault();
        
        if (!selectedGateway) {
          showToast('Please select a payment method', 'error');
          return;
        }

        if (cart.length === 0) {
          showToast('Cart is empty', 'error');
          return;
        }

        setIsSubmitting(true);

        try {
          const orderData = {
            products: cart,
            total_amount: calculateTotal().toFixed(2),
            payment_gateway_id: selectedGateway.id,
            country,
            city,
            postal_code: postalCode
          };

          const res = await fetch('orders.php?action=create', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(orderData)
          });

          const data = await res.json();

          if (data.success) {
            localStorage.removeItem('digimarket_cart');
            showToast('Order placed successfully!');
            setTimeout(() => {
              window.location.href = 'user_orders.html';
            }, 1500);
          } else {
            showToast(data.error || 'Failed to place order', 'error');
          }
        } catch (err) {
          showToast('Failed to place order', 'error');
        } finally {
          setIsSubmitting(false);
        }
      };

      const currency = settings?.currency || '$';

      return (
        <div className="min-h-screen bg-gray-900 p-4">
          <div className="max-w-5xl mx-auto">
            <div className="mb-6">
              <button onClick={() => window.location.href = 'index.php'} className="text-gray-400 hover:text-white transition-colors">
                <i className="fas fa-arrow-left mr-2"></i>Back to Store
              </button>
            </div>

            <h1 className="text-4xl font-bold mb-8">Checkout</h1>

            <div className="grid md:grid-cols-2 gap-8">
              {/* Order Summary */}
              <div className="bg-gray-800 rounded-lg p-6">
                <h2 className="text-2xl font-bold mb-4">Order Summary</h2>
                
                <div className="space-y-3 mb-6">
                  {cart.map((item, idx) => (
                    <div key={idx} className="flex justify-between items-center py-2 border-b border-gray-700">
                      <div>
                        <div className="font-medium">{item.name}</div>
                        <div className="text-sm text-gray-400">Qty: {item.quantity}</div>
                      </div>
                      <div className="font-semibold">{currency}{(item.price * item.quantity).toFixed(2)}</div>
                    </div>
                  ))}
                </div>

                <div className="space-y-2 text-sm border-t border-gray-700 pt-4">
                  <div className="flex justify-between text-gray-400">
                    <span>Subtotal:</span>
                    <span>{currency}{calculateSubtotal().toFixed(2)}</span>
                  </div>
                  {calculateFee() > 0 && (
                    <div className="flex justify-between text-gray-400">
                      <span>Payment Fee ({selectedGateway?.fee_type === 'percentage' ? `${selectedGateway.fee_value}%` : 'Fixed'}):</span>
                      <span>{currency}{calculateFee().toFixed(2)}</span>
                    </div>
                  )}
                  <div className="flex justify-between text-xl font-bold pt-2 border-t border-gray-700">
                    <span>Total:</span>
                    <span className="text-primary">{currency}{calculateTotal().toFixed(2)}</span>
                  </div>
                </div>
              </div>

              {/* Checkout Form */}
              <div>
                <form onSubmit={handleSubmit} className="space-y-6">
                  {/* Address Section */}
                  <div className="bg-gray-800 rounded-lg p-6">
                    <h2 className="text-2xl font-bold mb-4">Shipping Address</h2>
                    
                    <div className="space-y-4">
                      <div>
                        <label className="block text-sm font-medium mb-2">Country *</label>
                        <input
                          type="text"
                          required
                          value={country}
                          onChange={(e) => setCountry(e.target.value)}
                          className="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-primary"
                          placeholder="Enter your country"
                        />
                      </div>

                      <div>
                        <label className="block text-sm font-medium mb-2">City *</label>
                        <input
                          type="text"
                          required
                          value={city}
                          onChange={(e) => setCity(e.target.value)}
                          className="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-primary"
                          placeholder="Enter your city"
                        />
                      </div>

                      <div>
                        <label className="block text-sm font-medium mb-2">Postal Code *</label>
                        <input
                          type="text"
                          required
                          value={postalCode}
                          onChange={(e) => setPostalCode(e.target.value)}
                          className="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-primary"
                          placeholder="Enter postal code"
                        />
                      </div>
                    </div>
                  </div>

                  {/* Payment Method Section */}
                  <div className="bg-gray-800 rounded-lg p-6">
                    <h2 className="text-2xl font-bold mb-4">Payment Method</h2>
                    
                    {gateways.length === 0 ? (
                      <p className="text-gray-400">No payment methods available</p>
                    ) : (
                      <div className="space-y-3">
                        {gateways.map((gateway) => (
                          <div
                            key={gateway.id}
                            onClick={() => setSelectedGateway(gateway)}
                            className={`p-4 rounded-lg border-2 cursor-pointer transition-all ${
                              selectedGateway?.id === gateway.id
                                ? 'border-primary bg-gray-700'
                                : 'border-gray-700 bg-gray-750 hover:border-gray-600'
                            }`}
                          >
                            <div className="flex items-center justify-between mb-2">
                              <div className="flex items-center gap-3">
                                {gateway.logo_url && (
                                  <img src={gateway.logo_url} alt={gateway.gateway_name} className="w-8 h-8 object-contain" />
                                )}
                                <div>
                                  <div className="font-medium">{gateway.gateway_name}</div>
                                  {gateway.fee_value > 0 && (
                                    <div className="text-xs text-gray-400">
                                      Fee: {gateway.fee_type === 'percentage' ? `${gateway.fee_value}%` : `${currency}${gateway.fee_value}`}
                                    </div>
                                  )}
                                </div>
                              </div>
                              <div className={`w-5 h-5 rounded-full border-2 flex items-center justify-center ${
                                selectedGateway?.id === gateway.id ? 'border-primary' : 'border-gray-600'
                              }`}>
                                {selectedGateway?.id === gateway.id && (
                                  <div className="w-3 h-3 rounded-full bg-primary"></div>
                                )}
                              </div>
                            </div>
                            
                            {gateway.description && (
                              <p className="text-sm text-gray-400 mb-2">{gateway.description}</p>
                            )}
                            
                            {selectedGateway?.id === gateway.id && gateway.instructions && (
                              <div className="mt-3 p-3 bg-gray-900 rounded text-sm">
                                <div className="font-medium text-primary mb-1">Payment Instructions:</div>
                                <div className="text-gray-300 whitespace-pre-line">{gateway.instructions}</div>
                                {gateway.gateway_id && (
                                  <div className="mt-2 p-2 bg-gray-800 rounded">
                                    <span className="text-gray-400 text-xs">Payment ID/Account: </span>
                                    <code className="text-primary">{gateway.gateway_id}</code>
                                  </div>
                                )}
                              </div>
                            )}
                          </div>
                        ))}
                      </div>
                    )}
                  </div>

                  <button
                    type="submit"
                    disabled={isSubmitting || gateways.length === 0}
                    className="w-full bg-primary hover:bg-blue-600 disabled:bg-gray-700 disabled:cursor-not-allowed text-white py-3 px-6 rounded-lg font-medium transition-colors text-lg"
                  >
                    {isSubmitting ? (
                      <>
                        <i className="fas fa-spinner fa-spin mr-2"></i>
                        Processing...
                      </>
                    ) : (
                      <>
                        <i className="fas fa-check-circle mr-2"></i>
                        Place Order
                      </>
                    )}
                  </button>
                </form>
              </div>
            </div>
          </div>

          {toast && <Toast message={toast.message} type={toast.type} onClose={() => setToast(null)} />}
        </div>
      );
    };

    ReactDOM.render(<App />, document.getElementById('root'));
  </script>
</body>
</html>
