<!-- Filtros por Mes y Año -->
<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">
            <i class="fas fa-filter"></i>
            Filtros de Período
        </h5>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('contabilidad.index') }}" class="row g-3">
            <div class="col-md-3">
                <label for="mes" class="form-label">
                    <i class="fas fa-calendar-alt"></i>
                    Mes
                </label>
                <select class="form-select" id="mes" name="mes">
                    <option value="1" {{ ($mes ?? now()->month) == 1 ? 'selected' : '' }}>Enero</option>
                    <option value="2" {{ ($mes ?? now()->month) == 2 ? 'selected' : '' }}>Febrero</option>
                    <option value="3" {{ ($mes ?? now()->month) == 3 ? 'selected' : '' }}>Marzo</option>
                    <option value="4" {{ ($mes ?? now()->month) == 4 ? 'selected' : '' }}>Abril</option>
                    <option value="5" {{ ($mes ?? now()->month) == 5 ? 'selected' : '' }}>Mayo</option>
                    <option value="6" {{ ($mes ?? now()->month) == 6 ? 'selected' : '' }}>Junio</option>
                    <option value="7" {{ ($mes ?? now()->month) == 7 ? 'selected' : '' }}>Julio</option>
                    <option value="8" {{ ($mes ?? now()->month) == 8 ? 'selected' : '' }}>Agosto</option>
                    <option value="9" {{ ($mes ?? now()->month) == 9 ? 'selected' : '' }}>Septiembre</option>
                    <option value="10" {{ ($mes ?? now()->month) == 10 ? 'selected' : '' }}>Octubre</option>
                    <option value="11" {{ ($mes ?? now()->month) == 11 ? 'selected' : '' }}>Noviembre</option>
                    <option value="12" {{ ($mes ?? now()->month) == 12 ? 'selected' : '' }}>Diciembre</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="anio" class="form-label">
                    <i class="fas fa-calendar"></i>
                    Año
                </label>
                <select class="form-select" id="anio" name="anio">
                    @for($year = 2020; $year <= now()->year + 5; $year++)
                        <option value="{{ $year }}" {{ ($anio ?? now()->year) == $year ? 'selected' : '' }}>
                            {{ $year }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                        Filtrar
                    </button>
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                    <a href="{{ route('contabilidad.index') }}" class="btn btn-secondary">
                        <i class="fas fa-refresh"></i>
                        Limpiar
                    </a>
                </div>
            </div>
        </form>
        
        <!-- Información del período seleccionado -->
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle"></i>
                    <strong>Período seleccionado:</strong> 
                    @php
                        $meses = [
                            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
                            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
                            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
                        ];
                    @endphp
                    {{ $meses[$mes ?? now()->month] }} {{ $anio ?? now()->year }}
                </div>
            </div>
        </div>
    </div>
</div>
