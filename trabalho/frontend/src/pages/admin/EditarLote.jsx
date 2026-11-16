import { useState, useEffect } from 'react';
import { useParams, useNavigate, Link } from 'react-router-dom';
import { ArrowLeft, Save, Package } from 'lucide-react';

const API_URL = 'http://localhost/brecho/index.php';

const inputClass =
  'w-full p-2.5 bg-white text-stone-700 border border-stone-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all shadow-sm';

export default function EditarLote() {
  const { id } = useParams();
  const navigate = useNavigate();

  const [form, setForm] = useState({
    cliente_nome: '',
    telefone: '',
    chave_pix: '',
    descricao: ''
  });
  const [carregando, setCarregando] = useState(true);
  const [salvando, setSalvando] = useState(false);

  useEffect(() => {
    const carregarLote = async () => {
      try {
        const resposta = await fetch(`${API_URL}?acao=editar&id=${id}`, {
          credentials: 'include'
        });
        const dados = await resposta.json();
        if (resposta.ok && dados.id) {
          setForm({
            cliente_nome: dados.cliente_nome || '',
            telefone: dados.telefone || '',
            chave_pix: dados.chave_pix || '',
            descricao: dados.descricao || ''
          });
        }
      } catch (error) {
        console.error('Erro ao carregar lote:', error);
      } finally {
        setCarregando(false);
      }
    };
    if (id) carregarLote();
  }, [id]);

  const handleSubmit = async (e) => {
    e.preventDefault();
    setSalvando(true);
    try {
      const resposta = await fetch(`${API_URL}?acao=salvar_edicao`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include',
        body: JSON.stringify({
          lote_id: id,
          descricao_lote: form.descricao
        })
      });
      const dados = await resposta.json();
      if (resposta.ok && dados.sucesso) {
        alert(dados.mensagem || 'Lote atualizado com sucesso!');
        navigate('/admin/lotes');
      } else {
        alert(dados.mensagem || 'Erro ao atualizar lote.');
      }
    } catch (error) {
      alert('Erro de conexão com o servidor.');
    } finally {
      setSalvando(false);
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
    <div className="max-w-xl mx-auto my-6">
      <Link
        to="/admin/lotes"
        className="inline-flex items-center gap-1.5 text-sm font-semibold text-stone-500 hover:text-emerald-700 mb-4 transition"
      >
        <ArrowLeft size={16} />
        <span>Voltar para Lotes</span>
      </Link>

      <div className="bg-white border border-stone-200 rounded-2xl shadow-sm overflow-hidden">
        <div className="bg-stone-800 text-white p-6">
          <h2 className="text-xl font-black flex items-center gap-2">
            <Package className="text-emerald-400" size={22} />
            <span>Editar Dados do Lote #{id}</span>
          </h2>
        </div>

        <form onSubmit={handleSubmit} className="p-6 space-y-4">
          <div>
            <label className="block text-sm font-semibold text-stone-700 mb-1">
              Nome do Cliente:
            </label>
            <input
              type="text"
              value={form.cliente_nome}
              readOnly
              className={`${inputClass} bg-stone-100 text-stone-500 cursor-not-allowed`}
            />
          </div>

          <div>
            <label className="block text-sm font-semibold text-stone-700 mb-1">
              WhatsApp:
            </label>
            <input
              type="text"
              value={form.telefone}
              readOnly
              className={`${inputClass} bg-stone-100 text-stone-500 cursor-not-allowed`}
            />
          </div>

          <div>
            <label className="block text-sm font-semibold text-stone-700 mb-1">
              Chave PIX:
            </label>
            <input
              type="text"
              value={form.chave_pix}
              readOnly
              className={`${inputClass} bg-stone-100 text-stone-500 cursor-not-allowed`}
            />
          </div>

          <div>
            <label className="block text-sm font-semibold text-stone-700 mb-1">
              Descrição do Lote:
            </label>
            <input
              type="text"
              required
              value={form.descricao}
              onChange={(e) => setForm({ ...form, descricao: e.target.value })}
              className={inputClass}
            />
          </div>

          <button
            type="submit"
            disabled={salvando}
            className="w-full py-3 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg transition shadow-md flex items-center justify-center gap-2 disabled:opacity-60"
          >
            <Save size={18} />
            <span>{salvando ? 'Salvando...' : 'Salvar Alterações'}</span>
          </button>
        </form>
      </div>
    </div>
  );
}