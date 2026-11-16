import { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { Eye, EyeOff, LogIn, Mail, Lock, AlertCircle, ShoppingBag } from 'lucide-react';

const API_URL = 'http://localhost/brecho/index.php';

export default function Login() {
  const navigate = useNavigate();

  const [email, setEmail] = useState('');
  const [senha, setSenha] = useState('');
  const [mostrarSenha, setMostrarSenha] = useState(false);
  const [erro, setErro] = useState('');
  const [carregando, setCarregando] = useState(false);

  const handleSubmit = async (e) => {
    e.preventDefault();
    setErro('');
    setCarregando(true);

    try {
      const resposta = await fetch(`${API_URL}?acao=login`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include',
        body: JSON.stringify({ email, senha })
      });

      const dados = await resposta.json();

      if (resposta.ok && dados.sucesso) {
        localStorage.setItem('brecho_usuario', JSON.stringify(dados.usuario));
        window.dispatchEvent(new Event('usuarioAtualizado'));
        navigate('/vitrine');
      } else {
        setErro(dados.mensagem || 'E-mail ou senha incorretos!');
      }
    } catch (error) {
      console.error('Erro no login:', error);
      setErro('Erro de conexão com o servidor. Verifique se o Apache/XAMPP está ativo.');
    } finally {
      setCarregando(false);
    }
  };

  return (
    <div className="max-w-md mx-auto space-y-6 pb-8">
      <div className="border-b border-stone-200 pb-5 text-center">
        <div className="w-12 h-12 bg-emerald-100 text-emerald-700 rounded-xl flex items-center justify-center mx-auto mb-3">
          <ShoppingBag size={24} />
        </div>
        <h2 className="text-2xl font-black text-stone-800 tracking-tight">Login do Usuário</h2>
        <p className="text-stone-500 text-sm mt-1">
          Acesse sua conta no Intent Brechó
        </p>
      </div>

      <div className="bg-white border border-stone-200 rounded-2xl shadow-sm p-6 md:p-8">
        <form onSubmit={handleSubmit} className="space-y-5">
          {erro && (
            <div className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm font-medium flex items-center gap-2">
              <AlertCircle size={18} className="shrink-0 text-red-500" />
              <span>{erro}</span>
            </div>
          )}

          <div>
            <label className="block text-sm font-semibold text-stone-700 mb-1.5">
              E-mail:
            </label>
            <div className="relative">
              <Mail className="absolute left-3 top-3 text-stone-400" size={18} />
              <input
                type="email"
                required
                placeholder="seu@email.com"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                className="w-full pl-10 pr-4 py-2.5 bg-white text-stone-700 border border-stone-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all shadow-sm"
              />
            </div>
          </div>

          <div>
            <div className="flex justify-between items-center mb-1.5">
              <label className="block text-sm font-semibold text-stone-700">
                Senha:
              </label>
              <Link
                to="/recuperar-senha"
                className="text-xs font-semibold text-emerald-600 hover:text-emerald-700 hover:underline"
              >
                Esqueceu a senha?
              </Link>
            </div>

            <div className="relative">
              <Lock className="absolute left-3 top-3 text-stone-400" size={18} />
              <input
                type={mostrarSenha ? 'text' : 'password'}
                required
                placeholder="Digite sua senha"
                value={senha}
                onChange={(e) => setSenha(e.target.value)}
                className="w-full pl-10 pr-11 py-2.5 bg-white text-stone-700 border border-stone-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all shadow-sm"
              />
              <button
                type="button"
                onClick={() => setMostrarSenha(!mostrarSenha)}
                title={mostrarSenha ? 'Ocultar senha' : 'Mostrar senha'}
                className="absolute right-3 top-2.5 text-stone-400 hover:text-stone-600 p-0.5 transition-colors focus:outline-none"
              >
                {mostrarSenha ? <EyeOff size={20} /> : <Eye size={20} />}
              </button>
            </div>
          </div>

          <button
            type="submit"
            disabled={carregando}
            className="w-full py-3 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg transition shadow-md flex items-center justify-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed"
          >
            {carregando ? (
              <div className="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
            ) : (
              <>
                <LogIn size={18} />
                <span>Entrar</span>
              </>
            )}
          </button>

          <div className="pt-4 border-t border-stone-100 text-center text-sm text-stone-600">
            Não tem conta?{' '}
            <Link
              to="/cadastro"
              className="font-bold text-emerald-600 hover:text-emerald-700 hover:underline"
            >
              Cadastre-se aqui
            </Link>
          </div>
        </form>
      </div>
    </div>
  );
}