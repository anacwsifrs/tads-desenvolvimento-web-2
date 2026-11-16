import { useState, useEffect } from 'react';
import { User, Lock, Save, KeyRound, Eye, EyeOff, CheckCircle2, AlertCircle } from 'lucide-react';

const API_URL = 'http://localhost/brecho/index.php';

const inputClass =
  'w-full p-2.5 bg-white text-stone-700 border border-stone-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all shadow-sm';

export default function Perfil() {
  const [aba, setAba] = useState('dados');
  const [usuario, setUsuario] = useState({
    email: '',
    nome: '',
    telefone: '',
    chave_pix: '',
    saldo_credito: 0
  });
  const [foto, setFoto] = useState(null);
  const [carregando, setCarregando] = useState(true);

  const [feedbackDados, setFeedbackDados] = useState({ tipo: '', texto: '' });
  const [salvandoDados, setSalvandoDados] = useState(false);

  const [senhas, setSenhas] = useState({ senha_atual: '', nova_senha: '' });
  const [mostrarAtual, setMostrarAtual] = useState(false);
  const [mostrarNova, setMostrarNova] = useState(false);
  const [feedbackSenha, setFeedbackSenha] = useState({ tipo: '', texto: '' });
  const [salvandoSenha, setSalvandoSenha] = useState(false);

  useEffect(() => {
    const carregarPerfil = async () => {
      try {
        const resposta = await fetch(`${API_URL}?acao=perfil`, {
          credentials: 'include'
        });
        const dados = await resposta.json();
        if (resposta.ok && dados.id) {
          setUsuario(dados);
        }
      } catch (error) {
        console.error('Erro ao carregar perfil:', error);
      } finally {
        setCarregando(false);
      }
    };
    carregarPerfil();
  }, []);

  const handleSalvarDados = async (e) => {
    e.preventDefault();
    setFeedbackDados({ tipo: '', texto: '' });
    setSalvandoDados(true);

    const formData = new FormData();
    formData.append('nome', usuario.nome);
    formData.append('telefone', usuario.telefone);
    formData.append('pix', usuario.chave_pix || '');
    if (foto) formData.append('foto', foto);

    try {
      const resposta = await fetch(`${API_URL}?acao=atualizar_perfil`, {
        method: 'POST',
        credentials: 'include',
        body: formData
      });
      const dados = await resposta.json();

      if (resposta.ok && dados.sucesso) {
        setFeedbackDados({ tipo: 'sucesso', texto: dados.mensagem });
        const userLocal = JSON.parse(localStorage.getItem('brecho_usuario') || '{}');
        const atualizado = {
          ...userLocal,
          nome: usuario.nome,
          ...(dados.foto ? { foto: dados.foto } : {})
        };
        localStorage.setItem('brecho_usuario', JSON.stringify(atualizado));
        window.dispatchEvent(new Event('usuarioAtualizado'));
      } else {
        setFeedbackDados({ tipo: 'erro', texto: dados.mensagem || 'Erro ao atualizar perfil.' });
      }
    } catch (error) {
      setFeedbackDados({ tipo: 'erro', texto: 'Erro de comunicação com o servidor.' });
    } finally {
      setSalvandoDados(false);
    }
  };

  const handleSalvarSenha = async (e) => {
    e.preventDefault();
    setFeedbackSenha({ tipo: '', texto: '' });
    setSalvandoSenha(true);

    try {
      const resposta = await fetch(`${API_URL}?acao=atualizar_senha`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include',
        body: JSON.stringify(senhas)
      });
      const dados = await resposta.json();

      if (resposta.ok && dados.sucesso) {
        setFeedbackSenha({ tipo: 'sucesso', texto: dados.mensagem });
        setSenhas({ senha_atual: '', nova_senha: '' });
      } else {
        setFeedbackSenha({ tipo: 'erro', texto: dados.mensagem || 'Erro ao alterar senha.' });
      }
    } catch (error) {
      setFeedbackSenha({ tipo: 'erro', texto: 'Erro de comunicação com o servidor.' });
    } finally {
      setSalvandoSenha(false);
    }
  };

  if (carregando) {
    return (
      <div className="flex justify-center items-center py-20">
        <div className="w-8 h-8 border-4 border-emerald-600 border-t-transparent rounded-full animate-spin"></div>
      </div>
    );
  }

  return (
    <div className="max-w-2xl mx-auto space-y-6 pb-8">
      <div className="flex flex-col sm:flex-row sm:items-end justify-between gap-4 border-b border-stone-200 pb-5">
        <div>
          <h2 className="text-2xl font-black text-stone-800 flex items-center gap-2">
            <User className="text-emerald-600" size={26} />
            <span>Meu Perfil</span>
          </h2>
          <p className="text-stone-500 text-sm mt-1">
            Gerencie seus dados cadastrais, chave PIX, saldo e senha de acesso.
          </p>
        </div>

        <div className="flex gap-2">
          <button
            type="button"
            onClick={() => setAba('dados')}
            className={`px-4 py-2 rounded-lg text-sm font-bold flex items-center gap-1.5 transition ${
              aba === 'dados'
                ? 'bg-emerald-600 text-white shadow-sm'
                : 'bg-white border border-stone-200 text-stone-600 hover:bg-stone-100'
            }`}
          >
            <User size={16} />
            <span>Meus Dados</span>
          </button>

          <button
            type="button"
            onClick={() => setAba('senha')}
            className={`px-4 py-2 rounded-lg text-sm font-bold flex items-center gap-1.5 transition ${
              aba === 'senha'
                ? 'bg-emerald-600 text-white shadow-sm'
                : 'bg-white border border-stone-200 text-stone-600 hover:bg-stone-100'
            }`}
          >
            <Lock size={16} />
            <span>Segurança</span>
          </button>
        </div>
      </div>

      {aba === 'dados' ? (
        <form
          onSubmit={handleSalvarDados}
          className="bg-white border border-stone-200 rounded-2xl p-6 md:p-8 shadow-sm space-y-4"
        >
          <h3 className="text-lg font-bold text-stone-800">Informações Pessoais</h3>

          {feedbackDados.texto && (
            <div
              className={`px-4 py-3 rounded-lg text-sm font-medium flex items-center gap-2 border ${
                feedbackDados.tipo === 'sucesso'
                  ? 'bg-emerald-50 border-emerald-200 text-emerald-800'
                  : 'bg-red-50 border-red-200 text-red-700'
              }`}
            >
              {feedbackDados.tipo === 'sucesso' ? (
                <CheckCircle2 size={18} />
              ) : (
                <AlertCircle size={18} />
              )}
              <span>{feedbackDados.texto}</span>
            </div>
          )}

          <div>
            <label className="block text-sm font-semibold text-stone-700 mb-1">
              E-mail (Login):
            </label>
            <input
              type="email"
              value={usuario.email || ''}
              readOnly
              className={`${inputClass} bg-stone-100 text-stone-500 cursor-not-allowed`}
            />
            <span className="text-xs text-stone-400">O e-mail não pode ser alterado.</span>
          </div>

          <div>
            <label className="block text-sm font-semibold text-stone-700 mb-1">
              Nome Completo:
            </label>
            <input
              type="text"
              required
              value={usuario.nome || ''}
              onChange={(e) => setUsuario({ ...usuario, nome: e.target.value })}
              className={inputClass}
            />
          </div>

          <div>
            <label className="block text-sm font-semibold text-stone-700 mb-1">WhatsApp:</label>
            <input
              type="text"
              required
              value={usuario.telefone || ''}
              onChange={(e) => setUsuario({ ...usuario, telefone: e.target.value })}
              className={inputClass}
            />
          </div>

          <div>
            <label className="block text-sm font-semibold text-stone-700 mb-1">Chave PIX:</label>
            <input
              type="text"
              value={usuario.chave_pix || ''}
              onChange={(e) => setUsuario({ ...usuario, chave_pix: e.target.value })}
              className={inputClass}
            />
          </div>

          <div>
            <label className="block text-sm font-semibold text-stone-700 mb-1">
              Saldo de Crédito em Loja:
            </label>
            <input
              type="text"
              readOnly
              value={Number(usuario.saldo_credito || 0).toLocaleString('pt-BR', {
                style: 'currency',
                currency: 'BRL'
              })}
              className="w-full p-2.5 bg-emerald-50 text-emerald-700 font-black border border-emerald-200 rounded-lg cursor-not-allowed"
            />
            <span className="text-xs text-stone-500">
              Este saldo pode ser utilizado como desconto ou pagamento integral no carrinho de
              compras.
            </span>
          </div>

          <div>
            <label className="block text-sm font-semibold text-stone-700 mb-1">
              Nova Foto de Perfil (Opcional):
            </label>
            <input
              type="file"
              accept="image/*"
              onChange={(e) => setFoto(e.target.files[0])}
              className="w-full text-sm text-stone-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-stone-100 file:text-stone-700 hover:file:bg-stone-200"
            />
          </div>

          <button
            type="submit"
            disabled={salvandoDados}
            className="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg transition shadow-md flex items-center justify-center gap-2 disabled:opacity-60"
          >
            <Save size={18} />
            <span>{salvandoDados ? 'Salvando...' : 'Salvar Alterações'}</span>
          </button>
        </form>
      ) : (
        <form
          onSubmit={handleSalvarSenha}
          className="bg-white border border-stone-200 rounded-2xl p-6 md:p-8 shadow-sm space-y-4"
        >
          <h3 className="text-lg font-bold text-stone-800">Redefinir Senha</h3>

          {feedbackSenha.texto && (
            <div
              className={`px-4 py-3 rounded-lg text-sm font-medium flex items-center gap-2 border ${
                feedbackSenha.tipo === 'sucesso'
                  ? 'bg-emerald-50 border-emerald-200 text-emerald-800'
                  : 'bg-red-50 border-red-200 text-red-700'
              }`}
            >
              {feedbackSenha.tipo === 'sucesso' ? (
                <CheckCircle2 size={18} />
              ) : (
                <AlertCircle size={18} />
              )}
              <span>{feedbackSenha.texto}</span>
            </div>
          )}

          <div>
            <label className="block text-sm font-semibold text-stone-700 mb-1">Senha Atual:</label>
            <div className="relative">
              <input
                type={mostrarAtual ? 'text' : 'password'}
                required
                value={senhas.senha_atual}
                onChange={(e) => setSenhas({ ...senhas, senha_atual: e.target.value })}
                className={`${inputClass} pr-10`}
              />
              <button
                type="button"
                onClick={() => setMostrarAtual(!mostrarAtual)}
                className="absolute right-3 top-2.5 text-stone-400 hover:text-stone-600"
              >
                {mostrarAtual ? <EyeOff size={20} /> : <Eye size={20} />}
              </button>
            </div>
          </div>

          <div>
            <label className="block text-sm font-semibold text-stone-700 mb-1">Nova Senha:</label>
            <div className="relative">
              <input
                type={mostrarNova ? 'text' : 'password'}
                required
                value={senhas.nova_senha}
                onChange={(e) => setSenhas({ ...senhas, nova_senha: e.target.value })}
                className={`${inputClass} pr-10`}
              />
              <button
                type="button"
                onClick={() => setMostrarNova(!mostrarNova)}
                className="absolute right-3 top-2.5 text-stone-400 hover:text-stone-600"
              >
                {mostrarNova ? <EyeOff size={20} /> : <Eye size={20} />}
              </button>
            </div>
          </div>

          <button
            type="submit"
            disabled={salvandoSenha}
            className="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg transition shadow-md flex items-center justify-center gap-2 disabled:opacity-60"
          >
            <KeyRound size={18} />
            <span>{salvandoSenha ? 'Atualizando...' : 'Atualizar Senha'}</span>
          </button>
        </form>
      )}
    </div>
  );
}