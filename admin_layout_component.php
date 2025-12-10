// Admin Layout Component - Shared across all admin pages
const AdminLayout = ({ children, currentPage, currentUser, stats = {}, menuItems }) => {
  const [sidebarOpen, setSidebarOpen] = useState(true);

  const handleLogout = async () => {
    await fetch('auth.php?action=logout', { method: 'POST' });
    window.location.href = 'index.php';
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
              href={item.href}
              className={`w-full flex items-center gap-3 px-3 py-3 rounded-lg transition-all ${
                currentPage === item.href 
                  ? 'bg-primary text-white shadow-lg shadow-primary/20' 
                  : 'text-gray-400 hover:bg-gray-700 hover:text-white'
              }`}
              title={!sidebarOpen ? item.name : ''}
            >
              <i className={`fas ${item.icon} text-lg ${sidebarOpen ? '' : 'mx-auto'}`}></i>
              {sidebarOpen && (
                <>
                  <span className="flex-1 text-left">{item.name}</span>
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
      <main className="flex-1 overflow-y-auto flex flex-col">
        {children}
      </main>
    </div>
  );
};
