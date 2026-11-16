import { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { Eye, EyeOff, UserPlus, Camera, Trash2, AlertCircle, CheckCircle2 } from 'lucide-react';

const API_URL = 'http://localhost/brecho/index.php';

const inputClass =
  'w-full p-2.5 bg-white text-stone-700 border border-stone-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all shadow-sm';

export default function Cadastro() {
  const navigate = useNavigate();

  const [form, setForm] = useState({
    cpf: '',
    nome: '',
    email: '',
    senha: '',
    telefone: '',
    pix: ''
  });

  const [foto, setFoto] = useState(null);
  const [previewFoto, setPreviewFoto] = useState(null);
  const [mostrarSenha, setMostrarSenha] = useState(false);
  const [erro, setErro] = useState('');
  const [sucesso, setSucesso] = useState('');
  const [salvando, setSalvando] = useState(false);

  const handleChange = (campo, valor) => {
    setForm((prev) => ({ ...prev, [campo]: valor }));
  };

  const handleFotoChange = (e) => {
    const arquivo = e.target.files[0];
    if (arquivo) {
      setFoto(arquivo);
      setPreviewFoto(URL.createObjectURL(arquivo));
    }
  };

  const removerFoto = () => {
    setFoto(null);
    setPreviewFoto(null);
  };

  const validarFormulario = () => {
    const cpfLimpo = form.cpf.replace(/\D/g, '');
    if (cpfLimpo.length !== 11) {
      return 'Informe um CPF válido com 11 dígitos.';
    }
    if (form.nome.trim().length < 3) {
      return 'Informe seu nome completo.';
    }
    if (!form.email.includes('@')) {
      return 'Informe um endereço de e-mail válido.';
    }
    if (form.senha.length < 4) {
      return 'A senha deve ter pelo menos 4 caracteres.';
    }
    if (form.telefone.replace(/\D/g, '').length < 10) {
      return 'Informe um número de WhatsApp válido com DDD.';
    }
    return null;
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setErro('');
    setSucesso('');

    const erroValidacao = validarFormulario();
    if (erroValidacao) {
      setErro(erroValidacao);
      return;
    }

    setSalvando(true);

    const formData = new FormData();
    formData.append('cpf', form.cpf);
    formData.append('nome', form.nome);
    formData.append('email', form.email);
    formData.append('senha', form.senha);
    formData.append('telefone', form.telefone);
    formData.append('pix', form.pix);
    if (foto) {
      formData.append('foto', foto);
    }

    try {
      const resposta = await fetch(`${API_URL}?acao=salvar_cadastro`, {
        method: 'POST',
        body: formData
      });

      const dados = await resposta.json();

      if (resposta.ok && dados.sucesso) {
        setSucesso(dados.mensagem || 'Cadastro realizado com sucesso!');
        setTimeout(() => navigate('/login'), 1500);
      } else {
        setErro(dados.mensagem || 'Não foi possível concluir o cadastro.');
      }
    } catch (error) {
      console.error('Erro ao cadastrar:', error);
      setErro('Erro de conexão com o servidor.');
    } finally {
      setSalvando(false);
    }
  };

  return (
    <div className="max-w-2xl mx-auto my-6">
      <div className="bg-white border border-stone-200 rounded-2xl shadow-sm overflow-hidden">
        <div className="bg-stone-800 text-white p-6">
          <h2 className="text-2xl font-black tracking-tight flex items-center gap-2">
            <UserPlus className="text-emerald-400" size={24} />
            <span>Cadastro de Cliente</span>
          </h2>
          <p className="text-stone-400 text-sm mt-1">
            Crie sua conta para acompanhar lotes, receber propostas e reservar peças na vitrine.
          </p>
        </div>

        <form onSubmit={handleSubmit} className="p-6 md:p-8 space-y-6">
          {erro && (
            <div className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm font-medium flex items-center gap-2">
              <AlertCircle size={18} className="shrink-0 text-red-500" />
              <span>{erro}</span>
            </div>
          )}

          {sucesso && (
            <div className="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-lg text-sm font-medium flex items-center gap-2">
              <CheckCircle2 size={18} className="shrink-0 text-emerald-600" />
              <span>{sucesso} Redirecionando para o login...</span>
            </div>
          )}

          <div className="flex flex-col items-center justify-center p-4 bg-stone-50 border border-stone-200 rounded-xl">
            <div className="relative">
              {previewFoto ? (
                <img
                  src={previewFoto}
                  alt="Preview do perfil"
                  className="w-24 h-24 rounded-full object-cover border-4 border-emerald-500 shadow"
                />
              ) : (
                <div className="w-24 h-24 rounded-full bg-stone-200 border-2 border-dashed border-stone-400 flex flex-col items-center justify-center text-stone-500">
                  <Camera size={28} />
                </div>
              )}

              {previewFoto && (
                <button
                  type="button"
                  onClick={removerFoto}
                  title="Remover foto"
                  className="absolute -bottom-1 -right-1 bg-red-500 hover:bg-red-600 text-white p-1.5 rounded-full shadow transition"
                >
                  <Trash2 size={14} />
                </button>
              )}
            </div>

            <label className="mt-3 cursor-pointer inline-flex items-center gap-2 px-4 py-1.5 bg-white border border-stone-300 hover:bg-stone-100 text-stone-700 text-xs font-bold rounded-lg shadow-sm transition">
              <Camera size={14} className="text-emerald-600" />
              <span>{previewFoto ? 'Trocar Foto de Perfil' : 'Escolher Foto de Perfil (Opcional)'}</span>
              <input
                type="file"
                accept="image/*"
                onChange={handleFotoChange}
                className="hidden"
              />
            </label>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-semibold text-stone-700 mb-1.5">
                CPF (obrigatório):
              </label>
              <input
                type="text"
                required
                placeholder="000.000.000-00"
                value={form.cpf}
                onChange={(e) => handleChange('cpf', e.target.value)}
                className={inputClass}
              />
            </div>

            <div>
              <label className="block text-sm font-semibold text-stone-700 mb-1.5">
                WhatsApp (obrigatório):
              </label>
              <input
                type="text"
                required
                placeholder="(51) 99999-9999"
                value={form.telefone}
                onChange={(e) => handleChange('telefone', e.target.value)}
                className={inputClass}
              />
            </div>

            <div className="md:col-span-2">
              <label className="block text-sm font-semibold text-stone-700 mb-1.5">
                Nome Completo (obrigatório):
              </label>
              <input
                type="text"
                required
                placeholder="Seu nome completo"
                value={form.nome}
                onChange={(e) => handleChange('nome', e.target.value)}
                className={inputClass}
              />
            </div>

            <div>
              <label className="block text-sm font-semibold text-stone-700 mb-1.5">
                E-mail (obrigatório):
              </label>
              <input
                type="email"
                required
                placeholder="seu@email.com"
                value={form.email}
                onChange={(e) => handleChange('email', e.target.value)}
                className={inputClass}
              />
            </div>

            <div>
              <label className="block text-sm font-semibold text-stone-700 mb-1.5">
                Senha (obrigatória):
              </label>
              <div className="relative">
                <input
                  type={mostrarSenha ? 'text' : 'password'}
                  required
                  placeholder="Crie uma senha"
                  value={form.senha}
                  onChange={(e) => handleChange('senha', e.target.value)}
                  className={`${inputClass} pr-11`}
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

            <div className="md:col-span-2">
              <label className="block text-sm font-semibold text-stone-700 mb-1.5">
                Chave PIX (para recebimento de propostas):
              </label>
              <input
                type="text"
                placeholder="CPF, E-mail, Telefone ou Chave Aleatória"
                value={form.pix}
                onChange={(e) => handleChange('pix', e.target.value)}
                className={inputClass}
              />
            </div>
          </div>

          <button
            type="submit"
            disabled={salvando}
            className="w-full py-3.5 px-6 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-base rounded-lg transition shadow-md flex items-center justify-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed"
          >
            {salvando ? (
              <div className="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
            ) : (
              <>
                <UserPlus size={18} />
                <span>Cadastrar</span>
              </>
            )}
          </button>

          <div className="pt-4 border-t border-stone-100 text-center text-sm text-stone-600">
            Já possui uma conta?{' '}
            <Link
              to="/login"
              className="font-bold text-emerald-600 hover:text-emerald-700 hover:underline"
            >
              Faça login aqui
            </Link>
          </div>
        </form>
      </div>
    </div>
  );
}