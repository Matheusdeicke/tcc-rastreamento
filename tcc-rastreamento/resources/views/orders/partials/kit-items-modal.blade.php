{{-- Modal de peças do kit --}}
<div
    id="kitItemsModal"
    class="fixed inset-0 hidden items-center justify-center bg-black/40 z-50"
>
    <div class="bg-white rounded-xl shadow-lg w-full max-w-3xl p-4 sm:p-6 max-h-[80vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 id="kitItemsTitle" class="text-lg font-semibold text-brand-800">
                    Peças do kit
                </h2>
                <p class="text-xs text-gray-500">
                    Visualize a composição do material selecionado antes de enviar a solicitação.
                </p>
            </div>
            <button
                type="button"
                onclick="closeKitItemsModal()"
                class="text-gray-400 hover:text-gray-600 text-2xl leading-none"
            >
                &times;
            </button>
        </div>

        <div id="kitItemsContent" class="bg-white rounded-xl ring-1 ring-gray-200 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr class="text-left font-semibold text-brand-800">
                        <th class="px-4 py-3">Peça</th>
                        <th class="px-4 py-3">Código</th>
                        <th class="px-4 py-3 text-center">Qtd.</th>
                        <th class="px-4 py-3">Observações</th>
                    </tr>
                </thead>
                <tbody id="kitItemsTableBody" class="divide-y divide-gray-100">
                    {{-- preenchido via JS --}}
                </tbody>
            </table>
        </div>

        <div id="kitItemsEmpty" class="hidden px-4 py-6 text-center text-gray-500 text-sm">
            Este kit ainda não possui peças cadastradas.
        </div>

        <div class="mt-4 flex justify-end">
            <button
                type="button"
                onclick="closeKitItemsModal()"
                class="px-4 py-2 text-sm rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50"
            >
                Fechar
            </button>
        </div>
    </div>
</div>

<script>
    const kitSelect = document.getElementById('kit_id');
    const btnVerPecas = document.getElementById('btnVerPecas');

    if (kitSelect && btnVerPecas) {
        kitSelect.addEventListener('change', function () {
            const option = this.options[this.selectedIndex];
            if (option && option.value) {
                btnVerPecas.disabled = false;
            } else {
                btnVerPecas.disabled = true;
            }
        });
    }

    function openKitItemsModal() {
        const select = document.getElementById('kit_id');
        if (!select) return;

        const option = select.options[select.selectedIndex];
        if (!option || !option.value) return;

        const nomeKit = option.dataset.nome || 'Kit';
        const itemsJson = option.dataset.items || '[]';

        let items = [];
        try {
            items = JSON.parse(itemsJson);
        } catch (e) {
            console.error('Erro ao parsear itens do kit:', e);
            items = [];
        }

        const modal   = document.getElementById('kitItemsModal');
        const title   = document.getElementById('kitItemsTitle');
        const tbody   = document.getElementById('kitItemsTableBody');
        const empty   = document.getElementById('kitItemsEmpty');
        const content = document.getElementById('kitItemsContent');

        title.textContent = `Peças do kit: ${nomeKit}`;
        tbody.innerHTML = '';

        if (!items.length) {
            content.classList.add('hidden');
            empty.classList.remove('hidden');
        } else {
            content.classList.remove('hidden');
            empty.classList.add('hidden');

            items.forEach(function (item) {
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-gray-50/60';

                tr.innerHTML = `
                    <td class="px-4 py-3 font-medium text-gray-800">${item.nome ?? ''}</td>
                    <td class="px-4 py-3 text-gray-700">${item.codigo ?? '—'}</td>
                    <td class="px-4 py-3 text-center">${item.quantidade ?? ''}</td>
                    <td class="px-4 py-3 text-gray-600">${item.observacoes ?? '—'}</td>
                `;

                tbody.appendChild(tr);
            });
        }

        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeKitItemsModal() {
        const modal = document.getElementById('kitItemsModal');
        if (!modal) return;
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
</script>
