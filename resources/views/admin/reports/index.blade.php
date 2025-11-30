@extends('layouts.dashboard')

@section('title', 'Gestión de Reportes')
@section('header', 'Reportes de Productos')

@section('content')
<div class="admin-reports-container">
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Usuario (Reportante)</th>
                            <th>Producto Reportado</th>
                            <th>Agricultor (Vendedor)</th>
                            <th>Motivo</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reports as $report)
                            <tr>
                                <td>#{{ $report->id }}</td>
                                <td>
                                    <div class="user-info">
                                        <span class="user-name">{{ $report->user->name }}</span>
                                        <span class="user-email">{{ $report->user->email }}</span>
                                    </div>
                                </td>
                                <td>
                                    <a href="{{ route('products.show', $report->product->slug) }}" target="_blank">
                                        {{ $report->product->name }}
                                    </a>
                                </td>
                                <td>
                                    <div class="user-info">
                                        <span class="user-name">{{ $report->product->user->name }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-reason">{{ $report->reason }}</span>
                                </td>
                                <td>
                                    <span class="badge badge-status status-{{ $report->status }}">
                                        {{ ucfirst($report->status) }}
                                    </span>
                                </td>
                                <td>{{ $report->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                <td>
                                    <button class="btn-view-report" onclick="viewReport({{ $report->id }})">
                                        <i class="fas fa-eye"></i> Ver Detalle
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div style="display: flex; flex-direction: column; align-items: center; gap: 10px; color: #a0aec0;">
                                        <i class="fas fa-clipboard-check" style="font-size: 3rem; opacity: 0.5;"></i>
                                        <p>No hay reportes registrados.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4 px-4 pb-4">
                {{ $reports->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Report Detail Modal -->
<div id="reportDetailModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Reporte #<span id="modal-report-id"></span></h2>
            <span class="close-modal" onclick="closeReportModal()">&times;</span>
        </div>
        
        <div class="modal-body">
            <div class="report-details-grid">
                <div class="detail-group">
                    <label>Reportado por</label>
                    <p id="modal-reporter"></p>
                </div>
                <div class="detail-group">
                    <label>Producto</label>
                    <p id="modal-product"></p>
                </div>
                <div class="detail-group">
                    <label>Motivo</label>
                    <p id="modal-reason"></p>
                </div>
                <div class="detail-group">
                    <label>Estado Actual</label>
                    <select id="modal-status" class="form-control">
                        <option value="pending">Pendiente</option>
                        <option value="reviewed">Revisado</option>
                        <option value="resolved">Resuelto</option>
                        <option value="dismissed">Desestimado</option>
                    </select>
                </div>
                <div class="detail-group full-width">
                    <label>Descripción</label>
                    <p id="modal-description" class="description-box"></p>
                </div>
                <div class="detail-group full-width">
                    <label>Evidencias Adjuntas</label>
                    <div id="modal-evidence" class="evidence-grid"></div>
                </div>
                
                <div class="detail-group full-width">
                    <label>Notas del Administrador</label>
                    <textarea id="modal-admin-notes" class="form-control" rows="3" placeholder="Añade notas internas sobre la resolución de este reporte..."></textarea>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeReportModal()">Cancelar</button>
            <button class="btn btn-primary" onclick="updateReportStatus()">Guardar Cambios</button>
        </div>
    </div>
</div>

@push('styles')
    @vite(['resources/css/admin/reports.css'])
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/js/admin/reports.js'])
@endpush
@endsection
