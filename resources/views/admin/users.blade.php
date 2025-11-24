@extends('layouts.dashboard')

@section('title', 'Gestión de Usuarios')
@section('header', 'Usuarios y Verificaciones')

@section('content')
    <div class="table-container">
        <div class="table-responsive">
            <table class="table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background-color: #f8f9fa; border-bottom: 2px solid #e9ecef;">
                        <th style="padding: 12px; text-align: left;">ID</th>
                        <th style="padding: 12px; text-align: left;">Nombre</th>
                        <th style="padding: 12px; text-align: left;">Email</th>
                        <th style="padding: 12px; text-align: left;">Rol</th>
                        <th style="padding: 12px; text-align: left;">Estado Verificación</th>
                        <th style="padding: 12px; text-align: left;">Documento</th>
                        <th style="padding: 12px; text-align: left;">Estado Cuenta</th>
                        <th style="padding: 12px; text-align: left;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr style="border-bottom: 1px solid #e9ecef;">
                            <td style="padding: 12px;">{{ $user->id }}</td>
                            <td style="padding: 12px;">{{ $user->name }}</td>
                            <td style="padding: 12px;">{{ $user->email }}</td>
                            <td style="padding: 12px;">
                                <span style="padding: 4px 8px; border-radius: 4px; font-size: 0.85rem; background-color: {{ $user->role == 'admin' ? '#dc3545' : ($user->role == 'farmer' ? '#28a745' : '#007bff') }}; color: white;">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td style="padding: 12px;">
                                @if($user->role == 'farmer')
                                    <span style="padding: 4px 8px; border-radius: 4px; font-size: 0.85rem; background-color: {{ $user->verification_status == 'approved' ? '#28a745' : ($user->verification_status == 'pending' ? '#ffc107' : '#6c757d') }}; color: {{ $user->verification_status == 'pending' ? '#333' : 'white' }};">
                                        {{ ucfirst($user->verification_status) }}
                                    </span>
                                @else
                                    -
                                @endif
                            </td>
                            <td style="padding: 12px;">
                                @if($user->verification_document)
                                    <a href="{{ Storage::url($user->verification_document) }}" target="_blank" style="color: #17a2b8; text-decoration: none;"><i class="fas fa-file-alt"></i> Ver Doc</a>
                                @else
                                    -
                                @endif
                            </td>
                            <td style="padding: 12px;">
                                <span style="color: {{ $user->is_banned ? '#dc3545' : '#28a745' }}; font-weight: bold;">
                                    {{ $user->is_banned ? 'Suspendido' : 'Activo' }}
                                </span>
                            </td>
                            <td style="padding: 12px;">
                                <div style="display: flex; gap: 5px;">
                                    @if($user->role == 'farmer' && $user->verification_status == 'pending')
                                        <form action="{{ route('admin.verify', ['user' => $user->id, 'status' => 'approved']) }}" method="POST">
                                            @csrf
                                            <button type="submit" style="background:none; border:none; color:#28a745; cursor:pointer;" title="Aprobar"><i class="fas fa-check-circle fa-lg"></i></button>
                                        </form>
                                        <form action="{{ route('admin.verify', ['user' => $user->id, 'status' => 'rejected']) }}" method="POST">
                                            @csrf
                                            <button type="submit" style="background:none; border:none; color:#dc3545; cursor:pointer;" title="Rechazar"><i class="fas fa-times-circle fa-lg"></i></button>
                                        </form>
                                    @endif

                                    @if($user->role != 'admin')
                                        <form action="{{ route('admin.ban', $user->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" style="background:none; border:none; color:{{ $user->is_banned ? '#28a745' : '#333' }}; cursor:pointer;" title="{{ $user->is_banned ? 'Activar' : 'Suspender' }}">
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
        
        <div style="margin-top: 20px;">
            {{ $users->links() }}
        </div>
    </div>
@endsection
