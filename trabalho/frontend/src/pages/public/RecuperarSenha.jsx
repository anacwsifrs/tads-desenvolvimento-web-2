import { useState } from 'react';
import { Link } from 'react-router-dom';
import { KeyRound, Mail, Send, ArrowLeft, CheckCircle2, AlertCircle } from 'lucide-react';

const API_URL = 'http://localhost/brecho/index.php';

export default function RecuperarSenha() {
  const [email, setEmail] = useState('');
  const [mensagem, setMensagem] = useState('');
  const [sucesso, setSucesso] = useState(false);
  const [enviando, setEnviando] = useState(false);

  const handleSubmit = async (e) => {
    e.preventDefault();
    setMensagem('');
    setEnviando(true);

    try {
      const resposta = await fetch(`${API_URL}?acao=recuperar_senha`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email })
      });

      const dados = await resposta.json();

      setSucesso(Boolean(resposta.ok && dados.sucesso));
      setMensagem(dados.mensagem || 'Não foi possível processar a solicitação.');
      if (resposta.ok && dados.sucesso) {
        setEmail('');
      }
    } catch (error) {
      console.error('Erro ao recuperar senha:', error);
      setSucesso(false);
      setMensagem('Erro de conexão com o servidor.');
    } finally {
      setEnviando(false);
    }
  };

  return (
    <div className="max-w-md mx-auto my-8">
      <div className="bg-white border border-stone-200 rounded-2xl shadow-sm overflow-hidden">
        <div className="bg-stone-800 text-white p-6 text-center">
          <div className="w-12 h-12 bg-amber-500 rounded-xl flex items-center justify-center mx-auto mb-3 shadow">
            <KeyRound size={24} className="text-stone-900" />
          </div>
          <h2 className="text-2xl font-black tracking-tight">Recuperação de Senha</h2>
          <p className="text-stone-400 text-sm mt-1">
            Informe seu e-mail cadastrado para receber as instruções.
          </p>
        </div>

        <form onSubmit={handleSubmit} className="p-6 md:p-8 space-y-5">
          {mensagem && (
            <div
              className={`px-4 py-3 rounded-lg text-sm font-medium flex items-start gap-2 border ${
                sucesso
                  ? 'bg-emerald-50 border-emerald-200 text-emerald-800'
                  : 'bg-red-50 border-red-200 text-red-700'
              }`}
            >
              {sucesso ? (
                <CheckCircle2 size={18} className="shrink-0 text-emerald-600 mt-0.5" />
              ) : (
                <AlertCircle size={18} className="shrink-0 text-red-500 mt-0.5" />
              )}
              <span>{mensagem}</span>
            </div>
          )}

          <div>
            <label className="block text-sm font-semibold text-stone-700 mb-1.5">
              Informe seu e-mail cadastrado:
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

          <button
            type="submit"
            disabled={enviando}
            className="w-full py-3 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg transition shadow-md flex items-center justify-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed"
          >
            {enviando ? (
              <div className="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
            ) : (
              <>
                <Send size={18} />
                <span>Enviar Instruções</span>
              </>
            )}
          </button>

          <div className="pt-4 border-t border-stone-100 text-center">
            <Link
              to="/login"
              className="inline-flex items-center gap-1.5 text-sm font-semibold text-stone-600 hover:text-emerald-700 transition"
            >
              <ArrowLeft size={16} />
              <span>Voltar para o Login</span>
            </Link>
          </div>
        </form>
      </div>
    </div>
  );
}