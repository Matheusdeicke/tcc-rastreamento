<div
    id="addItemsModal"
    class="fixed inset-0 hidden items-center justify-center bg-black/40 z-50"
>
    <div class="bg-white rounded-xl shadow-lg w-full max-w-3xl p-4 sm:p-6 max-h-[80vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-brand-800">Adicionar peças ao kit</h2>
            <button
                type="button"
                onclick="closeAddItemsModal()"
                class="text-gray-400 hover:text-gray-600 text-2xl leading-none"
            >
                &times;
            </button>
        </div>

        <form action="{{ route('kits.items.storeMany', $kit) }}" method="POST">
            @csrf

            <div id="itemsContainer" class="space-y-3">
                {{-- Linhas serão inseridas via JavaScript --}}
            </div>

            <button
                type="button"
                onclick="addItemRow()"
                class="mt-3 inline-flex items-center text-sm font-medium text-emerald-700 hover:text-emerald-900"
            >
                + Adicionar mais peças
            </button>

            <div class="mt-6 flex justify-end gap-2">
                <button
                    type="button"
                    onclick="closeAddItemsModal()"
                    class="px-4 py-2 text-sm rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50"
                >
                    Cancelar
                </button>
                <button
                    type="submit"
                    class="px-4 py-2 text-sm rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 font-semibold"
                >
                    Salvar peças
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let itemIndex = 0;

    function openAddItemsModal() {
        const modal = document.getElementById('addItemsModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        const container = document.getElementById('itemsContainer');
        if (container.childElementCount === 0) {
            for (let i = 0; i < 5; i++) {
                addItemRow();
            }
        }
    }

    function closeAddItemsModal() {
        const modal = document.getElementById('addItemsModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function addItemRow() {
        const container = document.getElementById('itemsContainer');

        const row = document.createElement('div');
        row.className = 'grid grid-cols-12 gap-2 items-end border border-gray-200 rounded-lg p-3 bg-gray-50';

        row.innerHTML = `
            <div class="col-span-5">
                <label class="block text-xs text-gray-600 mb-1">Nome da peça</label>
                <input
                    type="text"
                    name="items[${itemIndex}][nome]"
                    class="w-full border rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-brand-700"
                >
            </div>
            <div class="col-span-2">
                <label class="block text-xs text-gray-600 mb-1">Código</label>
                <input
                    type="text"
                    name="items[${itemIndex}][codigo]"
                    class="w-full border rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-brand-700"
                >
            </div>
            <div class="col-span-2">
                <label class="block text-xs text-gray-600 mb-1">Quantidade</label>
                <input
                    type="number"
                    name="items[${itemIndex}][quantidade]"
                    min="1"
                    value="1"
                    class="w-full border rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-brand-700"
                >
            </div>
            <div class="col-span-2">
                <label class="block text-xs text-gray-600 mb-1">Observações</label>
                <input
                    type="text"
                    name="items[${itemIndex}][observacoes]"
                    class="w-full border rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-brand-700"
                >
            </div>
            <div class="col-span-1 flex justify-end">
                <button
                    type="button"
                    onclick="this.closest('div.grid').remove()"
                    class="text-xs text-rose-600 hover:text-rose-800"
                >
                    Remover
                </button>
            </div>
        `;

        container.appendChild(row);
        itemIndex++;
    }
</script>
