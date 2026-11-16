import { useState, useEffect } from 'react';
import { useParams, useNavigate, Link } from 'react-router-dom';
import { ArrowLeft, Eye, Pencil, Save } from 'lucide-react';

const API_URL = 'http://localhost/brecho/index.php';

export default function FormClienteAdmin({ readonly = false }) {
  const { id } = useParams();
  const navigate = useNavigate();

  const [cliente, setCliente] = useState({
    cpf: '',
    email: '',
    nome: '',
    telefone: '',
    chave_pix: '',
    saldo_credito: '0.00'
  });
  const [carregando, setCarregando] = useState(true);
  const [salvando, setSalvando] = useState(false);

  useEffect(() => {
    const carregarCliente = async () => {
      try {
        const resposta = await fetch(`${API_URL}?acao=buscar_cliente_id&id=${id}`, {
          credentials: 'include'
        });
        const dados = await resposta.json();
        if (resposta.ok && dados.id) {
          setCliente({
            cpf: dados.cpf || '',
            email: dados.email || '',
            nome: dados.nome || '',
            telefone: dados.telefone || '',
            chave_pix: dados.chave_pix || '',
            saldo_credito: dados.saldo_credito ?? '0.00'
          });
        }
      } catch (error) {
        console.error('Erro ao carregar cliente:', error);
      } finally {
        setCarregando(false);
      }
    };
    if (id) carregarCliente();
  }, [id]);

  const handleSubmit = async (e) => {
    e.preventDefault();
    if (readonly) return;

    setSalvando(true);
    try {
      const resposta = await fetch(`${API_URL}?acao=salvar_cliente_admin`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include',
        body: JSON.stringify({
          id,
          nome: cliente.nome,
          telefone: cliente.telefone,
          chave_pix: cliente.chave_pix,
          saldo_credito: cliente.saldo_credito
        })
      });
      const dados = await resposta.json();
      if (resposta.ok && dados.sucesso) {
        alert(dados.mensagem || 'Cliente atualizado com sucesso!');
        navigate('/admin/clientes');
      } else {
        alert(dados.mensagem || 'Erro ao atualizar cliente.');
      }
    } catch (error) {
      alert('Erro de conexão com o servidor.');
    } finally {
      setSalvando(false);
    }
  };

  const campoClass = (bloqueado) =>
    `w-full p-2.5 border border-stone-300 rounded-lg text-sm transition-all ${
      bloqueado
        ? 'bg-stone-100 text-stone-500 cursor-not-allowed'
        : 'bg-white text-stone-700 focus:outline-none focus:ring-2 focus:ring-emerald-500'
    }`;

  if (carregando) {
    return (
      <div className="flex justify-center items-center py-20">
        <div className="w-8 h-8 border-4 border-emerald-600 border-t-transparent rounded-full animate-spin"></div>
      </div>
    );
  }

  return (
    <div className="max-w-xl mx-auto my-6">
      <Link
        to="/admin/clientes"
        className="inline-flex items-center gap-1.5 text-sm font-semibold text-stone-500 hover:text-emerald-700 mb-4 transition"
      >
        <ArrowLeft size={16} />
        <span>Voltar para Usuários</span>
      </Link>

      <div className="bg-white border border-stone-200 rounded-2xl shadow-sm overflow-hidden">
        <div className="bg-stone-800 text-white p-6">
          <h2 className="text-xl font-black flex items-center gap-2">
            {readonly ? (
              <>
                <Eye className="text-sky-400" size={22} />
                <span>Dados do Cliente</span>
              </>
            ) : (
              <>
                <Pencil className="text-amber-400" size={22} />
                <span>Editar Cliente</span>
              </>
            )}
          </h2>
        </div>

        <form onSubmit={handleSubmit} className="p-6 space-y-4">
          <div>
            <label className="block text-sm font-semibold text-stone-700 mb-1">CPF:</label>
            <input type="text" value={cliente.cpf} readOnly className={campoClass(true)} />
          </div>

          <div>
            <label className="block text-sm font-semibold text-stone-700 mb-1">E-mail:</label>
            <input type="email" value={cliente.email} readOnly className={campoClass(true)} />
          </div>

          <div>
            <label className="block text-sm font-semibold text-stone-700 mb-1">
              Nome Completo:
            </label>
            <input
              type="text"
              value={cliente.nome}
              readOnly={readonly}
              required={!readonly}
              onChange={(e) => setCliente({ ...cliente, nome: e.target.value })}
              className={campoClass(readonly)}
            />
          </div>

          <div>
            <label className="block text-sm font-semibold text-stone-700 mb-1">WhatsApp:</label>
            <input
              type="text"
              value={cliente.telefone}
              readOnly={readonly}
              required={!readonly}
              onChange={(e) => setCliente({ ...cliente, telefone: e.target.value })}
              className={campoClass(readonly)}
            />
          </div>

          <div>
            <label className="block text-sm font-semibold text-stone-700 mb-1">Chave PIX:</label>
            <input
              type="text"
              value={cliente.chave_pix}
              readOnly={readonly}
              onChange={(e) => setCliente({ ...cliente, chave_pix: e.target.value })}
              className={campoClass(readonly)}
            />
          </div>

          <div>
            <label className="block text-sm font-semibold text-stone-700 mb-1">
              Saldo de Crédito (R$):
            </label>
            <input
              type="number"
              step="0.01"
              min="0"
              value={cliente.saldo_credito}
              readOnly={readonly}
              required={!readonly}
              onChange={(e) => setCliente({ ...cliente, saldo_credito: e.target.value })}
              className={campoClass(readonly)}
            />
          </div>

          {!readonly && (
            <button
              type="submit"
              disabled={salvando}
              className="w-full py-3 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg transition shadow-md flex items-center justify-center gap-2 disabled:opacity-60"
            >
              <Save size={18} />
              <span>{salvando ? 'Salvando...' : 'Salvar Alterações'}</span>
            </button>
          )}
        </form>
      </div>
    </div>
  );
}