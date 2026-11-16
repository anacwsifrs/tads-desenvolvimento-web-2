import { ShieldCheck, Clock, Award, FileCheck2 } from 'lucide-react';

export default function Termos() {
  return (
    <div className="max-w-3xl mx-auto my-6">
      <div className="bg-white border border-stone-200 rounded-2xl shadow-sm overflow-hidden">
        <div className="bg-stone-800 text-white p-6 md:p-8">
          <div className="inline-flex items-center gap-2 bg-emerald-600/20 border border-emerald-500/30 text-emerald-300 text-xs font-bold px-3 py-1 rounded-full mb-3">
            <FileCheck2 size={14} />
            <span>Documentação Legal & Acadêmica</span>
          </div>
          <h2 className="text-2xl md:text-3xl font-black tracking-tight">
            Termos de Serviço, Política de Privacidade (LGPD) e Declaração Anti-Plágio
          </h2>
        </div>

        <div className="p-6 md:p-8 space-y-6 text-stone-600 leading-relaxed">
          <section className="p-5 bg-stone-50 border border-stone-200 rounded-xl space-y-2">
            <h3 className="text-lg font-bold text-stone-800 flex items-center gap-2">
              <ShieldCheck className="text-emerald-600" size={20} />
              <span>1. Proteção de Dados Pessoais (LGPD)</span>
            </h3>
            <p className="text-sm md:text-base">
              Em conformidade com a Lei Geral de Proteção de Dados (Lei nº 13.709/2018), o sistema{' '}
              <strong className="text-stone-800">Brechó EcoModa</strong> garante que os dados cadastrais
              (CPF, e-mail, telefone e chave PIX) são utilizados exclusivamente para o gerenciamento de
              lotes e pagamentos na plataforma, não sendo compartilhados com terceiros.
            </p>
          </section>

          <section className="p-5 bg-stone-50 border border-stone-200 rounded-xl space-y-2">
            <h3 className="text-lg font-bold text-stone-800 flex items-center gap-2">
              <Clock className="text-amber-500" size={20} />
              <span>2. Condições de Avaliação e Prazos</span>
            </h3>
            <p className="text-sm md:text-base">
              Os lotes cadastrados passam por triagem física. Caso a proposta apresentada pela loja não
              seja respondida em até <strong className="text-stone-800">3 dias úteis</strong>, ou caso a
              proposta seja recusada e as peças não sejam retiradas em até{' '}
              <strong className="text-stone-800">3 dias úteis</strong>, as roupas serão automaticamente
              destinadas para doação.
            </p>
          </section>

          <section className="p-5 bg-emerald-50/60 border border-emerald-200 rounded-xl space-y-2">
            <h3 className="text-lg font-bold text-stone-800 flex items-center gap-2">
              <Award className="text-emerald-600" size={20} />
              <span>3. Declaração de Integridade Acadêmica e Anti-Plágio</span>
            </h3>
            <p className="text-sm md:text-base text-stone-700">
              Declara-se para fins acadêmicos que este projeto de aplicação web é de autoria individual e
              desenvolvido estritamente para avaliação na disciplina de Desenvolvimento Web, respeitando os
              direitos autorais e as diretrizes estipuladas no edital.
            </p>
          </section>
        </div>
      </div>
    </div>
  );
}