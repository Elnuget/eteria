<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="mb-0">
            <i class="fas fa-calculator"></i>
            Contabilidad
        </h3>
        <a href="{{ route('contabilidad.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            Nuevo Registro
        </a>
    </div>

    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($contabilidad->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Fecha</th>
                            <th>Motivo</th>
                            <th>Valor</th>
                            <th>Usuario</th>
                            <th>Registrado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($contabilidad as $registro)
                            <tr>
                                <td>
                                    <i class="fas fa-calendar text-primary"></i>
                                    {{ $registro->fecha->format('d/m/Y') }}
                                </td>
                                <td>{{ $registro->motivo }}</td>
                                <td>
                                    <span class="badge bg-{{ $registro->valor >= 0 ? 'success' : 'danger' }}">
                                        ${{ number_format(abs($registro->valor), 2) }}
                                        {{ $registro->valor >= 0 ? '(Ingreso)' : '(Gasto)' }}
                                    </span>
                                </td>
                                <td>
                                    <i class="fas fa-user text-secondary"></i>
                                    {{ $registro->usuario->name ?? 'Usuario no encontrado' }}
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ $registro->created_at->format('d/m/Y H:i') }}
                                    </small>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('contabilidad.show', $registro->id) }}" 
                                           class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('contabilidad.edit', $registro->id) }}" 
                                           class="btn btn-sm btn-outline-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('contabilidad.destroy', $registro->id) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('¿Estás seguro de eliminar este registro?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="d-flex justify-content-center mt-4">
                {{ $contabilidad->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-calculator fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">No hay registros de contabilidad</h4>
                <p class="text-muted">Comienza agregando tu primer registro contable.</p>
                <a href="{{ route('contabilidad.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    Crear Primer Registro
                </a>
            </div>
        @endif
    </div>
</div>
