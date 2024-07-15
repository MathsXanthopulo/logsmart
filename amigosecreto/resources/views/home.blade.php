@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header text-center">
                    <h1>Sorteio Amigo Secreto</h1>
                </div>
                <div class="card-body">
                    <div class="input-group mb-3">
                        <input type="text" id="busca" class="form-control" placeholder="Buscar por nome ou email">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button" onclick="filterPeople()">Buscar</button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered text-center">
                            <thead>
                                <tr>
                                    <th style="width: 5%;">#</th>
                                    <th class="name-column">Nome</th>
                                    <th style="width: 20%;">Email</th>
                                    <th style="width: 20%;">Ações</th>
                                </tr>
                            </thead>
                            <tbody id="person-list">
                                @foreach ($people as $index => $person)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td class="name-column">{{ $person->name }}</td>
                                    <td>{{ $person->email }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-warning btn-sm" onclick="editPerson({{ $person->id }})">Editar</button>
                                            <br>
                                            <button class="btn btn-danger btn-sm ml-1" onclick="deletePerson({{ $person->id }})">Excluir</button>
                                            <button class="btn btn-info btn-sm ml-1" onclick="showPair({{ $person->id }})">Ver Par</button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="btn-group mb-3" role="group">
                        <button class="btn btn-primary btn-sm mx-1" data-toggle="modal" data-target="#cadastroModal">Cadastrar Pessoa</button>
                        <button class="btn btn-success btn-sm mx-1" onclick="window.location.href='{{ route('web.raffles') }}'">Iniciar Sorteio</button>
                        <button type="button" class="btn btn-primary btn-sm mx-1" id="voltarMenuPrincipal" style="display: none;" onclick="window.location.href='{{ route('home') }}'">Voltar ao Menu Principal</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- modal do sorteio -->
<div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="errorModalLabel">Erro ao Sortear</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="errorModalMessage"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>


<!-- MODAL DE CADASTRO -->
<div class="modal fade" id="cadastroModal" tabindex="-1" role="dialog" aria-labelledby="cadastroModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cadastroModalLabel">Cadastrar Pessoa</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formCadastro" action="{{ route('person.store') }}" method="POST" novalidate>
                    @csrf
                    <div class="form-group">
                        <label for="nome">Nome:</label>
                        <input type="text" class="form-control" id="nome" name="name" required>
                        <div class="invalid-feedback">Por favor, insira seu nome.</div>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                        <div class="invalid-feedback">Por favor, insira um email válido.</div>
                    </div>
                    <button type="submit" class="btn btn-primary" id="btnSalvar" >Salvar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmacaoModal" tabindex="-1" role="dialog" aria-labelledby="confirmacaoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmacaoModalLabel">Cadastro Concluído</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Cadastro realizado com sucesso!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>


<!-- modal de exclusao -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="deleteModalLabel">Confirmar Exclusão</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            Tem certeza que deseja excluir esta pessoa?
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-danger" id="confirmDelete">Excluir</button>
        </div>
        </div>
    </div>
</div>

<div class="modal fade" id="successDeleteModal" tabindex="-1" role="dialog" aria-labelledby="successDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="successModalLabel">Exclusão Concluída</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            Pessoa excluída com sucesso!
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" data-dismiss="modal">Ok</button>
        </div>
        </div>
    </div>
</div>

<!-- Modal de Edição -->
<div class="modal fade" id="editarModal" tabindex="-1" role="dialog" aria-labelledby="editarModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editarModalLabel">Editar Cadastro</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editForm">
                    <div class="form-group">
                        <label for="editName">Nome</label>
                        <input type="text" class="form-control" id="editName" name="editName">
                    </div>
                    <div class="form-group">
                        <label for="editEmail">Email</label>
                        <input type="email" class="form-control" id="editEmail" name="editEmail">
                    </div>
                    <input type="hidden" id="editId" name="editId">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="updatePerson()">Salvar Mudanças</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="successModalLabel">Sucesso!</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="successMessage">Cadastro atualizado com sucesso!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="successOkButton">OK</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="errorUpdateModal" tabindex="-1" role="dialog" aria-labelledby="errorUpdateModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="errorModalLabel">Erro!</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="errorMessage">Erro ao atualizar cadastro.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para exibir o par sorteado -->
<div class="modal fade" id="pairModal" tabindex="-1" role="dialog" aria-labelledby="pairModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pairModalLabel">Par Sorteado</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="pairMessage">Aqui será exibido o par sorteado.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>





@endsection
