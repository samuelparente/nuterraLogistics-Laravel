@extends('layouts.master_admin')

@section('content_admin')
<main class="content">
    <div class="container-fluid p-0">

        <div class="mb-3">
            <h1 class="h3 mb-3"><strong>EVENTOS</strong> | Novo</h1>
        </div>

        <div class="row">
            <div class="col-12 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Inserir Novo Evento</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('events.event.store') }}" method="POST">
                            @csrf

                            <!-- Título do Evento -->
                            <div class="mb-3">
                                <label for="title" class="form-label">Título</label>
                                <input type="text" name="title" id="title" class="form-control" placeholder="Título do Evento" required>
                            </div>

                            <!-- Descrição do Evento -->
                            <div class="mb-3">
                                <label for="description" class="form-label">Descrição</label>
                                <textarea name="description" id="description" class="form-control" placeholder="Descrição do Evento"></textarea>
                            </div>

                            <!-- Data e Hora de Início -->
                            <div class="mb-3">
                                <label for="event_date" class="form-label">Data e Hora de Início</label>
                                <input type="datetime-local" name="event_date" id="event_date" class="form-control" required>
                            </div>

                            <!-- Data e Hora de Fim -->
                            <div class="mb-3">
                                <label for="end_date" class="form-label">Data e Hora de Fim</label>
                                <input type="datetime-local" name="end_date" id="end_date" class="form-control">
                            </div>

                            <!-- Local do Evento -->
                            <div class="mb-3">
                                <label for="location" class="form-label">Local</label>
                                <input type="text" name="location" id="location" class="form-control" placeholder="Local do Evento">
                            </div>

                            <!-- Capacidade -->
                            <div class="mb-3">
                                <label for="capacity" class="form-label">Capacidade</label>
                                <input type="number" name="capacity" id="capacity" class="form-control" placeholder="Capacidade Máxima">
                            </div>

                            <!-- Tipo de Evento -->
                            <div class="mb-3">
                                <label for="event_type_id" class="form-label">Tipo</label>
                                <select name="event_type_id" id="event_type_id" class="form-select" required>
                                    <option value="">Selecione...</option>
                                    @foreach($eventTypes as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="institution_id" class="form-label">Instituição</label>
                                <select name="institution_id" id="institution_id" class="form-select" required>
                                    <option value="">Selecione...</option>
                                    @foreach($institutions as $institution)
                                        <option value="{{ $institution->id }}">{{ $institution->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Estado do Evento -->
                            <div class="mb-3">
                                <label for="event_status_id" class="form-label">Estado</label>
                                <select name="event_status_id" id="event_status_id" class="form-select" required>
                                    <option value="">Selecione...</option>
                                    @foreach($eventStatuses as $status)
                                        <option value="{{ $status->id }}">{{ $status->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Seleção de Destinatários -->
                            <div class="mb-3">
                                <label class="form-label">Destinatários</label>
                                
                            <!-- Guardians -->
                            <div class="form-check">
                                <input type="checkbox" name="guardians" id="guardians" class="form-check-input" onchange="this.form.guardians_hidden.value=this.checked ? 'true' : 'false'">
                                <input type="hidden" name="guardians_hidden" value="false">
                                <label for="guardians" class="form-check-label">Todos os Encarregados de Educação associados à Instituição.</label>
                            </div>

                            <!-- Staff -->
                            <div class="form-check">
                                <input type="checkbox" name="staff" id="staff" class="form-check-input" onchange="this.form.staff_hidden.value=this.checked ? 'true' : 'false'">
                                <input type="hidden" name="staff_hidden" value="false">
                                <label for="staff" class="form-check-label">Todos os Funcionários associados à Instituição.</label>
                            </div>

                                {{-- <!-- Turmas -->
                                <div class="form-check">
                                    <input type="checkbox" name="classes_groups[]" value="turmas" id="turmas" class="form-check-input">
                                    <label for="turmas" class="form-check-label">Todos os Funcionários e Encarregados de Educação da Turma</label>
                                </div> --}}
                                <!-- Seção para Seleção de Turmas -->
                                {{-- <div id="turmasSection" class="group-selection-section" style="display: none;">
                                    <label for="turmas_list" class="form-label">Selecione as Turmas</label>
                                    <div id="turmas_list">
                                        @foreach($classGroups as $classGroup)
                                            <div class="form-check">
                                                <input type="checkbox" name="selected_class_groups[]" value="{{ $classGroup->id }}" class="form-check-input">
                                                <label class="form-check-label">{{ $classGroup->name }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div> --}}
                                <!-- Checkbox para Salas -->
                                <div class="form-check">
                                    <input type="checkbox" name="classroom_groups[]" value="salas" id="salas" class="form-check-input" onchange="toggleSalasSection()">
                                    <label for="salas" class="form-check-label">Todos os Funcionários e Encarregados de Educação de sala específica.</label>
                                </div>
                                
                                <!-- Seção para Seleção de Salas -->
                                <div id="salasSection" class="group-selection-section" style="display: none;">
                                    <label for="salas_list" class="form-label">Selecione as Salas</label>
                                    <div id="salas_list">
                                    @foreach($classrooms as $classroom)
                                        <div class="form-check">
                                        <input type="checkbox" name="selected_classrooms[]" value="{{ $classroom->id }}" class="form-check-input">
                                        <label class="form-check-label">{{ $classroom->name }}</label>
                                        </div>
                                    @endforeach
                                    </div>
                                </div>
                            <button type="submit" class="btn btn-primary">Inserir Evento</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<script>

    function toggleSalasSection() {
        const checkbox = document.getElementById('salas');
        const section = document.getElementById('salasSection');
        section.style.display = checkbox.checked ? 'block' : 'none';
    }
    
    document.addEventListener('DOMContentLoaded', function() {

        const checkboxGuardians = document.getElementById('guardians');
        const checkboxStaff = document.getElementById('staff');
        const checkboxSalas = document.getElementById('salas');
        const salasDiv = document.getElementById('salasCheckboxDiv'); // Para efeito visual

        checkboxGuardians.addEventListener('change', updateOptionsDisplay);
        checkboxStaff.addEventListener('change', updateOptionsDisplay);
        checkboxSalas.addEventListener('change', updateSalasSelection);

        function updateOptionsDisplay() {
            if (checkboxGuardians.checked || checkboxStaff.checked) {
                // Desativa e desmarca o checkbox das salas
                checkboxSalas.checked = false;
                checkboxSalas.disabled = true;
                salasDiv.style.opacity = '0.5'; // Indica que está desativado
                document.getElementById('salasSection').style.display = 'none';
            } else {
                // Habilita o checkbox das salas se os outros não estiverem marcados
                checkboxSalas.disabled = false;
                salasDiv.style.opacity = '1';
            }
        }

        function updateSalasSelection() {
            if (checkboxSalas.checked) {
                // Se salas estiver selecionado, desativa os outros dois
                checkboxGuardians.checked = false;
                checkboxStaff.checked = false;
                checkboxGuardians.disabled = true;
                checkboxStaff.disabled = true;
            } else {
                // Se salas for desmarcado, reativa os outros dois
                checkboxGuardians.disabled = false;
                checkboxStaff.disabled = false;
            }

            // Mostra ou esconde a seção de salas
            document.getElementById('salasSection').style.display = checkboxSalas.checked ? 'block' : 'none';
        }
        });

  </script>
@endsection
