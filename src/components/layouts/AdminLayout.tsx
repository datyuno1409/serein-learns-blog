import { useState, useEffect } from 'react';
import { Link, useLocation, useNavigate } from 'react-router-dom';
import { useLanguage } from '@/contexts/LanguageContext';
import { useAuth } from '@/contexts/AuthContext';
import {
  LayoutDashboard,
  FileText,
  FolderKanban,
  Settings,
  ChevronDown,
  Menu,
  X,
  LogOut
} from 'lucide-react';
import { cn } from '@/lib/utils';

interface AdminLayoutProps {
  children: React.ReactNode;
}

const AdminLayout = ({ children }: AdminLayoutProps) => {
  const { t } = useLanguage();
  const { user, logout } = useAuth();
  const location = useLocation();
  const navigate = useNavigate();
  const [isSidebarOpen, setIsSidebarOpen] = useState(true);
  const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false);

  const menuItems = [
    {
      title: t('admin.dashboard'),
      icon: LayoutDashboard,
      path: '/admin/dashboard'
    },
    {
      title: t('admin.articles'),
      icon: FileText,
      path: '/manage-articles'
    },
    {
      title: t('admin.projects'),
      icon: FolderKanban,
      path: '/admin/projects'
    },
    {
      title: t('admin.settings'),
      icon: Settings,
      path: '/admin/settings'
    }
  ];

  const isActive = (path: string) => location.pathname === path;

  const handleLogout = () => {
    logout();
    navigate('/login');
  };

  useEffect(() => {
    const handleResize = () => {
      if (window.innerWidth < 768) {
        setIsSidebarOpen(false);
      } else {
        setIsSidebarOpen(true);
      }
    };

    window.addEventListener('resize', handleResize);
    handleResize();

    return () => window.removeEventListener('resize', handleResize);
  }, []);

  if (!user) {
    navigate('/login');
    return null;
  }

  return (
    <div className="min-h-screen bg-gray-100">
      {/* Sidebar */}
      <aside
        className={cn(
          "fixed top-0 left-0 z-40 w-64 h-screen transition-transform bg-serein-800 text-white",
          !isSidebarOpen && "-translate-x-full"
        )}
      >
        {/* Logo */}
        <div className="h-16 flex items-center justify-between px-4 bg-serein-900">
          <Link to="/" className="text-xl font-bold">
            Serein Admin
          </Link>
          <button
            onClick={() => setIsSidebarOpen(false)}
            className="md:hidden text-gray-300 hover:text-white"
          >
            <X className="h-6 w-6" />
          </button>
        </div>

        {/* User Info */}
        <div className="p-4 border-t border-serein-700">
          <div className="flex items-center">
            <img
              src={user.avatar || '/placeholder.svg'}
              alt={user.name}
              className="w-8 h-8 rounded-full"
            />
            <div className="ml-3">
              <p className="text-sm font-medium">{user.name}</p>
              <p className="text-xs text-gray-300">{user.email}</p>
            </div>
          </div>
        </div>

        {/* Navigation */}
        <nav className="mt-4">
          {menuItems.map((item) => (
            <Link
              key={item.path}
              to={item.path}
              className={cn(
                "flex items-center px-4 py-3 text-sm font-medium transition-colors",
                isActive(item.path)
                  ? "bg-serein-700 text-white"
                  : "text-gray-300 hover:bg-serein-700 hover:text-white"
              )}
            >
              <item.icon className="h-5 w-5 mr-3" />
              {item.title}
            </Link>
          ))}
        </nav>
      </aside>

      {/* Main Content */}
      <div
        className={cn(
          "transition-all duration-300",
          isSidebarOpen ? "md:ml-64" : "md:ml-0"
        )}
      >
        {/* Header */}
        <header className="bg-white shadow-sm h-16 fixed top-0 right-0 left-0 z-30 flex items-center justify-between px-4 md:pl-4 md:pr-6">
          <div className="flex items-center">
            <button
              onClick={() => setIsSidebarOpen(!isSidebarOpen)}
              className="text-gray-500 hover:text-gray-600 focus:outline-none"
            >
              <Menu className="h-6 w-6" />
            </button>
          </div>

          <div className="flex items-center gap-4">
            <button
              onClick={handleLogout}
              className="flex items-center text-gray-500 hover:text-gray-600"
            >
              <LogOut className="h-5 w-5" />
              <span className="ml-2 text-sm font-medium hidden md:inline">
                {t('nav.logout')}
              </span>
            </button>
          </div>
        </header>

        {/* Page Content */}
        <main className="pt-16 min-h-screen">
          <div className="p-4 md:p-6">{children}</div>
        </main>
      </div>

      {/* Mobile Menu Overlay */}
      {!isSidebarOpen && (
        <div
          className="fixed inset-0 bg-gray-900 bg-opacity-50 z-30 md:hidden"
          onClick={() => setIsSidebarOpen(false)}
        />
      )}
    </div>
  );
};

export default AdminLayout;