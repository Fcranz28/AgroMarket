@extends('layouts.dashboard')

@section('title', 'Gestión de Usuarios')
@section('header', 'Usuarios y Verificaciones')

@section('content')
    {{-- Styles loaded via dashboard.css --}}

    <div class="users-container">
        <!-- Search and Filter Section -->
        <div class="actions-bar">
            <form action="{{ route('admin.users') }}" method="GET">
                <input type="text" name="search" placeholder="Buscar por nombre o email..." value="{{ request('search') }}" class="form-control">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Buscar
                </button>
                @if(request('search') || request('sort'))
                    <a href="{{ route('admin.users') }}" class="btn btn-secondary" title="Limpiar filtros">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
                
                <a href="{{ route('admin.users', ['sort' => 'reports_desc', 'search' => request('search')]) }}" class="btn {{ request('sort') == 'reports_desc' ? 'btn-primary' : 'btn-secondary' }}">
                    <i class="fas fa-exclamation-triangle"></i> Más Reportados
                </a>
            </form>
        </div>

        <div class="card-table">
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Rol</th>
                            <th>Reportes</th>
                            <th>Estado Verificación</th>
                            <th>Documento</th>
                            <th>Estado Cuenta</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>#{{ $user->id }}</td>
                                <td>
                                    <div style="display: flex; flex-direction: column;">
                                        <span style="font-weight: 600; color: var(--text-main);">{{ $user->name }}</span>
                                        <span style="font-size: 0.85rem; color: var(--text-light);">{{ $user->email }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-role-{{ $user->role }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td>
                                    @if($user->role == 'farmer')
                                        <span class="badge {{ $user->reports_received_count > 0 ? 'badge-danger' : 'badge-secondary' }}" style="{{ $user->reports_received_count > 0 ? 'background-color: #fee2e2; color: #dc2626;' : '' }}">
                                            {{ $user->reports_received_count }}
                                        </span>
                                    @else
                                        <span style="color: var(--text-light);">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->role == 'farmer')
                                        <span class="badge badge-status-{{ $user->verification_status }}">
                                            @if($user->verification_status == 'pending')
                                                <i class="fas fa-clock"></i>
                                            @elseif($user->verification_status == 'approved')
                                                <i class="fas fa-check-circle"></i>
                                            @endif
                                            {{ ucfirst($user->verification_status) }}
                                        </span>
                                    @else
                                        <span style="color: var(--text-light);">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->verification_document)
                                        <a href="{{ Storage::url($user->verification_document) }}" target="_blank" class="action-btn btn-icon-doc" title="Ver Documento">
                                            <i class="fas fa-file-alt fa-lg"></i>
                                        </a>
                                    @else
                                        <span style="color: var(--text-light);">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="{{ $user->is_banned ? 'badge-banned' : 'badge-active' }}">
                                        {{ $user->is_banned ? 'Suspendido' : 'Activo' }}
                                    </span>
                                </td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        @if($user->role == 'farmer' && $user->verification_status == 'pending')
                                            {{-- Placeholder Verify Button --}}
                                            <a href="{{ route('admin.verify.view', $user->id) }}" class="btn-verify">
                                                <i class="fas fa-user-check"></i> Verificar
                                            </a>
                                        @endif
                                        
                                        @if($user->role != 'admin')
                                            <form action="{{ route('admin.ban', $user->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="action-btn {{ $user->is_banned ? 'btn-icon-unlock' : 'btn-icon-ban' }}" title="{{ $user->is_banned ? 'Activar' : 'Suspender' }}">
                                                    <i class="fas fa-{{ $user->is_banned ? 'unlock' : 'ban' }}"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="pagination-wrapper">
            {{ $users->links() }}
        </div>
    </div>
@endsection
