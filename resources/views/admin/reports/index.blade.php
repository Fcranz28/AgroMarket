@extends('layouts.dashboard')

@php
    use App\Modules\Moderation\Enums\ReportStatus;
    use App\Modules\Moderation\Models\Report;

    $statusColors = [
        ReportStatus::Pending->value => 'warning',
        ReportStatus::Reviewed->value => 'info',
        ReportStatus::Resolved->value => 'success',
        ReportStatus::Dismissed->value => 'neutral',
    ];
@endphp

@section('title', 'Reportes')
@section('header', 'Reportes de productos')

@section('content')
    <x-ui.card :padding="false">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[860px] text-left text-sm">
                <thead class="border-b border-line text-xs tracking-wide text-muted uppercase">
                    <tr>
                        <th scope="col" class="px-5 py-3 font-semibold">Reporte</th>
                        <th scope="col" class="px-5 py-3 font-semibold">Comprador</th>
                        <th scope="col" class="px-5 py-3 font-semibold">Producto / agricultor</th>
                        <th scope="col" class="px-5 py-3 font-semibold">Motivo</th>
                        <th scope="col" class="px-5 py-3 font-semibold">Estado</th>
                        <th scope="col" class="px-5 py-3 text-right font-semibold">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse ($reports as $report)
                        <tr class="transition hover:bg-canvas/60">
                            <td class="px-5 py-3">
                                <p class="font-medium text-ink">#{{ $report->id }}</p>
                                <p class="text-xs text-muted">{{ $report->created_at->format('d/m/Y H:i') }}</p>
                            </td>
                            <td class="px-5 py-3">
                                <p class="text-ink">{{ $report->user->name }}</p>
                                <p class="text-xs text-muted">{{ $report->user->email }}</p>
                            </td>
                            <td class="px-5 py-3">
                                <a href="{{ route('products.show', $report->product->slug) }}" target="_blank" class="font-medium text-ink hover:text-brand">{{ $report->product->name }}</a>
                                <p class="text-xs text-muted">{{ $report->product->user?->name }}</p>
                            </td>
                            <td class="px-5 py-3 text-ink">{{ Report::REASONS[$report->reason] ?? $report->reason }}</td>
                            <td class="px-5 py-3"><x-ui.badge :color="$statusColors[$report->status->value]">{{ $report->status->label() }}</x-ui.badge></td>
                            <td class="px-5 py-3 text-right">
                                @php
                                    $detail = [
                                        'id' => $report->id,
                                        'statusUrl' => route('admin.reports.status', $report),
                                        'reporter' => $report->user->name.' ('.$report->user->email.')',
                                        'product' => $report->product->name,
                                        'farmer' => $report->product->user?->name,
                                        'orderId' => $report->order_id,
                                        'reason' => Report::REASONS[$report->reason] ?? $report->reason,
                                        'description' => $report->description,
                                        'evidence' => $report->evidence ?? [],
                                        'status' => $report->status->value,
                                        'notes' => $report->admin_notes,
                                    ];
                                @endphp
                                <x-ui.button variant="secondary" size="sm" x-on:click="$dispatch('open-modal', { name: 'report-detail', data: {{ Js::from($detail) }} })"><i class="fas fa-eye"></i> Ver detalle</x-ui.button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-14 text-center text-muted">
                                <i class="fas fa-clipboard-check mb-2 block text-3xl text-brand"></i>
                                No hay reportes registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($reports->hasPages())
            <div class="border-t border-line px-5 py-4">{{ $reports->links() }}</div>
        @endif
    </x-ui.card>

    <x-ui.modal name="report-detail" title="Detalle del reporte" max-width="max-w-2xl">
        <form
            class="flex flex-col gap-5"
            x-data="{ status: '', notes: '', saving: false, error: null }"
            x-effect="status = data.status ?? ''; notes = data.notes ?? ''"
            x-on:submit.prevent="
                saving = true; error = null;
                fetch(data.statusUrl, {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                    body: JSON.stringify({ status, admin_notes: notes }),
                })
                    .then(async (response) => ({ ok: response.ok, body: await response.json() }))
                    .then(({ ok, body }) => {
                        if (! ok) { error = body.message ?? 'No se pudo guardar.'; return; }
                        $dispatch('notify', { type: 'success', message: body.message });
                        window.location.reload();
                    })
                    .catch(() => error = 'Ocurrió un error al guardar.')
                    .finally(() => saving = false)
            "
        >
            <dl class="grid gap-4 text-sm sm:grid-cols-2">
                <div><dt class="text-muted">Reporte</dt><dd class="font-medium text-ink">#<span x-text="data.id"></span> · pedido #<span x-text="data.orderId"></span></dd></div>
                <div><dt class="text-muted">Reportado por</dt><dd class="font-medium text-ink" x-text="data.reporter"></dd></div>
                <div><dt class="text-muted">Producto</dt><dd class="font-medium text-ink"><span x-text="data.product"></span> <span class="text-muted" x-text="data.farmer ? '· ' + data.farmer : ''"></span></dd></div>
                <div><dt class="text-muted">Motivo</dt><dd class="font-medium text-ink" x-text="data.reason"></dd></div>
                <div class="sm:col-span-2"><dt class="text-muted">Descripción</dt><dd class="mt-1 rounded-xl bg-canvas p-3 whitespace-pre-line text-ink" x-text="data.description"></dd></div>
                <div class="sm:col-span-2" x-show="data.evidence && data.evidence.length">
                    <dt class="text-muted">Evidencias</dt>
                    <dd class="mt-1 flex flex-wrap gap-2">
                        <template x-for="url in data.evidence">
                            <a x-bind:href="url" target="_blank"><img x-bind:src="url" alt="Evidencia" class="size-24 rounded-lg object-cover"></a>
                        </template>
                    </dd>
                </div>
            </dl>

            <div class="grid gap-4 sm:grid-cols-[200px_minmax(0,1fr)]">
                <div class="flex flex-col gap-1.5">
                    <label for="report-status" class="text-sm font-medium text-ink">Estado</label>
                    <select id="report-status" x-model="status" class="w-full rounded-xl border border-line bg-surface px-4 py-2.5 text-sm text-ink focus:border-brand focus:ring-2 focus:ring-brand/25 focus:outline-none">
                        @foreach (ReportStatus::cases() as $status)
                            <option value="{{ $status->value }}">{{ $status->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-col gap-1.5">
                    <label for="report-notes" class="text-sm font-medium text-ink">Notas internas</label>
                    <textarea id="report-notes" x-model="notes" rows="3" placeholder="Cómo se resolvió, a quién se contactó..." class="w-full rounded-xl border border-line bg-surface px-4 py-2.5 text-sm text-ink focus:border-brand focus:ring-2 focus:ring-brand/25 focus:outline-none"></textarea>
                </div>
            </div>

            <p class="text-xs text-danger" x-show="error" x-text="error"></p>

            <div class="flex justify-end gap-2">
                <x-ui.button variant="secondary" x-on:click="$dispatch('close-modal', { name: 'report-detail' })">Cancelar</x-ui.button>
                <x-ui.button type="submit" x-bind:disabled="saving">Guardar</x-ui.button>
            </div>
        </form>
    </x-ui.modal>
@endsection
