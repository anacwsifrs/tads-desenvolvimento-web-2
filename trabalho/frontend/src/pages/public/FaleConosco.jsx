import { useState } from 'react';
import { MessageSquare, Send, CheckCircle2, AlertCircle, Mail, User, FileText } from 'lucide-react';

const API_URL = 'http://localhost/brecho/index.php';

const inputClass =
  'w-full p-2.5 bg-white text-stone-700 border border-stone-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all shadow-sm';

export default function FaleConosco() {
  const [form, setForm] = useState({
    nome: '',
    email: '',
    assunto: '',
    mensagem: ''
  });
  const [status, setStatus] = useState({ tipo: '', texto: '' });
  const [enviando, setEnviando] = useState(false);

  const handleChange = (campo, valor) => {
    setForm((prev) => ({ ...prev, [campo]: valor }));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setStatus({ tipo: '', texto: '' });
    setEnviando(true);

    try {
      const resposta = await fetch(`${API_URL}?acao=fale_conosco`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(form)
      });

      const dados = await resposta.json();

      if (resposta.ok && dados.sucesso) {
        setStatus({
          tipo: 'sucesso',
          texto: dados.mensagem || 'Mensagem enviada com sucesso!'
        });
        setForm({ nome: '', email: '', assunto: '', mensagem: '' });
      } else {
        setStatus({
          tipo: 'erro',
          texto: dados.mensagem || 'Erro ao enviar mensagem.'
        });
      }
    } catch (error) {
      console.error('Erro no envio de contato:', error);
      setStatus({
        tipo: 'erro',
        texto: 'Erro de conexão com o servidor.'
      });
    } finally {
      setEnviando(false);
    }
  };

  return (
    <div className="max-w-2xl mx-auto space-y-6 pb-8">
      <div className="border-b border-stone-200 pb-5">
        <h2 className="text-2xl font-black text-stone-800 flex items-center gap-2">
          <MessageSquare className="text-emerald-600" size={26} />
          <span>Fale Conosco</span>
        </h2>
        <p className="text-stone-500 text-sm mt-1">
          Tem dúvidas sobre avaliação de lotes, reservas ou funcionamento da loja? Envie sua mensagem.
        </p>
      </div>

      <form
        onSubmit={handleSubmit}
        className="bg-white border border-stone-200 rounded-2xl shadow-sm p-6 md:p-8 space-y-5"
      >
        {status.texto && (
          <div
            className={`px-4 py-3 rounded-lg text-sm font-medium flex items-center gap-2 border ${
              status.tipo === 'sucesso'
                ? 'bg-emerald-50 border-emerald-200 text-emerald-800'
                : 'bg-red-50 border-red-200 text-red-700'
            }`}
          >
            {status.tipo === 'sucesso' ? (
              <CheckCircle2 size={18} className="shrink-0 text-emerald-600" />
            ) : (
              <AlertCircle size={18} className="shrink-0 text-red-500" />
            )}
            <span>{status.texto}</span>
          </div>
        )}

        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label className="block text-sm font-semibold text-stone-700 mb-1.5">
              Seu Nome:
            </label>
            <div className="relative">
              <User className="absolute left-3 top-3 text-stone-400" size={18} />
              <input
                type="text"
                required
                placeholder="Digite seu nome"
                value={form.nome}
                onChange={(e) => handleChange('nome', e.target.value)}
                className={`${inputClass} pl-10`}
              />
            </div>
          </div>

          <div>
            <label className="block text-sm font-semibold text-stone-700 mb-1.5">
              Seu E-mail:
            </label>
            <div className="relative">
              <Mail className="absolute left-3 top-3 text-stone-400" size={18} />
              <input
                type="email"
                required
                placeholder="seu@email.com"
                value={form.email}
                onChange={(e) => handleChange('email', e.target.value)}
                className={`${inputClass} pl-10`}
              />
            </div>
          </div>
        </div>

        <div>
          <label className="block text-sm font-semibold text-stone-700 mb-1.5">
            Assunto:
          </label>
          <div className="relative">
            <FileText className="absolute left-3 top-3 text-stone-400" size={18} />
            <input
              type="text"
              required
              placeholder="Ex: Dúvida sobre avaliação de lote"
              value={form.assunto}
              onChange={(e) => handleChange('assunto', e.target.value)}
              className={`${inputClass} pl-10`}
            />
          </div>
        </div>

        <div>
          <label className="block text-sm font-semibold text-stone-700 mb-1.5">
            Mensagem:
          </label>
          <textarea
            rows={4}
            required
            placeholder="Escreva sua mensagem detalhada..."
            value={form.mensagem}
            onChange={(e) => handleChange('mensagem', e.target.value)}
            className={inputClass}
          />
        </div>

        <button
          type="submit"
          disabled={enviando}
          className="w-full py-3.5 px-6 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-base rounded-lg transition shadow-md flex items-center justify-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed"
        >
          {enviando ? (
            <div className="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
          ) : (
            <>
              <Send size={18} />
              <span>Enviar Mensagem</span>
            </>
          )}
        </button>
      </form>
    </div>
  );
}