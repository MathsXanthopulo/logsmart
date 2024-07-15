<!-- resources/views/layouts/app.blade.php -->

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Projeto</title>
    
    <!-- CSS do Bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

    <!-- Scripts do jQuery e Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>

    <!-- Outros estilos -->
    <style>
        .name-column {
            width: 45%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
    @yield('styles')
</head>
<body>
@yield('content')
        <!-- Scripts adicionais -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var form = document.getElementById("formCadastro");
            
            form.addEventListener("submit", function (event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                
                form.classList.add("was-validated");
            }, false);
        });
    </script>


<!-- JS PARA EXCLUSÃO DE CADASTRO -->
<script>
    // Função para excluir uma pessoa
    let personIdToDelete;

    function deletePerson(id) {
        personIdToDelete = id;
        $('#deleteModal').modal('show');
    }

    // Evento de confirmação de exclusão
    document.getElementById('confirmDelete').addEventListener('click', function () {
        console.log('Tentando excluir a pessoa com ID:', personIdToDelete);

        fetch(`{{ url('/people') }}/${personIdToDelete}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => {
            console.log('Resposta da requisição de exclusão:', response);

            if (response.ok) {
                $('#deleteModal').modal('hide'); // Fecha a modal de exclusão
                $('#successDeleteModal').modal('show'); // Exibe a modal de sucesso de exclusão

                return response.json().then(data => {
                    console.log('Pessoa excluída com sucesso:', data);
                    // Atualiza a página após 1 segundo (1000 milissegundos)
                    setTimeout(function () {
                        window.location.reload();
                    }, 1000); // Aguarda 1 segundo antes de recarregar a página
                }).catch(error => {
                    console.error('Erro ao parsear JSON:', error);
                    alert('Erro ao excluir pessoa.');
                });
            } else {
                return response.text().then(text => {
                    console.error('Erro ao excluir pessoa:', text);
                    alert('Erro ao excluir pessoa.');
                });
            }
        })
        .catch(error => {
            console.error('Erro na requisição de exclusão:', error);
            alert('Erro ao excluir pessoa.');
        });
    });

    // Função para editar uma pessoa
    function editPerson(id) {
        console.log('Abrindo modal de edição para pessoa com ID:', id);
        fetch(`{{ route('web.show', ['id' => ':id']) }}`.replace(':id', id))
            .then(response => response.json())
            .then(data => {
                console.log('Dados da pessoa para edição:', data);
                
                // Verifica se os campos existem antes de definir os valores
                if (document.getElementById('editName')) {
                    document.getElementById('editName').value = data.name;
                }
                if (document.getElementById('editEmail')) {
                    document.getElementById('editEmail').value = data.email;
                }
                if (document.getElementById('editId')) {
                    document.getElementById('editId').value = data.id;
                }

                // Abrir o modal de edição
                $('#editarModal').modal('show');
            })
            .catch(error => {
                console.error('Erro ao buscar dados da pessoa para edição:', error);
                alert('Erro ao buscar dados da pessoa.');
            });
    }

    // Função para atualizar uma pessoa
    function updatePerson() {
        var id = document.getElementById('editId').value; // Supondo que você tenha um campo hidden para armazenar o ID
        var name = document.getElementById('editName').value;
        var email = document.getElementById('editEmail').value;

        // Requisição AJAX utilizando jQuery
        $.ajax({
            url: '/api/people/' + id,
            method: 'PUT',
            data: {
                name: name,
                email: email
            },
            success: function(response) {
                $('#successModal').modal('show'); // Exibe a modal de sucesso
                $('#errorModal').modal('hide'); // Fecha a modal de erro, caso esteja aberta
            },
            error: function(xhr, status, error) {
                console.error(error);
                $('#errorModal').modal('show'); // Exibe a modal de erro
                $('#successModal').modal('hide'); // Fecha a modal de sucesso, caso esteja aberta
            }
        });
    }

    document.getElementById('successOkButton').addEventListener('click', function() {
        $('#successModal').modal('hide'); // Fecha a modal de sucesso
        window.location.reload(); // Recarrega a página
    });

    function filterPeople() {
    console.log('Iniciando filtro de pessoas...');
    const searchTerm = document.getElementById('busca').value.toLowerCase();
    console.log('Termo de busca:', searchTerm);
    const personList = document.getElementById('person-list');
    const rows = personList.getElementsByTagName('tr');

    for (let i = 0; i < rows.length; i++) {
        const nameColumn = rows[i].getElementsByClassName('name-column')[0];
        const emailColumn = rows[i].getElementsByTagName('td')[1]; // Corrigi o índice da coluna de e-mail para 1

        const name = nameColumn ? nameColumn.textContent.toLowerCase() : '';
        const email = emailColumn ? emailColumn.textContent.toLowerCase() : '';

        console.log('Verificando linha:', i, 'Nome:', name, 'Email:', email);

        if (name.includes(searchTerm) || email.includes(searchTerm)) {
            rows[i].style.display = '';
            console.log('Mostrando linha:', i);
        } else {
            rows[i].style.display = 'none';
            console.log('Escondendo linha:', i);
        }
    }
}

function showPair(id) {
    console.log('Buscando par sorteado para ID:', id);
    const apiUrl = `/api/pairs/${id}`;
    fetch(apiUrl)
        .then(response => {
            console.log('Resposta recebida da API:', response);
            if (!response.ok) {
                console.error('Erro na resposta da API:', response.statusText);
                throw new Error('Erro na resposta da API');
            }
            return response.json();
        })
        .then(data => {
            console.log('Dados recebidos da API:', data);
            const pairMessage = document.getElementById('pairMessage');
            if (data && data.receiver && data.receiver.name) {
                pairMessage.textContent = `Seu par sorteado é: ${data.receiver.name}`;
                console.log('Par sorteado encontrado:', data.receiver.name);
            } else {
                pairMessage.textContent = 'Sem par sorteado ainda.';
                console.log('Sem par sorteado ainda.');
            }
            $('#pairModal').modal('show');
        })
        .catch(error => {
            console.error('Erro ao buscar par sorteado:', error);
            alert('Par Ainda Não Sorteado.');
        });
}


    function filterPeople() {
        // Capturar o valor digitado no campo de busca
        var searchText = document.getElementById('busca').value.toLowerCase();

        // Selecionar todas as linhas da tabela de pessoas
        var rows = document.getElementById('person-list').getElementsByTagName('tr');

        // Iterar sobre as linhas da tabela (começando do índice 1 para ignorar o cabeçalho)
        for (var i = 0; i < rows.length; i++) {
            var nameColumn = rows[i].getElementsByClassName('name-column')[0]; // Coluna de nome
            var emailColumn = rows[i].getElementsByTagName('td')[2]; // Coluna de email

            if (nameColumn || emailColumn) {
                var nameText = nameColumn.textContent || nameColumn.innerText;
                var emailText = emailColumn.textContent || emailColumn.innerText;

                // Converter para minúsculas para comparação sem distinção entre maiúsculas e minúsculas
                nameText = nameText.toLowerCase();
                emailText = emailText.toLowerCase();

                // Verificar se o texto de busca está presente no nome ou email da pessoa
                if (nameText.includes(searchText) || emailText.includes(searchText)) {
                    rows[i].style.display = ''; // Mostrar a linha se houver correspondência
                } else {
                    rows[i].style.display = 'none'; // Ocultar a linha se não houver correspondência
                }
            }
        }

        // Mostrar o botão "Voltar ao Menu Principal" apenas se houver resultados de filtro visíveis
        var voltarMenuPrincipalButton = document.getElementById('voltarMenuPrincipal');
        var visibleRows = Array.from(rows).filter(row => row.style.display !== 'none');
        if (visibleRows.length > 0) {
            voltarMenuPrincipalButton.style.display = 'inline-block';
        } else {
            voltarMenuPrincipalButton.style.display = 'none';
        }
    }
// JS PARA FILTAR QUEM E SEU PAR
    const apiUrl = `/api/pairs/${id}`;
    fetch(apiUrl)
    .then(response => {
        console.log('Status da resposta:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Pares recebidos da API:', data);
        // Aqui você pode processar os pares como necessário
    })
    .catch(error => console.error('Erro ao buscar os pares:', error));


    // JS DA MODAL DE CADASTRO
    document.addEventListener('DOMContentLoaded', function() {
    var formCadastro = document.getElementById('formCadastro');
    var btnSalvar = document.getElementById('btnSalvar');
    var confirmacaoModal = document.getElementById('confirmacaoModal');

    formCadastro.addEventListener('submit', function(event) {
        event.preventDefault();

        if (formCadastro.checkValidity()) {
            var formData = new FormData(formCadastro);
            fetch(formCadastro.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // Exemplo de ação após salvar com sucesso
                $('#cadastroModal').modal('hide'); // Esconder modal de cadastro
                $('#confirmacaoModal').modal('show'); // Mostrar modal de confirmação
                // Pode redirecionar para a lista de usuários ou fazer outra ação necessária
                window.location.href = "{{ route('home') }}";
            })
            .catch(error => {
                console.error('Erro ao cadastrar:', error);
                alert('Erro ao cadastrar. Verifique os dados e tente novamente.');
            });
        } else {
            formCadastro.classList.add('was-validated');
        }
    });
});

function realizarSorteio() {
    fetch('/api/raffle', {
        method: 'POST',
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Erro ao sortear. Código: ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        // Exibir modal de sucesso com os pares sorteados (se necessário)
        console.log('Pares sorteados:', data);
        exibirModalParesSorteados(data);
    })
    .catch(error => {
        // Exibir modal de erro com a mensagem da API
        console.error('Erro ao realizar sorteio:', error.message);
        exibirModalErro('Erro ao sortear. Verifique se há pelo menos 2 pessoas cadastradas.');
    });
}

function exibirModalParesSorteados(pares) {
    const modalContent = document.getElementById('modalContent');
    modalContent.innerHTML = ''; // Limpar conteúdo anterior, se houver

    pares.forEach(par => {
        const parElement = document.createElement('p');
        parElement.textContent = `${par.giver.name} -> ${par.receiver.name}`;
        modalContent.appendChild(parElement);
    });

    $('#sorteioModal').modal('show');
}

function exibirModalErro(mensagem) {
    const modalErrorMessage = document.getElementById('modalErrorMessage');
    modalErrorMessage.textContent = mensagem;
    $('#erroModal').modal('show');
}
// MODAL DO SORTEIO
function raffle() {
    fetch('/api/raffle')
        .then(response => {
            if (!response.ok) {
                throw new Error('Erro ao realizar o sorteio.');
            }
            return response.json();
        })
        .then(data => {
            // Processar os pares sorteados, se necessário
            console.log('Pares sorteados:', data);
        })
        .catch(error => {
            console.error('Erro ao sortear:', error);
            alert('Erro ao realizar o sorteio: ' + error.message);
        });
}





</script>


@yield('scripts')
</body>
</html>
