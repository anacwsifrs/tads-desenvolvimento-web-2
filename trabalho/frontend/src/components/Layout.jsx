import { useState, useEffect } from 'react';
import { Link, Outlet, useNavigate } from 'react-router-dom';
import { ShoppingBag, ShoppingCart, ChevronDown, User, Package, ClipboardList, Users, LogOut, LogIn } from 'lucide-react';

const API_URL = 'http://localhost/brecho/index.php';
const UPLOADS_URL = 'http://localhost/brecho/uploads/';

export default function Layout() {
  const navigate = useNavigate();
  const [menuAberto, setMenuAberto] = useState(false);
  const [usuario, setUsuario] = useState(() => {
    const salvo = localStorage.getItem('brecho_usuario');
    return salvo ? JSON.parse(salvo) : null;
  });

  useEffect(() => {
    const sincronizarUsuario = () => {
      const salvo = localStorage.getItem('brecho_usuario');
      setUsuario(salvo ? JSON.parse(salvo) : null);
    };
    window.addEventListener('usuarioAtualizado', sincronizarUsuario);
    return () => window.removeEventListener('usuarioAtualizado', sincronizarUsuario);
  }, []);

  const handleLogout = async () => {
    if (!window.confirm('Tem certeza que deseja sair do sistema?')) return;
    try {
      await fetch(`${API_URL}?acao=logout`, { credentials: 'include' });
    } catch (e) {
      console.error(e);
    }
    localStorage.removeItem('brecho_usuario');
    setUsuario(null);
    setMenuAberto(false);
    navigate('/vitrine');
  };

  const qtdCarrinho = 0;

  return (
    <div className="min-h-screen flex flex-col bg-stone-100 text-stone-800 font-sans">
      <header className="bg-stone-800 text-white shadow-md sticky top-0 z-50">
        <div className="max-w-6xl mx-auto px-4 py-3 flex justify-between items-center">
          <div className="flex items-center gap-8">
            <Link to="/" className="text-xl font-black tracking-tight flex items-center gap-2 text-emerald-400 hover:text-emerald-300 transition">
              <ShoppingBag size={24} />
              <span>Intent Brechó</span>
            </Link>

            <nav className="hidden md:flex items-center gap-6 text-sm font-semibold text-stone-300">
              <Link to="/" className="hover:text-white transition">Início</Link>
              <Link to="/vitrine" className="hover:text-white transition">Vitrine</Link>
              <Link to="/fale-conosco" className="hover:text-white transition">Fale Conosco</Link>
              <Link to="/termos" className="hover:text-white transition">Termos e LGPD</Link>
            </nav>
          </div>

          <div className="relative">
            {usuario ? (
              <div>
                <button
                  onClick={() => setMenuAberto(!menuAberto)}
                  className="flex items-center gap-2.5 bg-stone-700 hover:bg-stone-600 px-3 py-1.5 rounded-full transition border border-stone-600 focus:outline-none"
                >
                  {usuario.foto && usuario.foto !== 'default.png' ? (
                    <img
                      src={`${UPLOADS_URL}${usuario.foto}`}
                      alt={usuario.nome}
                      className="w-7 h-7 rounded-full object-cover border border-white/50"
                    />
                  ) : (
                    <div className="w-7 h-7 rounded-full bg-emerald-600 flex items-center justify-center text-xs font-bold">
                      {usuario.nome?.charAt(0).toUpperCase()}
                    </div>
                  )}
                  <span className="text-sm font-medium">{usuario.nome}</span>
                  <ChevronDown size={14} className={`transition-transform ${menuAberto ? 'rotate-180' : ''}`} />
                </button>

                {menuAberto && (
                  <div
                    className="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-xl border border-stone-200 py-2 text-stone-700 z-50"
                    onMouseLeave={() => setMenuAberto(false)}
                  >
                    <div className="px-4 py-2 border-b border-stone-100 text-xs text-stone-400">
                      Conectado como:
                      <strong className="block text-stone-800 text-sm mt-0.5">{usuario.nome}</strong>
                    </div>

                    <Link to="/perfil" onClick={() => setMenuAberto(false)} className="flex items-center gap-2 px-4 py-2 text-sm hover:bg-stone-50 hover:text-emerald-600">
                      <User size={16} /> Meu Perfil
                    </Link>

                    <Link to="/carrinho" onClick={() => setMenuAberto(false)} className="flex items-center justify-between px-4 py-2 text-sm hover:bg-stone-50 hover:text-emerald-600">
                      <span className="flex items-center gap-2"><ShoppingCart size={16} /> Meu Carrinho</span>
                      {qtdCarrinho > 0 && (
                        <span className="bg-amber-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">
                          {qtdCarrinho}
                        </span>
                      )}
                    </Link>

                    <div className="my-1 border-t border-stone-100"></div>

                    {usuario.tipo === 'admin' ? (
                      <>
                        <Link to="/admin/lotes" onClick={() => setMenuAberto(false)} className="flex items-center gap-2 px-4 py-2 text-sm hover:bg-stone-50 hover:text-emerald-600">
                          <ClipboardList size={16} /> Painel Lotes
                        </Link>
                        <Link to="/admin/pedidos" onClick={() => setMenuAberto(false)} className="flex items-center gap-2 px-4 py-2 text-sm hover:bg-stone-50 hover:text-emerald-600">
                          <ShoppingBag size={16} /> Painel Vendas
                        </Link>
                        <Link to="/admin/clientes" onClick={() => setMenuAberto(false)} className="flex items-center gap-2 px-4 py-2 text-sm hover:bg-stone-50 hover:text-emerald-600">
                          <Users size={16} /> Painel Usuários
                        </Link>
                      </>
                    ) : (
                      <>
                        <Link to="/meus-lotes" onClick={() => setMenuAberto(false)} className="flex items-center gap-2 px-4 py-2 text-sm hover:bg-stone-50 hover:text-emerald-600">
                          <Package size={16} /> Meus Lotes
                        </Link>
                        <Link to="/meus-pedidos" onClick={() => setMenuAberto(false)} className="flex items-center gap-2 px-4 py-2 text-sm hover:bg-stone-50 hover:text-emerald-600">
                          <ShoppingBag size={16} /> Minhas Compras
                        </Link>
                      </>
                    )}

                    <div className="my-1 border-t border-stone-100"></div>

                    <button
                      onClick={handleLogout}
                      className="w-full text-left flex items-center gap-2 px-4 py-2 text-sm text-red-600 font-semibold hover:bg-red-50"
                    >
                      <LogOut size={16} /> Sair
                    </button>
                  </div>
                )}
              </div>
            ) : (
              <Link to="/login" className="bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-bold px-4 py-2 rounded-md flex items-center gap-2 transition shadow-sm">
                <LogIn size={16} /> Login / Cadastro
              </Link>
            )}
          </div>
        </div>
      </header>

      <main className="flex-1 w-full max-w-6xl mx-auto p-4 md:p-6">
        <Outlet />
      </main>

      <footer className="bg-stone-200 text-stone-600 text-center py-6 border-t border-stone-300 mt-auto text-sm">
        <p className="font-semibold">&copy; {new Date().getFullYear()} Intent Brechó - Todos os direitos reservados.</p>
        <p className="text-xs mt-1 text-stone-500">
          Trabalho Individual - Desenvolvimento Web 2 | <Link to="/termos" className="underline hover:text-stone-800">Política de Privacidade & Plágio</Link>
        </p>
      </footer>
    </div>
  );
}